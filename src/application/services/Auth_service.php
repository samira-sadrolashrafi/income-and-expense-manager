<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/Balance_service.php';

class Auth_service
{
    protected $CI;
    private $balance_service;
    private $questions;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model(array('User_model', 'Recovery_model'));
        $this->CI->load->helper(array('session', 'validation'));
        $this->CI->load->config('recovery_questions');

        $this->questions = $this->CI->config->item('recovery_questions');
        $this->balance_service = new Balance_service();
    }


    public function register($data)
    {
        if (isLoggedIn()) {
            return array('success' => false,'message' => 'شما قبلاً وارد حساب کاربری شده‌اید.','status' => 409);
        }

        $validation = $this->validate_register_data($data);

        if (!$validation['success']) {
            return $validation;
        }

        $data = $validation['data'];
        $db = $this->CI->db;

        $db->trans_begin();

        $user_id = $this->CI->User_model->create(array(
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ));

        if (!$user_id || !$this->balance_service->create_initial_balance($user_id)
            || !$this->CI->Recovery_model->save_user_recovery($user_id, $data['question_id'], $data['security_answer'])
            || $db->trans_status() === false) {
            $db->trans_rollback();

            return array('success' => false,'message' => 'خطا در ثبت‌نام.','status' => 500);
        }

        if (!$db->trans_commit()) {
            return array('success' => false,'message' => 'خطا در ثبت‌نام.','status' => 500);
        }

        return array('success' => true,'message' => 'ثبت‌نام با موفقیت انجام شد.','status' => 201);
    }


    public function login($data)
    {
        if (isLoggedIn()) {
            return array('success' => false,'message' => 'شما قبلاً وارد حساب کاربری شده‌اید.','status' => 409);
        }

        $validation = $this->validate_login_data($data);

        if (!$validation['success']) {
            return $validation;
        }

        $data = $validation['data'];
        $user = $this->CI->User_model->find_by_username($data['username']);

        if (!$user || !password_verify($data['password'], $user->password)) {
            return array(
                'success' => false,
                'message' => 'نام کاربری یا رمز عبور اشتباه است.',
                'status' => 401,
                'errors' => array(
                    'username_err' => '',
                    'password_err' => 'نام کاربری یا رمز عبور اشتباه است.'
                )
            );
        }

        if ($user->deleted_at !== null) {
            if ($this->is_recovery_available($user)) {
                return array(
                    'success' => false,
                    'message' => 'این حساب کاربری حذف شده است، اما هنوز امکان بازیابی آن وجود دارد.',
                    'status' => 409,
                    'account_deleted' => true,
                    'recovery_available' => true
                );
            }

            return array(
                'success' => false,
                'message' => 'مهلت ۳۰ روزه بازیابی این حساب به پایان رسیده است و دیگر امکان بازیابی آن وجود ندارد.',
                'status' => 410,
                'account_deleted' => true,
                'recovery_available' => false
            );
        }

        if (!$this->complete_login($user, $data['remember'])) {
            return array(
                'success' => false,
                'message' => 'نام کاربری یا رمز عبور اشتباه است.',
                'status' => 401,
                'errors' => array(
                    'username_err' => '',
                    'password_err' => 'نام کاربری یا رمز عبور اشتباه است.'
                )
            );
        }

        return array('success' => true,'message' => 'با موفقیت وارد شدید.','status' => 200);
    }


    public function restore_deleted_account($data)
    {
        if (isLoggedIn()) {
            return array('success' => false,'message' => 'شما قبلاً وارد حساب کاربری شده‌اید.','status' => 409);
        }

        $validation = $this->validate_restore_data($data);

        if (!$validation['success']) {
            return $validation;
        }

        $data = $validation['data'];
        $user = $this->CI->User_model->find_by_username($data['username']);

        if (!$user || $user->deleted_at === null || !password_verify($data['password'], $user->password)) {
            return array(
                'success' => false,
                'message' => 'نام کاربری یا رمز عبور اشتباه است، یا حساب قابل بازیابی نیست.',
                'status' => 401
            );
        }

        if (!$this->is_recovery_available($user)) {
            return array(
                'success' => false,
                'message' => 'مهلت ۳۰ روزه بازیابی این حساب به پایان رسیده است.',
                'status' => 410,
                'account_deleted' => true,
                'recovery_available' => false
            );
        }

        $db = $this->CI->db;
        $db->trans_begin();

        $restored = $this->CI->User_model->restore_account($user->id, date('Y-m-d H:i:s'));

        if (!$restored) {
            $db->trans_rollback();

            return array(
                'success' => false,
                'message' => 'نام کاربری یا رمز عبور اشتباه است، یا حساب قابل بازیابی نیست.',
                'status' => 401
            );
        }

        $user = $this->CI->User_model->find_active_by_id($user->id);

        if (!$user || !$this->complete_login($user, $data['remember'])
            || $db->trans_status() === false || !$db->trans_commit()) {
            $db->trans_rollback();
            $this->CI->load->library('session');
            $this->CI->session->sess_destroy();
            clearRememberCookie();

            return array(
                'success' => false,
                'message' => 'نام کاربری یا رمز عبور اشتباه است، یا حساب قابل بازیابی نیست.',
                'status' => 401
            );
        }

        return array(
            'success' => true,
            'message' => 'حساب شما با موفقیت بازیابی شد و وارد حساب کاربری شدید.',
            'status' => 200
        );
    }


    public function logout()
    {
        $this->CI->load->library('session');

        $user_id = $this->CI->session->userdata('user_id');

        if (!$user_id && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];

            if (is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token)) {
                $user = $this->CI->User_model->find_by_remember_token(hash('sha256', $token));
                $user_id = $user ? $user->id : null;
            }
        }

        clearRememberCookie();
        $this->CI->session->sess_destroy();

        if ($user_id && !$this->CI->User_model->clear_remember_token($user_id)) {
            return array(
                'success' => false,
                'message' => 'خروج انجام شد، اما حذف توکن از دیتابیس تأیید نشد.',
                'status' => 500
            );
        }

        return array('success' => true,'message' => 'با موفقیت خارج شدید.','status' => 200);
    }


    private function validate_register_data($data)
    {
        $fields = array('first_name', 'last_name', 'username', 'email','password', 'password_confirm', 'question_id', 'security_answer');

        if (!is_array($data)) {
            return array('success' => false,'message' => 'اطلاعات فرم نامعتبر است.','status' => 422);
        }

        foreach ($fields as $field) {
            if (!isset($data[$field]) || !is_string($data[$field])) {
                return array(
                    'success' => false,
                    'message' => 'اطلاعات فرم نامعتبر است.',
                    'status' => 422
                );
            }
        }

        $data = array(
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'username' => trim($data['username']),
            'email' => trim($data['email']),
            'password' => $data['password'],
            'password_confirm' => $data['password_confirm'],
            'question_id' => $data['question_id'],
            'security_answer' => $data['security_answer']
        );

        $errors = array(
            'first_name_err' => validatePersonName($data['first_name'], 'نام'),
            'last_name_err' => validatePersonName($data['last_name'], 'نام خانوادگی'),
            'username_err' => validateUsername($data['username']),
            'email_err' => validateEmail($data['email']),
            'password_err' => validatePassword($data['password']),
            'confirm_password_err' => '',
            'question_id_err' => '',
            'security_answer_err' => ''
        );

        if ($data['password'] !== $data['password_confirm']) {
            $errors['confirm_password_err'] = 'تکرار رمز عبور با رمز وارد شده مطابقت ندارد.';
        }

        if (!preg_match('/\A[1-5]\z/', $data['question_id'])
            || !isset($this->questions[(int) $data['question_id']])) {
            $errors['question_id_err'] = 'لطفاً یکی از پنج سؤال امنیتی را انتخاب کنید.';
        }

        if (strlen(trim($data['security_answer'])) < 6 || strlen($data['security_answer']) > 256) {
            $errors['security_answer_err'] = 'پاسخ امنیتی باید بین ۶ و ۲۵۶ کاراکتر باشد.';
        }

        if (empty($errors['username_err']) && $this->CI->User_model->find_by_username($data['username'])) {
            $errors['username_err'] = 'این نام کاربری قبلاً انتخاب شده است.';
        }

        if (empty($errors['email_err']) && $this->CI->User_model->find_by_email($data['email'])) {
            $errors['email_err'] = 'این ایمیل قبلاً ثبت شده است.';
        }

        foreach ($errors as $error) {
            if ($error !== '') {
                return array(
                    'success' => false,
                    'message' => 'اطلاعات فرم ثبت‌نام را اصلاح کنید.',
                    'status' => 422,
                    'errors' => $errors
                );
            }
        }

        return array('success' => true,'data' => $data);
    }


    private function validate_login_data($data)
    {
        if (!is_array($data) || !isset($data['username']) || !is_string($data['username'])
            || !isset($data['password']) || !is_string($data['password'])) {
            return array(
                'success' => false,
                'message' => 'اطلاعات ورود نامعتبر است.',
                'status' => 422
            );
        }

        $data = array(
            'username' => trim($data['username']),
            'password' => $data['password'],
            'remember' => isset($data['remember']) && $data['remember'] === '1'
        );

        $errors = array(
            'username_err' => '',
            'password_err' => validateLoginPassword($data['password'])
        );

        if ($data['username'] === '') {
            $errors['username_err'] = 'لطفاً نام کاربری خود را وارد کنید.';
        }

        if (!empty($errors['username_err']) || !empty($errors['password_err'])) {
            return array(
                'success' => false,
                'message' => 'اطلاعات فرم ورود را اصلاح کنید.',
                'status' => 422,
                'errors' => $errors
            );
        }

        return array('success' => true,'data' => $data);
    }


    private function validate_restore_data($data)
    {
        if (!is_array($data) || !isset($data['username']) || !is_string($data['username'])
            || !isset($data['password']) || !is_string($data['password'])) {
            return array(
                'success' => false,
                'message' => 'اطلاعات بازیابی نامعتبر است.',
                'status' => 422
            );
        }

        $username = trim($data['username']);
        $password = $data['password'];

        if ($username === '' || strlen($username) > 100 || strlen($password) > 1024 || validateLoginPassword($password) !== '') {
            return array(
                'success' => false,
                'message' => 'اطلاعات بازیابی نامعتبر است.',
                'status' => 422
            );
        }

        return array(
            'success' => true,
            'data' => array(
                'username' => $username,
                'password' => $password,
                'remember' => isset($data['remember']) && $data['remember'] === '1'
            )
        );
    }


    private function is_recovery_available($user)
    {
        if (empty($user->restore_until)) {
            return false;
        }

        $restore_timestamp = strtotime($user->restore_until);

        return $restore_timestamp !== false && $restore_timestamp > time();
    }


    private function complete_login($user, $remember)
    {
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + 30 * 86400;

            if (!$this->CI->User_model->update_remember_token($user->id, hash('sha256', $token), date('Y-m-d H:i:s', $expires))) {
                return false;
            }

            if (!setcookie('remember_token', $token, rememberCookieOptions($expires))) {
                $this->CI->User_model->clear_remember_token($user->id);
                clearRememberCookie();
                return false;
            }
        } else {
            if (!$this->CI->User_model->clear_remember_token($user->id)) {
                return false;
            }

            clearRememberCookie();
        }

        $this->CI->load->library('session');
        $this->CI->session->sess_regenerate(true);

        $this->CI->session->set_userdata(array(
            'user_id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'logged_in' => true
        ));

        return true;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recovery_service
{
    private $CI;
    private $questions;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model(array('User_model', 'Recovery_model'));
        $this->CI->load->helper(array('validation', 'session'));
        $this->CI->load->config('recovery_questions');

        $this->questions = $this->CI->config->item('recovery_questions');
    }

    public function question($username)
    {
        // مثلا کاربر قبلاً پاسخ امنیتی را تأیید کرده، اما هنوز رمز جدید را وارد نکرده است.
        $this->CI->session->unset_userdata('recovery_user_id');

        if (!is_string($username) || trim($username) === '') {
            return array(
                'success' => false,
                'message' => 'نام کاربری را وارد کنید.',
                'status' => 422
            );
        }

        $user = $this->CI->User_model->find_by_username(trim($username));

        $recovery = $user && $user->deleted_at === null ? $this->CI->Recovery_model->get_user_recovery($user->id) : null;

        $question_id = $recovery ? $recovery->question_id : 1;

        if ($recovery) {
            $this->CI->session->set_userdata('recovery_user', array(
                'user_id' => $user->id,
                'verified' => false
            ));
        }

        return array(
            'success' => true,
            'message' => 'لطفاً به سؤال امنیتی پاسخ دهید.',
            'status' => 200,
            'question' => $this->questions[$question_id]
        );
    }

    public function verify($answer)
    {
        //می‌خواهیم مطمئن شویم که اگر پاسخ امنیتی اشتباه بود، مجوز قبلی همچنان در Session باقی نماند.
        $recovery = $this->CI->session->userdata('recovery_user');

        if (!is_array($recovery) || empty($recovery['user_id']) || !isset($recovery['verified']) || $recovery['verified'] !== false) {
            return array(
                'success' => false,
                'message' => 'ابتدا نام کاربری را وارد کنید.',
                'status' => 403
            );
        }

        if (!is_string($answer) || trim($answer) === '') {
            return array(
                'success' => false,
                'message' => 'پاسخ امنیتی را وارد کنید.',
                'status' => 422
            );
        }

        $user_id = $recovery['user_id'];

        $user = $this->CI->User_model->find_active_by_id($user_id);

        $user_recovery = $this->CI->Recovery_model->get_user_recovery($user_id);

        if (!$user || !$user_recovery ||
            !password_verify(trim($answer), $user_recovery->answer_hash)) {
            return array(
                'success' => false,
                'message' => 'پاسخ امنیتی صحیح نیست.',
                'status' => 401
            );
        }

        $this->CI->session->sess_regenerate(true);

        $recovery['verified'] = true;

        $this->CI->session->set_userdata('recovery_user', $recovery);

        return array(
            'success' => true,
            'message' => 'پاسخ تأیید شد. رمز عبور جدید را تعیین کنید.',
            'status' => 200
        );
    }

    
    public function reset($password, $confirmation)
    {
        $recovery = $this->CI->session->userdata('recovery_user');

        if (!is_array($recovery) || empty($recovery['user_id']) || !isset($recovery['verified']) || $recovery['verified'] !== true) {            return array(
                'success' => false,
                'message' => 'ابتدا پاسخ سؤال امنیتی را تأیید کنید.',
                'status' => 403
            );
        }

        $user_id = $recovery['user_id'];

        if (!is_string($password) || !is_string($confirmation)) {
            return array(
                'success' => false,
                'message' => 'رمز عبور را وارد کنید.',
                'status' => 422
            );
        }

        $password_error = validatePassword($password);

        if ($password_error) {
            return array(
                'success' => false,
                'message' => 'رمز عبور را اصلاح کنید.',
                'status' => 422,
                'errors' => array('recovery_password_err' => $password_error)
            );
        }

        if ($password !== $confirmation) {
            return array(
                'success' => false,
                'message' => 'تکرار رمز عبور مطابقت ندارد.',
                'status' => 422,
                'errors' => array('recovery_confirm_err' => 'تکرار رمز عبور مطابقت ندارد.')
            );
        }

        if (!$this->CI->Recovery_model->reset_password($user_id, $password)) {
            return array(
                'success' => false,
                'message' => 'ذخیره رمز جدید انجام نشد؛ دوباره تلاش کنید.',
                'status' => 500
            );
        }

        $this->CI->session->unset_userdata('recovery_user');

        clearRememberCookie();

        return array(
            'success' => true,
            'message' => 'رمز عبور تغییر کرد. اکنون با رمز جدید وارد شوید.',
            'status' => 200
        );
    }


    public function enroll($user_id, $data)
    {
        $user = $this->CI->User_model->find_active_by_id($user_id);

        $password = $data['current_password'] ?? null;
        $question_id = $data['question_id'] ?? null;
        $answer = $data['answer'] ?? null;

        if (!$user || !is_string($password) || !password_verify($password, $user->password)) {
            return array(
                'success' => false,
                'message' => 'رمز عبور فعلی اشتباه است.',
                'status' => 401
            );
        }

        if (!is_string($question_id) || !isset($this->questions[$question_id])
            || !is_string($answer) || trim($answer) === '') {
            return array(
                'success' => false,
                'message' => 'سؤال و پاسخ امنیتی را وارد کنید.',
                'status' => 422
            );
        }

        if (!$this->CI->Recovery_model->save_user_recovery($user_id, $question_id, $answer)) {
            return array(
                'success' => false,
                'message' => 'ذخیره سؤال امنیتی انجام نشد.',
                'status' => 500
            );
        }

        return array(
            'success' => true,
            'message' => 'سؤال بازیابی حساب با موفقیت ثبت شد.',
            'status' => 200
        );
    }
}

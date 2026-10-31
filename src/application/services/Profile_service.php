<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_service
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Profile_model');
        $this->CI->load->helper(array('validation', 'session'));
        $this->CI->load->config('recovery_questions');
    }

    // دریافت اطلاعات پروفایل
    public function get($user_id)
    {
        $user = $this->CI->Profile_model->find_active_user($user_id);

        if (!$user) {
            return null;
        }

        $recovery = $this->CI->Profile_model->find_recovery_question($user_id);

        return array(
            'profile' => array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'question_id' => $recovery ? (string) $recovery->question_id : '',
                'avatar_url' => $this->avatar_url($user->avatar)
            ),
            'questions' => $this->CI->config->item('recovery_questions')
        );
    }

    // ذخیره تغییرات پروفایل
    public function update($user_id, $request, $files)
    {
        $user = $this->CI->Profile_model->find_active_user($user_id);

        if (!$user) {
            return $this->error('حساب کاربری فعال پیدا نشد.', 401);
        }

        $valid = validateProfileUpdate(
            $request,
            $this->CI->config->item('recovery_questions')
        );

        if (!$valid['success']) {
            return $valid;
        }

        $data = $valid['data'];

        if ($this->CI->Profile_model->username_taken($data['username'], $user_id)) {
            return $this->error('نام کاربری قبلاً استفاده شده است.', 422);
        }

        if ($this->CI->Profile_model->email_taken($data['email'], $user_id)) {
            return $this->error('ایمیل قبلاً استفاده شده است.', 422);
        }

        $change_password = $valid['password_change'];
        $change_recovery = $valid['recovery_change'];
        $security = $valid['security'];

        // بررسی عکس
        $remove_avatar = isset($request['remove_avatar']) && $request['remove_avatar'] === '1';

        $has_avatar = isset($files['avatar']['error']) && $files['avatar']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($remove_avatar && $has_avatar) {
            return $this->error('حذف و انتخاب عکس جدید هم‌زمان مجاز نیست.', 422);
        }

        $new_avatar = null;

        if ($has_avatar) {
            $new_avatar = $this->save_avatar();

            if (!$new_avatar) {
                return $this->error('عکس معتبر نیست؛ JPG، PNG یا WebP تا ۲ مگابایت انتخاب کنید.', 422);
            }
        }

        // آماده‌سازی اطلاعات
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($new_avatar) {
            $data['avatar'] = $new_avatar;
        } elseif ($remove_avatar) {
            $data['avatar'] = null;
        }

        if ($change_password) {
            $data['password'] = password_hash(
                $security['new_password'],
                PASSWORD_DEFAULT
            );

            if ($data['password'] === false) {
                $this->remove_avatar($new_avatar);
                return $this->error('تغییر رمز عبور انجام نشد.', 500);
            }

            $data['remember_token'] = null;
            $data['remember_token_expires_at'] = null;
        }

        // ذخیره در دیتابیس
        $db = $this->CI->db;
        $old_debug = $db->db_debug;
        $db->db_debug = false;
        $db->trans_begin();

        try {
            $active_user = $this->CI->Profile_model->find_active_user($user_id);

            if (!$active_user || (($change_password || $change_recovery) && !password_verify($security['current_password'], $active_user->password))) {

                $db->trans_rollback();
                $this->remove_avatar($new_avatar);

                return $this->error('رمز فعلی اشتباه است یا حساب در دسترس نیست.', 401);
            }

            $saved = $this->CI->Profile_model->update_active_user($user_id,$data);

            if ($saved && $change_recovery) {
                $saved = $this->CI->Profile_model->save_recovery_question($user_id,$security['question_id'],$security['recovery_answer']);
            }

            if (!$saved || $db->trans_status() === false) {
                return $this->cancel_update($new_avatar);
            }

            if (!$db->trans_commit()) {
                return $this->cancel_update($new_avatar);
            }

        } catch (Throwable $error) {
            return $this->cancel_update($new_avatar);

        } finally {
            $db->db_debug = $old_debug;
        }

        // به‌روزرسانی Session
        if ($change_password) {
            clearRememberCookie();
            $this->CI->session->sess_regenerate(true);
        }

        $avatar = $new_avatar ? $new_avatar : ($remove_avatar ? null : $active_user->avatar);

        $this->CI->session->set_userdata(array(
            'username' => $data['username'],
            'avatar' => $avatar
        ));

        if (($new_avatar || $remove_avatar) && $active_user->avatar !== $avatar) {
            $this->remove_avatar($active_user->avatar);
        }

        return array(
            'success' => true,
            'status' => 200,
            'message' => 'تغییرات پروفایل ذخیره شد.',
            'profile' => array(
                'username' => $data['username'],
                'avatar_url' => $this->avatar_url($avatar)
            )
        );
    }

    // آپلود عکس با کتابخانه CodeIgniter
    private function save_avatar()
    {
        $folder = FCPATH . 'uploads/avatars/';

        if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
            return false;
        }

        $config = array(
            'upload_path' => $folder,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 2048,
            'max_width' => 6000,
            'max_height' => 6000,
            'encrypt_name' => true
        );

        $this->CI->load->library('upload');
        $this->CI->upload->initialize($config);

        if (!$this->CI->upload->do_upload('avatar')) {
            return false;
        }

        $file = $this->CI->upload->data();

        return 'uploads/avatars/' . $file['file_name'];
    }

    // حذف عکس قبلی
    private function remove_avatar($path)
    {
        if (!$path || $path !== 'uploads/avatars/' . basename($path)) {
            return;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, array('jpg', 'jpeg', 'png', 'webp'), true)) {
            return;
        }

        $file = FCPATH . $path;

        if (is_file($file)) {
            unlink($file);
        }
    }

    // ساخت آدرس عکس
    private function avatar_url($path)
    {
        if (!$path) {
            return '';
        }

        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return filter_var($path, FILTER_VALIDATE_URL) ? $path : '';
        }

        if ($path === 'uploads/avatars/' . basename($path)) {
            return base_url($path);
        }

        return '';
    }

    // لغو ذخیره در صورت خطا
    private function cancel_update($new_avatar)
    {
        $this->CI->db->trans_rollback();
        $this->remove_avatar($new_avatar);

        return $this->error('ذخیره پروفایل انجام نشد.', 409);
    }

    private function error($message, $status)
    {
        return array(
            'success' => false,
            'message' => $message,
            'status' => $status
        );
    }
}
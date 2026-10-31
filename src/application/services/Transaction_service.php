<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/Balance_service.php';

class Transaction_service
{
    protected $CI;
    private $balance_service;
    private $restore_days = 30;
    private $last_error = array();


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('User_model');
        $this->CI->load->model('Category_model');
        $this->CI->load->model('Transaction_model');
        $this->CI->load->model('Balance_history_model');

        $this->balance_service = new Balance_service();
    }


    public function get_last_error()
    {
        return $this->last_error;
    }

    private function fail($message, $status)
    {
        $this->last_error = array(
            'message' => $message,
            'status' => $status
        );

        return false;
    }

    private function record_history_event($user_id,$event_type,$effect_type,$amount,$effective_date,$transaction_id,$reverse = false) {

        return $this->balance_service->record_transaction_history_event($user_id,$event_type,$effect_type,$amount,$effective_date,$transaction_id,$reverse);
    }


    private function validate_data($data)
    {
        if (!is_array($data)) {
            return $this->fail('داده‌های ارسالی نامعتبر است.', 422);
        }

        if (!isset($data['title'], $data['amount'], $data['category_id'], $data['transaction_date'])) {
            return $this->fail('عنوان، مبلغ، دسته‌بندی و تاریخ تراکنش الزامی است.', 422);
        }

        if (!is_string($data['title'])) {
            return $this->fail('عنوان تراکنش نامعتبر است.', 422);
        }

        $title = trim($data['title']);

        if (preg_match('/^.{1,150}$/u', $title) !== 1) {
            return $this->fail('عنوان تراکنش باید بین ۱ تا ۱۵۰ نویسه باشد.', 422);
        }

        $category_id = $data['category_id'];

        if (!is_string($category_id) && !is_int($category_id)) {
            return $this->fail('شناسه دسته‌بندی نامعتبر است.', 422);
        }

        $amount_error = $this->balance_service->amount_error($data['amount']);

        if ($amount_error !== null) {
            return $this->fail($amount_error, 422);
        }

        $amount = $this->balance_service->normalize_amount($data['amount']);

        $date = $data['transaction_date'];

        if (!is_string($date) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            return $this->fail('قالب تاریخ تراکنش نامعتبر است؛ تاریخ را از تقویم انتخاب کنید.', 422);
        }

        if (!checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1])) {
            return $this->fail('تاریخ انتخاب‌شده یک روز واقعی نیست؛ تاریخ معتبر انتخاب کنید.', 422);
        }

        $description = $data['description'] ?? null;

        if ($description !== null && !is_string($description)) {
            return $this->fail('توضیحات تراکنش نامعتبر است.', 422);
        }

        return array(
            'title' => $title,
            'amount' => $amount,
            'category_id' => $category_id,
            'transaction_date' => $date,
            'description' => $description === '' ? null : $description
        );
    }


    public function create($user_id, $data)
    {
        $values = $this->validate_data($data);

        if ($values === false) {
            return false;
        }

        $db = $this->CI->db;
        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $category = $this->CI->Category_model->find_category_by_id($user_id,$values['category_id']);

        if (!$category) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی انتخاب‌شده پیدا نشد؛ فهرست دسته‌بندی‌ها را تازه‌سازی کنید.',422);
        }

        if ($category->deleted_at !== null) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی انتخاب‌شده حذف شده است؛ دسته‌بندی دیگری انتخاب کنید.',422);
        }

        $values['user_id'] = $user_id;
        $values['created_at'] = date('Y-m-d H:i:s');

        $id = $this->CI->Transaction_model->create($values);

        if (!$id || !$this->balance_service->apply_transaction_effect($user_id,$category->type,$values['amount']) ||
            !$this->record_history_event($user_id,$category->type,$category->type,$values['amount'],$values['transaction_date'],$id) ||
            $db->trans_status() === false) {

            $db->trans_rollback();
            return $this->fail('ثبت تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        if (!$db->trans_commit()) {
            return $this->fail('ثبت تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        return $id;
    }


    public function get($user_id, $transaction_id)
    {
        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $transaction = $this->CI->Transaction_model->find_transaction_by_id($user_id, $transaction_id);

        if (!$transaction) {
            return $this->fail('تراکنش موردنظر پیدا نشد یا در دسترس نیست.',404);
        }

        if ($transaction->deleted_at !== null) {
            return $this->fail('این تراکنش حذف شده است و در فهرست فعال نیست.',404);
        }

        return $transaction;
    }


    public function get_all($user_id)
    {
        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $transactions = $this->CI->Transaction_model->find_active_transactions_by_user_id($user_id);

        if ($transactions === false) {
            return $this->fail('دریافت تراکنش‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.', 500);
        }

        return $transactions;
    }

    public function get_all_paginated($user_id, $params = array())
    {
        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.', 401);
        }

        $page = max(1, (int) ($params['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $search = $params['search'] ?? '';
        $type = $params['type'] ?? 'all';
        $category_id = $params['category_id'] ?? '';
        $date_from = $params['date_from'] ?? '';
        $date_to = $params['date_to'] ?? '';
        $sort = $params['sort'] ?? 'newest';

        if (
            !is_string($search) ||
            !in_array($type, array('all', 'income', 'expense'), true) ||
            !is_scalar($category_id) ||
            !is_string($date_from) ||
            !is_string($date_to) ||
            !in_array($sort, array('newest', 'oldest'), true)
        ) {
            return $this->fail('مقادیر فیلترها نامعتبر است.', 422);
        }

        foreach (array($date_from, $date_to) as $date) {
            if ($date === '') {
                continue;
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $this->fail('فرمت تاریخ فیلترها نامعتبر است.', 422);
            }

            $parts = explode('-', $date);
            if (!checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                return $this->fail('فرمت تاریخ فیلترها نامعتبر است.', 422);
            }
        }

        if ($date_from !== '' && $date_to !== '' && $date_from > $date_to) {
            return $this->fail('تاریخ شروع فیلتر نمی‌تواند بعد از تاریخ پایان باشد.',422);
        }

        $filters = array(
            'search' => trim($search),
            'type' => $type,
            'category_id' => $category_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'sort' => $sort
        );

        $transactions = $this->CI->Transaction_model->find_active_transactions_by_user_id($user_id, $limit, $offset, $filters);

        $total = $this->CI->Transaction_model->count_active_transactions_by_user_id($user_id, $filters);

        if ($transactions === false || $total === false) {
            return $this->fail('دریافت تراکنش‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.',500);
        }

        return array(
            'transactions' => $transactions,
            'pagination' => array(
                'current_page' => $page,
                'total_pages' => (int) ceil($total / $limit),
                'total' => $total
            )
        );
    }

    public function get_deleted_paginated($user_id, $params = array())
    {
        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $page = max(1, (int) ($params['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $transactions = $this->CI->Transaction_model->find_deleted_transactions_by_user_id($user_id, $limit, $offset);

        $total = $this->CI->Transaction_model->count_deleted_transactions_by_user_id($user_id);

        if ($transactions === false || $total === false) {
            return $this->fail('دریافت تراکنش‌های حذف‌شده انجام نشد؛ لطفاً دوباره تلاش کنید.',500);
        }

        return array(
            'transactions' => $transactions,
            'pagination' => array(
                'current_page' => $page,
                'total_pages' => (int) ceil($total / $limit),
                'total' => $total
            )
        );
    }


    public function update($user_id, $transaction_id, $data)
    {
        $values = $this->validate_data($data);

        if ($values === false) {
            return false;
        }

        $db = $this->CI->db;
        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $old = $this->CI->Transaction_model->find_transaction_by_id($user_id, $transaction_id);

        if (!$old) {
            $db->trans_rollback();
            return $this->fail('تراکنش موردنظر پیدا نشد یا در دسترس نیست.',404);
        }

        if ($old->deleted_at !== null) {
            $db->trans_rollback();
            return $this->fail('این تراکنش حذف شده و قابل ویرایش نیست.',404);
        }

        $old_category = $this->CI->Category_model->find_category_by_id($user_id,$old->category_id);

        $new_category = $this->CI->Category_model->find_category_by_id($user_id,$values['category_id']);

        if (!$old_category) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی اصلی این تراکنش پیدا نشد؛ با پشتیبانی تماس بگیرید.',500);
        }

        if (!$new_category) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی انتخاب‌شده پیدا نشد؛ فهرست دسته‌بندی‌ها را تازه‌سازی کنید.',422);
        }

        if ($new_category->deleted_at !== null) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی انتخاب‌شده حذف شده است؛ دسته‌بندی دیگری انتخاب کنید.',422);
        }

        $values['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->balance_service->reverse_transaction_effect($user_id,$old_category->type, $old->amount) ||
            !$this->CI->Transaction_model->update_active_transaction_by_user($user_id,$transaction_id,$values) ||
            !$this->balance_service->apply_transaction_effect($user_id,$new_category->type,$values['amount']) ||
            !$this->record_history_event(
                $user_id,
                'transaction_edit_reversal',
                $old_category->type,
                $old->amount,
                $old->transaction_date,
                $transaction_id,
                true
            ) ||
            !$this->record_history_event(
                $user_id,
                'transaction_edit',
                $new_category->type,
                $values['amount'],
                $values['transaction_date'],
                $transaction_id
            ) ||
            $db->trans_status() === false
        ) {
            $db->trans_rollback();
            return $this->fail('ویرایش تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        if (!$db->trans_commit()) {
            return $this->fail('ویرایش تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        return true;
    }


    public function delete($user_id, $transaction_id)
    {
        $db = $this->CI->db;
        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401
            );
        }

        $transaction = $this->CI->Transaction_model->find_transaction_by_id($user_id,$transaction_id);

        if (!$transaction) {
            $db->trans_rollback();
            return $this->fail('تراکنش موردنظر پیدا نشد یا در دسترس نیست.',404);
        }

        if ($transaction->deleted_at !== null) {
            $db->trans_rollback();
            return $this->fail('این تراکنش قبلاً حذف شده است.',404);
        }

        $category = $this->CI->Category_model->find_category_by_id($user_id,$transaction->category_id);

        if (!$category) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی این تراکنش پیدا نشد؛ با پشتیبانی تماس بگیرید.',500);
        }

        $now = date('Y-m-d H:i:s');

        $until = date('Y-m-d H:i:s',time() + $this->restore_days * 86400);

        if (!$this->CI->Transaction_model->soft_delete_transaction_by_user($user_id,$transaction_id,$now,$until) ||
            !$this->balance_service->reverse_transaction_effect($user_id,$category->type,$transaction->amount) ||
            !$this->record_history_event(
                $user_id,
                'transaction_delete_reversal',
                $category->type,
                $transaction->amount,
                $transaction->transaction_date,
                $transaction_id,
                true
            ) || $db->trans_status() === false)
            {
                $db->trans_rollback();
                return $this->fail('حذف تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
            }

        if (!$db->trans_commit()) {
            return $this->fail('حذف تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        return true;
    }


    public function restore($user_id, $transaction_id)
    {
        $db = $this->CI->db;
        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return $this->fail('حساب کاربری شما در دسترس نیست؛ دوباره وارد شوید.',401);
        }

        $transaction = $this->CI->Transaction_model->find_transaction_by_id($user_id,$transaction_id);

        if (!$transaction) {
            $db->trans_rollback();
            return $this->fail('تراکنش موردنظر پیدا نشد یا در دسترس نیست.',404);
        }

        if (!$transaction->deleted_at) {
            $db->trans_rollback();
            return $this->fail('این تراکنش حذف نشده است و نیازی به بازیابی ندارد.',404);
        }

        if (!$transaction->restore_until) {
            $db->trans_rollback();
            return $this->fail('برای این تراکنش مهلت بازیابی ثبت نشده و قابل بازیابی نیست.',404);
        }

        if (strtotime($transaction->restore_until) <= time()) {
            $db->trans_rollback();
            return $this->fail('مهلت بازیابی این تراکنش به پایان رسیده است.',404);
        }

        $category = $this->CI->Category_model->find_category_by_id($user_id,$transaction->category_id);

        if (!$category) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی این تراکنش پیدا نشد؛ با پشتیبانی تماس بگیرید.',500);
        }

        if ($category->deleted_at !== null) {
            $db->trans_rollback();
            return $this->fail('دسته‌بندی این تراکنش حذف شده است؛ ابتدا آن دسته‌بندی را بازیابی کنید.',422);
        }

        if (!$this->CI->Transaction_model->restore_transaction_by_user($user_id,$transaction_id) ||
            !$this->balance_service->apply_transaction_effect($user_id,$category->type,$transaction->amount) ||
            !$this->record_history_event(
                $user_id,
                'transaction_restore',
                $category->type,
                $transaction->amount,
                $transaction->transaction_date,
                $transaction_id
            ) ||
            $db->trans_status() === false
        ) {
            $db->trans_rollback();
            return $this->fail('بازگردانی تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        if (!$db->trans_commit()) {
            return $this->fail('بازگردانی تراکنش انجام نشد؛ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره تلاش کنید.',500);
        }

        return true;
    }
}
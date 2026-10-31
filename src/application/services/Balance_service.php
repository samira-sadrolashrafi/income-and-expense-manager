<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Balance_model');
        $this->CI->load->model('User_model');
        $this->CI->load->model('Balance_history_model');
    }


    public function amount_error($amount, $allow_negative = false)
    {
        if (!is_string($amount) && !is_int($amount)) {
            return 'مبلغ نامعتبر است.';
        }

        $value = trim((string) $amount);

        $pattern = $allow_negative ? '/^-?[0-9]+(?:\.[0-9]{1,2})?$/' : '/^[0-9]+(?:\.[0-9]{1,2})?$/';

        if (!preg_match($pattern, $value)) {
            return 'فرمت مبلغ نامعتبر است؛ فقط عدد با حداکثر دو رقم اعشار وارد کنید.';
        }

        $unsigned = ltrim($value, '-');

        $integer = explode('.', $unsigned, 2)[0];
        $integer = ltrim($integer, '0');

        if (strlen($integer) > 13) {
            return 'مبلغ واردشده بیش از حد مجاز است.';
        }

        return null;
    }

    public function normalize_amount($amount, $allow_negative = false)
    {
        if (!is_string($amount) && !is_int($amount)) {
            return false;
        }

        $value = trim((string) $amount);

        $pattern = $allow_negative ? '/^-?[0-9]+(?:\.[0-9]{1,2})?$/' : '/^[0-9]+(?:\.[0-9]{1,2})?$/';

        if (!preg_match($pattern, $value)) {
            return false;
        }

        $negative = false;

        if ($value[0] === '-') {
            $negative = true;
            $value = substr($value, 1);
        }

        $parts = explode('.', $value, 2);

        $integer = ltrim($parts[0], '0');

        if ($integer === '') {
            $integer = '0';
        }

        if (strlen($integer) > 13) {
            return false;
        }

        $fraction = '';

        if (isset($parts[1])) {
            $fraction = $parts[1];
        }

        $result = $integer . '.' . str_pad($fraction, 2, '0');

        if (!$negative || $result === '0.00') {
            return $result;
        }

        return '-' . $result;
    }

  
    private function negate_amount($amount)
    {
        $amount = $this->normalize_amount($amount, true);

        if ($amount === false) {
            return false;
        }

        if ($amount === '0.00') {
            return '0.00';
        }

        return ($amount[0] === '-') ? substr($amount, 1) : '-' . $amount;
    }

    public function create_initial_balance($user_id)
    {
        if ($user_id <= 0) {
            return false;
        }

        return $this->CI->Balance_model->create(array(
            'user_id' => $user_id,
            'balance' => '0.00',
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    public function increase($user_id, $amount)
    {
        $amount = $this->normalize_amount($amount);

        if ($amount === false || !$this->CI->User_model->find_active_by_id($user_id) || !$this->CI->Balance_model->find_by_user_id($user_id)) {
            return false;
        }

        return $this->CI->Balance_model->change_by($user_id,$amount);
    }

    public function decrease($user_id, $amount)
    {
        $amount = $this->normalize_amount($amount);

        if ($amount === false || !$this->CI->User_model->find_active_by_id($user_id) || !$this->CI->Balance_model->find_by_user_id($user_id)) {
            return false;
        }

        return $this->CI->Balance_model->change_by($user_id,'-' . $amount);
    }

    public function get($user_id)
    {
        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            return false;
        }

        return $this->CI->Balance_model->find_by_user_id($user_id);
    }

    //هدف این تابع این است که لازم نباشد در قسمت‌های مختلف پروژه دائماً بررسی کنیم که تراکنش درآمدی است یا هزینه
    public function get_history_delta($type, $amount, $reverse = false)
    {
        if ($type !== 'income' && $type !== 'expense') {
            return false;
        }

        $amount = $this->normalize_amount($amount);

        if ($amount === false) {
            return false;
        }

        $is_negative = ($type === 'expense');

        if ($reverse) {
            $is_negative = !$is_negative;
        }

        if ($amount === '0.00') {
            return '0.00';
        }

        return $is_negative ? '-' . $amount : $amount;
    }

    //وظیفه دارد یک رویداد مربوط به تغییر موجودی کاربر را آماده کند، اطلاعات آن را اعتبارسنجی کند و در جدول balance_history ذخیره کند.
    private function create_history_event($data)
    {
        if (!is_array($data) || !isset($data['user_id'], $data['event_type'], $data['amount_delta'], $data['effective_date'])) {
            return false;
        }

        $user_id = $data['user_id'];

        $event_type = trim($data['event_type']);

        if (!is_string($data['event_type']) || $event_type === '' || strlen($event_type) > 40) {
            return false;
        }

        $amount_delta = $this->normalize_amount($data['amount_delta'], true);

        if ($amount_delta === false) {
            return false;
        }

        $effective_date = $data['effective_date'];

        if (!is_string($effective_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $effective_date)) {
            return false;
        }

        $date = explode('-', $effective_date);

        if (!checkdate((int) $date[1], (int) $date[2], (int) $date[0])) {
            return false;
        }

        $transaction_id = null;

        if (isset($data['transaction_id'])) {
            $transaction_id = $data['transaction_id'];
        }

        $target_balance = null;

        if (isset($data['target_balance'])) {
            $target_balance = $this->normalize_amount($data['target_balance'], true);

            if ($target_balance === false) {
                return false;
            }
        }

        if (($event_type === 'opening_balance' || $event_type === 'balance_reset') && $target_balance === null) {
            return false;
        }

        $description = null;

        if (isset($data['description'])) {
            if (!is_string($data['description']) || strlen($data['description']) > 255) {
                return false;
            }

            $description = $data['description'];
        }

        $record = array(
            'user_id' => $user_id,
            'transaction_id' => $transaction_id,
            'event_type' => $event_type,
            'amount_delta' => $amount_delta,
            'target_balance' => $target_balance,
            'effective_date' => $effective_date,
            'description' => $description
        );

        return $this->CI->Balance_history_model->create_event($record);
    }

    //این تابع مشخص می‌کند که یک تراکنش چقدر موجودی را افزایش یا کاهش داده و سپس این تغییر را در تاریخچه موجودی کاربر ذخیره می‌کند.
    public function record_transaction_history_event($user_id,$event_type,$effect_type,$amount,$effective_date,$transaction_id,$reverse = false) {

        $amount_delta = $this->get_history_delta($effect_type,$amount,$reverse);

        if ($amount_delta === false) {
            return false;
        }

        $event_id = $this->create_history_event(array(
            'user_id' => $user_id,
            'transaction_id' => $transaction_id,
            'event_type' => $event_type,
            'amount_delta' => $amount_delta,
            'effective_date' => $effective_date
        ));

        return $event_id !== false;
    }

    public function apply_transaction_effect($user_id, $type, $amount)
    {
        if ($type === 'income') {
            return $this->increase($user_id, $amount);
        }

        if ($type === 'expense') {
            return $this->decrease($user_id, $amount);
        }

        return false;
    }

    public function reverse_transaction_effect($user_id, $type, $amount)
    {
        if ($type === 'income') {
            return $this->decrease($user_id, $amount);
        }

        if ($type === 'expense') {
            return $this->increase($user_id, $amount);
        }

        return false;
    }

    public function update_manual_balance($user_id, $amount)
    {
        $amount = $this->normalize_amount($amount, true);

        if ($amount === false || $user_id <= 0) {
            return false;
        }

        $db = $this->CI->db;

        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return false;
        }

        $balance = $this->CI->Balance_model->find_by_user_id($user_id);

        if (!$balance) {
            $db->trans_rollback();
            return false;
        }
       
        if ($amount !== '0.00') {
            if (!$this->CI->Balance_model->change_by($user_id,$amount)) {
                $db->trans_rollback();
                return false;
            }

            $event_id = $this->create_history_event(
                array(
                    'user_id' => $user_id,
                    'event_type' => 'manual_adjustment',
                    'amount_delta' => $amount,
                    'effective_date' => date('Y-m-d'),
                    'description' => 'Manual balance adjustment'
                )
            );

            if ($event_id === false) {
                $db->trans_rollback();
                return false;
            }
        }

        if ($db->trans_status() === false) {
            $db->trans_rollback();
            return false;
        }

        return $db->trans_commit();
    }

    public function reset_manual_balance($user_id)
    {
        if ($user_id <= 0) {
            return false;
        }

        $db = $this->CI->db;

        $db->trans_begin();

        if (!$this->CI->User_model->find_active_by_id($user_id)) {
            $db->trans_rollback();
            return false;
        }

        $balance = $this->CI->Balance_model->find_by_user_id($user_id);

        if (!$balance) {
            $db->trans_rollback();
            return false;
        }

        $amount_delta = $this->negate_amount($balance->balance);

        if ($amount_delta === false) {
            $db->trans_rollback();
            return false;
        }

        if (!$this->CI->Balance_model->update_balance($user_id,'0.00')) {
            $db->trans_rollback();
            return false;
        }

        $event_id = $this->create_history_event(
            array(
                'user_id' => $user_id,
                'event_type' => 'balance_reset',
                'amount_delta' => $amount_delta,
                'target_balance' => '0.00',
                'effective_date' => date('Y-m-d'),
                'description' => 'Manual balance reset'
            )
        );

        if ($event_id === false ||$db->trans_status() === false) {
            $db->trans_rollback();
            return false;
        }

        return $db->trans_commit();
    }
}

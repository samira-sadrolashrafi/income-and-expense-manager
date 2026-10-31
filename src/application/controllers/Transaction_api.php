<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/Transaction_service.php';
require_once APPPATH . 'controllers/BaseApiController.php';

class Transaction_api extends BaseApiController
{
    private $transaction_service;

    public function __construct()
    {
        parent::__construct();
        $this->transaction_service = new Transaction_service();
    }

    public function index()
    {
        $params = $this->input->get();

        $is_paginated = isset($params['page']) || isset($params['search']) || isset($params['type']) || isset($params['category_id']) ||
            isset($params['date_from']) || isset($params['date_to']) || isset($params['sort']);

        if ($is_paginated) {
            $result = $this->transaction_service->get_all_paginated($this->current_user_id(),$params);
        } else {
            $result = array(
                'transactions' => $this->transaction_service->get_all( $this->current_user_id())
            );
        }

        if ($result === false || $result['transactions'] === false) {
            return $this->respond_service_error($this->transaction_service, 'دریافت تراکنش‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'لیست تراکنش‌ها با موفقیت دریافت شد.',200,$result);
    }

    public function deleted()
    {
        $params = $this->input->get();

        $result = $this->transaction_service->get_deleted_paginated($this->current_user_id(),$params);

        if ($result === false || $result['transactions'] === false) {
            return $this->respond_service_error($this->transaction_service, 'دریافت تراکنش‌های حذف‌شده انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        foreach ($result['transactions'] as $transaction) {
            $transaction->restore_available = !empty($transaction->restore_until) && strtotime($transaction->restore_until) > time();
        }

        return $this->respond_json(true,'فهرست تراکنش‌های حذف‌شده دریافت شد.',200,$result);
    }

    public function store()
    {
        $data = $this->input->post();

        $transaction_id = $this->transaction_service->create($this->current_user_id(),$data);

        if ($transaction_id === false) {
            return $this->respond_service_error($this->transaction_service,'ایجاد تراکنش انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'تراکنش با موفقیت ایجاد شد.',200,
            array(
                'transaction_id' => $transaction_id
            )
        );
    }

    public function update($transaction_id)
    {
        $data = $this->input->post();

        if (!$this->transaction_service->update($this->current_user_id(),$transaction_id,$data)) {
            return $this->respond_service_error($this->transaction_service,'ویرایش تراکنش انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'تراکنش با موفقیت ویرایش شد.');
    }

    public function delete($transaction_id)
    {
        if (!$this->transaction_service->delete($this->current_user_id(),$transaction_id)) {
            return $this->respond_service_error($this->transaction_service,'حذف تراکنش انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'تراکنش با موفقیت حذف شد.');
    }

    public function restore($transaction_id)
    {
        if (!$this->transaction_service->restore($this->current_user_id(), $transaction_id)) {
            return $this->respond_service_error($this->transaction_service,'بازگردانی تراکنش انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'تراکنش با موفقیت بازگردانی شد.');
    }
}

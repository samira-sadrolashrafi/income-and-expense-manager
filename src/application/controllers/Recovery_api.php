<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/BaseApiController.php';
require_once APPPATH . 'services/Recovery_service.php';

class Recovery_api extends BaseApiController
{
    private $recovery_service;

    public function __construct()
    {
        parent::__construct(false);

        $this->recovery_service = new Recovery_service();
    }

    public function question()
    {
        $result = $this->recovery_service->question(
            $this->input->post('username')
        );

        return $this->send_result($result);
    }

    public function verify()
    {
        $result = $this->recovery_service->verify(
            $this->input->post('username'),
            $this->input->post('answer')
        );

        return $this->send_result($result);
    }

    public function reset()
    {
        $result = $this->recovery_service->reset(
            $this->input->post('password'),
            $this->input->post('password_confirm')
        );

        return $this->send_result($result);
    }

    //ثبت یا تنظیم سوال امنیتی برای کاربر وارد شده
    public function enroll()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $result = $this->recovery_service->enroll(
            $this->current_user_id(),
            $this->input->post()
        );

        return $this->send_result($result);
    }

    private function send_result($result)
    {
        $success = $result['success'];
        $message = $result['message'];
        $status = $result['status'];

        unset($result['success'], $result['message'], $result['status']);

        return $this->respond_json($success, $message, $status, $result);
    }
}

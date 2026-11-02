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
        $payload = json_decode(file_get_contents('php://input'), true);

        $result = $this->recovery_service->question($payload['username'] ?? null);

        return $this->send_result($result);
    }

    public function verify()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $result = $this->recovery_service->verify($payload['answer'] ?? null);

        return $this->send_result($result);
    }

    public function reset()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $result = $this->recovery_service->reset(
            $payload['password'] ?? null,
            $payload['password_confirm'] ?? null
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/BaseApiController.php';
require_once APPPATH . 'services/Auth_service.php';

class Auth_api extends BaseApiController
{
    private $auth_service;

    public function __construct()
    {
        parent::__construct(false);

        $this->auth_service = new Auth_service();
    }


    public function register()
    {
        $result = $this->auth_service->register($this->input->post());

        return $this->send_result($result);
    }


    public function login()
    {
        $result = $this->auth_service->login($this->input->post());

        return $this->send_result($result);
    }


    public function restore_account()
    {
        $result = $this->auth_service->restore_deleted_account($this->input->post());

        return $this->send_result($result);
    }


    public function logout()
    {
        $result = $this->auth_service->logout();

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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/Category_service.php';
require_once APPPATH . 'controllers/BaseApiController.php';

class Category_api extends BaseApiController
{
    private $category_service;

    public function __construct()
    {
        parent::__construct();
        $this->category_service = new Category_service();
    }

    public function index()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $params = $this->input->get();

        $is_paginated =isset($params['page']) || isset($params['search']) || isset($params['type']) || isset($params['sort']);

        if ($is_paginated) {
            $result = $this->category_service->get_all_paginated($this->user_id, $params);
        } else {
            $result = array(
                'categories' => $this->category_service->get_all($this->user_id)
            );
        }

        if ($result === false || $result['categories'] === false) {
            return $this->respond_service_error($this->category_service, 'دریافت دسته‌بندی‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true, 'فهرست دسته‌بندی‌ها دریافت شد.', 200, $result);
    }


    public function deleted()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $params = $this->input->get();

        $result = $this->category_service->get_deleted_paginated($this->user_id,$params);
    
        if ($result === false || $result['categories'] === false) {
            return $this->respond_service_error(
                $this->category_service,
                'دریافت دسته‌بندی‌های حذف‌شده انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        foreach ($result['categories'] as $category) {
            $category->restore_available = !empty($category->restore_until) && strtotime($category->restore_until) > time();
        }

        return $this->respond_json(true, 'فهرست دسته‌بندی‌های حذف‌شده دریافت شد.', 200, $result);
    }


    public function store()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $data = $this->input->post();

        $category_id = $this->category_service->create($this->user_id,$data);

        if ($category_id === false) {
            return $this->respond_service_error($this->category_service, 'ایجاد دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true, 'دسته‌بندی با موفقیت ایجاد شد.', 200,
            array(
                'category_id' => $category_id
            )
        );
    }


    public function update($category_id)
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $data = $this->input->post();

        if (!$this->category_service->update($this->user_id, $category_id, $data)) {
            return $this->respond_service_error($this->category_service, 'ویرایش دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true, 'دسته‌بندی با موفقیت ویرایش شد.');
    }

    public function delete($category_id)
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        if (!$this->category_service->delete($this->user_id,$category_id)) {
            return $this->respond_service_error($this->category_service,'حذف دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true,'دسته‌بندی با موفقیت حذف شد.');
    }


    public function restore($category_id)
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        if (!$this->category_service->restore($this->user_id,$category_id)) {
            return $this->respond_service_error($this->category_service,'بازگردانی دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.');
        }

        return $this->respond_json(true, 'دسته‌بندی با موفقیت بازگردانی شد.');
    }
}
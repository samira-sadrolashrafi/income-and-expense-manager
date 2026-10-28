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

        $page_param = $this->input->get('page');
        $per_page_param = $this->input->get('per_page');

        $search = $this->input->get('search');
        $type = $this->input->get('type');
        $sort = $this->input->get('sort');

        $has_pagination_or_filters = $page_param !== null || $per_page_param !== null || $search !== null || $type !== null || $sort !== null;

        if (!$has_pagination_or_filters) {
            $categories = $this->category_service->get_all(
                $this->user_id
            );

            if ($categories === false) {
                return $this->respond_service_error(
                    $this->category_service,
                    'دریافت دسته‌بندی‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.'
                );
            }

            return $this->respond_json(
                true,
                'فهرست دسته‌بندی‌ها دریافت شد.',
                200,
                array(
                    'categories' => $categories
                )
            );
        }

        $page = max(1, (int) $page_param);

        $per_page = (int) $per_page_param;

        if ($per_page < 1) {
            $per_page = 10;
        }

        $per_page = min($per_page, 100);

        $filters = array(
            'search' => $search,
            'type' => $type,
            'sort' => $sort
        );

        $result = $this->category_service->get_all_paginated(
            $this->user_id,
            $page,
            $per_page,
            $filters
        );

        if ($result === false) {
            return $this->respond_service_error(
                $this->category_service,
                'دریافت دسته‌بندی‌ها انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        return $this->respond_json(
            true,
            'فهرست دسته‌بندی‌ها دریافت شد.',
            200,
            array(
                'categories' => $result['categories'],
                'pagination' => $result['pagination']
            )
        );
    }


    public function deleted()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        if ($this->input->get('page') === null && $this->input->get('per_page') === null) {
            $categories = $this->category_service->get_deleted(
                $this->user_id
            );

            if ($categories === false) {
                return $this->respond_service_error(
                    $this->category_service,
                    'دریافت دسته‌بندی‌های حذف‌شده انجام نشد؛ لطفاً دوباره تلاش کنید.'
                );
            }

            foreach ($categories as $category) {
                $category->restore_available = !empty($category->restore_until) && strtotime($category->restore_until) > time();
            }

            return $this->respond_json(
                true,
                'فهرست دسته‌بندی‌های حذف‌شده دریافت شد.',
                200,
                array(
                    'categories' => $categories
                )
            );
        }

        $page = max(1, (int) $this->input->get('page'));
        $per_page = (int) $this->input->get('per_page');

        if ($per_page < 1) {
            $per_page = 10;
        }

        $per_page = min($per_page, 100);

        $result = $this->category_service->get_deleted_paginated(
            $this->user_id,
            $page,
            $per_page
        );

        if ($result === false) {
            return $this->respond_service_error(
                $this->category_service,
                'دریافت دسته‌بندی‌های حذف‌شده انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        foreach ($result['categories'] as $category) {
            $category->restore_available =
                !empty($category->restore_until) &&
                strtotime($category->restore_until) > time();
        }

        return $this->respond_json(
            true,
            'فهرست دسته‌بندی‌های حذف‌شده دریافت شد.',
            200,
            array(
                'categories' => $result['categories'],
                'pagination' => $result['pagination']
            )
        );
    }


    public function store()
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        $data = $this->category_input_data(true);

        $category_id = $this->category_service->create(
            $this->user_id,
            $data
        );

        if ($category_id === false) {
            return $this->respond_service_error(
                $this->category_service,
                'ایجاد دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        return $this->respond_json(
            true,
            'دسته‌بندی با موفقیت ایجاد شد.',
            200,
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

        $data = $this->category_input_data();

        if (!$this->category_service->update(
            $this->user_id,
            $category_id,
            $data
        )) {
            return $this->respond_service_error(
                $this->category_service,
                'ویرایش دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        return $this->respond_json(
            true,
            'دسته‌بندی با موفقیت ویرایش شد.'
        );
    }


    public function delete($category_id)
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        if (!$this->category_service->delete(
            $this->user_id,
            $category_id
        )) {
            return $this->respond_service_error(
                $this->category_service,
                'حذف دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        return $this->respond_json(
            true,
            'دسته‌بندی با موفقیت حذف شد.'
        );
    }


    public function restore($category_id)
    {
        if (!$this->require_authenticated_user()) {
            return;
        }

        if (!$this->category_service->restore(
            $this->user_id,
            $category_id
        )) {
            return $this->respond_service_error(
                $this->category_service,
                'بازگردانی دسته‌بندی انجام نشد؛ لطفاً دوباره تلاش کنید.'
            );
        }

        return $this->respond_json(
            true,
            'دسته‌بندی با موفقیت بازگردانی شد.'
        );
    }


    private function category_input_data($allow_json = false)
    {
        $data = array(
            'name' => $this->input->post('name'),
            'type' => $this->input->post('type')
        );

        if ($allow_json && $data['name'] === null && $data['type'] === null) {
            $json = json_decode(
                $this->input->raw_input_stream,
                true
            );

            if (is_array($json)) {
                $data = array(
                    'name' => $json['name'] ?? null,
                    'type' => $json['type'] ?? null
                );
            }
        }

        return $data;
    }
}
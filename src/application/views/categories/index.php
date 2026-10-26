<?php
/**
 * Tarazino | Categories management view (CodeIgniter 3).
 *
 * Bootstrap 5.3 RTL handles layout and components.
 * Font Awesome supplies icons; no category icons/colors are persisted.
 *
 * Vue (assets/js/vue/categories/index.js) owns the whole page: the active and
 * deleted lists, filters, server-side pagination, the tabs, the add/edit
 * modal, the delete confirmation and the restore action. The API filters on
 * the server; Bootstrap only shows/hides the modals.
 *
 * Vue contracts:
 * - categoriesMain is the Vue mount point (data-categories-api-url)
 * - categoriesMessage, categoryFormMessage, deleteCategoryMessage
 * - categoryModal, categoryForm, deleteCategoryModal
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layouts/header', array(
    'page_title' => 'مدیریت دسته‌بندی‌ها',
    'page_styles' => array(
        base_url(
            'assets/css/dashboard.css?v=' .
            filemtime(FCPATH . 'assets/css/dashboard.css')
        )
    )
));

$categories_api_url = site_url('api/categories');
$view_username = isset($username) && is_string($username)
    ? $username
    : '';
?>

<div class="container-fluid dashboard-shell px-0">
    <div class="row g-0">
        <?php
        $this->load->view('layouts/app_sidebar', array(
            'active_page' => 'categories'
        ));
        ?>

        <main class="col-lg-9 col-xl-10 dashboard-main"
              id="categoriesMain"
              data-component="categories-page"
              data-categories-api-url="<?php echo html_escape($categories_api_url); ?>">

            <?php
            $this->load->view('layouts/app_header', array(
                'app_page_title' => 'دسته‌بندی‌ها',
                'app_page_description' =>
                    'دسته‌بندی‌های درآمد و هزینه خود را مدیریت کنید.',
                'app_username' => $view_username
            ));
            ?>

            <div class="px-3 px-xl-4 pt-4">

                <!-- Page heading -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">مدیریت دسته‌بندی‌ها</h2>
                        <p class="text-body-secondary small mb-0">
                            دسته‌بندی‌های درآمد و هزینه را ایجاد، ویرایش و در صورت نیاز بازیابی کنید.
                        </p>
                    </div>

                    <button type="button"
                            id="addCategoryButton"
                            class="btn btn-primary d-inline-flex align-items-center gap-2"
                            @click="openCreateModal">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        <span>افزودن دسته‌بندی</span>
                    </button>
                </div>

                <div v-if="message.text"
                     id="categoriesMessage"
                     class="alert"
                     :class="'alert-' + message.type"
                     :key="message.seq"
                     role="alert"
                     aria-live="polite">{{ message.text }}</div>

<?php $this->load->view('categories/_filters'); ?>

<?php $this->load->view('categories/_lists'); ?>
            </div>

<?php $this->load->view('categories/_modals'); ?>

        </main>
    </div>
</div>

<?php $this->load->view('layouts/logout_modal'); ?>

<?php
$this->load->view('layouts/footer', array(
    'page_scripts' => array(
        base_url('assets/js/api.js'),

        // Vue and feature modules must load before the composition entry point.
        'https://unpkg.com/vue@3.5.13/dist/vue.global.js',
        base_url('assets/js/vue/categories/categories-list.js'),
        base_url('assets/js/vue/categories/categories-form.js'),
        base_url('assets/js/vue/categories/categories-delete.js'),
        base_url('assets/js/vue/categories/index.js')
    )
));
?>

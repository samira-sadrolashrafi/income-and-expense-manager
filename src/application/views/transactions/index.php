<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tarazino | Transactions page
 * CodeIgniter 3, Bootstrap 5.3 RTL.
 *
 * Vue (assets/js/vue/transactions/index.js) owns the page: the active and
 * deleted lists, filters, server-side pagination, the tabs, the add/edit form
 * modal and the delete/restore actions. The server filters, paginates and
 * applies every balance change; the page only displays the result.
 *
 * Vue contracts:
 * - transactionsMain is the Vue mount point (data-transactions-api-url,
 *   data-categories-api-url)
 * - transactionsMessage, deleteTransactionMessage
 * - transactionForm / transactionModal (shared with the dashboard)
 * - deleteTransactionModal
 */

$transactions_api_url = site_url('api/transactions');
$categories_api_url = site_url('api/categories');

$view_username = isset($display_name)
    && is_string($display_name)
    && trim($display_name) !== ''
        ? trim($display_name)
        : (
            isset($username)
            && is_string($username)
            && trim($username) !== ''
                ? trim($username)
                : 'کاربر'
        );

$this->load->view('layouts/header', array(
    'page_title' => 'مدیریت تراکنش‌ها',
    'page_styles' => array(
        base_url(
            'assets/css/dashboard.css?v=' .
            filemtime(FCPATH . 'assets/css/dashboard.css')
        )
    )
));
?>

<div class="container-fluid dashboard-shell px-0">
    <div class="row g-0">

        <?php
        $this->load->view(
            'layouts/app_sidebar',
            array('active_page' => 'transactions')
        );
        ?>

        <main
            class="col-lg-9 col-xl-10 dashboard-main"
            id="transactionsMain"
            data-component="transactions-page"
            data-transactions-api-url="<?php echo html_escape($transactions_api_url); ?>"
            data-categories-api-url="<?php echo html_escape($categories_api_url); ?>"
        >

            <?php
            $this->load->view('layouts/app_header', array(
                'app_page_title' => 'تراکنش‌ها',
                'app_page_description' =>
                    'ثبت، ویرایش، مدیریت و بازیابی درآمدها و هزینه‌ها',
                'app_username' => $view_username
            ));
            ?>

            <div class="px-3 px-xl-4 pt-4">

                <!-- Page heading -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">
                            فهرست تراکنش‌ها
                        </h2>

                        <p class="text-body-secondary small mb-0">
                            سوابق مالی خود را جست‌وجو و مدیریت کنید.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="addTransactionButton"
                        class="btn btn-primary d-inline-flex align-items-center gap-2"
                        :disabled="error !== ''"
                        @click="openCreateModal"
                    >
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        <span>افزودن تراکنش</span>
                    </button>
                </div>

                <div
                    v-if="message.text"
                    id="transactionsMessage"
                    class="alert"
                    :class="'alert-' + message.type"
                    :key="message.seq"
                    role="alert"
                    aria-live="polite"
                >{{ message.text }}</div>

                <!-- Active and deleted transactions -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">

                    <div
                        class="btn-group"
                        role="group"
                        aria-label="وضعیت فهرست تراکنش‌ها"
                    >
                        <button
                            type="button"
                            id="showActiveTransactionsButton"
                            class="btn"
                            :class="showDeleted
                                ? 'btn-outline-primary'
                                : 'btn-primary'"
                            aria-controls="activeTransactionsPanel"
                            :aria-pressed="showDeleted ? 'false' : 'true'"
                            @click="showActiveTab()"
                        >
                            <i class="fa-solid fa-list-check me-1" aria-hidden="true"></i>
                            فعال
                        </button>

                        <button
                            type="button"
                            id="showDeletedTransactionsButton"
                            class="btn"
                            :class="showDeleted
                                ? 'btn-primary'
                                : 'btn-outline-primary'"
                            aria-controls="deletedTransactionsPanel"
                            :aria-pressed="showDeleted ? 'true' : 'false'"
                            @click="showDeletedTab()"
                        >
                            <i class="fa-regular fa-trash-can me-1" aria-hidden="true"></i>
                            حذف‌شده
                        </button>
                    </div>

                    <span class="small text-body-secondary d-inline-flex align-items-center gap-2">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        تراکنش‌های حذف‌شده تا ۳۰ روز قابل بازیابی‌اند.
                    </span>
                </div>

<?php $this->load->view('transactions/_lists'); ?>

            </div>

<?php $this->load->view('transactions/_modals'); ?>

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
        base_url('assets/js/vue/transactions/transactions-list.js'),
        base_url('assets/js/vue/transactions/transactions-form.js'),
        base_url('assets/js/vue/transactions/transactions-delete.js'),
        base_url('assets/js/vue/transactions/index.js')
    )
));
?>

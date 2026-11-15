<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('layouts/header', array(
    'page_title' => 'داشبورد',
    'page_styles' => array(base_url('assets/css/dashboard.css'))
));
?>

<div id="dashboardApp">

    <div class="container-fluid dashboard-shell px-0"
         data-balance-url="<?php echo html_escape(site_url('api/dashboard/balance')); ?>"
         data-categories-url="<?php echo html_escape(site_url('api/categories')); ?>"
         data-transactions-url="<?php echo html_escape(site_url('api/transactions')); ?>"
         data-cashflow-url="<?php echo html_escape(site_url('api/dashboard/cashflow')); ?>">

        <div class="row g-0">

            <?php $this->load->view('layouts/app_sidebar', array('active_page' => 'dashboard')); ?>

            <main class="col-lg-9 col-xl-10 dashboard-main" id="dashboardMain">

                <?php $this->load->view('layouts/app_header', array(
                    'app_page_title' => 'داشبورد',
                    'app_page_description' => 'نمای کلی و تحلیل وضعیت مالی شما',
                    'app_username' => $username
                )); ?>

                <div class="px-3 px-xl-4 pt-4">

                    <div v-if="pageMessage"
                         class="alert"
                         :class="'alert-' + pageMessageType"
                         role="alert"
                         aria-live="polite">

                        {{ pageMessage }}

                    </div>


                    <!-- Welcome -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

                        <div>

                            <h2 class="h4 fw-bold mb-1">
                                سلام، <?php echo html_escape($username); ?>
                                <span aria-hidden="true">👋</span>
                            </h2>

                            <p class="text-body-secondary small mb-0">
                                با ترازینو، کنترل دخل‌وخرجت ساده‌تر میشه!
                            </p>

                        </div>

                        <a class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"
                           href="<?php echo html_escape(site_url('transactions')); ?>">

                            <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                            مشاهده تراکنش‌ها

                        </a>

                    </div>


                    <!-- Date Filter -->
                    <section class="card dashboard-card mb-4"
                             aria-labelledby="dashboardDateHeading"
                             id="dashboardDateFilter">

                        <div class="card-body py-3 px-3 px-md-4">

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                                <div>

                                    <h3 class="h6 fw-bold mb-1" id="dashboardDateHeading">

                                        <i class="fa-regular fa-calendar-days text-primary me-2"
                                           aria-hidden="true"></i>

                                        بازه زمانی گزارش

                                    </h3>

                                    <p class="text-body-secondary small mb-0">
                                        بازه انتخابی برای نمودار روند درآمد، هزینه و موجودی اعمال می‌شود.
                                    </p>

                                </div>

                                <div class="btn-group flex-wrap"
                                     role="group"
                                     aria-label="انتخاب بازه زمانی گزارش"
                                     data-dashboard-period>

                                    <input class="btn-check"
                                           type="radio"
                                           name="dashboardPeriod"
                                           id="dashboardPeriodWeek"
                                           value="week"
                                           autocomplete="off">

                                    <label class="btn btn-outline-primary btn-sm"
                                           for="dashboardPeriodWeek">
                                        هفته جاری
                                    </label>


                                    <input class="btn-check"
                                           type="radio"
                                           name="dashboardPeriod"
                                           id="dashboardPeriodMonth"
                                           value="month"
                                           autocomplete="off">

                                    <label class="btn btn-outline-primary btn-sm"
                                           for="dashboardPeriodMonth">
                                        ماه جاری
                                    </label>


                                    <input class="btn-check"
                                           type="radio"
                                           name="dashboardPeriod"
                                           id="dashboardPeriodYear"
                                           value="year"
                                           autocomplete="off"
                                           checked>

                                    <label class="btn btn-outline-primary btn-sm"
                                           for="dashboardPeriodYear">
                                        سال جاری
                                    </label>


                                    <input class="btn-check"
                                           type="radio"
                                           name="dashboardPeriod"
                                           id="dashboardPeriodCustom"
                                           value="custom"
                                           autocomplete="off">

                                    <label class="btn btn-outline-primary btn-sm"
                                           for="dashboardPeriodCustom">
                                        بازه دلخواه
                                    </label>

                                </div>

                            </div>


                            <div class="row g-2 align-items-end mt-2 d-none"
                                 id="dashboardCustomRange"
                                 aria-label="تاریخ شروع و پایان بازه دلخواه">

                                <div class="col-12 col-md-4 col-xl-3">

                                    <label for="dashboardDateFrom"
                                           class="form-label small mb-1">
                                        از تاریخ
                                    </label>

                                    <input type="date"
                                           id="dashboardDateFrom"
                                           name="from"
                                           class="form-control form-control-sm"
                                           autocomplete="off">

                                </div>


                                <div class="col-12 col-md-4 col-xl-3">

                                    <label for="dashboardDateTo"
                                           class="form-label small mb-1">
                                        تا تاریخ
                                    </label>

                                    <input type="date"
                                           id="dashboardDateTo"
                                           name="to"
                                           class="form-control form-control-sm"
                                           autocomplete="off">

                                </div>

                            </div>


                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">

                                <p id="dashboardDateFilterMessage"
                                   class="form-text mb-0 flex-grow-1"
                                   role="status"
                                   aria-live="polite">

                                    در حال آماده‌سازی بازه گزارش...

                                </p>

                                <button type="button"
                                        id="applyDashboardDateFilter"
                                        class="btn btn-primary btn-sm">

                                    <i class="fa-solid fa-filter me-1" aria-hidden="true"></i>
                                    اعمال بازه

                                </button>

                            </div>

                        </div>

                    </section>


                    <!-- Financial Cards -->
                    <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-3"
                             aria-label="شاخص‌های مالی">


                        <!-- Balance -->
                        <div class="col">

                            <article class="card dashboard-card tz-kpi h-100 border-primary-subtle">

                                <div class="card-body p-3 p-xxl-4">

                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">

                                        <h3 class="h6 text-body-secondary mb-0">
                                            موجودی فعلی
                                        </h3>

                                        <span class="bg-primary-subtle text-primary rounded-3 p-2"
                                              aria-hidden="true">

                                            <i class="fa-solid fa-wallet"></i>

                                        </span>

                                    </div>

                                    <p class="mb-0 mt-auto">

                                        <span class="tz-kpi-value tz-number text-primary"
                                              aria-live="polite">

                                            {{ balanceAmount === null ? 'در حال دریافت...' : formatMoney(balanceAmount) }}

                                        </span>

                                    </p>

                                    <span class="text-body-secondary small">
                                        تومان · مستقل از فیلتر زمانی
                                    </span>

                                </div>

                            </article>

                        </div>


                        <!-- Income -->
                        <div class="col">

                            <article class="card dashboard-card tz-kpi h-100 border-success-subtle">

                                <div class="card-body p-3 p-xxl-4">

                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">

                                        <h3 class="h6 text-body-secondary mb-0">
                                            درآمد دوره
                                        </h3>

                                        <span class="bg-success-subtle text-success rounded-3 p-2"
                                              aria-hidden="true">

                                            <i class="fa-solid fa-arrow-trend-up"></i>

                                        </span>

                                    </div>

                                    <p class="mb-0 mt-auto">

                                        <span id="incomeAmount"
                                              class="tz-kpi-value tz-number text-success"
                                              aria-live="polite">

                                            در حال دریافت...

                                        </span>

                                    </p>

                                    <span class="text-body-secondary small">
                                        تومان · در بازه انتخابی
                                    </span>

                                </div>

                            </article>

                        </div>


                        <!-- Expense -->
                        <div class="col">

                            <article class="card dashboard-card tz-kpi h-100 border-danger-subtle">

                                <div class="card-body p-3 p-xxl-4">

                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">

                                        <h3 class="h6 text-body-secondary mb-0">
                                            هزینه دوره
                                        </h3>

                                        <span class="bg-danger-subtle text-danger rounded-3 p-2"
                                              aria-hidden="true">

                                            <i class="fa-solid fa-arrow-trend-down"></i>

                                        </span>

                                    </div>

                                    <p class="mb-0 mt-auto">

                                        <span id="expenseAmount"
                                              class="tz-kpi-value tz-number text-danger"
                                              aria-live="polite">

                                            در حال دریافت...

                                        </span>

                                    </p>

                                    <span class="text-body-secondary small">
                                        تومان · در بازه انتخابی
                                    </span>

                                </div>

                            </article>

                        </div>


                        <!-- Debt -->
                        <div class="col">

                            <article class="card dashboard-card tz-kpi h-100 border-warning-subtle">

                                <div class="card-body p-3 p-xxl-4">

                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">

                                        <h3 class="h6 text-body-secondary mb-0">
                                            بدهی فعلی
                                        </h3>

                                        <span class="bg-warning-subtle text-warning-emphasis rounded-3 p-2"
                                              aria-hidden="true">

                                            <i class="fa-solid fa-credit-card"></i>

                                        </span>

                                    </div>

                                    <p class="mb-0 mt-auto">

                                        <span class="tz-kpi-value tz-number text-danger"
                                              aria-live="polite">

                                            {{ debtAmount === null ? 'در حال دریافت...' : formatMoney(debtAmount) }}

                                        </span>

                                    </p>

                                    <span class="text-body-secondary small">
                                        تومان · مستقل از فیلتر زمانی
                                    </span>

                                </div>

                            </article>

                        </div>

                    </section>


                    <!-- Quick Actions -->
                    <section class="d-flex flex-wrap align-items-center gap-2 mb-4"
                             aria-label="عملیات سریع">


                        <button type="button"
                                class="btn btn-success"
                                id="addDashboardTransactionButton"
                                @click="openTransactionModal"
                                :disabled="categories.length === 0 || savingTransaction">

                            <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>
                            ثبت تراکنش

                        </button>


                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#dashboardAddCategoryModal"
                                @click="resetCategoryForm">

                            <i class="fa-solid fa-folder-plus me-1" aria-hidden="true"></i>
                            افزودن دسته‌بندی

                        </button>


                        <button type="button"
                                class="btn btn-outline-primary"
                                @click="openBalanceModal">

                            <i class="fa-solid fa-pen-to-square me-1" aria-hidden="true"></i>
                            تغییر دستی موجودی

                        </button>


                        <button type="button"
                                class="btn btn-outline-danger"
                                @click="openResetBalanceModal">

                            <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>
                            صفر کردن موجودی

                        </button>

                    </section>


                    <!-- Chart -->
                    <div class="row g-3 mb-4">

                        <div class="col-12">

                            <section class="card dashboard-card h-100"
                                     aria-labelledby="cashflowChartHeading">

                                <div class="card-body p-3 p-xxl-4">

                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

                                        <h3 id="cashflowChartHeading" class="h6 fw-bold mb-0">

                                            <i class="fa-solid fa-chart-line text-primary me-2"
                                               aria-hidden="true"></i>

                                            روند درآمد، هزینه و موجودی

                                        </h3>

                                        <span class="text-body-secondary small">
                                            بازه مشترک گزارش
                                        </span>

                                    </div>


                                    <div class="d-flex flex-wrap gap-3 small mb-2"
                                         aria-label="راهنمای نمودار">

                                        <span>
                                            <i class="fa-solid fa-circle text-success fa-2xs me-1"
                                               aria-hidden="true"></i>
                                            درآمد
                                        </span>

                                        <span>
                                            <i class="fa-solid fa-circle text-danger fa-2xs me-1"
                                               aria-hidden="true"></i>
                                            هزینه
                                        </span>

                                        <span>
                                            <i class="fa-solid fa-circle text-primary fa-2xs me-1"
                                               aria-hidden="true"></i>
                                            موجودی
                                        </span>

                                    </div>


                                    <div class="tz-chart-area"
                                         aria-label="نمودار زمانی درآمد، هزینه و موجودی">

                                        <canvas id="cashflowChart"
                                                role="img"
                                                aria-label="روند درآمد، هزینه و موجودی در بازه انتخابی">
                                        </canvas>

                                    </div>

                                </div>

                            </section>

                        </div>

                    </div>


                    <!-- Recent Transactions -->
                    <section class="card dashboard-card mb-4"
                             aria-labelledby="recentTransactionsHeading">

                        <div class="card-header bg-body border-0 px-3 px-xxl-4 pt-4 pb-2 d-flex flex-wrap align-items-center justify-content-between gap-2">

                            <h3 id="recentTransactionsHeading" class="h6 fw-bold mb-0">

                                <i class="fa-solid fa-clock-rotate-left text-primary me-2"
                                   aria-hidden="true"></i>

                                آخرین تراکنش‌ها

                            </h3>

                            <a class="link-primary small text-decoration-none"
                               href="<?php echo html_escape(site_url('transactions')); ?>">

                                مشاهده همه
                                <i class="fa-solid fa-arrow-left ms-1" aria-hidden="true"></i>

                            </a>

                        </div>


                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle mb-0">

                                    <thead class="table-light">

                                        <tr>

                                            <th scope="col" class="ps-3 ps-xxl-4">
                                                عنوان
                                            </th>

                                            <th scope="col">
                                                دسته‌بندی
                                            </th>

                                            <th scope="col">
                                                مبلغ (تومان)
                                            </th>

                                            <th scope="col">
                                                تاریخ
                                            </th>

                                            <th scope="col" class="text-center pe-3">
                                                عملیات
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody id="dashboardTransactionsBody"
                                           aria-live="polite">

                                        <tr v-if="loadingTransactions">

                                            <td colspan="5"
                                                class="text-center text-body-secondary py-5">

                                                در حال دریافت تراکنش‌ها...

                                            </td>

                                        </tr>


                                        <tr v-else-if="transactionsError">

                                            <td colspan="5"
                                                class="text-center text-danger py-5">

                                                {{ transactionsError }}

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary ms-2"
                                                        @click="loadTransactions">
                                                    تلاش دوباره
                                                </button>

                                            </td>

                                        </tr>


                                        <tr v-else-if="transactions.length === 0">

                                            <td colspan="5"
                                                class="text-center text-body-secondary py-5">

                                                هنوز تراکنشی ثبت نشده است.

                                            </td>

                                        </tr>


                                        <template v-else>

                                            <tr v-for="transaction in transactions"
                                                :key="transaction.id">

                                                <td class="ps-3 ps-xxl-4">
                                                    {{ transaction.title || '—' }}
                                                </td>

                                                <td>
                                                    {{ categoryName(transaction) }}
                                                </td>

                                                <td :class="categoryType(transaction) === 'expense' ? 'text-danger' : 'text-success'">
                                                    {{ categoryType(transaction) === 'expense' ? '− ' : '+ ' }}{{ formatMoney(Number(transaction.amount)) }}
                                                </td>

                                                <td class="text-nowrap">
                                                    {{ formatDate(transaction.transaction_date) }}
                                                </td>

                                                <td class="text-nowrap text-center pe-3">

                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-primary me-1"
                                                            @click="editTransaction(transaction)"
                                                            :disabled="savingTransaction || deletingTransaction">

                                                        ویرایش

                                                    </button>


                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            @click="askDelete(transaction)"
                                                            :disabled="savingTransaction || deletingTransaction">

                                                        حذف

                                                    </button>

                                                </td>

                                            </tr>

                                        </template>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </section>

                </div>

            </main>

        </div>

    </div>


    <!-- Balance Modal -->
    <div class="modal fade tz-modal"
         id="dashboardBalanceModal"
         tabindex="-1"
         aria-labelledby="balanceModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form @submit.prevent="updateBalance" novalidate>

                    <div class="modal-header">

                        <h2 class="modal-title fs-5" id="balanceModalLabel">

                            <i class="fa-solid fa-wallet text-primary me-2"
                               aria-hidden="true"></i>

                            تغییر دستی موجودی

                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="بستن">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div v-if="balanceModalMessage"
                             class="alert alert-danger"
                             role="alert"
                             aria-live="polite">

                            {{ balanceModalMessage }}

                        </div>


                        <p class="small text-body-secondary">
                            مقدار واردشده به موجودی فعلی اضافه می‌شود.
                            برای کاهش موجودی، عدد منفی وارد کنید.
                        </p>


                        <label class="form-label fw-semibold"
                               for="balanceInput">

                            مبلغ تغییر موجودی
                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group" dir="ltr">

                            <span class="input-group-text">
                                تومان
                            </span>

                            <input type="text"
                                   class="form-control"
                                   id="balanceInput"
                                   v-model="balanceInput"
                                   @input="formatBalanceAmount"
                                   autocomplete="off">

                        </div>


                        <div class="form-text">
                            مثال: <span dir="ltr">۵۰۰,۰۰۰</span> برای افزایش یا
                            <span dir="ltr">-۲۰۰,۰۰۰</span> برای کاهش.
                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">

                            انصراف

                        </button>


                        <button type="submit"
                                class="btn btn-primary"
                                :disabled="savingBalance">

                            <i class="fa-solid fa-floppy-disk me-1"
                               aria-hidden="true"></i>

                            ذخیره

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Reset Balance Modal -->
    <div class="modal fade tz-modal"
         id="dashboardResetBalanceModal"
         tabindex="-1"
         aria-labelledby="resetBalanceModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h2 class="modal-title fs-5"
                        id="resetBalanceModalLabel">

                        <i class="fa-solid fa-triangle-exclamation text-danger me-2"
                           aria-hidden="true"></i>

                        صفر کردن موجودی

                    </h2>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="بستن">
                    </button>

                </div>


                <div class="modal-body">

                    <p class="mb-2">
                        آیا از صفر کردن موجودی حساب اطمینان دارید؟
                    </p>

                    <p class="small text-body-secondary mb-0">
                        با تأیید این عملیات، موجودی فعلی برابر صفر خواهد شد.
                    </p>


                    <div v-if="resetBalanceMessage"
                         class="alert alert-danger mt-3 mb-0"
                         role="alert"
                         aria-live="polite">

                        {{ resetBalanceMessage }}

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">

                        انصراف

                    </button>


                    <button type="button"
                            class="btn btn-danger"
                            @click="resetBalance"
                            :disabled="resettingBalance">

                        <i class="fa-solid fa-rotate-left me-1"
                           aria-hidden="true"></i>

                        تأیید و صفر کردن

                    </button>

                </div>

            </div>

        </div>

    </div>


<!-- Transaction Modal -->
    <div class="modal fade tz-modal"
         data-component="transaction-form-modal"
         id="dashboardTransactionModal"
         tabindex="-1"
         aria-labelledby="dashboardTransactionModalTitle"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <form id="dashboardTransactionForm"
                      @submit.prevent="saveTransaction"
                      novalidate
                      autocomplete="off">


                    <div class="modal-header px-4 py-3">

                        <div class="d-flex align-items-center gap-2">

                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary p-2" aria-hidden="true">
                                <i class="fa-solid fa-receipt"></i>
                            </span>

                            <h2 class="modal-title fs-5 fw-semibold"
                                id="dashboardTransactionModalTitle">

                                {{ editingId ? 'ویرایش تراکنش' : 'افزودن تراکنش' }}

                            </h2>

                        </div>


                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="بستن">
                        </button>

                    </div>


                    <div class="modal-body p-4">


                        <div v-if="transactionMessage"
                             id="dashboardTransactionFormMessage"
                             class="alert alert-danger"
                             role="alert"
                             aria-live="polite">

                            {{ transactionMessage }}

                        </div>


                        <div v-if="categoriesWarning"
                             id="dashboardTransactionCategoriesWarning"
                             class="alert alert-warning"
                             role="alert"
                             aria-live="polite">

                            {{ categoriesWarning }}

                        </div>


                        <div class="row g-3">


                            <div class="col-md-6">

                                <label for="dashboardTransactionTitle" class="form-label fw-medium">

                                    <i class="fa-solid fa-pen-to-square text-body-secondary me-1" aria-hidden="true"></i>
                                    عنوان

                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="dashboardTransactionTitle"
                                       name="title"
                                       v-model="transactionForm.title"
                                       maxlength="150"
                                       required
                                       autocomplete="off">

                            </div>


                            <div class="col-md-6">

                                <label for="dashboardTransactionType" class="form-label fw-medium">

                                    <i class="fa-solid fa-arrow-right-arrow-left text-body-secondary me-1" aria-hidden="true"></i>
                                    نوع تراکنش

                                </label>

                                <select class="form-select"
                                        id="dashboardTransactionType"
                                        v-model="transactionForm.type"
                                        @change="changeTransactionType"
                                        required
                                        aria-describedby="dashboardTransactionTypeHelp">

                                    <option value="">
                                        نوع را انتخاب کنید
                                    </option>

                                    <option value="income">
                                        درآمد
                                    </option>

                                    <option value="expense">
                                        هزینه
                                    </option>

                                </select>

                                <div id="dashboardTransactionTypeHelp" class="form-text">
                                    ابتدا نوع تراکنش را انتخاب کنید؛ سپس دسته‌بندی را انتخاب کنید.
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label for="dashboardTransactionAmount" class="form-label fw-medium">

                                    <i class="fa-solid fa-money-bill-wave text-body-secondary me-1" aria-hidden="true"></i>
                                    مبلغ

                                </label>


                                <div class="input-group" dir="ltr">

                                    <span class="input-group-text">
                                        تومان
                                    </span>

                                    <input type="text"
                                           class="form-control text-start"
                                           id="dashboardTransactionAmount"
                                           name="amount"
                                           v-model="transactionForm.amount"
                                           @input="formatTransactionAmount"
                                           inputmode="decimal"
                                           maxlength="20"
                                           autocomplete="off"
                                           required>

                                </div>


                                <div class="form-text">
                                    مبلغ صفر یا مثبت است؛ برای اعشار از نقطه استفاده کنید (حداکثر دو رقم).
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label for="dashboardTransactionCategory" class="form-label fw-medium">

                                    <i class="fa-solid fa-tags text-body-secondary me-1" aria-hidden="true"></i>
                                    دسته‌بندی

                                </label>

                                <select class="form-select"
                                        id="dashboardTransactionCategory"
                                        name="category_id"
                                        v-model="transactionForm.category_id"
                                        required>

                                    <option value="">
                                        {{ transactionForm.type ? 'دسته‌بندی را انتخاب کنید' : 'ابتدا نوع تراکنش را انتخاب کنید' }}
                                    </option>

                                    <option v-for="category in transactionCategories()"
                                            :key="category.id"
                                            :value="String(category.id)">

                                        {{ category.name }}

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label for="dashboardTransactionDate" class="form-label fw-medium">

                                    <i class="fa-solid fa-calendar-days text-body-secondary me-1" aria-hidden="true"></i>
                                    تاریخ

                                </label>

                                <input type="date"
                                       class="form-control"
                                       id="dashboardTransactionDate"
                                       name="transaction_date"
                                       v-model="transactionForm.transaction_date"
                                       required>

                            </div>


                            <div class="col-12">

                                <label for="dashboardTransactionDescription" class="form-label fw-medium">

                                    <i class="fa-solid fa-align-right text-body-secondary me-1" aria-hidden="true"></i>
                                    توضیحات
                                    <span class="text-secondary">(اختیاری)</span>

                                </label>

                                <textarea class="form-control"
                                          id="dashboardTransactionDescription"
                                          name="description"
                                          v-model="transactionForm.description"
                                          rows="3"></textarea>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer px-4 py-3 bg-body-tertiary">

                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            انصراف
                        </button>


                        <button type="submit"
                                id="saveDashboardTransactionButton"
                                class="btn btn-primary px-4"
                                :disabled="savingTransaction">

                            <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>

                            {{ savingTransaction ? 'در حال ذخیره...' : 'ذخیره' }}

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Add Category Modal -->
    <div class="modal fade tz-modal"
         id="dashboardAddCategoryModal"
         tabindex="-1"
         aria-labelledby="dashboardAddCategoryTitle"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form id="dashboardAddCategoryForm"
                      @submit.prevent="saveCategory">


                    <div class="modal-header">

                        <h2 class="modal-title fs-5"
                            id="dashboardAddCategoryTitle">

                            <i class="fa-solid fa-folder-plus text-primary me-2"
                               aria-hidden="true"></i>

                            افزودن دسته‌بندی

                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="بستن">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div v-if="categoryMessage"
                             class="alert alert-danger"
                             role="alert"
                             aria-live="polite">

                            {{ categoryMessage }}

                        </div>


                        <label for="dashboardCategoryName" class="form-label">
                            نام دسته‌بندی
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               class="form-control mb-3"
                               id="dashboardCategoryName"
                               name="name"
                               v-model="categoryForm.name"
                               maxlength="100"
                               required>


                        <label for="dashboardCategoryType" class="form-label">
                            نوع دسته‌بندی
                            <span class="text-danger">*</span>
                        </label>

                        <select id="dashboardCategoryType"
                                name="type"
                                class="form-select"
                                v-model="categoryForm.type"
                                required>

                            <option value="">
                                نوع را انتخاب کنید
                            </option>

                            <option value="income">
                                درآمد
                            </option>

                            <option value="expense">
                                هزینه
                            </option>

                        </select>


                        <p class="small text-body-secondary mt-3 mb-0">
                            با ثبت دسته‌بندی، فهرست انتخاب دسته‌بندی تراکنش نیز به‌روزرسانی می‌شود.
                        </p>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">

                            انصراف

                        </button>


                        <button type="submit"
                                class="btn btn-primary"
                                :disabled="savingCategory">

                            {{ savingCategory ? 'در حال ثبت...' : 'ثبت دسته‌بندی' }}

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Delete Transaction Modal -->
    <div class="modal fade tz-modal"
         id="dashboardDeleteTransactionModal"
         tabindex="-1"
         aria-labelledby="dashboardDeleteTransactionTitle"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h2 class="modal-title fs-5"
                        id="dashboardDeleteTransactionTitle">

                        <i class="fa-solid fa-trash-can text-danger me-2"
                           aria-hidden="true"></i>

                        حذف تراکنش

                    </h2>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="بستن">
                    </button>

                </div>


                <div class="modal-body">

                    <p>
                        آیا از حذف تراکنش
                        «<span class="fw-semibold">{{ transactionToDelete ? transactionToDelete.title : '' }}</span>»
                        اطمینان دارید؟
                    </p>

                    <p class="small text-body-secondary">
                        تراکنش حذف‌شده از بخش حذف‌شده‌های صفحه تراکنش‌ها قابل بازیابی است.
                    </p>


                    <div v-if="deleteMessage"
                         class="alert alert-danger"
                         role="alert"
                         aria-live="polite">

                        {{ deleteMessage }}

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">

                        انصراف

                    </button>


                    <button type="button"
                            id="confirmDashboardDeleteButton"
                            class="btn btn-danger"
                            @click="deleteTransaction"
                            :disabled="deletingTransaction">

                        {{ deletingTransaction ? 'در حال حذف...' : 'حذف تراکنش' }}

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
    Vue.createApp({
        data() {
            return {
                balanceAmount: null,
                debtAmount: null,
                balanceInput: '',

                transactions: [],
                categories: [],
                loadingTransactions: false,
                transactionsError: '',
                categoriesError: '',

                transactionForm: {
                    title: '',
                    type: '',
                    category_id: '',
                    amount: '',
                    transaction_date: '',
                    description: ''
                },

                editingId: null,
                transactionMessage: '',
                categoriesWarning: '',
                savingTransaction: false,

                transactionToDelete: null,
                deleteMessage: '',
                deletingTransaction: false,

                categoryForm: {
                    name: '',
                    type: ''
                },

                categoryMessage: '',
                savingCategory: false,

                pageMessage: '',
                pageMessageType: 'success',

                balanceModalMessage: '',
                resetBalanceMessage: '',

                savingBalance: false,
                resettingBalance: false,

                };

            },

            methods: {
                async loadBalance() {
                    try {
                        const response = await fetch("<?= site_url('api/dashboard/balance') ?>");

                        const result = await response.json();

                        if (!result.success) {

                            this.balanceAmount = null;
                            this.debtAmount = null;

                            this.pageMessage = result.message;
                            this.pageMessageType = 'danger';

                            return;
                        }

                        const balance = Number(result.balance);

                        if (balance >= 0) {
                            this.balanceAmount = balance;
                            this.debtAmount = 0;
                        } else {
                            this.balanceAmount = 0;
                            this.debtAmount = -balance;
                        }

                    } catch (error) {
                        this.balanceAmount = null;
                        this.debtAmount = null;

                        this.pageMessage = 'دریافت موجودی انجام نشد، دوباره تلاش کنید.';
                        this.pageMessageType = 'danger';
                    }
                },

                formatMoney(amount) {
                    return window.PersianFormat.number(amount);
                },

                formatDate(date) {
                    return window.PersianFormat.date(date);
                },

                formatBalanceAmount(event) {
                    this.balanceInput = window.PersianFormat.groupDigits(event.target.value);
                },

                openBalanceModal() {
                    this.balanceModalMessage = '';
                    this.balanceInput = '';

                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardBalanceModal')).show();
                },

                openResetBalanceModal() {
                    this.resetBalanceMessage = '';

                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardResetBalanceModal')).show();
                },

                async updateBalance(){
                    if(this.savingBalance){
                        return;
                    }

                    this.savingBalance = true;
                    this.balanceModalMessage = '';

                    try{
                        const amount = window.PersianFormat.normalizeInputNumber(this.balanceInput).trim();

                        const response = await fetch("<?= site_url('api/dashboard/balance') ?>",{
                            method: 'POST',
                            body: JSON.stringify({balance: amount})
                        });

                        const result = await response.json();

                        if(!result.success){
                            this.balanceModalMessage = result.message || "تغییر موجودی انجام نشد. ";
                            return;
                        }

                        window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardBalanceModal')).hide();

                        this.balanceInput = '';

                        this.pageMessage = result.message || "موجودی با موفقیت تغییر کرد. ";
                        this.pageMessageType = 'success';

                        await this.loadBalance();
                        await window.TarazinoReports.render();

                    } catch (error) {
                        this.balanceModalMessage = 'تغییر موجودی انجام نشد، دوباره تلاش کنید.';
                    }finally{
                        this.savingBalance = false;
                    }
                },

                async resetBalance(){
                    if(this.resettingBalance){
                        return;
                    }

                    this.resettingBalance = true;
                    this.resetBalanceMessage = '';

                    try{
                        const response = await fetch("<?= site_url('api/dashboard/balance/reset') ?>", {
                            method:'POST'
                        });

                        const result = await response.json();

                        if(!result.success){
                            this.resetBalanceMessage = result.message || "صفر کردن موجودی انجام نشد. ";
                            return;
                        }

                        window.bootstrap.Modal.getOrCreateInstance(
                            document.getElementById('dashboardResetBalanceModal')
                        ).hide();

                        this.pageMessage = result.message || 'موجودی با موفقیت صفر شد.';
                        this.pageMessageType = 'success';

                        await this.loadBalance();
                        await window.TarazinoReports.render();

                    }catch(error){
                        this.resetBalanceMessage = 'صفر کردن موجودی انجام نشد، دوباره تلاش کنید.';
                    } finally{
                        this.resettingBalance = false;
                    }

                },

                async loadCategories() {
                    this.categoriesError = '';

                    try {
                        const response = await fetch("<?= site_url('api/categories') ?>");
                        const result = await response.json();

                        if (result.success) {
                            this.categories = result.categories;
                        } else {
                            this.categories = [];
                            this.categoriesError = result.message || 'دریافت دسته‌بندی‌ها انجام نشد.';
                        }

                    } catch (error) {
                        this.categories = [];
                        this.categoriesError = 'دریافت دسته‌بندی‌ها انجام نشد.';
                    }

                    if (this.categoriesError) {
                        this.pageMessage = this.categoriesError;
                        this.pageMessageType = 'danger';
                    }

                    this.updateCategoriesWarning();

                    if (this.categoriesError) {
                        return false;
                    }

                    return true;
                },

                async loadTransactions() {
                    if (this.loadingTransactions) {
                        return;
                    }

                    this.loadingTransactions = true;
                    this.transactionsError = '';

                    try {
                        const response = await fetch("<?= site_url('api/transactions') ?>");
                        const result = await response.json();

                        if (!result.success) {
                            this.transactions = [];
                            this.transactionsError = result.message || 'دریافت تراکنش‌ها انجام نشد.';
                            return false;
                        }

                        this.transactions = result.transactions.slice(0, 5);

                        return true;

                    } catch (error) {
                        this.transactions = [];
                        this.transactionsError = 'دریافت تراکنش‌ها انجام نشد، دوباره تلاش کنید.';
                        return false;

                    } finally {
                        this.loadingTransactions = false;
                    }
                },

                categoryOf(transaction) {
                    return this.categories.find(category => {
                        return String(category.id) === String(transaction.category_id);});
                },

                categoryType(transaction) {
                    const category = this.categoryOf(transaction);
                    return category ? category.type : '';
                },

                categoryName(transaction) {
                    const category = this.categoryOf(transaction);
                    return category ? category.name : '—';
                },

                transactionCategories() {
                    return this.categories.filter(category => {
                        return category.type === this.transactionForm.type;});
                },

                updateCategoriesWarning() {
                    if (this.categoriesError) {
                        this.categoriesWarning = this.categoriesError;
                    } else if (this.categories.length === 0) {
                        this.categoriesWarning = 'ابتدا یک دسته‌بندی فعال ایجاد کنید.';
                    } else if (
                        this.transactionForm.type && this.transactionCategories().length === 0
                    ) {
                        this.categoriesWarning = 'برای این نوع تراکنش دسته‌بندی فعالی وجود ندارد.';
                    } else {
                        this.categoriesWarning = '';
                    }
                },

                resetTransactionForm() {
                    const today = new Date();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');

                    this.transactionForm = {
                        title: '',
                        type: '',
                        category_id: '',
                        amount: '',
                        transaction_date: today.getFullYear() + '-' + month + '-' + day,
                        description: ''
                    };

                    this.editingId = null;
                    this.transactionMessage = '';

                    this.updateCategoriesWarning();
                },

                openTransactionModal() {
                    this.resetTransactionForm();

                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardTransactionModal')).show();
                },

                editTransaction(transaction) {

                    this.resetTransactionForm();
                    this.editingId = String(transaction.id);
                    const category = this.categoryOf(transaction);

                    this.transactionForm = {
                        title: transaction.title || '',
                        type: category ? category.type : '',
                        category_id: String(transaction.category_id || ''),
                        amount: window.PersianFormat.groupDigits(transaction.amount),
                        transaction_date: transaction.transaction_date || '',
                        description: transaction.description || ''
                    };

                this.updateCategoriesWarning();

                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardTransactionModal')).show();
                },

                changeTransactionType() {
                    this.transactionForm.category_id = '';
                    this.updateCategoriesWarning();
                },

                formatTransactionAmount(event) {
                    this.transactionForm.amount = window.PersianFormat.groupDigits(event.target.value);
                },

                async saveTransaction() {
                    if (this.savingTransaction) {
                        return;
                    }

                    this.savingTransaction = true;
                    this.transactionMessage = '';

                    const data = {
                        title: this.transactionForm.title.trim(),
                        category_id: this.transactionForm.category_id,
                        amount: window.PersianFormat.normalizeInputNumber(this.transactionForm.amount).trim(),
                        transaction_date: this.transactionForm.transaction_date,
                        description: this.transactionForm.description
                    };

                    let url = "<?= site_url('api/transactions') ?>";

                    if (this.editingId) {
                        url = "<?= site_url('api/transactions') ?>/" + this.editingId + "/update";
                    }

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardTransactionModal')).hide();

                            this.resetTransactionForm();

                            this.pageMessage = result.message || 'تراکنش با موفقیت ذخیره شد.';
                            this.pageMessageType = 'success';

                            const loaded = await this.loadTransactions();

                            await this.loadBalance();
                            await window.TarazinoReports.render();

                            if (!loaded) {
                                this.pageMessage = 'تراکنش ذخیره شد، اما فهرست به‌روزرسانی نشد.';
                                this.pageMessageType = 'warning';
                            }

                        } else {
                            this.transactionMessage = result.message || 'ذخیره تراکنش انجام نشد.';
                        }

                    } catch (error) {
                        this.transactionMessage = 'ارتباط با سرور برقرار نشد، دوباره تلاش کنید.';

                    } finally {
                        this.savingTransaction = false;
                    }
                },

                askDelete(transaction) {
                    this.transactionToDelete = transaction;
                    this.deleteMessage = '';

                    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardDeleteTransactionModal')).show();
                },

                async deleteTransaction() {
                    if (this.deletingTransaction || !this.transactionToDelete) {
                        return;
                    }

                    this.deletingTransaction = true;
                    this.deleteMessage = '';

                    const url = "<?= site_url('api/transactions') ?>/" + this.transactionToDelete.id + "/delete";

                    try {
                        const response = await fetch(url, {
                            method: 'POST'
                        });

                        const result = await response.json();

                        if (result.success) {

                            window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardDeleteTransactionModal')).hide();

                            this.pageMessage = result.message || 'تراکنش با موفقیت حذف شد.';
                            this.pageMessageType = 'success';

                            this.transactionToDelete = null;

                            const loaded = await this.loadTransactions();

                            await this.loadBalance();
                            await window.TarazinoReports.render();

                            if (!loaded) {
                                this.pageMessage = 'تراکنش حذف شد، اما فهرست به‌روزرسانی نشد.';
                                this.pageMessageType = 'warning';
                            }

                        } else {
                            this.deleteMessage = result.message || 'حذف تراکنش انجام نشد.';
                        }

                    } catch (error) {
                        this.deleteMessage = 'ارتباط با سرور برقرار نشد، دوباره تلاش کنید.';
                    } finally {
                        this.deletingTransaction = false;
                    }
                },

                resetCategoryForm() {
                    this.categoryForm = {
                        name: '',
                        type: ''
                    };
                    this.categoryMessage = '';
                },

                async saveCategory() {
                    if (this.savingCategory) {
                        return;
                    }

                    this.savingCategory = true;
                    this.categoryMessage = '';

                    const data = {
                        name: this.categoryForm.name.trim(),
                        type: this.categoryForm.type
                    };

                    try {
                        const response = await fetch("<?= site_url('api/categories/store') ?>", {
                            method: 'POST',
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {

                            window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardAddCategoryModal')).hide();

                            this.resetCategoryForm();

                            this.pageMessage = result.message || 'دسته‌بندی با موفقیت ایجاد شد.';
                            this.pageMessageType = 'success';

                            await this.loadCategories();
                            await this.loadTransactions();

                        } else {
                            this.categoryMessage = result.message || 'ایجاد دسته‌بندی انجام نشد.';
                        }
                    } catch (error) {
                        this.categoryMessage = 'ارتباط با سرور برقرار نشد، دوباره تلاش کنید.';
                    } finally {
                        this.savingCategory = false;
                    }
                }
            },

            async mounted() {
                await this.loadCategories();

                this.loadTransactions();
                this.loadBalance();
            }

    }).mount('#dashboardApp');
</script>


<?php $this->load->view('layouts/logout_modal'); ?>

<?php
$this->load->view('layouts/footer', array(
    'use_charts' => TRUE,
    'page_scripts' => array(
        base_url('assets/js/api.js'),
        base_url('assets/js/dashboard-reports.js')
    )
));

?>


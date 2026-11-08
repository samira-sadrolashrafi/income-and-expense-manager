<?php $this->load->view('layouts/header', [
    'page_title' => 'مدیریت تراکنش‌ها',
    'page_styles' => [base_url('assets/css/dashboard.css')]
]); ?>

<div id="transactionApp" class="container-fluid dashboard-shell px-0">
    <div class="row g-0">

        <?php $this->load->view('layouts/app_sidebar', ['active_page' => 'transactions']); ?>

        <main class="col-lg-9 col-xl-10 dashboard-main" id="transactionsMain">

            <?php $this->load->view('layouts/app_header', [
                'app_page_title' => 'تراکنش‌ها',
                'app_page_description' => 'ثبت، ویرایش، مدیریت و بازیابی درآمدها و هزینه‌ها',
                'app_username' => $username
            ]); ?>

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
                        @click="openTransactionModal"
                        :disabled="savingTransaction || deletingTransaction || restoringId !== null"
                    >
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        <span>افزودن تراکنش</span>
                    </button>

                </div>

                <!-- Page message -->
                <div
                    v-if="message"
                    id="transactionsMessage"
                    class="alert"
                    :class="'alert-' + messageType"
                    role="alert"
                    aria-live="polite"
                >
                    {{ message }}
                </div>


                <!-- Active and deleted tabs -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">

                    <div class="btn-group" role="group" aria-label="وضعیت فهرست تراکنش‌ها">

                        <button
                            type="button"
                            id="showActiveTransactionsButton"
                            class="btn"
                            :class="currentTab === 'active' ? 'btn-primary' : 'btn-outline-primary'"
                            :aria-pressed="currentTab === 'active'"
                            @click="showActiveTransactions"
                        >
                            <i class="fa-solid fa-list-check me-1" aria-hidden="true"></i>
                            فعال
                        </button>


                        <button
                            type="button"
                            id="showDeletedTransactionsButton"
                            class="btn"
                            :class="currentTab === 'deleted' ? 'btn-primary' : 'btn-outline-primary'"
                            :aria-pressed="currentTab === 'deleted'"
                            @click="showDeletedTransactions"
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


                <!-- Active transactions -->
                <section
                    id="activeTransactionsPanel"
                    aria-labelledby="activeTransactionsHeading"
                    v-show="currentTab === 'active'"
                >

                    <h2 id="activeTransactionsHeading" class="visually-hidden">
                        تراکنش‌های فعال
                    </h2>


                    <!-- Transaction filters -->
                    <section
                        class="card dashboard-card mb-3"
                        aria-labelledby="transactionFiltersHeading"
                    >

                        <div class="card-body p-3 p-md-4">

                            <div class="d-flex align-items-center gap-2 mb-3">

                                <i class="fa-solid fa-filter text-primary" aria-hidden="true"></i>

                                <h3 id="transactionFiltersHeading" class="h6 fw-bold mb-0">
                                    جست‌وجو و فیلتر
                                </h3>

                            </div>


                            <div class="row g-3 align-items-end">

                                <!-- Search -->
                                <div class="col-12 col-md-6 col-xl-4">

                                    <label for="transactionSearch" class="form-label small fw-medium">
                                        جست‌وجو در تراکنش‌ها
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-body-tertiary" aria-hidden="true">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>

                                        <input
                                            type="search"
                                            id="transactionSearch"
                                            class="form-control"
                                            placeholder="عنوان یا توضیحات..."
                                            autocomplete="off"
                                            v-model="searchInput"
                                        >

                                    </div>

                                </div>


                                <!-- Transaction type -->
                                <div class="col-6 col-md-3 col-xl-2">

                                    <label for="transactionTypeFilter" class="form-label small fw-medium">
                                        نوع تراکنش
                                    </label>

                                    <select
                                        id="transactionTypeFilter"
                                        class="form-select"
                                        v-model="typeInput"
                                        @change="changeFilterType"
                                    >
                                        <option value="all">همه انواع</option>
                                        <option value="income">درآمد</option>
                                        <option value="expense">هزینه</option>
                                    </select>

                                </div>


                                <!-- Category -->
                                <div class="col-6 col-md-3 col-xl-2">

                                    <label for="transactionCategoryFilter" class="form-label small fw-medium">
                                        دسته‌بندی
                                    </label>

                                    <select
                                        id="transactionCategoryFilter"
                                        class="form-select"
                                        v-model="categoryInput"
                                    >

                                        <option value="all">
                                            همه دسته‌بندی‌ها
                                        </option>

                                        <option
                                            v-for="category in filterCategories()"
                                            :key="category.id"
                                            :value="String(category.id)"
                                        >
                                            {{ category.name }}
                                        </option>

                                    </select>

                                </div>


                                <!-- Date from -->
                                <div class="col-6 col-md-6 col-xl-2">

                                    <label for="transactionDateFrom" class="form-label small fw-medium">
                                        از تاریخ
                                    </label>

                                    <input
                                        type="date"
                                        id="transactionDateFrom"
                                        class="form-control"
                                        autocomplete="off"
                                        v-model="dateFromInput"
                                    >

                                </div>


                                <!-- Date to -->
                                <div class="col-6 col-md-6 col-xl-2">

                                    <label for="transactionDateTo" class="form-label small fw-medium">
                                        تا تاریخ
                                    </label>

                                    <input
                                        type="date"
                                        id="transactionDateTo"
                                        class="form-control"
                                        autocomplete="off"
                                        v-model="dateToInput"
                                    >

                                </div>

                            </div>


                            <!-- Sort and filter buttons -->
                            <div class="row g-3 mt-4 align-items-end">

                                <div class="col-12 col-md-auto">

                                    <div class="d-flex align-items-center gap-2">

                                        <label for="transactionSort" class="form-label small fw-medium mb-0">
                                            مرتب‌سازی
                                        </label>

                                        <select
                                            id="transactionSort"
                                            class="form-select"
                                            style="width: 160px"
                                            v-model="sortInput"
                                        >
                                            <option value="newest">جدیدترین</option>
                                            <option value="oldest">قدیمی‌ترین</option>
                                        </select>

                                    </div>

                                </div>


                                <div class="col d-flex justify-content-end gap-2">

                                    <button
                                        type="button"
                                        id="clearTransactionFiltersButton"
                                        class="btn btn-outline-secondary"
                                        @click="clearFilters"
                                        :disabled="loadingActive"
                                    >
                                        <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>
                                        پاک‌کردن فیلترها
                                    </button>


                                    <button
                                        type="button"
                                        id="applyTransactionFiltersButton"
                                        class="btn btn-primary"
                                        @click="applyFilters"
                                        :disabled="loadingActive"
                                    >
                                        <i class="fa-solid fa-filter me-1" aria-hidden="true"></i>
                                        اعمال فیلترها
                                    </button>

                                </div>

                            </div>

                        </div>

                    </section>


                    <!-- Active transactions table -->
                    <section
                        class="card dashboard-card mb-4"
                        aria-labelledby="transactionsTableHeading"
                    >

                        <div class="card-header bg-body border-bottom d-flex align-items-center justify-content-between gap-2 py-3 px-3 px-md-4">

                            <h3 id="transactionsTableHeading" class="h6 fw-bold mb-0">

                                <i class="fa-solid fa-table-list text-primary me-2" aria-hidden="true"></i>

                                تراکنش‌های فعال

                            </h3>

                            <span class="small text-body-secondary">
                                نمایش فهرست
                            </span>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>
                                        <th scope="col" class="ps-3">عنوان</th>
                                        <th scope="col">نوع</th>
                                        <th scope="col">مبلغ</th>
                                        <th scope="col">دسته‌بندی</th>
                                        <th scope="col">تاریخ</th>
                                        <th scope="col">توضیحات</th>
                                        <th scope="col" class="text-nowrap pe-3">عملیات</th>
                                    </tr>

                                </thead>


                                <tbody id="transactionsTableBody" aria-live="polite">


                                    <!-- Loading -->
                                    <tr v-if="loadingActive">

                                        <td colspan="7" class="text-center text-body-secondary py-5">

                                            <span
                                                class="spinner-border spinner-border-sm me-2"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>

                                            در حال دریافت تراکنش‌ها...

                                        </td>

                                    </tr>


                                    <!-- Error -->
                                    <tr v-else-if="activeError">

                                        <td colspan="7" class="text-center text-danger py-5">

                                            {{ activeError }}

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary ms-2"
                                                @click="loadTransactions()"
                                            >
                                                تلاش دوباره
                                            </button>

                                        </td>

                                    </tr>


                                    <!-- Empty -->
                                    <tr v-else-if="transactions.length === 0">

                                        <td colspan="7" class="text-center text-body-secondary py-5">
                                            تراکنشی با این شرایط پیدا نشد.
                                        </td>

                                    </tr>


                                    <!-- Transactions -->
                                    <template v-else>

                                        <tr
                                            v-for="transaction in transactions"
                                            :key="transaction.id"
                                        >

                                            <td class="ps-3">
                                                {{ transaction.title || '—' }}
                                            </td>


                                            <td>
                                                {{ transactionTypeLabel(transaction) }}
                                            </td>


                                            <td
                                                :class="categoryType(transaction) === 'expense' ? 'text-danger' : 'text-success'"
                                            >
                                                {{ formatAmount(transaction.amount) }}
                                            </td>


                                            <td>
                                                {{ categoryName(transaction) }}
                                            </td>


                                            <td class="text-nowrap">
                                                {{ formatDate(transaction.transaction_date) }}
                                            </td>


                                            <td>
                                                {{ transaction.description || '—' }}
                                            </td>


                                            <td class="text-nowrap pe-3">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary me-1"
                                                    @click="editTransaction(transaction)"
                                                    :disabled="savingTransaction || deletingTransaction || restoringId !== null"
                                                >
                                                    ویرایش
                                                </button>


                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    @click="askDelete(transaction)"
                                                    :disabled="savingTransaction || deletingTransaction || restoringId !== null"
                                                >
                                                    حذف
                                                </button>

                                            </td>

                                        </tr>

                                    </template>

                                </tbody>

                            </table>

                        </div>


                        <!-- Active pagination -->
                        <div
                            v-if="activePagination.total_pages > 1"
                            id="transactionsPagination"
                            class="card-footer bg-body"
                        >

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                                <span class="small text-body-secondary" aria-live="polite">

                                    نمایش {{ formatNumber(paginationStart(activePagination)) }}

                                    تا {{ formatNumber(paginationEnd(activePagination)) }}

                                    از {{ formatNumber(activePagination.total) }}

                                </span>


                                <nav aria-label="صفحه‌بندی تراکنش‌های فعال" class="tz-pagination">

                                    <ul class="pagination pagination-sm mb-0">


                                        <li class="page-item" :class="{ disabled: activePage === 1 }">

                                            <button
                                                type="button"
                                                class="page-link"
                                                :disabled="activePage === 1 || loadingActive"
                                                @click="changeActivePage(activePage - 1)"
                                            >
                                                قبلی
                                            </button>

                                        </li>


                                        <li
                                            v-for="(page, index) in getPageNumbers(activePage, activePagination.total_pages)"
                                            :key="index"
                                            class="page-item"
                                            :class="{ active: page === activePage, disabled: page === '...' }"
                                        >

                                            <button
                                                v-if="page !== '...'"
                                                type="button"
                                                class="page-link"
                                                :aria-current="page === activePage ? 'page' : null"
                                                :disabled="loadingActive"
                                                @click="changeActivePage(page)"
                                            >
                                                {{ formatNumber(page) }}
                                            </button>


                                            <span v-else class="page-link">
                                                ...
                                            </span>

                                        </li>


                                        <li
                                            class="page-item"
                                            :class="{ disabled: activePage === activePagination.total_pages }"
                                        >

                                            <button
                                                type="button"
                                                class="page-link"
                                                :disabled="activePage === activePagination.total_pages || loadingActive"
                                                @click="changeActivePage(activePage + 1)"
                                            >
                                                بعدی
                                            </button>

                                        </li>

                                    </ul>

                                </nav>

                            </div>

                        </div>

                    </section>

                </section>


                <!-- Deleted transactions -->
                <section
                    id="deletedTransactionsPanel"
                    class="card dashboard-card mb-4"
                    aria-labelledby="deletedTransactionsHeading"
                    v-show="currentTab === 'deleted'"
                >

                    <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">

                        <h2 id="deletedTransactionsHeading" class="h6 fw-bold mb-1">

                            <i class="fa-regular fa-trash-can text-danger me-2" aria-hidden="true"></i>

                            تراکنش‌های حذف‌شده

                        </h2>

                        <p class="small text-body-secondary mb-0">
                            بازیابی تراکنش فقط در مهلت مقرر و در صورت فعال بودن دسته‌بندی آن امکان‌پذیر است.
                        </p>

                    </div>


                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>
                                    <th scope="col" class="ps-3">عنوان</th>
                                    <th scope="col">مبلغ</th>
                                    <th scope="col">دسته‌بندی</th>
                                    <th scope="col">تاریخ تراکنش</th>
                                    <th scope="col">زمان حذف</th>
                                    <th scope="col" class="text-nowrap pe-3">وضعیت و عملیات</th>
                                </tr>

                            </thead>


                            <tbody id="deletedTransactionsTableBody" aria-live="polite">


                                <!-- Loading -->
                                <tr v-if="loadingDeleted">

                                    <td colspan="6" class="text-center text-body-secondary py-5">

                                        <span
                                            class="spinner-border spinner-border-sm me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>

                                        در حال دریافت تراکنش‌های حذف‌شده...

                                    </td>

                                </tr>


                                <!-- Error -->
                                <tr v-else-if="deletedError">

                                    <td colspan="6" class="text-center text-danger py-5">

                                        {{ deletedError }}

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary ms-2"
                                            @click="loadDeletedTransactions()"
                                        >
                                            تلاش دوباره
                                        </button>

                                    </td>

                                </tr>


                                <!-- Not loaded -->
                                <tr v-else-if="!deletedLoaded">

                                    <td colspan="6" class="text-center text-body-secondary py-5">
                                        برای مشاهده تراکنش‌های حذف‌شده، این بخش را انتخاب کنید.
                                    </td>

                                </tr>


                                <!-- Empty -->
                                <tr v-else-if="deletedTransactions.length === 0">

                                    <td colspan="6" class="text-center text-body-secondary py-5">
                                        تراکنش حذف‌شده‌ای وجود ندارد.
                                    </td>

                                </tr>


                                <!-- Deleted transactions -->
                                <template v-else>

                                    <tr
                                        v-for="transaction in deletedTransactions"
                                        :key="transaction.id"
                                    >

                                        <td class="ps-3">
                                            {{ transaction.title || '—' }}
                                        </td>


                                        <td>
                                            {{ formatAmount(transaction.amount) }}
                                        </td>


                                        <td>
                                            {{ transaction.category_name || '—' }}
                                        </td>


                                        <td class="text-nowrap">
                                            {{ formatDate(transaction.transaction_date) }}
                                        </td>


                                        <td class="text-nowrap">
                                            {{ formatDateTime(transaction.deleted_at) }}
                                        </td>


                                        <td class="text-nowrap pe-3">

                                            <span
                                                class="small d-block mb-2"
                                                :class="canRestore(transaction) ? 'text-success' : 'text-warning'"
                                            >
                                                {{ restoreReason(transaction) }}
                                            </span>


                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-success"
                                                @click="restoreTransaction(transaction)"
                                                :disabled="!canRestore(transaction) || restoringId !== null || deletingTransaction || savingTransaction"
                                            >
                                                {{ restoringId === String(transaction.id) ? 'در حال بازیابی...' : 'بازیابی' }}
                                            </button>

                                        </td>

                                    </tr>

                                </template>

                            </tbody>

                        </table>

                    </div>


                    <!-- Deleted pagination -->
                    <div
                        v-if="deletedPagination.total_pages > 1"
                        id="deletedTransactionsPagination"
                        class="card-footer bg-body"
                    >

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <span class="small text-body-secondary" aria-live="polite">

                                نمایش {{ formatNumber(paginationStart(deletedPagination)) }}

                                تا {{ formatNumber(paginationEnd(deletedPagination)) }}

                                از {{ formatNumber(deletedPagination.total) }}

                            </span>


                            <nav aria-label="صفحه‌بندی تراکنش‌های حذف‌شده" class="tz-pagination">

                                <ul class="pagination pagination-sm mb-0">


                                    <li class="page-item" :class="{ disabled: deletedPage === 1 }">

                                        <button
                                            type="button"
                                            class="page-link"
                                            :disabled="deletedPage === 1 || loadingDeleted"
                                            @click="changeDeletedPage(deletedPage - 1)"
                                        >
                                            قبلی
                                        </button>

                                    </li>


                                    <li
                                        v-for="(page, index) in getPageNumbers(deletedPage, deletedPagination.total_pages)"
                                        :key="index"
                                        class="page-item"
                                        :class="{ active: page === deletedPage, disabled: page === '...' }"
                                    >

                                        <button
                                            v-if="page !== '...'"
                                            type="button"
                                            class="page-link"
                                            :aria-current="page === deletedPage ? 'page' : null"
                                            :disabled="loadingDeleted"
                                            @click="changeDeletedPage(page)"
                                        >
                                            {{ formatNumber(page) }}
                                        </button>


                                        <span v-else class="page-link">
                                            ...
                                        </span>

                                    </li>


                                    <li
                                        class="page-item"
                                        :class="{ disabled: deletedPage === deletedPagination.total_pages }"
                                    >

                                        <button
                                            type="button"
                                            class="page-link"
                                            :disabled="deletedPage === deletedPagination.total_pages || loadingDeleted"
                                            @click="changeDeletedPage(deletedPage + 1)"
                                        >
                                            بعدی
                                        </button>

                                    </li>

                                </ul>

                            </nav>

                        </div>

                    </div>

                </section>

            </div>


            <!-- Create / Edit Transaction Modal -->
            <div
                class="modal fade tz-modal"
                data-component="transaction-form-modal"
                id="transactionModal"
                tabindex="-1"
                aria-labelledby="transactionModalTitle"
                aria-hidden="true"
            >

                <div class="modal-dialog modal-dialog-centered modal-lg">

                    <div class="modal-content">

                        <form
                            id="transactionForm"
                            @submit.prevent="saveTransaction"
                            novalidate
                            autocomplete="off"
                        >


                            <!-- Modal header -->
                            <div class="modal-header px-4 py-3">

                                <div class="d-flex align-items-center gap-2">

                                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary p-2" aria-hidden="true">
                                        <i class="fa-solid fa-receipt"></i>
                                    </span>

                                    <h2
                                        class="modal-title fs-5 fw-semibold"
                                        id="transactionModalTitle"
                                    >
                                        {{ editingId ? 'ویرایش تراکنش' : 'افزودن تراکنش' }}
                                    </h2>

                                </div>


                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="بستن"
                                ></button>

                            </div>


                            <!-- Modal body -->
                            <div class="modal-body p-4">


                                <div
                                    id="transactionFormMessage"
                                    v-if="transactionMessage"
                                    class="alert alert-danger"
                                    role="alert"
                                    aria-live="polite"
                                >
                                    {{ transactionMessage }}
                                </div>


                                <div
                                    id="transactionCategoriesWarning"
                                    v-if="categoriesWarning"
                                    class="alert alert-warning"
                                    role="alert"
                                    aria-live="polite"
                                >
                                    {{ categoriesWarning }}
                                </div>


                                <div class="row g-3">


                                    <!-- Title -->
                                    <div class="col-md-6">

                                        <label for="transactionTitle" class="form-label fw-medium">

                                            <i class="fa-solid fa-pen-to-square text-body-secondary me-1" aria-hidden="true"></i>

                                            عنوان

                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="transactionTitle"
                                            name="title"
                                            v-model="transactionForm.title"
                                            maxlength="150"
                                            required
                                            autocomplete="off"
                                                                                    >


                                    </div>


                                    <!-- Type -->
                                    <div class="col-md-6">

                                        <label for="transactionType" class="form-label fw-medium">

                                            <i class="fa-solid fa-arrow-right-arrow-left text-body-secondary me-1" aria-hidden="true"></i>

                                            نوع تراکنش

                                        </label>

                                        <select
                                            class="form-select"
                                            id="transactionType"
                                            v-model="transactionForm.type"
                                            @change="changeTransactionType"
                                            required
                                            aria-describedby="transactionTypeHelp"
                                        >

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

                                        <div id="transactionTypeHelp" class="form-text">
                                            ابتدا نوع تراکنش را انتخاب کنید؛ سپس دسته‌بندی را انتخاب کنید.
                                        </div>


                                    </div>


                                    <!-- Amount -->
                                    <div class="col-md-6">

                                        <label for="transactionAmount" class="form-label fw-medium">

                                            <i class="fa-solid fa-money-bill-wave text-body-secondary me-1" aria-hidden="true"></i>

                                            مبلغ

                                        </label>


                                        <div class="input-group" dir="ltr">

                                            <span class="input-group-text">
                                                تومان
                                            </span>

                                            <input
                                                type="text"
                                                class="form-control text-start"
                                                id="transactionAmount"
                                                name="amount"
                                                v-model="transactionForm.amount"
                                                @input="formatTransactionAmount"
                                                inputmode="decimal"
                                                maxlength="20"
                                                autocomplete="off"
                                                required
                                                aria-describedby="transactionAmountHelp"
                                            >


                                        </div>


                                        <div id="transactionAmountHelp" class="form-text">
                                            مبلغ صفر یا مثبت است؛ برای اعشار از نقطه استفاده کنید (حداکثر دو رقم).
                                        </div>

                                    </div>


                                    <!-- Category -->
                                    <div class="col-md-6">

                                        <label for="transactionCategory" class="form-label fw-medium">

                                            <i class="fa-solid fa-tags text-body-secondary me-1" aria-hidden="true"></i>

                                            دسته‌بندی

                                        </label>

                                        <select
                                            class="form-select"
                                            id="transactionCategory"
                                            name="category_id"
                                            v-model="transactionForm.category_id"
                                            required
                                                                                    >

                                            <option value="">
                                                {{ transactionForm.type ? 'دسته‌بندی را انتخاب کنید' : 'ابتدا نوع تراکنش را انتخاب کنید' }}
                                            </option>

                                            <option
                                                v-for="category in transactionCategories()"
                                                :key="category.id"
                                                :value="String(category.id)"
                                            >
                                                {{ category.name }}
                                            </option>

                                        </select>


                                    </div>


                                    <!-- Date -->
                                    <div class="col-md-6">

                                        <label for="transactionDate" class="form-label fw-medium">

                                            <i class="fa-solid fa-calendar-days text-body-secondary me-1" aria-hidden="true"></i>

                                            تاریخ

                                        </label>

                                        <input
                                            type="date"
                                            class="form-control"
                                            id="transactionDate"
                                            name="transaction_date"
                                            v-model="transactionForm.transaction_date"
                                            required
                                                                                    >


                                    </div>


                                    <!-- Description -->
                                    <div class="col-12">

                                        <label for="transactionDescription" class="form-label fw-medium">

                                            <i class="fa-solid fa-align-right text-body-secondary me-1" aria-hidden="true"></i>

                                            توضیحات

                                            <span class="text-secondary">(اختیاری)</span>

                                        </label>

                                        <textarea
                                            class="form-control"
                                            id="transactionDescription"
                                            name="description"
                                            v-model="transactionForm.description"
                                            rows="3"
                                        ></textarea>

                                    </div>

                                </div>

                            </div>


                            <!-- Modal footer -->
                            <div class="modal-footer px-4 py-3 bg-body-tertiary">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    انصراف
                                </button>


                                <button
                                    type="submit"
                                    id="saveTransactionButton"
                                    class="btn btn-primary px-4"
                                    :disabled="savingTransaction"
                                >
                                    <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>

                                    {{ savingTransaction ? 'در حال ذخیره...' : 'ذخیره' }}

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>


            <!-- Delete transaction modal -->
            <div
                class="modal fade tz-modal"
                id="deleteTransactionModal"
                tabindex="-1"
                aria-labelledby="deleteTransactionModalTitle"
                aria-hidden="true"
            >

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">


                        <div class="modal-header">

                            <h2 class="modal-title fs-5 fw-semibold" id="deleteTransactionModalTitle">

                                <i class="fa-regular fa-trash-can text-danger me-2" aria-hidden="true"></i>

                                حذف تراکنش

                            </h2>


                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="بستن"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <p class="mb-2">

                                آیا از حذف تراکنش «

                                <span class="fw-semibold">
                                    {{ transactionToDelete ? transactionToDelete.title : '' }}
                                </span>

                                » اطمینان دارید؟

                            </p>


                            <p class="small text-body-secondary mb-0">
                                تراکنش پس از حذف تا ۳۰ روز در بخش حذف‌شده‌ها قابل بازیابی خواهد بود.
                            </p>


                            <div
                                id="deleteTransactionMessage"
                                v-if="deleteMessage"
                                class="alert alert-danger mt-3 mb-0"
                                role="alert"
                                aria-live="polite"
                            >
                                {{ deleteMessage }}
                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                            >
                                انصراف
                            </button>


                            <button
                                type="button"
                                id="confirmDeleteTransactionButton"
                                class="btn btn-danger"
                                @click="deleteTransaction"
                                :disabled="deletingTransaction"
                            >
                                <i class="fa-solid fa-trash-can me-1" aria-hidden="true"></i>

                                {{ deletingTransaction ? 'در حال حذف...' : 'حذف تراکنش' }}

                            </button>

                        </div>

                    </div>

                </div>

            </div>


        </main>

    </div>
</div>

<?php $this->load->view('layouts/logout_modal'); ?>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>
    Vue.createApp({
        data(){
            return{
                transactions: [],
                deletedTransactions: [],
                categories: [],

                searchInput: '',
                typeInput: 'all',
                categoryInput: 'all',
                dateFromInput: '',
                dateToInput: '',
                sortInput: 'newest',

                appliedFilters: {
                    search: '',
                    type: 'all',
                    category_id: 'all',
                    date_from: '',
                    date_to: '',
                    sort: 'newest'
                },

                currentTab: 'active',

                loadingActive: false,
                loadingDeleted: false,
                deletedLoaded: false,

                activeError: '',
                deletedError: '',

                activePage: 1,
                deletedPage: 1,

                limit: 10,

                activePagination: {
                    current_page: 1,
                    total_pages: 0,
                    total: 0
                },

                deletedPagination: {
                    current_page: 1,
                    total_pages: 0,
                    total: 0
                },

                message: '',
                messageType: 'success',

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
                categoriesError: '',
                savingTransaction: false,

                transactionToDelete: null,
                deleteMessage: '',
                deletingTransaction: false,

                restoringId: null
            };
        },

        methods: {
            async loadCategories(){
                this.categoriesError = '';

                try{
                    const response = await fetch("<?= site_url('api/categories') ?>");

                    const result = await response.json();

                    if (!result.success) {

                        this.categories = [];
                        this.categoriesError = result.message || 'دریافت دسته‌بندی‌ها انجام نشد.';
                        this.updateCategoriesWarning();
                        return false;
                    }

                    this.categories = result.categories;

                    this.updateCategoriesWarning();
                    return true;

                }catch(error){
                    this.categories = [];
                    this.categoriesError = "دریافت دسته‌بندی‌ها انجام نشد. ";
                    this.updateCategoriesWarning();
                    return false;
                }
            },

            async loadTransactions(page = 1) {

                this.loadingActive = true;
                this.activeError = '';

                const params = new URLSearchParams(this.appliedFilters);

                params.append('page', page);

                try {

                    const response = await fetch(
                        "<?= site_url('api/transactions') ?>?" + params.toString()
                    );

                    const result = await response.json();


                    if (!result.success) {

                        this.transactions = [];

                        this.activePagination = {
                            current_page: 1,
                            total_pages: 0,
                            total: 0
                        };

                        this.activePage = 1;

                        this.activeError = result.message || 'دریافت تراکنش‌ها انجام نشد.';

                        return false;
                    }

                    const lastPage = Math.max(1, Number(result.pagination.total_pages));

                    if (page > lastPage) {
                        return await this.loadTransactions(lastPage);
                    }

                    this.transactions = result.transactions;
                    this.activePagination = result.pagination;
                    this.activePage = Number(result.pagination.current_page) || 1;

                    return true;

                } catch (error) {

                    this.transactions = [];

                    this.activePagination = {
                        current_page: 1,
                        total_pages: 0,
                        total: 0
                    };

                    this.activePage = 1;
                    this.activeError = "دریافت تراکنش‌ها انجام نشد.";

                    return false;

                } finally {
                    this.loadingActive = false;
                }

            },

            async loadDeletedTransactions(page = 1) {

                this.loadingDeleted = true;
                this.deletedError = '';

                try {

                    const response = await fetch(
                        "<?= site_url('api/transactions/deleted') ?>?page=" + page
                    );

                    const result = await response.json();

                    if (!result.success) {

                        this.deletedTransactions = [];
                        this.deletedPagination = {
                            current_page: 1,
                            total_pages: 0,
                            total: 0
                        };

                        this.deletedPage = 1;
                        this.deletedError = result.message || 'دریافت تراکنش‌های حذف‌شده انجام نشد.';
                        this.deletedLoaded = false;

                        return false;
                    }

                    const lastPage = Math.max(1, Number(result.pagination.total_pages));

                    if (page > lastPage) {
                        return await this.loadDeletedTransactions(lastPage);
                    }


                    this.deletedTransactions = result.transactions;
                    this.deletedPagination = result.pagination;
                    this.deletedPage = Number(result.pagination.current_page) || 1;
                    this.deletedLoaded = true;

                    return true;

                } catch (error) {

                    this.deletedTransactions = [];

                    this.deletedPagination = {
                        current_page: 1,
                        total_pages: 0,
                        total: 0
                    };

                    this.deletedPage = 1;
                    this.deletedError = 'دریافت تراکنش‌های حذف‌شده انجام نشد، دوباره تلاش کنید.';
                    this.deletedLoaded = false;

                    return false;

                } finally {
                    this.loadingDeleted = false;
                }
            },

            changeFilterType() {
                this.categoryInput = 'all';
            },

            applyFilters(){
                this.appliedFilters = {
                    search: this.searchInput.trim(),
                    type: this.typeInput,
                    category_id: this.categoryInput,
                    date_from: this.dateFromInput,
                    date_to: this.dateToInput,
                    sort: this.sortInput
                };

                this.activePage = 1;
                this.message = '';
                this.loadTransactions(1);
            },

            clearFilters(){
                this.searchInput = '';
                this.typeInput = 'all';
                this.categoryInput = 'all';
                this.dateFromInput = '';
                this.dateToInput = '';
                this.sortInput = 'newest';

                this.appliedFilters = {
                    search: '',
                    type: 'all',
                    category_id: 'all',
                    date_from: '',
                    date_to: '',
                    sort: 'newest'
                };

                this.activePage = 1;
                this.message = '';
                this.loadTransactions(1);
            },

            filterCategories() {

                return this.categories.filter(category => {
                    return this.typeInput === 'all' || category.type === this.typeInput;
                });

            },


            showActiveTransactions() {
                this.currentTab = 'active';
            },

            showDeletedTransactions() {
                this.currentTab = 'deleted';

                if (!this.deletedLoaded && !this.loadingDeleted) {
                    this.loadDeletedTransactions(1);
                }
            },

            changeActivePage(page) {
                if (this.loadingActive || page < 1 || page > this.activePagination.total_pages || page === this.activePage) {
                    return;
                }
                this.loadTransactions(page);
            },

            changeDeletedPage(page) {
                if (this.loadingDeleted || page < 1 || page > this.deletedPagination.total_pages || page === this.deletedPage) {
                    return;
                }
                this.loadDeletedTransactions(page);
            },

            getPageNumbers(currentPage, totalPages) {
                const pages = [];

                if (totalPages <= 4) {
                    for (let page = 1; page <= totalPages; page++) {
                        pages.push(page);
                    }
                    return pages;
                }

                const start = Math.max(1, currentPage - 1);

                const end = Math.min(totalPages, currentPage + 1);

                if (start > 1) {
                    pages.push(1);

                    if (start > 2) {
                        pages.push('...');
                    }
                }

                for (let page = start; page <= end; page++) {
                    pages.push(page);
                }

                if (end < totalPages) {
                    if (end < totalPages - 1) {
                        pages.push('...');
                    }
                    pages.push(totalPages);
                }
                return pages;
            },

            paginationStart(pagination) {
                if (Number(pagination.total) === 0) {
                    return 0;
                }
                return (Number(pagination.current_page) - 1) * this.limit + 1;
            },

            paginationEnd(pagination) {
                if (Number(pagination.total) === 0) {
                    return 0;
                }
                return Math.min(Number(pagination.current_page) * this.limit, Number(pagination.total));
            },

            categoryOf(transaction) {
                return this.categories.find(category => {
                    return String(category.id) === String(transaction.category_id);
                });
            },

            categoryType(transaction) {
                const category = this.categoryOf(transaction);
                return category ? category.type : '';
            },

            categoryName(transaction) {
                const category = this.categoryOf(transaction);
                return category ? category.name : '—';
            },

            transactionTypeLabel(transaction) {
                const type = this.categoryType(transaction);
                if (type === 'income') {
                    return 'درآمد';
                }

                if (type === 'expense') {
                    return 'هزینه';
                }
                return 'نامشخص';
            },

            formatNumber(number) {
                return window.PersianFormat.integer(number);
            },

            formatAmount(amount) {
                return window.PersianFormat.number(amount);
            },

            formatDate(date) {
                return window.PersianFormat.date(date);
            },

            formatDateTime(date) {
                return window.PersianFormat.dateTime(date);
            },

            transactionCategories() {
                return this.categories.filter(category => {
                    return category.type === this.transactionForm.type;
                });
            }, 
            
            updateCategoriesWarning() {

                if (this.categoriesError) {
                    this.categoriesWarning = this.categoriesError;
                } else if (this.categories.length === 0) {
                    this.categoriesWarning = 'ابتدا یک دسته‌بندی فعال ایجاد کنید.';
                } else if (
                    this.transactionForm.type &&
                    this.transactionCategories().length === 0
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

                window.bootstrap.Modal.getOrCreateInstance(
                    document.getElementById("transactionModal")).show();
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

                window.bootstrap.Modal.getOrCreateInstance(document.getElementById("transactionModal")).show();
            },

            changeTransactionType() {
                this.transactionForm.category_id = '';

                this.updateCategoriesWarning();
            },

            formatTransactionAmount(event) {

                this.transactionForm.amount = window.PersianFormat.groupDigits(event.target.value);

            },

            async saveTransaction(){
                if(this.savingTransaction){
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

                const page = this.editingId ? this.activePage : 1;

                try{
                    const response = await fetch(url , {
                        method: 'POST',
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if(result.success){

                        window.bootstrap.Modal.getOrCreateInstance(document.getElementById("transactionModal")).hide();
                        this.resetTransactionForm();

                        this.activePage = page; 
                        this.message = result.message || "تراکنش با موفقیت ذخیره شد. ";
                        this.messageType = 'success';

                        const loaded = await this.loadTransactions(page);// اگر تراکنش جدید ثبت کرده باشیم برابر 1 است. اگر تراکنشی را ویرایش کرده باشیم، همان صفحه قبلی را دریافت میکنیم.

                        if(!loaded){
                            this.message = "تراکنش ذخیره شد، اما فهرست به روزرسانی نشد. ";
                            this.messageType = 'warning';
                        }
                    }else{
                        this.transactionMessage = result.message || "ذخیره تراکنش انجام نشد. ";
                    }
                }catch(error){
                    this.transactionMessage = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید. ";
                }finally{
                    this.savingTransaction = false; // وضعیت ذخیره‌سازی را به حالت عادی برمیگرداند تا کاربر بتواند دوباره تراکنش ثبت یا ویرایش کند.
                }
            },

            askDelete(transaction) {

                this.transactionToDelete = transaction;
                this.deleteMessage = '';

                window.bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteTransactionModal')).show();
            },

            async deleteTransaction(){

                if (this.deletingTransaction || !this.transactionToDelete) {
                    return;
                }

                this.deletingTransaction = true;
                this.deleteMessage = '';

                let url = "<?= site_url('api/transactions') ?>/" + this.transactionToDelete.id + "/delete";

                try{
                    const response = await fetch(url, {
                        method: 'POST'
                    });

                    const result = await response.json();

                    if(result.success){
                        window.bootstrap.Modal.getOrCreateInstance(document.getElementById("deleteTransactionModal")).hide();

                        this.message = result.message || "تراکنش با موفقیت حذف شد. ";
                        this.messageType = 'success';
                        this.deletedLoaded = false;

                        const activeLoaded = await this.loadTransactions(this.activePage);
                        const deletedLoaded = await this.loadDeletedTransactions(1);

                        if (!activeLoaded || !deletedLoaded) {
                            this.message = "تراکنش حذف شد، اما یکی از فهرست‌ها به‌روزرسانی نشد.";
                            this.messageType = "warning";
                        }
                    } else{
                        this.deleteMessage = result.message || "حذف تراکنش انجام نشد. ";
                    }
                }catch(error){
                    this.deleteMessage = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید. ";
                }finally{
                    this.deletingTransaction = false; //بعد از پایان عملیات حذف، وضعیت حذف را به حالت عادی برمیگرداند تا کاربر بتواند دوباره عملیات حذف انجام دهد.
                }
                
            },

            canRestore(transaction) {

                return transaction.restore_available === true && transaction.category_deleted_at == null && !!transaction.category_name;
            },

            restoreReason(transaction) {

                if (!transaction.restore_available) {
                    return 'مهلت بازیابی تمام شده';
                }

                if (transaction.category_deleted_at != null || !transaction.category_name) {
                    return 'ابتدا دسته‌بندی مربوط را بازیابی کنید';
                }

                return 'قابل بازیابی';
            },

            async restoreTransaction(transaction) {

                if (this.restoringId !== null || this.savingTransaction || this.deletingTransaction || !this.canRestore(transaction)) {
                    return;
                }

                const confirmed = window.confirm('آیا تراکنش بازیابی شود؟');

                if (!confirmed) {
                    return;
                }

                this.restoringId = String(transaction.id);

                try {
                    const response = await fetch("<?= site_url('api/transactions') ?>/" + transaction.id + "/restore",{
                            method: 'POST'
                        });

                    const result = await response.json();

                    if (result.success) {

                        const activeLoaded = await this.loadTransactions(this.activePage);
                        const deletedLoaded = await this.loadDeletedTransactions(1);

                        if (activeLoaded && deletedLoaded) {

                            this.message = result.message || "تراکنش با موفقیت بازیابی شد.";
                            this.messageType = 'success';
                        } else {
                            this.message = "تراکنش بازیابی شد، اما یکی از فهرست‌ها به‌روزرسانی نشد.";
                            this.messageType = "warning";
                        }
                    } else {
                        this.message = result.message || "بازیابی تراکنش انجام نشد.";
                        this.messageType = 'danger';
                    }
                } catch (error) {
                    this.message = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید.";
                    this.messageType = 'danger';
                } finally {
                    this.restoringId = null;
                }
            }
        },

        async mounted() {  
            await this.loadCategories();
            this.loadTransactions();
        }
    }).mount('#transactionApp')
</script>

<?php $this->load->view('layouts/footer'); ?>

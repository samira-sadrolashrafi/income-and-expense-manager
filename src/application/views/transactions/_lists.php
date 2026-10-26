<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
                <!-- Active transactions -->
                <section
                    id="activeTransactionsPanel"
                    v-show="!showDeleted"
                    aria-labelledby="activeTransactionsHeading"
                    data-component="active-transactions"
                >
                    <h2
                        id="activeTransactionsHeading"
                        class="visually-hidden"
                    >
                        تراکنش‌های فعال
                    </h2>

<?php $this->load->view('transactions/_filters'); ?>

                    <!-- Active transactions table -->
                    <section
                        class="card dashboard-card mb-4"
                        aria-labelledby="transactionsTableHeading"
                        data-component="transactions-table"
                    >
                        <div class="card-header bg-body border-bottom d-flex align-items-center justify-content-between gap-2 py-3 px-3 px-md-4">
                            <h3
                                id="transactionsTableHeading"
                                class="h6 fw-bold mb-0"
                            >
                                <i
                                    class="fa-solid fa-table-list text-primary me-2"
                                    aria-hidden="true"
                                ></i>
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
                                        <th scope="col" class="text-nowrap pe-3">
                                            عملیات
                                        </th>
                                    </tr>
                                </thead>

                                <tbody
                                    id="transactionsTableBody"
                                    aria-live="polite"
                                >
                                    <tr v-if="loading">
                                        <td
                                            colspan="7"
                                            class="text-center text-body-secondary py-5"
                                        >
                                            <span
                                                class="spinner-border spinner-border-sm me-2"
                                                role="status"
                                                aria-hidden="true"
                                            ></span>
                                            در حال دریافت تراکنش‌ها...
                                        </td>
                                    </tr>

                                    <tr v-else-if="error">
                                        <td
                                            colspan="7"
                                            class="text-center text-danger py-5"
                                            role="alert"
                                        >
                                            {{ error }}

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger ms-2"
                                                @click="retryLoad"
                                            >
                                                تلاش دوباره
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-else-if="!hasTransactions">
                                        <td
                                            colspan="7"
                                            class="text-center text-body-secondary py-5"
                                        >
                                            تراکنشی با این شرایط پیدا نشد.
                                        </td>
                                    </tr>

                                    <tr
                                        v-for="tx in transactions"
                                        :key="tx.id"
                                    >
                                        <td class="ps-3">{{ tx.title || '—' }}</td>

                                        <td>{{ typeLabel(tx) }}</td>

                                        <td :class="amountClass(tx)">
                                            {{ formatNumber(tx.amount) }}
                                        </td>

                                        <td>{{ categoryName(tx) }}</td>

                                        <td class="text-nowrap">
                                            {{ formatDate(tx.transaction_date) }}
                                        </td>

                                        <td>{{ tx.description || '—' }}</td>

                                        <td class="text-nowrap">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary me-1"
                                                @click="openEditModal(tx)"
                                            >
                                                ویرایش
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger me-1"
                                                @click="askDelete(tx)"
                                            >
                                                حذف
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Active transactions pagination -->
                        <div
                            id="transactionsPagination"
                            class="card-footer bg-body"
                            data-component="pagination"
                            v-if="pagination.total_pages > 1"
                        >
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                                <span
                                    id="transactionsPaginationSummary"
                                    class="small text-body-secondary"
                                    aria-live="polite"
                                >{{ paginationSummary }}</span>

                                <nav
                                    aria-label="صفحه‌بندی تراکنش‌های فعال"
                                    class="tz-pagination"
                                >
                                    <ul
                                        id="transactionsPaginationList"
                                        class="pagination pagination-sm mb-0"
                                    >
                                        <li
                                            class="page-item"
                                            :class="{ disabled: !canGoPrevious }"
                                        >
                                            <button
                                                type="button"
                                                class="page-link"
                                                :disabled="!canGoPrevious"
                                                @click="changePage(
                                                    pagination.current_page - 1
                                                )"
                                            >
                                                قبلی
                                            </button>
                                        </li>

                                        <li
                                            v-for="page in paginationPages"
                                            :key="page"
                                            class="page-item"
                                            :class="{
                                                active: page === pagination.current_page
                                            }"
                                        >
                                            <button
                                                type="button"
                                                class="page-link"
                                                :disabled="loading"
                                                :aria-current="page === pagination.current_page
                                                    ? 'page'
                                                    : null"
                                                @click="changePage(page)"
                                            >
                                                {{ formatInteger(page) }}
                                            </button>
                                        </li>

                                        <li
                                            class="page-item"
                                            :class="{ disabled: !canGoNext }"
                                        >
                                            <button
                                                type="button"
                                                class="page-link"
                                                :disabled="!canGoNext"
                                                @click="changePage(
                                                    pagination.current_page + 1
                                                )"
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
                    v-show="showDeleted"
                    class="card dashboard-card mb-4"
                    aria-labelledby="deletedTransactionsHeading"
                    data-component="deleted-transactions"
                >
                    <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">
                        <h2
                            id="deletedTransactionsHeading"
                            class="h6 fw-bold mb-1"
                        >
                            <i
                                class="fa-regular fa-trash-can text-danger me-2"
                                aria-hidden="true"
                            ></i>
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
                                    <th scope="col" class="text-nowrap pe-3">
                                        وضعیت و عملیات
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                id="deletedTransactionsTableBody"
                                aria-live="polite"
                            >
                                <tr v-if="deletedLoading">
                                    <td
                                        colspan="6"
                                        class="text-center text-body-secondary py-5"
                                    >
                                        <span
                                            class="spinner-border spinner-border-sm me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        در حال دریافت تراکنش‌های حذف‌شده...
                                    </td>
                                </tr>

                                <tr v-else-if="deletedError">
                                    <td
                                        colspan="6"
                                        class="text-center text-danger py-5"
                                        role="alert"
                                    >
                                        {{ deletedError }}

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger ms-2"
                                            @click="retryDeleted"
                                        >
                                            تلاش دوباره
                                        </button>
                                    </td>
                                </tr>

                                <tr v-else-if="!deletedLoaded">
                                    <td
                                        colspan="6"
                                        class="text-center text-body-secondary py-5"
                                    >
                                        برای مشاهده تراکنش‌های حذف‌شده، این بخش را انتخاب کنید.
                                    </td>
                                </tr>

                                <tr v-else-if="!hasDeletedTransactions">
                                    <td
                                        colspan="6"
                                        class="text-center text-body-secondary py-5"
                                    >
                                        تراکنش حذف‌شده‌ای وجود ندارد.
                                    </td>
                                </tr>

                                <tr
                                    v-for="tx in deletedTransactions"
                                    :key="tx.id"
                                >
                                    <td class="ps-3">{{ tx.title || '—' }}</td>

                                    <td>{{ formatNumber(tx.amount) }}</td>

                                    <td>{{ tx.category_name || '—' }}</td>

                                    <td class="text-nowrap">
                                        {{ formatDate(tx.transaction_date) }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ formatDateTime(tx.deleted_at) }}
                                    </td>

                                    <td class="text-nowrap">
                                        <span
                                            class="small d-block mb-2"
                                            :class="canRestore(tx)
                                                ? 'text-success'
                                                : 'text-warning'"
                                        >{{ restoreReason(tx) }}</span>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success me-1"
                                            :disabled="!canRestore(tx)
                                                || deletedLoading
                                                || restoringId !== null"
                                            @click="restoreTransaction(tx)"
                                        >
                                            {{ restoringId === tx.id
                                                ? 'در حال بازیابی...'
                                                : 'بازیابی' }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Deleted transactions pagination -->
                    <div
                        id="deletedTransactionsPagination"
                        class="card-footer bg-body"
                        data-component="pagination"
                        v-if="deletedPagination.total_pages > 1"
                    >
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <span
                                id="deletedTransactionsPaginationSummary"
                                class="small text-body-secondary"
                                aria-live="polite"
                            >{{ deletedPaginationSummary }}</span>

                            <nav
                                aria-label="صفحه‌بندی تراکنش‌های حذف‌شده"
                                class="tz-pagination"
                            >
                                <ul
                                    id="deletedTransactionsPaginationList"
                                    class="pagination pagination-sm mb-0"
                                >
                                    <li
                                        class="page-item"
                                        :class="{ disabled: !canGoPreviousDeleted }"
                                    >
                                        <button
                                            type="button"
                                            class="page-link"
                                            :disabled="!canGoPreviousDeleted"
                                            @click="changeDeletedPage(
                                                deletedPagination.current_page - 1
                                            )"
                                        >
                                            قبلی
                                        </button>
                                    </li>

                                    <li
                                        v-for="page in deletedPaginationPages"
                                        :key="page"
                                        class="page-item"
                                        :class="{
                                            active: page === deletedPagination.current_page
                                        }"
                                    >
                                        <button
                                            type="button"
                                            class="page-link"
                                            :disabled="deletedLoading"
                                            :aria-current="page === deletedPagination.current_page
                                                ? 'page'
                                                : null"
                                            @click="changeDeletedPage(page)"
                                        >
                                            {{ formatInteger(page) }}
                                        </button>
                                    </li>

                                    <li
                                        class="page-item"
                                        :class="{ disabled: !canGoNextDeleted }"
                                    >
                                        <button
                                            type="button"
                                            class="page-link"
                                            :disabled="!canGoNextDeleted"
                                            @click="changeDeletedPage(
                                                deletedPagination.current_page + 1
                                            )"
                                        >
                                            بعدی
                                        </button>
                                    </li>
                                </ul>
                            </nav>

                        </div>
                    </div>
                </section>

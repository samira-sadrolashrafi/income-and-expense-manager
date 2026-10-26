<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
                    <!-- Transaction filters -->
                    <section
                        class="card dashboard-card mb-3"
                        aria-labelledby="transactionFiltersHeading"
                        data-component="transaction-filters"
                    >
                        <div class="card-body p-3 p-md-4">

                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i
                                    class="fa-solid fa-filter text-primary"
                                    aria-hidden="true"
                                ></i>

                                <h3
                                    id="transactionFiltersHeading"
                                    class="h6 fw-bold mb-0"
                                >
                                    جست‌وجو و فیلتر
                                </h3>
                            </div>

                            <div class="row g-3 align-items-end">

                                <!-- Search -->
                                <div class="col-12 col-md-6 col-xl-4">
                                    <label
                                        for="transactionSearch"
                                        class="form-label small fw-medium"
                                    >
                                        جست‌وجو در تراکنش‌ها
                                    </label>

                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-body-tertiary"
                                            aria-hidden="true"
                                        >
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>

                                        <input
                                            type="search"
                                            id="transactionSearch"
                                            v-model.trim="filters.search"
                                            class="form-control"
                                            placeholder="عنوان یا توضیحات..."
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <!-- Transaction type -->
                                <div class="col-6 col-md-3 col-xl-2">
                                    <label
                                        for="transactionTypeFilter"
                                        class="form-label small fw-medium"
                                    >
                                        نوع تراکنش
                                    </label>

                                    <select
                                        id="transactionTypeFilter"
                                        v-model="filters.type"
                                        class="form-select"
                                        @change="onTypeFilterChange"
                                    >
                                        <option value="all">همه انواع</option>
                                        <option value="income">درآمد</option>
                                        <option value="expense">هزینه</option>
                                    </select>
                                </div>

                                <!-- Category -->
                                <div class="col-6 col-md-3 col-xl-2">
                                    <label
                                        for="transactionCategoryFilter"
                                        class="form-label small fw-medium"
                                    >
                                        دسته‌بندی
                                    </label>

                                    <select
                                        id="transactionCategoryFilter"
                                        v-model="filters.category_id"
                                        class="form-select"
                                    >
                                        <option
                                            v-for="option in categoryFilterOptions"
                                            :key="option.value"
                                            :value="option.value"
                                        >{{ option.label }}</option>
                                    </select>
                                </div>

                                <!-- Date from -->
                                <div class="col-6 col-md-6 col-xl-2">
                                    <label
                                        for="transactionDateFrom"
                                        class="form-label small fw-medium"
                                    >
                                        از تاریخ
                                    </label>

                                    <input
                                        type="date"
                                        id="transactionDateFrom"
                                        v-model="filters.date_from"
                                        class="form-control"
                                        autocomplete="off"
                                    >
                                </div>

                                <!-- Date to -->
                                <div class="col-6 col-md-6 col-xl-2">
                                    <label
                                        for="transactionDateTo"
                                        class="form-label small fw-medium"
                                    >
                                        تا تاریخ
                                    </label>

                                    <input
                                        type="date"
                                        id="transactionDateTo"
                                        v-model="filters.date_to"
                                        class="form-control"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>

                            <!-- Apply and clear filters -->
                            <div class="row g-3 mt-4 align-items-end">
                                <div class="col-12 col-md-auto">
                                    <div class="d-flex align-items-center gap-2">
                                    <label
                                        for="transactionSort"
                                        class="form-label small fw-medium mb-0"
                                    >
                                        مرتب‌سازی
                                    </label>
                                    <select
                                        id="transactionSort"
                                        v-model="filters.sort"
                                        class="form-select"
                                        style="width: 160px"
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
                                    >
                                        <i
                                            class="fa-solid fa-rotate-left me-1"
                                            aria-hidden="true"
                                        ></i>
                                        پاک‌کردن فیلترها
                                    </button>

                                    <button
                                        type="button"
                                        id="applyTransactionFiltersButton"
                                        class="btn btn-primary"
                                        @click="applyFilters"
                                    >
                                        <i
                                            class="fa-solid fa-filter me-1"
                                            aria-hidden="true"
                                        ></i>
                                        اعمال فیلترها
                                    </button>
                                </div>

                            </div>
                        </div>
                    </section>

<?php
$this->load->view('layouts/header', array(
    'page_title' => 'مدیریت دسته‌بندی‌ها',
    'page_styles' => array(base_url('assets/css/dashboard.css'))
));
?>

<div class="container-fluid dashboard-shell px-0">

    <div class="row g-0">

        <?php $this->load->view('layouts/app_sidebar', array(
            'active_page' => 'categories'
        )); ?>


        <main id="categoriesApp" class="col-lg-9 col-xl-10 dashboard-main">

            <?php $this->load->view('layouts/app_header', array(
                'app_page_title' => 'دسته‌بندی‌ها',
                'app_page_description' => 'دسته‌بندی‌های درآمد و هزینه خود را مدیریت کنید.',
                'app_username' => $username
            )); ?>


            <div class="px-3 px-xl-4 pt-4">


                <!-- Page heading -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

                    <div>
                        <h2 class="h5 fw-bold mb-1">
                            مدیریت دسته‌بندی‌ها
                        </h2>

                        <p class="text-body-secondary small mb-0">
                            دسته‌بندی‌های درآمد و هزینه خود را مدیریت کنید.
                        </p>
                    </div>


                    <button type="button"
                            class="btn btn-primary d-inline-flex align-items-center gap-2"
                            @click="openAddModal"
                            :disabled="saving || deleting || restoringId !== null">

                        <i class="fa-solid fa-plus" aria-hidden="true"></i>

                        <span>افزودن دسته‌بندی</span>

                    </button>

                </div>


                <!-- General message -->
                <div v-if="message"
                     class="alert"
                     :class="'alert-' + messageType"
                     role="alert">

                    {{ message }}

                </div>


                <!-- Search and filters -->
                <section class="card dashboard-card mb-3">

                    <div class="card-body p-3 p-md-4">

                        <div class="mb-3">

                            <h3 class="h6 fw-bold mb-0">

                                <i class="fa-solid fa-filter text-primary me-2"
                                   aria-hidden="true"></i>

                                جست‌وجو و فیلتر

                            </h3>

                        </div>


                        <div class="row g-3 align-items-end">


                            <!-- Search -->
                            <div class="col-12 col-md-6 col-xl-4">

                                <label for="categoriesSearch"
                                       class="form-label small fw-medium">

                                    جست‌وجو در نام دسته‌بندی

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text bg-body-tertiary">

                                        <i class="fa-solid fa-magnifying-glass"
                                           aria-hidden="true"></i>

                                    </span>

                                    <input type="search"
                                           id="categoriesSearch"
                                           class="form-control"
                                           placeholder="نام دسته‌بندی را وارد کنید"
                                           autocomplete="off"
                                           v-model="searchInput">

                                </div>

                            </div>


                            <!-- Type filter -->
                            <div class="col-6 col-md-3 col-xl-2">

                                <label for="categoriesTypeFilter"
                                       class="form-label small fw-medium">

                                    نوع دسته‌بندی

                                </label>

                                <select id="categoriesTypeFilter"
                                        class="form-select"
                                        v-model="typeInput">

                                    <option value="all">همه انواع</option>
                                    <option value="income">درآمد</option>
                                    <option value="expense">هزینه</option>

                                </select>

                            </div>


                            <!-- Sorting -->
                            <div class="col-6 col-md-3 col-xl-2">

                                <label for="categoriesSortFilter"
                                       class="form-label small fw-medium">

                                    مرتب‌سازی

                                </label>

                                <select id="categoriesSortFilter"
                                        class="form-select"
                                        v-model="sortInput">

                                    <option value="newest">جدیدترین</option>
                                    <option value="oldest">قدیمی‌ترین</option>

                                </select>

                            </div>


                            <!-- Filter buttons -->
                            <div class="col-12 col-xl-4">

                                <div class="d-flex flex-wrap align-items-center gap-2">

                                    <button type="button"
                                            class="btn btn-primary"
                                            @click="applyFilters"
                                            :disabled="loadingActive || saving || deleting || restoringId !== null">

                                        <i class="fa-solid fa-filter me-1"
                                           aria-hidden="true"></i>

                                        اعمال فیلترها

                                    </button>


                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            @click="clearFilters"
                                            :disabled="loadingActive || saving || deleting || restoringId !== null">

                                        <i class="fa-solid fa-rotate-left me-1"
                                           aria-hidden="true"></i>

                                        پاک کردن فیلترها

                                    </button>

                                </div>

                            </div>

                        </div>


                        <p class="form-text mb-0 mt-3">

                            پس از تنظیم فیلترها، روی «اعمال فیلترها» کلیک کنید.

                        </p>

                    </div>

                </section>


                <!-- Active and deleted tabs -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">

                    <div class="btn-group"
                         role="group"
                         aria-label="وضعیت دسته‌بندی‌ها">

                        <button type="button"
                                class="btn"
                                :class="activeTab === 'active' ? 'btn-primary' : 'btn-outline-primary'"
                                :aria-pressed="activeTab === 'active'"
                                @click="showActiveCategories">

                            <i class="fa-solid fa-list-check me-1"
                               aria-hidden="true"></i>

                            فعال

                        </button>


                        <button type="button"
                                class="btn"
                                :class="activeTab === 'deleted' ? 'btn-primary' : 'btn-outline-primary'"
                                :aria-pressed="activeTab === 'deleted'"
                                @click="showDeletedCategories">

                            <i class="fa-regular fa-trash-can me-1"
                               aria-hidden="true"></i>

                            حذف‌شده

                        </button>

                    </div>


                    <span class="small text-body-secondary">

                        <i class="fa-regular fa-clock me-1"
                           aria-hidden="true"></i>

                        دسته‌بندی‌های حذف‌شده تا ۳۰ روز امکان بازیابی دارند.

                    </span>

                </div>


                <!-- Active categories -->
                <div v-show="activeTab === 'active'">

                    <section class="card dashboard-card mb-4">


                        <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">

                            <h3 class="h6 fw-bold mb-0">

                                <i class="fa-solid fa-tags text-primary me-2"
                                   aria-hidden="true"></i>

                                دسته‌بندی‌های فعال

                            </h3>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th scope="col" class="ps-3">
                                            نام دسته‌بندی
                                        </th>

                                        <th scope="col">
                                            نوع
                                        </th>

                                        <th scope="col">
                                            تاریخ ایجاد
                                        </th>

                                        <th scope="col" class="text-nowrap pe-3">
                                            عملیات
                                        </th>

                                    </tr>

                                </thead>


                                <tbody aria-live="polite">


                                    <tr v-if="loadingActive">

                                        <td colspan="4"
                                            class="text-center text-body-secondary py-5">

                                            <span class="spinner-border spinner-border-sm me-2"
                                                  role="status"
                                                  aria-hidden="true"></span>

                                            در حال دریافت دسته‌بندی‌ها...

                                        </td>

                                    </tr>


                                    <tr v-else-if="activeError">

                                        <td colspan="4"
                                            class="text-center text-danger py-5">

                                            {{ activeError }}

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary ms-2"
                                                    @click="loadActiveCategories()">

                                                تلاش دوباره

                                            </button>

                                        </td>

                                    </tr>


                                    <tr v-else-if="activeCategories.length === 0">

                                        <td colspan="4"
                                            class="text-center text-body-secondary py-5">

                                            دسته‌بندی فعالی مطابق فیلترها وجود ندارد.

                                        </td>

                                    </tr>


                                    <template v-else>

                                        <tr v-for="category in activeCategories"
                                            :key="category.id">


                                            <td class="ps-3">

                                                <i class="fa-solid me-2"
                                                   :class="categoryIcon(category)"
                                                   aria-hidden="true"></i>

                                                {{ category.name || '—' }}

                                            </td>


                                            <td>

                                                {{ category.type === 'income' ? 'درآمد' : 'هزینه' }}

                                            </td>


                                            <td class="text-nowrap">

                                                {{ formatDate(category.created_at) }}

                                            </td>


                                            <td class="text-nowrap pe-3">


                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary me-1"
                                                        @click="openEditModal(category)"
                                                        :disabled="saving || deleting || restoringId !== null">

                                                    ویرایش

                                                </button>


                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        @click="askDelete(category)"
                                                        :disabled="saving || deleting || restoringId !== null">

                                                    حذف

                                                </button>


                                            </td>

                                        </tr>

                                    </template>


                                </tbody>

                            </table>

                        </div>


                        <!-- Active pagination -->
                        <div v-if="activePagination && activePagination.total_pages > 1"
                             class="card-footer bg-body">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">


                                <span class="small text-body-secondary">

                                    نمایش

                                    {{ formatNumber(paginationStart(activePagination)) }}

                                    تا

                                    {{ formatNumber(paginationEnd(activePagination)) }}

                                    از

                                    {{ formatNumber(activePagination.total) }}

                                </span>


                                <nav class="tz-pagination"
                                     aria-label="صفحه‌بندی دسته‌بندی‌های فعال">

                                    <ul class="pagination pagination-sm mb-0">


                                        <!-- Previous page -->
                                        <li class="page-item"
                                            :class="{ disabled: activePage === 1 }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="activePage === 1 || loadingActive || saving || deleting || restoringId !== null"
                                                    @click="changeActivePage(activePage - 1)">

                                                قبلی

                                            </button>

                                        </li>


                                        <!-- Page numbers -->
                                        <li v-for="(page, index) in getPageNumbers(activePage, activePagination.total_pages)"
                                            :key="index"
                                            class="page-item"
                                            :class="{ active: page === activePage, disabled: page === '...' }">


                                            <button v-if="page !== '...'"
                                                    type="button"
                                                    class="page-link"
                                                    :aria-current="page === activePage ? 'page' : null"
                                                    :disabled="loadingActive || saving || deleting || restoringId !== null"
                                                    @click="changeActivePage(page)">

                                                {{ formatNumber(page) }}

                                            </button>


                                            <span v-else class="page-link">

                                                ...

                                            </span>


                                        </li>


                                        <!-- Next page -->
                                        <li class="page-item"
                                            :class="{ disabled: activePage === activePagination.total_pages }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="activePage === activePagination.total_pages || loadingActive || saving || deleting || restoringId !== null"
                                                    @click="changeActivePage(activePage + 1)">

                                                بعدی

                                            </button>

                                        </li>


                                    </ul>

                                </nav>

                            </div>

                        </div>


                    </section>

                </div>



                <!-- Deleted categories -->
                <div v-show="activeTab === 'deleted'">

                    <section class="card dashboard-card mb-4">


                        <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">

                            <h3 class="h6 fw-bold mb-1">

                                <i class="fa-regular fa-trash-can text-danger me-2"
                                   aria-hidden="true"></i>

                                دسته‌بندی‌های حذف‌شده

                            </h3>


                            <p class="small text-body-secondary mb-0">

                                دسته‌بندی‌های حذف‌شده در مهلت مجاز قابل بازیابی هستند.

                            </p>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">


                                <thead class="table-light">

                                    <tr>

                                        <th scope="col" class="ps-3">
                                            نام دسته‌بندی
                                        </th>

                                        <th scope="col">
                                            نوع
                                        </th>

                                        <th scope="col">
                                            زمان حذف
                                        </th>

                                        <th scope="col">
                                            وضعیت بازیابی
                                        </th>

                                        <th scope="col" class="text-nowrap pe-3">
                                            عملیات
                                        </th>

                                    </tr>

                                </thead>


                                <tbody aria-live="polite">


                                    <tr v-if="loadingDeleted">

                                        <td colspan="5"
                                            class="text-center text-body-secondary py-5">

                                            <span class="spinner-border spinner-border-sm me-2"
                                                  role="status"
                                                  aria-hidden="true"></span>

                                            در حال دریافت دسته‌بندی‌های حذف‌شده...

                                        </td>

                                    </tr>


                                    <tr v-else-if="deletedError">

                                        <td colspan="5"
                                            class="text-center text-danger py-5">

                                            {{ deletedError }}

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary ms-2"
                                                    @click="loadDeletedCategories()">

                                                تلاش دوباره

                                            </button>

                                        </td>

                                    </tr>


                                    <tr v-else-if="deletedCategories.length === 0">

                                        <td colspan="5"
                                            class="text-center text-body-secondary py-5">

                                            دسته‌بندی حذف‌شده‌ای وجود ندارد.

                                        </td>

                                    </tr>


                                    <template v-else>

                                        <tr v-for="category in deletedCategories"
                                            :key="category.id">


                                            <td class="ps-3">

                                                <i class="fa-solid me-2"
                                                   :class="categoryIcon(category)"
                                                   aria-hidden="true"></i>

                                                {{ category.name || '—' }}

                                            </td>


                                            <td>

                                                {{ category.type === 'income' ? 'درآمد' : 'هزینه' }}

                                            </td>


                                            <td class="text-nowrap">

                                                {{ formatDateTime(category.deleted_at) }}

                                            </td>


                                            <td :class="category.restore_available ? 'text-success' : 'text-warning'">

                                                {{ category.restore_available ? 'در مهلت بازیابی' : 'مهلت بازیابی تمام شده' }}

                                            </td>


                                            <td class="text-nowrap pe-3">

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success"
                                                        @click="restoreCategory(category)"
                                                        :disabled="!category.restore_available || saving || deleting || restoringId !== null">

                                                    {{ restoringId === category.id ? 'در حال بازیابی...' : 'بازیابی' }}

                                                </button>

                                            </td>


                                        </tr>

                                    </template>


                                </tbody>

                            </table>

                        </div>


                        <!-- Deleted pagination -->
                        <div v-if="deletedPagination && deletedPagination.total_pages > 1"
                             class="card-footer bg-body">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">


                                <span class="small text-body-secondary">

                                    نمایش

                                    {{ formatNumber(paginationStart(deletedPagination)) }}

                                    تا

                                    {{ formatNumber(paginationEnd(deletedPagination)) }}

                                    از

                                    {{ formatNumber(deletedPagination.total) }}

                                </span>


                                <nav class="tz-pagination"
                                     aria-label="صفحه‌بندی دسته‌بندی‌های حذف‌شده">

                                    <ul class="pagination pagination-sm mb-0">


                                        <!-- Previous page -->
                                        <li class="page-item"
                                            :class="{ disabled: deletedPage === 1 }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="deletedPage === 1 || loadingDeleted || saving || deleting || restoringId !== null"
                                                    @click="changeDeletedPage(deletedPage - 1)">

                                                قبلی

                                            </button>

                                        </li>


                                        <!-- Page numbers -->
                                        <li v-for="(page, index) in getPageNumbers(deletedPage, deletedPagination.total_pages)"
                                            :key="index"
                                            class="page-item"
                                            :class="{ active: page === deletedPage, disabled: page === '...' }">


                                            <button v-if="page !== '...'"
                                                    type="button"
                                                    class="page-link"
                                                    :aria-current="page === deletedPage ? 'page' : null"
                                                    :disabled="loadingDeleted || saving || deleting || restoringId !== null"
                                                    @click="changeDeletedPage(page)">

                                                {{ formatNumber(page) }}

                                            </button>


                                            <span v-else class="page-link">

                                                ...

                                            </span>


                                        </li>

                                        
                                        <!-- Next page -->
                                        <li class="page-item"
                                            :class="{ disabled: deletedPage === deletedPagination.total_pages }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="deletedPage === deletedPagination.total_pages || loadingDeleted || saving || deleting || restoringId !== null"
                                                    @click="changeDeletedPage(deletedPage + 1)">

                                                بعدی

                                            </button>

                                        </li>


                                    </ul>

                                </nav>

                            </div>

                        </div>


                    </section>

                </div>


            </div>


            <!-- Add / Edit category modal -->
            <div class="modal fade tz-modal"
                 id="categoryModal"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="categoryModalTitle"
                 aria-hidden="true">


                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">


                        <form @submit.prevent="saveCategory">


                            <div class="modal-header">

                                <div class="d-flex align-items-center gap-2">

                                    <i class="fa-solid fa-tags text-primary"
                                       aria-hidden="true"></i>

                                    <h2 class="modal-title fs-5 fw-semibold mb-0"
                                        id="categoryModalTitle">

                                        {{ isEditing ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی' }}

                                    </h2>

                                </div>


                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        :disabled="saving"
                                        aria-label="بستن"></button>

                            </div>



                            <div class="modal-body p-3 p-md-4">


                                <p class="small text-body-secondary mb-3">

                                    برای مرتب‌سازی درآمدها و هزینه‌ها، یک نام و نوع مشخص کنید.

                                </p>


                                <!-- Backend error message -->
                                <div v-if="formMessage"
                                     class="alert alert-danger"
                                     role="alert">

                                    {{ formMessage }}

                                </div>


                                <!-- Category name -->
                                <div class="mb-3">

                                    <label for="categoryName"
                                           class="form-label fw-medium">

                                        نام دسته‌بندی

                                    </label>


                                    <input type="text"
                                           class="form-control"
                                           id="categoryName"
                                           autocomplete="off"
                                           placeholder="مثلاً خوراکی، حمل‌ونقل یا حقوق"
                                           v-model="categoryName"
                                           @input="formMessage = ''"
                                           :disabled="saving">

                                </div>



                                <!-- Category type -->
                                <div class="mb-0">

                                    <label for="categoryType"
                                           class="form-label fw-medium">

                                        نوع دسته‌بندی

                                    </label>


                                    <select class="form-select"
                                            id="categoryType"
                                            v-model="categoryType"
                                            @change="formMessage = ''"
                                            :disabled="saving">

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

                                </div>


                            </div>



                            <div class="modal-footer">


                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal"
                                        :disabled="saving">

                                    انصراف

                                </button>



                                <button type="submit"
                                        class="btn btn-primary"
                                        :disabled="saving">

                                    {{ saving ? 'در حال ذخیره...' : 'ذخیره' }}

                                </button>


                            </div>


                        </form>

                    </div>

                </div>

            </div>



            <!-- Delete category confirmation modal -->
            <div class="modal fade tz-modal"
                 id="deleteCategoryModal"
                 tabindex="-1"
                 role="dialog"
                 aria-labelledby="deleteCategoryModalTitle"
                 aria-hidden="true">


                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">


                        <div class="modal-header">

                            <h2 class="modal-title fs-5 fw-semibold"
                                id="deleteCategoryModalTitle">

                                <i class="fa-regular fa-trash-can text-danger me-2"
                                   aria-hidden="true"></i>

                                حذف دسته‌بندی

                            </h2>


                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    :disabled="deleting"
                                    aria-label="بستن"></button>

                        </div>



                        <div class="modal-body">


                            <p class="mb-2">

                                آیا از حذف دسته‌بندی

                                «<span class="fw-semibold">
                                    {{ categoryToDelete ? categoryToDelete.name : '' }}
                                </span>»

                                اطمینان دارید؟

                            </p>



                            <div class="alert alert-warning small mb-0"
                                 role="note">

                                <i class="fa-solid fa-triangle-exclamation me-1"
                                   aria-hidden="true"></i>

                                تراکنش‌های فعال مرتبط نیز حذف نرم می‌شوند و اثر مالی آن‌ها از موجودی برگردانده می‌شود.
                                بازیابی فقط در مهلت مجاز امکان‌پذیر است.

                            </div>



                            <div v-if="deleteMessage"
                                 class="alert alert-danger mt-3 mb-0"
                                 role="alert">

                                {{ deleteMessage }}

                            </div>


                        </div>



                        <div class="modal-footer">


                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal"
                                    :disabled="deleting">

                                انصراف

                            </button>



                            <button type="button"
                                    class="btn btn-danger"
                                    @click="deleteCategory"
                                    :disabled="deleting || !categoryToDelete">

                                {{ deleting ? 'در حال حذف...' : 'حذف دسته‌بندی' }}

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
            return {
                activeCategories: [],
                deletedCategories: [],

                loadingActive: true,
                loadingDeleted: false,

                activeError: '',
                deletedError: '',

                deletedLoaded: false,

                activeTab: 'active',

                searchInput: '',
                typeInput: 'all',
                sortInput: 'newest',

                appliedFilters: {
                    search: '',
                    type: 'all',
                    sort: 'newest'
                },

                activePage: 1,
                deletedPage: 1,

                limit: 10,

                activePagination: null,
                deletedPagination: null,

                message: '',
                messageType: 'success',

                categoryName: '',
                categoryType: '',


                formMessage: '',

                isEditing: false,
                editingCategoryId: null,

                categoryToDelete: null,
                deleteMessage: '',


                saving: false,
                deleting: false,
                restoringId: null
            }
        },

        methods: {
            async loadActiveCategories(page = this.activePage){
                this.loadingActive = true;
                this.activeError = '';

                const params = new URLSearchParams();

                params.append('page', page);
                params.append('search', this.appliedFilters.search);
                params.append('type', this.appliedFilters.type);
                params.append('sort', this.appliedFilters.sort);

                try{
                    const response = await fetch(
                        "<?= site_url('api/categories') ?>?" + params.toString()
                    );

                    const result = await response.json();

                    if(!result.success){
                        this.activeCategories = [];
                        this.activePagination = null;

                        this.activeError = result.message || "دریافت دسته بندی ها انجام نشد. ";
                        return false;
                    }

                    if(result.pagination.total_pages > 0 && result.pagination.total_pages < page){
                        return await this.loadActiveCategories(result.pagination.total_pages);
                    }

                    this.activeCategories = result.categories;
                    this.activePagination = result.pagination;

                    this.activePage = Number(result.pagination.current_page) || 1;
                    return true; 

                }catch(error){
                    this.activeCategories = [];
                    this.activePagination = null;

                    this.activeError = "دریافت دسته بندی ها انجام نشد، دوباره تلاش کنید. ";

                    return false;
                } finally{
                    this.loadingActive = false;
                }
            },

            async loadDeletedCategories(page = this.deletedPage){
                this.loadingDeleted = true;
                this.deletedError = '';

                try{
                    const response = await fetch(
                    "<?= site_url('api/categories/deleted') ?>?page=" + page
                );

                const result = await response.json();

                if(!result.success){
                    this.deletedCategories = [];
                    this.deletedPagination = null;

                    this.deletedError = result.message || "دریافت دسته بندی های حذف شده انجام نشد. ";

                    this.deletedLoaded = false;

                    return false;
                }

                if(result.pagination.total_pages > 0 && result.pagination.total_pages < page){
                    return await this.loadDeletedCategories(result.pagination.total_pages);
                }

                this.deletedCategories = result.categories;
                this.deletedPagination = result.pagination;

                this.deletedPage = Number(result.pagination.current_page) || 1;

                this.deletedLoaded = true;
                
                return true;

                }catch(error){
                    this.deletedCategories = [];
                    this.deletedPagination = null;

                    this.deletedError = "دریافت دسته بندی های حذف شده انجام نشد، دوباره تلاش کنید. ";

                    this.deletedLoaded = false;

                    return false;

                } finally{
                    this.loadingDeleted = false;
                }
            },
            
            applyFilters(){

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.appliedFilters = {
                    search : this.searchInput.trim(),
                    type: this.typeInput,
                    sort: this.sortInput
                };

                this.activePage = 1;

                this.message = '';

                this.loadActiveCategories(1);
            },

            clearFilters(){

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.searchInput = '';
                this.typeInput = 'all';
                this.sortInput = 'newest';

                this.appliedFilters = {
                    search: '',
                    type: 'all',
                    sort: 'newest'
                };

                this.activePage = 1;

                this.message = '';

                this.loadActiveCategories(1);
            },

            showActiveCategories() {
                this.activeTab = 'active';
            },

            showDeletedCategories(){
                this.activeTab = 'deleted';

                if(!this.deletedLoaded && !this.loadingDeleted){
                    this.loadDeletedCategories(1);
                }
            },

            changeActivePage(page) {
                
                if (this.saving || this.deleting || this.restoringId !== null || this.loadingActive || !this.activePagination || page < 1 || page > this.activePagination.total_pages || page === this.activePage) {
                    return;
                }

                this.loadActiveCategories(page);

            },

            changeDeletedPage(page) {
                
                if (this.saving || this.deleting || this.restoringId !== null || this.loadingDeleted || !this.deletedPagination || page < 1 || page > this.deletedPagination.total_pages || page === this.deletedPage) {
                    return;
                }

                this.loadDeletedCategories(page);

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

            paginationStart(pagination){
                return (Number(pagination.current_page) - 1) * this.limit + 1;
            },

            paginationEnd(pagination){
                return Math.min(Number(pagination.current_page) * this.limit, Number(pagination.total));
            },

            formatNumber(number) {
                return window.PersianFormat.integer(number);
            },

            formatDate(date) {
                return window.PersianFormat.date(date);
            },

            formatDateTime(date) {
                return window.PersianFormat.dateTime(date);
            },

            categoryIcon(category) {

                const name = String(category.name || '');

                if (category.type === 'income') {

                    if (/حقوق|دستمزد/.test(name)) {
                        return 'fa-money-bill-wave text-success';
                    }

                    if (/فروش/.test(name)) {
                        return 'fa-cart-shopping text-primary';
                    }

                    if (/هدیه/.test(name)) {
                        return 'fa-gift text-info';
                    }

                    return 'fa-arrow-trend-up text-success';

                }

                if (/خوراک|غذا/.test(name)) {
                    return 'fa-utensils text-warning';
                }

                if (/حمل|رفت.*آمد/.test(name)) {
                    return 'fa-car text-info';
                }

                if (/مسکن|اجاره/.test(name)) {
                    return 'fa-house text-primary';
                }

                if (/درمان|سلامت/.test(name)) {
                    return 'fa-heart-pulse text-danger';
                }

                if (/آموزش/.test(name)) {
                    return 'fa-graduation-cap text-info';
                }

                return 'fa-arrow-trend-down text-danger';

            },

            resetCategoryForm(){
                this.categoryName = '';
                this.categoryType = '';


                this.formMessage = '';

                this.isEditing = false;
                this.editingCategoryId = null;
            },

            openAddModal(){

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.resetCategoryForm();

                window.bootstrap.Modal.getOrCreateInstance(document.getElementById("categoryModal")).show();
            },

            openEditModal(category) {

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.resetCategoryForm();

                this.isEditing = true;
                this.editingCategoryId = category.id;

                this.categoryName = category.name;
                this.categoryType = category.type;

                window.bootstrap.Modal.getOrCreateInstance(document.getElementById("categoryModal")).show();

            },

            //ایجاد و ویرایش دسته بندی
            async saveCategory(){

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.saving = true;

                this.formMessage = '';

                let url = "<?= site_url('api/categories/store') ?>";

                if (this.isEditing) {
                    url = "<?= site_url('api/categories') ?>/"

                        + this.editingCategoryId + "/update";
                }

                const page = this.isEditing ? this.activePage : 1;

                try{
                    const response = await fetch(url, {
                        method: 'POST',
                        body: JSON.stringify({
                            name: this.categoryName.trim(),
                            type: this.categoryType
                        })
                    });

                    const result = await response.json();

                    if(result.success){
                        window.bootstrap.Modal.getOrCreateInstance(document.getElementById("categoryModal")).hide();

                        this.resetCategoryForm();

                        this.activePage = page; 

                        this.message = result.message || "دسته بندی با موفقیت ذخیره شد. ";
                        this.messageType = 'success';

                        const loaded = await this.loadActiveCategories(page);

                        if(!loaded){
                            this.message = "دسته بندی ذخیره شد، اما فهرست به روز رسانی نشد. ";
                            this.messageType = "warning";
                        }
                    }else{
                        this.formMessage = result.message || "ذخیره دسته بندی انجام نشد. ";
                    }
                }catch(error){
                    this.formMessage = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید. ";
                }finally{
                    this.saving = false;
                }
            },

            askDelete(category){

                if (this.saving || this.deleting || this.restoringId !== null) {
                    return;
                }

                this.categoryToDelete = category;

                this.deleteMessage = '';

                window.bootstrap.Modal.getOrCreateInstance(document.getElementById("deleteCategoryModal")).show();
            },

            async deleteCategory(){

                if(this.saving || this.deleting || this.restoringId !== null || !this.categoryToDelete){
                    return;
                }

                this.deleting = true;
                this.deleteMessage = '';

                try{
                    const response = await fetch("<?= site_url('api/categories') ?>/" 
                        + this.categoryToDelete.id + "/delete", {
                        method: 'POST'
                    });

                    const result = await response.json();

                    if(result.success){

                        window.bootstrap.Modal.getOrCreateInstance(document.getElementById("deleteCategoryModal")).hide();

                        this.categoryToDelete = null;

                        this.deletedLoaded = false;

                        this.message = result.message || "دسته بندی مورد نظر با موفقیت حذف شد. ";
                        this.messageType = 'success';

                        const loaded = await this.loadActiveCategories();

                        if(!loaded){
                            this.message = "دسته بندی مورد نظر حذف شد، اما فهرست به روز نشد. ";
                            this.messageType = 'warning';
                        }
                    } else{
                        this.deleteMessage = result.message || "حذف دسته بندی انجام نشد. ";
                    }
                }catch(error){
                    this.deleteMessage = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید. "    
                }finally{
                    this.deleting = false;
                }
            },

            async restoreCategory(category){
                if(this.saving || this.deleting || this.restoringId !== null || !category.restore_available){
                    return;
                }

                const confirmed = window.confirm("دسته بندی بازیابی شود؟ تراکنش های حذف شده خودکار بازیابی نمی شوند. ");

                if(!confirmed){
                    return;
                }

                this.restoringId = category.id;

                try{
                    const response = await fetch("<?= site_url('api/categories') ?>/"
                        + category.id + "/restore",{
                            method: 'POST'
                    });

                    const result = await response.json();

                    if(result.success){

                        const activeLoaded = await this.loadActiveCategories();

                        const deletedLoaded = await this.loadDeletedCategories();

                        if(activeLoaded && deletedLoaded){
                            this.message = result.message || "دسته بندی با موفقیت بازیابی شد. ";
                            this.messageType = "success";
                        } else{
                            this.message = "دسته بندی بازیابی شد، اما یکی از فهرست ها به روز نشد. ";
                            this.messageType = 'warning';
                        }
                    } else{
                        this.message = result.message || "بازیابی دسته بندی انجام نشد. ";
                        this.messageType = 'danger';
                    }
                }catch(error){
                    this.message = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید. ";
                    this.messageType = 'danger';
                } finally{
                    this.restoringId = null;
                }
            }
        },

        mounted(){
            this.loadActiveCategories();
        }

    }).mount('#categoriesApp')
</script>


<?php $this->load->view('layouts/footer'); ?>

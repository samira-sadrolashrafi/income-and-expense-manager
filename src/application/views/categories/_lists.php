                <!-- Active categories -->
                <div id="activeCategoriesPanel"
                     v-show="!showDeleted"
                     data-component="active-categories">

                    <section class="card dashboard-card mb-4"
                             aria-labelledby="categoriesTableHeading">

                        <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">
                            <h3 id="categoriesTableHeading"
                                class="h6 fw-bold mb-0">
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
                                        <th scope="col">نوع</th>
                                        <th scope="col">تاریخ ایجاد</th>
                                        <th scope="col" class="text-nowrap pe-3">
                                            عملیات
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="categoriesTableBody"
                                       aria-live="polite">

                                    <tr v-if="loading">
                                        <td colspan="4"
                                            class="text-center text-body-secondary py-5">
                                            <span class="spinner-border spinner-border-sm me-2"
                                                  role="status"
                                                  aria-hidden="true"></span>
                                            در حال دریافت دسته‌بندی‌ها...
                                        </td>
                                    </tr>

                                    <tr v-else-if="error">
                                        <td colspan="4"
                                            class="text-center text-danger py-5"
                                            role="alert">
                                            {{ error }}

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger ms-2"
                                                    @click="retryLoad">
                                                تلاش دوباره
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-else-if="!hasCategories">
                                        <td colspan="4"
                                            class="text-center text-body-secondary py-5">
                                            دسته‌بندی فعالی مطابق فیلترها وجود ندارد.
                                        </td>
                                    </tr>

                                    <tr v-for="category in categories"
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
                                                    @click="openEditModal(category)">
                                                ویرایش
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger me-1"
                                                    @click="askDeleteCategory(category)">
                                                حذف
                                            </button>
                                        </td>

                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <!-- Server-side pagination -->
                        <div id="categoriesPagination"
                            class="card-footer bg-body"
                            data-component="pagination"
                            v-if="pagination.total_pages > 1">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                                <span id="categoriesPaginationSummary"
                                    class="small text-body-secondary"
                                    aria-live="polite">
                                    {{ paginationSummary }}
                                </span>

                                <nav class="tz-pagination"
                                    aria-label="صفحه‌بندی دسته‌بندی‌های فعال">

                                    <ul id="categoriesPaginationList"
                                        class="pagination pagination-sm mb-0">

                                        <!-- Previous page -->
                                        <li class="page-item"
                                            :class="{ disabled: !canGoPrevious }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="!canGoPrevious"
                                                    @click="changePage(pagination.current_page - 1)">
                                                قبلی
                                            </button>
                                        </li>

                                        <!-- Page numbers -->
                                        <li v-for="page in paginationPages"
                                            :key="page"
                                            class="page-item"
                                            :class="{ active: page === pagination.current_page }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="loading"
                                                    :aria-current="page === pagination.current_page ? 'page' : null"
                                                    @click="changePage(page)">
                                                {{ formatInteger(page) }}
                                            </button>
                                        </li>

                                        <!-- Next page -->
                                        <li class="page-item"
                                            :class="{ disabled: !canGoNext }">

                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="!canGoNext"
                                                    @click="changePage(pagination.current_page + 1)">
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
                <div id="deletedCategoriesPanel"
                     v-show="showDeleted"
                     data-component="deleted-categories">

                    <section class="card dashboard-card mb-4"
                             aria-labelledby="deletedCategoriesHeading">

                        <div class="card-header bg-body border-bottom py-3 px-3 px-md-4">
                            <h3 id="deletedCategoriesHeading"
                                class="h6 fw-bold mb-1">
                                <i class="fa-regular fa-trash-can text-danger me-2"
                                   aria-hidden="true"></i>
                                دسته‌بندی‌های حذف‌شده
                            </h3>

                            <p class="small text-body-secondary mb-0">
                                بازیابی دسته‌بندی در مهلت مجاز امکان‌پذیر است؛
                                وضعیت دقیق هر مورد از سرور دریافت می‌شود.
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-3">
                                            نام دسته‌بندی
                                        </th>
                                        <th scope="col">نوع</th>
                                        <th scope="col">زمان حذف</th>
                                        <th scope="col">وضعیت بازیابی</th>
                                        <th scope="col" class="text-nowrap pe-3">
                                            عملیات
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="deletedCategoriesTableBody"
                                       aria-live="polite">
                                    <tr v-if="deletedLoading">
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
                                            class="text-center text-danger py-5"
                                            role="alert">
                                            {{ deletedError }}

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger ms-2"
                                                    @click="retryDeleted">
                                                تلاش دوباره
                                            </button>
                                        </td>
                                    </tr>

                                    <tr v-else-if="!hasDeletedCategories">
                                        <td colspan="5"
                                            class="text-center text-body-secondary py-5">
                                            دسته‌بندی حذف‌شده‌ای وجود ندارد.
                                        </td>
                                    </tr>

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

                                        <td :class="category.restore_available
                                            ? 'text-success'
                                            : 'text-warning'">
                                            {{ category.restore_available
                                                ? 'در مهلت بازیابی'
                                                : 'مهلت بازیابی تمام شده' }}
                                        </td>

                                        <td class="text-nowrap pe-3">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success me-1"
                                                    @click="restoreCategory(category)"
                                                    :disabled="!category.restore_available
                                                        || deletedLoading
                                                        || restoringId !== null">
                                                {{ restoringId === category.id
                                                    ? 'در حال بازیابی...'
                                                    : 'بازیابی' }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="deletedCategoriesPagination"
                             class="card-footer bg-body"
                             data-component="pagination"
                             v-if="deletedPagination.total_pages > 1">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <span id="deletedCategoriesPaginationSummary"
                                      class="small text-body-secondary"
                                      aria-live="polite">
                                    {{ deletedPaginationSummary }}
                                </span>

                                <nav class="tz-pagination"
                                     aria-label="صفحه‌بندی دسته‌بندی‌های حذف‌شده">
                                    <ul id="deletedCategoriesPaginationList"
                                        class="pagination pagination-sm mb-0">
                                        <li class="page-item"
                                            :class="{ disabled: !canGoPreviousDeleted }">
                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="!canGoPreviousDeleted"
                                                    @click="changeDeletedPage(
                                                        deletedPagination.current_page - 1
                                                    )">
                                                قبلی
                                            </button>
                                        </li>

                                        <li v-for="page in deletedPaginationPages"
                                            :key="page"
                                            class="page-item"
                                            :class="{
                                                active: page === deletedPagination.current_page
                                            }">
                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="deletedLoading"
                                                    :aria-current="page === deletedPagination.current_page
                                                        ? 'page'
                                                        : null"
                                                    @click="changeDeletedPage(page)">
                                                {{ formatInteger(page) }}
                                            </button>
                                        </li>

                                        <li class="page-item"
                                            :class="{ disabled: !canGoNextDeleted }">
                                            <button type="button"
                                                    class="page-link"
                                                    :disabled="!canGoNextDeleted"
                                                    @click="changeDeletedPage(
                                                        deletedPagination.current_page + 1
                                                    )">
                                                بعدی
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </section>
                </div>


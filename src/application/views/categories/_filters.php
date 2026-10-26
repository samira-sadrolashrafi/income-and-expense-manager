                <!-- Search, filters and sorting -->
                <section class="card dashboard-card mb-3"
                         aria-labelledby="categoryFiltersHeading"
                         data-component="category-filters">

                    <div class="card-body p-3 p-md-4">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <h3 id="categoryFiltersHeading"
                                class="h6 fw-bold mb-0">
                                <i class="fa-solid fa-filter text-primary me-2"
                                   aria-hidden="true"></i>
                                جست‌وجو و فیلتر
                            </h3>
                        </div>

                        <div class="row g-3 align-items-end">

                            <!-- Category name search -->
                            <div class="col-12 col-md-6 col-xl-4">
                                <label for="categoriesSearch"
                                       class="form-label small fw-medium">
                                    جست‌وجو در نام دسته‌بندی
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary"
                                          aria-hidden="true">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>

                                    <input type="search"
                                           id="categoriesSearch"
                                           v-model.trim="filters.search"
                                           class="form-control"
                                           placeholder="نام دسته‌بندی را وارد کنید"
                                           autocomplete="off"
                                           aria-describedby="categoriesFiltersNote">
                                </div>
                            </div>

                            <!-- Category type filter -->
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="categoriesTypeFilter"
                                       class="form-label small fw-medium">
                                    نوع دسته‌بندی
                                </label>

                                <select id="categoriesTypeFilter"
                                        v-model="filters.type"
                                        class="form-select"
                                        aria-describedby="categoriesFiltersNote">
                                    <option value="all">همه انواع</option>
                                    <option value="income">درآمد</option>
                                    <option value="expense">هزینه</option>
                                </select>
                            </div>

                            <!-- Sorting dropdown -->
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="categoriesSortFilter"
                                       class="form-label small fw-medium">
                                    مرتب‌سازی
                                </label>

                                <select id="categoriesSortFilter"
                                        v-model="filters.sort"
                                        class="form-select"
                                        aria-describedby="categoriesFiltersNote">
                                    <option value="newest" selected>
                                        جدیدترین
                                    </option>
                                    <option value="oldest">
                                        قدیمی‌ترین
                                    </option>
                                </select>
                            </div>

                            <!-- Apply and clear buttons -->
                            <div class="col-12 col-xl-4">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <button type="button"
                                            id="applyCategoriesFiltersButton"
                                            class="btn btn-primary"
                                            @click="applyFilters">
                                        <i class="fa-solid fa-filter me-1"
                                           aria-hidden="true"></i>
                                        اعمال فیلترها
                                    </button>

                                    <button type="button"
                                            id="clearCategoriesFiltersButton"
                                            class="btn btn-outline-secondary"
                                            @click="clearFilters">
                                        <i class="fa-solid fa-rotate-left me-1"
                                           aria-hidden="true"></i>
                                        پاک کردن فیلترها
                                    </button>
                                </div>
                            </div>

                        </div>

                        <p id="categoriesFiltersNote"
                           class="form-text mb-0 mt-3">
                            پس از تنظیم فیلترها، روی «اعمال فیلترها» کلیک کنید.
                            برای بازگشت به فهرست کامل، از «پاک کردن فیلترها» استفاده کنید.
                        </p>

                    </div>
                </section>

                <!-- Active and deleted category tabs -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="btn-group"
                         role="group"
                         aria-label="وضعیت دسته‌بندی‌ها">

                        <button type="button"
                                id="showActiveCategoriesButton"
                                class="btn"
                                :class="showDeleted
                                    ? 'btn-outline-primary'
                                    : 'btn-primary'"
                                aria-controls="activeCategoriesPanel"
                                :aria-pressed="showDeleted ? 'false' : 'true'"
                                @click="showActiveTab()">
                            <i class="fa-solid fa-list-check me-1"
                               aria-hidden="true"></i>
                            فعال
                        </button>

                        <button type="button"
                                id="showDeletedCategoriesButton"
                                class="btn"
                                :class="showDeleted
                                    ? 'btn-primary'
                                    : 'btn-outline-primary'"
                                aria-controls="deletedCategoriesPanel"
                                :aria-pressed="showDeleted ? 'true' : 'false'"
                                @click="showDeletedTab()">
                            <i class="fa-regular fa-trash-can me-1"
                               aria-hidden="true"></i>
                            حذف‌شده
                        </button>

                    </div>

                    <span class="small text-body-secondary d-inline-flex align-items-center gap-2">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        دسته‌بندی‌های حذف‌شده تا ۳۰ روز امکان بازیابی دارند.
                    </span>
                </div>


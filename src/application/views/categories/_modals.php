            <!-- Add/edit category modal -->
            <div class="modal fade tz-modal"
                 id="categoryModal"
                 tabindex="-1"
                 aria-labelledby="categoryModalTitle"
                 aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <form id="categoryForm"
                              novalidate
                              @submit.prevent="saveCategory">
                            <div class="modal-header">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-tags text-primary"
                                       aria-hidden="true"></i>

                                    <h2 class="modal-title fs-5 fw-semibold mb-0"
                                        id="categoryModalTitle">
                                        {{ form.id === null
                                            ? 'افزودن دسته‌بندی'
                                            : 'ویرایش دسته‌بندی' }}
                                    </h2>
                                </div>

                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="بستن"></button>
                            </div>

                            <div class="modal-body p-3 p-md-4">
                                <p class="small text-body-secondary mb-3">
                                    برای مرتب‌سازی درآمدها و هزینه‌ها، یک نام و نوع مشخص کنید.
                                </p>

                                <div v-if="formMessage.text"
                                     id="categoryFormMessage"
                                     class="alert"
                                     :class="'alert-' + formMessage.type"
                                     :key="formMessage.seq"
                                     role="alert"
                                     aria-live="polite">{{ formMessage.text }}</div>

                                <div class="mb-3">
                                    <label for="categoryName"
                                           class="form-label fw-medium">
                                        نام دسته‌بندی
                                        <span class="text-danger"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           :class="{ 'is-invalid': formErrors.name }"
                                           id="categoryName"
                                           name="name"
                                           v-model="form.name"
                                           @input="onFormInput('name')"
                                           maxlength="100"
                                           required
                                           autocomplete="off"
                                           placeholder="مثلاً خوراکی، حمل‌ونقل یا حقوق"
                                           aria-describedby="categoryNameError categoryNameHelp">

                                    <div id="categoryNameError"
                                         class="invalid-feedback">{{ formErrors.name }}</div>

                                    <div id="categoryNameHelp"
                                         class="form-text">
                                        نام باید بین ۱ تا ۱۰۰ نویسه باشد.
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label for="categoryType"
                                           class="form-label fw-medium">
                                        نوع دسته‌بندی
                                        <span class="text-danger"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <select class="form-select"
                                            :class="{ 'is-invalid': formErrors.type }"
                                            id="categoryType"
                                            name="type"
                                            v-model="form.type"
                                            @change="onFormInput('type')"
                                            required
                                            aria-describedby="categoryTypeError">
                                        <option value="">
                                            نوع را انتخاب کنید
                                        </option>
                                        <option value="income">درآمد</option>
                                        <option value="expense">هزینه</option>
                                    </select>

                                    <div id="categoryTypeError"
                                         class="invalid-feedback">{{ formErrors.type }}</div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                    انصراف
                                </button>

                                <button type="submit"
                                        id="saveCategoryButton"
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
                                    aria-label="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-2">
                                آیا از حذف دسته‌بندی
                                «<span id="deleteCategoryName"
                                       class="fw-semibold">{{ deleteTarget
                                           ? deleteTarget.name
                                           : '' }}</span>»
                                اطمینان دارید؟
                            </p>

                            <div class="alert alert-warning small mb-0"
                                 role="note">
                                <i class="fa-solid fa-triangle-exclamation me-1"
                                   aria-hidden="true"></i>
                                تراکنش‌های فعال مرتبط نیز حذف نرم می‌شوند و اثر مالی آن‌ها از موجودی برگردانده می‌شود.
                                بازیابی فقط در مهلت مجاز امکان‌پذیر است.
                            </div>

                            <div v-if="deleteMessage.text"
                                 id="deleteCategoryMessage"
                                 class="alert mt-3 mb-0"
                                 :class="'alert-' + deleteMessage.type"
                                 :key="deleteMessage.seq"
                                 role="alert"
                                 aria-live="polite">{{ deleteMessage.text }}</div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">
                                انصراف
                            </button>

                            <button type="button"
                                    id="confirmDeleteCategoryButton"
                                    class="btn btn-danger"
                                    :disabled="deleting"
                                    @click="confirmDelete">
                                {{ deleting ? 'در حال حذف...' : 'حذف دسته‌بندی' }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>


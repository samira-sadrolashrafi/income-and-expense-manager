
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="modal fade tz-modal" data-component="transaction-form-modal"
     id="<?= html_escape($form_prefix) ?>Modal" tabindex="-1"
     aria-labelledby="<?= html_escape($form_prefix) ?>ModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="<?= html_escape($form_prefix) ?>Form" novalidate autocomplete="off">
                <div class="modal-header px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary p-2" aria-hidden="true">
                            <i class="fa-solid fa-receipt"></i>
                        </span>
                        <h2 class="modal-title fs-5 fw-semibold" id="<?= html_escape($form_prefix) ?>ModalTitle">افزودن تراکنش</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>

                <div class="modal-body p-4">
                    <div id="<?= html_escape($form_prefix) ?>FormMessage" class="alert d-none" role="alert" aria-live="polite"></div>
                    <div id="<?= html_escape($form_prefix) ?>CategoriesWarning" class="alert alert-warning d-none" role="alert" aria-live="polite"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="<?= html_escape($form_prefix) ?>Title" class="form-label fw-medium">
                                <i class="fa-solid fa-pen-to-square text-body-secondary me-1" aria-hidden="true"></i>عنوان
                            </label>
                            <input type="text" class="form-control" id="<?= html_escape($form_prefix) ?>Title"
                                   name="title" maxlength="150" required autocomplete="off"
                                   aria-describedby="<?= html_escape($form_prefix) ?>TitleError">
                            <div id="<?= html_escape($form_prefix) ?>TitleError" class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="<?= html_escape($form_prefix) ?>Type" class="form-label fw-medium">
                                <i class="fa-solid fa-arrow-right-arrow-left text-body-secondary me-1" aria-hidden="true"></i>نوع تراکنش
                            </label>
                            <select class="form-select" id="<?= html_escape($form_prefix) ?>Type" required
                                    aria-describedby="<?= html_escape($form_prefix) ?>TypeHelp <?= html_escape($form_prefix) ?>TypeError">
                                <option value="">نوع را انتخاب کنید</option>
                                <option value="income">درآمد</option>
                                <option value="expense">هزینه</option>
                            </select>
                            <div id="<?= html_escape($form_prefix) ?>TypeHelp" class="form-text">ابتدا نوع تراکنش را انتخاب کنید؛ سپس دسته‌بندی را انتخاب کنید.</div>
                            <div id="<?= html_escape($form_prefix) ?>TypeError" class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="<?= html_escape($form_prefix) ?>Amount" class="form-label fw-medium">
                                <i class="fa-solid fa-money-bill-wave text-body-secondary me-1" aria-hidden="true"></i>مبلغ
                            </label>
                            <div class="input-group has-validation" dir="ltr">
                                <span class="input-group-text">تومان</span>
                                <input type="text" class="form-control text-start" id="<?= html_escape($form_prefix) ?>Amount"
                                       name="amount" inputmode="decimal" maxlength="20" autocomplete="off" required
                                       aria-describedby="<?= html_escape($form_prefix) ?>AmountHelp <?= html_escape($form_prefix) ?>AmountError">
                                <div id="<?= html_escape($form_prefix) ?>AmountError" class="invalid-feedback text-end" dir="rtl"></div>
                            </div>
                            <div id="<?= html_escape($form_prefix) ?>AmountHelp" class="form-text">مبلغ مثبت است؛ برای اعشار از نقطه استفاده کنید (حداکثر دو رقم).</div>
                        </div>

                        <div class="col-md-6">
                            <label for="<?= html_escape($form_prefix) ?>Category" class="form-label fw-medium">
                                <i class="fa-solid fa-tags text-body-secondary me-1" aria-hidden="true"></i>دسته‌بندی
                            </label>
                            <select class="form-select" id="<?= html_escape($form_prefix) ?>Category" name="category_id" required
                                    aria-describedby="<?= html_escape($form_prefix) ?>CategoryError">
                                <option value="">ابتدا نوع تراکنش را انتخاب کنید</option>
                            </select>
                            <div id="<?= html_escape($form_prefix) ?>CategoryError" class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="<?= html_escape($form_prefix) ?>Date" class="form-label fw-medium">
                                <i class="fa-solid fa-calendar-days text-body-secondary me-1" aria-hidden="true"></i>تاریخ
                            </label>
                            <input type="date" class="form-control" id="<?= html_escape($form_prefix) ?>Date"
                                   name="transaction_date" required aria-describedby="<?= html_escape($form_prefix) ?>DateError">
                            <div id="<?= html_escape($form_prefix) ?>DateError" class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <label for="<?= html_escape($form_prefix) ?>Description" class="form-label fw-medium">
                                <i class="fa-solid fa-align-right text-body-secondary me-1" aria-hidden="true"></i>توضیحات
                                <span class="text-secondary">(اختیاری)</span>
                            </label>
                            <textarea class="form-control" id="<?= html_escape($form_prefix) ?>Description"
                                      name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" id="save<?= ucfirst(html_escape($form_prefix)) ?>Button" class="btn btn-primary px-4">ذخیره</button>
                </div>
            </form>
        </div>
    </div>
</div>

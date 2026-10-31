<div
    class="modal fade tz-modal"
    id="recoveryModal"
    tabindex="-1"
    aria-labelledby="recoveryModalTitle"
    aria-describedby="recoveryModalDescription"
    aria-hidden="true"
    data-question-url="<?php echo html_escape(site_url('auth/recovery/question')); ?>"
    data-verify-url="<?php echo html_escape(site_url('auth/recovery/verify')); ?>"
    data-reset-url="<?php echo html_escape(site_url('auth/recovery/reset')); ?>"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header p-3 p-sm-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center rounded-3 p-3" aria-hidden="true">
                        <i class="fa-solid fa-key fs-5"></i>
                    </span>
                    <div>
                        <h2 class="modal-title h5 fw-bold mb-1" id="recoveryModalTitle">فراموشی رمز عبور</h2>
                        <p class="text-body-secondary small mb-0" id="recoveryModalDescription">
                            بازیابی حساب با سؤال امنیتی
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="بستن پنجره"></button>
            </div>

            <div class="modal-body p-3 p-sm-4">
                <div class="alert d-none" id="recoveryMessage" role="alert" aria-live="polite"></div>

                <form id="recoveryQuestionForm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">مرحله ۱ از ۳</span>
                        <span class="fw-semibold">شناسایی حساب</span>
                    </div>
                    <p class="text-body-secondary small mb-4">
                        نام کاربری خودت را وارد کن تا سؤال امنیتی حسابت نمایش داده شود.
                    </p>
                    <div class="mb-4">
                        <label for="recoveryUsername" class="form-label fw-semibold">نام کاربری</label>
                        <input
                            type="text"
                            class="form-control"
                            name="username"
                            id="recoveryUsername"
                            minlength="3"
                            maxlength="100"
                            autocomplete="username"
                            autocapitalize="none"
                            spellcheck="false"
                            dir="ltr"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        نمایش سؤال امنیتی
                    </button>
                </form>

                <form id="recoveryVerifyForm" class="d-none">
                    <input type="hidden" name="username" id="recoveryVerifyUsername">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">مرحله ۲ از ۳</span>
                        <span class="fw-semibold">تأیید پاسخ امنیتی</span>
                    </div>

                    <div class="rounded-3 bg-body-tertiary border p-3 mb-4">
                        <div class="text-body-secondary small mb-2">سؤال امنیتی حساب شما</div>
                        <p id="recoveryQuestionLabel" class="fw-semibold mb-0"></p>
                    </div>

                    <div class="mb-4">
                        <label for="recoveryAnswer" class="form-label fw-semibold">پاسخ امنیتی</label>
                        <input
                            type="password"
                            class="form-control"
                            name="answer"
                            id="recoveryAnswer"
                            maxlength="256"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        بررسی پاسخ
                    </button>
                </form>

                <form id="recoveryResetForm" class="d-none">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">مرحله ۳ از ۳</span>
                        <span class="fw-semibold">تعیین رمز جدید</span>
                    </div>

                    <p class="text-body-secondary small mb-4">
                        پاسخ امنیتی تأیید شده است. رمز عبور جدید خود را وارد کن.
                    </p>

                    <div class="mb-3">
                        <label for="recoveryNewPassword" class="form-label fw-semibold">رمز عبور جدید</label>
                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            id="recoveryNewPassword"
                            minlength="8"
                            maxlength="1024"
                            autocomplete="new-password"
                            dir="ltr"
                            required
                        >
                        <div class="form-text">
                            حداقل ۸ کاراکتر؛ شامل حروف بزرگ و کوچک انگلیسی، عدد و یک علامت.
                        </div>
                        <div id="recovery_password_err" class="text-danger small mt-1"></div>
                    </div>

                    <div class="mb-4">
                        <label for="recoveryConfirmPassword" class="form-label fw-semibold">تکرار رمز عبور جدید</label>
                        <input
                            type="password"
                            class="form-control"
                            name="password_confirm"
                            id="recoveryConfirmPassword"
                            maxlength="1024"
                            autocomplete="new-password"
                            dir="ltr"
                            required
                        >
                        <div id="recovery_confirm_err" class="text-danger small mt-1"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        تغییر رمز عبور
                    </button>
                </form>
            </div>

            <div class="modal-footer justify-content-between gap-2 p-3">
                <button type="button" class="btn btn-link text-decoration-none d-none" id="recoveryStartOver">
                    شروع دوباره
                </button>
                <button type="button" class="btn btn-outline-secondary ms-auto" data-bs-dismiss="modal">
                    بستن
                </button>
            </div>
        </div>
    </div>
</div>

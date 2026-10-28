<?php $this->load->view('layouts/header', array(
    'page_title' => 'پروفایل',
    'page_styles' => array(base_url('assets/css/dashboard.css'))
)); ?>

<div id="profileApp">
    <div class="container-fluid dashboard-shell px-0">
        <div class="row g-0">
            <?php $this->load->view('layouts/app_sidebar', array('active_page' => 'profile')); ?>

            <main class="col-lg-9 col-xl-10 dashboard-main" id="profileMain">
                <?php $this->load->view('layouts/app_header', array(
                    'app_page_title' => 'پروفایل',
                    'app_page_description' => 'اطلاعات حساب خود را مدیریت کنید.',
                    'app_username' => $username
                )); ?>

                <div class="px-3 px-xl-4 py-4">
                    <h2 class="h5 fw-bold mb-4">تنظیمات پروفایل</h2>

                    <form id="profileForm" method="post" enctype="multipart/form-data">
                        <section class="card dashboard-card mb-3">
                            <div class="card-body p-3 p-md-4">
                                <h3 class="h6 fw-bold mb-4" id="profileAccountHeading">اطلاعات حساب کاربری</h3>

                                <div class="row g-4">
                                    <div class="col-12 col-xl-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="profileFirstName" class="form-label">نام</label>
                                                <input id="profileFirstName" name="first_name" class="form-control" maxlength="100" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileLastName" class="form-label">نام خانوادگی</label>
                                                <input id="profileLastName" name="last_name" class="form-control" maxlength="100" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileUsername" class="form-label">نام کاربری</label>
                                                <input id="profileUsername" name="username" class="form-control" dir="ltr" maxlength="100" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileEmail" class="form-label">ایمیل</label>
                                                <input id="profileEmail" name="email" type="email" class="form-control" dir="ltr" maxlength="191" required>
                                            </div>

                                            <div class="col-12">
                                                <label for="profileRecoveryQuestion" class="form-label">سؤال بازیابی حساب</label>
                                                <select id="profileRecoveryQuestion" name="question_id"
                                                        class="form-select">
                                                    <option value="">انتخاب سؤال بازیابی</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label for="profileRecoveryAnswer" class="form-label">پاسخ جدید سؤال بازیابی</label>
                                                <input id="profileRecoveryAnswer" name="recovery_answer" type="password"
                                                       class="form-control"
                                                       placeholder="فقط برای تغییر پاسخ وارد کنید">
                                                <div class="form-text">تغییر سؤال یا پاسخ به رمز عبور فعلی نیاز دارد.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-xl-3 text-center">
                                        <img id="profileAvatarImage"
                                             class="tz-profile-avatar shadow-sm mb-3 d-none" width="96" height="96" alt="عکس پروفایل">

                                        <span id="profileAvatarFallback"
                                              class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3"
                                              style="width:96px;height:96px">
                                            <i class="fa-solid fa-user fa-2xl"></i>
                                        </span>

                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#profileAvatarModal">
                                                تغییر عکس
                                            </button>
                                            <button type="button" id="profileRemoveAvatar"
                                                    class="btn btn-outline-danger btn-sm">
                                                حذف عکس
                                            </button>
                                        </div>
                                        <p class="small text-body-secondary mt-2">JPG، PNG یا WebP تا ۲ مگابایت</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="card dashboard-card mb-3">
                            <div class="card-body p-3 p-md-4">
                                <h3 class="h6 fw-bold mb-4" id="profilePasswordHeading">تغییر رمز عبور</h3>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="profileCurrentPassword" class="form-label">رمز فعلی</label>
                                        <div class="input-group">
                                            <input id="profileCurrentPassword" name="current_password" class="form-control"
                                                   type="password">
                                            <button type="button" class="btn btn-outline-secondary">نمایش</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="profileNewPassword" class="form-label">رمز جدید</label>
                                        <div class="input-group">
                                            <input id="profileNewPassword" name="new_password" class="form-control"
                                                   type="password">
                                            <button type="button" class="btn btn-outline-secondary">نمایش</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="profileConfirmPassword" class="form-label">تکرار رمز جدید</label>
                                        <div class="input-group">
                                            <input id="profileConfirmPassword" name="confirm_password" class="form-control"
                                                   type="password">
                                            <button type="button" class="btn btn-outline-secondary">نمایش</button>
                                        </div>
                                    </div>
                                </div>
                                <p class="form-text mt-3 mb-0">رمز جدید حداقل ۸ کاراکتر و شامل حروف بزرگ و کوچک انگلیسی، عدد و علامت ویژه باشد.</p>
                            </div>
                        </section>

                        <button id="profileSaveButton" type="button" class="btn btn-primary w-100 btn-lg">ذخیره تغییرات</button>
                        <div id="profileMessage" class="alert alert-danger mt-3 d-none" role="alert"></div>
                    </form>

                    <section class="card border-danger-subtle mt-4">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <h3 class="h6 fw-bold text-danger" id="deleteAccountHeading">حذف حساب کاربری</h3>
                                <p class="small text-body-secondary mb-0">اطلاعات مالی باقی می‌مانند و تا ۳۰ روز امکان بازیابی حساب وجود دارد.</p>
                            </div>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteAccountModal">حذف حساب کاربری</button>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <!-- انتخاب تصویر -->
    <div class="modal fade" id="profileAvatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h6">تغییر عکس پروفایل</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <input id="profileAvatarInput" name="avatar" form="profileForm" type="file" class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <p class="form-text mt-2">پس از انتخاب عکس، روی «ذخیره تغییرات» بزنید.</p>
                    <div id="profileAvatarError" class="alert alert-danger mt-2 d-none" role="alert"></div>
                    <div id="profileAvatarPreviewNotice" class="alert alert-success mt-2 d-none" role="status"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>

    <!-- حذف حساب -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteAccountForm" method="post">
                    <div class="modal-header">
                        <h3 class="modal-title h6 text-danger">تأیید حذف حساب</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                    </div>
                    <div class="modal-body">
                        <p>برای حذف حساب، رمز فعلی را وارد کنید.</p>
                        <p class="alert alert-warning small">فقط تا ۳۰ روز امکان بازیابی حساب دارید.</p>
                        <label for="deleteAccountPassword" class="form-label">رمز عبور فعلی</label>
                        <input id="deleteAccountPassword" name="current_password" type="password"
                               class="form-control" required>
                        <div class="form-check mt-3">
                            <input id="deleteAccountConfirmation" name="confirm_deletion" class="form-check-input" type="checkbox" required>
                            <label for="deleteAccountConfirmation" class="form-check-label">حذف حسابم را تأیید می‌کنم.</label>
                        </div>
                        <div id="deleteAccountMessage" class="alert alert-danger mt-3 d-none" role="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="button" id="deleteAccountSubmitButton" class="btn btn-danger">
                            تأیید و حذف حساب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/logout_modal'); ?>

<?php $this->load->view('layouts/footer'); ?>

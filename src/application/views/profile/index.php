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
                    'app_username' => $username,
                    'app_use_vue' => true
                )); ?>

                <div class="px-3 px-xl-4 py-4">
                    <h2 class="h5 fw-bold mb-4">تنظیمات پروفایل</h2>

                    <form id="profileForm" method="post" enctype="multipart/form-data"
                          novalidate @submit.prevent="saveProfile">
                        <section class="card dashboard-card mb-3">
                            <div class="card-body p-3 p-md-4">
                                <h3 class="h6 fw-bold mb-4" id="profileAccountHeading">اطلاعات حساب کاربری</h3>

                                <div class="row g-4">
                                    <div class="col-12 col-xl-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="profileFirstName" class="form-label">نام</label>
                                                <input id="profileFirstName" name="first_name" v-model="firstName"
                                                       class="form-control">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileLastName" class="form-label">نام خانوادگی</label>
                                                <input id="profileLastName" name="last_name" v-model="lastName"
                                                       class="form-control">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileUsername" class="form-label">نام کاربری</label>
                                                <input id="profileUsername" name="username" v-model="username"
                                                       class="form-control" dir="ltr">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="profileEmail" class="form-label">ایمیل</label>
                                                <input id="profileEmail" name="email" type="email" v-model="email"
                                                       class="form-control" dir="ltr">
                                            </div>

                                            <div class="col-12">
                                                <label for="profileRecoveryQuestion" class="form-label">سؤال بازیابی حساب</label>
                                                <select id="profileRecoveryQuestion" name="question_id" v-model="questionId"
                                                        class="form-select">
                                                    <option value="">انتخاب سؤال بازیابی</option>
                                                    <option v-for="(question, id) in questions" :key="id" :value="String(id)">
                                                        {{ question }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label for="profileRecoveryAnswer" class="form-label">پاسخ جدید سؤال بازیابی</label>
                                                <input id="profileRecoveryAnswer" name="recovery_answer" type="password"
                                                       v-model="recoveryAnswer" class="form-control"
                                                       placeholder="فقط برای تغییر پاسخ وارد کنید">
                                                <div class="form-text">تغییر سؤال یا پاسخ به رمز عبور فعلی نیاز دارد.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-xl-3 text-center">
                                        <img v-if="avatarUrl" id="profileAvatarImage" :src="avatarUrl"
                                             class="tz-profile-avatar shadow-sm mb-3" width="96" height="96" alt="عکس پروفایل">

                                        <span v-else id="profileAvatarFallback"
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
                                                    class="btn btn-outline-danger btn-sm" @click="removeAvatar">
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
                                            <input id="profileCurrentPassword" name="current_password"
                                                   v-model="currentPassword" class="form-control"
                                                   :type="showCurrentPassword ? 'text' : 'password'">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    @click="showCurrentPassword = !showCurrentPassword"
                                                    :aria-pressed="showCurrentPassword">
                                                {{ showCurrentPassword ? 'پنهان کردن' : 'نمایش' }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="profileNewPassword" class="form-label">رمز جدید</label>
                                        <div class="input-group">
                                            <input id="profileNewPassword" name="new_password" v-model="newPassword"
                                                   class="form-control" :type="showNewPassword ? 'text' : 'password'">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    @click="showNewPassword = !showNewPassword"
                                                    :aria-pressed="showNewPassword">
                                                {{ showNewPassword ? 'پنهان کردن' : 'نمایش' }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="profileConfirmPassword" class="form-label">تکرار رمز جدید</label>
                                        <div class="input-group">
                                            <input id="profileConfirmPassword" name="confirm_password" v-model="confirmPassword"
                                                   class="form-control" :type="showConfirmPassword ? 'text' : 'password'">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    @click="showConfirmPassword = !showConfirmPassword"
                                                    :aria-pressed="showConfirmPassword">
                                                {{ showConfirmPassword ? 'پنهان کردن' : 'نمایش' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="form-text mt-3 mb-0">رمز جدید حداقل ۸ کاراکتر و شامل حروف بزرگ و کوچک انگلیسی، عدد و علامت ویژه باشد.</p>
                            </div>
                        </section>

                        <button id="profileSaveButton" type="submit" class="btn btn-primary w-100 btn-lg"
                                :disabled="!profileLoaded || saving">
                            {{ saving ? 'در حال ذخیره...' : 'ذخیره تغییرات' }}
                        </button>

                        <div v-if="profileMessage" :class="'alert alert-' + profileMessageType + ' mt-3'"
                             role="alert">
                            {{ profileMessage }}
                        </div>
                    </form>

                    <section class="card border-danger-subtle mt-4">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <h3 class="h6 fw-bold text-danger" id="deleteAccountHeading">حذف حساب کاربری</h3>
                                <p class="small text-body-secondary mb-0">اطلاعات مالی باقی می‌مانند و تا ۳۰ روز امکان بازیابی حساب وجود دارد.</p>
                            </div>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteAccountModal" @click="resetDeleteAccount">
                                حذف حساب کاربری
                            </button>
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
                    <input :key="avatarInputKey" id="profileAvatarInput" name="avatar" form="profileForm"
                           type="file" class="form-control" accept="image/jpeg,image/png,image/webp"
                           @change="selectAvatar">
                    <p class="form-text mt-2">پس از انتخاب عکس، روی «ذخیره تغییرات» بزنید.</p>
                    <div v-if="avatarNotice" id="profileAvatarPreviewNotice"
                         class="alert alert-success mt-2" role="status">
                        {{ avatarNotice }}
                    </div>
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
                <form id="deleteAccountForm" method="post" novalidate @submit.prevent="deleteAccount">
                    <div class="modal-header">
                        <h3 class="modal-title h6 text-danger">تأیید حذف حساب</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="بستن" @click="resetDeleteAccount"></button>
                    </div>
                    <div class="modal-body">
                        <p>برای حذف حساب، رمز فعلی را وارد کنید.</p>
                        <p class="alert alert-warning small">فقط تا ۳۰ روز امکان بازیابی حساب دارید.</p>

                        <label for="deleteAccountPassword" class="form-label">رمز عبور فعلی</label>
                        <input id="deleteAccountPassword" name="current_password" type="password"
                               v-model="deletePassword" class="form-control"
                               @input="deleteAccountMessage = ''">

                        <div class="form-check mt-3">
                            <input id="deleteAccountConfirmation" name="confirm_deletion" value="1"
                                   v-model="confirmDeletion" class="form-check-input" type="checkbox"
                                   @change="deleteAccountMessage = ''">
                            <label for="deleteAccountConfirmation" class="form-check-label">حذف حسابم را تأیید می‌کنم.</label>
                        </div>

                        <div v-if="deleteAccountMessage"
                             :class="'alert alert-' + deleteAccountMessageType + ' mt-3'"
                             role="alert">
                            {{ deleteAccountMessage }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                @click="resetDeleteAccount">انصراف</button>
                        <button type="submit" id="deleteAccountSubmitButton" class="btn btn-danger"
                                :disabled="deletingAccount">
                            {{ deletingAccount ? 'در حال ارسال درخواست...' : 'تأیید و حذف حساب' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/logout_modal'); ?>

<script>
    Vue.createApp({
        data(){
            return{
                firstName: '',
                lastName: '',
                username: <?= json_encode($username) ?>,
                email: '',
                questionId: '',
                recoveryAnswer: '',
                currentPassword: '',
                newPassword: '',
                confirmPassword: '',

                questions: {},
                initialQuestion: '',
                profileLoaded: false,
                saving: false,
                profileMessage: '',
                profileMessageType: 'danger',

                avatarUrl: <?= json_encode(
                    $this->session->userdata('avatar')
                        ? base_url('uploads/avatars/' . basename($this->session->userdata('avatar')))
                        : ''
                ) ?>,
                avatarFile: null,
                avatarPreviewUrl: '',
                avatarRemoved: false,
                avatarNotice: '',
                avatarInputKey: 0,

                showCurrentPassword: false,
                showNewPassword: false,
                showConfirmPassword: false,

                deletePassword: '',
                confirmDeletion: false,
                deleteAccountMessage: '',
                deleteAccountMessageType: 'danger',
                deletingAccount: false
            }
        },

        methods:{
            async loadProfile(){
                try{
                    const response = await fetch("<?= site_url('api/profile') ?>");
                    const result = await response.json();

                    if(!result.success){
                        this.profileMessage = result.message || "دریافت اطلاعات پروفایل انجام نشد. ";
                        return;
                    }

                    const profile = result.profile;

                    this.firstName = profile.first_name || '';
                    this.lastName = profile.last_name || '';
                    this.username = profile.username || '';
                    this.email = profile.email || '';
                    this.questionId = profile.question_id || '';
                    this.initialQuestion = this.questionId;
                    this.questions = result.questions || {};
                    this.avatarUrl = profile.avatar_url || '';

                    this.profileLoaded = true;
                    this.profileMessage = '';

                }catch(error){
                    this.profileMessage = "دریافت اطلاعات پروفایل انجام نشد، دوباره تلاش کنید. ";
                }
            },

            selectAvatar(event){
                const file = event.target.files[0];

                if(!file){
                    return;
                }

                this.releaseAvatarPreview();
                this.avatarFile = file;
                this.avatarPreviewUrl = window.URL.createObjectURL(file); // این دستور یک آدرس موقت برای فایل انتخاب شده ایجاد می کند، 
                //این آدرس به مرورگر اجازه می دهد عکس را بدون آپلود کردن روی سرور نمایش دهد. 
                this.avatarRemoved = false;
                this.avatarUrl = this.avatarPreviewUrl;
                this.avatarNotice = 'پیش‌نمایش عکس آماده است؛ برای ثبت روی «ذخیره تغییرات» بزنید.';
            },

            releaseAvatarPreview() {
                if (this.avatarPreviewUrl) {
                    window.URL.revokeObjectURL(this.avatarPreviewUrl);
                    this.avatarPreviewUrl = '';
                }
            },

            removeAvatar() {
                this.releaseAvatarPreview();
                this.avatarFile = null;
                this.avatarRemoved = true;
                this.avatarUrl = '';
                this.avatarNotice = 'حذف عکس انتخاب شد؛ هنوز روی سرور ذخیره نشده است.';
                this.avatarInputKey++;
            },

            async saveProfile(){
                if(!this.profileLoaded || this.saving){
                    return;
                }

                this.profileMessage = '';

                const changingPassword = Boolean(this.newPassword || this.confirmPassword);
                const recoveryChanged = this.questionId !== this.initialQuestion || Boolean(this.recoveryAnswer);

                const formData = new FormData();

                formData.append('first_name', this.firstName);
                formData.append('last_name', this.lastName);
                formData.append('username', this.username);
                formData.append('email', this.email);

                if(changingPassword){
                    formData.append('new_password', this.newPassword);
                    formData.append('confirm_password', this.confirmPassword);
                }

                if(recoveryChanged){
                    formData.append('question_id', this.questionId);
                    formData.append('recovery_answer', this.recoveryAnswer);
                }

                if (changingPassword || recoveryChanged) {
                    formData.append('current_password', this.currentPassword);
                }

                if (this.avatarFile) {
                    formData.append('avatar', this.avatarFile);
                }

                if (this.avatarRemoved) {
                    formData.append('remove_avatar', '1');
                }

                this.saving = true;

                try{
                    const response = await fetch("<?= site_url('api/profile') ?>",{
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (!result.success) {
                        this.profileMessage = result.message || 'ذخیره تغییرات انجام نشد.';
                        this.profileMessageType = 'danger';
                        return;
                    }

                    if (recoveryChanged) {
                        this.initialQuestion = this.questionId;
                    }

                    this.currentPassword = '';
                    this.newPassword = '';
                    this.confirmPassword = '';
                    this.recoveryAnswer = '';

                    this.releaseAvatarPreview();
                    this.avatarFile = null;
                    this.avatarRemoved = false;
                    this.avatarUrl = result.profile.avatar_url || '';
                    this.avatarNotice = '';
                    this.avatarInputKey++;

                    this.username = result.profile.username || this.username;
                    this.profileMessage = result.message || 'تغییرات پروفایل ذخیره شد.';
                    this.profileMessageType = 'success';
                } catch (error) {
                    this.profileMessage = 'ذخیره تغییرات انجام نشد، دوباره تلاش کنید.';
                    this.profileMessageType = 'danger';
                } finally {
                    this.saving = false;
                }
            },

            async deleteAccount(){
                if(this.deletingAccount){
                    return;
                }

                const formData = new FormData();
                formData.append('current_password', this.deletePassword);
                formData.append('confirm_deletion', this.confirmDeletion ? '1' : '0');

                this.deletingAccount = true;
                this.deleteAccountMessage = '';

                try{
                    const response = await fetch("<?= site_url('api/profile/delete-account') ?>", {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (!result.success) {
                        this.deleteAccountMessage = result.message || 'حذف حساب انجام نشد.';
                        this.deleteAccountMessageType = 'danger';
                        return;
                    }
                    window.location.assign("<?= site_url('login') ?>");
                }catch(error){
                    this.deleteAccountMessage = 'حذف حساب انجام نشد، دوباره تلاش کنید.';
                    this.deleteAccountMessageType = 'danger';
                }finally{
                    this.deletingAccount = false;
                }
            },
            
            resetDeleteAccount() {
                this.deletePassword = '';
                this.confirmDeletion = false;
                this.deleteAccountMessage = '';
                this.deleteAccountMessageType = 'danger';
            }
        },

        mounted(){
            this.loadProfile();
        }

    }).mount('#profileApp')
</script>

<?php $this->load->view('layouts/footer'); ?>

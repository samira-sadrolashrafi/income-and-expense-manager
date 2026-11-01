
<?php
$this->load->view('layouts/header', array(
    'page_title' => 'ورود به حساب کاربری',
    'page_styles' => array(base_url('assets/css/dashboard.css'))
));
?>

<div id="loginApp">
    <main class="container-fluid bg-body-tertiary min-vh-100 d-flex align-items-center py-4 py-lg-5" id="loginPage">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11 col-xl-10 col-xxl-9">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                        <div class="row g-0">

                            <div class="col-12 col-lg-6">
                                <section class="card-body p-4 p-md-5" aria-labelledby="loginPageTitle">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <svg class="tz-brand-logo" viewBox="0 0 48 48" width="48" height="48"
                                             aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                            <defs>
                                                <linearGradient id="tzMarkLogin" x1="0" y1="0" x2="1" y2="1">
                                                    <stop offset="0" stop-color="#132D56"/>
                                                    <stop offset="1" stop-color="#174B96"/>
                                                </linearGradient>
                                            </defs>
                                            <rect x="1" y="1" width="46" height="46" rx="12" fill="url(#tzMarkLogin)"/>
                                            <rect x="7" y="9" width="18" height="7" rx="2.4" fill="#DAECFF"/>
                                            <rect x="7" y="21" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                                            <rect x="17" y="21" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                                            <rect x="7" y="31" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                                            <rect x="17" y="31" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                                            <rect x="27" y="28" width="5" height="10" rx="2" fill="#42A5F5"/>
                                            <rect x="34" y="21" width="5" height="17" rx="2" fill="#55C5ED"/>
                                            <rect x="41" y="14" width="4" height="24" rx="2" fill="#41D4B4"/>
                                        </svg>

                                        <div>
                                            <p class="fw-bold fs-4 lh-sm mb-0">ترازینو</p>
                                            <small class="text-body-secondary">مدیریت مالی شخصی</small>
                                        </div>
                                    </div>

                                    <h1 class="h3 fw-bold mb-2" id="loginPageTitle">ورود به حساب کاربری</h1>
                                    <p class="text-body-secondary mb-4">نام کاربری و رمز عبورت رو وارد کن.</p>

                                    <div v-if="message" id="loginMessage" class="alert alert-danger"
                                         role="alert" aria-live="polite">
                                         {{message}}
                                    </div>

                                    <form id="loginForm" @submit.prevent="login" novalidate>
                                        <div class="mb-3">
                                            <label for="username" class="form-label fw-medium">نام کاربری</label>
                                            <div class="input-group">
                                                <span class="input-group-text" aria-hidden="true">
                                                    <i class="fa-solid fa-user"></i>
                                                </span>
                                                <input type="text" id="username" name="username"
                                                       class="form-control" placeholder="نام کاربری"
                                                       autocomplete="username" maxlength="100"
                                                       v-model="username"
                                                       aria-describedby="username_err" required>
                                            </div>
                                            <div id="username_err" class="text-danger small mt-1" aria-live="polite">
                                                {{errors.username_err}}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                                <label for="password" class="form-label fw-medium mb-0">رمز عبور</label>
                                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"
                                                        data-bs-toggle="modal" data-bs-target="#recoveryModal">
                                                    فراموشی رمز؟
                                                </button>
                                            </div>

                                            <div class="input-group">
                                                <span class="input-group-text" aria-hidden="true">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                                <input type="password" id="password" name="password"
                                                       class="form-control" placeholder="رمز عبور"
                                                       autocomplete="current-password" dir="ltr"
                                                       v-model="password"
                                                       aria-describedby="password_err" required>
                                            </div>
                                            <div id="password_err" class="text-danger small mt-1" aria-live="polite">
                                                {{errors.password_err}}
                                            </div>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input type="checkbox" class="form-check-input"
                                                    v-model="remember"
                                                   id="remember" name="remember" value="1" >
                                            <label class="form-check-label" for="remember">مرا به خاطر بسپار</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                            ورود به حساب
                                        </button>
                                    </form>

                                    <div v-if="showRestore" class="alert alert-warning mt-3" role="alert">
                                        <p class="mb-2">
                                            این حساب حذف شده است، اما هنوز امکان بازیابی آن وجود دارد.
                                        </p>
                                        <div v-if="restoreMessage" class="text-danger small mb-2">
                                            {{restoreMessage}}
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" @click="restoreAccount">
                                            بازیابی حساب و ورود
                                        </button>
                                    </div>

                                    <div class="text-center mt-4 pt-3 border-top">
                                        <span class="text-body-secondary">حساب کاربری نداری؟</span>
                                        <a class="fw-semibold text-decoration-none ms-1" href="/register">ثبت‌نام کن</a>
                                    </div>
                                </section>
                            </div>

                            <div class="col-lg-6 d-none d-lg-flex dashboard-sidebar-wrap">
                                <aside class="text-white p-4 p-xl-5 d-flex flex-column justify-content-center w-100"
                                       aria-label="معرفی ترازینو">
                                    <div class="mb-4">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-4
                                                     border border-white border-opacity-25 p-3 mb-4">
                                            <i class="fa-solid fa-chart-line fa-2x" aria-hidden="true"></i>
                                        </span>
                                        <h2 class="h3 fw-bold lh-base mb-3">مدیریت مالی، ساده و روشن</h2>
                                        <p class="text-white-50 lh-lg mb-0">
                                            درآمدها، هزینه‌ها و موجودی حسابت رو در یک فضای منظم مدیریت کن.
                                        </p>
                                    </div>

                                    <div class="d-flex flex-column gap-3 mt-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-arrow-right-arrow-left fa-fw" aria-hidden="true"></i>
                                            <span>ثبت و پیگیری تراکنش‌ها</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-tags fa-fw" aria-hidden="true"></i>
                                            <span>مدیریت دسته‌بندی‌های مالی</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-chart-pie fa-fw" aria-hidden="true"></i>
                                            <span>نمای کلی وضعیت مالی</span>
                                        </div>
                                    </div>
                                </aside>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $this->load->view('auth/recovery_modal'); ?>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>
    Vue.createApp({
        data() {
            return {
                username: '',
                password: '',
                remember: false,
                message: '',
                errors: {},
                showRestore: false,
                restoreMessage: ''
            }
        },
        methods: {
            async login(){
                this.message = '';
                this.errors={};
                this.showRestore = false;
                this.restoreMessage = '';

                // const formData = new FormData();

                // formData.append('username',this.username);
                // formData.append('password',this.password);
                // formData.append('remember',this.remember ? '1' : '0');

                try {
                    const response = await fetch('/auth/login', {
                        method: 'POST',
                        body: JSON.stringify({ username: this.username ,password: this.password })
                    });

                    const result = await response.json();

                    if(result.success){
                        window.location.href = '/dashboard';
                    }else if(result.account_deleted && result.recovery_available){
                        this.showRestore = true;
                    } else{
                        this.message = result.message;
                        this.errors = result.errors || {};
                    }
                } catch(error){
                    this.message = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید."
                }
            },

            async restoreAccount(){
                this.restoreMessage = '';

                const formData = new FormData();

                formData.append('username',this.username);
                formData.append('password',this.password);
                formData.append('remember',this.remember ? '1' : '0');

                try{
                    const response = await fetch('/auth/account/restore',{
                    method: 'POST',
                    body: formData
                    });

                    const result = await response.json();

                    if(result.success){
                        window.location.href = '/dashboard';
                    }else{
                        this.restoreMessage = result.message;
                    }
                } catch(error){
                    this.restoreMessage = "ارتباط با سرور برقرار نشد، دوباره تلاش کنید.";
                }
            }
        }
    }).mount('#loginApp');
</script>


<?php $this->load->view('layouts/footer'); ?>

<script src="<?php echo base_url('assets/js/auth-request.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/recovery.js'); ?>"></script>
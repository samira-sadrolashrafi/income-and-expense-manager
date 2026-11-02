<div id="recoveryApp">

    <div
        class="modal fade tz-modal"
        id="recoveryModal"
        tabindex="-1"
        aria-labelledby="recoveryModalTitle"
        aria-describedby="recoveryModalDescription"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow">

                <div class="modal-header p-3 p-sm-4">

                    <div class="d-flex align-items-center gap-3">

                        <span class="bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center rounded-3 p-3">
                            <i class="fa-solid fa-key fs-5"></i>
                        </span>

                        <div>
                            <h2 class="modal-title h5 fw-bold mb-1" id="recoveryModalTitle">
                                فراموشی رمز عبور
                            </h2>

                            <p class="text-body-secondary small mb-0" id="recoveryModalDescription">
                                بازیابی حساب با سؤال امنیتی
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close ms-2"
                        data-bs-dismiss="modal"
                        aria-label="بستن پنجره">
                    </button>

                </div>


                <div class="modal-body p-3 p-sm-4">

                    <!-- پیام‌ها -->
                    <div
                        v-if="recoveryMessage"
                        class="alert"
                        :class="recoveryMessageType"
                        id="recoveryMessage"
                        role="alert"
                    >
                        {{ recoveryMessage }}
                    </div>

                    <!-- مرحله اول: شناسایی حساب -->
                    <form
                        id="recoveryQuestionForm"
                        v-if="recoveryStep === 1"
                        @submit.prevent="recoveryQuestion"
                    >

                        <div class="d-flex align-items-center gap-2 mb-3">

                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                مرحله ۱ از ۳
                            </span>

                            <span class="fw-semibold">
                                شناسایی حساب
                            </span>

                        </div>

                        <p class="text-body-secondary small mb-4">
                            نام کاربری خودت را وارد کن تا سؤال امنیتی حسابت نمایش داده شود.
                        </p>

                        <div class="mb-4">

                            <label for="recoveryUsername" class="form-label fw-semibold">
                                نام کاربری
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="recoveryUsername"
                                v-model="recoveryUsername"
                                minlength="3"
                                maxlength="100"
                                autocomplete="username"
                                autocapitalize="none"
                                spellcheck="false"
                                dir="ltr"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100 py-2 fw-semibold"
                        >
                            نمایش سؤال امنیتی
                        </button>

                    </form>


                    <!-- مرحله دوم: تأیید پاسخ امنیتی -->
                    <form
                        id="recoveryVerifyForm"
                        v-if="recoveryStep === 2"
                        @submit.prevent="recoveryVerify"
                    >

                        <div class="d-flex align-items-center gap-2 mb-3">

                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                مرحله ۲ از ۳
                            </span>

                            <span class="fw-semibold">
                                تأیید پاسخ امنیتی
                            </span>

                        </div>

                        <div class="rounded-3 bg-body-tertiary border p-3 mb-4">

                            <div class="text-body-secondary small mb-2">
                                سؤال امنیتی حساب شما
                            </div>

                            <p class="fw-semibold mb-0">
                                {{ recoveryQuestionText }}
                            </p>

                        </div>

                        <div class="mb-4">

                            <label for="recoveryAnswer" class="form-label fw-semibold">
                                پاسخ امنیتی
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="recoveryAnswer"
                                v-model="recoveryAnswer"
                                maxlength="256"
                                autocomplete="off"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100 py-2 fw-semibold"
                        >
                            بررسی پاسخ
                        </button>

                    </form>


                    <!-- مرحله سوم: تعیین رمز جدید -->
                    <form
                        v-if="recoveryStep === 3"
                        @submit.prevent="recoveryReset"
                    >

                        <div class="d-flex align-items-center gap-2 mb-3">

                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                مرحله ۳ از ۳
                            </span>

                            <span class="fw-semibold">
                                تعیین رمز جدید
                            </span>

                        </div>

                        <p class="text-body-secondary small mb-4">
                            پاسخ امنیتی تأیید شده است. رمز عبور جدید خود را وارد کن.
                        </p>


                        <div class="mb-3">

                            <label for="recoveryNewPassword" class="form-label fw-semibold">
                                رمز عبور جدید
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="recoveryNewPassword"
                                v-model="recoveryPassword"
                                minlength="8"
                                maxlength="1024"
                                autocomplete="new-password"
                                dir="ltr"
                                required
                            >

                            <div class="form-text">
                                حداقل ۸ کاراکتر؛ شامل حروف بزرگ و کوچک انگلیسی، عدد و یک علامت.
                            </div>

                            <div class="text-danger small mt-1">
                                {{ recoveryErrors.recovery_password_err }}
                            </div>

                        </div>


                        <div class="mb-4">

                            <label for="recoveryConfirmPassword" class="form-label fw-semibold">
                                تکرار رمز عبور جدید
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="recoveryConfirmPassword"
                                v-model="recoveryPasswordConfirm"
                                maxlength="1024"
                                autocomplete="new-password"
                                dir="ltr"
                                required
                            >

                            <div class="text-danger small mt-1">
                                {{ recoveryErrors.recovery_confirm_err }}
                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100 py-2 fw-semibold"
                        >
                            تغییر رمز عبور
                        </button>

                    </form>

                </div>


                <div class="modal-footer justify-content-between gap-2 p-3">

                    <button
                        type="button"
                        class="btn btn-link text-decoration-none"
                        v-if="recoveryStep !== 1"
                        @click="resetRecovery"
                    >
                        شروع دوباره
                    </button>

                    <button
                        type="button"
                        class="btn btn-outline-secondary ms-auto"
                        data-bs-dismiss="modal"
                    >
                        بستن
                    </button>

                </div>

            </div>
        </div>
    </div>

</div>


<script>
    Vue.createApp({
        data(){
            return{
                recoveryStep: 1,
                recoveryUsername: '',
                recoveryAnswer: '',
                recoveryQuestionText: '',
                recoveryPassword: '',
                recoveryPasswordConfirm: '',
                recoveryMessage: '',
                recoveryMessageType: 'alert-danger',
                recoveryErrors: {}
            };
        },
        methods: {
            async recoveryQuestion(){
                this.recoveryMessage = '';
                this.recoveryMessageType = 'alert-danger';

                try{
                    const response = await fetch('/auth/recovery/question',{
                        method: 'POST',
                        body: JSON.stringify({username:this.recoveryUsername})
                    });

                    const result = await response.json();

                    if(result.success){
                        this.recoveryQuestionText = result.question;
                        this.recoveryStep = 2;
                    } else{
                        this.recoveryMessage = result.message;
                    }
                }catch(error){
                    this.recoveryMessage = "ارتباط با سرور برقرار نشد. ";
                }
            },

            async recoveryVerify(){
                this.recoveryMessage = '';
                this.recoveryMessageType = 'alert-danger';

                try{
                    const response = await fetch('/auth/recovery/verify', {
                        method: 'POST',
                        body: JSON.stringify({answer:this.recoveryAnswer})
                    });

                    const result = await response.json();

                    if(result.success){
                        this.recoveryStep = 3;
                        this.recoveryMessage = result.message;
                        this.recoveryMessageType = 'alert-success'
                    } else{
                        this.recoveryMessage = result.message;
                    }
                }catch(error){
                    this.recoveryMessage = "ارتباط با سرور برقرار نشد. "
                }
            },

            async recoveryReset(){
                this.recoveryMessage = '';
                this.recoveryMessageType = 'alert-danger';
                this.recoveryErrors = {};

                try{
                    const response = await fetch('/auth/recovery/reset', {
                        method: 'POST',
                        body: JSON.stringify({password:this.recoveryPassword , password_confirm:this.recoveryPasswordConfirm})
                    });

                    const result = await response.json();

                    if(result.success){
                        this.resetRecovery();

                        this.recoveryMessage = result.message;
                        this.recoveryMessageType = 'alert-success';
                    } else{
                        this.recoveryMessage = result.message;
                        this.recoveryErrors = result.errors || {};
                    }
                }catch(error){
                    this.recoveryMessage = 'ارتباط با سرور برقرار نشد. ';
                }
            },

            resetRecovery() {
                this.recoveryStep = 1;
                this.recoveryUsername = '';
                this.recoveryAnswer = '';
                this.recoveryQuestionText = '';
                this.recoveryPassword = '';
                this.recoveryPasswordConfirm = '';
                this.recoveryMessage = '';
                this.recoveryMessageType = 'alert-danger';
                this.recoveryErrors = {};
            }
        }
    }).mount('#recoveryApp');

</script>
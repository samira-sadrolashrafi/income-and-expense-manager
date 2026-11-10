<div
    class="modal fade tz-modal"
    id="logoutConfirmModal"
    tabindex="-1"
    aria-labelledby="logoutConfirmTitle"
    aria-describedby="logoutConfirmDescription"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h2 class="modal-title fs-5 fw-bold" id="logoutConfirmTitle">
                    تأیید خروج از حساب
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="بستن پنجره"
                ></button>
            </div>

            <div class="modal-body px-4 py-3">

                <div class="d-flex align-items-start gap-3">
                    <span
                        class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-3 bg-danger-subtle text-danger p-3 fs-4"
                        aria-hidden="true"
                    >
                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                    </span>

                    <div class="pt-1">
                        <p class="fw-semibold mb-2" id="logoutConfirmDescription">
                            آیا مطمئنی می‌خواهی از حساب کاربری خارج شوی؟
                        </p>

                        <p class="small text-body-secondary mb-0">
                            برای استفاده دوباره از ترازینو باید وارد حساب خود شوی.
                        </p>
                    </div>
                </div>

                <div
                    v-if="logoutMessage"
                    id="logoutMessage"
                    class="alert alert-danger mt-3 mb-0"
                    role="alert"
                    aria-live="polite"
                >
                    {{ logoutMessage }}
                </div>

            </div>

            <div class="modal-footer border-0 px-4 pt-0 pb-4 gap-2">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                    :disabled="loggingOut"
                >
                    انصراف
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    :disabled="loggingOut"
                    @click="logout"
                >
                    {{ loggingOut ? 'در حال خروج...' : 'بله، خارج شو' }}
                </button>

            </div>

        </div>
    </div>
</div>

<script>
    const logoutModal = document.getElementById('logoutConfirmModal');

    const logoutApp = Vue.createApp({
        data() {
            return {
                loggingOut: false,
                logoutMessage: ''
            };
        },
        methods: {
            async logout() {
                if (this.loggingOut) {
                    return;
                }

                this.loggingOut = true;
                this.logoutMessage = '';

                try {
                    const response = await fetch("<?= site_url('auth/logout') ?>", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result && result.success === true) {
                        window.location.href = "<?= site_url('login') ?>";
                        return;
                    }

                    this.logoutMessage = result && result.message
                        ? result.message
                        : 'خروج از حساب انجام نشد، دوباره تلاش کنید.';

                } catch (error) {
                    this.logoutMessage = 'ارتباط با سرور برقرار نشد، دوباره تلاش کنید.';
                } finally {
                    this.loggingOut = false;
                }
            }
        }
    }).mount(logoutModal);

    logoutModal.addEventListener('show.bs.modal', function () {
        logoutApp.logoutMessage = '';
    });

    const mobileNav = document.getElementById('dashboardMobileNav');

    if (mobileNav) {
        const mobileLogoutButton = mobileNav.querySelector('[data-sidebar-logout]');

        if (mobileLogoutButton) {
            mobileLogoutButton.addEventListener('click', function () {
                mobileNav.dataset.openLogout = 'true';
            });

            mobileNav.addEventListener('hidden.bs.offcanvas', function () {
                if (mobileNav.dataset.openLogout !== 'true') {
                    return;
                }

                mobileNav.dataset.openLogout = 'false';
                bootstrap.Modal.getOrCreateInstance(logoutModal).show();
            });
        }
    }
</script>

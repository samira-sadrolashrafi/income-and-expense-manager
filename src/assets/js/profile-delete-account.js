/** TARAZINO | Delete-account modal. */
(function (window, document) {
    'use strict';

    const byId = id => document.getElementById(id);

    function initialize() {
        const root = byId('profileMain');
        const form = byId('deleteAccountForm');
        const modalElement = byId('deleteAccountModal');

        if (!root || !form || !modalElement) return;

        const endpoint = (root.dataset.deleteAccountApiUrl || '').trim();
        const loginUrl = (root.dataset.loginUrl || '').trim();
        const password = byId('deleteAccountPassword');
        const confirmation = byId('deleteAccountConfirmation');
        const submitButton = byId('deleteAccountSubmitButton');
        const messageBox = byId('deleteAccountMessage');
        const passwordFeedback = byId('deleteAccountPasswordFeedback');

        if (!password || !confirmation || !submitButton || !messageBox) return;

        let submitting = false;

        function showMessage(message, type) {
            messageBox.textContent = message || '';
            messageBox.className = message
                ? 'alert alert-' + (type || 'danger') + ' mt-3 mb-0'
                : 'alert d-none mt-3 mb-0';
        }

        function showPasswordError(message) {
            password.classList.toggle('is-invalid', Boolean(message));

            if (passwordFeedback) {
                passwordFeedback.textContent = message || '';
                passwordFeedback.classList.toggle('d-none', !message);
            }
        }

        password.addEventListener('input', function () {
            showPasswordError('');
            showMessage('');
        });

        confirmation.addEventListener('change', function () {
            showMessage('');
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (submitting) return;

            if (!password.value) {
                showPasswordError('برای حذف حساب، رمز فعلی را وارد کنید.');
                password.focus();
                return;
            }

            showPasswordError('');

            if (!confirmation.checked) {
                showMessage('برای ادامه، تأیید حذف حساب را علامت بزنید.');
                confirmation.focus();
                return;
            }

            if (!endpoint) {
                showMessage('آدرس API حذف حساب مشخص نشده است.');
                return;
            }

            if (!window.TarazinoApi || typeof window.TarazinoApi.post !== 'function') {
                showMessage('فایل api.js بارگذاری نشده است. صفحه را تازه‌سازی کنید.');
                return;
            }

            submitting = true;
            submitButton.disabled = true;
            submitButton.textContent = 'در حال ارسال درخواست...';

            try {
                const result = await window.TarazinoApi.post(endpoint, new FormData(form));
                showMessage(result.message || 'حساب کاربری حذف شد.', 'success');

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                }

                window.location.assign(loginUrl || '/login');
            } catch (error) {
                showMessage(error.message || 'حذف حساب انجام نشد. دوباره تلاش کنید.');
            } finally {
                submitting = false;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fa-regular fa-trash-can me-1" aria-hidden="true"></i> تأیید و حذف حساب';
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            form.reset();
            password.classList.remove('is-invalid');
            showPasswordError('');
            showMessage('');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})(window, document);
(function (window, document) {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('recoveryModal');
        if (!modal) return;

        const questionForm = document.getElementById('recoveryQuestionForm');
        const verifyForm = document.getElementById('recoveryVerifyForm');
        const resetForm = document.getElementById('recoveryResetForm');
        const messageBox = document.getElementById('recoveryMessage');
        const startOver = document.getElementById('recoveryStartOver');

        function showMessage(message, type) {
            messageBox.textContent = message;
            messageBox.className = message
                ? 'alert alert-' + (type || 'danger')
                : 'alert d-none';
        }

        function showStep(step) {
            questionForm.classList.toggle('d-none', step !== 1);
            verifyForm.classList.toggle('d-none', step !== 2);
            resetForm.classList.toggle('d-none', step !== 3);
            startOver.classList.toggle('d-none', step === 1);
        }

        function resetForms() {
            questionForm.reset();
            verifyForm.reset();
            resetForm.reset();

            document.getElementById('recoveryQuestionLabel').textContent = '';
            document.getElementById('recovery_password_err').textContent = '';
            document.getElementById('recovery_confirm_err').textContent = '';

            showMessage('');
            showStep(1);
        }

        questionForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = questionForm.querySelector('button[type="submit"]');
            button.disabled = true;
            showMessage('');

            try {
                const response = await window.TarazinoAuthRequest.post(
                    modal.dataset.questionUrl,
                    questionForm
                );

                if (!response.ok) {
                    showMessage(response.result.message || 'دریافت سؤال انجام نشد.');
                    return;
                }

                document.getElementById('recoveryQuestionLabel').textContent = response.result.question;

                document.getElementById('recoveryVerifyUsername').value =
                    document.getElementById('recoveryUsername').value.trim();

                showStep(2);
            } catch (error) {
                showMessage(error.message || 'درخواست انجام نشد.');
            } finally {
                button.disabled = false;
            }
        });

        verifyForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = verifyForm.querySelector('button[type="submit"]');
            button.disabled = true;
            showMessage('');

            try {
                const response = await window.TarazinoAuthRequest.post(
                    modal.dataset.verifyUrl,
                    verifyForm
                );

                if (!response.ok) {
                    showMessage(response.result.message || 'پاسخ امنیتی صحیح نیست.');
                    return;
                }

                showStep(3);
                showMessage('پاسخ تأیید شد. رمز عبور جدید را وارد کنید.', 'success');
            } catch (error) {
                showMessage(error.message || 'درخواست انجام نشد.');
            } finally {
                button.disabled = false;
            }
        });

        resetForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = resetForm.querySelector('button[type="submit"]');
            const password = document.getElementById('recoveryNewPassword').value;
            const confirmation = document.getElementById('recoveryConfirmPassword').value;

            document.getElementById('recovery_password_err').textContent = '';
            document.getElementById('recovery_confirm_err').textContent = '';

            if (password !== confirmation) {
                document.getElementById('recovery_confirm_err').textContent =
                    'تکرار رمز عبور مطابقت ندارد.';
                return;
            }

            button.disabled = true;
            showMessage('');

            try {
                const response = await window.TarazinoAuthRequest.post(
                    modal.dataset.resetUrl,
                    resetForm
                );

                if (!response.ok) {
                    const errors = response.result.errors || {};

                    document.getElementById('recovery_password_err').textContent =
                        errors.recovery_password_err || '';
                    document.getElementById('recovery_confirm_err').textContent =
                        errors.recovery_confirm_err || '';

                    showMessage(response.result.message || 'تغییر رمز عبور انجام نشد.');
                    return;
                }

                resetForms();
                showMessage('رمز عبور تغییر کرد. اکنون با رمز جدید وارد شوید.', 'success');
            } catch (error) {
                showMessage(error.message || 'درخواست انجام نشد.');
            } finally {
                button.disabled = false;
            }
        });

        startOver.addEventListener('click', resetForms);
        modal.addEventListener('hidden.bs.modal', resetForms);

        modal.addEventListener('show.bs.modal', function () {
            resetForms();

            const loginUsername = document.getElementById('username');

            if (loginUsername && loginUsername.value.trim()) {
                document.getElementById('recoveryUsername').value = loginUsername.value.trim();
            }
        });

        showStep(1);
    });
})(window, document);
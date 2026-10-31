/** TARAZINO | Profile page UI helpers. */
(function (window, document) {
    'use strict';

    const byId = id => document.getElementById(id);

    function initialize() {
        if (!byId('profileMain')) return null;

        const input = byId('profileAvatarInput');
        const image = byId('profileAvatarImage');
        const fallback = byId('profileAvatarFallback');
        const removeButton = byId('profileRemoveAvatar');
        const errorBox = byId('profileAvatarError');
        const noticeBox = byId('profileAvatarPreviewNotice');

        if (!input || !image || !fallback || !removeButton || !errorBox || !noticeBox) {
            return null;
        }

        let previewUrl = null;
        let removed = false;

        function showAvatarMessage(message, isError) {
            const target = isError ? errorBox : noticeBox;
            const other = isError ? noticeBox : errorBox;

            other.classList.add('d-none');
            target.textContent = message || '';
            target.classList.toggle('d-none', !message);
        }

        function showAvatar(url) {
            const hasImage = Boolean(url);

            image.classList.toggle('d-none', !hasImage);
            fallback.classList.toggle('d-none', hasImage);

            if (hasImage) {
                image.src = url;
            } else {
                image.removeAttribute('src');
            }
        }

        function releasePreview() {
            if (previewUrl) {
                window.URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function selectedFile() {
            return input.files && input.files.length ? input.files[0] : null;
        }

        function validateAvatar(file) {
            if (!file) return '';

            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                return 'فرمت عکس باید JPG، PNG یا WebP باشد.';
            }

            if (file.size < 1 || file.size > 2 * 1024 * 1024) {
                return 'حجم عکس باید بیشتر از صفر و حداکثر ۲ مگابایت باشد.';
            }

            return '';
        }

        input.addEventListener('change', function () {
            const file = selectedFile();
            const error = validateAvatar(file);

            if (error) {
                input.value = '';
                showAvatarMessage(error, true);
                return;
            }

            if (!file) return;

            releasePreview();
            previewUrl = window.URL.createObjectURL(file);
            removed = false;
            showAvatar(previewUrl);
            showAvatarMessage('پیش‌نمایش عکس آماده است؛ برای ثبت روی «ذخیره تغییرات» بزنید.', false);
        });

        removeButton.addEventListener('click', function () {
            input.value = '';
            releasePreview();
            removed = true;
            showAvatar('');
            showAvatarMessage('حذف عکس انتخاب شد؛ هنوز روی سرور ذخیره نشده است.', false);
        });

        document.querySelectorAll('[data-profile-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const field = byId(button.dataset.profilePasswordToggle);
                if (!field) return;

                const showPassword = field.type === 'password';
                field.type = showPassword ? 'text' : 'password';
                button.textContent = showPassword ? 'پنهان کردن' : 'نمایش';
                button.setAttribute('aria-pressed', String(showPassword));
                button.setAttribute('aria-label', showPassword ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
            });
        });

        window.addEventListener('pagehide', releasePreview);

        return Object.freeze({
            getAvatar: function () {
                return {
                    file: selectedFile(),
                    removed: removed
                };
            },
            validateAvatar: validateAvatar,
            saved: function (avatarUrl) {
                removed = false;
                input.value = '';
                releasePreview();
                showAvatarMessage('', false);

                if (typeof avatarUrl === 'string') {
                    showAvatar(avatarUrl);
                }
            }
        });
    }

    window.TarazinoProfileUI = Object.freeze({ initialize: initialize });
})(window, document);
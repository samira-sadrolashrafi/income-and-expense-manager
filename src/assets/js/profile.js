/** TARAZINO | Load and save profile information. */
(function (w, d) {
    'use strict';

    const $ = id => d.getElementById(id);

    function initialize() {
        const root = $('profileMain');
        const form = $('profileForm');
        const button = $('profileSaveButton');

        if (!root || !form || !button) return;

        const ui = w.TarazinoProfileUI && w.TarazinoProfileUI.initialize();
        const endpoint = (root.dataset.profileApiUrl || '').trim();

        if (!ui) return;

        let initialQuestion = '';
        let loaded = false;
        let saving = false;

        function message(text, type = 'danger') {
            const box = $('profileMessage');
            box.textContent = text || '';
            box.className = text ? 'alert alert-' + type + ' mt-3' : 'alert d-none mt-3';
        }

        function fieldError(id, problem) {
            const field = $(id);
            if (!field) return;

            field.classList.toggle('is-invalid', Boolean(problem));

            const errorId = id + 'Feedback';
            let feedback = $(errorId);

            if (!feedback && problem) {
                feedback = d.createElement('div');
                feedback.id = errorId;
                feedback.className = 'invalid-feedback d-block';
                (field.closest('.input-group') || field).after(feedback);
            }

            if (feedback) {
                feedback.textContent = problem || '';
                feedback.classList.toggle('d-none', !problem);
            }
        }

        function validPersonName(name) {
            return [...name].length <= 100 && /\p{L}/u.test(name)
                && /^[\p{L}\p{M}\p{Zs}\u200c\u0027\u2019-]+$/u.test(name);
        }

        function loadQuestions(questions, selectedId) {
            const select = $('profileRecoveryQuestion');
            select.innerHTML = '<option value="">انتخاب سؤال بازیابی</option>';

            Object.entries(questions || {}).forEach(([id, question]) => {
                const option = d.createElement('option');
                option.value = id;
                option.textContent = question;
                select.appendChild(option);
            });

            select.value = selectedId || '';
            initialQuestion = selectedId || '';
        }

        async function loadProfile() {
            button.disabled = true;

            if (!endpoint || !w.TarazinoApi || typeof w.TarazinoApi.get !== 'function') {
                message('اطلاعات لازم برای دریافت پروفایل موجود نیست. صفحه را تازه‌سازی کنید.');
                return;
            }

            try {
                const result = await w.TarazinoApi.get(endpoint);
                const profile = result.profile;

                if (!profile) throw new Error('اطلاعات پروفایل دریافت نشد.');

                $('profileFirstName').value = profile.first_name || '';
                $('profileLastName').value = profile.last_name || '';
                $('profileUsername').value = profile.username || '';
                $('profileEmail').value = profile.email || '';

                loadQuestions(result.questions, profile.question_id);
                ui.saved(profile.avatar_url || '');

                loaded = true;
                button.disabled = false;
                message('');
            } catch (error) {
                message(error.message || 'دریافت اطلاعات پروفایل انجام نشد.');
            }
        }

        function validate() {
            const first = $('profileFirstName').value.trim();
            const last = $('profileLastName').value.trim();
            const username = $('profileUsername').value.trim();
            const email = $('profileEmail').value.trim();
            const current = $('profileCurrentPassword').value;
            const password = $('profileNewPassword').value;
            const confirm = $('profileConfirmPassword').value;
            const question = $('profileRecoveryQuestion').value;
            const answer = $('profileRecoveryAnswer').value.trim();
            const questionChanged = question !== initialQuestion;
            const recoveryChanged = questionChanged || Boolean(answer);
            const changingPassword = Boolean(password || confirm);
            const emailInput = $('profileEmail');
            const avatar = ui.getAvatar();

            const errors = {
                profileFirstName: validPersonName(first) ? '' : 'نام معتبر وارد کنید.',
                profileLastName: validPersonName(last) ? '' : 'نام خانوادگی معتبر وارد کنید.',
                profileUsername: /^[A-Za-z0-9_]{3,100}$/.test(username) ? '' : 'نام کاربری باید ۳ تا ۱۰۰ کاراکتر انگلیسی، عدد یا _ باشد.',
                profileEmail: email && email.length <= 191 && !emailInput.validity.typeMismatch ? '' : 'ایمیل معتبر وارد کنید.',
                profileCurrentPassword: (changingPassword || recoveryChanged) && !current ? 'برای تغییر اطلاعات امنیتی، رمز فعلی الزامی است.' : '',
                profileNewPassword: changingPassword && !/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$!%*?&]).{8,}$/.test(password)
                    ? 'رمز جدید باید حداقل ۸ کاراکتر و شامل حروف بزرگ و کوچک، عدد و علامت ویژه باشد.' : '',
                profileConfirmPassword: changingPassword && password !== confirm ? 'تکرار رمز جدید مطابقت ندارد.' : '',
                profileRecoveryQuestion: recoveryChanged && !question ? 'سؤال بازیابی را انتخاب کنید.' : '',
                profileRecoveryAnswer: recoveryChanged && (new TextEncoder().encode(answer).length < 6 || new TextEncoder().encode(answer).length > 256)
                    ? 'پاسخ جدید باید بین ۶ تا ۲۵۶ بایت باشد.' : ''
            };

            Object.entries(errors).forEach(([id, problem]) => fieldError(id, problem));

            const avatarError = ui.validateAvatar(avatar.file);
            if (avatarError) message(avatarError);
            if (avatarError || Object.values(errors).some(Boolean)) return null;

            const data = new FormData(form);
            data.set('first_name', first);
            data.set('last_name', last);
            data.set('username', username);
            data.set('email', email);

            if (!changingPassword) {
                data.delete('new_password');
                data.delete('confirm_password');
            }

            if (!recoveryChanged) {
                data.delete('question_id');
                data.delete('recovery_answer');
            }

            if (!changingPassword && !recoveryChanged) {
                data.delete('current_password');
            }

            if (avatar.removed) {
                data.delete('avatar');
                data.set('remove_avatar', '1');
            } else if (!avatar.file) {
                data.delete('avatar');
            }

            return { data, question, recoveryChanged };
        }

        form.addEventListener('input', event => {
            if (event.target.id) fieldError(event.target.id, '');
            message('');
        });

        form.addEventListener('change', event => {
            if (event.target.id) fieldError(event.target.id, '');
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();

            if (!loaded || saving) return;

            const draft = validate();
            if (!draft) return;

            if (!w.TarazinoApi || typeof w.TarazinoApi.post !== 'function') {
                message('فایل api.js بارگذاری نشده است.');
                return;
            }

            saving = true;
            button.disabled = true;

            try {
                const result = await w.TarazinoApi.post(endpoint, draft.data);

                if (draft.recoveryChanged) initialQuestion = draft.question;

                ['profileCurrentPassword', 'profileNewPassword', 'profileConfirmPassword', 'profileRecoveryAnswer']
                    .forEach(id => { $(id).value = ''; });

                const avatarUrl = result.profile && typeof result.profile.avatar_url === 'string'
                    ? result.profile.avatar_url : undefined;
                ui.saved(avatarUrl);

                const accountMenu = $('tarazinoAccountMenu');
                if (accountMenu) {
                    const name = accountMenu.querySelector('.fw-semibold');
                    if (name) name.textContent = $('profileUsername').value.trim();

                    const header = accountMenu.parentElement && accountMenu.parentElement.querySelector('.dropdown-header');
                    if (header) header.textContent = $('profileUsername').value.trim();

                    if (typeof avatarUrl === 'string') {
                        const avatar = accountMenu.querySelector('img');
                        const fallback = accountMenu.querySelector('span.rounded-circle');

                        if (avatarUrl && avatar) {
                            avatar.src = avatarUrl;
                            avatar.classList.remove('d-none');
                        } else if (avatarUrl && fallback) {
                            const image = d.createElement('img');
                            image.src = avatarUrl;
                            image.width = 40;
                            image.height = 40;
                            image.alt = 'عکس پروفایل';
                            image.className = 'rounded-circle object-fit-cover flex-shrink-0';
                            fallback.replaceWith(image);
                        } else if (!avatarUrl && avatar) {
                            const badge = d.createElement('span');
                            badge.className = 'd-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle flex-shrink-0';
                            badge.style.width = '40px';
                            badge.style.height = '40px';
                            badge.innerHTML = '<i class="fa-solid fa-user" aria-hidden="true"></i>';
                            avatar.replaceWith(badge);
                        }
                    }
                }

                message(result.message || 'تغییرات با موفقیت ذخیره شدند.', 'success');
            } catch (error) {
                message(error.message || 'ذخیره تغییرات انجام نشد.');
            } finally {
                saving = false;
                button.disabled = false;
            }
        });

        loadProfile();
    }

    if (d.readyState === 'loading') {
        d.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})(window, document);
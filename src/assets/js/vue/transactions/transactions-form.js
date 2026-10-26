'use strict';

{
    const w = window;

    function modal(id) {
        const element = document.getElementById(id);
        return element
            ? w.bootstrap.Modal.getOrCreateInstance(element)
            : null;
    }

    w.TarazinoVueTransactionsForm = {
        methods: {
            /* ---------------------------------------------------------------
             * Shared form modal helpers (the modal markup is also used by the
             * dashboard, so it is driven through its stable element IDs).
             * ------------------------------------------------------------- */

            formField(name) {
                return document.getElementById('transaction' + name);
            },

            formError(name, text) {
                const input = this.formField(name);
                const feedback =
                    document.getElementById('transaction' + name + 'Error');

                if (input) {
                    input.classList.toggle('is-invalid', !!text);
                }

                if (feedback) {
                    feedback.textContent = text || '';
                }
            },

            formNotice(id, text, type) {
                const element = document.getElementById(id);

                if (!element) {
                    return;
                }

                element.textContent = text || '';
                element.className = text
                    ? 'alert alert-' + (type || 'danger')
                    : 'alert d-none';
            },

            refreshFormDate() {
                const element = document.getElementById('transactionModal');

                if (element && w.TarazinoDatepicker) {
                    w.TarazinoDatepicker.refresh(element);
                }
            },

            categoryOptions(selected = '') {
                const select = this.formField('Category');
                const typeField = this.formField('Type');

                if (!select || !typeField) {
                    return;
                }

                const type = typeField.value;

                select.replaceChildren();

                const choices = [
                    {
                        id: '',
                        name: type
                            ? 'دسته‌بندی را انتخاب کنید'
                            : 'ابتدا نوع تراکنش را انتخاب کنید'
                    },
                    ...this.categories.filter(
                        category => category.type === type
                    )
                ];

                choices.forEach(choice => {
                    const option = document.createElement('option');

                    option.value = String(choice.id);
                    option.textContent = choice.name;

                    select.append(option);
                });

                if (
                    Array.from(select.options).some(
                        option => option.value === String(selected)
                    )
                ) {
                    select.value = String(selected);
                }

                const warning = type && choices.length === 1
                    ? 'برای این نوع تراکنش ابتدا دسته‌بندی بسازید.'
                    : '';

                this.formNotice(
                    'transactionCategoriesWarning',
                    warning,
                    'warning'
                );
            },

            resetForm() {
                const form = document.getElementById('transactionForm');

                if (form) {
                    form.reset();
                }

                this.editingId = null;

                const title = document.getElementById('transactionModalTitle');

                if (title) {
                    title.textContent = 'افزودن تراکنش';
                }

                this.formNotice('transactionFormMessage', '');

                ['Title', 'Type', 'Amount', 'Category', 'Date']
                    .forEach(name => this.formError(name));

                this.categoryOptions();
                this.refreshFormDate();
            },

            openCreateModal() {
                if (!this.categoriesLoaded) {
                    this.showMessage(
                        'دسته‌بندی‌ها هنوز دریافت نشده‌اند.',
                        'danger'
                    );

                    return;
                }

                this.resetForm();

                const now = new Date();
                const pad = value => String(value).padStart(2, '0');
                const today = [
                    now.getFullYear(),
                    pad(now.getMonth() + 1),
                    pad(now.getDate())
                ].join('-');

                const dateField = this.formField('Date');

                if (dateField) {
                    dateField.value = today;
                }

                if (!this.categories.length) {
                    this.formNotice(
                        'transactionCategoriesWarning',
                        'ابتدا یک دسته‌بندی فعال ایجاد کنید.',
                        'warning'
                    );
                }

                this.refreshFormDate();

                const instance = modal('transactionModal');

                if (instance) {
                    instance.show();
                }
            },

            openEditModal(tx) {
                this.resetForm();

                this.editingId = String(tx.id);

                const title = document.getElementById('transactionModalTitle');

                if (title) {
                    title.textContent = 'ویرایش تراکنش';
                }

                this.formField('Title').value = tx.title || '';
                this.formField('Amount').value = w.PersianFormat.groupDigits(
                    tx.amount == null ? '' : tx.amount
                );
                this.formField('Date').value = tx.transaction_date || '';
                this.formField('Description').value = tx.description || '';

                const category = this.categoryOf(tx);

                this.formField('Type').value = category ? category.type : '';

                this.categoryOptions(tx.category_id);
                this.refreshFormDate();

                const instance = modal('transactionModal');

                if (instance) {
                    instance.show();
                }
            },

            validDate(value) {
                if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    return false;
                }

                const [year, month, day] = value.split('-').map(Number);
                const check = new Date(Date.UTC(year, month - 1, day));

                return check.getUTCFullYear() === year &&
                    check.getUTCMonth() + 1 === month &&
                    check.getUTCDate() === day;
            },

            buildPayload() {
                const title = this.formField('Title').value.trim();
                const type = this.formField('Type').value;
                const categoryId = this.formField('Category').value;
                const date = this.formField('Date').value;
                const raw = w.PersianFormat
                    .normalizeInputNumber(this.formField('Amount').value)
                    .trim();

                const match = /^(\d+)(?:\.(\d{1,2}))?$/.exec(raw);
                const integer = match
                    ? (match[1].replace(/^0+/, '') || '0')
                    : '';
                const decimals = match ? (match[2] || '') : '';

                const amount = match &&
                    integer.length <= 13 &&
                    /[1-9]/.test(integer + decimals)
                    ? integer + (decimals ? '.' + decimals : '')
                    : null;

                const category = this.categoryOf({ category_id: categoryId });
                const titleLength = [...title].length;

                const issues = {
                    Title: titleLength >= 1 && titleLength <= 150
                        ? ''
                        : 'عنوان باید بین ۱ تا ۱۵۰ نویسه باشد.',
                    Type: ['income', 'expense'].includes(type)
                        ? ''
                        : 'نوع تراکنش را انتخاب کنید.',
                    Category: category && category.type === type
                        ? ''
                        : 'دسته‌بندی مناسب را انتخاب کنید.',
                    Amount: amount
                        ? ''
                        : 'مبلغ مثبت با حداکثر ۱۳ رقم و دو رقم اعشار وارد کنید.',
                    Date: this.validDate(date)
                        ? ''
                        : 'تاریخ معتبر انتخاب کنید.'
                };

                Object.entries(issues)
                    .forEach(([name, text]) => this.formError(name, text));

                if (Object.values(issues).some(Boolean)) {
                    return null;
                }

                const data = new FormData();

                data.append('title', title);
                data.append('category_id', categoryId);
                data.append('amount', amount);
                data.append('transaction_date', date);
                data.append('description',
                    this.formField('Description').value);

                return data;
            },

            async saveTransaction(event) {
                if (event) {
                    event.preventDefault();
                }

                if (this.saving) {
                    return;
                }

                const data = this.buildPayload();

                if (!data) {
                    return;
                }

                const button = document.getElementById('saveTransactionButton');

                this.saving = true;

                if (button) {
                    button.disabled = true;
                }

                try {
                    const url = this.apiBase() + (
                        this.editingId
                            ? '/' + encodeURIComponent(this.editingId) +
                                '/update'
                            : ''
                    );

                    const result = await w.TarazinoApi.post(url, data);

                    const instance = modal('transactionModal');

                    if (instance) {
                        instance.hide();
                    }

                    this.showMessage(
                        result.message || 'تراکنش ذخیره شد.',
                        'success'
                    );

                    try {
                        await this.loadTransactions(
                            this.pagination.current_page
                        );
                    } catch (error) {
                        this.showMessage(
                            'ذخیره شد؛ اما فهرست به‌روز نشد: ' +
                            error.message,
                            'warning'
                        );
                    }
                } catch (error) {
                    this.formNotice(
                        'transactionFormMessage',
                        error.message || 'ذخیره انجام نشد.'
                    );
                } finally {
                    this.saving = false;

                    if (button) {
                        button.disabled = false;
                    }
                }
            },

            /* ---------------------------------------------------------------
             * Amount input guards (Persian digits, one decimal point only)
             * ------------------------------------------------------------- */

            onAmountKeydown(event) {
                if (
                    event.ctrlKey ||
                    event.metaKey ||
                    event.altKey ||
                    event.key.length !== 1
                ) {
                    return;
                }

                if (/^[0-9]$/.test(event.key)) {
                    return;
                }

                if (event.key === '.') {
                    const input = event.currentTarget;
                    const remaining = input.value.slice(0, input.selectionStart) +
                        input.value.slice(input.selectionEnd);
                    const integerBeforeCursor = w.PersianFormat
                        .normalizeInputNumber(
                            input.value.slice(0, input.selectionStart)
                        )
                        .split('.')[0];

                    if (
                        !remaining.includes('.') &&
                        /[0-9]/.test(integerBeforeCursor)
                    ) {
                        return;
                    }
                }

                event.preventDefault();
            },

            onAmountPaste(event) {
                event.preventDefault();

                const input = event.currentTarget;
                const clipboard = event.clipboardData || w.clipboardData;
                const pasted = clipboard ? clipboard.getData('text') : '';
                const normalized = w.PersianFormat
                    .normalizeInputNumber(pasted)
                    .trim();

                if (!/^\d+(?:\.\d{1,2})?$/.test(normalized)) {
                    this.formError(
                        'Amount',
                        'مبلغ کپی‌شده معتبر نیست؛ فقط عدد مثبت با حداکثر دو رقم اعشار وارد کنید.'
                    );

                    return;
                }

                const start = input.selectionStart;
                const end = input.selectionEnd;
                const result = w.PersianFormat.formatAmountInput(
                    input.value.slice(0, start) + normalized +
                        input.value.slice(end),
                    start + normalized.length
                );

                input.value = result.text;
                input.setSelectionRange(result.caret, result.caret);
                this.formError('Amount');
            },

            onAmountInput(event) {
                const input = event.currentTarget;
                const result = w.PersianFormat.formatAmountInput(
                    input.value,
                    input.selectionStart
                );

                input.value = result.text;
                input.setSelectionRange(result.caret, result.caret);
                this.formError('Amount');
            },

            /* ---------------------------------------------------------------
             * The shared modal stays native HTML, so its events are bound here.
             * ------------------------------------------------------------- */

            bindFormModal() {
                const form = document.getElementById('transactionForm');

                if (form) {
                    form.addEventListener('submit', this.saveTransaction);
                }

                const typeField = this.formField('Type');

                if (typeField) {
                    typeField.addEventListener('change', () => {
                        this.formError('Type');
                        this.categoryOptions();
                    });
                }

                ['Title', 'Type', 'Amount', 'Category', 'Date']
                    .forEach(name => {
                        const input = this.formField(name);

                        if (!input) {
                            return;
                        }

                        if (name === 'Amount') {
                            input.addEventListener(
                                'keydown',
                                this.onAmountKeydown
                            );
                            input.addEventListener(
                                'paste',
                                this.onAmountPaste
                            );
                        }

                        input.addEventListener('input', event => {
                            if (name === 'Amount') {
                                this.onAmountInput(event);
                            }

                            this.formError(name);
                        });

                        input.addEventListener('change', () => {
                            this.formError(name);
                        });
                    });

                const modalElement = document.getElementById('transactionModal');

                if (modalElement) {
                    modalElement.addEventListener('hidden.bs.modal', () => {
                        this.resetForm();
                    });
                }
            }
        }
    };
}

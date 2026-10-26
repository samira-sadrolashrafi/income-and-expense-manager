'use strict';

{
    const w = window;
    function modal(id) {
        const element = document.getElementById(id);
        return element
            ? w.bootstrap.Modal.getOrCreateInstance(element)
            : null;
    }

    w.TarazinoVueCategoriesForm = {
        methods: {
            /* ---------------------------------------------------------------
             * Add / edit
             * ------------------------------------------------------------- */

            resetForm() {
                this.form = { id: null, name: '', type: '' };
                this.formErrors = { name: '', type: '' };
                this.formMessage.text = '';
            },

            openCreateModal() {
                this.resetForm();

                const instance = modal('categoryModal');
                if (instance) instance.show();
            },

            openEditModal(category) {
                this.resetForm();

                this.form.id = category.id;
                this.form.name = category.name || '';
                this.form.type = category.type || '';

                const instance = modal('categoryModal');
                if (instance) instance.show();
            },

            onFormInput(field) {
                this.formErrors[field] = '';
                this.formMessage.text = '';
            },

            validateForm() {
                const name = String(this.form.name || '').trim();
                const length = [...name].length;

                this.formErrors = {
                    name: length >= 1 && length <= 100
                        ? ''
                        : 'نام باید بین ۱ تا ۱۰۰ نویسه باشد.',
                    type: ['income', 'expense'].includes(this.form.type)
                        ? ''
                        : 'نوع دسته‌بندی را انتخاب کنید.'
                };

                return !this.formErrors.name && !this.formErrors.type;
            },

            async saveCategory() {
                if (this.saving || !this.validateForm()) {
                    return;
                }

                this.saving = true;
                this.formMessage.text = '';

                const data = new FormData();
                data.append('name', String(this.form.name).trim());
                data.append('type', this.form.type);

                const url = this.form.id === null
                    ? this.apiBase() + '/store'
                    : this.apiBase() + '/' +
                        encodeURIComponent(this.form.id) + '/update';

                try {
                    const result = await w.TarazinoApi.post(url, data);

                    const instance = modal('categoryModal');
                    if (instance) instance.hide();

                    this.showMessage(
                        result.message || 'دسته‌بندی ذخیره شد.',
                        'success'
                    );

                    try {
                        await this.loadCategories(
                            this.pagination.current_page
                        );
                    } catch (error) {
                        this.showMessage(
                            'ذخیره شد، اما فهرست به‌روز نشد: ' +
                            error.message,
                            'warning'
                        );
                    }
                } catch (error) {
                    this.setMessage(
                        this.formMessage,
                        error.message || 'ذخیره انجام نشد.'
                    );
                } finally {
                    this.saving = false;
                }
            },


        }
    };
}

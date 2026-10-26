'use strict';

{
    const w = window;
    function modal(id) {
        const element = document.getElementById(id);
        return element
            ? w.bootstrap.Modal.getOrCreateInstance(element)
            : null;
    }

    w.TarazinoVueCategoriesDelete = {
        methods: {
            /* ---------------------------------------------------------------
             * Delete / restore
             * ------------------------------------------------------------- */

            askDeleteCategory(category) {
                this.deleteTarget = category;
                this.deleteMessage.text = '';

                const instance = modal('deleteCategoryModal');
                if (instance) instance.show();
            },

            async confirmDelete() {
                if (this.deleting || !this.deleteTarget) {
                    return;
                }

                this.deleting = true;
                this.deleteMessage.text = '';

                try {
                    const result = await w.TarazinoApi.post(
                        this.apiBase() + '/' +
                        encodeURIComponent(this.deleteTarget.id) + '/delete'
                    );

                    const instance = modal('deleteCategoryModal');
                    if (instance) instance.hide();

                    this.showMessage(
                        result.message || 'دسته‌بندی حذف شد.',
                        'success'
                    );

                    // The deleted list must be fetched again on next open.
                    this.deletedLoaded = false;

                    try {
                        await this.loadCategories(
                            this.pagination.current_page
                        );
                    } catch (error) {
                        this.showMessage(
                            'حذف شد، اما فهرست به‌روز نشد: ' +
                            error.message,
                            'warning'
                        );
                    }
                } catch (error) {
                    this.setMessage(
                        this.deleteMessage,
                        error.message || 'حذف انجام نشد.'
                    );
                } finally {
                    this.deleting = false;
                }
            },

            async restoreCategory(category) {
                if (this.restoringId !== null) {
                    return;
                }

                if (!w.confirm(
                    'دسته‌بندی بازیابی شود؟ تراکنش‌های حذف‌شده خودکار بازیابی نمی‌شوند.'
                )) {
                    return;
                }

                this.restoringId = category.id;

                try {
                    const result = await w.TarazinoApi.post(
                        this.apiBase() + '/' +
                        encodeURIComponent(category.id) + '/restore'
                    );

                    this.deletedLoaded = false;

                    const results = await Promise.allSettled([
                        this.loadCategories(this.pagination.current_page),
                        this.loadDeleted(
                            this.deletedPagination.current_page
                        )
                    ]);

                    const failed = results.some(
                        item => item.status === 'rejected'
                    );

                    this.showMessage(
                        failed
                            ? 'دسته‌بندی بازیابی شد؛ اما تازه‌سازی یکی از فهرست‌ها انجام نشد.'
                            : (result.message ||
                                'دسته‌بندی بازیابی شد.'),
                        failed ? 'warning' : 'success'
                    );
                } catch (error) {
                    this.showMessage(
                        error.message || 'بازیابی انجام نشد.'
                    );
                } finally {
                    this.restoringId = null;
                }
            },


        }
    };
}

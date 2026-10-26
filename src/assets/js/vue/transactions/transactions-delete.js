'use strict';

{
    const w = window;

    function modal(id) {
        const element = document.getElementById(id);
        return element
            ? w.bootstrap.Modal.getOrCreateInstance(element)
            : null;
    }

    w.TarazinoVueTransactionsDelete = {
        methods: {
            /* ---------------------------------------------------------------
             * Delete / restore
             * ------------------------------------------------------------- */

            askDelete(tx) {
                this.deleteTarget = tx;
                this.deleteMessage.text = '';
                this.deleteMessage.seq += 1;

                const instance = modal('deleteTransactionModal');

                if (instance) {
                    instance.show();
                }
            },

            async confirmDelete() {
                if (this.deleting || !this.deleteTarget) {
                    return;
                }

                const id = this.deleteTarget.id;

                this.deleting = true;
                this.deleteMessage.text = '';

                try {
                    const result = await w.TarazinoApi.post(
                        this.apiBase() + '/' +
                        encodeURIComponent(id) + '/delete'
                    );

                    const instance = modal('deleteTransactionModal');

                    if (instance) {
                        instance.hide();
                    }

                    this.showMessage(
                        result.message || 'تراکنش حذف شد.',
                        'success'
                    );

                    // The deleted list must be fetched again after the change.
                    this.deletedLoaded = false;

                    const results = await Promise.allSettled([
                        this.loadTransactions(this.pagination.current_page),
                        this.loadDeleted(this.deletedPagination.current_page)
                    ]);

                    if (results.some(item => item.status === 'rejected')) {
                        this.showMessage(
                            'حذف شد؛ اما یکی از فهرست‌ها به‌روز نشد.',
                            'warning'
                        );

                        this.deletedLoaded = false;
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

            async restoreTransaction(tx) {
                if (this.restoringId !== null || this.deleting) {
                    return;
                }

                if (!w.confirm(
                    'آیا تراکنش بازیابی شود؟ تغییرات مالی توسط سرور محاسبه می‌شوند.'
                )) {
                    return;
                }

                this.restoringId = tx.id;

                try {
                    const result = await w.TarazinoApi.post(
                        this.apiBase() + '/' +
                        encodeURIComponent(tx.id) + '/restore'
                    );

                    this.showMessage(
                        result.message || 'تراکنش بازیابی شد.',
                        'success'
                    );

                    this.deletedLoaded = false;

                    const results = await Promise.allSettled([
                        this.loadTransactions(this.pagination.current_page),
                        this.loadDeleted(this.deletedPagination.current_page)
                    ]);

                    if (results.some(item => item.status === 'rejected')) {
                        this.showMessage(
                            'بازیابی شد؛ اما دریافت دوباره اطلاعات ناقص ماند.',
                            'warning'
                        );

                        this.deletedLoaded = false;
                    }
                } catch (error) {
                    this.showMessage(
                        error.message || 'بازیابی انجام نشد.'
                    );
                } finally {
                    this.restoringId = null;
                }
            }
        }
    };
}

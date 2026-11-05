/** TARAZINO | Dashboard transactions coordinator. */
(function (w, d) {
    'use strict';

    function message(id, text, type = 'danger') {

        const element = d.getElementById(id);

        if (!element) {
            return;
        }

        element.textContent = text;
        element.className = text ? 'alert alert-' + type : 'alert d-none';

    }


    async function reload(state, refreshReport = true) {

        if (state.loading) {
            return;
        }

        state.loading = true;

        try {

            const [transactionsResult, categoriesResult] = await Promise.all([

                w.TarazinoApi.get(state.transactionsUrl),
                w.TarazinoApi.get(state.categoriesUrl)

            ]);

            if (
                !Array.isArray(transactionsResult.transactions) ||
                !Array.isArray(categoriesResult.categories)
            ) {
                throw new Error('ساختار اطلاعات دریافتی معتبر نیست.');
            }

            state.transactions = transactionsResult.transactions;
            state.categories = categoriesResult.categories;

            w.TarazinoTransactions.render(state);

        } catch (error) {

            state.transactions = [];
            state.categories = [];

            w.TarazinoTransactions.render(state);

            message('dashboardMessage', error.message);

        } finally {

            state.loading = false;

        }

        if (refreshReport) {
            await w.TarazinoReports.render();
        }

    }


    function initialize() {

        const root = d.querySelector('.dashboard-shell[data-transactions-url]');

        if (!root) {
            return;
        }

        if (
            !w.PersianFormat ||
            !w.TarazinoApi ||
            !w.TarazinoReports ||
            !w.TarazinoTransactions
        ) {
            message('dashboardMessage', 'یکی از فایل‌های مشترک داشبورد بارگذاری نشده است.');
            return;
        }

        const state = {

            categoriesUrl: root.dataset.categoriesUrl,
            transactionsUrl: root.dataset.transactionsUrl,

            transactions: [],
            categories: [],

            loading: false,
            saving: false,
            deleting: false,

            editingId: null,
            deletingId: null

        };

        w.TarazinoReports.initialize(state);

        w.TarazinoTransactions.initialize(state, () => reload(state));

        // The report module loads its initial data on its own.
        reload(state, false);

    }


    if (d.readyState === 'loading') {
        d.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }

})(window, document);
'use strict';

{
    const w = window;
    const PAGE_SIZE = 10;
    const DEFAULT_FILTERS = Object.freeze({
        search: '',
        type: 'all',
        category_id: 'all',
        date_from: '',
        date_to: '',
        sort: 'newest'
    });

    w.TarazinoVueTransactionsList = {
        computed: {
            hasTransactions() {
                return this.transactions.length > 0;
            },

            paginationSummary() {
                return this.pageSummary(this.pagination);
            },

            canGoPrevious() {
                return Number(this.pagination.current_page) > 1 &&
                    !this.loading;
            },

            canGoNext() {
                return Number(this.pagination.current_page) <
                    Number(this.pagination.total_pages) &&
                    !this.loading;
            },

            paginationPages() {
                return this.pageNumbers(this.pagination);
            },

            hasDeletedTransactions() {
                return this.deletedTransactions.length > 0;
            },

            deletedPaginationSummary() {
                return this.pageSummary(this.deletedPagination);
            },

            deletedPaginationPages() {
                return this.pageNumbers(this.deletedPagination);
            },

            canGoPreviousDeleted() {
                return Number(this.deletedPagination.current_page) > 1 &&
                    !this.deletedLoading;
            },

            canGoNextDeleted() {
                return Number(this.deletedPagination.current_page) <
                    Number(this.deletedPagination.total_pages) &&
                    !this.deletedLoading;
            },

            /**
             * Category filter options follow the currently selected type.
             * The "all" option always stays available as the default.
             */
            categoryFilterOptions() {
                const options = [{ value: 'all', label: 'همه دسته‌بندی‌ها' }];

                this.categories
                    .filter(category =>
                        this.filters.type === 'all' ||
                        category.type === this.filters.type
                    )
                    .forEach(category => {
                        options.push({
                            value: String(category.id),
                            label: category.name
                        });
                    });

                return options;
            }
        },

        methods: {
            /** Base API URL taken from the mount element. */
            apiBase() {
                const page = document.getElementById('transactionsMain');
                const url = page && page.dataset.transactionsApiUrl;

                if (!url) {
                    throw new Error('آدرس API تراکنش‌ها تنظیم نشده است.');
                }

                return url.replace(/\/$/, '');
            },

            categoriesApiBase() {
                const page = document.getElementById('transactionsMain');
                const url = page && page.dataset.categoriesApiUrl;

                if (!url) {
                    throw new Error('آدرس API دسته‌بندی‌ها تنظیم نشده است.');
                }

                return url.replace(/\/$/, '');
            },

            /** "نمایش ۱ تا ۱۰ از ۲۵" for a pagination object. */
            pageSummary(pagination) {
                const total = Number(pagination.total_items) || 0;
                const perPage = Number(pagination.per_page) || PAGE_SIZE;
                const page = Number(pagination.current_page) || 1;

                if (total === 0) {
                    return 'موردی وجود ندارد';
                }

                const start = (page - 1) * perPage + 1;
                const end = Math.min(page * perPage, total);

                return 'نمایش ' +
                    this.formatInteger(start) + ' تا ' +
                    this.formatInteger(end) + ' از ' +
                    this.formatInteger(total);
            },

            /** A window of up to five page numbers around the current page. */
            pageNumbers(pagination) {
                const totalPages = Number(pagination.total_pages) || 0;
                const currentPage = Number(pagination.current_page) || 1;

                const firstPage = Math.max(
                    1,
                    Math.min(currentPage - 2, totalPages - 4)
                );

                const lastPage = Math.min(totalPages, firstPage + 4);
                const pages = [];

                for (let page = firstPage; page <= lastPage; page++) {
                    pages.push(page);
                }

                return pages;
            },

            /* ---------------------------------------------------------------
             * Active transactions
             * ------------------------------------------------------------- */

            activeUrl(page) {
                const url = new URL(this.apiBase(), w.location.href);
                const filters = this.appliedFilters;

                url.searchParams.set('page', String(page));
                url.searchParams.set(
                    'per_page',
                    String(this.pagination.per_page || PAGE_SIZE)
                );

                // "all" means "no filter" and must not be sent to the server.
                if (filters.search) {
                    url.searchParams.set('search', filters.search);
                }

                if (filters.type && filters.type !== 'all') {
                    url.searchParams.set('type', filters.type);
                }

                if (filters.category_id && filters.category_id !== 'all') {
                    url.searchParams.set('category_id', filters.category_id);
                }

                if (filters.date_from) {
                    url.searchParams.set('date_from', filters.date_from);
                }

                if (filters.date_to) {
                    url.searchParams.set('date_to', filters.date_to);
                }

                if (filters.sort) {
                    url.searchParams.set('sort', filters.sort);
                }

                return url.toString();
            },

            async requestTransactions(page) {
                const response = await w.TarazinoApi.get(
                    this.activeUrl(page)
                );

                if (
                    !Array.isArray(response.transactions) ||
                    !response.pagination ||
                    typeof response.pagination.total_pages !== 'number'
                ) {
                    throw new Error(
                        'ساختار پاسخ صفحه‌بندی تراکنش‌ها معتبر نیست.'
                    );
                }

                return response;
            },

            async loadTransactions(page = 1) {
                const requestId = ++this.requestId;

                this.loading = true;
                this.error = '';

                try {
                    const [firstResponse, categoryResponse] =
                        await Promise.all([
                            this.requestTransactions(page),
                            w.TarazinoApi.get(this.categoriesApiBase())
                        ]);

                    let response = firstResponse;
                    const totalPages = Number(
                        response.pagination.total_pages
                    );

                    if (totalPages > 0 && page > totalPages) {
                        response = await this.requestTransactions(totalPages);
                    } else if (totalPages === 0 && page !== 1) {
                        response = await this.requestTransactions(1);
                    }

                    // Ignore results from older requests.
                    if (requestId !== this.requestId) {
                        return null;
                    }

                    if (!Array.isArray(categoryResponse.categories)) {
                        throw new Error(
                            'ساختار پاسخ دسته‌بندی‌ها معتبر نیست.'
                        );
                    }

                    this.transactions = response.transactions;
                    this.pagination = response.pagination;
                    this.categories = categoryResponse.categories;
                    this.categoriesLoaded = true;

                    return response;
                } catch (error) {
                    if (requestId !== this.requestId) {
                        return null;
                    }

                    this.categoriesLoaded = false;
                    this.error = error.message ||
                        'دریافت تراکنش‌ها انجام نشد.';

                    throw error;
                } finally {
                    if (requestId === this.requestId) {
                        this.loading = false;
                    }
                }
            },

            applyFilters() {
                const type = ['income', 'expense'].includes(this.filters.type)
                    ? this.filters.type
                    : 'all';

                const allowed =
                    this.categoryFilterOptions.map(option => option.value);

                this.appliedFilters = {
                    search: String(this.filters.search || '').trim(),
                    type: type,
                    category_id: allowed.includes(String(this.filters.category_id))
                        ? String(this.filters.category_id)
                        : 'all',
                    date_from: String(this.filters.date_from || ''),
                    date_to: String(this.filters.date_to || ''),
                    sort: ['newest', 'oldest'].includes(this.filters.sort)
                        ? this.filters.sort
                        : 'newest'
                };

                this.clearMessage();
                this.loadTransactions(1).catch(() => { });
            },

            clearFilters() {
                this.filters = { ...DEFAULT_FILTERS };
                this.appliedFilters = { ...DEFAULT_FILTERS };

                this.clearMessage();
                this.refreshFilterDates();
                this.loadTransactions(1).catch(() => { });
            },

            /** Rebuild only the category options; the list waits for Apply. */
            onTypeFilterChange() {
                const allowed =
                    this.categoryFilterOptions.map(option => option.value);

                if (!allowed.includes(String(this.filters.category_id))) {
                    this.filters.category_id = 'all';
                }
            },

            refreshFilterDates() {
                this.$nextTick(() => {
                    const root = document.getElementById('transactionsMain');

                    if (root && w.TarazinoDatepicker) {
                        w.TarazinoDatepicker.refresh(root);
                    }
                });
            },

            retryLoad() {
                this.loadTransactions(this.pagination.current_page)
                    .catch(() => { });
            },

            changePage(page) {
                const nextPage = Number(page);
                const currentPage = Number(this.pagination.current_page);
                const totalPages = Number(this.pagination.total_pages);

                if (
                    this.loading ||
                    nextPage < 1 ||
                    nextPage > totalPages ||
                    nextPage === currentPage
                ) {
                    return;
                }

                this.loadTransactions(nextPage).catch(() => { });
            },

            /* ---------------------------------------------------------------
             * Deleted transactions
             * ------------------------------------------------------------- */

            deletedUrl(page) {
                const url = new URL(
                    this.apiBase() + '/deleted',
                    w.location.href
                );

                url.searchParams.set('page', String(page));
                url.searchParams.set(
                    'per_page',
                    String(this.deletedPagination.per_page || PAGE_SIZE)
                );

                return url.toString();
            },

            async requestDeleted(page) {
                const response = await w.TarazinoApi.get(
                    this.deletedUrl(page)
                );

                if (
                    !Array.isArray(response.transactions) ||
                    !response.pagination ||
                    typeof response.pagination.total_pages !== 'number'
                ) {
                    throw new Error('فهرست حذف‌شده‌ها معتبر نیست.');
                }

                // restore_available comes from the API; never recompute it here.
                return response;
            },

            async loadDeleted(page = 1) {
                const requestId = ++this.deletedRequestId;

                this.deletedLoading = true;
                this.deletedError = '';

                try {
                    let response = await this.requestDeleted(page);
                    const totalPages = Number(
                        response.pagination.total_pages
                    );

                    if (totalPages > 0 && page > totalPages) {
                        response = await this.requestDeleted(totalPages);
                    } else if (totalPages === 0 && page !== 1) {
                        response = await this.requestDeleted(1);
                    }

                    if (requestId !== this.deletedRequestId) {
                        return null;
                    }

                    this.deletedTransactions = response.transactions;
                    this.deletedPagination = response.pagination;
                    this.deletedLoaded = true;

                    return response;
                } catch (error) {
                    if (requestId !== this.deletedRequestId) {
                        return null;
                    }

                    this.deletedLoaded = false;
                    this.deletedError = error.message ||
                        'دریافت تراکنش‌های حذف‌شده ناموفق بود.';

                    throw error;
                } finally {
                    if (requestId === this.deletedRequestId) {
                        this.deletedLoading = false;
                    }
                }
            },

            changeDeletedPage(page) {
                const nextPage = Number(page);
                const currentPage =
                    Number(this.deletedPagination.current_page);
                const totalPages =
                    Number(this.deletedPagination.total_pages);

                if (
                    this.deletedLoading ||
                    nextPage < 1 ||
                    nextPage > totalPages ||
                    nextPage === currentPage
                ) {
                    return;
                }

                this.loadDeleted(nextPage).catch(() => { });
            },

            retryDeleted() {
                this.loadDeleted(this.deletedPagination.current_page)
                    .catch(() => { });
            },

            /* ---------------------------------------------------------------
             * Tabs
             * ------------------------------------------------------------- */

            showActiveTab() {
                this.showDeleted = false;
            },

            showDeletedTab() {
                this.showDeleted = true;

                if (!this.deletedLoaded) {
                    this.loadDeleted().catch(() => { });
                }
            },

            /* ---------------------------------------------------------------
             * Display and restore helpers
             * ------------------------------------------------------------- */

            categoryOf(tx) {
                return this.categories.find(
                    category =>
                        String(category.id) === String(tx.category_id)
                ) || null;
            },

            categoryName(tx) {
                const category = this.categoryOf(tx);

                return category ? category.name : '—';
            },

            typeLabel(tx) {
                const category = this.categoryOf(tx);
                const type = category ? category.type : '';

                return type === 'income'
                    ? 'درآمد'
                    : type === 'expense'
                        ? 'هزینه'
                        : 'نامشخص';
            },

            amountClass(tx) {
                const category = this.categoryOf(tx);

                return category && category.type === 'expense'
                    ? 'text-danger'
                    : 'text-success';
            },

            canRestore(tx) {
                return tx.restore_available === true &&
                    tx.category_deleted_at == null &&
                    !!tx.category_name;
            },

            restoreReason(tx) {
                if (!tx.restore_available) {
                    return 'مهلت بازیابی تمام شده';
                }

                if (tx.category_deleted_at != null || !tx.category_name) {
                    return 'ابتدا دسته‌بندی مربوط را بازیابی کنید';
                }

                return 'قابل بازیابی';
            },

            formatDate(value) {
                if (
                    w.PersianFormat &&
                    typeof w.PersianFormat.date === 'function'
                ) {
                    return w.PersianFormat.date(value);
                }

                return value || '—';
            },

            formatDateTime(value) {
                if (
                    w.PersianFormat &&
                    typeof w.PersianFormat.dateTime === 'function'
                ) {
                    return w.PersianFormat.dateTime(value);
                }

                return value || '—';
            },

            formatNumber(value) {
                if (
                    w.PersianFormat &&
                    typeof w.PersianFormat.number === 'function'
                ) {
                    return w.PersianFormat.number(value);
                }

                return String(value);
            },

            formatInteger(value) {
                if (
                    w.PersianFormat &&
                    typeof w.PersianFormat.integer === 'function'
                ) {
                    return w.PersianFormat.integer(value);
                }

                return String(value);
            }
        }
    };
}

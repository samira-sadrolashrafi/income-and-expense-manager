'use strict';

{
    const w = window;
    const PAGE_SIZE = 10;
    const DEFAULT_FILTERS = Object.freeze({
        search: '',
        type: 'all',
        sort: 'newest'
    });

    w.TarazinoVueCategoriesList = {
        computed: {
            hasCategories() {
                return this.categories.length > 0;
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

            hasDeletedCategories() {
                return this.deletedCategories.length > 0;
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
            }
        },

        methods: {
            /** Base API URL taken from the mount element. */
            apiBase() {
                const page = document.getElementById('categoriesMain');
                const url = page && page.dataset.categoriesApiUrl;

                if (!url) {
                    throw new Error(
                        'آدرس API دسته‌بندی‌ها تنظیم نشده است.'
                    );
                }

                return url.replace(/\/$/, '');
            },

            /** "نمایش ۱ تا ۱۰ از ۲۵" for a pagination object. */
            pageSummary(pagination) {
                const total = Number(pagination.total) || 0;
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
             * Active categories
             * ------------------------------------------------------------- */

            activeUrl(page) {
                const url = new URL(this.apiBase(), w.location.href);

                url.searchParams.set('page', String(page));
                url.searchParams.set(
                    'per_page',
                    String(this.pagination.per_page || PAGE_SIZE)
                );
                url.searchParams.set('search', this.appliedFilters.search);
                url.searchParams.set('type', this.appliedFilters.type);
                url.searchParams.set('sort', this.appliedFilters.sort);

                return url.toString();
            },

            async requestCategories(page) {
                const response = await w.TarazinoApi.get(
                    this.activeUrl(page)
                );

                if (
                    !Array.isArray(response.categories) ||
                    !response.pagination ||
                    typeof response.pagination.total_pages !== 'number'
                ) {
                    throw new Error(
                        'اطلاعات دریافتی از سرور معتبر نیست.'
                    );
                }

                return response;
            },

            async loadCategories(page = 1) {
                const requestId = ++this.requestId;

                this.loading = true;
                this.error = '';

                try {
                    let response = await this.requestCategories(page);
                    const totalPages = response.pagination.total_pages;

                    if (totalPages > 0 && page > totalPages) {
                        response = await this.requestCategories(totalPages);
                    } else if (totalPages === 0 && page !== 1) {
                        response = await this.requestCategories(1);
                    }

                    // Ignore results from older requests.
                    if (requestId !== this.requestId) {
                        return null;
                    }

                    this.categories = response.categories;
                    this.pagination = response.pagination;

                    return response;
                } catch (error) {
                    if (requestId !== this.requestId) {
                        return null;
                    }

                    this.error = error.message ||
                        'دریافت دسته‌بندی‌ها ناموفق بود.';

                    throw error;
                } finally {
                    if (requestId === this.requestId) {
                        this.loading = false;
                    }
                }
            },

            applyFilters() {
                this.appliedFilters = {
                    search: String(this.filters.search || '').trim(),
                    type: ['income', 'expense'].includes(this.filters.type)
                        ? this.filters.type
                        : 'all',
                    sort: ['newest', 'oldest'].includes(this.filters.sort)
                        ? this.filters.sort
                        : 'newest'
                };

                this.clearMessage();
                this.loadCategories(1).catch(() => { });
            },

            clearFilters() {
                this.filters = { ...DEFAULT_FILTERS };
                this.appliedFilters = { ...DEFAULT_FILTERS };

                this.clearMessage();
                this.loadCategories(1).catch(() => { });
            },

            retryLoad() {
                this.loadCategories(this.pagination.current_page)
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

                this.loadCategories(nextPage).catch(() => { });
            },

            /* ---------------------------------------------------------------
             * Deleted categories
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
                    !Array.isArray(response.categories) ||
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
                    const totalPages = response.pagination.total_pages;

                    if (totalPages > 0 && page > totalPages) {
                        response = await this.requestDeleted(totalPages);
                    } else if (totalPages === 0 && page !== 1) {
                        response = await this.requestDeleted(1);
                    }

                    if (requestId !== this.deletedRequestId) {
                        return null;
                    }

                    this.deletedCategories = response.categories;
                    this.deletedPagination = response.pagination;
                    this.deletedLoaded = true;

                    return response;
                } catch (error) {
                    if (requestId !== this.deletedRequestId) {
                        return null;
                    }

                    this.deletedLoaded = false;
                    this.deletedError = error.message ||
                        'دریافت دسته‌بندی‌های حذف‌شده ناموفق بود.';

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
             * Display helpers
             * ------------------------------------------------------------- */

            categoryIcon(category) {
                const name = String(category.name || '');

                const presets = category.type === 'income'
                    ? [
                        [/حقوق|دستمزد/, 'fa-money-bill-wave', 'text-success'],
                        [/فروش/, 'fa-cart-shopping', 'text-primary'],
                        [/هدیه/, 'fa-gift', 'text-info']
                    ]
                    : [
                        [/خوراک|غذا/, 'fa-utensils', 'text-warning'],
                        [/حمل|رفت.*آمد/, 'fa-car', 'text-info'],
                        [/مسکن|اجاره/, 'fa-house', 'text-primary'],
                        [/درمان|سلامت/, 'fa-heart-pulse', 'text-danger'],
                        [/آموزش/, 'fa-graduation-cap', 'text-info']
                    ];

                const match = presets.find(
                    ([pattern]) => pattern.test(name)
                );

                const classes = match
                    ? match.slice(1)
                    : category.type === 'income'
                        ? ['fa-arrow-trend-up', 'text-success']
                        : ['fa-arrow-trend-down', 'text-danger'];

                return classes.join(' ');
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

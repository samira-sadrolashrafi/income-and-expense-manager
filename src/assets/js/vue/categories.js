/** TARAZINO | Vue 3 categories page (Options API). */
(function (w) {
    'use strict';

    const app = w.Vue.createApp({
        data() {
            return {
                categories: [],
                loading: true,
                error: '',
                deletedCategories: [],
                deletedLoading: false,
                deletedError: '',
                deletedEmptyMessage: 'برای مشاهده دسته‌بندی‌های حذف‌شده، این بخش را انتخاب کنید.',

                deletedPagination: {
                    current_page: 1,
                    per_page: 10,
                    total: 0,
                    total_pages: 0
                },

                filters: {
                    search: '',
                    type: 'all',
                    sort: 'newest'
                },

                appliedFilters: {
                    search: '',
                    type: 'all',
                    sort: 'newest'
                },

                pagination: {
                    current_page: 1,
                    per_page: 10,
                    total: 0,
                    total_pages: 0
                },

                requestId: 0
            };
        },

        computed: {
            hasCategories() {
                return this.categories.length > 0;
            },

            paginationSummary() {
                const total = Number(this.pagination.total) || 0;
                const perPage = Number(this.pagination.per_page) || 10;
                const page = Number(this.pagination.current_page) || 1;

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
                const totalPages =
                    Number(this.pagination.total_pages) || 0;

                const currentPage =
                    Number(this.pagination.current_page) || 1;

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

            hasDeletedCategories() {
                return this.deletedCategories.length > 0;
            },

            deletedPaginationSummary() {
                const total = Number(this.deletedPagination.total) || 0;
                const perPage = Number(this.deletedPagination.per_page) || 10;
                const page = Number(this.deletedPagination.current_page) || 1;

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

            deletedPaginationPages() {
                const totalPages =
                    Number(this.deletedPagination.total_pages) || 0;

                const currentPage =
                    Number(this.deletedPagination.current_page) || 1;

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

            canGoPreviousDeleted() {
                return this.deletedPagination.current_page > 1 &&
                    !this.deletedLoading;
            },

            canGoNextDeleted() {
                return this.deletedPagination.current_page <
                    this.deletedPagination.total_pages &&
                    !this.deletedLoading;
            }
        },

        methods: {
            buildUrl(page) {
                const pageElement = document.getElementById('categoriesMain');
                const apiUrl = pageElement &&
                    pageElement.dataset.categoriesApiUrl;

                if (!apiUrl) {
                    throw new Error(
                        'آدرس API دسته‌بندی‌ها تنظیم نشده است.'
                    );
                }

                const url = new URL(apiUrl, w.location.href);

                url.searchParams.set('page', String(page));
                url.searchParams.set(
                    'per_page',
                    String(this.pagination.per_page)
                );
                url.searchParams.set('search', this.appliedFilters.search);
                url.searchParams.set('type', this.appliedFilters.type);
                url.searchParams.set('sort', this.appliedFilters.sort);

                return url.toString();
            },

            async requestCategories(page) {
                const response = await w.TarazinoApi.get(
                    this.buildUrl(page)
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

            notifyLegacy(response) {
                const legacy = w.TarazinoCategoriesLegacy;

                if (legacy && typeof legacy.syncActive === 'function') {
                    legacy.syncActive(response);
                } else {
                    // The old script may not have initialized yet.
                    w.TarazinoPendingCategoriesResponse = response;
                }
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
                    this.loading = false;
                    this.notifyLegacy(response);

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

            resetFilters() {
                this.filters.search = '';
                this.filters.type = 'all';
                this.filters.sort = 'newest';

                this.appliedFilters = {
                    search: '',
                    type: 'all',
                    sort: 'newest'
                };
            },

            retryLoad() {
                this.loadCategories(this.pagination.current_page)
                    .catch(() => { });
            },

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

            formatInteger(value) {
                if (
                    w.PersianFormat &&
                    typeof w.PersianFormat.integer === 'function'
                ) {
                    return w.PersianFormat.integer(value);
                }

                return String(value);
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
        setDeletedCategories(categories, pagination) {
            this.deletedCategories = Array.isArray(categories)
                ? categories
                : [];

            if (pagination) {
                this.deletedPagination = pagination;
            }

            this.deletedLoading = false;
            this.deletedError = '';
            this.deletedEmptyMessage = this.deletedCategories.length
                ? ''
                : 'دسته‌بندی حذف‌شده‌ای وجود ندارد.';
        },

        setDeletedStatus(text, isError = false) {
            const isLoading = text.startsWith('در حال دریافت');

            this.deletedLoading = isLoading;
            this.deletedError = isError ? text : '';
            this.deletedEmptyMessage =
                !isLoading && !isError ? text : '';

            if (isLoading || isError) {
                this.deletedCategories = [];
            }
        },

        changeDeletedPage(page) {
            if (
                this.deletedLoading ||
                page < 1 ||
                page > this.deletedPagination.total_pages ||
                page === this.deletedPagination.current_page
            ) {
                return;
            }

            const legacy = window.TarazinoCategoriesLegacy;

            if (legacy) {
                legacy.changeDeletedPage(page);
            }
        },

        retryDeleted() {
            const legacy = window.TarazinoCategoriesLegacy;

            if (legacy) {
                legacy.retryDeleted().catch(() => { });
            }
        },

        formatDateTime(value) {
            if (
                window.PersianFormat &&
                typeof window.PersianFormat.dateTime === 'function'
            ) {
                return window.PersianFormat.dateTime(value);
            }

            return value || '—';
        },
        },

        mounted() {
            this.loadCategories().catch(() => { });
        }
    });

    // Vue uses the existing HTML in categories/index.php; no template string.
    w.TarazinoCategoriesVue = app.mount('#categoriesMain');
})(window);

/** TARAZINO | Categories coordinator: forms, deleted list, and Vue integration. */
(function (w, d) {
    'use strict';

    const $ = id => d.getElementById(id);
    const api = () => w.TarazinoApi;
    const fmt = () => w.PersianFormat;
    const PAGE_SIZE = 10;

    const DEFAULT_FILTERS = {
        search: '',
        type: 'all',
        sort: 'newest'
    };

    const s = {
        apiUrl: '',
        active: [],
        deleted: [],
        page: { active: 1, deleted: 1 },
        pagination: { active: null, deleted: null },
        perPage: PAGE_SIZE,
        busy: false,
        deletingId: null,
        deletedLoaded: false,
        loadingDeleted: false
    };

    let form;
    let list;

    function notice(id, text, type = 'danger') {
        const element = $(id);
        if (!element) return;

        element.textContent = text || '';
        element.className = text
            ? 'alert alert-' + type
            : 'alert d-none';

        if (id === 'deleteCategoryMessage') {
            element.classList.add('mt-3', 'mb-0');
        }
    }

    /**
     * Vue owns the active category request and table data.
     * The old coordinator keeps a copy for edit/delete event handlers.
     */
    function syncActive(result) {
        if (
            !result ||
            !Array.isArray(result.categories) ||
            !result.pagination
        ) {
            return;
        }

        s.active = result.categories;
        s.pagination.active = result.pagination;
        s.page.active = result.pagination.total_pages > 0
            ? result.pagination.current_page
            : 1;

        if (list) {
            list.render('active');
        }
    }

    // This callback is available even if Vue's first request finishes early.
    w.TarazinoCategoriesLegacy = Object.freeze({
        syncActive,
        changeDeletedPage(page) {
            return changePage('deleted', page);
        },
        retryDeleted() {
            return loadDeleted();
        }
    });

    /** Apply the filters currently stored in Vue. */
    async function applyFilters() {
        if (s.busy) return;

        const categoriesVue = w.TarazinoCategoriesVue;
        if (!categoriesVue) return;

        const filters = categoriesVue.filters;

        categoriesVue.appliedFilters = {
            search: String(filters.search || '').trim(),
            type: ['income', 'expense'].includes(filters.type)
                ? filters.type
                : 'all',
            sort: ['newest', 'oldest'].includes(filters.sort)
                ? filters.sort
                : 'newest'
        };

        s.page.active = 1;
        notice('categoriesMessage', '');

        await loadActive(1);
    }

    /** Reset Vue's filter fields and reload the first page. */
    async function clearFilters() {
        if (s.busy) return;

        const categoriesVue = w.TarazinoCategoriesVue;
        if (!categoriesVue) return;

        categoriesVue.resetFilters();
        categoriesVue.appliedFilters = { ...DEFAULT_FILTERS };
        s.page.active = 1;
        notice('categoriesMessage', '');

        await loadActive(1);
    }

    async function reloadActive(page = s.page.active) {
        const categoriesVue = w.TarazinoCategoriesVue;

        if (!categoriesVue) {
            throw new Error('برنامه Vue دسته‌بندی‌ها بارگذاری نشده است.');
        }

        return categoriesVue.loadCategories(page);
    }

    async function loadActive(page = s.page.active) {
        try {
            await reloadActive(page);
        } catch (error) {
            notice(
                'categoriesMessage',
                error.message || 'دریافت دسته‌بندی‌ها انجام نشد.'
            );
        }
    }

    async function loadDeleted(page = s.page.deleted) {
        if (s.loadingDeleted) return;

        s.loadingDeleted = true;
        list.empty('deleted', 'در حال دریافت دسته‌بندی‌های حذف‌شده...');

        try {
            const result = await api().get(
                s.apiUrl + '/deleted'
                + '?page=' + encodeURIComponent(page)
                + '&per_page=' + encodeURIComponent(s.perPage)
            );

            if (
                !Array.isArray(result.categories) ||
                !result.pagination ||
                typeof result.pagination.total_pages !== 'number'
            ) {
                throw new Error('فهرست حذف‌شده‌ها معتبر نیست.');
            }

            const pagination = result.pagination;

            if (
                pagination.total_pages > 0 &&
                page > pagination.total_pages
            ) {
                s.loadingDeleted = false;
                return loadDeleted(pagination.total_pages);
            }

            s.deleted = result.categories;
            s.pagination.deleted = pagination;
            s.page.deleted = pagination.total_pages > 0
                ? pagination.current_page
                : 1;

            for (const category of s.deleted) {
                category.restore_available =
                    !!category.restore_until &&
                    new Date(category.restore_until).getTime() > Date.now();
            }

            s.deletedLoaded = true;
            list.render('deleted');
        } catch (error) {
            s.deletedLoaded = false;

            list.empty(
                'deleted',
                error.message || 'دریافت انجام نشد.',
                true,
                () => loadDeleted()
            );

            notice(
                'categoriesMessage',
                error.message || 'دریافت دسته‌بندی‌های حذف‌شده انجام نشد.'
            );

            throw error;
        } finally {
            s.loadingDeleted = false;
        }
    }

    async function changePage(kind, page) {
        if (s.busy) return;
        if (kind === 'deleted' && s.loadingDeleted) return;
        if (kind === 'active' && w.TarazinoCategoriesVue.loading) return;

        const pagination = s.pagination[kind];
        if (!pagination) return;

        if (
            page < 1 ||
            page > pagination.total_pages ||
            page === s.page[kind]
        ) {
            return;
        }

        const previousPage = s.page[kind];
        s.page[kind] = page;

        try {
            if (kind === 'active') {
                await reloadActive(page);
            } else {
                await loadDeleted(page);
            }
        } catch (error) {
            s.page[kind] = previousPage;
            notice(
                'categoriesMessage',
                error.message || 'دریافت صفحه انجام نشد.'
            );
        }
    }

    function askDelete(category) {
        s.deletingId = String(category.id);
        $('deleteCategoryName').textContent = category.name || '';
        notice('deleteCategoryMessage', '');

        w.bootstrap.Modal
            .getOrCreateInstance($('deleteCategoryModal'))
            .show();
    }

    async function remove() {
        if (s.busy || s.deletingId === null) return;

        const button = $('confirmDeleteCategoryButton');
        s.busy = true;
        button.disabled = true;
        button.textContent = 'در حال حذف...';

        try {
            const result = await api().post(
                s.apiUrl + '/' + encodeURIComponent(s.deletingId) + '/delete'
            );

            w.bootstrap.Modal
                .getOrCreateInstance($('deleteCategoryModal'))
                .hide();

            notice(
                'categoriesMessage',
                result.message || 'دسته‌بندی حذف شد.',
                'success'
            );

            s.deletedLoaded = false;

            try {
                await reloadActive();
            } catch (error) {
                notice(
                    'categoriesMessage',
                    'حذف شد، اما فهرست به‌روز نشد: ' + error.message,
                    'warning'
                );
            }
        } catch (error) {
            notice(
                'deleteCategoryMessage',
                error.message || 'حذف انجام نشد.'
            );
        } finally {
            s.busy = false;
            s.deletingId = null;
            button.disabled = false;
            button.textContent = 'حذف دسته‌بندی';
        }
    }

    async function restore(id, button) {
        if (
            s.busy ||
            !w.confirm(
                'دسته‌بندی بازیابی شود؟ تراکنش‌های حذف‌شده خودکار بازیابی نمی‌شوند.'
            )
        ) {
            return;
        }

        s.busy = true;
        button.disabled = true;
        button.textContent = 'در حال بازیابی...';

        try {
            const result = await api().post(
                s.apiUrl + '/' + encodeURIComponent(id) + '/restore'
            );

            s.deletedLoaded = false;

            const updates = await Promise.allSettled([
                reloadActive(),
                loadDeleted()
            ]);

            const failed = updates.some(
                item => item.status === 'rejected'
            );

            notice(
                'categoriesMessage',
                failed
                    ? 'دسته‌بندی بازیابی شد؛ اما تازه‌سازی یکی از فهرست‌ها انجام نشد.'
                    : (result.message || 'دسته‌بندی بازیابی شد.'),
                failed ? 'warning' : 'success'
            );
        } catch (error) {
            notice(
                'categoriesMessage',
                error.message || 'بازیابی انجام نشد.'
            );
        } finally {
            s.busy = false;

            if (s.deletedLoaded) {
                list.render('deleted');
            } else {
                button.disabled = false;
                button.textContent = 'بازیابی';
            }
        }
    }

    function switchPanel(deleted) {
        $('activeCategoriesPanel').hidden = deleted;
        $('deletedCategoriesPanel').hidden = !deleted;

        $('activeCategoriesPanel').classList.toggle('d-none', deleted);
        $('deletedCategoriesPanel').classList.toggle('d-none', !deleted);

        $('showActiveCategoriesButton').className =
            'btn ' + (deleted ? 'btn-outline-primary' : 'btn-primary');

        $('showDeletedCategoriesButton').className =
            'btn ' + (deleted ? 'btn-primary' : 'btn-outline-primary');

        $('showActiveCategoriesButton').setAttribute(
            'aria-pressed',
            String(!deleted)
        );

        $('showDeletedCategoriesButton').setAttribute(
            'aria-pressed',
            String(deleted)
        );

        if (deleted && !s.deletedLoaded) {
            loadDeleted().catch(() => { });
        }
    }

    function initialize() {
        const page = $('categoriesMain');
        if (!page) return;

        if (
            !api() ||
            !fmt() ||
            !w.bootstrap ||
            !w.TarazinoCategoryForm ||
            !w.TarazinoCategoryList ||
            !w.TarazinoCategoriesVue
        ) {
            notice(
                'categoriesMessage',
                'یکی از اسکریپت‌های مشترک دسته‌بندی‌ها بارگذاری نشده است.'
            );
            return;
        }

        s.apiUrl = page.dataset.categoriesApiUrl.replace(/\/$/, '');

        const ctx = {
            s,
            $,
            api,
            fmt,
            notice,
            reloadActive,
            changePage,
            applyFilters,
            clearFilters,
            edit: category => form.openEdit(category),
            askDelete,
            restore
        };

        form = w.TarazinoCategoryForm.create(ctx);
        list = w.TarazinoCategoryList.create(ctx);

        form.bind();
        list.bind();

        $('confirmDeleteCategoryButton').addEventListener('click', remove);

        $('showActiveCategoriesButton').addEventListener(
            'click',
            () => switchPanel(false)
        );

        $('showDeletedCategoriesButton').addEventListener(
            'click',
            () => switchPanel(true)
        );

        $('deleteCategoryModal').addEventListener(
            'hidden.bs.modal',
            () => notice('deleteCategoryMessage', '')
        );

        // Vue may have completed its first API request before this file initialized.
        if (w.TarazinoPendingCategoriesResponse) {
            syncActive(w.TarazinoPendingCategoriesResponse);
            delete w.TarazinoPendingCategoriesResponse;
        } else if (
            !w.TarazinoCategoriesVue.loading &&
            !w.TarazinoCategoriesVue.error
        ) {
            syncActive({
                categories: w.TarazinoCategoriesVue.categories,
                pagination: w.TarazinoCategoriesVue.pagination
            });
        }

        switchPanel(false);
    }

    if (d.readyState === 'loading') {
        d.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})(window, document);

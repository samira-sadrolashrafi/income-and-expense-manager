/** TARAZINO | Category tables and server-side pagination. */
(function (w, d) {
    'use strict';

    function create(ctx) {
        const {
            s,
            $,
            fmt,
            edit,
            askDelete,
            restore,
            changePage,
            applyFilters,
            clearFilters
        } = ctx;

        const ids = kind => kind === 'active'
            ? ['categoriesTableBody', 'categoriesPagination']
            : ['deletedCategoriesTableBody', 'deletedCategoriesPagination'];

        function td(row, value, className = '') {
            const cell = row.insertCell();
            cell.textContent = value == null ? '' : value;
            cell.className = className;
            return cell;
        }

        function action(cell, text, actionName, id, color) {
            const button = d.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-sm btn-outline-' + color + ' me-1';
            button.textContent = text;
            button.dataset.action = actionName;
            button.dataset.id = String(id);
            cell.appendChild(button);
            return button;
        }

        function empty(
            kind,
            text,
            isError = false,
            retry = null,
            hidePagination = true
        ) {
            if (kind === 'deleted' && w.TarazinoCategoriesVue) {
                w.TarazinoCategoriesVue.setDeletedStatus(text, isError);
                return;
            }

            const [bodyId, paginationId] = ids(kind);
            const body = $(bodyId);
            body.replaceChildren();

            const cell = body.insertRow().insertCell();
            cell.colSpan = kind === 'active' ? 4 : 5;
            cell.className = 'text-center py-5 ' +
                (isError ? 'text-danger' : 'text-body-secondary');
            cell.textContent = text;

            if (hidePagination) {
                $(paginationId).classList.add('d-none');
            }

            if (retry) {
                const button = d.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-sm btn-outline-primary ms-2';
                button.textContent = 'تلاش دوباره';
                button.addEventListener('click', retry);
                cell.appendChild(button);
            }
        }

        function renderPagination(kind) {
            const [, prefix] = ids(kind);
            const pagination = s.pagination[kind];

            if (!pagination) {
                $(prefix).classList.add('d-none');
                return;
            }

            const currentPage = Number(pagination.current_page) || 1;
            const totalPages = Number(pagination.total_pages) || 0;
            const total = Number(pagination.total) || 0;
            const perPage = Number(pagination.per_page) || 10;
            const vueLoading = kind === 'active' &&
                w.TarazinoCategoriesVue &&
                w.TarazinoCategoriesVue.loading;

            $(prefix).classList.toggle('d-none', totalPages <= 1);

            const summary = $(prefix + 'Summary');

            if (total === 0) {
                summary.textContent = 'موردی وجود ندارد';
            } else {
                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(currentPage * perPage, total);

                summary.textContent =
                    `نمایش ${fmt().integer(start)} تا ` +
                    `${fmt().integer(end)} از ${fmt().integer(total)}`;
            }

            const pageList = $(prefix + 'List');
            pageList.replaceChildren();

            if (totalPages <= 1) return;

            function pageButton(text, page, disabled, selected = false) {
                const li = d.createElement('li');
                const button = d.createElement('button');
                const isDisabled = disabled || Boolean(vueLoading);

                li.className = 'page-item' +
                    (isDisabled ? ' disabled' : '') +
                    (selected ? ' active' : '');

                button.type = 'button';
                button.className = 'page-link';
                button.textContent = text;
                button.disabled = isDisabled;

                if (selected) {
                    button.setAttribute('aria-current', 'page');
                }

                button.addEventListener('click', () => {
                    changePage(kind, page);
                });

                li.appendChild(button);
                pageList.appendChild(li);
            }

            pageButton('قبلی', currentPage - 1, currentPage === 1);

            const first = Math.max(
                1,
                Math.min(currentPage - 2, totalPages - 4)
            );

            for (
                let page = first;
                page <= Math.min(totalPages, first + 4);
                page++
            ) {
                pageButton(
                    fmt().integer(page),
                    page,
                    false,
                    page === currentPage
                );
            }

            pageButton('بعدی', currentPage + 1, currentPage === totalPages);
        }

        function visual(category) {
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

            const match = presets.find(([pattern]) => pattern.test(name));

            return match
                ? match.slice(1)
                : category.type === 'income'
                    ? ['fa-arrow-trend-up', 'text-success']
                    : ['fa-arrow-trend-down', 'text-danger'];
        }

        function render(kind) {
            if (kind === 'active') {
                return;
            }

            if (kind === 'deleted') {
                const vue = w.TarazinoCategoriesVue;
                if (vue) {
                    vue.setDeletedCategories(
                        s.deleted,
                        s.pagination.deleted
                    );
                }
                return;
            }

            const items = s.deleted;

            if (!items.length) {
                empty(
                    'deleted',
                    'دسته‌بندی حذف‌شده‌ای وجود ندارد.',
                    false,
                    null,
                    false
                );
                renderPagination('deleted');
                return;
            }

            const body = $(ids('deleted')[0]);
            body.replaceChildren();

            items.forEach(category => {
                const row = body.insertRow();
                const name = row.insertCell();
                const icon = d.createElement('i');
                const [symbol, color] = visual(category);

                icon.className = 'fa-solid ' + symbol + ' ' + color + ' me-2';
                icon.setAttribute('aria-hidden', 'true');
                name.append(icon, d.createTextNode(category.name || '—'));

                td(row, category.type === 'income' ? 'درآمد' : 'هزینه');
                td(row, fmt().dateTime(category.deleted_at), 'text-nowrap');

                const allowed = category.restore_available === true;

                td(
                    row,
                    allowed ? 'در مهلت بازیابی' : 'مهلت بازیابی تمام شده',
                    allowed ? 'text-success' : 'text-warning'
                );

                action(
                    row.insertCell(),
                    'بازیابی',
                    'restore',
                    category.id,
                    'success'
                ).disabled = !allowed || s.busy;
            });

            renderPagination('deleted');
        }

        function bind() {
            $('categoriesTableBody').addEventListener('click', event => {
                const button = event.target.closest('button[data-action]');
                if (!button) return;

                const category = s.active.find(
                    item => String(item.id) === button.dataset.id
                );
                if (!category) return;

                if (button.dataset.action === 'edit') edit(category);
                if (button.dataset.action === 'delete') askDelete(category);
            });

            $('deletedCategoriesTableBody').addEventListener(
                'click',
                event => {
                    const button = event.target.closest(
                        'button[data-action="restore"]'
                    );

                    if (button && !button.disabled) {
                        restore(button.dataset.id, button);
                    }
                }
            );

            const searchInput = $('categoriesSearch');
            const typeInput = $('categoriesTypeFilter');
            const sortInput = $('categoriesSortFilter');

            if (searchInput) searchInput.disabled = false;
            if (typeInput) typeInput.disabled = false;
            if (sortInput) sortInput.disabled = false;

            const applyButton = $('applyCategoriesFiltersButton');

            if (applyButton) {
                applyButton.addEventListener('click', applyFilters);
            }

            const clearButton = $('clearCategoriesFiltersButton');

            if (clearButton) {
                clearButton.addEventListener('click', clearFilters);
            }

            const note = $('categoriesFiltersNote');

            if (note) {
                note.textContent =
                    'برای اجرای فیلترها، روی «اعمال فیلترها» کلیک کنید.';
            }
        }

        return { bind, render, empty };
    }

    w.TarazinoCategoryList = Object.freeze({ create });
})(window, document);

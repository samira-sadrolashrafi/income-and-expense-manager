/** TARAZINO | Vue 3 categories page composition (Options API). */
'use strict';

const modules = [
    window.TarazinoVueCategoriesList,
    window.TarazinoVueCategoriesForm,
    window.TarazinoVueCategoriesDelete
];

if (!Vue || modules.some(module => !module)) {
    throw new Error('Vue category modules did not load in the expected order.');
}

const PAGE_SIZE = 10;
const DEFAULT_FILTERS = Object.freeze({
    search: '',
    type: 'all',
    sort: 'newest'
});

const options = {
    data() {
        return {
            categories: [],
            loading: true,
            error: '',
            deletedCategories: [],
            deletedLoading: false,
            deletedError: '',
            deletedLoaded: false,
            filters: { ...DEFAULT_FILTERS },
            appliedFilters: { ...DEFAULT_FILTERS },
            pagination: {
                current_page: 1,
                per_page: PAGE_SIZE,
                total: 0,
                total_pages: 0
            },
            deletedPagination: {
                current_page: 1,
                per_page: PAGE_SIZE,
                total: 0,
                total_pages: 0
            },
            showDeleted: false,
            requestId: 0,
            deletedRequestId: 0,
            message: { text: '', type: 'success', seq: 0 },
            form: { id: null, name: '', type: '' },
            formErrors: { name: '', type: '' },
            formMessage: { text: '', type: 'danger', seq: 0 },
            saving: false,
            deleteTarget: null,
            deleteMessage: { text: '', type: 'danger', seq: 0 },
            deleting: false,
            restoringId: null
        };
    },

    computed: Object.assign(
        {},
        ...modules.map(module => module.computed || {})
    ),

    methods: Object.assign(
        {
            setMessage(target, text, type) {
                target.text = text || '';
                target.type = type || 'danger';
                target.seq += 1;
            },

            showMessage(text, type) {
                this.setMessage(this.message, text, type || 'success');
            },

            clearMessage() {
                this.setMessage(this.message, '', 'success');
            }
        },
        ...modules.map(module => module.methods || {})
    ),

    mounted() {
        this.loadCategories().catch(() => { });
    }
};

const app = Vue.createApp(options);
app.mount('#categoriesMain');

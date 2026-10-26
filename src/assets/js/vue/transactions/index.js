/** TARAZINO | Vue 3 transactions page composition (Options API). */
'use strict';

const modules = [
    window.TarazinoVueTransactionsList,
    window.TarazinoVueTransactionsForm,
    window.TarazinoVueTransactionsDelete
];

if (!Vue || modules.some(module => !module)) {
    throw new Error('Vue transaction modules did not load in the expected order.');
}

const PAGE_SIZE = 10;
const DEFAULT_FILTERS = Object.freeze({
    search: '',
    type: 'all',
    category_id: 'all',
    date_from: '',
    date_to: '',
    sort: 'newest'
});

const options = {
    data() {
        return {
            transactions: [],
            loading: true,
            error: '',
            categories: [],
            categoriesLoaded: false,
            deletedTransactions: [],
            deletedLoading: false,
            deletedError: '',
            deletedLoaded: false,
            filters: { ...DEFAULT_FILTERS },
            appliedFilters: { ...DEFAULT_FILTERS },
            pagination: {
                current_page: 1,
                per_page: PAGE_SIZE,
                total_items: 0,
                total_pages: 0
            },
            deletedPagination: {
                current_page: 1,
                per_page: PAGE_SIZE,
                total_items: 0,
                total_pages: 0
            },
            showDeleted: false,
            requestId: 0,
            deletedRequestId: 0,
            message: { text: '', type: 'success', seq: 0 },
            editingId: null,
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
        this.bindFormModal();
        this.loadTransactions().catch(() => { });
    }
};

const app = Vue.createApp(options);
app.mount('#transactionsMain');

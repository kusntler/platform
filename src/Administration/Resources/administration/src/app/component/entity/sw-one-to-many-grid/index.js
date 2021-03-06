import Criteria from 'src/core/data-new/criteria.data';
import template from './sw-one-to-many-grid.html.twig';

export default {
    name: 'sw-one-to-many-grid',
    extendsFrom: 'sw-data-grid',
    template,

    inject: ['repositoryFactory'],

    props: {
        collection: {
            required: true,
            type: Object
        },
        localMode: {
            type: Boolean,
            default: true
        },
        dataSource: {
            type: [Array, Object],
            required: false
        }
    },

    data() {
        return {
            page: 1,
            limit: 25,
            total: 0
        };
    },

    methods: {
        createdComponent() {
            this.$super.createdComponent();

            // assign collection as records for the sw-data-grid
            this.applyResult(this.collection);

            // local mode means, the records are loaded with the parent record
            if (this.localMode) {
                return Promise.resolve();
            }

            // Create repository by collection sources
            // the collection contains the route for the entities /customer/{id}/addresses
            this.repository = this.repositoryFactory.create(
                // product_price
                this.collection.entity,

                // product/{id}/price-rules/
                this.collection.source
            );

            // records contains a pre loaded offset
            if (this.records.length > 0) {
                return Promise.resolve();
            }

            return this.repository.search(this.records.criteria, this.records.context)
                .then(this.applyResult);
        },

        applyResult(result) {
            this.records = result;

            if (result.total) {
                this.total = result.total;
            } else {
                this.total = result.length;
            }

            this.page = result.criteria.page;
            this.limit = result.criteria.limit;
        },

        save(record) {
            if (this.localMode) {
                // records will be saved with the root record
                return Promise.resolve();
            }

            return this.repository.save(record, this.records.context).then(() => {
                return this.repository.search(this.records.criteria, this.records.context)
                    .then(this.applyResult);
            });
        },

        revert() {
            if (this.localMode) {
                return Promise.resolve();
            }

            return this.repository.search(this.records.criteria, this.records.context);
        },

        deleteItem(id) {
            if (this.localMode) {
                this.collection.remove(id);
                // records will be saved with the root record
                return Promise.resolve();
            }

            return this.repository.delete(id, this.records.context).then(() => {
                return this.repository.search(this.records.criteria, this.records.context)
                    .then(this.applyResult);
            });
        },

        sort(column) {
            if (this.localMode) {
                return Promise.resolve();
            }

            this.records.criteria.resetSorting();

            let direction = 'ASC';
            if (this.currentSortBy === column.dataIndex) {
                if (this.currentSortDirection === direction) {
                    direction = 'DESC';
                }
            }

            this.records.criteria.addSorting(
                Criteria.sort(column.dataIndex, direction)
            );

            this.currentSortBy = column.dataIndex;
            this.currentSortDirection = direction;
            return this.repository.search(this.records.criteria, this.records.context)
                .then(this.applyResult);
        },

        paginate(params) {
            if (this.localMode) {
                return Promise.resolve();
            }

            this.records.criteria.setPage(params.page);
            this.records.criteria.setLimit(params.limit);
            return this.repository.search(this.records.criteria, this.records.context)
                .then(this.applyResult);
        }
    }
};

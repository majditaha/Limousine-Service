import pluralize from 'pluralize';
import _ from 'lodash/fp';
import clientConfig from '_shared/services/ClientConfig';

export default function (resource) {
  return {
    created() {
      this.clearFilter(false);
      this.newItem();

      this.applyQueryParamsToFilter();

      // Update params with default params
      this.params = _.assignAll([{}, this.params, this.defaultParams]);

      if (this.getListOnLoad) {
        this.getList();
      }

      // Load item if its id is passed in query params
      const itemId = this.$route.query.itemId;
      if (itemId && this.getItemOnLoad) {
        const loadItem = () => {
          this.getItem(itemId).then(() => {
            this.$emit('item-loaded', itemId);
          });
        };

        // If list is also loaded, then load item after list loading is finished
        if (this.getListOnLoad) {
          this.$once('rest-data-loaded', loadItem);
        }
        else {
          loadItem();
        }
      }
    },

    data() {
      return {
        // Current used resource
        resource,

        // Active resource instance (used in form)
        current: {},

        // Which variable to use to store new resource data
        newResource: 'current',

        // Active resource original instance (used in form)
        originalCurrent: {},

        // List of errors for current resource instance
        errors: {},

        // Default data for new resource instance
        newResourceData: {},

        // List of items coming from query
        data: [],

        // Additional data from query
        meta: {},

        // Query params
        params: {},

        defaultParams: {
          sort: 'id',
          order: 'desc',
        },

        // Filter params
        filter: {},

        // See method substituteKeys
        selectKeys: {},

        // Default params for filter
        filterDefaults: {},

        // Fields that should apply to filter from get params
        filterQueryParams: [],

        // Filter keys that will not be removed if value is default
        // See getParams
        filterAllowedDefaults: [],

        // Pager params
        currentPage: 1,
        itemsPerPage: clientConfig.itemsPerPage != null ? +clientConfig.itemsPerPage : 25,

        // Indicates that data has initially loaded
        dataLoaded: false,

        // Should list be reloaded when page loads
        getListOnLoad: true,

        // Should item be loaded when page loads
        // if itemId is passed in query params
        getItemOnLoad: true,

        // Should list be reloaded when new resource saved
        getListOnCreate: true,

        // Should list be reloaded when resource updated
        getListOnUpdate: true,

        // Should fresh resource be initialized when new resource was created
        makeNewAfterCreate: true,

        // Should fresh resource be initialized when list loads after saving
        makeNewAfterListLoad: false,

        // Flag determining that current record is saving
        saving: false,

        // Flag determining that current record is destroying
        destroying: false,

        // Flag determining that list is loading
        loading: false,

        // Flag determining that item is loading
        itemLoading: false,
      };
    },

    computed: {
      isDataEmpty() {
        return (_.isPlainObject(this.data) && _.isEmpty(this.data)) ||
          this.data.length === 0;
      },
    },

    methods: {

      // Mostly internal method used to collect query params for list method
      // from different sources like general params, filter, pagination, etc.
      getParams(params = {}) {
        // Exclude all keys from filter that are equal to default filter value
        // and not in allowed array
        const filterKeys = Object.keys(this.filter);
        const filteredFilterKeys = _.filter(key =>
          this.filterAllowedDefaults.includes(key) ||
          !_.isEqual(this.filter[key], this.filterDefaults[key])
        )(filterKeys);
        const filter = _.pick(filteredFilterKeys)(this.filter);

        return _.assignAll([{}, this.params, filter, params, {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
        }]);
      },

      // Override this method if you want more control of what params are passed
      // to list query
      beforeQuery(params) {
        return params;
      },

      // Get params from query and add them to filter, if allowed
      applyQueryParamsToFilter() {
        const params = _.pickBy((value, key) =>
          _.includes(key)(this.filterQueryParams)
        )(this.$route.query);

        this.filter = _.assign(this.filter, params);
      },

      // When filtering we do not want to send whole object to server instead of an id
      // But vue-multiselect operates whole objects, so selectFilterKey defines a
      // correspondance of filter name and which key should be taken from it
      // E.g. location => 'id' would say, that filter.location would be changed
      // to filter.location.id
      substituteKeys(params) {
        const paramsKeys = Object.keys(params);
        return _.reduce((obj, key) => {
          const value = params[key];
          const appendKey = this.selectKeys[key];
          const merge = {};
          if (appendKey == null) {
            merge[key] = value;
          }
          else {
            let val;
            let newKey;
            if (_.isArray(value)) {
              val = value.map(item => item[appendKey]);
              newKey = pluralize.singular(key) +
                _.flow(
                  _.curry(pluralize.plural),
                  _.capitalize
                )(appendKey);
            }
            else {
              val = _.has(appendKey)(value) ? value[appendKey] : null;
              newKey = key + _.capitalize(appendKey);
            }
            merge[newKey] = val;
          }
          return _.assignAll([{}, obj, merge]);
        }, {})(paramsKeys);
      },

      // Override this method if you want to customize what is done on page change
      onPageChanged(page) {
        this.currentPage = page;
        this.getList();
      },

      // Override this method if you want to customize what is done
      // when user changes number of displayed items per page
      onItemsPerPageChanged() {
        this.currentPage = 1;
        this.getList();
      },

      // Method to pass to update event of vue-multiselect in filter
      // to prevent too many same looking methods
      onFilterSelected(value, id) {
        this.$set(`filter.${id}`, value);
      },

      // Method to pass to update event of vue-multiselect in current
      // to prevent too many same looking methods
      onCurrentSelected(value, id) {
        this.$set(`current.${id}`, value);
      },

      // Override this method if you want to do something with each item in list
      // after it is loaded
      changeItemAfterLoad(item) {
        return item;
      },

      // Get one item by id
      getItem(id) {
        const queryParams = { id };
        const params = this.getParams();
        if (params.with != null) {
          queryParams.with = params.with;
        }
        this.itemLoading = true;
        return this.resource.get(queryParams).then((response) => {
          if (response.body.data != null) {
            this.current = response.body.data;
          }
          else {
            this.current = response.body;
          }
          this.current = this.changeItemAfterLoad(this.current);
        }).finally(() => {
          this.itemLoading = false;
        });
      },

      // Load list of items
      // Basically should not be overriden
      // Emits rest-data-loaded event
      getList(params = {}) {
        this.loading = true;

        const queryParams = _.flow(
          _.curry(this.getParams),
          _.curry(this.substituteKeys),
          _.curry(this.beforeQuery)
        )(params);

        return this.resource.query(queryParams).then((response) => {
          this.data = response.body.data;

          // Copy response meta to this.meta, in case of conflict response wins
          this.meta = _.assignAll([{}, this.meta, response.body.meta]);

          this.data = this.data.map(this.changeItemAfterLoad.bind(this));

          this.$emit('rest-data-loaded', queryParams);

          this.dataLoaded = true;
        }).finally(() => {
          this.loading = false;
        });
      },

      // Get filtered data
      applyFilter() {
        this.currentPage = 1;
        this.getList();
      },

      // Set filter to its default state
      clearFilter(getList = true) {
        this.$set(this, 'filter', JSON.parse(JSON.stringify(this.filterDefaults)));
        if (getList) {
          this.getList();
        }
        this.$emit('clear-filter');
      },

      // Prepare form for creating new resource
      newItem() {
        this.makeNew();
        this.errors = {};
        this.$emit('new');
      },

      // Prepare form for editing a resource
      edit(item) {
        this.current = JSON.parse(JSON.stringify(item));
        this.originalCurrent = JSON.parse(JSON.stringify(item));
        this.errors = {};
        this.$emit('editing');
      },

      // Restore original item
      restoreOriginal() {
        if (this.originalCurrent.id != null) {
          this.current = _.assignAll([{}, this.originalCurrent]);
        }
        else {
          this.makeNew();
        }
        this.$emit('original-restored');
      },

      // Override this method if you want to do something before item is destroyed
      beforeDestroy(item) {
        return item;
      },

      destroy(item) {
        if (this.destroying) {
          return false;
        }

        this.destroying = true;

        let itemToDestroy = null;

        if (_.isInteger(item)) {
          itemToDestroy = { id: item };
        }
        else {
          itemToDestroy = item;
        }

        itemToDestroy = this.beforeDestroy(itemToDestroy);

        this.$emit('destroying', itemToDestroy);

        return this.resource.remove(itemToDestroy).then(() => {
          this.$emit('destroyed');
          this.getList();
        }).finally(() => {
          this.destroying = false;
        });
      },

      // First confirm, then destroy
      confirmDestroy(item, message = 'Вы уверены?') {
        this.$modals.confirm({
          message,
          parent: this.resource.name,
          onApprove: () => {
            this.destroy(item);
          },
        });
      },

      // Override this method if you want to do something before item is saved
      beforeSave(item) {
        return item;
      },

      // Save record
      // If no item is passed, current record will be saved
      save(item) {
        if (this.saving) {
          return false;
        }

        this.saving = true;

        let itemToSave = null;
        if (item != null) {
          itemToSave = item;
        }
        else {
          itemToSave = this.current;
        }

        itemToSave = _.flow(
          _.curry(this.substituteKeys),
          _.curry(this.beforeSave)
        )(itemToSave);

        this.errors = {};

        let promise;
        if (itemToSave.id != null) {
          promise = this.resource.update({ id: itemToSave.id }, itemToSave);
        }
        else {
          promise = this.resource.save({ id: null }, itemToSave);
        }

        return promise.then((response) => {
          const canLoadList =
            (itemToSave.id == null && this.getListOnCreate) ||
            (itemToSave.id != null && this.getListOnUpdate);

          const canInitNew =
            itemToSave.id == null &&
            itemToSave === this.current &&
            this.makeNewAfterCreate;

          let updatedCurrent = response.body;

          if (updatedCurrent.data != null) {
            updatedCurrent = updatedCurrent.data;
          }

          if (canInitNew) {
            this.newItem();
          }

          if (canLoadList) {
            this.getList().then(() => {
              if (this.removeCurrentAfterListLoad) {
                this.current = null;
              }
            });
          }

          if (itemToSave.id) {
            this.$emit('updated', itemToSave.id);
          }
          else {
            itemToSave.id = updatedCurrent.id;
            this.$emit('created', updatedCurrent.id);
          }

          return itemToSave;
        }).catch((response) => {
          this.errors = response.body.errors;
          this.$emit('save-error', this.errors);
          throw response;
        }).finally(() => {
          // Close modal after 250 ms so modal has time to hide
          // fix for flash (super hero) users
          setTimeout(() => {
            this.saving = false;
          }, 250);
        });
      },

      // Get errors by path to them
      // Path should exclude 'errors'
      // This is needed if there is no such path, because
      // vue says it is undefined
      getErrors(path) {
        return _.get(this.errors, path);
      },

      makeNew() {
        this[this.newResource] = JSON.parse(JSON.stringify(this.newResourceData));
      },
    },
  };
}

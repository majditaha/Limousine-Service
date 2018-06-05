<template>
  <div>
    <bs-filter name="sections" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Пользователь</label>
          <select class="form-control" v-model="filter.userId">
            <option v-for="user in meta.users" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Тип</label>
          <select class="form-control" v-model="filter.type">
            <option v-for="type in types" :value="type">{{ type | translateType }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Дата с</label>
          <datepicker v-model="filter.createdAtFrom"></datepicker>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Дата по</label>
          <datepicker v-model="filter.createdAtTo"></datepicker>
        </div>
      </div>
    </bs-filter>

    <loading-indicator :loading="loading">
      <div class="row">
        <div class="col-xs-12">
          <div class="form-group">
            <label>Баланс за период:</label>
            {{ meta.totalBalance }} р.
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th v-rest-sortable-column="'id'">#</th>
              <th v-rest-sortable-column="'from_user_id'">Имя</th>
              <th v-rest-sortable-column="'created_at'">Дата платежа</th>
              <th v-rest-sortable-column="'type'">Тип</th>
              <th v-rest-sortable-column="'amount'">Сумма</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data">
              <td>{{ item.id }}</td>
              <td>{{ item.fromUserId | valueById(meta.users) }}</td>
              <td>{{ item.createdAt | format('ruTime') }}</td>
              <td>{{ item.type | translateType }}</td>
              <td>{{ item.amount | amountBasedOnType(item.type) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </loading-indicator>

    <pager
      :total-items="meta.total"
      :current-page="currentPage"
      :page-size="itemsPerPage"
      @page-changed="onPageChanged"
    ></pager>

  </div>
</template>
<script>
import rest from '_shared/mixins/rest';
import Transaction from '_shared/resources/admin/Transaction';
import _ from 'lodash/fp';
import clientConfig from '_shared/services/ClientConfig';
import valueById from '_shared/filters/valueById';
import format from '_shared/filters/format';
import restSortableColumn from '_shared/directives/restSortableColumn';

export default {

  mixins: [rest(Transaction)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Datepicker: () => import('_components/datepicker'),
  },

  directives: {
    restSortableColumn,
  },

  filters: {
    valueById,
    format,
    translateType(type) {
      switch (type) {
        case 'input':
          return 'Внесение';
        case 'payment':
          return 'Оплата преподавателю';
        case 'withdrawal':
          return 'Вывод';
        case 'all':
          return 'Все';
        default:
          return 'Неизвестно';
      }
    },

    amountBasedOnType(amount, type) {
      switch (type) {
        case 'input':
          return `+${amount}`;
        case 'withdrawal':
          return `-${amount}`;
        default:
          return amount;
      }
    },
  },

  data() {
    return {
      filterDefaults: {
        userId: null,
        createdAtFrom: null,
        createdAtTo: null,
        type: null,
      },

      filterQueryParams: [
        'userId',
      ],

      defaultParams: {
        sort: 'created_at',
        order: 'desc',
      },

      meta: {
        users: [],
        types: clientConfig.transactionTypes,
        totalBalance: 0,
      },
    };
  },

  computed: {
    types() {
      return _.concat(this.meta.types, ['all']);
    },
  },

};
</script>

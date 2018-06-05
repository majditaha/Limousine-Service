<template>
  <div>
    <bs-filter name="cities" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-12">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control" />
        </div>
      </div>
    </bs-filter>

    <button type="button" class="btn btn-success" @click="newItem()">+ Добавить</button>

    <loading-indicator :loading="loading">
      <div class="table-responsive">
        <table class="table table-striped table-selectable">
          <thead>
            <tr>
              <th v-rest-sortable-column="'id'">#</th>
              <th v-rest-sortable-column="'name'">Наименование</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
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

    <vudal name="city">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание города</h4>
        <h4 class="modal-title" v-else>Редактирование города</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Город</label>
                <input type="text" v-model="current.name" class="form-control">
                <error-messages :errors="errors.name"></error-messages>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-default cancel">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-md" @click="save()">Сохранить</button>
        <button type="button" class="btn btn-danger btn-md" @click="confirmDestroy(current.id)" v-if="current.id != null">Удалить</button>
      </div>
    </vudal>
  </div>
</template>
<script>
import rest from '_shared/mixins/rest';
import City from '_shared/resources/admin/City';
import restSortableColumn from '_shared/directives/restSortableColumn';

export default {

  mixins: [rest(City)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
  },

  directives: {
    restSortableColumn,
  },

  data() {
    return {
      filterDefaults: {
        name: null,
      },
    };
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.city.$show();
    });
    this.$on('editing', () => {
      this.$modals.city.$show();
    });
    this.$on('created', () => {
      this.$modals.city.$hide();
    });
    this.$on('updated', () => {
      this.$modals.city.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.city.$hide();
    });
  },

};
</script>

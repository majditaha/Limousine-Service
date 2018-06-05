<template>
  <div>
    <bs-filter name="schools" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Город</label>
          <select class="form-control" v-model="filter.cityId">
            <option v-for="city in meta.cities" :value="city.id">{{ city.name }}</option>
          </select>
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
              <th v-rest-sortable-column="'city_id'">Город</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.cityId | valueById(meta.cities) }}</td>
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

    <vudal name="school">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание школы</h4>
        <h4 class="modal-title" v-else>Редактирование школы</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Город:</label>
                <select class="form-control" v-model="current.cityId">
                  <option v-for="city in meta.cities" :value="city.id" >{{ city.name }}</option>
                </select>
                <error-messages :errors="errors.cityId"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Школа:</label>
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
import School from '_shared/resources/admin/School';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';

export default {

  mixins: [rest(School)],

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
        cityId: null,
      },
      meta: {
        schools: [],
      },
    };
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.school.$show();
    });
    this.$on('editing', () => {
      this.$modals.school.$show();
    });
    this.$on('created', () => {
      this.$modals.school.$hide();
    });
    this.$on('updated', () => {
      this.$modals.school.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.school.$hide();
    });
  },

  filters: {
    valueById,
  },

};
</script>

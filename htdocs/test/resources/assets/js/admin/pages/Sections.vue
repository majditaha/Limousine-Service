<template>
  <div>
    <bs-filter name="sections" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Предмет</label>
          <select class="form-control" v-model="filter.disciplineId">
            <option v-for="discipline in meta.disciplines" :value="discipline.id">{{ discipline.name }}</option>
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
              <th v-rest-sortable-column="'discipline_id'">Предмет</th>
              <th v-rest-sortable-column="'order'">Порядок</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.disciplineId | valueById(meta.disciplines) }}</td>
              <td>{{ item.order }}</td>
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

    <vudal name="section">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание блока</h4>
        <h4 class="modal-title" v-else>Редактирование блока</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Наименование:</label>
                <input type="text" v-model="current.name" class="form-control">
                <error-messages :errors="errors.name"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Предмет:</label>
                <select class="form-control" v-model="current.disciplineId">
                  <option v-for="discipline in meta.disciplines" :value="discipline.id" >{{ discipline.name }}</option>
                </select>
                <error-messages :errors="errors.disciplineId"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Порядок сортировки:</label>
                <!-- TODO: improve UX to select and update order -->
                <input type="text" v-model="current.order" class="form-control">
                <error-messages :errors="errors.order"></error-messages>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-default cancel">Закрыть</button>
        <button type="submit" data-save-btn class="btn btn-primary btn-md" @click="save()">Сохранить</button>
        <button type="button" class="btn btn-danger btn-md" @click="confirmDestroy(current.id)" v-if="current.id != null">Удалить</button>
      </div>
    </vudal>
  </div>
</template>
<script>
import rest from '_shared/mixins/rest';
import Section from '_shared/resources/admin/Section';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';

export default {

  mixins: [rest(Section)],

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
        disciplineId: null,
      },

      meta: {
        disciplines: [],
      },

      defaultParams: {
        sort: 'order',
        order: 'asc',
      },
    };
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.section.$show();
    });
    this.$on('editing', () => {
      this.$modals.section.$show();
    });
    this.$on('created', () => {
      this.$modals.section.$hide();
    });
    this.$on('updated', () => {
      this.$modals.section.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.section.$hide();
    });
  },

  filters: {
    valueById,
  },

  methods: {
    filterFilterDisciplines() {
      this.filterDisciplines = this.meta.disciplines.filter(section =>
        section.disciplineId === this.filter.disciplineId
      );
    },
  },

};
</script>

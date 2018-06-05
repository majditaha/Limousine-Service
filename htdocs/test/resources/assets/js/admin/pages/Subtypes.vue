<template>
  <div>
    <bs-filter name="subtypes" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Блок</label>
          <select class="form-control" v-model="filter.sectionId">
            <option v-for="section in meta.sections" :value="section.id">{{ section.name }}</option>
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
              <th>Блок</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.sectionId | valueById(meta.sections) }}</td>
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

    <vudal name="subtype">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание подтипа</h4>
        <h4 class="modal-title" v-else>Редактирование подтипа</h4>
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
                <label>Блок:</label>
                <select class="form-control" v-model="current.sectionId">
                  <option v-for="section in meta.sections" :value="section.id">{{ section.name }}</option>
                </select>
                <error-messages :errors="errors.sectionId"></error-messages>
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
import Subtype from '_shared/resources/admin/Subtype';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';
import QuillEditor from '_components/quillEditor';

export default {

  mixins: [rest(Subtype)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
    QuillEditor,
  },

  directives: {
    restSortableColumn,
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.subtype.$show();
    });
    this.$on('editing', () => {
      this.$modals.subtype.$show();
    });
    this.$on('created', () => {
      this.$modals.subtype.$hide();
    });
    this.$on('updated', () => {
      this.$modals.subtype.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.subtype.$hide();
    });
  },

  filters: {
    valueById,
  },

  data() {
    return {
      filterDefaults: {
        name: null,
        sectionId: null,
      },

      meta: {
        sections: [],
      },

    };
  },

};
</script>

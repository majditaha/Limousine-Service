<template>
  <div>
    <bs-filter name="pages" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
        <div class="form-group">
          <label>В тексте содержится</label>
          <input type="text" v-model="filter.content" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Раздел сайта</label>
          <select class="form-control" v-model="filter.menuItemId">
            <option v-for="menuItem in meta.menuItems" :value="menuItem.id">{{ menuItem.name }}</option>
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
              <th v-rest-sortable-column="'menu_item_id'">Раздел сайта</th>
              <th v-rest-sortable-column="'order'">Порядок</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.menuItemId | valueById(meta.menuItems) }}</td>
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

    <vudal name="page">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание текста</h4>
        <h4 class="modal-title" v-else>Редактирование текста</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>Наименование:</label>
                <input type="text" v-model="current.name" class="form-control" :disabled="isAgreement">
                <error-messages :errors="errors.name"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Раздел сайта:</label>
                <select class="form-control" v-model="current.menuItemId">
                  <option v-for="menuItem in meta.menuItems" :value="menuItem.id" >{{ menuItem.name }}</option>
                </select>
                <error-messages :errors="errors.menuItemId"></error-messages>
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
            <div class="col-xs-12">
              <div class="form-group">
                <label>Текст:</label>
                <quill-editor v-model="current.content"></quill-editor>
                <error-messages :errors="errors.content"></error-messages>
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
import Page from '_shared/resources/admin/Page';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';
import QuillEditor from '_components/quillEditor';

export default {

  mixins: [rest(Page)],

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

  data() {
    return {
      filterDefaults: {
        name: null,
        text: null,
        menuItemId: null,
      },

      meta: {
        menuItems: [],
      },

      defaultParams: {
        sort: 'order',
        order: 'asc',
      },
    };
  },

  computed: {
    isAgreement() {
      return this.current.name === 'Пользовательское соглашение';
    },
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.page.$show();
    });
    this.$on('editing', () => {
      this.$modals.page.$show();
    });
    this.$on('created', () => {
      this.$modals.page.$hide();
    });
    this.$on('updated', () => {
      this.$modals.page.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.page.$hide();
    });
  },

  filters: {
    valueById,
  },

};
</script>

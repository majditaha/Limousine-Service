<template>
  <div>
    <bs-filter name="theories" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
        <div class="form-group">
          <label>В тексте содержится</label>
          <input type="text" v-model="filter.text" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Блок</label>
          <select class="form-control" v-model="filter.sectionId">
            <option v-for="section in filterSections" :value="section.id">{{ section.name }}</option>
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
              <th>Предмет</th>
              <th>Блок</th>
              <th v-rest-sortable-column="'order'">Порядок</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.section.disciplineId | valueById(meta.disciplines) }}</td>
              <td>{{ item.section.name }}</td>
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

    <vudal name="theory">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание теории</h4>
        <h4 class="modal-title" v-else>Редактирование теории</h4>
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
                <select class="form-control" v-model="current.discipline">
                  <option v-for="discipline in meta.disciplines" :value="discipline">{{ discipline.name }}</option>
                </select>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Блок:</label>
                <select class="form-control" v-model="current.sectionId">
                  <option v-for="section in currentSections" :value="section.id">{{ section.name }}</option>
                </select>
                <error-messages :errors="errors.sectionId"></error-messages>
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
                <quill-editor v-model="current.text"></quill-editor>
                <error-messages :errors="errors.text"></error-messages>
              </div>
            </div>
            <div class="col-xs-9">
              <div class="form-group">
                <label>Текст в PDF:</label>
                <input type="text" v-model="current.textPdfName" class="form-control" disabled>
                <input type="hidden" v-model="current.textPdf" class="form-control">
                <error-messages :errors="errors.textPdf"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="uploadcare-btn">
                <cloudinary @upload="onPdfUploaded" @fail="onUploadFail"></cloudinary>
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
import _ from 'lodash/fp';
import rest from '_shared/mixins/rest';
import Theory from '_shared/resources/admin/Theory';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';
import QuillEditor from '_components/quillEditor';

export default {

  mixins: [rest(Theory)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
    QuillEditor,
    Cloudinary: () => import('_components/cloudinary'),
  },

  directives: {
    restSortableColumn,
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.theory.$show();
    });
    this.$on('editing', () => {
      const discipline = _.find(disc =>
        disc.id === this.current.section.disciplineId
      )(this.meta.disciplines);

      this.$set(this.current, 'discipline', discipline);

      this.$set(this.current, 'textPdfName', this.current.textPdf.originalFilename);

      this.$modals.theory.$show();
    });
    this.$on('created', () => {
      this.$modals.theory.$hide();
    });
    this.$on('updated', () => {
      this.$modals.theory.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.theory.$hide();
    });
  },

  filters: {
    valueById,
  },

  data() {
    return {
      filterDefaults: {
        name: null,
        text: null,
        sectionId: null,
      },

      meta: {
        disciplines: [],
      },

      defaultParams: {
        sort: 'order',
        order: 'asc',
        with: 'section',
      },

      newResourceData: {
        discipline: {},
      },

      errors: {},
    };
  },

  computed: {
    currentSections() {
      return this.current.discipline != null
        ? this.current.discipline.sections
        : [];
    },

    filterSections() {
      return _.flatMap(discipline => discipline.sections)(this.meta.disciplines);
    },
  },

  methods: {
    onPdfUploaded(file) {
      if (file.format === 'pdf') {
        this.errors = {};
        this.$set(this.current, 'textPdfName', `${file.originalFilename}.${file.format}`);
        this.$set(this.current, 'textPdf', file);
      }
      else {
        this.errors = {};
        this.$set(this.errors, 'textPdfName', 'Загруженный файл не является документом в формате PDF');
      }
    },

    onUploadFail(error) {
      this.errors = {};
      this.errors.textPdf = error;
    },
  },
};
</script>

<style>
  .uploadcare-btn {
    margin-top: 18px;
  }
</style>
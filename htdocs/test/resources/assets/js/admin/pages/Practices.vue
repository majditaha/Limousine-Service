<template>
  <div>
    <bs-filter name="practices" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Наименование</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
        <div class="form-group">
          <label>В тексте содержится</label>
          <input type="text" v-model="filter.text" class="form-control">
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Тип</label>
              <select class="form-control" v-model="filter.type">
                <option v-for="type in meta.types" :value="type">{{ type | translateType }}</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label>Подтип</label>
              <select class="form-control" v-model="filter.subtypeId">
                <option v-for="subtype in filterSubtypes" :value="subtype.id">{{ subtype.name }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Предмет</label>
          <select class="form-control" v-model="filter.disciplineId" @change="onFilterDisciplineSelected">
            <option v-for="discipline in meta.disciplines" :value="discipline.id">{{ discipline.name }}</option>
          </select>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Блок</label>
              <select class="form-control" v-model="filter.sectionId" @change="onFilterSectionSelected">
                <option v-for="section in filterSections" :value="section.id">{{ section.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label>Вариант</label>
              <select class="form-control" v-model="filter.variantId">
                <option v-for="variant in filterVariants" :value="variant.id">{{ variant.name }}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Теория</label>
          <select class="form-control" v-model="filter.theoryId">
            <option v-for="theory in filterTheories" :value="theory.id">{{ theory.name }}</option>
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
              <th>Теория</th>
              <th>Предмет</th>
              <th>Блок</th>
              <th v-rest-sortable-column="'order'">Порядок</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.theoryId | valueById(meta.theories) }}</td>
              <td>{{ item.disciplineId | valueById(meta.disciplines) }}</td>
              <td>{{ item.sectionId | valueById(meta.sections) }}</td>
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

    <vudal name="practice">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание практики</h4>
        <h4 class="modal-title" v-else>Редактирование практики</h4>
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
            <div class="col-xs-4">
              <div class="form-group">
                <label>Тип:</label>
                <select class="form-control" v-model="current.type" :disabled="current.id != null">
                  <option v-for="type in meta.types" :value="type">{{ type | translateType }}</option>
                </select>
                <error-messages :errors="errors.type"></error-messages>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="form-group">
                <label>Цена в XP:</label>
                <!-- TODO: improve UX to select and update order -->
                <input type="number" v-model="current.xpGain" class="form-control">
                <error-messages :errors="errors.xpGain"></error-messages>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="form-group">
                <label>Порядок сортировки:</label>
                <!-- TODO: improve UX to select and update order -->
                <input type="text" v-model="current.order" class="form-control">
                <error-messages :errors="errors.order"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                <label>Предмет:</label>
                <select class="form-control" v-model="current.disciplineId" @change="onCurrentDisciplineSelected">
                  <option v-for="discipline in meta.disciplines" :value="discipline.id">{{ discipline.name }}</option>
                </select>
                <error-messages :errors="errors.disciplineId"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                <label>Блок:</label>
                <select
                  class="form-control"
                  v-model="current.sectionId"
                  @change="onCurrentSectionSelected"
                  :disabled="current.type === 'testing' || current.type === 'ege'"
                >
                  <option v-for="section in currentSections" :value="section.id">{{ section.name }}</option>
                </select>
                <error-messages :errors="errors.sectionId"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                <label>Вариант:</label>
                <select class="form-control" v-model="current.variantId" :disabled="current.type !== 'testing' && current.type !== 'ege'">
                  <option v-for="variant in currentVariants" :value="variant.id">{{ variant.name }}</option>
                </select>
                <error-messages :errors="errors.variantId"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                <label>Теория:</label>
                <select class="form-control" v-model="current.theoryId" :disabled="current.type !== 'theory' && current.type !== 'practice'">
                  <option v-for="theory in currentTheories" :value="theory.id">{{ theory.name }}</option>
                </select>
                <error-messages :errors="errors.theoryId"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Подтип:</label>
                <select class="form-control" v-model="current.subtypeId" :disabled="!current.sectionId">
                  <option v-for="subtype in currentSubtypes" :value="subtype.id">{{ subtype.name }}</option>
                </select>
                <error-messages :errors="errors.subtype"></error-messages>
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
                <cloudinary @upload="onPdfUploaded('textPdf', $event)" @fail="onUploadFail('textPdf', $event)"></cloudinary>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Подсказка:</label>
                <quill-editor v-model="current.hint"></quill-editor>
                <error-messages :errors="errors.hint"></error-messages>
              </div>
            </div>
            <div class="col-xs-9">
              <div class="form-group">
                <label>Подсказка в PDF:</label>
                <input type="text" v-model="current.hintPdfName" class="form-control" disabled>
                <input type="hidden" v-model="current.hintPdf" class="form-control">
                <error-messages :errors="errors.hintPdf"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="uploadcare-btn">
                <cloudinary @upload="onPdfUploaded('hintPdf', $event)" @fail="onUploadFail('hintPdf', $event)"></cloudinary>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Решение:</label>
                <quill-editor v-model="current.solution"></quill-editor>
                <error-messages :errors="errors.solution"></error-messages>
              </div>
            </div>
            <div class="col-xs-9">
              <div class="form-group">
                <label>Решение в PDF:</label>
                <input type="text" v-model="current.solutionPdfName" class="form-control" disabled>
                <input type="hidden" v-model="current.solutionPdf" class="form-control">
                <error-messages :errors="errors.solutionPdf"></error-messages>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="uploadcare-btn">
                <cloudinary @upload="onPdfUploaded('solutionPdf', $event)" @fail="onUploadFail('solutionPdf', $event)"></cloudinary>
              </div>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
            <div class="col-xs-4">
              <div class="form-group">
                <label>Тип ответа:</label>
                <select class="form-control" v-model="current.answerType">
                  <option v-for="answerType in meta.answerTypes" :value="answerType">{{ answerType | translateAnswerType }}</option>
                </select>
                <error-messages :errors="errors.answerType"></error-messages>
              </div>
            </div>
            <div class="col-xs-8">
              <div class="form-group">
                <practice-answer-editor :type="current.answerType" :answers.sync="current.answers" :errors="errors"></practice-answer-editor>
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
import Practice from '_shared/resources/admin/Practice';
import clientConfig from '_shared/services/ClientConfig';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';
import PracticeAnswerEditor from '_shared/components/practiceAnswers/editor';
import QuillEditor from '_components/quillEditor';

export default {

  mixins: [rest(Practice)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
    QuillEditor,
    PracticeAnswerEditor,
    Cloudinary: () => import('_components/cloudinary'),
  },

  directives: {
    restSortableColumn,
  },

  filters: {
    valueById,

    translateType(type) {
      switch (type) {
        case 'theory':
          return 'Закрепление теории';
        case 'practice':
          return 'Практика';
        case 'testing':
          return 'Тест';
        case 'ege':
          return 'Тест ЕГЭ';
        default:
          return 'Неизвестно';
      }
    },

    translateAnswerType(type) {
      switch (type) {
        case 'single_choice':
          return 'Одиночный выбор';
        case 'multiple_choice':
          return 'Множественный выбор';
        case 'single_value':
          return 'Ввод значения';
        case 'multiple_value':
          return 'Ввод нескольких значений';
        case 'text':
          return 'Текст';
        case 'matching':
          return 'Сопоставление ответов';
        default:
          return 'Неизвестно';
      }
    },
  },

  mounted() {
    this.$on('new', () => {
      this.$set(this.current, 'answerType', 'single_choice');
      this.$modals.practice.$show();
    });
    this.$on('editing', () => {
      this.onCurrentDisciplineSelected();
      this.onFilterDisciplineSelected();
      this.onCurrentSectionSelected();
      this.onFilterSectionSelected();
      if (this.current.answers == null) {
        this.current.answers = [];
      }

      const textPdfName =
        this.current.textPdf != null && this.current.textPdf.originalFilename != null
          ? this.current.textPdf.originalFilename
          : this.current.textPdf.urlPdf;
      this.$set(this.current, 'textPdfName', textPdfName);

      const hintPdfName =
        this.current.hintPdf != null && this.current.hintPdf.originalFilename != null
          ? this.current.hintPdf.originalFilename
          : this.current.hintPdf.urlPdf;
      this.$set(this.current, 'hintPdfName', hintPdfName);

      const solutionPdfName =
        this.current.solutionPdf != null && this.current.solutionPdf.originalFilename != null
          ? this.current.solutionPdf.originalFilename
          : this.current.solutionPdf.urlPdf;
      this.$set(this.current, 'solutionPdfName', solutionPdfName);

      this.$modals.practice.$show();
    });
    this.$on('created', () => {
      this.$modals.practice.$hide();
    });
    this.$on('updated', () => {
      this.$modals.practice.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.practice.$hide();
    });
  },

  data() {
    return {
      filterDefaults: {
        name: null,
        text: null,
        disciplineId: null,
        sectionId: null,
        theoryId: null,
        subtypeId: null,
        type: null,
      },

      defaultParams: {
        sort: 'order',
        order: 'asc',
        with: 'answers',
      },

      meta: {
        disciplines: [],
        sections: [],
        variants: [],
        theories: [],
        subtypes: [],
        types: clientConfig.practiceTypes,
        answerTypes: clientConfig.answerTypes,
      },

      newResourceData: {
        answers: [],
        xpGain: 0,
        type: 'theory',
      },

      filterSections: [],
      filterVariants: [],
      filterTheories: [],
      filterSubtypes: [],

      currentSections: [],
      currentVariants: [],
      currentTheories: [],
      currentSubtypes: [],

      errors: {},
    };
  },

  methods: {
    onFilterDisciplineSelected() {
      this.filterSections = this.meta.sections.filter(section =>
        section.disciplineId === this.filter.disciplineId
      );

      this.filterVariants = this.meta.variants.filter(variant =>
        variant.disciplineId === this.filter.disciplineId
      );
    },

    onFilterSectionSelected() {
      this.filterTheories = this.meta.theories.filter(theory =>
        theory.sectionId === this.filter.sectionId
      );

      this.filterSubtypes = this.meta.subtypes.filter(subtype =>
        subtype.sectionId === this.filter.sectionId
      );
    },

    onCurrentDisciplineSelected() {
      this.currentSections = this.meta.sections.filter(section =>
        section.disciplineId === this.current.disciplineId
      );

      this.currentVariants = this.meta.variants.filter(variant =>
        variant.disciplineId === this.current.disciplineId
      );
    },

    onCurrentSectionSelected() {
      this.currentTheories = this.meta.theories.filter(theory =>
        theory.sectionId === this.current.sectionId
      );

      this.currentSubtypes = this.meta.subtypes.filter(subtype =>
        subtype.sectionId === this.current.sectionId
      );
    },

    onPdfUploaded(field, file) {
      if (file.format === 'pdf') {
        this.$set(this.current, `${field}Name`, `${file.originalFilename}.${file.format}`);
        this.$set(this.current, field, file);
      }
      else {
        this.errors = {};
        this.$set(this.errors, field, 'Загруженный файл не является документом в формате PDF');
      }
    },

    onUploadFail(field, error) {
      this.errors = {};
      this.$set(this.errors, field, error);
    },
  },

  watch: {
    'current.answerType'(newVal, oldVal) {
      if (newVal !== oldVal && oldVal != null && newVal === 'single_value') {
        this.current.answers = [];
      }
    },
  },

};
</script>

<style>
  .uploadcare-btn {
    margin-top: 18px;
  }
</style>

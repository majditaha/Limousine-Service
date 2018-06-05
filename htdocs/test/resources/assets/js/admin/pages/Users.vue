<template>
  <div>
    <div class="row bottom15" v-if="meta.inactiveCount > 0">
      <div class="col-xs-12">
        <strong class="inactive-count">Неактивных пользователей: {{ meta.inactiveCount }}</strong>
        <a href="#" @click.prevent="findInactive()">Найти</a>
      </div>
    </div>
    <bs-filter name="users" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-6">
        <div class="form-group">
          <label>Имя</label>
          <input type="text" v-model="filter.name" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Email</label>
          <input type="text" v-model="filter.email" class="form-control">
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Пол</label>
          <div>
            <input type="radio" v-model="filter.gender" :value="1">
            <label>Мужской</label>
            <input type="radio" v-model="filter.gender" :value="0">
            <label>Женский</label>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Дата рождения с</label>
              <datepicker v-model="filter.birthDateFrom"></datepicker>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label>Дата рождения по</label>
              <datepicker v-model="filter.birthDateTo"></datepicker>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Тип</label>
          <select class="form-control" v-model="filter.role">
            <option v-for="type in meta.types" :value="type">{{ type | translateType }}</option>
          </select>
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
      <div class="col-xs-6">
        <div class="form-group">
          <label>Школа</label>
          <select class="form-control" v-model="filter.schoolId">
            <option v-for="school in schools" :value="school.id">{{ school.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Класс</label>
              <select class="form-control" v-model="filter.grade">
                <option v-for="grade in meta.grades" :value="grade">{{ grade }}</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="form-group">
              <label>&nbsp;</label>
              <select class="form-control" v-model="filter.gradeName">
                <option v-for="gradeName in meta.gradeNames" :value="gradeName">{{ gradeName }}</option>
              </select>
            </div>
          </div>
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
              <th v-rest-sortable-column="'name'">Имя</th>
              <th v-rest-sortable-column="'birth_date'">Дата рождения</th>
              <th v-rest-sortable-column="'city_id'">Город</th>
              <th v-rest-sortable-column="'school_id'">Школа</th>
              <th v-rest-sortable-column="'grade'">Класс</th>
              <th v-rest-sortable-column="'role'">Тип</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.birthDate | format('ru') }}</td>
              <td>{{ item.cityId | valueById(meta.cities) }}</td>
              <td>{{ item.schoolId | valueById(schools) }}</td>
              <td>{{ item.grade }}{{ item.gradeName }}</td>
              <td>{{ item.role | translateType }}</td>
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

    <vudal name="user">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id == null">Создание пользователя</h4>
        <h4 class="modal-title" v-else>Редактирование пользователя</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Имя</label>
                <input type="text" v-model="current.name" class="form-control">
                <error-messages :errors="errors.name"></error-messages>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Email</label>
                <input type="text" v-model="current.email" class="form-control">
                <error-messages :errors="errors.email"></error-messages>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <label>Пароль</label>
                <input type="password" v-model="current.password" class="form-control">
                <error-messages :errors="errors.password"></error-messages>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Пол</label>
                <div>
                  <input type="radio" v-model="current.gender" value="1">
                  <label>Мужской</label>
                  <input type="radio" v-model="current.gender" value="0">
                  <label>Женский</label>
                </div>
                <error-messages :errors="errors.gender"></error-messages>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Дата рождения</label>
                <datepicker v-model="current.birthDate"></datepicker>
                <error-messages :errors="errors.birthDate"></error-messages>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Тип</label>
                <select class="form-control" v-model="current.role">
                  <option v-for="type in meta.types" :value="type">{{ type | translateType }}</option>
                </select>
                <error-messages :errors="errors.type"></error-messages>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Город</label>
                <select class="form-control" v-model="current.cityId">
                  <option v-for="city in meta.cities" :value="city.id">{{ city.name }}</option>
                </select>
                <error-messages :errors="errors.cityId"></error-messages>
              </div>
            </div>
            <div class="col-xs-6" v-if="current.role === 'user'">
              <div class="form-group">
                <label>Школа</label>
                <select class="form-control" v-model="current.schoolId">
                  <option v-for="school in schools" :value="school.id">{{ school.name }}</option>
                </select>
                <error-messages :errors="errors.schoolId"></error-messages>
              </div>
            </div>
            <div class="col-xs-6" v-if="current.role === 'user'">
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group">
                    <label>Класс</label>
                    <select class="form-control" v-model="current.grade">
                      <option v-for="grade in meta.grades" :value="grade">{{ grade }}</option>
                    </select>
                    <error-messages :errors="errors.grade"></error-messages>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <select class="form-control" v-model="current.gradeName">
                      <option v-for="gradeName in meta.gradeNames" :value="gradeName">{{ gradeName }}</option>
                    </select>
                    <error-messages :errors="errors.gradeName"></error-messages>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <input type="checkbox" v-model="current.active" />
                <label>Активен</label>
                <error-messages :errors="errors.active"></error-messages>
              </div>
            </div>
            <div v-if="current.id != null">
              <div class="col-xs-12">
                <router-link :to="{ name: 'transactions', query: { userId: this.current.id } }">
                  История платежей и баланс
                </router-link>
              </div>
              <div class="col-xs-12">
                <router-link :to="{ name: 'messages', query: { fromUserId: this.current.id, type: 'review' } }">
                  Отзывы
                </router-link>
              </div>
              <div class="col-xs-12">
                <router-link :to="{ name: 'messages', query: { fromUserId: this.current.id, type: 'faq' } }">
                  Вопросы
                </router-link>
              </div>
              <div class="col-xs-12">
                <router-link :to="{ name: 'messages', query: { fromUserId: this.current.id, type: 'check_request' } }">
                  Запросы на консультацию и проверку
                </router-link>
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
import User from '_shared/resources/admin/User';
import _ from 'lodash/fp';
import clientConfig from '_shared/services/ClientConfig';
import restSortableColumn from '_shared/directives/restSortableColumn';
import valueById from '_shared/filters/valueById';
import format from '_shared/filters/format';

export default {

  mixins: [rest(User)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
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
        case 'user':
          return 'Ученик';
        case 'teacher':
          return 'Учитель';
        case 'admin':
          return 'Администратор';
        default:
          return 'Неизвестно';
      }
    },
  },

  mounted() {
    this.$on('new', () => {
      this.$modals.user.$show();
    });
    this.$on('editing', () => {
      if (this.current.birthDate == null) {
        this.current.birthDate = '';
      }
      this.$modals.user.$show();
    });
    this.$on('created', () => {
      this.$modals.user.$hide();
    });
    this.$on('updated', () => {
      this.$modals.user.$hide();
    });
    this.$on('destroyed', () => {
      this.$modals.user.$hide();
    });
  },

  data() {
    return {
      filterDefaults: {
        name: null,
        email: null,
        gender: null, // male
        birthDateFrom: null,
        birthDateTo: null,
        role: null,
        cityId: null,
        schoolId: null,
        grade: null,
        gradeName: null,
      },

      meta: {
        grades: clientConfig.grades,
        gradeNames: clientConfig.gradeNames,
        cities: [],
        schools: [],
        types: clientConfig.userTypes,
      },

      newResourceData: {
        birthDate: null,
        role: 'admin',
      },
    };
  },

  computed: {
    schools() {
      return _.flatMap(city => city.schools)(this.meta.cities);
    },
  },

  methods: {
    findInactive() {
      this.$set(this.filter, 'active', false);
      this.applyFilter();
    },
  },

};
</script>

<style>
  .inactive-count {
    font-size: larger;
  }
</style>

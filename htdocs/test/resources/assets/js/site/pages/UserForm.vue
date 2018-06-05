<template>
  <div>
  <h2>Пожалуйста заполните форму для продолжения работы</h2>
  <div class="profile">
    <div class="row">
      <div class="col-6">
        <div class="block">
          <div class="title">Ваш пол: <span class="text-red">*</span></div>
          <div class="row">
            <div class="col-6">
              <div class="radio">
                <input type="radio" id="radio-1" name="sex" v-model="current.gender" :value="1">
                <label for="radio-1">Мужской</label>
              </div>
            </div>
            <div class="col-6">
              <div class="radio">
                <input type="radio" id="radio-2" name="sex" v-model="current.gender" :value="0">
                <label for="radio-2">Женский</label>
              </div>
            </div>
            <error-messages :errors="errors.gender"></error-messages>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="block">
          <div class="title">Дата рождения:</div>
          <datepicker v-model="current.birthDate"></datepicker>
          <div>
            <error-messages :errors="errors.birthDate"></error-messages>
          </div>
        </div>
      </div>
    </div>
    <div class="block">
      <div class="title">Вы: <span class="text-red">*</span></div>
      <div class="row">
        <div class="col" v-for="subtype in meta.userSubTypes">
          <div class="radio">
            <input type="radio" name="subtype" :id="'subtype_' + subtype" v-model="current.subtype" :value="subtype">
            <label :for="'subtype_' + subtype">{{ subtype | translateSubType }}</label>
          </div>
        </div>
      </div>
      <error-messages :errors="errors.subtype"></error-messages>
    </div>
    <div class="block" v-if="!Auth.user.email">
      <div class="row">
        <div class="col">
          <div class="title">Email: <span class="text-red">*</span></div>
          <small>Вам будет отправлено письмо со ссылкой на подтверждение</small>
          <input type="email" v-model="current.email">
          <error-messages :errors="errors.email"></error-messages>
        </div>
      </div>
    </div>
    <div class="block">
      <div class="row">
        <div class="col">
          <div class="title">Город:</div>
          <cities-select :cities="meta.cities" v-model="current.cityId" :new-city.sync="current.newCity"></cities-select>
        </div>
        <div class="col">
          <div class="title">Школа:</div>
          <schools-select :schools="schools" v-model="current.schoolId" :new-school.sync="current.newSchool"></schools-select>
        </div>
        <div class="col">
          <div class="title">Класс:</div>
          <div class="row">
            <div class="col-7">
              <select v-model="current.grade" class="no-margin-top">
                <option v-for="grade in meta.grades" :value="grade">{{ grade }}</option>
              </select>
            </div>
            <div class="col">
              <select v-model="current.gradeName" class="no-margin-top">
                <option v-for="name in meta.gradeNames" :value="name">{{ name }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="profile">

    <div class="block">
      <div class="title">Вы хотите: <span class="text-red">*</span></div>
      <div class="row">
        <div class="col">
          <div class="radio">
            <input type="radio" name="role" id="you_user" :value="'user'" v-model="current.role">
            <label for="you_user">Подготовиться к ЕГЭ</label>
          </div>
        </div>
        <div class="col">
          <div class="radio">
            <input type="radio" name="role" id="you_teacher" :value="'teacher'" v-model="current.role">
            <label for="you_teacher">Сотрудничать с нами в качестве преподавателя (потребуется подтверждение квалификации)</label>
          </div>
        </div>

      </div>
    </div>

    <div class="block">
      <div class="row">
        <div class="col">
          <div class="check simple">
            <input type="checkbox" id="agreement" v-model="current.acceptedAgreement">
            <label for="agreement">Согласен с <a href="/agreement" class="agreement-link" target="_blank">Пользовательским Соглашением</a></label>
          </div>
          <error-messages :errors="errors.acceptedAgreement"></error-messages>
        </div>
      </div>
    </div>
  </div>
  <div class="text-right">
    <a href="#" class="btn btn-black mt-4" @click.prevent="save()">Далее</a>
  </div>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';
import City from '_shared/resources/City';

export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
    Datepicker: () => import('_components/datepicker'),
    CitiesSelect: () => import('_components/citiesSelect'),
    SchoolsSelect: () => import('_components/schoolsSelect'),
  },

  filters: {
    translateSubType(subtype) {
      switch (subtype) {
        case 'pupil':
          return 'Ученик';
        case 'student':
          return 'Студент';
        case 'parent':
          return 'Родитель';
        case 'teacher':
          return 'Учитель';
        default:
          return 'Неизвестно';
      }
    },
  },

  created() {
    this.current = _.merge({}, this.Auth.user);

    City.query({ with: 'schools', sort: 'name' }).then((response) => {
      this.meta.cities = response.body.data;
    });
  },

  data() {
    return {
      current: {},
      role: null,

      errors: {},

      meta: {
        cities: [],
        grades: ClientConfig.grades,
        gradeNames: ClientConfig.gradeNames,
        userSubTypes: ClientConfig.userSubTypes,
      },
    };
  },

  computed: {
    schools() {
      if (this.current.cityId == null) {
        return [];
      }

      const selectedCity = _.find(city => city.id === this.current.cityId)(this.meta.cities);

      if (selectedCity == null) {
        return [];
      }

      return selectedCity.schools;
    },
  },

  methods: {
    save() {
      Account.update(this.current).then(() => {
        this.errors = {};
        if (this.current.role === 'user') {
          location.href = this.$router.resolve({ name: 'mainPage' }).href;
        }
        else if (this.current.role === 'teacher') {
          location.href = this.$router.resolve({ name: 'teacherForm' }).href;
        }
      }).catch((response) => {
        this.errors = response.body.errors;
      });
    },
  },

  watch: {
    'current.cityId'(newVal) {
      if (newVal === -1) {
        this.current.schoolId = -1;
      }
    },
  },
};
</script>

<style>
  .agreement-link {
    color: #42a6fd;
  }
</style>

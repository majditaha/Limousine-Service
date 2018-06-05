<template>
  <div>
    <div class="row justify-content-between profile">
      <div class="col-3">
        <div class="image-block">
          <img v-if="current.photo == null" src="https://maxcdn.icons8.com/Share/icon/Users//circled_user_female1600.png" alt="">
          <img v-else :src="current.photo" alt="">
        </div>
        <div class="user_name">Аватар</div>
        <div class="rating" v-if="Auth.isTeacher">
          <router-link :to="{ name: 'rating' }">
            Рейтинг
            <span class="text-sky">{{ Auth.user.rating.avg }}</span>
          </router-link>
        </div>
        <div class="top15">
          <cloudinary :images-only="true" :multiple="true" button-text="Загрузить аватар" @upload="writeAvatar"></cloudinary>
          <span v-if="avatarLoaded">Аватар загружен</span>
        </div>
        <div class="top15" v-if="current.role === 'teacher'">
          <cloudinary :images-only="true" button-text="Загрузить скан разворота паспорта" @upload="writePassportScan"></cloudinary>
          <span v-if="current.passportFile != null">
            Скан паспорта загружен.
            <a :href="current.passportFile" target="_blank">Скачать</a>
          </span>
          <error-messages :errors="errors.passportFile"></error-messages>
        </div>
        <div class="top15" v-if="current.role === 'teacher'">
          <cloudinary :images-only="true" button-text="Загрузить скан трудовой книжки" @upload="writeEmploymentHistory"></cloudinary>
          <span v-if="current.emplHistoryFile != null">
            Скан трудовой книжки загружен.
            <a :href="current.emplHistoryFile" target="_blank">Скачать</a>
          </span>
          <error-messages :errors="errors.emplHistoryFile"></error-messages>
        </div>
      </div>
      <div class="col-8">
        <div class="block">
          <div class="title">Полное имя: <span class="text-red">*</span></div>
          <input type="text" placeholder="ФИО" v-model="current.name">
          <error-messages :errors="errors.name"></error-messages>
        </div>
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
              <error-messages :errors="errors.birthDate"></error-messages>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <div class="block">
              <div class="title">Мобильный телефон:</div>
              <input type="tel" v-model="current.phone">
              <error-messages :errors="errors.phone"></error-messages>
            </div>
          </div>
          <div class="col-6">
            <div class="block">
              <div class="title">Email: <span class="text-red">*</span></div>
              <input type="email" v-model="current.email" :disabled="!!Auth.user.email">
              <error-messages :errors="errors.email"></error-messages>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <div class="block">
              <div class="title" v-if="!current.hasPassword">Задайте пароль:</div>
              <div class="title" v-else>Изменить пароль:</div>
              <input type="password" v-model="current.password">
              <error-messages :errors="errors.password"></error-messages>
            </div>
          </div>
          <div class="col-6">
            <div class="block">
              <div class="title">Повторить пароль:</div>
              <input type="password" v-model="current.passwordConfirmation">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="profile">
      <div class="block" v-if="current.role === 'user'">
        <div class="title">Вы: <span class="text-red">*</span></div>
        <div class="row">
          <div class="col" v-for="subtype in meta.userSubTypes">
            <div class="radio">
              <input type="radio" :id="'you_' + subtype" v-model="current.subtype" :value="subtype">
              <label :for="'you_' + subtype">{{ subtype | translateSubType }}</label>
            </div>
          </div>
        </div>
        <error-messages :errors="errors.subtype"></error-messages>
      </div>
      <div class="block" v-if="current.role === 'user'">
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
      <div class="block" v-if="current.role === 'teacher'">
        <div class="title">Предметы, на которых вы специализируетесь <span class="text-red">*</span></div>
        <div class="row">
          <div class="col" v-for="discipline in meta.disciplines">
            <div class="check simple">
              <input type="checkbox" :id="'discipline_' + discipline.id" v-model="current.disciplineIds" :value="discipline.id">
              <label :for="'discipline_' + discipline.id">{{ discipline.name }}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="text-right">
      <a href="#" class="btn btn-black mt-4" @click.prevent="save()">Сохранить изменения</a>
    </div>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';
import City from '_shared/resources/City';
import Discipline from '_shared/resources/Discipline';
import Noty from 'noty';

export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
    Datepicker: () => import('_components/datepicker'),
    Cloudinary: () => import('_components/cloudinary'),
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

    Discipline.query().then((response) => {
      this.meta.disciplines = response.body.data;
    });
  },

  data() {
    return {
      current: {},

      errors: {},

      avatarLoaded: false,
      passportScanLoaded: false,
      employmentHistoryLoaded: false,

      meta: {
        cities: [],
        disciplines: [],
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
      Noty.closeAll();
      Account.update(this.current).then(() => {
        this.errors = {};
        new Noty({
          text: 'Данные сохранены',
          timeout: 7000,
        }).show();
      }).catch((response) => {
        console.log(response);
        this.errors = response.body.errors;
        new Noty({
          text: 'Ошибка ввода данных',
          type: 'error',
          timeout: 7000,
        }).show();
      });
    },

    writeAvatar(file) {
      this.current.photo = file.secureUrl;
      this.avatarLoaded = true;
    },

    writePassportScan(file) {
      this.current.passportFile = file.secureUrl;
      if (this.errors.passportFile != null) {
        this.errors.passportFile = null;
      }
    },

    writeEmploymentHistory(file) {
      this.current.emplHistoryFile = file.secureUrl;
      if (this.errors.emplHistoryFile != null) {
        this.errors.emplHistoryFile = null;
      }
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

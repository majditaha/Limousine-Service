<template>
  <div>
    <h2>Пожалуйста заполните форму для продолжения работы</h2>
    <div class="profile">
      <div class="block">
        <div class="title">Предметы, на которых вы специализируетесь: <span class="text-red">*</span></div>
        <div class="row">
          <div class="col" v-for="discipline in meta.disciplines">
            <div class="check simple">
              <input type="checkbox" :id="'discipline_' + discipline.id" v-model="current.disciplineIds" :value="discipline.id">
              <label :for="'discipline_' + discipline.id">{{ discipline.name }}</label>
            </div>
          </div>
        </div>
      </div>
      <div class="row row-eq-height">
        <div class="col-6">
          <div class="block">
            <div class="title">Скан или фото разворота паспорта: <span class="text-red">*</span></div>
            <cloudinary :images-only="true" button-text="Загрузить скан разворота паспорта" @upload="writePassportScan"></cloudinary>
            <div>
              <span v-if="current.passportFile != null">
                Скан паспорта загружен.
                <a :href="current.passportFile" target="_blank">Скачать</a>
              </span>
            </div>
            <div>
              <error-messages :errors="errors.passportFile"></error-messages>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="block">
            <div class="title">Скан или фото страниц трудовой книжки,
              <br>
              <small>подтверждающих работу в качестве преподавателя:</small>
              <span class="text-red">*</span>
            </div>
            <cloudinary :images-only="true" button-text="Загрузить скан трудовой книжки" @upload="writeEmploymentHistory"></cloudinary>
            <div>
              <span v-if="current.emplHistoryFile != null">
                Скан трудовой книжки загружен.
                <a :href="current.emplHistoryFile" target="_blank">Скачать</a>
              </span>
            </div>
            <div>
              <error-messages :errors="errors.emplHistoryFile"></error-messages>
            </div>
          </div>
        </div>
      </div>
      <div class="block mt-4" v-if="!Auth.user.email">
        <div class="row">
          <div class="col">
            <div class="title">Email: <span class="text-red">*</span></div>
            <small>Вам будет отправлено письмо со ссылкой на подтверждение</small>
            <input type="email" v-model="current.email">
            <error-messages :errors="errors.email"></error-messages>
          </div>
        </div>
      </div>
      <div class="block mt-4">
        <div class="title">Мобильный телефон: <span class="text-red">*</span></div>
        <div class="row">
          <div class="col-6">
            <input type="tel" v-model="current.phone">
            <error-messages :errors="errors.phone"></error-messages>
          </div>
        </div>
      </div>
    </div>
    <div class="block mt-4">
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
    <div class="text-right">
      <a href="#" class="btn btn-black mt-4" @click.prevent="save()">Далее</a>
    </div>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';
import Discipline from '_shared/resources/Discipline';

export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
    Cloudinary: () => import('_components/cloudinary'),
  },

  created() {
    this.current = _.merge({}, this.Auth.user);

    Discipline.query().then((response) => {
      this.meta.disciplines = response.body.data;
    });
  },

  data() {
    return {
      current: {},

      errors: {},

      passportScanLoaded: false,
      employmentHistoryLoaded: false,

      meta: {
        disciplines: [],
        grades: ClientConfig.grades,
        gradeNames: ClientConfig.gradeNames,
      },
    };
  },

  methods: {
    save() {
      Account.update(this.current).then(() => {
        this.errors = {};
        this.$router.go({ name: 'mainPage' });
      }).catch((response) => {
        this.errors = response.body.errors;
      });
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
};
</script>

<style>
  .agreement-link {
    color: #42a6fd;
  }
</style>

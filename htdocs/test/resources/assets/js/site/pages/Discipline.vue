<template>
  <div class="profile_icons nom row full-height align-items-center justify-content-center">
    <div class="item col-sm-2">
      <router-link :to="{ name: 'trainings' }">
        <img src="/images/profile_10.png" alt="">
        <span class="title">Обучение</span>
      </router-link>
    </div>
    <div class="item col-sm-2">
      <router-link :to="{ name: 'theories' }">
        <img src="/images/profile_7.png" alt="">
        <span class="title">Теория</span>
      </router-link>
    </div>
    <div class="item col-sm-2">
      <a href="#" @click.prevent="$modals.askPracticeMode.$show()">
        <img src="/images/profile_8.png" alt="">
        <span class="title">Практика</span>
      </a>
    </div>
    <div class="item col-sm-2">
      <router-link :to="{ name: 'tests' }">
        <img src="/images/profile_9.png" alt="">
        <span class="title">Тесты</span>
      </router-link>
    </div>
    <div class="item col-sm-2">
      <router-link :to="{ name: 'trainingsStat' }">
        <img src="/images/profile_43.png" alt="">
        <span class="title">Анализ</span>
      </router-link>
    </div>
    <div class="col-sm-4" v-if="hoursSelectorVisible">
      <div class="form">
        <div class="form_head">
          <h3>Цель дня</h3>
          <span class="icon" @click="hoursSelectorVisible = false">close</span>
        </div>
        <div class="form_body">
          <p>Сколько времени хотите потратить на обучение сегодня?</p>
          <select v-model="hoursToSpend" class="small">
            <option></option>
            <option value="1">1 час</option>
            <option value="2">2 часа</option>
            <option value="3">3 часа</option>
            <option value="4">4 часа</option>
            <option value="5">5 часов</option>
            <option value="6">6 часов</option>
          </select>
          <select v-model="minutesToSpend" class="small">
            <option></option>
            <option value="10">10 мин</option>
            <option value="20">20 мин</option>
            <option value="30">30 мин</option>
            <option value="40">40 мин</option>
            <option value="50">50 мин</option>
          </select>
          <button class="btn btn-black mt-3" @click="setDesiredHours">Подтвердить</button>
        </div>
      </div>
    </div>
    <custom-vudal name="askPracticeMode" :auto-center="false">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Выберите режим практики</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <router-link :to="{ name: 'practices' }" class="btn btn-blue">Ручной</router-link>
            <router-link :to="{ name: 'practice', params: { sectionId: 'auto' } }" class="btn btn-blue">Автоматический</router-link>
          </div>
        </div>
      </div>
    </custom-vudal>
  </div>
</template>
<script>
import Account from '_shared/services/Account';
import Auth from '_shared/services/Auth';

export default {

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) !== -1) {
      next();
    }
  },

  components: {
    CustomVudal: () => import('_components/customVudal'),
  },

  created() {
    // this.hoursSelectorVisible = this.Auth.user.needToAskDesiredMinutes;
  },

  data() {
    return {
      hoursSelectorVisible: false,
      hoursToSpend: null,
      minutesToSpend: null,
    };
  },

  methods: {
    setDesiredHours() {
      const hours = this.hoursToSpend;
      const minutes = this.minutesToSpend;

      Account.setDesiredHours({ hours, minutes }).then(() => {
        this.hoursSelectorVisible = false;
      });
    },
  },

};
</script>

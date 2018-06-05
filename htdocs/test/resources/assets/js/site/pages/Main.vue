<template>
  <div class="profile_icons row full-height align-items-center">
    <div class="item col-sm-2">
      <router-link :to="{ name: 'profile' }">
        <img src="/images/profile_1.png" alt="">
        <span class="title">Профиль</span>
      </router-link>
    </div>
    <div class="item col-sm-2">
      <router-link :to="{ name: 'messages' }">
        <img src="/images/profile_2.png" alt="">
        <span class="title">Обращения</span>
      </router-link>
    </div>
    <div class="item col-sm-2">
      <router-link :to="{ name: 'plans' }">
        <img src="/images/profile_3.png" alt="">
        <span class="title">Тарифы</span>
      </router-link>
    </div>
    <div class="item col-sm-2" v-for="discipline in disciplines">
      <router-link v-if="userHasDiscipline(discipline.id)" :to="{ name: 'discipline', params: { disciplineId: discipline.id } }">
        <img :src="discipline.iconFile" alt="">
        <span class="title">{{ discipline.name }}</span>
      </router-link>
      <router-link v-else :to="{ name: 'plans' }">
        <img :src="discipline.iconFile" alt="">
        <span class="title">{{ discipline.name }}</span>
      </router-link>
    </div>
  </div>
</template>
<script>
import _ from 'lodash/fp';
import Discipline from '_shared/resources/Discipline';

export default {

  created() {
    Discipline.query().then((response) => {
      this.disciplines = response.data.data;
    });
  },

  data() {
    return {
      disciplines: [],
    };
  },

  methods: {
    userHasDiscipline(disciplineId) {
      return _.includes(disciplineId)(this.Auth.user.disciplineIds);
    },
  },

};
</script>

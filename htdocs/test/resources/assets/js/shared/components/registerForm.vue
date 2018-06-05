<template>
  <div>
    <input type="text" placeholder="Ваше полное имя" v-model="data.name" :class="{'input-error': Auth.errors.reg.name}">
    <error-messages v-if="Auth.errors.reg.name != null" :errors="Auth.errors.reg.name"></error-messages>
    <input type="text" placeholder="Email" v-model="data.email" :class="{'input-error': Auth.errors.reg.email}">
    <error-messages v-if="Auth.errors.reg.email != null" :errors="Auth.errors.reg.email"></error-messages>
    <input type="password" placeholder="Пароль" v-model="data.password" :class="{'input-error': Auth.errors.reg.password}">
    <error-messages v-if="Auth.errors.reg.password != null" :errors="Auth.errors.reg.password"></error-messages>
    <button class="btn btn-green full-width large" @click.prevent="register()">Бесплатная регистрация</button>
    <a href="/login/vkontakte" class="btn btn-link full-width large">
      <img src="/fonts/icon-vk.svg" alt="" width="24" class="mr-2">
      Вход через ВКонтакте
    </a>
  </div>
</template>
<script>
export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
  },

  data() {
    return {
      data: {
        name: '',
        email: '',
        password: '',
      },
    };
  },

  methods: {
    register() {
      this.Auth.register(this.data, '/');
    },
  },

};
</script>
<style>
input+span {
  padding: 0 !important;
}
.input-error{
  border: 2px solid #FF0000;
}
input::-webkit-input-placeholder {color:#777;}
input::-moz-placeholder          {color:#777;}/* Firefox 19+ */
input:-moz-placeholder           {color:#777;}/* Firefox 18- */
input:-ms-input-placeholder      {color:#777;}
</style>

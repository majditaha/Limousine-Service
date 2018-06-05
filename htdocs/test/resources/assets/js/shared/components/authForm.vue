<template>
  <div>
    <div class="modal-body" @keyup.enter="Auth.login(email, password, '/')">
      <input type="text" placeholder="E-mail" v-model="email">
      <error-messages :errors="Auth.errors.login.email"></error-messages>
      <input type="password" placeholder="Пароль" v-model="password">
      <error-messages :errors="Auth.errors.login.password"></error-messages>
      <a href="#" @click.prevent="onForgotPasswordClick">Забыли пароль?</a>
    </div>
    <div class="modal-footer">
      <a href="/login/vkontakte" class="btn btn-link btn-sky full-width">
        <img src="/fonts/icon-vk.svg" alt="" width="24" class="mr-2">
        Вход через Вконтакте
      </a>
      <a href="#" class="btn btn-blue" @click.prevent="Auth.login(email, password, '/')">Отправить</a>
    </div>
  </div>
</template>
<script>
export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
  },

  data() {
    return {
      email: '',
      password: '',
    };
  },

  methods: {
    onForgotPasswordClick() {
      this.$emit('forgot-password-click');
    },
  },

};
</script>

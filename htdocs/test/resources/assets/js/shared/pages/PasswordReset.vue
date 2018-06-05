<template>
  <div class="block">
    <input type="password" placeholder="Новый пароль" v-model="newPassword">
    <error-messages :errors="Auth.errors.reset.password"></error-messages>
    <input type="password" placeholder="Повторите новый пароль" v-model="newPasswordConfirmation">
    <div>
      <a href="#" class="btn btn-blue" @click.prevent="changePassword">Отправить</a>
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
      newPassword: '',
      newPasswordConfirmation: '',
    };
  },

  methods: {
    changePassword() {
      this.Auth.changePassword(
        this.$route.query.email,
        this.newPassword,
        this.newPasswordConfirmation,
        this.$route.params.token,
      );
    },
  },

};
</script>

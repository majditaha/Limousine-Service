<template>
  <div>
    <div class="modal-body">
      <input type="text" placeholder="E-mail" v-model="email">
      <error-messages :errors="Auth.errors.reset.email"></error-messages>
      <div>
        <a href="#" @click.prevent="onAuthClick">Авторизация</a>
      </div>
    </div>
    <div class="modal-footer">
      <a href="#" class="btn btn-blue" @click.prevent="reset">Отправить</a>
    </div>
  </div>
</template>
<script>
import Noty from 'noty';

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
    onAuthClick() {
      this.$emit('auth-click');
    },

    reset() {
      this.Auth.passwordReset(this.email).then((response) => {
        new Noty({
          text: response.status,
          type: 'success',
        }).show();
        this.email = '';
        this.password = '';
        this.$emit('email-sent');
      });
    },
  },

};
</script>

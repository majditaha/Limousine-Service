<template>
  <div>
    <custom-vudal name="auth">
      <div class="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 v-if="state === 'auth'" class="modal-title">Авторизация</h5>
              <h5 v-else-if="state === 'forgot_password'" class="modal-title">Восстановление пароля</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <auth-form
              v-if="state === 'auth'"
              @forgot-password-click="state = 'forgot_password'"
            ></auth-form>
            <forgot-password-form
              v-if="state === 'forgot_password'"
              @auth-click="state = 'auth'"
              @email-sent="onPasswordEmailSent"
            ></forgot-password-form>
          </div>
        </div>
      </div>

    </custom-vudal>
  </div>
</template>
<script>
import AuthForm from '_shared/components/authForm';
import ForgotPasswordForm from '_shared/components/forgotPasswordForm';

export default {

  components: {
    AuthForm,
    ForgotPasswordForm,
    CustomVudal: () => import('_components/customVudal'),
  },

  data() {
    return {
      state: 'auth',
    };
  },

  methods: {
    onPasswordEmailSent() {
      this.$modals.auth.$hide();
      this.state = 'auth';
    },
  },

};
</script>

<template>
  <div class="block">
    <p v-if="success">
      Email успешно подтвержден
    </p>
    <p v-else>
      Ошибка! Email не подтвержден
    </p>
    <auth-modal></auth-modal>
  </div>
</template>
<script>
export default {

  components: {
    AuthModal: () => import('_components/authModal'),
  },

  computed: {
    success() {
      return this.$route.query.success === '1';
    },
  },

};
</script>

<template>
  <div>
    <loading-indicator :loading="loading">
      <div class="block" v-html="text"></div>
    </loading-indicator>
  </div>
</template>
<script>
export default {

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
  },

  created() {
    this.loadAgreement();
  },

  data() {
    return {
      text: '',
      loading: true,
    };
  },

  methods: {
    loadAgreement() {
      this.loading = true;
      this.$http.get('/api/agreement').then((response) => {
        this.text = response.data;
      }).finally(() => {
        this.loading = false;
      });
    },
  },

};
</script>

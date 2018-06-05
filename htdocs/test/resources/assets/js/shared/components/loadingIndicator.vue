<template>
  <div class="loader">
    <spinner v-if="internalLoading" class="spinner"></spinner>
    <slot v-else></slot>
  </div>
</template>
<script>
import Spinner from '_components/circleSpinner';
import _ from 'lodash/fp';

export default {

  props: {
    loading: {
      required: true,
    },
    minDelay: {
      type: Number,
      default: 1000,
    },
  },

  components: { Spinner },

  data() {
    return {
      internalLoading: (_.isBoolean(this.loading) ? this.loading : false),
      lastLoadingStartedAt: null,
    };
  },

  methods: {
    // If loading took more that minDelay ms, then show loader a bit longer
    // This is convenient to eliminate loader flickering
    finishLoading() {
      if (this.lastLoadingStartedAt != null) {
        const now = Date.now();
        const diff = now - this.lastLoadingStartedAt;
        const leftMs = this.minDelay - diff;
        if (diff < this.minDelay) {
          setTimeout(() => {
            this.internalLoading = false;
            this.lastLoadingStartedAt = null;
          }, leftMs);
        }
        else {
          this.internalLoading = false;
          this.lastLoadingStartedAt = null;
        }
      }
      else {
        this.internalLoading = false;
      }
    },
  },

  watch: {
    loading(newVal) {
      if (_.isBoolean(newVal)) {
        if (newVal) {
          this.lastLoadingStartedAt = Date.now();
          this.internalLoading = true;
          return;
        }
        this.finishLoading();
      }
      else if (newVal.then != null) {
        this.lastLoadingStartedAt = Date.now();
        this.internalLoading = true;
        newVal.finally(() => {
          this.finishLoading();
        });
      }
      else {
        this.internalLoading = false;
      }
    },
  },

};
</script>
<style lang="css" scoped>
  .loader {
    width: 100%;
    height: 100%;
    margin: auto;
  }

  .spinner {
  }
</style>

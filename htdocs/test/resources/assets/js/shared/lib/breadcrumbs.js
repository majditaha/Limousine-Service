import Vue from 'vue';

export default {
  install() {
    let breadcrumbs = null;
    Object.defineProperty(Vue.prototype, '$breadcrumbs', {
      get() {
        return breadcrumbs;
      },

      set(newValue) {
        breadcrumbs = newValue;
        BreadcrumbsEventBus.$emit('updated');
      },
    });

    Vue.mixin({
      created() {
        if (this.$route != null && this.$route.meta != null && this.$route.meta.breadcrumbs != null) {
          this.$breadcrumbs = this.$route.meta.breadcrumbs;
        }
      },
    });
  },
};

export const BreadcrumbsEventBus = new Vue();

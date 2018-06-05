<template>
  <h1>{{ title }}</h1>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import _ from 'lodash/fp';

export default {

  computed: {
    title() {
      if (this.$route.meta == null) {
        return null;
      }

      // For static pages there is only one item in breadcrumbs, aside main page
      if (this.$route.meta.static) {
        const { alias } = this.$route.params;

        const menuItem = _.find(item => item.alias === alias)(ClientConfig.menu);

        if (menuItem == null) {
          return null;
        }

        return menuItem.name;
      }

      return this.$route.meta.title;
    },
  },

};
</script>

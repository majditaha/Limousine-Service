<template>
  <div>
    <div v-for="(page, index) in pages" class="block mb-4">
      <div v-if="!(page.name === title && index === 0)" class="title">{{ page.name }}</div>
      <div v-html="page.content" class="ql-editor"></div>
    </div>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import _ from 'lodash/fp';

export default {
  props: ['pages'],

  computed: {
    title() {
      if (this.$route.meta == null) {
        return null;
      }

      // For static pages there is only one item in breadcrumbs, aside main page
      if (this.$route.meta.static) {
        const { alias } = this.$route.params;
        const menuItem = _.find(item => item.alias === alias)(ClientConfig.menu);
        return menuItem.name;
      }
      return this.$route.meta.title;
    },
  },
};
</script>

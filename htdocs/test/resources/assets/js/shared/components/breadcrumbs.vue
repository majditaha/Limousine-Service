<template>
  <div class="breadcrumbs">
    <template v-if="breadcrumbsVisible">
      <router-link :to="{ name: 'mainPage' }">Главная</router-link>
      <span v-for="item in breadcrumbs">
        >
        <router-link v-if="item.url != null" :to="item.url">{{ item.title }}</router-link>
        <span v-else>{{ item.title }}</span>
      </span>
    </template>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import _ from 'lodash/fp';
import { BreadcrumbsEventBus } from '_shared/lib/breadcrumbs';

export default {

  mounted() {
    this.breadcrumbs = this.getBreadcrumbs();
    BreadcrumbsEventBus.$on('updated', () => {
      this.breadcrumbs = this.getBreadcrumbs();
    });
  },

  data() {
    return {
      breadcrumbs: [],
    };
  },

  computed: {
    breadcrumbsVisible() {
      return this.$route.name !== 'mainPage' && this.breadcrumbs != null;
    },
  },

  methods: {

    getBreadcrumbs() {
      // For static pages there is only one item in breadcrumbs, aside main page
      if (this.$route.meta != null && this.$route.meta.static) {
        const { alias } = this.$route.params;

        const menuItem = _.find(item => item.alias === alias)(ClientConfig.menu);

        if (menuItem == null) {
          return [];
        }

        return [
          { title: menuItem.name, url: null },
        ];
      }

      if (this.$breadcrumbs == null) {
        return null;
      }

      return _.compact(this.$breadcrumbs);
    },
  },

  watch: {
    $route() {
      this.breadcrumbs = this.getBreadcrumbs();
    },
  },
};
</script>

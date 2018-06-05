<template>
  <div>
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" v-for="tab in tabs" @click="select(tab)">
        <a :class="getTabClass(tab)" data-toggle="tab">{{ tab.header }}</a>
      </li>
    </ul>

    <div :class="contentClass">
      <div class="tab-content">
        <slot></slot>
      </div>
    </div>

  </div>
</template>
<script>
export default {

  props: {
    active: {
      type: Number,
      default: 0,
    },
    contentClass: null,
    navItemClass: {
      type: Array,
      default() {
        return [];
      },
    },
  },

  data() {
    return {
      tabs: [],
      isTabset: true,
      activeTab: null,
    };
  },

  mounted() {
    this.activeTab = this.tabs[this.active];
  },

  methods: {
    select(tab) {
      this.activeTab = tab;
    },

    getTabClass(tab) {
      const activeClass = tab.active ? ['active'] : [];
      return this.navItemClass.concat(activeClass);
    },
  },

  watch: {
    active(value) {
      this.activeTab = this.tabs[value];
    },
  },
};
</script>
<style scoped>
  .nav a {
    cursor: pointer;
  }
</style>

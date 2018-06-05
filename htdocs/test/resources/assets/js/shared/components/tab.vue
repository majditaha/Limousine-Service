<template>
  <div
    role="tabpanel"
    class="tab-pane active"
    v-show="show"
    :class="{hide: !show}"
  >
    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    header: String,
  },

  created() {
    let tabset = this;
    while (tabset && !tabset.isTabset && tabset.$parent) {
      tabset = tabset.$parent;
    }

    if (!tabset.isTabset) {
      this.tabset = {};
      console.warn('Warning: "tab" depend on "tabset" to work properly');
      return;
    }

    tabset.tabs.push(this);
    this.tabset = tabset;
  },

  beforeDestroy() {
    if (this.tabset.active === this.index) {
      this.tabset.active = 0;
    }

    this.tabset.tabs.splice(this, 1);
  },

  data() {
    return {
      tabs: [],
      tabset: {},
    };
  },

  computed: {
    active() {
      return this.tabset.activeTab === this;
    },

    index() {
      return this.tabset.tabs.indexOf(this);
    },

    show() {
      return this.tabset && this.active;
    },
  },

  watch: {
    show(value) {
      if (value) {
        this.$emit('activated');
      }
      else {
        this.$emit('deactivated');
      }
    },
  },
};
</script>

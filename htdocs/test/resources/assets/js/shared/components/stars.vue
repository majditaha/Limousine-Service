<template>
  <div>
    <template v-if="editable">
      <img
        v-for="n in max"
        :src="n <= internalValue ? activeStarUrl : inactiveStarUrl"
        @mouseover="internalValue = n"
        @mouseleave="internalValue = prevInternalValue"
        @click="onSelect(n)"
      >
    </template>
    <template v-else>
      <img v-for="n in full" :src="activeStarUrl">
      <img v-for="n in empty" :src="inactiveStarUrl">
    </template>
  </div>
</template>
<script>
import _ from 'lodash/fp';

export default {

  props: {
    value: Number,
    editable: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      max: 5,
      internalValue: this.value,
      prevInternalValue: this.value,

      activeStarUrl: '/fonts/icon-star.svg',
      inactiveStarUrl: '/fonts/icon-star-nope.svg',
    };
  },

  computed: {
    full() {
      return _.round(this.internalValue) || 0;
    },

    empty() {
      return this.max - this.full;
    },
  },

  methods: {
    onSelect(numberOfStars) {
      this.prevInternalValue = numberOfStars;
      this.internalValue = numberOfStars;

      this.$emit('edit', this.internalValue);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
      this.prevInternalValue = newVal;
    },
  },

};
</script>

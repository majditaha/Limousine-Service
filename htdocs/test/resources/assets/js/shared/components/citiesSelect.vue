<template>
  <div>
    <select v-if="internalValue !== -1" v-model="internalValue" class="no-margin-top" @change="onChange">
      <option :value="-1">Моего города нет в списке</option>
      <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
    </select>
    <input v-else type="text" v-model="internalNewCity" @keyup="onNewCityUpdate" />
  </div>
</template>
<script>
export default {

  props: {
    cities: Array,
    value: null,
    newCity: String,
  },

  data() {
    return {
      internalValue: this.value,
      internalNewCity: this.newCity,
    };
  },

  methods: {
    onChange() {
      this.$emit('input', this.internalValue);
    },

    onNewCityUpdate() {
      this.$emit('update:newCity', this.internalNewCity);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
    },
  },

};
</script>

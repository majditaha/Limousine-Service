<template>
  <div>
    <select v-if="internalValue !== -1" v-model="internalValue" class="no-margin-top" @change="onChange">
      <option :value="-1">Моей школы нет в списке</option>
      <option v-for="school in schools" :value="school.id">{{ school.name }}</option>
    </select>
    <input v-else type="text" v-model="internalNewSchool" @keyup="onNewSchoolUpdate" />
  </div>
</template>
<script>
export default {

  props: {
    schools: Array,
    value: null,
    newSchool: String,
  },

  data() {
    return {
      internalValue: this.value,
      internalNewSchool: this.newSchool,
    };
  },

  methods: {
    onChange() {
      this.$emit('input', this.internalValue);
    },

    onNewSchoolUpdate() {
      this.$emit('update:newSchool', this.internalNewSchool);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
    },
  },

};
</script>

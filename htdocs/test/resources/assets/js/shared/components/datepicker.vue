<template>
  <datepicker v-model="internalValue" :format="format"></datepicker>
</template>
<script>
import { datepicker } from 'vue-strap';
import moment from 'moment';

const format = 'dd-MM-yyyy';
const dbFormat = 'YYYY-MM-DD';
const momentFormat = 'DD.MM.YYYY';

export default {

  props: ['value'],

  components: { datepicker },

  data() {
    return {
      internalValue: this.value == null
        ? ''
        : moment(this.value, dbFormat).format(momentFormat),

      format,
    };
  },

  computed: {
    date() {
      if (!this.internalValue) {
        return null;
      }
      return moment(this.internalValue, momentFormat).format(dbFormat);
    },
  },

  watch: {
    internalValue() {
      this.$emit('input', this.date);
    },

    value(newVal) {
      if (!newVal) {
        this.internalValue = '';
        return;
      }
      this.internalValue = moment(newVal, dbFormat).format(momentFormat);
    },
  },

};
</script>

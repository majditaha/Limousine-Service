<template>
  <div>
    <div v-if="showTitle" class="title">Впишите ответ</div>
    <div class="row">
      <div class="col-12">
        <div class="input" :class="inputClass">
          <input type="text" :disabled="disabled" v-model="selectedValue" @change="updateValue(answer)" />
        </div>
        <div v-if="showCorrectAnswer">
          <b>Правильный ответ</b> - {{ answer.value }}
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import _ from 'lodash/fp';

export default {

  props: {
    answers: Array,

    value: null,

    disabled: {
      type: Boolean,
      default: false,
    },

    showTitle: {
      type: Boolean,
      default: true,
    },

    showCorrectAnswer: {
      type: Boolean,
      default: false,
    },

    highlightResults: {
      type: Boolean,
      default: false,
    },
  },

  created() {
    this.selectedValue = this.findSelectedValue();
  },

  data() {
    return {
      internalValue: this.value,

      selectedValue: null,
    };
  },

  computed: {
    answer() {
      return this.answers[0];
    },

    inputClass() {
      if (!this.highlightResults) {
        return false;
      }

      return this.isAllCorrect() ? 'success' : 'error';
    },
  },

  methods: {
    updateValue(answer) {
      this.internalValue = {
        [answer.id]: this.selectedValue,
      };

      this.$emit('input', this.internalValue);
    },

    findSelectedValue() {
      return this.answer.userValue;
    },

    isAllCorrect() {
      return _.toLower(this.answer.value) === _.toLower(this.selectedValue);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
      const selectedValue = _.first(_.values(newVal));
      if (!selectedValue) {
        this.selectedValue = null;
      }
    },
  },

};
</script>

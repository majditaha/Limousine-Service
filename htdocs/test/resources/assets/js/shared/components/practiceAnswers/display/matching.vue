<template>
  <div>
    <div v-if="showTitle" class="title">Сопоставьте варианты</div>
    <div class="row align-items-center" v-for="(answer, index) in orderedAnswers">
      <div class="col-1">
        {{ index + 1 }}.
      </div>
      <div class="col">
        <div class="select" :class="getSelectClass(answer)">
          <select :disabled="disabled" v-model="selectedValue[answer.id]" @change="updateValue()">
            <option v-for="option in options" :value="option">{{ option }}</option>
          </select>
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

    highlightResults: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      internalValue: this.value,
      selectedValue: {},
    };
  },

  computed: {
    orderedAnswers() {
      return _.sortBy(answer => answer.order)(this.answers);
    },

    options() {
      return _.flow(
        _.map('value'),
        // Sorts by alphabetic
        _.sortBy(_.identity),
      )(this.answers);
    },
  },

  methods: {
    updateValue() {
      if (this.disabled) {
        return;
      }
      this.$emit('input', this.selectedValue);
    },

    getSelectClass(answer) {
      if (!this.highlightResults) {
        return [];
      }

      const correct = this.value[answer.id] === answer.value;
      return [correct ? 'success' : 'error'];
    },

    isAllCorrect() {
      if (_.size(this.internalValue) !== this.answers.length) {
        return false;
      }

      return _.flow(
        _.keys,
        _.every((answerId) => {
          const value = this.internalValue[answerId];
          const foundAnswer = _.find(answer => answer.id === +answerId)(this.answers);
          return foundAnswer.value === value;
        })
      )(this.internalValue);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
    },
  },

};
</script>

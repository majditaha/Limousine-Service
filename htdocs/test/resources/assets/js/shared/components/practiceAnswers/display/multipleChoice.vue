<template>
  <div>
    <div v-if="showTitle" class="title">Выберите ответы</div>
    <div v-for="(answer, index) in orderedAnswers" class="row">
      <div class="col-12">
        <div class="check simple" :class="getAnswerClass(answer)">
          <input
            :id="'check' + index + answer.practiceId"
            type="checkbox"
            :disabled="disabled"
            v-model="selectedValues"
            :value="answer.value"
            @change="updateValue(answer)"
          />
          <label :for="'check' + index + answer.practiceId">{{ answer.value }}</label>
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

  created() {
    this.selectedValues = this.findSelectedValues();
  },

  data() {
    return {
      internalValue: this.fillMissingValues(this.value),

      selectedValues: null,
    };
  },

  computed: {
    orderedAnswers() {
      return _.sortBy(answer => answer.order)(this.answers);
    },
  },

  methods: {
    updateValue(answer) {
      if (this.disabled) {
        return;
      }

      if (this.internalValue[answer.id] != null) {
        this.internalValue[answer.id] = undefined;
      }
      else {
        this.internalValue[answer.id] = answer.value;
      }
      this.$emit('input', this.internalValue);
    },

    findSelectedValues() {
      return _.flow(
        _.values,
        _.filter(value => value !== undefined),
      )(this.internalValue);
    },

    fillMissingValues(value) {
      const valueCopy = value;
      if (_.size(value) !== this.answers.length) {
        this.answers.forEach((answer) => {
          if (!_.has(answer.id)(valueCopy)) {
            valueCopy[answer.id] = undefined;
          }
        });
      }
      return valueCopy;
    },

    getAnswerClass(answer) {
      if (!this.highlightResults) {
        return [];
      }

      const correct = answer.correct && _.includes(answer.value)(this.selectedValues);
      return [correct ? 'green' : 'error'];
    },

    isAllCorrect() {
      const selectedAnswers = _.sortBy(_.identity)(this.selectedValues);
      return _.flow(
        _.filter(answer => answer.correct),
        _.map('value'),
        _.sortBy(_.identity),
        _.isEqual(selectedAnswers),
      )(this.answers);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = this.fillMissingValues(newVal);
      this.selectedValues = this.findSelectedValues();
    },
  },

};
</script>

<style lang="scss" scoped>
  div.check {
    margin: 5px 0;
  }
</style>

<template>
  <div>
    <div v-if="showTitle" class="title">Впишите ответы</div>
    <div v-for="(answer, index) in orderedAnswers" class="row">
      <div class="col-12">
        <div class="input" :class="getAnswerClass(answer)">
          <input type="text" :disabled="disabled" v-model="internalValue[answer.id]" @change="updateValue()" />
        </div>
        <div v-if="showCorrectAnswer">
          <b>Правильный ответ</b>: {{ answer.value }}
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

    showCorrectAnswer: {
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
    this.updateValue();
  },

  data() {
    return {
      internalValue: this.fillMissingValues(this.value),
    };
  },

  computed: {
    orderedAnswers() {
      return _.sortBy(answer => answer.order)(this.answers);
    },
  },

  methods: {
    updateValue() {
      if (this.disabled) {
        return;
      }
      this.$emit('input', this.internalValue);
    },

    getAnswerClass(answer) {
      if (!this.highlightResults) {
        return false;
      }

      return _.toLower(answer.value) === _.toLower(this.internalValue[answer.id])
        ? 'success'
        : 'error';
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

    isAllCorrect() {
      const correctAnswers = _.flow(
        _.map(answer => _.toLower(answer.value)),
        _.sortBy(_.identity),
      )(this.answers);

      const givenAnswers = _.flow(
        _.values,
        _.map(_.toLower),
        _.sortBy(_.identity),
      )(this.internalValue);

      return _.isEqual(correctAnswers, givenAnswers);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = this.fillMissingValues(newVal);
    },
  },

};
</script>

<template>
  <div>
    <div v-if="showTitle" class="title">Выберите ответ</div>
    <div v-for="(answer, index) in orderedAnswers" class="row">
      <div class="col-12">
        <div class="radio" :class="{error: highlightResults && selectedValue == answer.value && !isCorrect}">
          <input :id="'radio' + index + answer.practiceId" :name="'radio' + answer.practiceId" :disabled="disabled" type="radio" v-model="selectedValue" :value="answer.value" @change="updateValue(answer)" />
          <label :for="'radio' + index + answer.practiceId">{{ answer.value }}</label>
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
    this.selectedValue = this.findSelectedValue();
  },

  data() {
    return {
      internalValue: this.value,

      selectedValue: null,
    };
  },

  computed: {
    orderedAnswers() {
      return _.sortBy(answer => answer.order)(this.answers);
    },

    isCorrect() {
      return _.flow(
        _.find(answer => answer.value === this.selectedValue),
        _.get('correct'),
      )(this.answers);
    },
  },

  methods: {
    updateValue(answer) {
      if (_.size(this.internalValue) !== this.answers.length) {
        const arrayOfUndefined =
          _.fill(0, this.answers.length, undefined)(Array(this.answers.length));
        const answerIds = _.map('id')(this.answers);
        this.internalValue = _.zipObject(answerIds)(arrayOfUndefined);
      }

      this.internalValue = _.flow(
        // Set all values to null
        _.mapValues(() => undefined),
        _.set(answer.id, answer.value)
      )(this.internalValue);

      this.$emit('input', this.internalValue);

      if (this.isCorrect) {
        this.$emit('correct');
      }
      else {
        this.$emit('wrong');
      }
    },

    findSelectedValue() {
      const answerId = _.flow(
        _.findKey(value => value !== undefined),
        _.toInteger,
      )(this.internalValue);

      return _.flow(
        _.find(answer => answer.id === answerId),
        _.get('value'),
      )(this.answers);
    },

    isAllCorrect() {
      return _.flow(
        _.find(answer => answer.value === this.selectedValue),
        _.get('correct'),
      )(this.answers);
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
      this.selectedValue = this.findSelectedValue();
    },
  },

};
</script>

<style lang="scss" scoped>
  div.radio {
    margin: 5px 0;
  }
</style>

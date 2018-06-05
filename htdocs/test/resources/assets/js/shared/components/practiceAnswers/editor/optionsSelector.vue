<template>
  <div>
    <div class="row">
      <div class="col-xs-12">
        <label>Варианты ответов</label>
      </div>
    </div>

    <div class="row" v-if="internalAnswers.length === 0">
      <div class="col-xs-12">
        Пусто
      </div>
    </div>

    <template v-for="(answer, index) in orderedAnswers">
      <div class="row" :class="{top15: index !== 0}">
        <div class="col-xs-1 correct-answer" v-if="showCorrect">
          <input type="checkbox" v-model="answer.correct" />
        </div>
        <div :class="[showCorrect ? 'col-xs-7' : 'col-xs-8']">
          <input type="text" v-model="answer.value" class="form-control" />
        </div>
        <div class="col-xs-1">
          <button class="btn btn-default" :disabled="!canMoveDown(answer)" @click.prevent="moveDown(answer)">↓</button>
        </div>
        <div class="col-xs-1">
          <button class="btn btn-default" :disabled="!canMoveUp(answer)" @click.prevent="moveUp(answer)">↑</button>
        </div>
        <div class="col-xs-1">
          <button class="btn btn-default" @click.prevent="remove(answer)">x</button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-11" :class="{'col-xs-offset-1': showCorrect}" v-if="errors['answers.' + index + '.value'] != null">
          <error-messages :errors="errors['answers.' + index + '.value']"></error-messages>
        </div>
      </div>
    </template>

    <div class="row">
      <div class="col-xs-11" :class="{'col-xs-offset-1': showCorrect}" v-if="errors.answers != null">
        <error-messages :errors="errors.answers"></error-messages>
      </div>
    </div>

    <div class="row pull-right" :class="{top15: internalAnswers.length}">
      <div class="col-xs-12">
        <div class="form-group">
          <label>&nbsp;</label>
          <button class="btn btn-success" @click.prevent="addAnswer()">Добавить вариант ответа</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import _ from 'lodash/fp';

export default {

  props: {
    type: String,
    answers: Array,
    errors: null,
    showCorrect: {
      type: Boolean,
      default: true,
    },
  },

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
  },

  data() {
    return {
      internalAnswers: this.answers,
    };
  },

  computed: {
    orderedAnswers() {
      return _.sortBy(answer => answer.order)(this.internalAnswers);
    },
  },

  methods: {
    update() {
      this.resetOrder();
      this.$emit('update:answers', this.orderedAnswers);
    },

    addAnswer() {
      this.internalAnswers.push({
        correct: this.type === 'multiple_value',
        value: '',
      });
      this.update();
    },

    moveUp(answer) {
      if (!this.canMoveUp(answer)) {
        return;
      }

      const index = answer.order - 1;

      // Remove answer from array
      const answers = _.reject(x => x.order === answer.order)(this.internalAnswers);
      // Insert answer to array under new index
      answers.splice(index - 1, 0, answer);
      this.internalAnswers = answers;
      this.update();
    },

    moveDown(answer) {
      if (!this.canMoveDown(answer)) {
        return;
      }

      const index = answer.order - 1;

      // Remove answer from array
      const answers = _.reject(x => x.order === answer.order)(this.internalAnswers);
      // Insert answer to array under new index
      answers.splice(index + 1, 0, answer);
      this.internalAnswers = answers;
      this.update();
    },

    remove(answer) {
      this.internalAnswers = _.reject(x => x.order === answer.order)(this.internalAnswers);
      this.update();
    },

    // Set order property to every answer, based on its position in array
    resetOrder() {
      // Create map function that supports index parameter, passed to callback
      const map = _.map.convert({ cap: false });
      this.internalAnswers = map((answer, index) =>
        _.assignAll([{}, answer, {
          order: index + 1,
        }])
      )(this.internalAnswers);
    },

    canMoveUp(answer) {
      const index = answer.order - 1;
      return index !== 0 && this.internalAnswers.length > 0;
    },

    canMoveDown(answer) {
      const index = answer.order - 1;
      return index !== this.internalAnswers.length - 1 && this.internalAnswers.length > 0;
    },
  },

  watch: {
    answers(newValue) {
      this.internalAnswers = newValue;
    },

    type(newValue) {
      // In multiple value type all options should be selected as correct
      // to prevent validation errors
      if (newValue === 'multiple_value') {
        this.internalAnswers = _.map(answer =>
          _.assignAll([{}, answer, {
            correct: true,
          }])
        )(this.internalAnswers);
        this.update();
        this.errors.answers = null;
      }
    },
  },

};
</script>

<style>
  .correct-answer {
    padding-top: 8px;
  }
</style>

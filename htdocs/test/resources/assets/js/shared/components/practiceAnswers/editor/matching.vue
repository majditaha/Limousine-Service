<template>
  <div>
    <div class="row">
      <div class="col-xs-12">
        <label>Сопоставление</label>
      </div>
    </div>

    <div class="row" v-if="internalAnswers.length === 0">
      <div class="col-xs-12">
        Пусто
      </div>
    </div>

    <template v-for="(answer, index) in orderedAnswers">
      <div class="row">
        <div class="col-xs-1">
          <div class="form-group letter">
            {{ index | letter }}
          </div>
        </div>
        <div class="col-xs-9">
          <div class="form-group">
            <input type="text" v-model="answer.value" class="form-control" />
          </div>
        </div>
        <div class="col-xs-1">
          <button class="btn btn-default" @click.prevent="remove(answer)">x</button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-11 col-xs-offset-1" v-if="errors['answers.' + index + '.value'] != null">
          <error-messages :errors="errors['answers.' + index + '.value']"></error-messages>
        </div>
      </div>
    </template>

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
    answers: Array,
    errors: null,
  },

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
  },

  filters: {
    letter(index) {
      return String.fromCharCode(index + 1040);
    },
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
        correct: true,
        value: '',
      });
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
  },

  watch: {
    answers(newValue) {
      this.internalAnswers = newValue;
    },
  },

};
</script>

<style>
  .letter {
    padding-top: 8px;
  }
</style>

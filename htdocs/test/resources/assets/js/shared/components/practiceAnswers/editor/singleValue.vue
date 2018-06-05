<template>
  <div>
    <div class="row">
      <div class="col-xs-12">
        <label>Ответы</label>
      </div>
    </div>

    <div class="row" v-if="internalAnswers.length">
      <div class="col-xs-12">
        <input type="text" v-model="internalAnswers[0].value" class="form-control" />
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <error-messages :errors="errors['answers.0.value']"></error-messages>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12" v-if="errors.answers != null">
        <error-messages :errors="errors.answers"></error-messages>
      </div>
    </div>
  </div>
</template>
<script>
export default {

  props: {
    answers: Array,
    errors: null,
  },

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
  },

  created() {
    if (this.answers.length === 0) {
      this.addAnswer();
    }
    else {
      this.internalAnswers = this.answers;
    }
  },

  data() {
    return {
      internalAnswers: [],
    };
  },

  methods: {
    update() {
      this.$emit('update:answers', this.internalAnswers);
    },

    addAnswer() {
      this.internalAnswers.push({
        correct: true,
        value: '',
        order: 1,
      });
      this.update();
    },
  },

  watch: {
    answers(newValue) {
      if (newValue == null || newValue.length === 0) {
        this.internalAnswers = [];
        this.addAnswer();
      }

      this.internalAnswers = newValue;
    },
  },

};
</script>

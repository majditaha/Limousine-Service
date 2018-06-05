<template>
  <div>
    <div v-if="showTitle" class="title">Впишите ответ</div>
    <div class="row">
      <div class="col-12">
        <div class="input" :class="{success: highlightResults}">
          <textarea rows="3" cols="30" :disabled="disabled" v-model="selectedValue" @change="updateValue(answer)"></textarea>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
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
    answer() {
      return this.answers[0];
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

    // eslint-disable-next-line lodash-fp/prefer-constant
    isAllCorrect() {
      return true;
    },
  },

  watch: {
    value(newVal) {
      this.internalValue = newVal;
    },
  },

};
</script>

<style lang="scss" scoped>
  input {
    display: inline-block;
    width: auto;
  }
</style>

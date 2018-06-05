<template>
  <div>
    <template v-for="(practice, index) in practices">
      <h2>Практическое задание №{{ practice.id }}</h2>
      <practice-item
        :practice="practice"
        :first-check-answers.sync="firstCheckAnswers[index]"
        :answers.sync="selectedAnswers[index]"
        :show-check-button="firstCheckAnswers[index] == null"
        :disabled="disabledAnswers[index]"
        @update:firstCheckAnswers="onChecked(practice, index)"
        @consult-click="onConsultClicked(practice)"
      ></practice-item>
    </template>
    <consult-modal :practice="currentConsultPractice" :message-type="currentConsultType"></consult-modal>
  </div>
</template>
<script>
import _ from 'lodash/fp';
import Account from '_shared/services/Account';

export default {

  props: {
    practices: Array,
    setFinishedOnCheck: {
      type: Boolean,
      default: true,
    },
  },

  components: {
    PracticeItem: () => import('_components/practiceItem'),
    ConsultModal: () => import('_components/consultModal'),
  },

  data() {
    return {
      firstCheckAnswers: [],
      selectedAnswers: [],
      disabledAnswers: [],

      currentConsultPractice: null,
      currentConsultType: 'check_request',
    };
  },

  methods: {
    areAnswersGiven(index) {
      return _.size(this.firstCheckAnswers[index]) > 0;
    },

    setPracticeFinished(practice, answers) {
      if (practice.finished) {
        return null;
      }

      return Account.setPracticeFinished(practice.id, answers);
    },

    onChecked(practice, index) {
      if (!this.areAnswersGiven(index)) {
        return;
      }
      this.$emit('answer', practice);

      const emitFinished = () => {
        if (this.firstCheckAnswers.length === this.practices.length) {
          this.$emit('finished', this.firstCheckAnswers);
        }
      };

      if (this.setFinishedOnCheck) {
        const promise = this.setPracticeFinished(practice, this.firstCheckAnswers[index]);
        if (promise != null) {
          promise.then(emitFinished);
        }
      }
      else {
        emitFinished();
      }
    },

    onConsultClicked(practice) {
      this.currentConsultPractice = practice;
      this.currentConsultType = 'check_request';
      this.$modals.consult.$show();
    },
  },

};
</script>

<template>
  <div>
    <loading-indicator :loading="loading">
      <h1 v-if="variant != null">Тест</h1>
      <template v-for="(practice, index) in practices">
        <h2>Практическое задание №{{ index + 1 }}</h2>
        <practice-item
          v-if="practice != null"
          :show-theory-button="false"
          :practice="practice"
          :first-check-answers.sync="firstCheckAnswers[index]"
          :answers.sync="selectedAnswers[index]"
          :show-check-button="firstCheckAnswers[index] == null"
          :disabled="disabledAnswers[index]"
          @update:firstCheckAnswers="onChecked(practice, index)"
          @consult-click="onConsultClicked(practice)"
          @check-test-click="onCheckTestClicked(practice)"
          ></practice-item>
      </template>
      <div class="text-right mt-4" v-if="showFinishButton">
        <a href="#" class="btn btn-black" @click.prevent="finish()">Завершить</a>
      </div>
    </loading-indicator>
    <consult-modal :practice="currentConsultPractice" :message-type="currentConsultType"></consult-modal>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
import Variant from '_shared/resources/Variant';
import Auth from '_shared/services/Auth';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';

export default {

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) === -1) {
      next(false);
      return;
    }
    next();
  },

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    PracticeItem: () => import('_components/practiceItem'),
    FoundMistakeBtn: () => import('_components/foundMistakeBtn'),
    ConsultModal: () => import('_components/consultModal'),
    DisableModal: () => import('_components/disableModal'),
  },

  created() {
    this.loadDiscipline();
  },

  data() {
    return {
      discipline: null,

      loading: true,

      currentConsultPractice: null,
      currentConsultType: 'check_request',

      currentIndex: 0,

      variant: null,

      selectedAnswers: [],
      firstCheckAnswers: {},
      disabledAnswers: [],
    };
  },

  computed: {
    practices() {
      if (this.variant == null) {
        return [];
      }

      return this.variant.practices;
    },

    showFinishButton() {
      const firstCheckAnswers = _.values(this.firstCheckAnswers).filter(answer => answer != null);
      return firstCheckAnswers.length === this.practices.length;
    },
  },

  methods: {
    loadDiscipline() {
      this.loading = true;

      const id = this.$route.params.disciplineId;

      Discipline.get({ id }).then((response) => {
        this.discipline = response.data;
      })
        .then(() => {
          const variantId = this.$route.params.variantId;
          return Variant.get({ id: variantId, with: 'practices.answers.userAnswers' });
        })
        .then((response) => {
          if (_.isEmpty(response.data)) {
            return;
          }
          this.variant = response.data;

          this.fillBreadcrumbs();
          this.fillSelectedAnswers();
        })
        .finally(() => {
          this.loading = false;
        });
    },

    fillBreadcrumbs() {
      this.$breadcrumbs = [
        { title: this.discipline.name, url: `/discipline/${this.discipline.id}` },
        { title: 'Тесты', url: `/discipline/${this.discipline.id}/tests` },
        { title: this.variant.name, url: null },
      ];
    },

    fillSelectedAnswers() {
      this.selectedAnswers =
        _.map(practice => practice.selectedAnswers)(this.variant.practices);

      this.firstCheckAnswers = _.reduce((carry, practice) => {
        if (practice.selectedAnswers == null) {
          return carry;
        }

        const index = _.indexOf(practice)(this.variant.practices);
        return { ...carry, [index]: practice.selectedAnswers };
      }, {})(this.variant.practices);

      this.disabledAnswers =
        _.map(practice => practice.selectedAnswers != null)(this.variant.practices);
    },

    finish() {
      this.$router.push({
        name: 'tests',
      });
    },

    setPracticeFinished(practice, answers) {
      if (practice.finished) {
        return null;
      }

      return Account.setPracticeFinished(practice.id, answers);
    },

    reset() {
      this.selectedAnswers = [];
      this.disabledAnswers = [];
      this.firstCheckAnswers = {};
    },

    areAnswersGiven(index) {
      return _.size(this.firstCheckAnswers[index]) > 0;
    },

    onChecked(practice, index) {
      if (!this.areAnswersGiven(index)) {
        return;
      }
      this.setPracticeFinished(practice, this.firstCheckAnswers[index]);
    },

    onConsultClicked(practice) {
      if (!this.Auth.user.canCreateRequests) {
        this.$modals.confirm({
          message: 'У вас закончилось количество доступных запросов на консультацию. Приобрести?',
          approveLabel: 'Да',
          cancelLabel: 'Нет',
          onApprove: () => {
            this.$router.push({ name: 'plans' });
          },
        });
        return;
      }
      this.currentConsultPractice = practice;
      this.currentConsultType = 'check_request';
      this.$modals.consult.$show();
    },

    onCheckTestClicked(practice) {
      if (!this.Auth.user.canCreateTestRequests) {
        this.$modals.confirm({
          message: 'У вас закончилось количество доступных проверок. Приобрести?',
          approveLabel: 'Да',
          cancelLabel: 'Нет',
          onApprove: () => {
            this.$router.push({ name: 'plans' });
          },
        });
        return;
      }
      this.currentConsultPractice = practice;
      this.currentConsultType = 'check_test';
      this.$modals.consult.$show();
    },
  },

};
</script>

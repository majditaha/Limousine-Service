<template>
  <div>
    <loading-indicator :loading="loading">
      <div class="block" v-if="currentPractice == null">
        Нет задач
        <div class="text-right mt-4">
          <router-link :to="{ name: 'discipline' }" class="btn btn-black">В меню</router-link>
        </div>
      </div>
      <practice-item
        v-if="currentPractice != null"
        :practice="currentPractice"
        :first-check-answers.sync="firstCheckAnswers"
        :answers.sync="selectedAnswers"
        :show-check-button="showCheckButton"
        @consult-click="onConsultClicked(currentPractice)"
      >
        <div slot="solutionButtons">
          <div class="text-right mt-4" v-if="nextPractice != null">
            <div v-scroll-to="'#header'" class="btn btn-black" @click="next()">
              Далее
            </div>
          </div>
          <div class="text-right mt-4" v-if="nextPractice == null">
            <div class="btn btn-black" @click="finish()">
              Закончить
            </div>
          </div>
        </div>
      </practice-item>
    </loading-indicator>
    <consult-modal v-if="currentPractice != null" :practice="currentPractice"></consult-modal>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
import Practice from '_shared/resources/Practice';
import Auth from '_shared/services/Auth';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';

export default {

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) !== -1) {
      next();
    }
  },

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ConsultModal: () => import('_components/consultModal'),
    PracticeItem: () => import('_components/practiceItem'),
    DisableModal: () => import('_components/disableModal'),
  },

  created() {
    if (this.auto) {
      this.loadDisciplineWithoutSections();
      this.loadPractices();
    }
    else {
      this.loadDisciplineWithSections();
    }
  },

  data() {
    return {
      discipline: null,

      loading: true,

      currentIndex: 0,

      selectedAnswers: {},

      // Answers that user has selected on first check
      firstCheckAnswers: null,

      loadedPractices: [],
    };
  },

  computed: {
    section() {
      if (this.auto || this.discipline == null) {
        return null;
      }

      const { sectionId } = this.$route.params;
      return _.find(section => section.id === +sectionId)(this.discipline.sections);
    },

    practices() {
      if (!this.auto) {
        if (this.section == null) {
          return [];
        }

        return this.section.practices;
      }
      return this.loadedPractices;
    },

    currentPractice() {
      return this.practices[this.currentIndex];
    },

    nextPractice() {
      return this.practices[this.currentIndex + 1];
    },

    showCheckButton() {
      return this.firstCheckAnswers == null;
    },

    auto() {
      return this.$route.params.sectionId === 'auto';
    },
  },

  methods: {
    loadPractices() {
      this.loading = true;

      Practice.getSmart().then((response) => {
        this.loadedPractices = response.data;
      }).finally(() => {
        this.loading = false;
      });
    },

    loadDisciplineWithoutSections() {
      const id = this.$route.params.disciplineId;
      Discipline.get({ id }).then((response) => {
        this.discipline = response.data;
        this.fillBreadcrumbs(false);
      });
    },

    loadDisciplineWithSections() {
      this.loading = true;

      const id = this.$route.params.disciplineId;
      const relations = [
        'sections.practicesOfPracticeType.answers',
        'sections.practicesOfPracticeType.userProgresses',
      ].join(',');
      Discipline.get({ id, with: relations }).then((response) => {
        this.discipline = response.data;
        this.currentIndex = this.findStartingPracticeIndex();
        this.fillBreadcrumbs();
      }).finally(() => {
        this.loading = false;
      });
    },

    fillBreadcrumbs(includeSection = true) {
      this.$breadcrumbs = [
        { title: this.discipline.name, url: `/discipline/${this.discipline.id}` },
        { title: 'Практика', url: `/discipline/${this.discipline.id}/practices` },
        includeSection
          ? { title: this.section.name, url: `/discipline/${this.discipline.id}/practices/${this.section.id}` }
          : null,
        { title: 'Тест', url: null },
      ];
    },

    // Find first practice that user has stopped on
    findStartingPracticeIndex() {
      const index = _.findIndex(practice => !practice.finished)(this.practices);
      return index === -1 ? 0 : index;
    },

    prev() {
      this.currentIndex -= 1;
    },

    next() {
      this.setFinished();
      this.reset();
      this.currentIndex += 1;
    },

    // Exit from practice
    finish() {
      this.setFinished();
      if (this.auto) {
        this.$router.push({ name: 'discipline' });
      }
      else {
        this.$router.push({ name: 'practices', params: { disciplineId: this.$route.params.disciplineId, sectionId: null } });
      }
    },

    // Set practice as finished and save answers
    setFinished() {
      Account.setPracticeFinished(this.currentPractice.id, this.firstCheckAnswers);
    },

    // Get everything back to defaults to prepare for next practice
    reset() {
      this.firstCheckAnswers = null;
      this.selectedAnswers = {};
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
      this.$modals.consult.$show();
    },
  },

};
</script>

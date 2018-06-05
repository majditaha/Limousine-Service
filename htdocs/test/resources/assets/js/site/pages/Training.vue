<template>
  <div>
    <loading-indicator :loading="loading">
      <div class="block" v-if="section == null">
        Вы прошли все темы
        <div class="text-right mt-4">
          <router-link :to="{ name: 'discipline' }" class="btn btn-black">В меню</router-link>
        </div>
      </div>
      <div v-else>
        <small class="subtitle" v-if="section != null">Тема ({{ sectionIndex + 1 }}/{{ discipline.sections.length }}):</small>
        <h1 v-if="section != null">{{ section.name }}</h1>
        <div class="block">
          <found-mistake-btn></found-mistake-btn>
          <p class="noselect" v-if="currentTheory != null" v-html="currentTheory.text"></p>
          <div v-if="currentTheory != null && currentTheory.textPdf != null">
            <pdf-viewer :src="currentTheory.textPdf"></pdf-viewer>
          </div>
        </div>
        <template v-for="(practice, index) in practices">
          <h2 v-if="practice != null">Упражнение №{{ practice.id }}</h2>
          <practice-item
            v-if="practice != null"
            theory-button-action="scrollTop"
            :practice="practice"
            :first-check-answers.sync="firstCheckAnswers[index]"
            :answers.sync="selectedAnswers[index]"
            :show-check-button="firstCheckAnswers[index] == null"
            :disabled="disabledAnswers[index]"
            @update:firstCheckAnswers="onChecked(practice, index)"
            @consult-click="onConsultClicked(practice)"
            ></practice-item>
        </template>
        <div class="text-right mt-4" v-if="showNextButton">
          <a href="#" v-scroll-to="'#header'" class="btn btn-black" @click.prevent="next()">Далее</a>
        </div>
        <div class="text-right mt-4" v-if="showFinishButton">
          <a href="#" class="btn btn-black" @click.prevent="finish()">Завершить</a>
        </div>
      </div>
    </loading-indicator>
    <consult-modal :practice="currentConsultPractice"></consult-modal>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
import Section from '_shared/resources/Section';
import Auth from '_shared/services/Auth';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';

function loadLatestInTraining(disciplineId) {
  return Section.getLatestInTraining({ disciplineId }).then(response => response.data);
}

function loadSection(id) {
  return Section.get({ id }).then(response => response.data);
}

export default {

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) === -1) {
      next(false);
    }
    const promise = to.params.sectionId != null
      ? loadSection(to.params.sectionId)
      : loadLatestInTraining(to.params.disciplineId);

    promise.then((section) => {
      if (section != null && section.theoriesFinished && section.practicesFinished) {
        next({
          name: 'trainingPractices',
          params: {
            sectionId: section.id,
          },
        });
        return;
      }

      next();
    });
  },

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    PracticeItem: () => import('_components/practiceItem'),
    FoundMistakeBtn: () => import('_components/foundMistakeBtn'),
    ConsultModal: () => import('_components/consultModal'),
    DisableModal: () => import('_components/disableModal'),
    PdfViewer: () => import('_components/pdfViewer'),
  },

  created() {
    this.loadDiscipline();
  },

  data() {
    return {
      discipline: null,

      loading: true,

      currentConsultPractice: null,

      currentIndex: 0,

      currentSectionId: null,

      section: null,

      selectedAnswers: [],
      firstCheckAnswers: {},
      disabledAnswers: [],
    };
  },

  computed: {
    sectionIndex() {
      if (this.discipline == null) {
        return 0;
      }
      return _.findIndex(section => section.id === this.section.id)(this.discipline.sections);
    },

    theories() {
      if (this.section == null) {
        return [];
      }

      return this.section.theories;
    },

    currentTheory() {
      return this.theories[this.currentIndex];
    },

    nextTheory() {
      return this.theories[this.currentIndex + 1];
    },

    practices() {
      if (this.currentTheory == null) {
        return [];
      }

      return this.currentTheory.practices;
    },

    showNextButton() {
      const firstCheckAnswers = _.values(this.firstCheckAnswers).filter(answer => answer != null);
      return this.nextTheory != null && firstCheckAnswers.length === this.practices.length;
    },

    showFinishButton() {
      const firstCheckAnswers = _.values(this.firstCheckAnswers).filter(answer => answer != null);
      return this.nextTheory == null && firstCheckAnswers.length === this.practices.length;
    },
  },

  methods: {
    loadDiscipline() {
      this.loading = true;

      const id = this.$route.params.disciplineId;

      Discipline.get({ id, with: 'sections' }).then((response) => {
        this.discipline = response.data;
      })
        .then(() => (
          this.$route.params.sectionId != null
            ? loadSection(this.$route.params.sectionId)
            : loadLatestInTraining(id)
        ))
        .then((section) => {
          if (_.isEmpty(section)) {
            return;
          }
          this.section = section;

          this.currentIndex = this.findStartingTheoryIndex();
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
        { title: 'Обучение', url: `/discipline/${this.discipline.id}/trainings` },
        { title: this.section.name, url: null },
      ];
    },

    // Find first theory that user has stopped on
    findStartingTheoryIndex() {
      return _.findIndex(theory => !theory.finished)(this.theories);
    },

    fillSelectedAnswers() {
      this.selectedAnswers =
        _.map(practice => practice.selectedAnswers)(this.currentTheory.practices);

      this.firstCheckAnswers = _.reduce((carry, practice) => {
        if (practice.selectedAnswers == null) {
          return carry;
        }

        const index = _.indexOf(practice)(this.currentTheory.practices);
        return { ...carry, [index]: practice.selectedAnswers };
      }, {})(this.currentTheory.practices);

      this.disabledAnswers =
        _.map(practice => practice.selectedAnswers != null)(this.currentTheory.practices);
    },

    prev() {
      this.currentIndex -= 1;
    },

    next() {
      if (this.practices.length === 0) {
        this.setTheoryFinished();
      }

      this.reset();
      this.currentIndex += 1;
    },

    finish() {
      this.setTheoryFinished();
      this.$router.push({
        name: 'trainingPractices',
        params: {
          sectionId: this.section.id,
        },
      });
    },

    setTheoryFinished() {
      if (this.currentTheory.finished) {
        return;
      }

      Account.setTheoryFinished(this.currentTheory.id, true);
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
      const promise = this.setPracticeFinished(practice, this.firstCheckAnswers[index]);

      if (promise != null) {
        promise.then(() => {
          if (_.values(this.firstCheckAnswers).length === this.practices.length) {
            this.setTheoryFinished();
          }
        });
      }
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

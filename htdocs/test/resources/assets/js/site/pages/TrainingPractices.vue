<template>
  <div>
    <loading-indicator :loading="loading">
      <div class="block" v-if="notAllow">
        Задачи по данному блоку уже завершены
        <div class="text-right mt-4">
          <router-link :to="{ name: 'discipline' }" class="btn btn-black">В меню</router-link>
        </div>
      </div>
      <div v-else>
        <h1 v-if="section != null">{{ section.name }}</h1>
        <trainings-list
          v-if="practices.length > 0"
          :practices="practices"
          @answer="onAnswer"
          :set-finished-on-check="false"
          @finished="onFinished"
          ></trainings-list>
        <div class="block" v-else>
          В настоящий момент данный блок не заполнен, приносим свои извинения.
          <div class="mt-4">
            <router-link :to="{ name: 'discipline' }" class="btn btn-green">В меню</router-link>
          </div>
        </div>
      </div>
    </loading-indicator>
    <div class="text-right mt-4" v-if="showFinishButton">
      <a href="#" class="btn btn-black" @click.prevent="finish()">Завершить</a>
    </div>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
import Practice from '_shared/resources/Practice';
import Account from '_shared/services/Account';
import _ from 'lodash/fp';

export default {

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    TrainingsList: () => import('_components/trainingsList'),
    DisableModal: () => import('_components/disableModal'),
  },

  created() {
    this.loadPractices();
    this.loadDiscipline();
  },

  data() {
    return {
      discipline: null,
      practices: [],

      loading: true,

      notAllow: false,

      answeredPractices: [],

      firstCheckAnswers: [],
    };
  },

  computed: {
    section() {
      if (this.discipline == null) {
        return null;
      }

      const { sectionId } = this.$route.params;
      return _.find(section => section.id === +sectionId)(this.discipline.sections);
    },

    showFinishButton() {
      return this.practices.length && this.answeredPractices.length === this.practices.length;
    },
  },

  methods: {
    loadPractices() {
      this.loading = true;

      const { sectionId } = this.$route.params;
      Practice.getTrainings({ sectionId }).then((response) => {
        this.practices = response.data;
      }).finally(() => {
        this.loading = false;
      });
    },

    loadDiscipline() {
      const id = this.$route.params.disciplineId;

      const relations = [
        'sections.theoriesWithTheoryPractices.userTrainingProgresses',
        'sections.practicesOfTheoryType.userProgresses',
        'sections.userProgresses',
      ].join(',');
      Discipline.get({ id, with: relations }).then((response) => {
        this.discipline = response.data;
        this.notAllow =
          !this.section.canPassAgain &&
          (this.section.finished ||
            (!this.section.theoriesFinished && !this.section.practicesFinished)
          );
        this.fillBreadcrumbs(response.data);
      });
    },

    fillBreadcrumbs() {
      this.$breadcrumbs = [
        { title: this.discipline.name, url: `/discipline/${this.discipline.id}` },
        { title: 'Обучение', url: `/discipline/${this.discipline.id}/trainings` },
        { title: this.section.name, url: null },
      ];
    },

    onFinished(firstCheckAnswers) {
      this.firstCheckAnswers = firstCheckAnswers;
    },

    setPracticeFinished(practice, answers) {
      if (practice.finished) {
        return null;
      }

      return Account.setPracticeFinished(practice.id, answers, true);
    },

    finish() {
      const promises = this.answeredPractices.map((practice, index) => {
        const answers = this.firstCheckAnswers[index];
        return this.setPracticeFinished(practice, answers);
      });

      Promise.all(promises).then(() => {
        Account.setSectionFinished(this.section.id).then(() => {
          this.$router.push({ name: 'trainings' });
        });
      });
    },

    onAnswer(practice) {
      this.answeredPractices.push(practice);
    },
  },

};
</script>

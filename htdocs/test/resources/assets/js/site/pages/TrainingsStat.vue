<template>
  <div>
    <loading-indicator :loading="loading">
      <h1 v-if="discipline != null">{{ discipline.name }}</h1>
      <div class="block" v-if="sections.length === 0">
        Вы не прошли ни одной темы
        <div class="text-right mt-4">
          <router-link :to="{ name: 'trainings' }" class="btn btn-black">Перейти к обучению</router-link>
        </div>
      </div>
      <template v-else>
        <h2>Выберите задание</h2>
        <div class="block mb-4" v-for="(section, index) in sections">
          <div class="row align-items-center">
            <div class="col-3">
              <b>Блок {{ index + 1 }}.</b> {{ section.name }}
            </div>
            <div class="col">
              <div>
                <img src="/fonts/icon-theory.svg" alt=""> Теория: <span class="text-green font-weight-bold">{{ getTheoriesPercent(section) }}%</span>
                ({{ getFinishedTheories(section).length }}/{{ section.theories.length }})
              </div>
              <div>
                <img src="/fonts/icon-practice.svg" alt=""> Практика: <span class="text-green font-weight-bold">{{ getTrainingsPercent(section) }}%</span>
                ({{ getCorrectAnswered(section) }}/{{ section.trainingsAnswerResults.length }})
              </div>
            </div>
            <div class="col text-center">
              Уровень знаний по теме:
              <div>
                <stars :value="getKnowledgeLevel(section)"></stars>
              </div>
            </div>
            <div class="col-2 text-right">
              <template v-if="section.canPassAgain">
                <router-link
                  v-if="!isSectionFinished(section)"
                  :to="{ name: 'training', params: { sectionId: section.id } }"
                  class="btn btn-green full-width"
                  >Продолжить</router-link>
                <button v-else class="btn btn-black simple full-width" @click="startSectionOver(section)">Начать заново</button>
              </template>
            </div>
          </div>
        </div>
      </template>
    </loading-indicator>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Section from '_shared/resources/Section';
import Discipline from '_shared/resources/Discipline';
import Auth from '_shared/services/Auth';
import _ from 'lodash/fp';

export default {

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) !== -1) {
      next();
    }
  },

  components: {
    Stars: () => import('_components/stars'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    DisableModal: () => import('_components/disableModal'),
  },

  mounted() {
    this.loading = true;

    const id = this.$route.params.disciplineId;

    Discipline.get({ id }).then((response) => {
      this.discipline = response.data;
      this.$breadcrumbs = [
        { title: this.discipline.name, url: `/discipline/${this.discipline.id}` },
        { title: 'Обучение', url: null },
      ];
    })
      .then(() =>
        Section.getFinished({ disciplineId: id })
      )
      .then((response) => {
        this.sections = response.data;
      })
      .finally(() => {
        this.loading = false;
      });
  },

  data() {
    return {
      discipline: null,

      loading: true,

      sections: [],
    };
  },

  methods: {
    getTrainingsPercent(section) {
      if (section.trainingsAnswerResults.length === 0) {
        return 0;
      }

      return _.floor(
        (this.getCorrectAnswered(section) / section.trainingsAnswerResults.length) * 100
      );
    },

    getCorrectAnswered(section) {
      return _.compact(section.trainingsAnswerResults).length;
    },

    getFinishedTheories(section) {
      return _.filter(theory => theory.finished)(section.theories);
    },

    getTheoriesPercent(section) {
      return _.floor((this.getFinishedTheories(section).length / section.theories.length) * 100);
    },

    getFinishedPractices(section) {
      return _.filter(practice => practice.finished)(section.practices);
    },

    isSectionFinished(section) {
      return section.practicesFinished && section.theoriesFinished && section.trainingsFinished;
    },

    getKnowledgeLevel(section) {
      const avg = (this.getTheoriesPercent(section) + this.getTrainingsPercent(section)) / 2;
      return avg / 20;
    },

    startSectionOver(section) {
      Section.dropProgress({ id: section.id }).then(() => {
        this.$router.push({
          name: 'trainings',
          params: {
            sectionId: section.id,
          },
        });
      });
    },
  },

};
</script>

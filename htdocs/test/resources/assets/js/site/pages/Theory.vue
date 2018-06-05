<template>
  <div>
    <loading-indicator :loading="loading">
      <div class="block">
        <found-mistake-btn :theory="currentTheory"></found-mistake-btn>
        <div class="noselect" v-if="currentTheory != null" v-html="currentTheory.text"></div>
        <div v-if="currentTheory != null && currentTheory.textPdf != null">
          <pdf-viewer ref="pdfViewer" :src="currentTheory.textPdf"></pdf-viewer>
        </div>
      </div>
      <div>
        <div class="pull-left mt-4" v-if="prevTheory != null">
          <a href="#" class="btn btn-black" @click.prevent="prev()">Назад</a>
        </div>
        <div class="text-right mt-4" v-if="nextTheory == null && $route.params.theoryId == null">
          <a href="#" class="btn btn-black" @click.prevent="finish()">Завершить</a>
        </div>
        <div class="text-right mt-4" v-else>
          <a href="#" v-scroll-to="'#header'" class="btn btn-black" @click.prevent="next()">Далее</a>
        </div>
      </div>
    </loading-indicator>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
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
    FoundMistakeBtn: () => import('_components/foundMistakeBtn'),
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

      currentIndex: 0,
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

    prevTheory() {
      return this.theories[this.currentIndex - 1];
    },
  },

  methods: {
    loadDiscipline() {
      this.loading = true;

      const id = this.$route.params.disciplineId;
      Discipline.get({ id, with: 'sections.theories.userProgresses' }).then((response) => {
        this.discipline = response.data;

        if (this.$route.params.theoryId) {
          this.currentIndex =
            _.findIndex(theory => theory.id === +this.$route.params.theoryId)(this.theories);
        }
        else {
          this.currentIndex = this.findStartingTheoryIndex();
        }
        this.fillBreadcrumbs();
      }).finally(() => {
        this.loading = false;
      });
    },

    fillBreadcrumbs() {
      this.$breadcrumbs = [
        { title: this.discipline.name, url: `/discipline/${this.discipline.id}` },
        { title: 'Теория', url: `/discipline/${this.discipline.id}/theories` },
        { title: this.section.name, url: null },
      ];
    },

    // Find first theory that user has stopped on
    findStartingTheoryIndex() {
      const index = _.findIndex(theory => !theory.finished)(this.theories);
      return index === -1 ? 0 : index;
    },

    prev() {
      this.currentIndex -= 1;
      this.$refs.pdfViewer.forceLoad();
    },

    next() {
      this.setFinished();
      this.currentIndex += 1;
      this.$refs.pdfViewer.forceLoad();
    },

    finish() {
      this.setFinished();
      this.$router.push({ name: 'theories', params: { disciplineId: this.$route.params.disciplineId, sectionId: null } });
    },

    setFinished() {
      if (this.currentTheory.finished) {
        return;
      }

      Account.setTheoryFinished(this.currentTheory.id, false);
    },
  },

};
</script>

<style scoped>
  .pull-left {
    float: left;
  }
</style>

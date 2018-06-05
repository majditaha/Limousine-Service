<template>
  <div class="profile_icons row full-height align-items-center">
    <div class="col">
      <div class="row justify-content-center" v-for="(sectionGroup, sectionIndex) in sections">
        <div class="item col-sm-2" v-for="(section, index) in sectionGroup">
          <router-link :to="{ name: 'practice', params: { sectionId: section.id } }">
            <div class="progress">
              <div class="bar" :style="{ height: getSectionFinishedPercent(section) + '%'}"></div>
              <span>{{ sectionIndex * 5 + index + 1 }}</span>
            </div>
            <span class="title">
              {{ section.name }}
              <small v-if="getSectionFinishedPercent(section) > 0">решено на {{ getSectionFinishedPercent(section) }}%</small>
            </span>
          </router-link>
        </div>
      </div>
    </div>
    <disable-modal v-if="Auth.user.timedOut" name="disabled" :close-by-esc="false"></disable-modal>
  </div>
</template>
<script>
import Discipline from '_shared/resources/Discipline';
import Auth from '_shared/services/Auth';
import _ from 'lodash/fp';

export default {

  components: {
    DisableModal: () => import('_components/disableModal'),
  },

  beforeRouteEnter(to, from, next) {
    if (Auth.user.disciplineIds.indexOf(+to.params.disciplineId) !== -1) {
      next();
    }
  },

  created() {
    const id = this.$route.params.disciplineId;
    Discipline.get({ id, with: 'sections.practicesOfPracticeType.userProgresses' }).then((response) => {
      this.discipline = response.data;
    });
  },

  data() {
    return {
      discipline: null,
    };
  },

  computed: {
    sections() {
      if (this.discipline == null) {
        return [];
      }

      return _.flow(
        _.filter(section => section.practices.length > 0),
        _.chunk(5),
      )(this.discipline.sections);
    },
  },

  methods: {
    getSectionFinishedPercent(section) {
      const finished = _.flow(
        _.filter(practice => !!practice.finished),
        _.size,
      )(section.practices);

      if (finished > 0) {
        return _.floor((finished / section.practices.length) * 100);
      }
      return 0;
    },
  },

};
</script>

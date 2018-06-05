<template>
  <div class="profile_icons row full-height align-items-center">
    <div class="col">
      <div class="row justify-content-center" v-for="(variantGroup, variantIndex) in variants">
        <div class="item col-sm-2" v-for="(variant, index) in variantGroup">
          <router-link :to="{ name: 'test', params: { variantId: variant.id } }">
            <div class="progress">
              <div class="bar" :style="{ height: getVariantFinishedPercent(variant) + '%'}"></div>
              <span>{{ variantIndex * 5 + index + 1 }}</span>
            </div>
            <span class="title">
              {{ variant.name }}
              <small v-if="getVariantFinishedPercent(variant) > 0">решено на {{ getVariantFinishedPercent(variant) }}%</small>
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
    Discipline.get({ id, with: 'variants.practices.userProgresses' }).then((response) => {
      this.discipline = response.data;
    });
  },

  data() {
    return {
      discipline: null,
    };
  },

  computed: {
    variants() {
      if (this.discipline == null) {
        return [];
      }

      return _.flow(
        _.filter(variant => variant.practices.length > 0),
        _.chunk(5),
      )(this.discipline.variants);
    },
  },

  methods: {
    getVariantFinishedPercent(variant) {
      const finished = _.flow(
        _.filter(practice => !!practice.finished),
        _.size,
      )(variant.practices);

      if (finished > 0) {
        return _.floor((finished / variant.practices.length) * 100);
      }
      return 0;
    },
  },

};
</script>

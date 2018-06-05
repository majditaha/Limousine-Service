<template>
  <div>
    <a href="#" @click.prevent="$modals.mistake.$show()" class="icon-error">
      <img src="/fonts/icon-error.svg" alt="">
    </a>
    <mistake-modal :practice="practice" :theory="theory"></mistake-modal>
  </div>
</template>
<script>
export default {

  props: {
    practice: null,
    theory: null,
  },

  components: {
    MistakeModal: () => import('_components/mistakeModal'),
  },

};
</script>

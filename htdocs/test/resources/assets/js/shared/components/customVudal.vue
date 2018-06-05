<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <slot></slot>
  </div>
</template>
<script>
import $ from 'jquery';
import modalMixin from 'vudal/dist/modalMixin';

export default {

  mixins: [modalMixin],

  mounted() {
    $(this.$el).on('click', '.close', () => {
      this.hide();
    });
    // To close modal by clicking on backdrop, but vudal-dimmer has z-index higher
    $(document).on('click', '.vudal-dimmer', () => {
      if (event.target !== $('.vudal-dimmer > div').children().get(0)) {
        return;
      }
      this.hide();
    });
  },

  destroyed() {
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
  },

  methods: {
    show() {
      modalMixin.methods.show.call(this);
      $('body').addClass('modal-open');

      const backdrop = $('<div/>').addClass('modal-backdrop show');
      $('body').append(backdrop);
    },

    hide() {
      modalMixin.methods.hide.call(this);
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();
    },
  },

};
</script>

<style lang="scss">
  .modal {
    display: block;
  }

  .modal-backdrop {
    z-index: 1000;
  }

  .vudal.hide {
    display: none;
  }
</style>

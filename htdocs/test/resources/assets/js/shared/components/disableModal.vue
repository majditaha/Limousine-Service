<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Время вышло</h5>
          </div>
          <div class="modal-body">
            <p>Выбранное вами время на сегодня закончилось</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import $ from 'jquery';
import modalMixin from 'vudal/dist/modalMixin';

export default {

  mixins: [modalMixin],

  mounted() {
    this.show();
  },

  destroyed() {
    this.$modals.removeModal(this);
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

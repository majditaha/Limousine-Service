<template>
  <custom-vudal name="mistake" :auto-center="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Сообщить об ошибке</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <message-input :practice="practice" :theory="theory" type="mistake" @message="onMessage" :can-upload-images="false"></message-input>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </custom-vudal>
</template>
<script>
export default {

  props: {
    practice: null,
    theory: null,
  },

  components: {
    CustomVudal: () => import('_components/customVudal'),
    MessageInput: () => import('_components/messageInput'),
  },

  methods: {
    onMessage() {
      this.$modals.mistake.$hide();
    },
  },

};
</script>

<style lang="scss">
  .image-preview {
    display: inline-block;
    margin-right: 10px;
    img {
      width: 100px;
      height: 100px;
    }
  }

  .file-container {
    margin-top: 10px;
  }
</style>

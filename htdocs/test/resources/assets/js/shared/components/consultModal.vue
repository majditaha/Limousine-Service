<template>
  <div>
    <custom-vudal name="consultMessage">
      <div class="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Запрос консультации</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <loading-indicator :loading="requestsLoading">
                <template v-if="availableRequests === 0">
                  <p>Пожалуйста оплатите запросы на консультацию для продолжения работы</p>
                  <div class="text-center">
                    <router-link :to="{ name: 'plans' }" class="btn btn-green">Перейти к тарифам</router-link>
                  </div>
                </template>
                <message-input v-else :practice="practice" :type="messageType" @message="onMessage"></message-input>
              </loading-indicator>
            </div>
            <div class="modal-footer">
              <!-- <a href="#" class="btn btn-black">Отправить</a> -->
            </div>
          </div>
        </div>
      </div>
    </custom-vudal>
    <custom-vudal name="consult">
      <div class="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Предупреждение</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body text-center">
              <p>Данная услуга платная, с вашего счета будет списана соответствующая сумма (подробнее см. раздел "Тарифы")</p>
            </div>
            <div class="modal-footer justify-content-center">
              <a href="#" class="btn btn-black" @click.prevent="showMessageModal()">Подтвердить</a>
              <a href="#" class="btn btn-black" @click.prevent="$modals.consult.$hide()">Отказаться</a>
            </div>
          </div>
        </div>
      </div>
    </custom-vudal>
  </div>
</template>
<script>
import Account from '_shared/services/Account';

export default {

  props: {
    practice: null,
    messageType: {
      type: String,
      default: 'check_request',
    },
  },

  components: {
    CustomVudal: () => import('_components/customVudal'),
    MessageInput: () => import('_components/messageInput'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
  },

  data() {
    return {
      availableRequests: 0,
      requestsLoading: null,
    };
  },

  methods: {
    showMessageModal() {
      this.$modals.consult.$hide();
      this.requestsLoading = Account.getAvailableResources().then((resources) => {
        this.availableRequests = this.messageType === 'check_request'
          ? resources.requests
          : resources.tests;
      });
      setTimeout(this.$modals.consultMessage.$show, 100);
    },

    onMessage() {
      this.$modals.consultMessage.$hide();
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

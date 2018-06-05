<template>
  <div>
    <a href="#" v-for="message in messages" @click.prevent="openMessage(message)">
      <div class="block mb-4" :class="{is_taken: message.takenAt != null}">
        <div class="row align-items-center">
          <div class="col-1">
            <div class="image-block full-width">
              <img v-if="message.history[0].sender.photo != null" :src="message.history[0].sender.photo" alt="">
              <img v-else src="/images/no_avatar.png" alt="">
            </div>
          </div>
          <div class="col-2">
            {{ message.history[0].sender.name }}
          </div>
          <div class="col">
            Запрос задания на проверку: <b>{{ getDisciplineName(message) }}</b>
          </div>
        </div>
      </div>
    </a>
    <div class="text-center mt-4" v-if="messages.length === 0">
      Пока нет ни одного запроса
    </div>
    <message-modal
      :can-answer="true"
      :message="current"
      :disciplines="disciplines"
      :inputLimit="inputLimit"
      @hide="updateData"
    ></message-modal>
    <custom-vudal name="startWarning" :auto-center="false" id="modal_warning">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Предупреждение</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <error-messages :errors="errors.message"></error-messages>
          <div class="modal-body">
            Невыполненные задания, скажутся на вашем рейтинге.
            <br> Вы готовы начать работу?
          </div>
          <div class="modal-footer">
            <button class="btn btn-green" id="accept_warning" @click.prevent="openMessageModal()">Подтвердить</button>
            <button type="button" class="btn btn-blue" @click.prevent="closeStartWarning()" data-dismiss="modal">Отказаться</button>
          </div>
        </div>
      </div>
    </custom-vudal>
  </div>
</template>

<script>
import _ from 'lodash/fp';
import Message from '_shared/resources/teacher/Message';
import Discipline from '_shared/resources/Discipline';
import ClientConfig from '_shared/services/ClientConfig';
import Noty from 'noty';

export default {

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
    MessageModal: () => import('_components/messageModal'),
    CustomVudal: () => import('_components/customVudal'),
  },

  created() {
    this.loadMessages();

    Discipline.query({ itemsPerPage: 999999, with: 'sections' }).then((response) => {
      this.disciplines = response.body.data;
    });
  },

  data() {
    return {
      disciplines: [],
      messages: [],
      errors: {},

      filter: {
      },

      current: {},

      loading: true,
    };
  },

  computed: {
    inputLimit() {
      return ClientConfig.answerLength * 1;
    },
  },

  methods: {
    openMessage(item) {
      this.current = JSON.parse(JSON.stringify(item));
      if (item.takenAt) {
        this.openMessageModal();
      }
      else {
        this.$modals.startWarning.$show();
      }
    },

    openMessageModal() {
      const id = this.current.history[0].id;
      Message.markTaken({ id }, {}).then(() => {
        this.errors = {};
        this.$modals.startWarning.$hide();
        this.$modals.message.$show();
      }).catch((response) => {
        this.errors = response.body.errors;
        new Noty({
          text: response.body.errors.message,
          type: 'error',
          timeout: 7000,
        }).show();
      });
    },

    closeStartWarning() {
      this.$modals.startWarning.$hide();
    },

    loadMessages() {
      this.loading = true;
      const params = {
        ...this.filter,
        available: true,
        itemsPerPage: 999999,
        type: 'check_request',
        with: 'practice.answers.userAnswers',
      };
      Message.query(params).then((response) => {
        this.messages = response.body.data;
        this.meta = response.body.meta;
      }).finally(() => {
        this.loading = false;
      });
    },

    getDisciplineName(item) {
      return _.flow(
        _.find(discipline => discipline.id === item.practice.disciplineId),
        _.get('name'),
      )(this.disciplines);
    },

    updateData() {
      this.loadMessages();
    },
  },
};
</script>

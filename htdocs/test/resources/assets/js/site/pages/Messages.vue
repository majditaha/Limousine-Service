<template>
  <div>
    <h2 class="green">Активные</h2>
    <div class="block">
      <table class="table">
        <thead>
          <tr>
            <th width="200">Отправлено</th>
            <th width="120">Тип</th>
            <th>Комментарий</th>
          </tr>
        </thead>
        <tbody>
          <tr class="clickable-row" v-for="item in activeMessages" @click="edit(item)">
            <td>{{ item.createdAt | timeAgo }}</td>
            <td>{{ item.practice.type | translateType }}</td>
            <td>{{ getSectionName(item) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <h2>Выполненные</h2>
    <div class="block">
      <table class="table">
        <thead>
          <tr>
            <th width="200">Выполнено</th>
            <th width="120">Тип</th>
            <th>Комментарий</th>
            <th>Оценка</th>
          </tr>
        </thead>
        <tbody>
          <tr class="clickable-row" v-for="item in finishedMessages" @click="edit(item)">
            <td>{{ item.createdAt | timeAgo }}</td>
            <td>{{ item.practice.type | translateType }}</td>
            <td>{{ getSectionName(item) }}</td>
            <td>
              <stars v-model="item.rating"></stars>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <message-modal
      :can-answer="false"
      :message="current"
      :disciplines="meta.disciplines"
      @answer="loadMessages()"
      @rating-updated="loadMessages()"
    ></message-modal>
  </div>
</template>
<script>
import Message from '_shared/resources/Message';
import Discipline from '_shared/resources/Discipline';
import MessageModal from '_components/messageModal';
import timeAgo from '_shared/filters/timeAgo';
import _ from 'lodash/fp';

export default {

  components: {
    MessageModal,
    Stars: () => import('_components/stars'),
  },

  filters: {
    timeAgo,

    translateType(type) {
      switch (type) {
        case 'theory':
          return 'Закрепление теории';
        case 'practice':
          return 'Практика';
        case 'testing':
          return 'Тест';
        case 'ege':
          return 'Тест ЕГЭ';
        default:
          return 'Неизвестно';
      }
    },
  },

  created() {
    this.loadMessages();

    Discipline.query({ itemsPerPage: 999999, with: 'sections' }).then((response) => {
      this.meta.disciplines = response.body.data;
    });
  },

  mounted() {
    if (this.$route.params.uid != null) {
      Message.query({ uid: this.$route.params.uid, with: 'practice.answers.userAnswers' }).then((response) => {
        const messages = response.data.data;
        if (!messages.length) {
          return;
        }

        this.edit(messages[0]);
      });
    }
  },

  data() {
    return {
      activeMessages: [],
      finishedMessages: [],

      current: {},

      meta: {
        disciplines: [],
      },
    };
  },

  methods: {
    edit(item) {
      this.current = JSON.parse(JSON.stringify(item));
      this.$modals.message.$show();
    },

    getSectionName(item) {
      return _.flow(
        _.flatMap(discipline => discipline.sections),
        _.find(section => section.id === item.practice.sectionId),
        _.get('name'),
      )(this.meta.disciplines);
    },

    loadMessages() {
      Message.query({ active: true, type: 'checks', with: 'practice.answers.userAnswers' }).then((response) => {
        this.activeMessages = response.body.data;
      });
      Message.query({ active: false, type: 'checks', with: 'practice.answers.userAnswers' }).then((response) => {
        this.finishedMessages = response.body.data;
      });
    },
  },

};
</script>

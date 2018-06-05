<template>
  <div>
    <div class="block">
      <div class="row align-items-center">
        <div class="col-2">Период</div>
        <div class="col-3">
          <datepicker v-model="filter.createdAtFrom" @input="loadMessages()"></datepicker>
        </div>
        <div class="col-1 text-center">
          <small>по</small>
        </div>
        <div class="col-3">
          <datepicker v-model="filter.createdAtTo" @input="loadMessages()"></datepicker>
        </div>
      </div>
    </div>
    <loading-indicator :loading="loading">
      <a href="#" v-for="message in messages" @click.prevent="openMessageModal(message)">
        <div class="block mt-5 mb-4">
          <div class="row align-items-center">
            <div class="col-1">
              <div class="image-block full-width">
                <img v-if="message.history[0].sender.photo != null" :src="message.history[0].sender.photo" alt="">
                <img v-else src="/images/no_avatar.png" alt="">
              </div>
            </div>
            <div class="col-1">{{ message.history[0].sender.name }}</div>
            <div class="col-2">
              {{ message.createdAt | format('shortTime') }}
            </div>
            <div class="col-2">
              Запрос задания на проверку:
              <div class="mt-3">
                <b>{{ getDisciplineName(message) }}</b>
              </div>
            </div>
            <div class="col-2">
              Затраченное время:
              <div class="mt-3"><b>{{ getSpentTimeInSeconds(message) | secondsToTime }}</b></div>
            </div>
            <div class="col-2">
              Оценка пользователя:
              <div class="mt-3">
                <stars :value="message.rating"></stars>
              </div>
            </div>
            <div class="col-2">
              Оплата:
              <div class="mt-3 text-green">{{ formatMessagePrice(message) }} р</div>
            </div>
          </div>
        </div>
      </a>
      <div class="mt-4 text-center" v-if="messages.length === 0">
        Вы пока не выполнили ни одного задания.
      </div>
      <div class="mt-4 text-center" v-if="messages.length !== 0">
        Итого за период: <b>{{ meta.total | pluralize('задание', 'задания', 'заданий') }}</b>,
        средний рейтинг: <b>{{ avgRating }}</b>,
        затрачено времени: <b>{{ totalTime | pluralize('минута', 'минуты', 'минут') }}</b>,
        сумма <b class="text-green">{{ totalPrice | pluralize('рубль', 'рубля', 'рублей') }}</b>
      </div>
    </loading-indicator>
    <message-modal
      :can-answer="false"
      :message="current"
      :disciplines="disciplines"
    ></message-modal>
  </div>
</template>
<script>
import _ from 'lodash/fp';
import moment from 'moment';
import Message from '_shared/resources/teacher/Message';
import Discipline from '_shared/resources/Discipline';
import format from '_shared/filters/format';
import pluralize from '_shared/filters/pluralize';

export default {

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    MessageModal: () => import('_components/messageModal'),
    Stars: () => import('_components/stars'),
    Datepicker: () => import('_components/datepicker'),
  },

  filters: {
    format,
    pluralize,
    secondsToTime(seconds) {
      const duration = moment.duration(seconds, 'seconds');
      const hours = duration.hours();
      const paddedMinutes = _.padCharsStart('0')(2)(duration.minutes());
      return `${hours}:${paddedMinutes}`;
    },
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

      filter: {
        createdAtFrom: null,
        createdAtTo: null,
      },

      current: {},

      meta: {},

      loading: true,
    };
  },

  computed: {
    avgRating() {
      if (this.messages.length === 0) {
        return 0;
      }

      return _.flow(
        _.map(message => message.rating),
        _.mean,
        _.floor,
      )(this.messages);
    },

    totalTime() {
      return _.flow(
        _.map(this.getSpentTimeInSeconds),
        _.sum,
        _.thru(seconds => _.floor(seconds / 60)),
      )(this.messages);
    },

    totalPrice() {
      return _.flow(
        _.map(message => message.price),
        _.sum,
        _.thru(sum => Math.floor(sum / 100)),
      )(this.messages);
    },
  },

  methods: {
    openMessageModal(item) {
      this.current = JSON.parse(JSON.stringify(item));
      this.$modals.message.$show();
    },

    loadMessages() {
      this.loading = true;
      const params = {
        ...this.filter,
        itemsPerPage: 999999,
        active: false,
        type: 'checks',
        with: 'practice.answers.userAnswers',
        sort: 'finished_at',
        order: 'desc',
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

    formatMessagePrice(message) {
      return Math.floor(message.price / 100);
    },

    getSpentTimeInSeconds(message) {
      const takenAt = moment(message.takenAt);
      const finishedAt = moment(message.finishedAt);

      const seconds = finishedAt.diff(takenAt, 'seconds');
      return seconds;
    },
  },

};
</script>

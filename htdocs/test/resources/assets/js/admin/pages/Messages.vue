<template>
  <div>
    <bs-filter name="messages" :current-filter="filter" @apply="applyFilter()" @clear="clearFilter()">
      <div class="col-xs-4">
        <div class="form-group">
          <label>От кого</label>
          <select class="form-control" v-model="filter.fromUserId">
            <option v-for="user in meta.users" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-4">
        <div class="form-group">
          <label>Кому</label>
          <select class="form-control" v-model="filter.toUserId">
            <option v-for="user in meta.users" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-4">
        <div class="form-group">
          <label>В тексте содержится</label>
          <input type="text" v-model="filter.content" class="form-control" />
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Дата с</label>
          <datepicker v-model="filter.createdAtFrom"></datepicker>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Дата по</label>
          <datepicker v-model="filter.createdAtTo"></datepicker>
        </div>
      </div>
      <div class="col-xs-4">
        <div class="form-group">
          <label>Тип сообщения</label>
          <select class="form-control" v-model="filter.type">
            <option v-for="type in types" :value="type">{{ type | translateType }}</option>
          </select>
        </div>
      </div>
      <div class="col-xs-2">
        <div class="form-group">
          <label>Прочитано</label>
          <div>
            <input type="checkbox" v-model="filter.read" />
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Оценка пользователя</label>
          <select class="form-control" v-model="filter.rating">
            <option v-for="rating in 5" :value="rating">{{ rating }}</option>
          </select>
        </div>
      </div>
    </bs-filter>

    <loading-indicator :loading="loading">
      <div class="table-responsive">
        <table class="table table-striped table-selectable">
          <thead>
            <tr>
              <th v-rest-sortable-column="'id'">#</th>
              <th v-rest-sortable-column="'from_user_id'">Отправитель</th>
              <th v-rest-sortable-column="'created_at'">Дата отправления</th>
              <th v-rest-sortable-column="'type'">Тип</th>
              <th v-rest-sortable-column="'readAt'">Прочитано</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.id }}</td>
              <td>{{ item.fromUserId | valueById(meta.users) }}</td>
              <td>{{ item.createdAt | format('ruTime') }}</td>
              <td>{{ item.type | translateType }}</td>
              <td>{{ item.readAt | yesNo }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </loading-indicator>

    <vudal name="message">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title">Сообщение #{{ current.id }} ({{ current.type | translateType }})</h4>
      </div>
      <div class="content">
        <loading-indicator :loading="historyPromise">
          <div class="row" v-if="currentHistory.length">
            <div class="col-xs-12" v-if="current.type === 'review' || current.type === 'faq'">
              <div class="form-group">
                <input type="checkbox" v-model="firstInHistory.public" />
                <label v-if="current.type === 'review'">В раздел "Отзывы"</label>
                <label v-if="current.type === 'faq'">В раздел "FAQ"</label>
              </div>
              <div class="form-group" v-if="current.type === 'review'">
                <input type="checkbox" v-model="firstInHistory.onMainPage" :disabled="!firstInHistory.public" />
                <label v-if="current.type === 'review'">Показывать на главной</label>
              </div>
            </div>
            <div class="col-xs-12" v-if="current.type === 'rating_explanation'">
              <div class="form-group">
                Рейтинг проставлен в сообщении №{{ current.ratingMessageId }}
              </div>
            </div>
            <div class="col-xs-12" v-if="current.type === 'check_request' || current.type === 'check_test'">
              <div class="form-group">
                <label>Оценка преподавателя:</label>
                <span v-if="firstInHistory.rating == null">еще не поставлена</span>
                <span v-else>{{ firstInHistory.rating }}</span>
              </div>
              <div class="form-group">
                <label>Обоснование рейтинга</label>
                <span v-if="current.ratingMessage == null">еще нет</span>
                <span v-else>{{ current.ratingMessage.content }}</span>
              </div>
            </div>
            <div class="col-xs-12">

              <div class="row" :class="{top15: index !== 0}" v-for="(message, index) in currentHistory">
                <div
                  class="message col-xs-7"
                  :class="[message.fromUserId === firstInHistory.fromUserId ? 'col-xs-offset-1' : 'col-xs-offset-4']"
                  >
                  <div class="center-block">
                    От: {{ message.sender.name }}
                  </div>
                  <div class="center-block">
                    Дата: {{ message.createdAt | format('ruTime') }}
                  </div>
                  <div class="center-block">
                    {{ message.content }}
                  </div>
                </div>
                <div class="col-xs-1"></div>
              </div>

            </div>
            <div class="col-xs-offset-1 col-xs-10 top15" v-if="current.type !== 'rating_explanation'">
              <div class="form-group">
                <label>Добавить ответ</label>
                <textarea rows="5" v-model="answerContent" class="form-control">
                </textarea>
              </div>
            </div>
          </div>
        </loading-indicator>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-default cancel">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-md" @click="save(firstInHistory)">Сохранить</button>
        <button type="button" class="btn btn-danger btn-md" @click="confirmDestroy(current.id)" v-if="current.id != null">Удалить</button>
      </div>
    </vudal>
  </div>
</template>
<script>
import rest from '_shared/mixins/rest';
import Message from '_shared/resources/admin/Message';
import _ from 'lodash/fp';
import clientConfig from '_shared/services/ClientConfig';
import valueById from '_shared/filters/valueById';
import format from '_shared/filters/format';
import yesNo from '_shared/filters/yesNo';
import restSortableColumn from '_shared/directives/restSortableColumn';

export default {

  mixins: [rest(Message)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    BsFilter: () => import('_components/bsFilter'),
    Vudal: () => import('vudal'),
    Datepicker: () => import('_components/datepicker'),
  },

  directives: {
    restSortableColumn,
  },

  filters: {
    valueById,
    format,
    yesNo,
    translateType(type) {
      switch (type) {
        case 'faq':
          return 'Вопрос';
        case 'review':
          return 'Отзыв';
        case 'check_request':
          return 'Запрос на проверку';
        case 'check_test':
          return 'Запрос на проверку теста';
        case 'message':
          return 'Сообщение';
        case 'mistake':
          return 'Сообщение об ошибке';
        case 'rating_explanation':
          return 'Обоснование рейтинга';
        case 'all':
          return 'Все';
        default:
          return 'Неизвестно';
      }
    },
  },

  mounted() {
    this.$on('editing', () => {
      this.loadHistory(this.current);
      this.$modals.message.$show();
    });
    this.$on('updated', () => {
      this.$modals.message.$hide();
      this.answerContent = '';
    });
    this.$on('destroyed', () => {
      this.$modals.message.$hide();
    });
  },

  data() {
    return {
      filterDefaults: {
        fromUserId: null,
        toUserId: null,
        content: null,
        createdAtFrom: null,
        createdAtTo: null,
        type: 'all',
        read: false,
        rating: null,
      },

      filterQueryParams: [
        'fromUserId',
        'type',
      ],

      params: {
        with: 'ratingMessage',
      },

      meta: {
        users: [],
        types: clientConfig.messageTypes,
      },

      currentHistory: [],
      historyPromise: null,

      answerContent: '',
    };
  },

  computed: {
    types() {
      return _.concat(['all'], this.meta.types);
    },

    firstInHistory() {
      return this.currentHistory[0];
    },
  },

  methods: {
    beforeSave(item) {
      return _.assignAll([{
        answer: this.answerContent,
      }, item]);
    },

    loadHistory(message) {
      const id = message.id;
      this.historyPromise = Message.getHistory({ id }).then((response) => {
        this.currentHistory = response.data;
      });
    },
  },

};
</script>

<style>
  .message {
    border: 1px solid rgba(34,36,38,.15);
    border-radius: 3px;
  }

  .center-block {
    text-align: center;
  }
</style>

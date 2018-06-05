<template>
  <custom-vudal name="message" :auto-center="false" id="message_modal" @hide="onHide">
    <teacher-timer v-if="showTeacherTimer" :taken-at="message.takenAt"></teacher-timer>
    <div v-if="message != null && message.practice != null" class="modal-dialog" role="document" style="max-width: 1140px;width: 100%; margin-right: auto;margin-left: auto;padding-right: 15px;padding-left: 15px;">
      <div class="modal-content" style="background: none;">
        <small class="subtitle">{{ initialMessage.sender.name }}</small>
        <h1>{{ message.practice.disciplineId | valueById(disciplines) }}</h1>
        <h2>{{ message.practice.name }}</h2>
        <div class="row mt-4">
          <div class="col-12">
            <div class="block">
              <button class="close">
                <span aria-hidden="true">&times;</span>
              </button>
              <p class="noselect" v-html="message.practice.text"></p>
              <div v-if="message.practice.textPdf != null">
                <pdf-viewer :src="message.practice.textPdf"></pdf-viewer>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-12">
            <div class="block noselect">
              <div class="title" style="margin: 0 0 15px;">Ответ</div>
              <answer-display
                :show-correct-answer="true"
                :disabled="true"
                :type="message.practice.answerType"
                :answers="message.practice.answers"
                :show-title="false"
                v-model="selectedAnswers"
              ></answer-display>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12">
              <div class="block">
                <div class="title" style="margin: 0 0 15px;">Вопрос</div>
                <p>{{ initialMessage.content }}</p>
              </div>
            </div>
            <div class="col-12" v-if="answerMessage != null">
              <div class="block">
                <div class="title" style="margin: 0 0 15px;">Ответ</div>
                <p>{{ answerMessage.content }}</p>
                <div>
                  <div class="image-preview" v-for="image in answerMessage.images">
                    <a :href="image.url" target="_blank">
                      <img :src="image.url" />
                    </a>
                  </div>
                  <div class="file-container" v-for="file in answerMessage.files">
                    <a :href="file.url">{{ file.name }}</a>
                  </div>
                </div>
                <template v-if="!Auth.isTeacher">
                  <div>
                    <strong>Оцените ответ преподавателя</strong>
                  </div>
                  <stars :value="initialMessage.rating" :editable="initialMessage.rating == null" @edit="onRatingSelected"></stars>
                  <message-input
                    v-if="showRatingExplanation"
                    :parent-message="initialMessage"
                    type="rating_explanation"
                    @message="saveRating(tempRating)"
                    :can-upload-images="false"
                  ></message-input>
                </template>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12" v-if="canAnswer && answerMessage == null">
              <div class="block">
                <div v-if="Auth.isTeacher" class="title" style="margin: 0 0 15px;">Ответ преподавателя</div>
                <div v-else class="title" style="margin: 0 0 15px;">Новое обращение</div>
                <message-input :parent-message="initialMessage" :practice="message.practice" :limit="inputLimit" type="check_request" @message="onMessage"></message-input>
              </div>
            </div>
          </div>
        </div>
      </div>
    </custom-vudal>
</template>
<script>
import Message from '_shared/resources/Message';
import valueById from '_shared/filters/valueById';
import _ from 'lodash/fp';
import Noty from 'noty';

export default {

  props: {
    message: Object,
    disciplines: Array,
    canAnswer: {
      type: Boolean,
      default: false,
    },
    inputLimit: {
      type: Number,
      default: null,
    },
  },

  filters: { valueById },

  components: {
    AnswerDisplay: () => import('_components/practiceAnswers/display'),
    CustomVudal: () => import('_components/customVudal'),
    MessageInput: () => import('_components/messageInput'),
    TeacherTimer: () => import('_components/teacherTimer'),
    Stars: () => import('_components/stars'),
    PdfViewer: () => import('_components/pdfViewer'),
  },

  data() {
    return {
      initialMessage: null,
      answerMessage: null,

      selectedAnswers: {},

      showRatingExplanation: false,
      tempRating: null,
    };
  },

  computed: {
    showTeacherTimer() {
      return this.Auth.isTeacher && this.$route.meta != null && this.$route.meta.showTeacherTimer;
    },
  },

  methods: {
    onMessage() {
      this.$modals.message.$hide();
      this.$emit('answer');
    },

    markAnswerRead() {
      if (this.answerMessage == null ||
        this.answerMessage.sender.id === this.Auth.user.id ||
        this.answerMessage.readAt != null
      ) {
        return;
      }

      const id = this.answerMessage.id;

      Message.markRead({ id }, {});
    },

    onHide() {
      this.$emit('hide');
    },

    onRatingSelected(rating) {
      this.tempRating = rating;
      if (rating < 5) {
        this.showRatingExplanation = true;
      }
      else {
        this.saveRating(rating);
      }
    },

    saveRating(rating) {
      const { id } = this.initialMessage;
      this.showRatingExplanation = false;
      Message.setRating({ id }, { rating }).then(() => {
        this.initialMessage.rating = rating;
        this.$emit('rating-updated');
        new Noty({
          text: 'Сохранено',
          type: 'success',
          timeout: 7000,
        }).show();
      });
    },
  },

  watch: {
    message(newVal) {
      this.initialMessage = newVal.history[0];
      this.answerMessage = newVal.history[1];

      const predicate = (carry, answer) =>
        _.assignAll([{
          [answer.id]: answer.userValue,
        }, carry]);

      this.selectedAnswers = this.message.practice.answers.reduce(predicate, {});

      this.markAnswerRead();
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

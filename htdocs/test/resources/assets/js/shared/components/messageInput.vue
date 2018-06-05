<template>
  <div>
    <textarea cols="30" rows="3" v-model="message.content"></textarea>
    <div class="textarea_panel">
      <cloudinary @upload="onFileUpload" :multiple="true">
        <a href="#">
          <img src="/fonts/icon-form-1.svg" alt="">
        </a>
      </cloudinary>

      <cloudinary @upload="onImageUpload" :multiple="true" :images-only="true" v-if="canUploadImages">
        <a href="#">
          <img src="/fonts/icon-form-2.svg" alt="">
        </a>
      </cloudinary>

      <div class="top15">
        <div class="image-preview" v-for="image in message.images">
          <a :href="image.url" target="_blank">
            <img :src="image.url" />
          </a>
        </div>
        <div class="file-container" v-for="file in message.files">
          <a :href="file.url">{{ file.name }}</a>
        </div>
      </div>
      <!-- <a href="#"> -->
        <!-- <img src="/fonts/icon-form-3.svg" alt=""> -->
        <!-- </a> -->
    </div>
    <error-messages :errors="errors.content"></error-messages>
    <div class="row mt-3">
      <div class="col" v-if="limit != null">
        Ответ должен содержать не менее <b>{{ limit }} знаков</b> (Сейчас <b>{{ message.content.length }}</b>/{{ limit }})
      </div>
      <div class="offset-8 col-4">
        <button class="btn btn-black mt-0 full-width" :disabled="limit != null && message.content.length < limit" @click="postMessage()">Отправить</button>
      </div>
    </div>
  </div>
</template>
<script>
import Noty from 'noty';
import Message from '_shared/resources/Message';
import TeacherMessage from '_shared/resources/teacher/Message';

export default {

  props: {
    type: {
      required: true,
      type: String,
    },
    theory: null,
    parentMessage: null,
    practice: null,
    limit: {
      type: Number,
      default: null,
    },
    canUploadImages: {
      type: Boolean,
      default: true,
    },
  },

  components: {
    ErrorMessages: () => import('_components/errorMessages'),
    Cloudinary: () => import('_components/cloudinary'),
  },

  data() {
    return {
      message: {
        type: this.type,
        practiceId: this.practice != null ? this.practice.id : null,
        theoryId: this.theory != null ? this.theory.id : null,
        images: [],
        files: [],
        content: '',
      },
      errors: {},
    };
  },

  methods: {
    postMessage() {
      let messagePromise;
      let text;

      if (this.parentMessage != null && this.type !== 'rating_explanation') {
        messagePromise = TeacherMessage.answer({ id: this.parentMessage.id }, this.message);
        text = 'Консультация отправлена';
      }
      else {
        if (this.type === 'rating_explanation') {
          this.message.ratingMessageId = this.parentMessage.id;
        }
        messagePromise = Message.save(this.message);
        switch (this.type) {
          case 'check_request':
            text = 'Запрос на консультацию успешно отправлен';
            break;
          case 'check_test':
            text = 'Запрос на проверку успешно отправлен';
            break;
          case 'mistake':
            text = 'Сообщение об ошибке отправлено';
            break;
          default:
            text = null;
        }
      }

      messagePromise.then(() => {
        this.errors = {};
        this.$emit('message');
        if (text != null) {
          new Noty({
            text,
            type: 'success',
            timeout: 7000,
          }).show();
        }
        this.message.content = '';
        this.message.images = [];
        this.message.files = [];
      }).catch((response) => {
        if (response.data.errors != null) {
          this.errors = response.data.errors;
        }
        else if (response.data.error != null) {
          new Noty({
            text: response.data.error,
            type: 'error',
            timeout: 7000,
          }).show();
        }
      });
    },

    onFileUpload(file) {
      this.message.files.push({
        name: file.originalFilename,
        url: file.secureUrl,
      });
    },

    onImageUpload(file) {
      this.message.images.push({
        name: file.originalFilename,
        url: file.secureUrl,
      });
    },
  },

  watch: {
    practice(newVal) {
      this.$set(this.message, 'practiceId', newVal != null ? newVal.id : null);
    },

    theory(newVal) {
      this.$set(this.message, 'theoryId', newVal != null ? newVal.id : null);
    },

    type(newVal) {
      this.message.type = newVal;
    },
  },

};
</script>

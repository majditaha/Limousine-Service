<template>
  <div class="inline">
    <slot>
      <label :for="_uid" class="btn btn-sky btn-icon full-width large btn-primary">
        <img src="/fonts/icon-plus.svg" alt="">
        {{ buttonText }}
      </label>
    </slot>
    <input :id="_uid" type="file" class="form-control" @change="upload($event.target.files)" style="display: none;" :accept="accept" />
    <div class="progress-holder" v-if="progress > 0 && progress !== 100">
      <div class="progress">
        <div class="progress-bar progress-bar-info progress-bar-striped" :style="{width: progress + '%'}" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>
  </div>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';
import _ from 'lodash/fp';

export default {

  props: {
    pdfOnly: {
      type: Boolean,
      default: false,
    },

    imagesOnly: {
      type: Boolean,
      default: false,
    },

    buttonText: {
      type: String,
      default: 'Выбрать файл',
    },
  },

  mounted() {
    if (this.$slots.default != null) {
      this.$slots.default[0].elm.addEventListener('click', (event) => {
        event.preventDefault();
        // eslint-disable-next-line no-underscore-dangle
        const input = document.getElementById(this._uid);
        input.click();
      });
    }
  },

  data() {
    return {
      progress: 0,
      cloudinary: {
        uploadPreset: ClientConfig.cloudinary.uploadPreset,
        postUrl: `https://api.cloudinary.com/v1_1/${ClientConfig.cloudinary.cloudName}/upload`,
      },
    };
  },

  computed: {
    accept() {
      if (this.pdfOnly) {
        return 'application/pdf';
      }

      if (this.imagesOnly) {
        return 'image/*';
      }

      return '*';
    },
  },

  methods: {
    upload(file) {
      if (!file) {
        return;
      }

      if (file[0].size > 10485760) {
        this.$emit('fail', 'Максимальный размер файла – 10 Мб');
        return;
      }

      const formData = new FormData();
      formData.append('file', file[0]);
      formData.append('upload_preset', this.cloudinary.uploadPreset);
      const config = {
        progress: (progressEvent) => {
          this.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        },
        headers: {
          'Content-Type': 'form-data/multipart',
        },
        before(request) {
          request.headers.delete('Authorization');
          request.headers.delete('x-csrf-token');
        },
      };
      this.$http.post(this.cloudinary.postUrl, formData, config).then((response) => {
        const data = _.pick([
          'publicId',
          'originalFilename',
          'format',
          'pages',
          'resourceType',
          'type',
          'version',
          'etag',
          'createdAt',
          'secureUrl',
        ])(response.data);
        data.cdn = 'cloudinary';
        data.urlPdf = response.data.secureUrl;
        this.$emit('upload', data);
      }).catch((e) => {
        this.$emit('fail', e);
      });
    },
  },
};
</script>

<style>
  div.inline {
    display: inline-block;
  }

  .progress-holder {
    bottom: 5px;
    left: 5px;
    right: 5px;
    display: block;
  }

  .progress {
    height: 5px;
    margin: 0;
  }
</style>

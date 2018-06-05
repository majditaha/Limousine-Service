<template>
  <div class="inline" :data-multiple="multiple" :data-images-only="imagesOnly">
    <slot>
      <button class="btn btn-sky btn-icon full-width large btn-primary" @click.prevent="onLoadClicked">
        <img src="/fonts/icon-plus.svg" alt="">
        {{ buttonText }}
      </button>
    </slot>
    <div class="progress-holder" v-if="progress > 0 && progress !== 100">
      <div class="progress">
        <div class="progress-bar progress-bar-info progress-bar-striped" :style="{width: progress + '%'}" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>
  </div>
  <!-- <input -->
  <!-- type="hidden" -->
  <!-- name="file" -->
  <!-- ref="uploader" -->
  <!-- class="uploader-button" -->
  <!-- clearable="true" /> -->
</template>
<script>
import uploadcare from 'uploadcare-widget';
import ClientConfig from '_shared/services/ClientConfig';

export default {

  props: {
    multiple: {
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

  beforeCreate() {
    window.UPLOADCARE_PUBLIC_KEY = ClientConfig.uploadcare.publicKey;
    window.UPLOADCARE_LOCALE = 'ru';
  },

  mounted() {
    if (this.$slots.default != null) {
      this.$slots.default[0].elm.addEventListener('click', (event) => {
        event.preventDefault();
        this.onLoadClicked();
      });
    }
    // eslint-disable-next-line new-cap
    const widget = uploadcare.Widget(this.$el);
    widget.onChange(this.onChange);
    this.widget = widget;
  },

  data() {
    return {
      widget: null,
      progress: 0,
    };
  },

  methods: {
    onLoadClicked() {
      this.widget.openDialog();
    },

    onChange(file) {
      if (!file) {
        return;
      }

      file
        .promise()
        .progress((info) => {
          this.progress = info.progress * 100;
          this.$emit('progress', info);
        })
        .fail(info => this.$emit('fail', info))
        .done((info) => {
          if (this.multiple) {
            file.files().forEach((fileData) => {
              fileData.done((fileInfo) => {
                this.$emit('upload', fileInfo);
              });
            });
          }
          else {
            this.$emit('upload', info);
          }
          this.widget.value(null);
        });
    },
  },

};
</script>

<style>
  div.inline {
    display: inline-block;
  }

  .uploadcare--widget {
    display: none !important;
  }

  .progress-holder {
    bottom: 5px;
    left: 5px;
    right: 5px;
  }

  .uploadcare-widget-status-started ~ .progress-holder {
    display: block;
  }

  .progress {
    height: 5px;
    margin: 0;
  }

  .uploadcare-widget-status {
    display: none !important;

    canvas {
      display: none;
    }
  }
</style>

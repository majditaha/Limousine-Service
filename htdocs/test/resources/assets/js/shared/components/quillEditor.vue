<template>
  <div>
    <div v-show="videoVisible" class="popover bottom" :style="popoverPosition">
      <div class="arrow"></div>
      <h3 class="popover-title">Загрузка видео</h3>
      <div class="popover-content">
        <uploadcare @upload="onVideoUploaded"></uploadcare>
      </div>
    </div>
    <quill-editor
      class="quill-editor"
      v-model="internalValue"
      :options="options"
      ref="editor"
    ></quill-editor>
  </div>
</template>
<script>
import Quill from 'quill';

window.Quill = Quill;

export default {

  props: {
    value: String,
  },

  components: {
    Uploadcare: () => import('_components/uploadcare'),
  },

  mounted() {
    Quill.register(CustomClass, true);
    const uploadcareButton = document.querySelector('.ql-uploadcare');
    uploadcareButton.addEventListener('click', this.videoClick);
  },

  data() {
    return {
      videoVisible: false,

      internalValue: this.value,

      popoverPosition: {
        top: 0,
        left: 0,
      },

      options: {
        placeholder: 'Вставляйте текст сюда',
        modules: {
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'],
              [{ 'custom-underline': 'double' }, { 'custom-underline': 'wavy' }],
              ['blockquote', 'code-block'],
              [{ header: 1 }, { header: 2 }],
              [{ list: 'ordered' }, { list: 'bullet' }],
              [{ script: 'sub' }, { script: 'super' }],
              [{ indent: '-1' }, { indent: '+1' }],
              [{ direction: 'rtl' }],
              [{ size: ['small', false, 'large', 'huge'] }],
              [{ header: [1, 2, 3, 4, 5, 6, false] }],
              [{ color: [] }, { background: [] }],
              [{ font: [] }],
              [{ align: [] }],
              ['clean'],
              ['link', 'image', 'uploadcare'],
            ],
            handlers: {
              'custom-underline'(value) {
                if (value) {
                  this.quill.format('underline', false);
                  this.quill.format('strike', false);
                }
                this.quill.format('custom-underline', value);
              },
            },
          },
        },
      },
    };
  },

  methods: {
    videoClick(event) {
      this.videoVisible = !this.videoVisible;
      const top = (event.target.offsetTop + event.target.clientHeight + 3);
      const left = (event.target.offsetLeft - (event.target.clientWidth / 2) - 60);
      this.$set(this.popoverPosition, 'top', `${top}px`);
      this.$set(this.popoverPosition, 'left', `${left}px`);
    },

    onVideoUploaded(file) {
      const quill = this.$refs.editor.quill;

      const range = quill.getSelection(true);

      // Taken from http://quilljs.com/guides/cloning-medium-with-parchment/
      quill.insertText(range.index, '\n', Quill.sources.USER);
      const url = `${file.originalUrl}/-/inline/yes/`;
      quill.insertEmbed(range.index + 1, 'video', url, Quill.sources.USER);
      quill.formatText(range.index + 1, 1, { height: '170', width: '400' });
      quill.setSelection(range.index + 2, Quill.sources.SILENT);

      this.videoVisible = false;
    },

  },

  watch: {
    internalValue(newVal) {
      this.$emit('input', newVal);
    },

    value(newVal) {
      if (newVal === this.internalValue) {
        return;
      }
      this.internalValue = newVal;
    },
  },

};

const Parchment = Quill.import('parchment');
const CustomClass = new Parchment.Attributor.Class('custom-underline', 'ql-custom-underline', {
  scope: Parchment.Scope.INLINE,
});

</script>

<style>
  .ql-uploadcare {
    background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAQCAYAAAAmlE46AAAA0ElEQVQ4T6XRO05CURSF4Y85SKUT8FFohdhAHINGWpXKxIBOQJwASkyoAFuMjsFo46PSwscEoNI5aE5yb8duuKc8Z//591qnhD88oo4Ozsw+59n7A2qlDHzHMQ6wH4DXGOEK6zkYzMbXOTjBDbZQDcaf8YQ9LOXg3Bl/s4JWsBwYv/GVisFC4Ywv2MUpTgLjBbq4xWZufEUDbbQCsIdLjFEpvOoP7rGG1cD4iQ9so1z4O6a4S6HT/oEx9ZBK3MFi4YxvOEITh4FxiAH62Jg74z8vbUAB4smGpAAAAABJRU5ErkJggg==") !important;
    background-repeat: no-repeat !important;
    width: 14px !important;
    height: 16px !important;
    display: inline-block;
    margin-top: 4px;
    margin-left: 5px;
  }

  .popover {
    display: block;
  }

  .ql-custom-underline[value="double"]:after {
    content: "D";
  }

  .ql-custom-underline[value="wavy"]:after {
    content: "W";
    text-decoration-color: inherit !important;
  }

  .ql-custom-underline-double, .ql-custom-underline[value="double"]:after {
    text-decoration-line: underline;
    text-decoration-style: double;
    -webkit-text-decoration-style: double;
  }

  .ql-custom-underline-wavy, .ql-custom-underline[value="wavy"]:after {
    text-decoration-line: underline;
    text-decoration-style: wavy;
    -webkit-text-decoration-style: wavy;
    text-decoration-color: red;
  }

  /* IE support */
  @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
    .ql-custom-underline-double, .ql-custom-underline[value="double"]:after {
      border-bottom: double 3px;
    }

    .ql-custom-underline-wavy, .ql-custom-underline[value="wavy"]:after {
      background: url(data:image/gif;base64,R0lGODlhBAADAIABAP8AAP///yH5BAEAAAEALAAAAAAEAAMAAAIFRB5mGQUAOw==) repeat-x 100% 100%;
      padding-bottom: 2px;
      text-decoration: none;
      white-space: nowrap;
    }
  }

  /* MS Edge support */
  @supports (-ms-ime-align:auto) {
    .ql-custom-underline-double, .ql-custom-underline[value="double"]:after {
      border-bottom: double 3px;
    }

    .ql-custom-underline-wavy, .ql-custom-underline[value="wavy"]:after {
      background: url(data:image/gif;base64,R0lGODlhBAADAIABAP8AAP///yH5BAEAAAEALAAAAAAEAAMAAAIFRB5mGQUAOw==) repeat-x 100% 100%;
      padding-bottom: 2px;
      text-decoration: none;
      white-space: nowrap;
    }
  }
</style>

<template>
  <div v-if="src.cdn === 'cloudinary'">
    <img v-for="page in src.pages" :src="imageSrc(page)" width="100%">
  </div>
  <div v-else>
    <pdf ref="pdf" :src="src.urlPdf" :page="page" @numPages="pdfPages = $event"></pdf>
    <div v-if="pdfPages > 1">
      <div v-for="page in pdfPagesRange">
        <pdf ref="pdf" :src="src.urlPdf" :page="page"></pdf>
      </div>
    </div>
  </div>

</template>
<script>
import _ from 'lodash/fp';
import pdf from 'vue-pdf';

export default {

  props: {
    src: {
      type: Object,
      default: null,
    },
  },

  components: {
    pdf,
  },

  data() {
    return {
      page: 1,
      pdfPages: 0,
    };
  },

  computed: {
    pdfPagesRange() {
      return _.range(2, this.pdfPages + 1);
    },
  },

  methods: {
    // HACK: for some reason pdf does not load new file automatically when src changes
    // Here we do it manually
    forceLoad() {
      const pdfComponent = _.isArray(this.$refs.pdf) ? this.$refs.pdf[0] : this.$refs.pdf;

      // Without setTimeout not reloading. Javascript at its finest
      setTimeout(() => {
        pdfComponent.pdf.loadDocument(this.src.urlPdf);
      }, 0);
    },

    imageSrc(page) {
      const width = (this.Auth.isTeacher) ? 1020 : 1050;
      const src = `${this.src.urlPdf.slice(0, -4)}.png`;
      return src.replace('/upload/', `/upload/c_scale,dn_300,fl_png8.progressive,w_${width},pg_${page}/`);
    },
  },

};
</script>

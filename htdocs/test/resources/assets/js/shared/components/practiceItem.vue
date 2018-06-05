<template>
  <div class="row">
    <div class="col-12 mb-4">
      <div class="block">
        <found-mistake-btn :practice="internalPractice"></found-mistake-btn>
        <div class="noselect" v-html="internalPractice.text">
        </div>
        <div v-if="internalPractice != null && internalPractice.textPdf != null">
          <pdf-viewer ref="pdfPractice" :src="internalPractice.textPdf"></pdf-viewer>
        </div>
        <div class="mt-auto">
          <button class="btn btn-green" @click.prevent="onConsultClick">
            <img src="/fonts/icon-consul.svg" alt="">
            Консультация
          </button>
          <router-link
            v-if="internalPractice.theoryId != null && showTheoryButton && theoryButtonAction === 'link'"
            :to="{ name: 'theory', params: { sectionId: internalPractice.sectionId, theoryId: internalPractice.theoryId } }"
            class="btn btn-sky"
            target="_blank"
          >
            <img src="/fonts/icon-info.svg" alt="">
            Перейти к теории
          </router-link>
          <button
            v-if="internalPractice.theoryId != null && showTheoryButton && theoryButtonAction === 'scrollTop'"
            class="btn btn-sky"
            v-scroll-to="'#header'"
          >
            <img src="/fonts/icon-info.svg" alt="">
            Перейти к теории
          </button>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="block noselect">
        <answer-display
          ref="answerDisplay"
          :type="internalPractice.answerType"
          :answers="internalPractice.answers"
          :highlight-results="highlight || disabled"
          :disabled="disabled || showSolution"
          v-model="internalAnswers"
          @input="onAnswersUpdated"
        ></answer-display>
        <div class="text-right mt-4" v-if="showCheckButton && !disabled">
          <button class="btn btn-black" @click.prevent="check()">Проверить</button>
        </div>
        <div class="text-right mt-4" v-if="isText">
          <button class="btn btn-black" @click.prevent="checkText()">Отправить эксперту</button>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="alert error" v-if="hasHint && showHint">
        <div class="title">Подсказка</div>
        <div v-html="internalPractice.hint"></div>
        <div v-if="internalPractice != null && internalPractice.hintPdf != null">
          <pdf-viewer ref="pdfHint" :src="internalPractice.hintPdf"></pdf-viewer>
        </div>
        <div class="text-right mt-4">
          <div class="btn btn-black" @click.prevent="check()">
            <img src="/fonts/icon-refresh.svg" alt="">
            Еще раз
          </div>
        </div>
      </div>
      <div class="alert" :class="[isAnswerCorrect() ? 'success' : 'error']" v-if="hasSolution && showSolution">
        <div class="title">Верный ответ</div>
        <div v-html="internalPractice.solution"></div>
        <div v-if="internalPractice != null && internalPractice.solutionPdf != null">
          <pdf-viewer ref="pdfSolution" :src="internalPractice.solutionPdf"></pdf-viewer>
        </div>
        <slot name="solutionButtons"></slot>
      </div>
      <slot name="solutionButtons" v-if="!hasSolution && showSolution">
      </slot>
    </div>

  </div>
</template>
<script>
import _ from 'lodash/fp';

export default {

  props: {
    practice: null,
    showCheckButton: {
      type: Boolean,
      default: true,
    },
    showTheoryButton: {
      type: Boolean,
      default: true,
    },
    theoryButtonAction: {
      type: String,
      default: 'link',
    },
    answers: null,
    disabled: Boolean,
  },

  components: {
    FoundMistakeBtn: () => import('_components/foundMistakeBtn'),
    AnswerDisplay: () => import('_components/practiceAnswers/display'),
    PdfViewer: () => import('_components/pdfViewer'),
  },

  data() {
    return {
      internalPractice: this.practice,

      internalAnswers: this.answers != null ? this.answers : {},
      firstCheckAnswers: null,

      wasChecked: false,
      highlight: false,

      showHint: false,
      showSolution: false,
    };
  },

  computed: {
    isText() {
      return this.practice.answerType === 'text';
    },

    hasHint() {
      return !_.isEmpty(this.practice.hint) || !_.isEmpty(this.practice.hintPdf);
    },

    hasSolution() {
      return !_.isEmpty(this.practice.solution) || !_.isEmpty(this.practice.solutionPdf);
    },
  },

  methods: {
    areAnswersFilled() {
      return _.flow(
        _.values,
        _.compact,
        _.size,
        _.thru(size => size > 0),
      )(this.internalAnswers);
    },

    isAnswerCorrect() {
      return this.$refs.answerDisplay.isAllCorrect();
    },

    checkText() {
      if (!this.isText) {
        return;
      }

      if (!this.areAnswersFilled()) {
        return;
      }

      if (!this.wasChecked) {
        this.wasChecked = true;
        this.$emit('update:firstCheckAnswers', this.internalAnswers);
      }

      this.$emit('check-test-click');
    },

    check() {
      if (!this.areAnswersFilled()) {
        return;
      }

      const allAnswersCorrect = this.$refs.answerDisplay.isAllCorrect();

      if (!this.wasChecked) {
        this.wasChecked = true;
        this.$emit('update:firstCheckAnswers', this.internalAnswers);
      }

      if (allAnswersCorrect) {
        this.showHint = false;
        this.showSolution = true;
      }

      if (this.showHint) {
        this.showSolution = true;
        this.highlight = true;
        this.showHint = false;
        return;
      }

      if (!allAnswersCorrect) {
        if (this.hasHint) {
          this.showHint = true;
        }
        // If user has no hint, then we directly show solution
        else {
          this.showSolution = true;
        }
      }

      if (this.showSolution) {
        this.highlight = true;
      }
    },

    reset() {
      this.wasChecked = false;
      this.highlight = false;
      this.showHint = false;
      this.showSolution = false;
      this.internalAnswers = {};
      this.$emit('update:firstCheckAnswers', null);
      this.$emit('update:answers', this.internalAnswers);
    },

    onConsultClick() {
      this.$emit('consult-click');
    },

    onAnswersUpdated() {
      if (this.highlight) {
        this.highlight = false;
      }
    },

    reloadPdfs() {
      if (this.$refs.pdfPractice && this.internalPractice.textPdf != null) {
        this.$refs.pdfPractice.forceLoad();
      }
      if (this.$refs.pdfHint && this.hasHint) {
        this.$refs.pdfHint.forceLoad();
      }
      if (this.$refs.pdfSolution && this.hasSolution) {
        this.$refs.pdfSolution.forceLoad();
      }
    },
  },

  watch: {
    practice(newVal) {
      this.internalPractice = newVal;
      this.reset();
      this.$nextTick(this.reloadPdfs);
    },

    answers(newVal) {
      this.internalAnswers = newVal != null ? newVal : {};
    },

    internalAnswers(newVal) {
      this.$emit('update:answers', newVal);
    },
  },

};
</script>

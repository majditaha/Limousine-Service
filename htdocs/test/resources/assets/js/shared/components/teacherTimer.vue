<template>
  <div class="timer">
    <div class="line">
      <div class="circle" :style="{left: percent + '%'}"></div>
    </div>
    <span>{{ time }}</span>
  </div>
</template>
<script>
import _ from 'lodash/fp';
import ClientConfig from '_shared/services/ClientConfig';
import moment from 'moment';
import { dbTime } from '_shared/lib/dateFormats';

export default {

  props: {
    takenAt: null,
  },

  created() {
    this.internalTakenAt = this.takenAt != null ? this.takenAt : moment.utc().format(dbTime);
    this.updateTimeLeft();
    this.interval = setInterval(this.updateTimeLeft, 60000);
  },

  beforeDestroy() {
    clearInterval(this.interval);
  },

  data() {
    return {
      teacherAnswerTime: ClientConfig.teacherAnswerTime,
      timeLeft: ClientConfig.teacherAnswerTime,

      internalTakenAt: null,

      interval: null,
    };
  },

  computed: {
    time() {
      if (!this.timeLeft || this.timeLeft < 1) {
        return '00:00';
      }

      const hours = _.floor(this.timeLeft / 60);
      const minutes = _.padCharsStart('0')(2)(this.timeLeft % 60);

      return `${hours}:${minutes}`;
    },

    percent() {
      if (this.timeLeft >= this.teacherAnswerTime) {
        return 0;
      }

      if (!this.timeLeft || this.timeLeft < 1) {
        return 100;
      }

      return _.floor(100 - ((this.timeLeft / this.teacherAnswerTime) * 100));
    },
  },

  methods: {
    updateTimeLeft() {
      const nowInUtc = moment.utc().format(dbTime);
      const timePassed = moment(nowInUtc).diff(moment(this.internalTakenAt), 'minutes');
      this.timeLeft = +this.teacherAnswerTime - timePassed;
    },
  },

  watch: {
    takenAt(newVal) {
      this.internalTakenAt = newVal != null ? newVal : moment.utc().format(dbTime);
      this.updateTimeLeft();
    },
  },

};
</script>

<style>
  .timer span {
    background: none;
  }
</style>

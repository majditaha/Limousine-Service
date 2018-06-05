<template>
  <div class="timer" v-if="Auth.isAuthenticated && visible">
    <div class="line">
      <div class="circle" :style="{left: percent + '%'}"></div>
    </div>
    <span>{{ time }}</span>
  </div>
</template>
<script>
import _ from 'lodash/fp';
import Account from '_shared/services/Account';

export default {

  props: {
    // Needed to log user's presence, but not display timer itself
    visible: {
      type: Boolean,
      default: true,
    },
  },

  created() {
    this.presenceMinutes = this.Auth.user.presenceMinutes;
    this.startPresenceTimer();
  },

  destroyed() {
    this.stopPresenceTimer();
  },

  data() {
    return {
      interval: null,

      presenceMinutes: 0,
    };
  },

  computed: {
    time() {
      const { desiredMinutesToSpend } = this.Auth.user;

      if (this.presenceMinutes > desiredMinutesToSpend) {
        return '00:00';
      }

      const totalMinutes = desiredMinutesToSpend - this.presenceMinutes;
      const hours = _.floor(totalMinutes / 60);
      const minutes = _.padCharsStart('0')(2)(totalMinutes % 60);

      return `${hours}:${minutes}`;
    },

    percent() {
      const { desiredMinutesToSpend } = this.Auth.user;

      if (this.presenceMinutes > desiredMinutesToSpend) {
        return 100;
      }

      if (!desiredMinutesToSpend) {
        return 0;
      }

      return _.floor((this.presenceMinutes / desiredMinutesToSpend) * 100);
    },
  },

  methods: {
    stopPresenceTimer() {
      clearInterval(this.interval);
    },

    startPresenceTimer() {
      this.logPresence();
      this.interval = setInterval(this.logPresence, 60000);
    },

    logPresence() {
      Account.logUserPresence().then((data) => {
        this.presenceMinutes = data.presenceMinutes;
      });
    },
  },

};
</script>

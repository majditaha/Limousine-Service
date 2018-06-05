import Vue from 'vue';
import '_shared/bootstrap';
import initRouter from '_shared/router';
import routes from '_site/routes';
import Root from '_site/pages/Root';
import RootBg from '_site/pages/RootBg';
import ClientConfig from '_shared/services/ClientConfig';
import { VudalPlugin } from 'vudal';
import 'popper.js';
import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo);

Vue.use(VudalPlugin, {
  confirm: {
    approveLabel: 'Подтвердить',
    cancelLabel: 'Отмена',
  },
});

const router = initRouter({
  routes,
});

ClientConfig.get().then(() => {
  new Vue({
    router,
    render(h) {
      if (this.$route.meta.blueBackground) {
        return h(RootBg);
      }
      return h(Root);
    },
  }).$mount('#app');
});

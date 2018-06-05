import Vue from 'vue';
import VueRouter from 'vue-router';
import '_shared/bootstrap';
import routes from '_noAuth/routes';
import Root from '_site/pages/Root';
import Main from '_noAuth/pages/Main';
import ClientConfig from '_shared/services/ClientConfig';
import 'popper.js';
import VueScrollTo from 'vue-scrollto';
import { VudalPlugin } from 'vudal';

Vue.use(VueScrollTo);
Vue.use(VudalPlugin);

const router = new VueRouter({
  routes,
  hashbang: false,
  history: true,
  mode: 'history',
});

ClientConfig.get().then(() => {
  new Vue({
    router,
    render(h) {
      if (this.$route.name === 'mainPage') {
        return h(Main);
      }
      return h(Root);
    },
  }).$mount('#app');
});

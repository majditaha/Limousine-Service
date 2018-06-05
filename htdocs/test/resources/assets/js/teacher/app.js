import Vue from 'vue';
import '_shared/bootstrap';
import initRouter from '_shared/router';
import routes from '_teacher/routes';
import Root from '_site/pages/Root';
import ClientConfig from '_shared/services/ClientConfig';
import { VudalPlugin } from 'vudal';
import 'popper.js';

Vue.use(VudalPlugin);

const router = initRouter({
  routes,
});

ClientConfig.get().then(() => {
  new Vue({
    router,
    render(h) {
      return h(Root);
    },
  }).$mount('#app');
});

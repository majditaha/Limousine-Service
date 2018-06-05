import Vue from 'vue';
import '_shared/bootstrap';
import initRouter from '_shared/router';
import routes from '_admin/routes';
import Root from '_admin/pages/Root';
import ClientConfig from '_shared/services/ClientConfig';
import { VudalPlugin } from 'vudal';
import VueQuillEditor from 'vue-quill-editor';

Vue.use(VueQuillEditor);

Vue.use(VudalPlugin, {
  confirm: {
    approveLabel: 'Подтвердить',
    cancelLabel: 'Отмена',
    approveBtnClass: 'btn btn-danger',
    cancelBtnClass: 'btn btn-default',
  },
});

const router = initRouter({
  routes,
  base: '/admin',
});

ClientConfig.get().then(() => {
  new Vue({
    router,
    render(h) {
      return h(Root);
    },
  }).$mount('#app');
});

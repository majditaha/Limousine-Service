import Vue from 'vue';
import VueResource from 'vue-resource';
import VueRouter from 'vue-router';
import VueResourceCaseConverter from 'vue-resource-case-converter';
import VueUnauthorizedInterceptor from '_shared/services/unauthorizedInterceptor';
import moment from 'moment';
import VueBreadcrumbs from '_shared/lib/breadcrumbs';

import '_shared/mixins/Auth';

Vue.use(VueBreadcrumbs);
Vue.use(VueResource);
Vue.use(VueRouter);
Vue.use(VueResourceCaseConverter);
Vue.use(VueUnauthorizedInterceptor);

moment.locale('ru');

function importAll(r) {
  return r.keys().map(r);
}

importAll(require.context('./../../img', true, /\.(png|jpe?g|svg)$/));

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = require('jquery');

  require('bootstrap-sass');
}
catch (e) {
  console.log(e);
}

/**
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  Vue.http.headers.common['X-CSRF-TOKEN'] = token.content;
}
else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * User api token as a authentication header
 */

const apiToken = document.head.querySelector('meta[name="api-token"]');

if (apiToken) {
  Vue.http.headers.common.Authorization = `Bearer ${apiToken.content}`;
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// TODO: if needed, uncomment and move in separate file

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//  broadcaster: 'pusher',
//  key: 'your-pusher-key'
// });

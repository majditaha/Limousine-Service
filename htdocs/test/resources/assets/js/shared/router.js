import VueRouter from 'vue-router';
import Auth from '_shared/services/Auth';
import _ from 'lodash/fp';

export default function (params) {
  const options = _.extend({
    hashbang: false,
    history: true,
    mode: 'history',
  }, params);

  const router = new VueRouter(options);

  router.beforeEach((to, from, next) => {
    let title = null;
    if (to.meta != null) {
      title = to.meta.pageTitle;
      if (title == null) {
        title = to.meta.title;
      }
    }
    document.title = title != null ? `Expass - ${title}` : 'Expass';
    // if user not authenticated then go to login page
    Auth.getAuthenticatedUser().then((user) => {
      if (!user) {
        location.href = '/';
      }
      else {
        const ignoreCurrentRoute = to.name === 'confirmation' ||
          to.name === 'agreement';

        if (!ignoreCurrentRoute && !Auth.user.isValid && Auth.isTeacher && to.name !== 'teacherForm') {
          router.go({ name: 'teacherForm' });
          return;
        }

        if (!ignoreCurrentRoute && !Auth.user.isValid && Auth.isUser && to.name !== 'userForm') {
          router.go({ name: 'userForm' });
          return;
        }

        next();
      }
    }).catch((error) => {
      console.error(error);
    });
  });


  return router;
}

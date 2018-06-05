import Vue from 'vue';
import _ from 'lodash/fp';
import { Promise } from 'es6-promise';

export default {

  // authentication errors
  errors: {
    login: {},
    reg: {},
    reset: {},
  },

  // current user data
  user: {},

  getAuthenticatedUser() {
    return new Promise((resolve) => {
      Vue.http.get('/auth/check').then((response) => {
        this.user = response.body;
        resolve(this.user);
      }, () => {
        resolve(null);
      });
    });
  },

  get isAuthenticated() {
    return !_.isEmpty(this.user);
  },

  get isTeacher() {
    return this.user.role === 'teacher';
  },

  get isUser() {
    return this.user.role === 'user';
  },

  login(email, password, redirect = false) {
    this.errors.login = {};

    Vue.http.post('/auth/login', { email, password }).then((response) => {
      if (response.data.success) {
        if (redirect) {
          location.href = redirect;
        }
      }
    }, (response) => {
      this.errors.login = response.data.errors;
    });
  },

  passwordReset(email, redirect = false) {
    this.errors.reset = {};
    return Vue.http.post('/auth/password/email', { email }).then((response) => {
      if (redirect) {
        location.href = redirect;
      }
      return response.data;
    }).catch((response) => {
      this.errors.reset = response.data.errors;
      throw response;
    });
  },

  changePassword(email, password, passwordConfirmation, token) {
    this.errors.reset = {};
    return Vue.http.post('/auth/password/reset', { email, password, passwordConfirmation, token }).then(() => {
      location.href = '/';
    }).catch((response) => {
      this.errors.reset = response.data.errors;
      throw response;
    });
  },

  register(data, redirect = false) {
    return Vue.http.post('/auth/register', data).then((response) => {
      if (response.data.success) {
        if (redirect) {
          location.href = redirect;
        }
      }
    }).catch((response) => {
      this.errors.reg = response.data.errors;
    });
  },

  logout(redirect = false) {
    Vue.http.get('/auth/logout').then(() => {
      this.user = {};
      if (redirect) {
        location.href = redirect;
      }
    });
  },
};

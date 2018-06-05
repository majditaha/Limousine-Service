import Vue from 'vue';

export default {
  get(alias) {
    return Vue.http.get(`/api/pages/${alias}`).then(response => response.data);
  },
};

import Vue from 'vue';

const clientConfig = {};

function get() {
  return Vue.http.get('/api/client_config')
    .then(response => response.data)
    .then(config => Object.assign(clientConfig, config));
}

clientConfig.get = get;

export default clientConfig;

import Vue from 'vue';

const resource = Vue.resource('/api/disciplines{/id}', {}, {});
resource.name = 'discipline';
export default resource;

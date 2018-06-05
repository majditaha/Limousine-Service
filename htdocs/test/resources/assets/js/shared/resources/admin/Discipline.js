import Vue from 'vue';

const resource = Vue.resource('/api/admin/disciplines{/id}', {}, {});
resource.name = 'discipline';
export default resource;

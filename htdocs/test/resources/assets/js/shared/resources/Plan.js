import Vue from 'vue';

const resource = Vue.resource('/api/plans{/id}', {}, {});
resource.name = 'plan';
export default resource;

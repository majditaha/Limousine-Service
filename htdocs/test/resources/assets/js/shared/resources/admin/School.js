import Vue from 'vue';

const resource = Vue.resource('/api/admin/schools{/id}', {}, {});
resource.name = 'school';
export default resource;

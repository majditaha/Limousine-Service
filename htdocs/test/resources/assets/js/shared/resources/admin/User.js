import Vue from 'vue';

const resource = Vue.resource('/api/admin/users{/id}', {}, {});
resource.name = 'user';
export default resource;

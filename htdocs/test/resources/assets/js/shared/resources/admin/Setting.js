import Vue from 'vue';

const resource = Vue.resource('/api/admin/settings{/id}', {}, {});
resource.name = 'setting';
export default resource;

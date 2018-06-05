import Vue from 'vue';

const resource = Vue.resource('/api/admin/practices{/id}', {}, {});
resource.name = 'practice';
export default resource;

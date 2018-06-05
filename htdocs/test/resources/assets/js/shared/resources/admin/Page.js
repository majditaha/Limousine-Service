import Vue from 'vue';

const resource = Vue.resource('/api/admin/pages{/id}', {}, {});
resource.name = 'page';
export default resource;

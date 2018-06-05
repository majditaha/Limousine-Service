import Vue from 'vue';

const resource = Vue.resource('/api/admin/cities{/id}', {}, {});
resource.name = 'city';
export default resource;

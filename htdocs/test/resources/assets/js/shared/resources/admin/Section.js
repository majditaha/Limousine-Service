import Vue from 'vue';

const resource = Vue.resource('/api/admin/sections{/id}', {}, {});
resource.name = 'section';
export default resource;

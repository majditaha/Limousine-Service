import Vue from 'vue';

const resource = Vue.resource('/api/admin/subtypes{/id}', {}, {});
resource.name = 'subtype';
export default resource;

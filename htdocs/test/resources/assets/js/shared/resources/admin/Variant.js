import Vue from 'vue';

const resource = Vue.resource('/api/admin/variants{/id}', {}, {});
resource.name = 'variant';
export default resource;

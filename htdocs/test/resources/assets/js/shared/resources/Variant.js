import Vue from 'vue';

const resource = Vue.resource('/api/variants{/id}', {}, {});
resource.name = 'variant';
export default resource;

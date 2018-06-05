import Vue from 'vue';

const resource = Vue.resource('/api/cities{/id}', {}, {});
resource.name = 'city';
export default resource;

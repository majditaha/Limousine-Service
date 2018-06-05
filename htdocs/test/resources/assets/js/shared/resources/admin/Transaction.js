import Vue from 'vue';

const resource = Vue.resource('/api/admin/transactions{/id}', {}, {});
resource.name = 'transaction';
export default resource;

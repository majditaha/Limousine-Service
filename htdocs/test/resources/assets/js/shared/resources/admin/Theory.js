import Vue from 'vue';

const resource = Vue.resource('/api/admin/theories{/id}', {}, {});
resource.name = 'theory';
export default resource;

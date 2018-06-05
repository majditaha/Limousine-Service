import Vue from 'vue';

const resource = Vue.resource('/api/admin/messages{/id}', {}, {
  getHistory: { method: 'GET', url: '/api/admin/messages{/id}/history' },
});
resource.name = 'message';
export default resource;

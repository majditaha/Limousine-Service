import Vue from 'vue';

const resource = Vue.resource('/api/teacher/messages{/id}', {}, {
  answer: { method: 'POST', url: '/api/teacher/messages{/id}/answer' },
  markRead: { method: 'PUT', url: '/api/teacher/messages{/id}/mark_read' },
  markTaken: { method: 'PUT', url: '/api/teacher/messages{/id}/mark_taken' },
});
resource.name = 'message';
export default resource;

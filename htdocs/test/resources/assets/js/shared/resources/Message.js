import Vue from 'vue';

const resource = Vue.resource('/api/messages{/id}', {}, {
  answer: { method: 'POST', url: '/api/messages{/id}/answer' },
  markRead: { method: 'PUT', url: '/api/messages{/id}/mark_read' },
  getReviews: { method: 'GET', url: '/api/messages/reviews' },
  setRating: { method: 'PUT', url: '/api/messages{/id}/rating' },
});
resource.name = 'message';
export default resource;

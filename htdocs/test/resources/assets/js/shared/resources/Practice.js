import Vue from 'vue';

const resource = Vue.resource('/api/practices{/id}', {}, {
  getTrainings: { method: 'GET', url: '/api/practices/trainings/{section_id}' },
  getSmart: { method: 'GET', url: '/api/practices/smart' },
});
resource.name = 'practice';
export default resource;

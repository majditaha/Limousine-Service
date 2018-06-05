import Vue from 'vue';

const resource = Vue.resource('/api/sections{/id}', {}, {
  getLatestInTraining: { method: 'GET', url: '/api/sections/latest_in_training/{discipline_id}' },
  getFinished: { method: 'GET', url: '/api/sections/finished/{discipline_id}' },
  dropProgress: { method: 'GET', url: '/api/sections{/id}/drop_progress' },
});
resource.name = 'section';
export default resource;

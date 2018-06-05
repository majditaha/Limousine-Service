import Vue from 'vue';

export default {
  update(data) {
    return Vue.http.put('/api/account', data).then(response => response.data);
  },

  setDesiredHours(data) {
    return Vue.http.put('/api/account/desired_hours', data).then(response => response.data);
  },

  setTheoryFinished(theoryId, isTraining) {
    return Vue.http.put(`/api/account/finish_theory/${theoryId}`, { isTraining }).then(response => response.data);
  },

  setSectionFinished(sectionId) {
    return Vue.http.put(`/api/account/finish_section/${sectionId}`).then(response => response.data);
  },

  setPracticeFinished(practiceId, selectedAnswers, isTraining = false) {
    const answers = selectedAnswers;
    return Vue.http.put(`/api/account/finish_practice/${practiceId}`, { answers, isTraining }).then(response => response.data);
  },

  logUserPresence() {
    return Vue.http.post('/api/account/presence').then(response => response.data);
  },

  getAvailableResources() {
    return Vue.http.get('/api/account/available_resources').then(response => response.data);
  },

  addMoney(amount) {
    return Vue.http.post('/api/account/add_money', { amount }).then(response => response.data);
  },

  purchasePlan(plan) {
    const { disciplineIds, selectedCount } = plan;
    return Vue.http.post(`/api/account/purchase_plan/${plan.id}`, {
      disciplineIds,
      selectedCount,
    }).then(response => response.data);
  },
};

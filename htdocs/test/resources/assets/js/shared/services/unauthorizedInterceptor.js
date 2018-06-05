export default {
  install(Vue) {
    Vue.http.interceptors.push((request, next) => {
      next((response) => {
        if (response.status >= 400 && response.body.authError) {
          location.href = '/';
        }
        return response;
      });
    });
  },
};

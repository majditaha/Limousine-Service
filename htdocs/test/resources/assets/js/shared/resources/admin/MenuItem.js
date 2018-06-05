import Vue from 'vue';

const resource = Vue.resource('/api/admin/menu_items{/id}', {}, {});
resource.name = 'menuItem';
export default resource;

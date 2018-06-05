<template>
  <ul class="footer-menu">
    <li v-for="item in menu">
      <router-link :to="'/' + item.alias">{{ item.name }}</router-link>
    </li>
  </ul>
</template>
<script>
import ClientConfig from '_shared/services/ClientConfig';

export default {

  data() {
    return {
      menu: ClientConfig.menu,
    };
  },
};
</script>

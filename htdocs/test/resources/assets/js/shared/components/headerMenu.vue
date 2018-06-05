<template>
  <ul>
    <li v-for="item in menu" :class="{ active: item.alias === $route.params.alias }">
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

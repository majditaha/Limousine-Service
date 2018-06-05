<template>
  <div>
    <a v-for="item in items" :href="'/' + item.alias">{{ item.name }}</a>
    <a href="/auth/logout">Выход</a>
  </div>
</template>
<script>
export default {

  props: ['items'],

};
</script>

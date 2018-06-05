<template>
  <div>
    <aside class="sidebar">
      <ul>
        <li>
          <router-link :to="{ name: 'rating' }">
            <img src="/fonts/icon-rating.svg" alt="">
            Мой рейтинг
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'finishedRequests' }">
            <img src="/fonts/icon-profile-check.svg" alt="">
            Выполненные заказы
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'mainPage' }">
            <img src="/fonts/icon-profile-check.svg" alt="">
            Запросы на проверку заказов
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'statistics' }">
            <img src="/fonts/icon-profile-check.svg" alt="">
            Текущий баланс
          </router-link>
        </li>
      </ul>
    </aside>
    <div class="rating">
      Баланс
      <span class="text-green">{{ availableMoney / 100 }} руб</span>
    </div>
  </div>
</template>
<script>
import Account from '_shared/services/Account';

export default {

  created() {
    this.loadAvailableMoney();
  },

  data() {
    return {
      availableMoney: 0,
    };
  },

  methods: {
    loadAvailableMoney() {
      Account.getAvailableResources().then((data) => {
        this.availableMoney = data.money;
      });
    },
  },

};
</script>

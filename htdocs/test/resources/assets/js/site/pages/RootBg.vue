<template>
  <div>
    <!-- Фон -->
    <div class="section-demo">
      <blue-bg></blue-bg>
    </div>
    <!-- Шапка -->
    <header id="header">
      <nav class="row align-items-center">
        <div class="col-sm-8">
          <!-- Логотип -->
          <a href="/" class="logo"><img src="/images/logo.png" alt=""></a>
          <!-- Меню -->
          <header-menu></header-menu>
        </div>
        <div class="col-sm-4 text-right">
          <!-- Панель пользователя -->
          <div class="user_panel" v-if="Auth.isAuthenticated">
            <div class="rounded-image">
              <router-link :to="{ name: 'profile' }">
                <img v-if="Auth.user.photo == null" src="/images/no_avatar.png" alt="">
                <img v-else :src="Auth.user.photo" alt="">
              </router-link>
            </div>
            <span>{{ Auth.user.name }}</span>
            <a href="/auth/logout">
              <span class="icon text-blue">exit_to_app</span>
            </a>
          </div>
        </div>
      </nav>
    </header>
    <!-- Контент -->
    <main id="main">
      <div class="container">
        <router-view></router-view>
      </div>
    </main>
    <!-- Футер -->
    <footer id="footer">
      <div class="container">
        <div class="row">
          <div class="col-6">
            <div class="copy">
              © Copyright by Expass {{ (new Date()).getFullYear() }}
            </div>
          </div>
          <div class="col-6 text-right">
            <footer-menu></footer-menu>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
<script>
import Breadcrumbs from '_components/breadcrumbs';
import HeaderMenu from '_components/headerMenu';
import FooterMenu from '_components/footerMenu';
import PageHeading from '_components/pageHeading';
import BlueBg from '_components/blueBg';
// import '_shared/lib/bg';

export default {

  components: {
    Breadcrumbs,
    HeaderMenu,
    FooterMenu,
    PageHeading,
    BlueBg,
  },

};
</script>

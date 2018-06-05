<template>
  <div>
  <!-- Шапка -->
  <header id="header" class="white">
    <timer v-if="showUserTimer" :visible="false"></timer>
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
        <div class="user_panel" v-else>
          <a href="#" @click.prevent="$modals.auth.$show()" class="btn btn-sky">Авторизация</a>
        </div>
      </div>
    </nav>
  </header>
  <!-- Контент -->
  <main id="main">
    <div class="container" v-if="Auth.isTeacher && Auth.user.isValid && !Auth.user.active">
      <breadcrumbs></breadcrumbs>
      <div class="block">
        Ваш аккаунт ещё не активирован. По всем вопросам обращайтесь по адресу {{ contactEmail }}
      </div>
    </div>
    <template v-else>
      <div class="container">
        <breadcrumbs></breadcrumbs>
        <page-heading></page-heading>
        <template v-if="Auth.isTeacher && showTeacherMenu">
          <div class="row profile">
            <div class="col-md-9">
              <router-view></router-view>
            </div>
            <div class="col-md-3">
              <teacher-menu></teacher-menu>
            </div>
          </div>
        </template>
        <div v-else>
          <router-view></router-view>
        </div>
        <auth-modal v-if="!Auth.isAuthenticated"></auth-modal>
      </div>
    </template>
  </main>
  <!-- Футер -->
  <footer id="footer" class="blue">
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
import Timer from '_components/timer';
import TeacherMenu from '_components/teacherMenu';
import ClientConfig from '_shared/services/ClientConfig';

export default {

  components: {
    Breadcrumbs,
    HeaderMenu,
    FooterMenu,
    PageHeading,
    Timer,
    TeacherMenu,
    AuthModal: () => import('_components/authModal'),
  },

  computed: {
    showUserTimer() {
      return this.Auth.isUser && this.$route.meta != null && this.$route.meta.showTimer;
    },

    showTeacherMenu() {
      return this.Auth.isTeacher && this.$route.meta != null && this.$route.meta.showTeacherMenu;
    },
  },

  data() {
    return {
      contactEmail: ClientConfig.contactEmail,
    };
  },

};
</script>

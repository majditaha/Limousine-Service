<template>
  <div>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">EXPASS</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/auth/logout">Выход</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li v-for="route in sidebarRoutes()">
              <router-link :to="{ name: route.name }" active-class="active">{{ route.meta.title }}</router-link>
            </li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">{{ $route.meta.title }}</h1>

          <div class="row placeholders">
            <router-view></router-view>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
import clientConfig from '_shared/services/ClientConfig';
import MainMenu from '_components/mainMenu';
import 'bootstrap';

export default {
  components: { MainMenu },

  data() {
    return {
      menu: clientConfig.menu,
    };
  },

  methods: {
    sidebarRoutes() {
      const routeNames = [];
      this.$router.options.routes.forEach((route) => {
        if (route.meta != null && route.meta.sidebar != null && route.meta.sidebar) {
          routeNames.push(route);
        }
      });
      return routeNames;
    },
  },

};
</script>

<style>
  .sidebar {
      position: fixed;
      top: 51px;
      bottom: 0;
      left: 0;
      z-index: 1000;
      display: block;
      padding: 20px;
      overflow-x: hidden;
      overflow-y: auto;
      background-color: #f5f5f5;
      border-right: 1px solid #eee;
  }

  .nav-sidebar {
      margin-right: -21px;
      margin-bottom: 20px;
      margin-left: -20px;
  }

  .nav-sidebar > li > a.active, .nav-sidebar > .active > a:hover, .nav-sidebar > .active > a:focus {
      color: #fff;
      background-color: #428bca;
  }

  .main {
      padding-top: 20px;
      padding-right: 40px;
      padding-left: 40px;
  }
</style>

<template>
  <div>
    <!-- Шапка -->
    <header id="header-main">
      <div class="container">
        <nav class="row align-items-md-center">
          <div class="col-sm-8 col-lg-10">
            <!-- Логотип -->
            <a href="/" class="logo"><img src="images/logo_white.png" alt=""></a>
            <!-- Меню -->
            <header-menu></header-menu>
          </div>
          <div class="col-sm-4 col-lg-2 text-right">
            <!-- Панель пользователя -->
            <div class="user_panel">
              <a href="#" @click.prevent="$modals.auth.$show()" class="btn btn-sky">Авторизация</a>
            </div>
          </div>
        </nav>
        <h1>Системная подготовка к ЕГЭ</h1>
        <div class="row">
          <div class="col col-md-12 col-lg-4">
            <form action="">
              <div class="title">Регистрация</div>
              <register-form></register-form>
              <span>
                Используй 7-дневный пробный период чтобы убедиться в этом лично
              </span>
            </form>
          </div>
          <div class="col d-none d-md-inline-block">
            <div class="macbook-header">
              <img class="macbook" src="/images/macbook-header.png" alt="">
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- Контент -->
    <section id="plus">
      <div class="container">
        <div class="title">Наши <span>преимущества</span></div>
        <div class="sub-title"><span>Expass</span> поможет раскрыть Ваш потенциал</div>
        <div class="list">
          <div class="row">
            <div class="col-md-6 col-lg-3">
              <div class="item">
                <h3>Интерактивный метод <span>освоения материалов</span></h3>
                <img src="/images/icon-main-1.png" alt="">
                <p>
                  Математический алгоритм позволяет определять сильные и слабые стороны в знаниях пользователя и самостоятельно подбирает
                </p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="item">
                <h3>Индивидуальный подход <span>освоения материалов</span></h3>
                <img src="/images/icon-main-2.png" alt="">
                <p>
                  Математический алгоритм позволяет определять сильные и слабые стороны в знаниях пользователя и самостоятельно подбирает
                </p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="item">
                <h3>Подробный разбор <span>каждого задания</span></h3>
                <img src="/images/icon-main-3.png" alt="">
                <p>
                  Математический алгоритм позволяет определять сильные и слабые стороны в знаниях пользователя и самостоятельно подбирает
                </p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="item">
                <h3>Поддержка online <span>24/7</span></h3>
                <img src="/images/icon-main-4.png" alt="">
                <p>
                  Огромный штат профессиональных репетиторов готов 24/7 ответить на любой Ваш вопрос в теоретическом материале или проверить пройденный Вами тест ЕГЭ в соответствии со всеми требованиями Министерства
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="how">
      <div class="container">
        <div class="title">Как <span>это работает</span></div>
        <div class="sub-title"><span>Expass</span> расскажет!</div>
        <div class="dots-container"></div>
        <div class="nav-container"></div>
        <div class="slider owl-carousel">
          <div data-dot="Шаг 1">
            <div class="image-block">
              <img src="/images/header-bg.jpg" alt="">
            </div>
          </div>
          <div data-dot="Шаг 2">
            <div class="image-block">
              <img src="/images/header-bg.jpg" alt="">
            </div>
          </div>
          <div data-dot="Шаг 3">
            <div class="image-block">
              <img src="/images/header-bg.jpg" alt="">
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="calc">
      <div class="container">
        <div class="row align-items-center justify-content-between">
          <div class="col-md-6 col-lg-6">
            <div class="title text-left">
              А вы знаете сколько вы платите в год своему репетитору?
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <form>
              <span>Посчитайте сами</span>
              <input type="text" placeholder="Укажите цену в руб." v-model="calc.price">
              <input type="text" placeholder="Количество дней в неделю" v-model="calc.days">
              <p>
                Итого: <span>{{ calcTotal }}</span> руб.
              </p>
            </form>
          </div>
        </div>
      </div>
    </section>
    <section id="price">
      <div class="container">
        <div class="title">Наши <span>тарифы</span></div>
        <div class="sub-title"><span>Expass</span> доступен всем!</div>
        <div class="overflow">
          <div class="row row-eq-height list">
            <div class="col-3">
              <ul class="text-left">
                <li></li>
                <li>Адаптивность</li>
                <li>Необходимость покупки книг</li>
                <li>Банк вопросов по ЕГЭ</li>
                <li>Актульные тесты по ЕГЭ</li>
                <li>Проверка заданий</li>
                <li>Возможность задавать вопросы</li>
                <li>Работа 24/7</li>
                <li><b>Стоимость на 9 месяцев</b></li>
              </ul>
            </div>
            <div class="col-3">
              <ul class="blue">
                <li><img src="/images/logo_white.png" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li>1 999 ₽</li>
              </ul>
            </div>
            <div class="col-2">
              <ul>
                <li>Репетитор</li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li>72 000 ₽</li>
              </ul>
            </div>
            <div class="col-2">
              <ul>
                <li>Курсы</li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li>42 000 ₽</li>
              </ul>
            </div>
            <div class="col-2">
              <ul>
                <li>Видеоуроки</li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-minus.svg" alt=""></li>
                <li><img src="/fonts/icon-check-green.svg" alt=""></li>
                <li>3 600 ₽</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="text-center">
          <a href="#" v-scroll-to="'#pre-footer'" class="btn btn-green large">Начать учиться</a>
        </div>
      </div>
    </section>
    <section id="review">
      <div class="container">
        <div class="title">Наши <span>довольные пользователи</span></div>
        <div class="sub-title"><span>Expass</span> радует :)</div>
        <div class="slider-review owl-carousel">
          <div v-for="message in reviews">
            <div class="block">
              <div class="title">{{ message.sender.name }} <span>| {{ message.createdAt | format('ru') }}</span></div>
              <p>
                {{ message.content }}
              </p>
              <stars :value="message.rating" class="stars"></stars>
            </div>
          </div>
        </div>
        <div class="text-center">
          <p>
            <span>95%</span> сутдентов довольны Expass
          </p>
          <img src="/images/logo_white.png" alt="">
        </div>
      </div>
    </section>
    <section id="pre-footer">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-Lg-4">
            <form action="">
              <div class="title">Регистрация</div>
              <register-form></register-form>
            </form>
          </div>
        </div>
        <!-- <div class="block">
          <div class="title">О сайте (SEO)</div>
          <p>
            Дальше я покажу, что надежды на построение такой теории сильно возросли, ибо мы сейчас значительно больше узнали о Вселенной. Но не нужно быть чересчур уверенным – мы уже не раз сталкивались с миражами! Например, в начале века считалось, что всё можно объяснить с помощью свойств, характеризующих непрерывное вещество, скажем, таких, как упругость и теплопроводность. Открытие строения атома и принципа неопределённости навсегда покончило с подобным подходом. Затем в 1928 г. физик, лауреат Нобелевской премии Макс Борн, выступая перед гостями.
          </p>
        </div> -->
      </div>
    </section>
    <section id="partners">
      <div class="container">
        <div class="title">Наши <span>партнеры</span></div>
        <div class="row">
          <div class="col-sm item text-center">
            <img src="http://cdn.instantlogosearch.com/svg/instantlogosearch/Netflix.svg" height="40" width="147" alt="">
            <br>
            <h3>Netflix</h3>
          </div>
          <div class="col-sm item text-center">
            <img src="http://cdn.instantlogosearch.com/svg/instantlogosearch/Netflix.svg" height="40" width="147" alt="">
            <br>
            <h3>Netflix</h3>
          </div>
          <div class="col-sm item text-center">
            <img src="http://cdn.instantlogosearch.com/svg/instantlogosearch/Netflix.svg" height="40" width="147" alt="">
            <br>
            <h3>Netflix</h3>
          </div>
          <div class="col-sm item text-center">
            <img src="http://cdn.instantlogosearch.com/svg/instantlogosearch/Netflix.svg" height="40" width="147" alt="">
            <br>
            <h3>Netflix</h3>
          </div>
          <div class="col-sm item text-center">
            <img src="http://cdn.instantlogosearch.com/svg/instantlogosearch/Netflix.svg" height="40" width="147" alt="">
            <br>
            <h3>Netflix</h3>
          </div>
        </div>
      </div>
    </section>
    <!-- Футер -->
    <footer id="footer">
      <div class="container">
        <div class="row">
          <div class="col-sm-6 col-12"">
            <div class="copy">
              © Copyright by Expass {{ (new Date()).getFullYear() }}
            </div>
          </div>
          <div class="col-12 col-sm-6 text-sm-right">
            <footer-menu></footer-menu>
          </div>
        </div>
      </div>
    </footer>

    <auth-modal></auth-modal>
  </div>
</template>
<script>
import Message from '_shared/resources/Message';
import HeaderMenu from '_components/headerMenu';
import FooterMenu from '_components/footerMenu';
import RegisterForm from '_components/registerForm';
import $ from 'jquery';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';
import format from '_shared/filters/format';

export default {

  components: {
    HeaderMenu,
    FooterMenu,
    RegisterForm,
    AuthModal: () => import('_components/authModal'),
    Stars: () => import('_components/stars'),
  },

  filters: {
    format,
  },

  created() {
    Message.getReviews().then((response) => {
      this.reviews = response.data;
      this.$nextTick(this.loadReviewsCarousel);
    });
  },

  mounted() {
    $('body').addClass('main_page');
    // TODO: move to separate component
    $('.slider').owlCarousel({
      items: 1,
      dotsContainer: '.dots-container',
      dotsData: true,
      navContainer: '.nav-container',
      navText: ["<img class='reverse' src='/images/icon-arrow-small.png' alt=''>", "<img src='/images/icon-arrow-small.png' alt=''>"],
    });
  },

  destroyed() {
    $('body').removeClass('main_page');
  },

  data() {
    return {
      calc: {
        price: '',
        days: '',
      },

      reviews: [],
    };
  },

  computed: {
    calcTotal() {
      if (!isNaN(+this.calc.price) && !isNaN(+this.calc.days)) {
        return this.calc.price * this.calc.days * 52;
      }
      return 0;
    },
  },

  methods: {
    loadReviewsCarousel() {
      $('.slider-review').owlCarousel({
        dots: false,
        margin: 30,
        nav: true,
        navText: ["<img src='/images/icon-arrow-slider.png' alt=''>", "<img class='reverse' src='/images/icon-arrow-slider.png' alt=''>"],
        responsive: {
          992: {
            items: 3,
          },
          768: {
            items: 2,
            dots: false,
          },
          0: {
            items: 1,
            dots: true,
          },
        },
      });
    },
  },

};
</script>

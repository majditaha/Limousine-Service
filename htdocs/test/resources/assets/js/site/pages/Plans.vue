<template>
  <div>
    <loading-indicator :loading="loading">
      <h1>Тарифы</h1>
      <h3>При  покупке 2-х предметов сразу – скидка <span class="text-green font-weight-bold">25%</span></h3>
      <h2 class="green">Основные</h2>
      <div class="row justify-content-center">
        <div class="col-3" v-for="plan in mainPlans">
          <div class="plans_item">
            <div>
              <b>{{ plan.name }}</b>
            </div>
            <div>
              {{ plan.months | pluralize('месяц', 'месяца', 'месяцев') }}
            </div>
            <div class="check_list_item">
              <div class="padding" data-toggle="collapse" :data-target="'#plans_' + plan.id" aria-expanded="false" :aria-controls="'plans_' + plan.id">
                <!-- {{ plan.disciplines | pluralize('предмет', 'предмета', 'предметов') }} -->
                Выберите предметы
                <img src="/fonts/icon-arrow-down.svg" alt="">
              </div>
              <div class="collapse check_list" :id="'plans_' + plan.id">
                <div class="check simple with_count" v-for="discipline in meta.disciplines">
                  <input
                  type="checkbox"
                  :id="'check_plans_' + plan.id + discipline.id"
                  v-model="plan.disciplineIds"
                  :value="discipline.id"
                  >
                  <label :for="'check_plans_' + plan.id + discipline.id">{{ discipline.name }}</label>
                </div>
                <button class="btn btn-sky full-width" data-toggle="collapse" :data-target="'#plans_' + plan.id" aria-expanded="false" :aria-controls="'plans_' + plan.id">
                  Подтвердить
                </button>
              </div>
            </div>
            <div>
              <span>{{ getMainPlanPrice(plan) }} ₽</span>
            </div>
          </div>

          <a href="#" class="btn btn-green full-width" @click.prevent="purchasePlan(plan)">Приобрести</a>
        </div>
      </div>
      <h2 class="margin-top-100">Дополнительные</h2>
      <div class="row justify-content-center">
        <div class="col-3" v-for="plan in additionalPlans">
          <div class="plans_item">
            <div>
              <b>{{ plan.name }}</b>
              <small class="text-green" v-if="plan.months > 0">*Только при активной подписке</small>
              <div class="check_list_item">
                <div class="padding" data-toggle="collapse" :data-target="'#plans_' + plan.id" aria-expanded="false" :aria-controls="'plans_' + plan.id">
                  <div v-if="plan.requests > 0">{{ plan.selectedCount | pluralize('консультация', 'консультации', 'консультаций') }}</div>
                  <div v-else-if="plan.tests > 0">{{ plan.selectedCount | pluralize('тест', 'теста', 'тестов') }}</div>
                  <div v-else-if="plan.months > 0">1 месяц</div>
                  <img src="/fonts/icon-arrow-down.svg" alt="" v-if="plan.months === 0">
                </div>
                <div class="collapse check_list" :id="'plans_' + plan.id" v-if="plan.months === 0">
                  <div class="row justify-content-center">
                    <div class="count">
                      <span class="minus" @click="decrement(plan)">-</span>
                      <input type="text" v-model="plan.selectedCount">
                      <span class="plus" @click="increment(plan)">+</span>
                    </div>
                  </div>
                  <button class="btn btn-sky full-width" data-toggle="collapse" :data-target="'#plans_' + plan.id" aria-expanded="false" aria-controls="'plans_' + plan.id">
                    Подтвердить
                  </button>
                </div>
              </div>
            </div>
            <div>
              <span>{{ getAdditionalPlanPrice(plan) }} ₽</span>
            </div>
          </div>
          <a href="#" class="btn btn-green full-width" @click.prevent="purchasePlan(plan)">Приобрести</a>
        </div>
      </div>

      <h2 class="margin-top-100">Моя информация</h2>
      <div class="block my_info">
        <div class="row align-items-center justify-content-between">
          <div class="col">Остаток на счету:</div>
          <div class="col text-right">
            <span class="text-green">{{ availableResources.money / 100 }} ₽</span>
            <a href="#" class="btn btn-sky" @click.prevent="$modals.addMoney.$show()">
              Пополнить счет
            </a>
          </div>
        </div>
        <template v-if="Auth.user.isSubscriptionActive">
          <div class="row justify-content-between">
            <div class="col">Осталось запросов на консультацию:</div>
            <div class="col text-right">{{ availableResources.requests }}</div>
          </div>
          <div class="row justify-content-between">
            <div class="col">Осталось запросов на проверку тестов:</div>
            <div class="col text-right">{{ availableResources.tests }}</div>
          </div>
        </template>
      </div>
      <tabs :active="activeTab" :nav-item-class="['nav-item', 'nav-link']" content-class="block my_info" v-if="Auth.user.disciplineIds.length">
        <tab :key="subscription.id" v-for="subscription in Auth.user.disciplineSubscriptions" :header="subscription.discipline.name">
          <div class="row align-items-center justify-content-between">
            <div class="col">Срок действия:</div>
            <div class="col text-right" :class="{'text-danger': !Auth.user.isSubscriptionActive}">
              до {{ subscription.subscriptionEndsAt | format('ru') }}
            </div>
          </div>
        </tab>
      </tabs>
    </loading-indicator>
    <custom-vudal name="addMoney" :auto-center="false" id="modal_buy">
      <div class="modal-dialog" role="document">
        <h3>Пополнение личного счета</h3>
        <!-- <h2>Сумма, которая будет зачислена на счет - 0 рублей</h2> -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Введите сумму в рублях</h5>
            <button type="button" class="close" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col">
                <input type="text" value="1000" v-model="addAmount">
              </div>
              <div class="col-3">
                <a href="#" class="btn btn-black large" @click.prevent="addMoney()">Оплатить</a>
              </div>
            </div>
            <img class="mt-5" src="/images/payments.png" alt="">
          </div>
          <div class="modal-footer">
          </div>
        </div>
      </div>
    </custom-vudal>
  </div>
</template>
<script>
import Noty from 'noty';
import Account from '_shared/services/Account';
import ClientConfig from '_shared/services/ClientConfig';
import Plan from '_shared/resources/Plan';
import pluralize from '_shared/filters/pluralize';
import format from '_shared/filters/format';

export default {

  components: {
    LoadingIndicator: () => import('_components/loadingIndicator'),
    CustomVudal: () => import('_components/customVudal'),
    Tab: () => import('_components/tab'),
    Tabs: () => import('_components/tabs'),
  },

  filters: {
    pluralize,
    format,
  },

  mounted() {
    this.loadPlans();
    this.loadAvailableResources();
  },

  data() {
    return {
      plans: [],

      availableResources: {
        money: 0,
        requests: 0,
        tests: 0,
      },

      activeTab: 0,

      meta: {},

      loading: true,

      addAmount: 1000,
    };
  },

  computed: {
    mainPlans() {
      return this.plans.filter(plan => plan.main).map((plan) => {
        this.$set(plan, 'selectedCount', 0);
        this.$set(plan, 'disciplineIds', []);
        return plan;
      });
    },

    additionalPlans() {
      return this.plans.filter(plan => !plan.main).map((plan) => {
        this.$set(plan, 'selectedCount', 0);
        this.$set(plan, 'disciplineIds', []);
        return plan;
      });
    },
  },

  methods: {
    loadPlans() {
      this.loading = true;
      Plan.query().then((response) => {
        this.plans = response.data.data;
        this.meta = response.data.meta;
      }).finally(() => {
        this.loading = false;
      });
    },

    loadAvailableResources() {
      Account.getAvailableResources().then((data) => {
        this.availableResources = data;
      });
    },

    addMoney() {
      Account.addMoney(this.addAmount).then(() => {
        this.loadAvailableResources();
        new Noty({
          text: `Ваш баланс успешно пополнен на ${this.addAmount} рублей`,
          timeout: 7000,
        }).show();
        this.$modals.addMoney.$hide();
      }).catch((response) => {
        new Noty({
          text: response.data.error,
          type: 'error',
          timeout: 7000,
        }).show();
      });
    },

    purchasePlan(plan) {
      if (!plan.main && !this.Auth.user.isSubscriptionActive) {
        this.$modals.alert('Необходимо иметь активную подписку, чтобы купить этот тариф');
        return;
      }

      Account.purchasePlan(plan).then(() => {
        location.reload();
      }).catch((response) => {
        new Noty({
          text: response.data.error,
          type: 'error',
          timeout: 7000,
        }).show();
      });
    },

    isSubscribed(disciplineId) {
      return this.Auth.user.disciplineIds.indexOf(disciplineId) !== -1;
    },

    increment(plan) {
      // const selectedCount = plan.selectedCount + 1;
      // this.$set(plan, 'selectedCount', selectedCount);
      Object.assign(plan, { selectedCount: plan.selectedCount + 1 });
    },

    decrement(plan) {
      Object.assign(plan, { selectedCount: plan.selectedCount - 1 });
    },

    getMainPlanPrice(plan) {
      const disciplinesCount = plan.disciplineIds.length;
      if (disciplinesCount === 0) {
        return 0;
      }

      const discounts = ClientConfig.discounts;

      let discount = discounts[discounts.length - 1];
      if (disciplinesCount <= discounts.length) {
        discount = discounts[disciplinesCount - 1];
      }

      let price = plan.price * disciplinesCount;
      price -= (price * discount) / 100;
      return Math.ceil(price / 100);
    },

    getAdditionalPlanPrice(plan) {
      const price = plan.months > 0 ? plan.price : plan.price * plan.selectedCount;
      return Math.ceil(price / 100);
    },
  },

};
</script>

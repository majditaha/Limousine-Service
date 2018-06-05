<template>
  <div class="block">
    <div class="row">
      <div class="col-6">
        <div class="now">
          {{ incomeLabel }} <span class="text-green">{{ totalIncome / 100 }}₽</span>
        </div>
      </div>
      <div class="col-3">
        <select v-model="periodType">
          <option value="week">Отчет за неделю</option>
          <option value="month">Отчет за месяц</option>
          <option value="year">Отчет за год</option>
        </select>
      </div>
      <div class="col-3">
        <select v-model="period">
          <option v-for="period in periods" :value="period">{{ period.str }}</option>
        </select>
      </div>
    </div>
    <line-chart
      :chart-data="chartData"
      :width="550"
      :height="300"
    ></line-chart>
    <ul class="rating-list">
      <li>
        <div class="row">
          <div class="col">
            Доход:
          </div>
          <div class="col text-right">
            {{ totalIncome / 100 }}₽
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col">
            Доход от решения заданий:
          </div>
          <div class="col text-right">
            {{ totalRequests / 100 }}₽
          </div>
        </div>
      </li>
      <li>
        <div class="row">
          <div class="col">
            Доход от решения тестов ЭГЭ:
          </div>
          <div class="col text-right">
            {{ totalTests / 100 }}₽
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>
import moment from 'moment';
import _ from 'lodash/fp';
import { dbTime } from '_shared/lib/dateFormats';
import { Line, mixins } from 'vue-chartjs';

const { reactiveProp } = mixins;

const LineChart = {
  extends: Line,

  mixins: [reactiveProp],

  mounted() {
    this.renderChart(this.chartData, this.options);
  },
};

export default {

  components: {
    LineChart,
  },

  created() {
    this.period = _.last(this.periods);
    this.loadStatistics();
  },

  data() {
    return {
      periodType: 'week',

      periodTypes: ['week', 'month', 'year'],

      period: null,

      data: {
        checkRequestAmount: 0,
        checkTestAmount: 0,
      },
    };
  },

  computed: {
    incomeLabel() {
      switch (this.periodType) {
        case 'week':
          return 'Недельный доход';
        case 'month':
          return 'Месячный доход';
        case 'year':
          return 'Годовой доход';
        default:
          return '';
      }
    },

    totalIncome() {
      return this.totalRequests + this.totalTests;
    },

    totalRequests() {
      return _.sumBy(item => item.checkRequestAmount)(this.data);
    },

    totalTests() {
      return _.sumBy(item => item.checkTestAmount)(this.data);
    },

    chartLabels() {
      switch (this.periodType) {
        case 'week':
          return ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС'];

        case 'month':
          return _.range(
            +moment(this.period.start).format('D')
          )(
            +moment(this.period.end).format('D') + 1
          );

        case 'year':
          return moment.months();

        default:
          return [];
      }
    },

    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [{
          label: 'Доход',
          data: this.chartLabels.map((label, index) => {
            const foundItem = _.find(item => +item.datePart === index + 1)(this.data);
            return foundItem != null
              ? (foundItem.checkRequestAmount + foundItem.checkTestAmount) / 100
              : 0;
          }),
          backgroundColor: '#78d7fc',
        }],
      };
    },

    periods() {
      switch (this.periodType) {
        case 'week':
          return this.weeks;

        case 'month':
          return this.months;

        case 'year':
          return this.years;

        default:
          return [];
      }
    },

    weeks() {
      const startOfCurrentWeek = moment().startOf('week');
      const startAt = moment(this.Auth.user.registeredDate).startOf('week');

      const current = moment();

      let date = startAt;

      const weeks = [];

      while (date <= startOfCurrentWeek) {
        const start = date.clone();
        const end = date.clone().endOf('week');

        const endYear = end.format('YYYY');
        const currentYear = current.format('YYYY');

        const startFormat = start.format('M') === end.format('M') ? 'D' : 'D MMM';
        const endFormat = +endYear >= +currentYear ? 'D MMM' : 'D MMM YYYY';
        weeks.push({
          str: `${start.format(startFormat)}-${end.format(endFormat)}`,
          start: start.format(dbTime),
          end: end.format(dbTime),
        });
        date = date.add(1, 'week');
      }

      return weeks;
    },

    months() {
      const startAt = moment(this.Auth.user.registeredDate);

      const current = moment().add(1, 'month');

      let date = startAt;

      const months = [];

      while (date.format('MYYYY') !== current.format('MYYYY')) {
        const year = date.format('YYYY');
        const currentYear = current.format('YYYY');

        const format = year === currentYear ? 'MMMM' : 'MMMM YYYY';
        months.push({
          str: date.format(format),
          start: date.clone().startOf('month').format(dbTime),
          end: date.clone().endOf('month').format(dbTime),
        });
        date = date.add(1, 'month');
      }

      return months;
    },

    years() {
      const startAt = moment(this.Auth.user.registeredDate);

      const current = moment();

      let date = startAt;

      const years = [];

      while (+date.format('YYYY') <= +current.format('YYYY')) {
        years.push({
          str: date.format('YYYY'),
          start: date.clone().startOf('year').format(dbTime),
          end: date.clone().endOf('year').format(dbTime),
        });
        date = date.add(1, 'year');
      }

      return years;
    },
  },

  methods: {
    loadStatistics() {
      const { start: dateStart, end: dateEnd } = this.period;
      const periodType = this.periodType;
      this.$http.get('/api/teacher/statistics', {
        params: { dateStart, dateEnd, periodType },
      }).then((response) => {
        this.data = response.body;
      });
    },
  },

  watch: {
    periodType() {
      this.period = _.last(this.periods);
    },

    period() {
      this.loadStatistics();
    },
  },

};
</script>

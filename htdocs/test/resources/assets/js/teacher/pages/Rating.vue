<template>
  <div class="block">
    <div class="title mb-0">Оценки</div>
    <div class="text-right">
      <div class="now">
        текущая оценка <span>{{ Auth.user.rating.avg }}</span>
      </div>
    </div>
      <ul class="rating-list">
        <li v-for="n in range">
          <div class="row">
              <div class="col">
                {{ n }} <img src="/fonts/icon-star.svg" alt="">
              </div>
              <div class="col text-right">
                {{ Auth.user.rating.stars[n] || 0 }}%
              </div>
          </div>
          <div class="bar" :style="{ width: Auth.user.rating.stars[n] + '%' }"></div>
        </li>
      </ul>
      <div class="text-center mt-3">
          Пользователи оценивают работу по шкале от <b>1</b> до <b>5</b> звезд. Ваше рейтинг - это среднее значение,<br>выставленных вам пользователями за последние <b>500 работ</b>.
      </div>
  </div>
</template>
<script>
import _ from 'lodash/fp';

export default {
  data() {
    return {
      range: _.range(5)(0),
    };
  },
};
</script>

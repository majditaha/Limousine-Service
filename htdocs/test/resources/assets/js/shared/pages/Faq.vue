<template>
  <div>
    <div class="input-title">
      <div class="row align-items-center">
        <div class="col">
          <div class="pl-3">
            <input type="text" placeholder="Начните писать здесь..">
          </div>
        </div>
        <div class="col-1">
          <button class="btn"><img src="assets/img/icons/icon-search.svg" alt=""></button>
        </div>
        <div class="col-2">
          <a href="#" class="btn btn-black large full-width">Добавить вопрос</a>
        </div>
      </div>
    </div>
    <div class="block mb-4">
      <div class="row">
        <div class="col-2 text-center">
          <div class="image-block">
            <img src="assets/img/user.jpg" alt="">
          </div>
        </div>
        <div class="col">
          <div class="title">Проблемы с внесением оплаты</div>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ullam porro error vitae corrupti saepe eveniet earum adipisci ad natus cupiditate, mollitia assumenda hic labore quos, velit deleniti quae eius voluptatem.
          </p>
        </div>
      </div>
    </div>
    <div class="row justify-content-end">
      <div class="col-11">
        <div class="block block_answer mb-4">
          <div class="title font-weight-bold">Ответ: Проблемы с внесением оплаты</div>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ullam porro error vitae corrupti saepe eveniet earum adipisci ad natus cupiditate, mollitia assumenda hic labore quos, velit deleniti quae eius voluptatem.
          </p>
          <div class="panel text-right">
            <a href="#"><img src="assets/img/icons/icon-answer.svg" alt=""> Ответить</a>
            <span class="text-green"><img src="assets/img/icons/icon-check-green.svg" alt=""> Проблема решена</span>
          </div>
        </div>
      </div>
    </div>
    <h1 class="mt-5 mb-3">Популярные вопросы (FAQ)</h1>
    <div class="block small" data-toggle="collapse" data-target="#faq-1" aria-expanded="false" aria-controls="faq-1">
      <b>Как осуществить оплату?</b>
      <div class="collapse" id="faq-1">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempora repellendus delectus accusantium explicabo fugit maxime quod nostrum doloribus sapiente omnis.</p>
      </div>
    </div>
    <div class="block small" data-toggle="collapse" data-target="#faq-2" aria-expanded="false" aria-controls="faq-2">
      <b>Как запросить консультацию?</b>
      <div class="collapse" id="faq-2">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempora repellendus delectus accusantium explicabo fugit maxime quod nostrum doloribus sapiente omnis.</p>
      </div>
    </div>
  </div>
</template>
<script>
/* This page is a backup and not used right now. */
import Message from '_shared/resources/Message';

export default {

  created() {
    Message.query().then((response) => {
      this.messages = response.data.body;
    });
  },

  data() {
    return {
      messages: [],
    };
  },

};
</script>

<template>
  <div class="panel panel-primary">
    <div class="panel-heading" @click="filterVisible = !filterVisible">
      <i class="fa fa-search"></i>Фильтр
      <div class="actions pull-right">
        <a class="btn btn-default btn-xs btn-collapse">
          <i class="fa fa-minus" v-if="filterVisible"></i>
          <i class="fa fa-plus" v-else></i>
        </a>
      </div>
    </div>
    <div class="panel-body" v-show="filterVisible">
      <div>
        <div class="row">
          <slot></slot>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <div class="page-actions pull-right">
              <button type="button" class="btn btn-default btn-md btn-icon" @click="clear()">
                <i class="fa fa-close"></i>Очистить
              </button>
              <button type="button" class="btn btn-primary btn-md btn-icon" @click="apply()">
                <i class="fa fa-search"></i>Поиск
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import ErrorMessages from './errorMessages';

export default {

  props: {
    name: {
      type: String,
      required: true,
    },

    currentFilter: Object,
  },

  components: { ErrorMessages },

  data() {
    return {
      filterVisible: false,
    };
  },

  methods: {
    apply() {
      this.$emit('apply');
    },

    clear() {
      this.$emit('clear');
    },
  },

};
</script>

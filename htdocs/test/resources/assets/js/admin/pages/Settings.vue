<template>
  <div>

    <loading-indicator :loading="loading">
      <div class="table-responsive">
        <table class="table table-striped table-selectable">
          <thead>
            <tr>
              <th>Описание</th>
              <th>Значение</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" @click="edit(item)">
              <td>{{ item.description }}</td>
              <td>{{ item.value }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </loading-indicator>

    <pager
      :total-items="meta.total"
      :current-page="currentPage"
      :page-size="itemsPerPage"
      @page-changed="onPageChanged"
    ></pager>

    <vudal name="setting">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title">Изменение настройки</h4>
      </div>
      <div class="content">
        <form>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <label>{{ current.description }}</label>
                <input type="text" v-model="current.value" class="form-control">
                <error-messages :errors="errors.value"></error-messages>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-default cancel">Закрыть</button>
        <button type="submit" class="btn btn-primary btn-md" @click="save()">Сохранить</button>
      </div>
    </vudal>
  </div>
</template>
<script>
import rest from '_shared/mixins/rest';
import Setting from '_shared/resources/admin/Setting';

export default {

  mixins: [rest(Setting)],

  components: {
    Pager: () => import('_components/pager'),
    LoadingIndicator: () => import('_components/loadingIndicator'),
    ErrorMessages: () => import('_components/errorMessages'),
    Vudal: () => import('vudal'),
  },

  mounted() {
    this.$on('editing', () => {
      this.$modals.setting.$show();
    });
    this.$on('updated', () => {
      this.$modals.setting.$hide();
    });
  },

};
</script>

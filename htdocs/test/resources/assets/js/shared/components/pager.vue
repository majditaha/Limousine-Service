<template>
  <ul class="pagination">
    <li class="paginate_button first" :class="{disabled: internalPage == 1}">
      <a href="#" tabindex="0" @click.prevent="setPage(1)">Первая</a>
    </li>
    <li class="paginate_button previous" :class="{disabled: internalPage - 1 == 0}">
      <a href="#" tabindex="0" @click.prevent="setPage(internalPage - 1)">Пред.</a>
    </li>
    <li class="paginate_button" :class="{active: page == internalPage}" v-for="page in pages" @click.prevent="setPage(page)">
      <a href="#" tabindex="0">{{ page }}</a>
    </li>
    <li class="paginate_button next" :class="{disabled: internalPage + 1 == pages.length + 1}">
      <a href="#" tabindex="0" @click.prevent="setPage(internalPage + 1)">След.</a>
    </li>
    <li class="paginate_button last" :class="{disabled: internalPage == pages.length}">
      <a href="#" tabindex="0" @click.prevent="setPage(pages.length)">Посл.</a>
    </li>
  </ul>
</template>
<script>
import _ from 'lodash/fp';

export default {
  props: {
    currentPage: {
      type: Number,
      default: 1,
    },

    pageSize: {
      type: Number,
      default: 25,
    },

    totalItems: {
      type: Number,
      default: 0,
    },
  },

  mounted() {
    this.updateTotalPages();
    this.updatePages();
  },

  data() {
    return {
      pages: [],
      totalPages: 0,
      internalPage: this.currentPage,
    };
  },

  methods: {
    onPageSelected(page) {
      this.$emit('page-changed', page);
    },

    setPage(page) {
      if (page <= 0 || page > this.totalPages) return;

      this.internalPage = page;
      this.updatePages();
      this.onPageSelected(page);
    },

    prevPage() {
      if (this.internalPage === 1) {
        return;
      }
      this.setPage(this.internalPage - 1);
    },

    nextPage() {
      if (this.internalPage === this.totalPages) {
        return;
      }
      this.setPage(this.internalPage + 1);
    },

    // Update starting and ending pages
    updatePages() {
      let begin = this.internalPage - 2;

      if (this.internalPage - 2 <= 1) {
        begin = 1;
      }
      if (this.internalPage === this.totalPages) {
        begin -= 2;
      }
      else if (this.internalPage === this.totalPages - 1) {
        begin -= 1;
      }
      if (begin < 1) {
        begin = 1;
      }

      let end = this.internalPage + 2;

      if (this.internalPage + 2 > this.totalPages) {
        end = this.totalPages;
      }
      if (this.internalPage === 1) {
        end += 2;
      }
      else if (this.internalPage === 2) {
        end += 1;
      }
      if (end > this.totalPages) {
        end = this.totalPages;
      }
      if (!end) {
        end = begin;
      }
      this.pages = _.range(begin, end + 1);
    },

    updateTotalPages() {
      this.totalPages = _.floor(this.totalItems / this.pageSize);
      if (this.totalItems % this.pageSize > 0 || this.totalItems === 0) {
        this.totalPages += 1;
      }
    },
  },

  watch: {
    totalItems() {
      this.updateTotalPages();
      this.updatePages();
    },

    currentPage(value) {
      this.setPage(value);
      this.updateTotalPages();
      this.updatePages();
    },

    pageSize() {
      this.updateTotalPages();
      this.updatePages();
    },
  },
};
</script>

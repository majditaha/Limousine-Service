import Page from '_shared/services/Page';
import PageContent from '_shared/pages/PageContent';
import NotFound from '_shared/pages/NotFound';
import LoadingIndicator from '_components/loadingIndicator';

export default {

  mounted() {
    this.loadPages();
  },

  data() {
    return {
      pages: [],
      found: true,
      loading: null,
    };
  },

  render(h) {
    if (!this.found) {
      return h(NotFound);
    }

    const { loading, pages } = this;

    return h(LoadingIndicator, {
      props: { loading },
    }, [
      h(PageContent, {
        props: { pages },
      }),
    ]);
  },

  methods: {
    loadPages() {
      this.loading = Page.get(this.$route.params.alias).then((pages) => {
        this.pages = pages;
        this.found = true;
      }).catch(() => {
        this.found = false;
      });
    },
  },

  watch: {
    $route() {
      this.loadPages();
    },
  },

};

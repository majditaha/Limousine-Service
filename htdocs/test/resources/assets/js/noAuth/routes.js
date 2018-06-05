import sharedRoutes from '_shared/routes';
import _ from 'lodash/fp';

export default _.concat([
  {
    name: 'mainPage',
    path: '/',
    component: () => import('_noAuth/pages/Main'),
  },
], sharedRoutes);

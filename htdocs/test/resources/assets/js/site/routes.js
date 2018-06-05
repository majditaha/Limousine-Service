import sharedRoutes from '_shared/routes';
import _ from 'lodash/fp';

export default _.concat([
  {
    name: 'mainPage',
    path: '/',
    component: () => import('_site/pages/Main'),
    meta: {
      pageTitle: 'Главная',
      blueBackground: true,
    },
  },
  {
    name: 'userForm',
    path: '/form/user',
    component: () => import('_site/pages/UserForm'),
    meta: {
      title: 'Анкета пользователя',
      breadcrumbs: [
        { title: 'Анкета пользователя', url: '/form/user' },
      ],
    },
  },
  {
    name: 'profile',
    path: '/profile',
    component: () => import('_shared/pages/Profile'),
    meta: {
      title: 'Личный кабинет',
      breadcrumbs: [
        { title: 'Личный кабинет', url: 'profile' },
      ],
    },
  },
  {
    name: 'plans',
    path: '/plans',
    component: () => import('_site/pages/Plans'),
    meta: {
      pageTitle: 'Тарифы',
      breadcrumbs: [
        { title: 'Тарифы', url: null },
      ],
    },
  },
  {
    name: 'messages',
    path: '/messages/:uid?',
    component: () => import('_site/pages/Messages'),
    meta: {
      title: 'Обращения',
      breadcrumbs: [
        { title: 'Аккаунт ученика', url: 'profile' },
        { title: 'Обращения', url: 'messages' },
      ],
    },
  },
  {
    name: 'discipline',
    path: '/discipline/:disciplineId',
    component: () => import('_site/pages/Discipline'),
    meta: {
      blueBackground: true,
    },
  },
  {
    name: 'theories',
    path: '/discipline/:disciplineId/theories',
    component: () => import('_site/pages/Theories'),
    meta: {
      pageTitle: 'Теории',
      blueBackground: true,
    },
  },
  {
    name: 'theory',
    path: '/discipline/:disciplineId/theories/:sectionId/:theoryId?',
    component: () => import('_site/pages/Theory'),
    meta: {
      pageTitle: 'Теории',
    },
  },
  {
    name: 'tests',
    path: '/discipline/:disciplineId/tests',
    component: () => import('_site/pages/Tests'),
    meta: {
      blueBackground: true,
    },
  },
  {
    name: 'test',
    path: '/discipline/:disciplineId/tests/:variantId',
    component: () => import('_site/pages/Test'),
    meta: {
      showTimer: true,
    },
  },
  {
    name: 'practices',
    path: '/discipline/:disciplineId/practices',
    component: () => import('_site/pages/Practices'),
    meta: {
      blueBackground: true,
    },
  },
  {
    name: 'practice',
    path: '/discipline/:disciplineId/practices/:sectionId',
    component: () => import('_site/pages/Practice'),
    meta: {
      title: 'Практическое задание',
      showTimer: true,
    },
  },
  {
    name: 'trainings',
    path: '/discipline/:disciplineId/trainings',
    component: () => import('_site/pages/Training'),
    meta: {
      pageTitle: 'Обучение',
      showTimer: true,
    },
  },
  {
    name: 'trainingsStat',
    path: '/discipline/:disciplineId/trainings_stat',
    component: () => import('_site/pages/TrainingsStat'),
    meta: {
      pageTitle: 'Обучение',
    },
  },
  {
    name: 'training',
    path: '/discipline/:disciplineId/training/:sectionId',
    component: () => import('_site/pages/Training'),
    meta: {
      pageTitle: 'Обучение',
      showTimer: true,
    },
  },
  {
    name: 'trainingPractices',
    path: '/discipline/:disciplineId/training/:sectionId/practices',
    component: () => import('_site/pages/TrainingPractices'),
    meta: {
      pageTitle: 'Обучение',
      showTimer: true,
    },
  },
], sharedRoutes);

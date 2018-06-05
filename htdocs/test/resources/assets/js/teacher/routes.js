import sharedRoutes from '_shared/routes';
import _ from 'lodash/fp';

export default _.concat([
  {
    name: 'mainPage',
    path: '/',
    component: () => import('_teacher/pages/Requests'),
    meta: {
      title: 'Запросы на проверку заказов',
      breadcrumbs: [
        { title: 'Личный кабинет', url: '/profile' },
        { title: 'Запросы на проверку заказов', url: '/' },
      ],
      showTeacherMenu: true,
      showTeacherTimer: true,
    },
  },
  {
    name: 'teacherForm',
    path: '/form/teacher',
    component: () => import('_teacher/pages/TeacherForm'),
    meta: {
      title: 'Анкета преподавателя',
      breadcrumbs: [
        { title: 'Анкета преподавателя', url: '/form/teacher' },
      ],
    },
  },
  {
    name: 'profile',
    path: '/profile',
    component: () => import('_shared/pages/Profile'),
    meta: {
      title: 'Анкета преподавателя',
      breadcrumbs: [
        { title: 'Личный кабинет', url: '/profile' },
      ],
    },
  },
  {
    name: 'rating',
    path: '/rating',
    component: () => import('_teacher/pages/Rating'),
    meta: {
      title: 'Мой рейтинг',
      breadcrumbs: [
        { title: 'Личный кабинет', url: '/profile' },
        { title: 'Мой рейтинг', url: 'rating' },
      ],
      showTeacherMenu: true,
    },
  },
  {
    name: 'statistics',
    path: '/statistics',
    component: () => import('_teacher/pages/Statistics'),
    meta: {
      title: 'Текущий баланс',
      breadcrumbs: [
        { title: 'Личный кабинет', url: '/profile' },
        { title: 'Текущий баланс', url: 'statistics' },
      ],
      showTeacherMenu: true,
    },
  },
  {
    name: 'finishedRequests',
    path: '/finished_requests',
    component: () => import('_teacher/pages/FinishedRequests'),
    meta: {
      title: 'Выполненные заказы',
      breadcrumbs: [
        { title: 'Личный кабинет', url: '/profile' },
        { title: 'Выполненные заказы', url: null },
      ],
      showTeacherMenu: true,
    },
  },
], sharedRoutes);

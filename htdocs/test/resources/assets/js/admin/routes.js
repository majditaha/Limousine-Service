export default [
  {
    path: '/',
    component: () => import('_admin/pages/Dashboard'),
  },
  {
    name: 'users',
    path: '/users',
    component: () => import('_admin/pages/Users'),
    meta: {
      title: 'Пользователи',
      sidebar: true,
    },
  },
  {
    name: 'cities',
    path: '/cities',
    component: () => import('_admin/pages/Cities'),
    meta: {
      title: 'Города',
      sidebar: true,
    },
  },
  {
    name: 'schools',
    path: '/schools',
    component: () => import('_admin/pages/Schools'),
    meta: {
      title: 'Школы',
      sidebar: true,
    },
  },
  {
    name: 'disciplines',
    path: '/disciplines',
    component: () => import('_admin/pages/Disciplines'),
    meta: {
      title: 'Предметы',
      sidebar: true,
    },
  },
  {
    name: 'sections',
    path: '/sections',
    component: () => import('_admin/pages/Sections'),
    meta: {
      title: 'Блоки',
      sidebar: true,
    },
  },
  {
    name: 'theories',
    path: '/theories',
    component: () => import('_admin/pages/Theories'),
    meta: {
      title: 'Теория',
      sidebar: true,
    },
  },
  {
    name: 'subtypes',
    path: '/subtypes',
    component: () => import('_admin/pages/Subtypes'),
    meta: {
      title: 'Подтипы',
      sidebar: true,
    },
  },
  {
    name: 'variants',
    path: '/variants',
    component: () => import('_admin/pages/Variants'),
    meta: {
      title: 'Варианты',
      sidebar: true,
    },
  },
  {
    name: 'practices',
    path: '/practices',
    component: () => import('_admin/pages/Practices'),
    meta: {
      title: 'Практики',
      sidebar: true,
    },
  },
  {
    name: 'transactions',
    path: '/transactions',
    component: () => import('_admin/pages/Transactions'),
    meta: {
      title: 'Баланс и платежи',
      sidebar: true,
    },
  },
  {
    name: 'messages',
    path: '/messages',
    component: () => import('_admin/pages/Messages'),
    meta: {
      title: 'Сообщения',
      sidebar: true,
    },
  },
  {
    name: 'settings',
    path: '/settings',
    component: () => import('_admin/pages/Settings'),
    meta: {
      title: 'Настройки',
      sidebar: true,
    },
  },
  {
    name: 'menu_items',
    path: '/menu_items',
    component: () => import('_admin/pages/MenuItems'),
    meta: {
      title: 'Меню сайта',
      sidebar: true,
    },
  },
  {
    name: 'pages',
    path: '/pages',
    component: () => import('_admin/pages/Pages'),
    meta: {
      title: 'Тексты',
      sidebar: true,
    },
  },
];

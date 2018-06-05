export default [
  {
    name: 'confirmation',
    path: '/confirmation',
    component: () => import('_shared/pages/Confirmation'),
  },
  {
    name: 'passwordReset',
    path: '/password/reset/:token',
    component: () => import('_shared/pages/PasswordReset'),
  },
  {
    name: 'agreement',
    path: '/agreement',
    component: () => import('_shared/pages/Agreement'),
  },
  {
    path: '/:alias',
    component: () => import('_shared/pages/Static'),
    meta: {
      static: true,
    },
  },
];

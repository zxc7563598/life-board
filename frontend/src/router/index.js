import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'HomeView',
    component: () => import('../views/Home.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'LoginView',
    component: () => import('../views/Login.vue'),
    meta: { hideLayout: true },
  },
  {
    path: '/register',
    name: 'RegisterView',
    component: () => import('../views/Register.vue'),
    meta: { hideLayout: true },
  },
  {
    path: '/profile',
    name: 'ProfileView',
    component: () => import('../views/Profile.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/bill',
    name: 'BillView',
    component: () => import('../views/Bill.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/bill-analytics',
    name: 'BillAnalyticsView',
    component: () => import('../views/BillAnalytics.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/todo-list',
    name: 'TodoListView',
    component: () => import('../views/TodoList.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('token')
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  }
  else {
    next()
  }
})

export default router

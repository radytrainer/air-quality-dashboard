import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/views/HomePage.vue'
import CityDetailView from '@/views/CityDetailView.vue'
import CompareCitiesView from '@/views/CompareCitiesView.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'
import Login from '@/views/Login.vue'
import { useAuthStore } from '@/stores/airQuality'
import RegisterPage from '@/views/RegisterPage.vue'

const routes = [
  {
    path: '/',
    redirect: '/home', 
  },
  {
    path: '/home',
    name: 'home',
    component: HomePage,
  },
  {
    path: '/city-detail',
    name: 'city-detail',
    component: CityDetailView,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/compare-cities',
    name: 'compare-cities',
    component: CompareCitiesView,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/analytics',
    name: 'analytics',
    component: AnalyticsView,
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router

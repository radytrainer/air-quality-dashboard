import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/views/HomePage.vue'
import CityDetailView from '@/views/CityDetailView.vue'
import CompareCitiesView from '@/views/CompareCitiesView.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'
import Login from '@/views/Login.vue'
import { useAuthStore } from '@/stores/airQuality'

const routes = [
  {
    path: '/',
    redirect: '/home', // Redirect root to /home
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
  },
  {
    path: '/compare-cities',
    name: 'compare-cities',
    component: CompareCitiesView,
  },
  {
    path: '/analytics',
    name: 'analytics',
    component: AnalyticsView,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
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

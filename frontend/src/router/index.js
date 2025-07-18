import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/views/HomePage.vue'
import CityDetailView from '@/views/CityDetailView.vue'
import CompareCitiesView from '@/views/CompareCitiesView.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'

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
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router

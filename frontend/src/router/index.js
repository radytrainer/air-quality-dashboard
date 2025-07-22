import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/views/HomePage.vue'
import CityDetailView from '@/views/CityDetailView.vue'
import AdminDashboard from '@/views/AdminDashboard.vue'
import CompareCitiesView from '@/views/CompareCitiesView.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'
import Login from '@/views/Login.vue'
import RegisterPage from '@/views/RegisterPage.vue'



import { useAuthStore } from '@/stores/airQuality'
import CityAQI from '@/views/CityAQI.vue'
import ContactView from '@/views/ContactView.vue'




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
    meta: { requiresAuth: true },
  },
  {
    path: '/compare-cities',
    name: 'compare-cities',
    component: CompareCitiesView,
    meta: { requiresAuth: true },
  },
  {
    path: '/analytics',
    name: 'analytics',
    component: AnalyticsView,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin-dashboard',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/cityaqi',
    name: 'cityaqi',
    component: CityAQI,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage,
  },
  {
    path: '/contact',
    name: 'contact',
    component: ContactView
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login')
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return next('/home') // Redirect normal user away from admin pages
  }

  next()
})

export default router



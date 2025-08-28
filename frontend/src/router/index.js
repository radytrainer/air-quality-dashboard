import { createRouter, createWebHistory } from 'vue-router'

import HomePage from '@/views/HomePage.vue'
import CityDetailView from '@/views/CityDetailView.vue'
import AdminDashboard from '@/views/AdminDashboard.vue'
import CompareCitiesView from '@/views/CompareCitiesView.vue'
import AnalyticsView from '@/views/AnalyticsView.vue'
import Login from '@/views/Login.vue'
import RegisterPage from '@/views/RegisterPage.vue'
import CityAQI from '@/views/CityAQI.vue'
import ContactView from '@/views/ContactView.vue'
import HealthAlert from '@/views/HealthAlert.vue'
import UserManagement from '@/views/UserManagement.vue'



import { useAuthStore } from '@/stores/airQuality'
import ContactUser from '@/views/ContactUser.vue'
import Profile from '@/views/Profile.vue'
import Message from '@/views/Message.vue'
import AdminNews from '@/views/AdminNews.vue'
import UserNews from '@/views/UserNews.vue'
import Categories from '@/views/Categories.vue'




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
    path: '/city/:id',
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
    path: '/user-news',
    name: 'user-news',
    component: UserNews,
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
    path: '/health-alert',
    name: 'health-alert',
    component: HealthAlert,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin-news',
    name: 'admin-news', 
    component: AdminNews,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/user-management',
    name: 'user-management',
    component: UserManagement,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/categories',
    name: 'categories',
    component: Categories,
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
  },
  {
    path: '/usercontact',
    name: 'contactuser',
    component: ContactUser
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile
  },
  {
    path: '/message',
    name: 'message',
    component: Message
  },
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
    return next('/home')
  }

  next()
})

export default router



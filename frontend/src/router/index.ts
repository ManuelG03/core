import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: () => import('../views/Home.vue')
    },
    {
      path: '/leitor',
      name: 'Leitor',
      component: () => import('../views/Leitor.vue')
    }
  ]
})

export default router

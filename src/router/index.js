import { createRouter, createWebHistory } from 'vue-router'
import AppForm from '@/components/admin/apps/appForm.vue'
import AppsList from '@/components/admin/apps/appsList.vue'
import RolesList from '@/components/admin/roles/rolesList.vue'
import UserForm from '@/components/admin/users/userForm.vue'
import UsersList from '@/components/admin/users/usersList.vue'
import Apps from '@/components/users/apps.vue'
import Login from '@/components/users/login.vue'
import { useAuthStore } from '@/stores/auth'
import Home from '@/components/Home.vue'
import ResetPwd from '@/components/users/resetPwd.vue'
import UpdatePwd from '@/components/users/updatePwd.vue'
import Notfound from '@/components/Notfound.vue'
const router = createRouter({
  history: createWebHistory(
    import.meta.env.BASE_URL || 'http://localhost:5173/',
  ),
  routes: [
    {
      path: '/home',
      name: 'accueil',
      alias: '/',
      component: Home,
    },
    {
      path: '/resetPassword',
      name: 'pwdReset',
      component: ResetPwd,
    },
    {
      path: '/updatePassword',
      name: 'pwdUpdate',
      component: UpdatePwd,
      meta: { requiresAuth: true }
    },
    {
      path: '/connexion',
      name: 'login',
      alias: '/login',
      component: Login,
    },
    {
      path: '/applications',
      name: 'app',
      component: Apps,
      meta: { requiresAuth: true },
    },
    {
      path: '/admin/applications',
      name: 'adminApps',
      component: AppsList,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/applications/:action',
      name: 'appForm',
      component: AppForm,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/users',
      name: 'adminUsers',
      component: UsersList,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/users/:action',
      name: 'userForm',
      component: UserForm,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/admin/roles',
      name: 'usersRoles',
      component: RolesList,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: '/404',
      name: '404',
      component: Notfound,
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/404'
    },
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth && (!authStore.token)) {
    next('/login')
  } else if (
    to.meta.requiresAuth &&
    to.meta.requiresAdmin &&
    authStore.token &&
    !authStore.user.is_admin
  ) {
    next('/applications')
  } else {
    next()
  }
})

export default router

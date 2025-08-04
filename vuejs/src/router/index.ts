import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '@/views/LoginView.vue';
import DashboardView from '../views/dashboard/DashboardView.vue';
import ModerationView from '../views/moderation/ModerationView.vue';
import GithubEnd from '@/views/GithubEnd.vue';
import { useAuthStore } from '@/stores/auth';

const routes = [
  { path: '/login', component: LoginView, meta: { public: true } },
  { path: '/', component: DashboardView },
  { path: '/moderation', component: ModerationView },
  { path: '/oauth/github', component: GithubEnd, meta: { public: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, _from, next) => {
  const auth = useAuthStore();
  if (!to.meta.public && !auth.isAuthenticated) {
    await auth.tryRefresh();
  }
  if (!to.meta.public && !auth.isAuthenticated) return next('/login');
  next();
});

export default router;

import { defineStore } from 'pinia';
import api from '@/services/api';
import type { Tokens, User } from '@/types.ts'



export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as null | User,
    tokens: null as null | Tokens,
  }),
  getters: {
    isAuthenticated: (s) => !!s.tokens?.access_token,
    isModerator: (s) => s.user?.roles.includes('moderator'),
  },
  actions: {
    setTokens(t: Tokens) {
      this.tokens = t;
      localStorage.setItem('tokens', JSON.stringify(t));
      api.defaults.headers.common.Authorization = `Bearer ${t.access_token}`;
    },

    loadTokens() {
      const raw = localStorage.getItem('tokens');
      if (raw) this.setTokens(JSON.parse(raw));
    },

    async login(email: string, password: string) {
      const { data } = await api.post<Tokens>('/auth/login', { email, password });
      this.setTokens(data);
      await this.fetchMe();
    },

    async githubLogin() {
      const { data } = await api.get<{ url: string }>('/auth/github/url');
      window.location.href = data.url;
    },

    async finishGithubLogin(code: string, state: string) {
      const { data } = await api.post('/auth/github/callback', { code, state });
      this.setTokens({ access_token: data.access_token, refresh_token: data.refresh_token });
      this.user = data.user;
    },

    async fetchMe() {
      const { data } = await api.get('/auth/me');
      this.user = data;
    },

    async tryRefresh() {
      this.loadTokens();

      if (!this.tokens) return;

      try {
        const { data } = await api.post<Tokens>('/auth/refresh', {
          refresh_token: this.tokens.refresh_token,
        });

        this.setTokens(data);

        await this.fetchMe();
      } catch {
        await this.logout();
      }
    },

    async logout() {
      try {
        await api.post('/auth/logout')
      } finally {
        this.tokens = null;
        this.user = null;
        localStorage.removeItem('tokens');
        delete api.defaults.headers.common.Authorization;
      }
    },
  },
});

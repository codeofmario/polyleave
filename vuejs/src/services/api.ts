import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const api = axios.create({
  baseURL: import.meta.env.VITE_BACKEND_URL || 'http://localhost:8000/api',
});

api.interceptors.response.use(
  (r) => r,
  async (err) => {
    if (err.response?.status === 401) {
      const auth = useAuthStore();
      await auth.tryRefresh();
      if (auth.isAuthenticated) {
        err.config.headers.Authorization = `Bearer ${auth.tokens!.access_token}`;
        return axios.request(err.config);
      }
    }
    return Promise.reject(err);
  },
);
export default api;

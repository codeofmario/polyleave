import { defineStore } from 'pinia';
import api from '@/services/api';
import type { Leave, LeaveStatus } from '@/types.ts'

export const useLeaveStore = defineStore('leave', {
  state: () => ({ leaves: [] as Leave[] }),
  actions: {
    async fetchApproved() {
      const { data } = await api.get<Leave[]>('/leaves');
      this.leaves = data;
    },

    async requestLeave(payload: { start_date: string; end_date: string; reason?: string }) {
      await api.post('/leaves', payload);
      await this.fetchApproved();
    },

    async listPending() {
      const { data } = await api.get<Leave[]>('/moderation/leaves/pending');
      this.leaves = data;
    },

    async decide(id: number, status: LeaveStatus) {
      await api.put(`/moderation/leaves/${id}`, { status });
      await this.listPending();
    },
  },
});

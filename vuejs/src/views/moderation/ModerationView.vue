<template>
  <v-container fluid>
    <h2 class="text-h5 mb-4">Pending Requests</h2>

    <ag-grid-vue
      class="ag-theme-quartz"
      style="width: 100%; height: 400px"
      :rowData="leave.leaves"
      :columnDefs="columns"
      :components="{ ActionCell }"
    />
  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { AgGridVue } from 'ag-grid-vue3';
import type { ColDef } from 'ag-grid-community';
import { useLeaveStore } from '@/stores/leave';
import ActionCell from './ActionCell.vue'
import type { LeaveStatus } from '@/types.ts'

const leave = useLeaveStore();

const columns: ColDef[] = [
  {
    headerName: 'User',
    valueGetter: ({ data }) => data.user?.name ?? data.user_id,
    flex: 1,
  },
  { field: 'start_date', headerName: 'From', flex: 1 },
  { field: 'end_date', headerName: 'To', flex: 1 },
  { field: 'reason', headerName: 'Reason', flex: 2 },
  {
    headerName: 'Actions',
    cellRenderer: ActionCell,
    cellStyle: { padding: 0 },
    cellRendererParams: {
      decide: (id: number, status: LeaveStatus) => {
        leave.decide(id, status);
      },
    },
  },
];

onMounted(leave.listPending);
</script>

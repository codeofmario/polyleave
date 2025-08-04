<template>
  <v-container fluid>
    <h2>Hello {{ auth.user?.name }}!</h2>


    <v-card class="my-4 pa-4">
      <div class="d-flex align-center justify-space-between mb-2">
        <h3 class="mb-0">Leave requests</h3>
        <v-btn color="primary" @click="dialog = true">
          <v-icon class="mr-1">mdi-plus</v-icon> New
        </v-btn>
      </div>

      <ag-grid-vue
        class="ag-theme-quartz w-100"
        :columnDefs="columnDefs"
        :rowData="leave.leaves"
        :style="{ height: '300px' }"
      />
    </v-card>

    <v-dialog v-model="dialog" max-width="600">
      <v-card class="pa-4">
        <h3 class="mb-4">Leave Request</h3>

        <v-form @submit.prevent="request">
          <v-text-field
            label="From"
            type="date"
            v-model="start"
            :rules="[required]"
            required
          />
          <v-text-field
            label="To"
            type="date"
            v-model="end"
            :rules="[required]"
            required
          />
          <v-text-field label="Reason" v-model="reason" />

          <div class="d-flex justify-end mt-4">
            <v-btn variant="text" class="mr-2" @click="dialog = false">
              Cancel
            </v-btn>
            <v-btn type="submit" color="primary">Send</v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>
  </v-container>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { AgGridVue } from 'ag-grid-vue3'
import { useAuthStore } from '@/stores/auth'
import { useLeaveStore } from '@/stores/leave'
import StatusCell from '@/views/dashboard/StatusCell.vue'


const auth  = useAuthStore()
const leave = useLeaveStore()

const columnDefs = [
  { field: 'start_date', headerName: 'From',   flex: 1 },
  { field: 'end_date',   headerName: 'To',    flex: 1 },
  { field: 'reason',     headerName: 'Reason', flex: 2 },
  {
    field: 'status',
    headerName: 'Status',
    flex: 1,
    cellRenderer: StatusCell,
  },
]

const dialog = ref(false)

const start  = ref<string>('')
const end    = ref<string>('')
const reason = ref<string>('')

const required = (v: string) => !!v || 'Required fields'


async function request () {
  await leave.requestLeave({
    start_date: start.value,
    end_date:   end.value,
    reason:     reason.value,
  })

  start.value  = ''
  end.value    = ''
  reason.value = ''

  dialog.value = false

  await leave.fetchApproved()
}

onMounted(() => leave.fetchApproved())
</script>

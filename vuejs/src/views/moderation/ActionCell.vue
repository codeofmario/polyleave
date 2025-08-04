<template>
  <div class="actions">
    <v-btn
      prepend-ico="mdi-check"
      color="success"
      variant="flat"
      class="mr-1"
      @click="handleClick('approved')"
    >Accept</v-btn>
    <v-btn
      prepend-ico="mdi-close"
      color="error"
      variant="flat"
      border
      @click="handleClick('rejected')"
    >Refuse</v-btn>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'
import type { ICellRendererParams } from 'ag-grid-community'
import type { LeaveStatus } from '@/types.ts'

interface RendererParams extends ICellRendererParams {
  decide: (id: number, status: LeaveStatus) => void
}

const props = defineProps<{ params: RendererParams }>()

function handleClick(status: LeaveStatus) {
  const { data, decide } = props.params
  decide(data.id, status)
}
</script>


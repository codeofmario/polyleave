<template>
  <v-chip
    :color="palette[data.status] || 'secondary'"
    class="text-white"
    size="small"
    label
  >
    {{ data.status }}
  </v-chip>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'
import type { ICellRendererParams } from 'ag-grid-community'

const props = defineProps<{ params: ICellRendererParams }>()
const { data } = props.params

const palette: Record<string, string> = {
  approved: 'success',
  pending:  'warning',
  rejected: 'error',
}
</script>


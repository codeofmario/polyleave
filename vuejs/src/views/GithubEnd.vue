<template>Stiamo entrando…</template>
<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
useAuthStore()
  .finishGithubLogin(route.query.code as string, route.query.state as string)
  .finally(() => router.replace('/'));
</script>

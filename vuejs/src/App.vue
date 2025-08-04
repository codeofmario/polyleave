<template>
  <v-layout>
    <nav-bar />

    <v-main app color="primary">
      <router-view />
    </v-main>
  </v-layout>
</template>
<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import NavBar from '@/components/NavBar.vue'

const auth = useAuthStore()
auth.loadTokens()

if (auth.isAuthenticated) {
  auth.fetchMe()
}
</script>

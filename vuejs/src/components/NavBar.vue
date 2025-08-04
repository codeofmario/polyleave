<template>
  <v-app-bar density="comfortable" elevation="2">
    <v-btn :to="isModerator ? '/moderation' : '/'" variant="plain" class="d-flex align-center text-none pr-4">
      <span class="text-h6">PolyLeave</span>
    </v-btn>

    <v-divider vertical class="mx-2" />

    <v-btn v-if="auth.isAuthenticated" :to="isModerator ? '/moderation' : '/'" variant="text" class="text-none" exact> Home </v-btn>

    <v-spacer />

    <v-btn v-if="auth.isAuthenticated" variant="text" class="text-none" @click="signOut">
      Logout
    </v-btn>
  </v-app-bar>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'

const router = useRouter()
const auth = useAuthStore()

const isModerator = computed(() => {
  const roles = auth.user?.roles || []
  return roles.includes('moderator') || roles.includes('moderation')
})

async function signOut() {
  await auth.logout()
  await router.push('/login')
}
</script>

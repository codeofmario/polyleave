<template>
  <v-container fluid class="d-flex align-center justify-center fill-height">
    <v-card class="pa-6"
            elevation="4"
            style="width: 50%; max-width: 560px">
      <v-card-title class="text-h6">Login</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-text-field v-model="email" label="Email" type="email" required />
          <v-text-field v-model="password" label="Password" type="password" required />
          <v-btn type="submit" color="primary" block class="my-4">Entra</v-btn>
        </v-form>
        <v-btn color="secondary" block @click="auth.githubLogin">Login con GitHub</v-btn>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const email = ref('');
const password = ref('');
const auth = useAuthStore();
const router = useRouter();

async function submit() {
  try {
    await auth.login(email.value, password.value);
    await router.push(
      auth.user?.roles.includes('moderator') ? '/moderation' : '/'
    )
  } catch (e) {
    alert('Credenziali errate');
  }
}
</script>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const loading = ref(false);
const router = useRouter();

const form = ref({
  email: '',
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  const success = await authStore.login(form.value);
  loading.value = false;
  
  if (success) {
    router.push('/home');
  }
};

const authErrors = computed(() => authStore.authErrors);
</script>

<template>
  <div class="app-container">
    <div class="login-app">
      <header class="app-header">
        <h1 class="app-title">Welcome Back</h1>
        <p class="app-subtitle">Login to manage your tasks</p>
      </header>

      <div v-if="authErrors.general" class="error-alert" role="alert">
        {{ authErrors.general[0] }}
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <input 
            type="email" 
            v-model="form.email" 
            placeholder="Email address"
            :class="{ 'input-error': authErrors.email }"
          >
          <span class="error-message" v-if="authErrors.email">{{ authErrors.email[0] }}</span>
        </div>
  
        <div class="form-group">
          <input 
            type="password" 
            v-model="form.password"
            placeholder="Password"
            :class="{ 'input-error': authErrors.password }"
          >
          <span class="error-message" v-if="authErrors.password">{{ authErrors.password[0] }}</span>
        </div>
  
        <button type="submit" :disabled="loading" class="login-button">
          <span v-if="!loading">Login</span>
          <span v-else>Logging in...</span>
        </button>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <router-link to="/register" class="link">Sign up</router-link></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  background-color: #f9fafb;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.login-app {
  width: 100%;
  max-width: 420px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  padding: 2.5rem;
}

.app-header {
  margin-bottom: 2rem;
  text-align: center;
}

.app-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
}

.app-subtitle {
  font-size: 1rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.login-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

input {
  width: 92%;
  padding: 0.875rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.2s;
  background-color: #f9fafb;
}

input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  background-color: white;
}

.input-error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.input-error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.error-message {
  display: block;
  margin-top: 0.5rem;
  color: #ef4444;
  font-size: 0.85rem;
}

.error-alert {
  background-color: #fef2f2;
  color: #ef4444;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #fecaca;
  font-size: 0.9rem;
}

.login-button {
  width: 100%;
  padding: 1rem;
  background-color: #6366f1;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

.login-button:hover {
  background-color: #4f46e5;
}

.login-button:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.login-footer {
  text-align: center;
  font-size: 0.9rem;
  color: #6b7280;
  margin-top: 1.5rem;
}

.link {
  color: #6366f1;
  text-decoration: none;
  font-weight: 500;
}

.link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .login-app {
    padding: 1.75rem;
  }
  
  .app-container {
    padding: 1rem;
  }
}
</style>
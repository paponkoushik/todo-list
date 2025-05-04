<script setup>
import { ref,computed } from 'vue';
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
    <div class="login-container">
      <h2>Login Your Account</h2>

      <div v-if="authErrors.general" class="warnign" role="alert">
        {{ authErrors.general[0] }}
      </div>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="form.email" 
            :class="{ 'is-invalid': authErrors.email }"
          >
          <span class="error-message" v-if="authErrors.email">{{ authErrors.email[0] }}</span>
        </div>
  
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="form.password"
            :class="{ 'is-invalid': authErrors.password }"
          >
          <span class="error-message" v-if="authErrors.password">{{ authErrors.password[0] }}</span>
        </div>
  
        <button type="submit" :disabled="loading">
          <span v-if="!loading">Login</span>
          <span v-else>Login...</span>
        </button>
      </form>
    </div>
</template>
  
<style scoped>
  .login-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
  }
  
  input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  input.is-invalid {
    border-color: #ff4444;
  }
  
  .error-message {
    color: #ff4444;
    font-size: 0.8em;
  }
  
  button {
    background-color: #42b983;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
  }
  .warnign {
    background-color: #ff4444;
    color: #fff;
    padding: 10px;
    margin-bottom: 10px;
    width: 100%s;

  }
</style>
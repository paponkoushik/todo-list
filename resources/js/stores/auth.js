import { defineStore } from 'pinia';
import axios from 'axios';
import { useRouter } from 'vue-router';
import api from '../plugins/axios';


export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    errors: {}
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    authErrors: (state) => state.errors
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('/api/login', credentials);
        
        console.log(response);
        
        this.token = response.data.token;
        this.user = response.data.user;
        
        localStorage.setItem('token', this.token);
        
        return true;
      } catch (error) {
        if (error.response?.status === 422) {
            this.errors = { ...error.response.data.errors };
          } else if (error.response?.status === 401) {
            this.errors = { general: [error.response.data.message] };
          } else {
            this.errors = { general: ["An unexpected error occurred"] };
          }
        return false;
      }
    },
    
    async logout() {
      try {
        await api.post('logout');
        this.token = null;
        this.user = null;
        return true;
      } catch (error) {
        console.error('Logout error:', error);
        return false;
      }

    }
  }
});
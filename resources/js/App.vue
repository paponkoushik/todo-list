<template>
    <div id="app">
      <router-view />
    </div>
  </template>
  
  <script setup>
  import { useAuthStore } from './stores/auth';
  
  const authStore = useAuthStore();
  
  const logout = () => {
    authStore.logout();
  };
  </script>
  
  <style>
  nav {
    padding: 20px;
    background: #f0f0f0;
    margin-bottom: 20px;
  }
  
  nav a {
    margin-right: 10px;
  }
  
  button {
    background: none;
    border: none;
    color: #42b983;
    cursor: pointer;
    text-decoration: underline;
  }
  </style>
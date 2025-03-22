<template>
    <nav>
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-between">
          <a :href="route('main.index')" class="p-2 text-lg font-bold bg-white rounded-md">Home</a>
          <a :href="route('manage.task')" class="p-2 ml-3 text-lg font-bold bg-white rounded-md">Create Task</a>
        </div>
        <a @click="logout" class="p-2 text-lg font-bold bg-white rounded-md cursor-pointer">
          Logout
        </a>
      </div>
    </nav>
  </template>
  
  <script lang="js" setup>
    import { ref } from "vue";
    import axios from "axios"; // Use axios for API requests
    import { Inertia } from '@inertiajs/inertia';
    import { route } from 'ziggy-js';

  const logout = async () => {
    try {
      // Make the API request to log out the user
      const response = await axios.post('/api/user/logout', {}, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        }
      });
  
      // If the logout is successful, clear the token from localStorage
      localStorage.removeItem('token');
      
      // Use Inertia to redirect after login
      Inertia.visit(route('login')); 

  
    } catch (error) {
      console.error("Error during logout:", error);
    }
  };
  </script>
  
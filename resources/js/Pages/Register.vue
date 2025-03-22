<template>
    <section class="bg-gray-20">
      <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
        <div class="flex flex-col items-center justify-center min-h-28">
          <h3 class="text-3xl font-bold text-gray-800">Task Management System</h3>
        </div>
        <div class="w-full bg-white rounded-lg shadow border-2 border-gray-200 md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
          <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
              Create an account
            </h1>
            <form @submit.prevent="register" class="space-y-4 md:space-y-6">
            <!-- Name Input -->
              <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  id="name"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder=""
                  required
                />
              </div>
              <!-- Email Input -->
              <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                <input
                  v-model="form.email"
                  type="email"
                  name="email"
                  id="email"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="name@company.com"
                  required
                />
              </div>
  
              <!-- Password Input -->
              <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                <input
                  v-model="form.password"
                  type="password"
                  name="password"
                  id="password"
                  placeholder="••••••••"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  required
                />
              </div>
  
              <!-- Confirm Password Input -->
              <div>
                <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm password</label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  name="confirm-password"
                  id="confirm-password"
                  placeholder="••••••••"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  required
                />
              </div>
  
              <!-- Error Message -->
              <div v-if="error" class="text-red-500 text-sm">
                {{ error }}
              </div>
  
              <!-- Success Message -->
              <div v-if="success" class="text-green-500 text-sm">
                {{ success }}
              </div>
  
              <!-- Submit Button -->
              <button
                type="submit"
                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                Create an account
              </button>
              <div class="justify-center text-center text-blue-500">
                    <a :href="route('login')" class="justify-center text-center text-lg font-bold bg-white rounded-md">login here</a>
                </div>   
            </form>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';
  
  // Reactive state for form data
  const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  
  // Reactive state for error and success messages
  const error = ref('');
  const success = ref('');
  
  // Register function
  const register = async () => {
    error.value = ''; // Clear previous errors
    success.value = ''; // Clear previous success messages
  
    // Validate password confirmation
    if (form.value.password !== form.value.password_confirmation) {
      error.value = 'Passwords do not match.';
      return;
    }
  
    try {
      // Send POST request to the API
      const response = await axios.post('/api/user/register', form.value);
  
      // Handle success
      success.value = 'Registration successful! You can now log in.';
      form.value = {name: '', email: '', password: '', password_confirmation: '' }; // Clear form
      Inertia.visit(route('login')); 

    } catch (err) {
      // Handle errors
      if (err.response && err.response.data.message) {
        error.value = err.response.data.message;
      } else {
        error.value = 'An error occurred during registration. Please try again.';
      }
    }
  };
  </script>
  
  <style scoped>
  /* Add custom styles if needed */
  </style>
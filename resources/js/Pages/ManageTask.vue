<template>
    <div class="flex flex-col h-screen">
      <header class="p-4 bg-gray-200">
        <Navbar />
      </header>
      <div class="flex flex-col gap-4 p-2 bg-white">
        <div class="p-2 mx-10 mt-10 rounded-md">
          <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-semibold text-center mb-6">Task Management</h1>
  
            <!-- Add Task Button -->
            <div class="flex justify-right mb-6">
              <button
                @click="showModal = true"
                class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-lg shadow-md"
              >
                Add New Task
              </button>
            </div>
  
            <!-- Task List -->
            <div class="space-y-4">
              <div
                v-for="task in tasks"
                :key="task.id"
                class="flex items-center justify-between p-4 border rounded-lg shadow-sm bg-white"
              >
                <div class="flex flex-col">
                  <span class="text-lg font-semibold">{{ task.title }}</span>
                  <span class="text-sm text-gray-600">{{ task.description }}</span>
                </div>
                <div class="flex items-center space-x-4">
                  <span
                    :class="`px-3 py-1 rounded-lg ${priorityClasses[task.priority]}`"
                    class="text-white text-xs font-semibold"
                  >
                    {{ task.priority.charAt(0).toUpperCase() + task.priority.slice(1) }}
                  </span>
                  <span
                    :class="{
                      'text-green-500': task.status === 'completed',
                      'text-yellow-500': task.status === 'pending',
                    }"
                    class="text-sm font-semibold"
                  >
                    {{ task.status.charAt(0).toUpperCase() + task.status.slice(1) }}
                  </span>
                  <div class="space-x-2">
                    <button
                      @click="editTask(task)"
                      class="px-3 py-1 text-black border-2 border-yellow-500 rounded-lg"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteTask(task.id)"
                      class="px-3 py-1 text-black border-2 border-red-500 rounded-lg"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Laravel-like Pagination Links -->
            <div class="mt-4 flex justify-center items-center space-x-2">
              <button
                v-if="pagination.prev_page_url"
                @click="fetchTasks(pagination.prev_page_url)"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
              >
                Previous
              </button>
  
              <!-- Page Numbers -->
              <template v-for="page in pagination.last_page" :key="page">
                <button
                  @click="goToPage(page)"
                  :class="{
                    'px-4 py-2 bg-blue-500 text-white rounded': pagination.current_page === page,
                    'px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded': pagination.current_page !== page,
                  }"
                >
                  {{ page }}
                </button>
              </template>
  
              <button
                v-if="pagination.next_page_url"
                @click="fetchTasks(pagination.next_page_url)"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
              >
                Next
              </button>
            </div>
  
            <!-- Add/Edit Task Modal -->
            <div
              v-if="showModal"
              class="fixed inset-0 flex justify-center items-center bg-gray-30 bg-opacity-10"
            >
              <div class="bg-white p-6 rounded-lg w-full max-w-md border">
                <h2 class="text-2xl font-semibold mb-4">{{ isEditMode ? 'Edit Task' : 'Add Task' }}</h2>
  
                <form @submit.prevent="isEditMode ? updateTask() : createTask()">
                  <div class="mb-4">
                    <label for="title" class="block text-sm font-semibold text-gray-700">Title</label>
                    <input
                      v-model="currentTask.title"
                      id="title"
                      type="text"
                      required
                      class="w-full px-4 py-2 border rounded-lg mt-2"
                      placeholder="Task title"
                    />
                  </div>
                  <div class="mb-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                    <textarea
                      v-model="currentTask.description"
                      id="description"
                      rows="3"
                      required
                      class="w-full px-4 py-2 border rounded-lg mt-2"
                      placeholder="Task description"
                    ></textarea>
                  </div>
                  <div class="mb-4">
                    <label for="priority" class="block text-sm font-semibold text-gray-700">Priority</label>
                    <select
                      v-model="currentTask.priority"
                      id="priority"
                      required
                      class="w-full px-4 py-2 border rounded-lg mt-2"
                    >
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </div>
                  <div class="mb-4">
                    <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                    <select
                      v-model="currentTask.status"
                      id="status"
                      required
                      class="w-full px-4 py-2 border rounded-lg mt-2"
                    >
                      <option value="pending">Pending</option>
                      <option value="completed">Completed</option>
                    </select>
                  </div>
                  <div class="flex justify-end">
                    <button
                      type="button"
                      @click="closeModal"
                      class="px-4 py-2 text-white bg-gray-500 hover:bg-gray-600 rounded-lg mr-2"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-lg"
                    >
                      {{ isEditMode ? 'Update Task' : 'Add Task' }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import Navbar from '../layouts/Navbar.vue';
  import { Inertia } from '@inertiajs/inertia';
  import { route } from 'ziggy-js';
  
  // Task state
  const tasks = ref([]);  // Start with an empty array
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    next_page_url: null,
    prev_page_url: null,
  });
  
  // Modal and task state
  const showModal = ref(false);
  const currentTask = ref({
    id: null,
    title: '',
    description: '',
    priority: 'low',
    status: 'pending',
  });
  const isEditMode = ref(false);
  const token = localStorage.getItem('token');
  
  // Priority color mapping
  const priorityClasses = {
    low: 'bg-green-500',
    medium: 'bg-yellow-500',
    high: 'bg-red-500',
  };
  
  // Fetch tasks when the component is mounted
  onMounted(async () => {
    if (!token) {
        Inertia.visit(route('login')); 
    } else {
        await fetchTasks();
    }
    
  });
  
  // Fetch tasks from API
  const fetchTasks = async (url = '/api/tasks/tasks') => {
    try {
      const response = await axios.get(url, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      tasks.value = response.data.data;  // Populate the tasks array with fetched data
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        next_page_url: response.data.next_page_url,
        prev_page_url: response.data.prev_page_url,
      };
    } catch (error) {
      console.error('Error fetching tasks:', error);
    }
  };
  
  // Go to a specific page
  const goToPage = (page) => {
    const url = `/api/tasks/tasks?page=${page}`;
    fetchTasks(url);
  };
  
  // Add Task
  const createTask = async () => {
    try {
      const newTask = { ...currentTask.value };
      const response = await axios.post('/api/tasks/tasks', newTask, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      tasks.value.push(response.data);  // Add the newly created task to the tasks list
      closeModal();
    } catch (error) {
      console.error('Error creating task:', error);
    }
  };
  
  // Edit Task
  const editTask = (task) => {
    currentTask.value = { ...task };
    isEditMode.value = true;
    showModal.value = true;
  };
  
  // Update Task
  const updateTask = async () => {
    try {
      const updatedTask = { ...currentTask.value };
      const response = await axios.put(`/api/tasks/tasks/${currentTask.value.id}`, updatedTask, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      });
      const index = tasks.value.findIndex((task) => task.id === currentTask.value.id);
      if (index !== -1) {
        tasks.value[index] = response.data;  // Update the task in the tasks list
      }
      closeModal();
    } catch (error) {
      console.error('Error updating task:', error);
    }
  };
  
  // Delete Task
  const deleteTask = async (id) => {
    const isConfirmed = window.confirm('Are you sure you want to delete this task?');
    if (isConfirmed) {
      try {
        await axios.delete(`/api/tasks/tasks/${id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        tasks.value = tasks.value.filter((task) => task.id !== id);
      } catch (error) {
        console.error('Error deleting task:', error);
      }
    }
  };
  
  // Close Modal
  const closeModal = () => {
    showModal.value = false;
    currentTask.value = { id: null, title: '', description: '', priority: 'low', status: 'pending' };
    isEditMode.value = false;
  };
  </script>
  
  <style scoped>
  /* You can add additional styles here if needed */
  </style>
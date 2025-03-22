<template>
    <div class="flex flex-col h-screen">
      <header class="p-4 bg-gray-200">
        <Navbar />
      </header>
      <div class="flex flex-col gap-4 p-2 bg-white">
        <div class="p-2 mx-10 mt-10 bg-gray-200 rounded-md">
          <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-semibold text-center mb-6">Task List</h1>
  
            <!-- Priority Filter -->
            <div class="mb-6 flex justify-center space-x-4">
              <button
                class="px-4 py-2 rounded-lg border bg-green-100 hover:bg-green-200"
                @click="filterTasks('low')"
              >
                Low Priority
              </button>
              <button
                class="px-4 py-2 rounded-lg border bg-yellow-100 hover:bg-yellow-200"
                @click="filterTasks('medium')"
              >
                Medium Priority
              </button>
              <button
                class="px-4 py-2 rounded-lg border bg-red-100 hover:bg-red-200"
                @click="filterTasks('high')"
              >
                High Priority
              </button>
              <button
                class="px-4 py-2 rounded-lg border bg-gray-100 hover:bg-gray-200"
                @click="clearFilter"
              >
                All Tasks
              </button>
            </div>
  
            <!-- Task List with Draggable -->
            <draggable
              v-model="filteredTasks"
              item-key="id"
              :group="'tasks'"
              class="space-y-4"
              :move="onMove"
            >
              <template #item="{ element }">
                <div
                  :key="element.id"
                  class="flex items-center justify-between p-4 border rounded-lg shadow-sm bg-white"
                >
                  <div class="flex items-center">
                    <span
                      :class="`w-3 h-3 rounded-full mr-3 ${priorityClasses[element.priority]}`"
                    ></span>
                    <span class="text-gray-800">
                      {{ element.title }}
                    </span>
                  </div>
                  <div>
                    <button
                      @click="toggleCompletion(element)"
                      :class="{
                        'bg-gray-500': element.status === 'completed',
                        'bg-blue-500': element.status !== 'completed',
                        'cursor-not-allowed': element.status === 'completed',
                      }"
                      :disabled="element.status === 'completed'"
                      class="px-3 py-1 text-white rounded-lg"
                    >
                      {{ element.status === 'completed' ? 'Completed' : 'Mark as Complete' }}
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
  
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
          </div>
        </div>
      </div>
    </div>
</template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';
  import Navbar from "../layouts/Navbar.vue";
  import draggable from 'vuedraggable';
  import { Inertia } from '@inertiajs/inertia';
  import { route } from 'ziggy-js';
  
  const tasks = ref([]); // Will hold the fetched task data
  const token = localStorage.getItem('token');
  const currentFilter = ref('');
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    next_page_url: null,
    prev_page_url: null,
  });
  
  // Fetch tasks from the API
  const fetchTasks = async (url = '/api/tasks/tasks') => {
    try {
      const response = await axios.get(url, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
        params: {
          priority: currentFilter.value, // Include priority filter in the request
        },
      });
      tasks.value = response.data.data; // Access the correct data property
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

  // Check if token exists on component mount
onMounted(() => {
  if (!token) {
    Inertia.visit(route('login')); 
  } else {
    fetchTasks();
  }
});
  // Filter tasks based on the selected priority
  const filteredTasks = computed(() => {
    if (!currentFilter.value) return tasks.value;
    return tasks.value.filter(task => task.priority === currentFilter.value);
  });
  
  // Toggle task completion
  const toggleCompletion = async (task) => {
    const confirmed = window.confirm("Are you sure you want to mark this task as completed?");
    if (!confirmed) return;
  
    try {
      const updatedTask = { ...task, status: 'completed' };
      await axios.put(`/api/tasks/tasks/${task.id}`, updatedTask, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
  
      // Directly update the status in the tasks array
      const taskIndex = tasks.value.findIndex(t => t.id === task.id);
      if (taskIndex !== -1) {
        tasks.value[taskIndex].status = 'completed';
      }
    } catch (error) {
      console.error('Error updating task completion:', error);
    }
  };
  
  // Clear the filter and show all tasks
  const clearFilter = async () => {
    currentFilter.value = '';
    await fetchTasks(); // Refetch tasks without any priority filter
  };
  
  // Filter tasks based on priority
  const filterTasks = async (priority) => {
    currentFilter.value = priority;
    await fetchTasks(); // Refetch tasks with the selected priority filter
  };
  
  // Go to a specific page
  const goToPage = (page) => {
    const url = `/api/tasks/tasks?page=${page}`;
    fetchTasks(url);
  };
  
  // Priority color classes
  const priorityClasses = {
    low: 'bg-green-500',
    medium: 'bg-yellow-500',
    high: 'bg-red-500',
  };
  </script>
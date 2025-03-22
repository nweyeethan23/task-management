<template>
    <div class="font-[sans-serif]">
        <div class="flex flex-col items-center justify-center min-h-48">
            <h3 class="text-3xl font-bold text-gray-800">Task Management System</h3>
        </div>
        <div class="flex flex-col items-center justify-center px-4 py-6">
            <div class="grid items-center w-full max-w-6xl gap-6 md:grid-cols-2">
                <div class="border border-gray-300 rounded-lg p-6 max-w-md shadow-[0_2px_22px_-4px_rgba(93,96,127,0.2)] max-md:mx-auto">
                    <form @submit.prevent="loginUser" class="pt-5 pb-5 space-y-4">
                        <div class="mb-8">
                            <h3 class="text-3xl font-bold text-gray-800">Login</h3>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm text-gray-800">Email Address</label>
                            <div class="relative flex items-center">
                                <input v-model="form.mail_address" name="mail_address" type="email" required class="w-full py-3 pl-4 pr-10 text-sm text-gray-800 border border-gray-300 rounded-lg outline-blue-600" placeholder="Enter email address" />
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm text-gray-800">Password</label>
                            <div class="relative flex items-center">
                                <input v-model="form.password" name="password" :type="showPassword ? 'text' : 'password'" required class="w-full py-3 pl-4 pr-10 text-sm text-gray-800 border border-gray-300 rounded-lg outline-blue-600" placeholder="Enter password" />
                                <svg @click="showPassword = !showPassword" xmlns="http://www.w3.org/2000/svg" fill="#bbb" stroke="#bbb" class="w-[18px] h-[18px] absolute right-4 cursor-pointer" viewBox="0 0 128 128">
                                    <path v-if="!showPassword" d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" data-original="#000000"></path>
                                    <path v-else d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" data-original="#000000"></path>
                                </svg>
                            </div>
                        </div>

                        <div v-if="error || Object.keys(errors).length > 0" class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                            <span v-if="error" class="block sm:inline">{{ error }}</span>
                            <span v-for="(errorArray, key) in errors" :key="key" class="block sm:inline">{{ errorArray[0] }}</span>
                        </div>

                        <div class="!mt-8">
                            <button type="submit" class="w-full shadow-xl py-2.5 px-4 text-sm tracking-wide rounded-lg text-white bg-gray-600 hover:bg-blue-700 focus:outline-none" :disabled="form.processing">
                                {{ form.processing ? "Logging in..." : "Login" }}
                            </button>
                        </div>
                        <div class="justify-center text-center text-blue-500">
                            <a :href="route('register')" class="justify-center text-center text-lg font-bold bg-white rounded-md">Register</a>
                        </div>    
                    </form>
                </div>
                <div class="max-md:mt-8">
                    <img src="https://readymadeui.com/login-image.webp" class="w-full aspect-[71/50] max-md:w-4/5 mx-auto block object-cover" alt="Dining Experience" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios"; // Use axios for API requests
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';

const showPassword = ref(false);

const props = defineProps({
    error: String,
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = ref({
    mail_address: "",
    password: "",
    processing: false
});
const url = route('main.index');
const error = ref(""); // To handle error message

// Login function
const loginUser = async () => {
    form.value.processing = true;
    error.value = ""; // Reset error before making the request
    try {
        const response = await axios.post(
            "/api/user/login", 
            new URLSearchParams({
                email: form.value.mail_address,
                password: form.value.password,
            })
        );

        form.value.processing = false;

        localStorage.setItem('token', response.data.token);
            
        // Use Inertia to redirect after login
        Inertia.visit(route('main.index')); 

    } catch (err) {
        form.value.processing = false;
        if (err.response && err.response.data) {
            error.value = err.response.data.message || "Login failed. Please try again.";
        } else {
            error.value = "An error occurred. Please try again later.";
        }
    }
};
</script>

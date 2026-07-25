import axios from 'axios';
import { getAccessToken } from './storage'; // Adjust path as necessary

// Base URL configuration
// const API_URL = 'http://localhost/Disposable-Bazar/api/';
const API_URL = 'https://ecommerce-inventory.thegallerygen.com/api/';


// Public API instance
const publicApi = axios.create({
    baseURL: API_URL,
    headers: {
        'Accept': 'application/json',
    },
});

// Protected API instance
const protectedApi = axios.create({
    baseURL: API_URL,
    headers: {
        'Accept': 'application/json',
    },
});

// Add request interceptor to protected API to include the authorization token
protectedApi.interceptors.request.use(
    async (config) => {
        const token = await getAccessToken();
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Optionally add interceptors for responses if needed
protectedApi.interceptors.response.use(
    (response) => {
        console.log('Response received with ', response);
        return response;
    },
    (error) => {
        console.error('Response error:', error);
        return Promise.reject(error);
    }
);

// Function to get CSRF token from cookies
const getCsrfToken = () => {
    // Adjust the name if necessary
    const name = 'XSRF-TOKEN=';
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) === ' ') cookie = cookie.substring(1);
        if (cookie.indexOf(name) === 0) return cookie.substring(name.length, cookie.length);
    }
    return '';
};

// Set CSRF token in public API headers
publicApi.interceptors.request.use(
    (config) => {
        const csrfToken = getCsrfToken();
        if (csrfToken) {
            config.headers['X-XSRF-TOKEN'] = csrfToken;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default {
    public: publicApi,
    protected: protectedApi,
};

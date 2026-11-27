import axios from 'axios';

// Base URL mengarah ke API Laravel
const API_BASE_URL = `${window.location.origin}/api`;
const API_KEY = import.meta.env.VITE_API_KEY || 'cHJvZHVjdF9hcHBzX2FwaV9yb3V0ZXJfMjAyNg==';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-API-Key': API_KEY, // Header wajib untuk middleware api.router.key Laravel
  },
});

// Interceptor untuk menyisipkan Bearer token otomatis dari localStorage
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('driver_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  // Jika payload adalah FormData (untuk upload foto/dokumen), biarkan browser set boundary
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type'];
  } else {
    config.headers['Content-Type'] = 'application/json';
  }

  return config;
});

// Interceptor tangani jika token expired / unauthorized
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Hanya redirect jika 401 dan bukan dari endpoint /login itu sendiri
    if (
      error.response &&
      error.response.status === 401 &&
      !error.config?.url?.includes('/login')
    ) {
      localStorage.removeItem('driver_token');
      localStorage.removeItem('driver_user');
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);

export default api;

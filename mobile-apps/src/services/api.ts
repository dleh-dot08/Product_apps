import axios from 'axios';
import { getStorageItemAsync } from '../context/AuthContext';

const API_BASE_URL = 'https://driverapp.aqpa-indonesia.com/api';

const API_KEY = 'cHJvZHVjdF9hcHBzX2FwaV9yb3V0ZXJfMjAyNg==';

const api = axios.create({
  baseURL: API_BASE_URL,

  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',

    // Router API Key
    'X-API-Key': API_KEY,
  },
});

// Sisipkan Sanctum token pada setiap request setelah login
api.interceptors.request.use(
  async (config) => {
    try {
      const token = await getStorageItemAsync('userToken');

      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }

      return config;
    } catch (error) {
      console.error('Error fetching token for interceptor', error);

      return config;
    }
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;
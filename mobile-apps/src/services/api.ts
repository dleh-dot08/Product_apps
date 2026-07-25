import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

// Gunakan 10.0.2.2 untuk mengakses localhost PC dari Emulator Android.
// Gunakan localhost untuk iOS/Web.
const getBaseUrl = () => {
  if (Platform.OS === 'android') {
    return 'http://10.0.2.2:8000/api';
  }
  return 'http://localhost:8000/api';
};

const api = axios.create({
  baseURL: getBaseUrl(),
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

import { getStorageItemAsync } from '../context/AuthContext';

// Interceptor untuk menyisipkan Token di setiap Request
api.interceptors.request.use(async (config) => {
  try {
    const token = await getStorageItemAsync('userToken');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  } catch (error) {
    console.error('Error fetching token for interceptor', error);
  }
  return config;
});

export default api;

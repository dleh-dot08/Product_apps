import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

export const setStorageItemAsync = async (key: string, value: string) => {
  if (Platform.OS === 'web') {
    try {
      localStorage.setItem(key, value);
    } catch (error) {
      console.error('Local storage is unavailable:', error);
    }

    return;
  }

  await SecureStore.setItemAsync(key, value);
};

export const getStorageItemAsync = async (key: string) => {
  if (Platform.OS === 'web') {
    try {
      return localStorage.getItem(key);
    } catch (error) {
      console.error('Local storage is unavailable:', error);
      return null;
    }
  }

  return SecureStore.getItemAsync(key);
};

export const deleteStorageItemAsync = async (key: string) => {
  if (Platform.OS === 'web') {
    try {
      localStorage.removeItem(key);
    } catch (error) {
      console.error('Local storage is unavailable:', error);
    }

    return;
  }

  await SecureStore.deleteItemAsync(key);
};

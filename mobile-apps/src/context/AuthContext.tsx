import React, { createContext, useContext, useState, useEffect } from 'react';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import api from '../services/api';

type User = {
  id: number;
  name: string;
  email: string;
  role?: { id: number; name: string };
  division?: { id: number; name: string };
};

type AuthContextType = {
  user: User | null;
  isLoading: boolean;
  login: (email: string, password: string, device_name: string) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

// Helper function untuk SecureStore yang aman di Web
export const setStorageItemAsync = async (key: string, value: string) => {
  if (Platform.OS === 'web') {
    try {
      localStorage.setItem(key, value);
    } catch (e) {
      console.error('Local storage is unavailable:', e);
    }
  } else {
    await SecureStore.setItemAsync(key, value);
  }
};

export const getStorageItemAsync = async (key: string) => {
  if (Platform.OS === 'web') {
    try {
      return localStorage.getItem(key);
    } catch (e) {
      console.error('Local storage is unavailable:', e);
      return null;
    }
  }
  return await SecureStore.getItemAsync(key);
};

export const deleteStorageItemAsync = async (key: string) => {
  if (Platform.OS === 'web') {
    try {
      localStorage.removeItem(key);
    } catch (e) {
      console.error('Local storage is unavailable:', e);
    }
  } else {
    await SecureStore.deleteItemAsync(key);
  }
};

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    // Cek apakah ada token saat aplikasi pertama kali dibuka
    const bootstrapAsync = async () => {
      try {
        const userToken = await getStorageItemAsync('userToken');
        if (userToken) {
          // Verify token ke server
          const response = await api.get('/user');
          setUser(response.data);
        }
      } catch (e) {
        // Token tidak valid atau expired
        await deleteStorageItemAsync('userToken');
      } finally {
        setIsLoading(false);
      }
    };

    bootstrapAsync();
  }, []);

  const login = async (email: string, password: string, device_name: string) => {
    try {
      const response = await api.post('/login', { email, password, device_name });
      const { token, user: userData } = response.data;
      
      await setStorageItemAsync('userToken', token);
      setUser(userData);
    } catch (error: any) {
      console.error("Login failed", error.response?.data || error.message);
      throw error;
    }
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error("Logout request failed, but clearing local session.");
    } finally {
      await deleteStorageItemAsync('userToken');
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider value={{ user, isLoading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

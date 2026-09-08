import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';
import { getStorageItemAsync } from '../context/AuthContext';
import AsyncStorage from '@react-native-async-storage/async-storage';
import api from './api';
import axios from 'axios';

const LOCATION_TASK_NAME = 'background-location-task';

// Definisikan background task
TaskManager.defineTask(LOCATION_TASK_NAME, async ({ data, error }) => {
  if (error) {
    console.error('Background location error:', error);
    return;
  }
  if (data) {
    const { locations } = data as { locations: Location.LocationObject[] };
    const location = locations[0];
    
    if (location) {
      try {
        const taskId = await AsyncStorage.getItem('active_task_id');
        const token = await getStorageItemAsync('userToken');
        
        // Base URL from axios defaults used in app
        const baseURL = api.defaults.baseURL || 'https://api.aqpa-indonesia.com/api';
        
        // Hanya kirim ke server jika ada task yang aktif dan ada token
        if (taskId && token) {
          await axios.post(`${baseURL}/driver/location`, {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
            heading: location.coords.heading,
            task_id: taskId
          }, {
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': `Bearer ${token}`
            }
          });
          console.log(`[LocationService] Lokasi terkirim untuk task ${taskId}`);
        }
      } catch (e) {
        console.error('Error sending background location:', e);
      }
    }
  }
});

export const requestLocationPermissions = async () => {
  const { status: foregroundStatus } = await Location.requestForegroundPermissionsAsync();
  if (foregroundStatus !== 'granted') {
    return false;
  }
  
  const { status: backgroundStatus } = await Location.requestBackgroundPermissionsAsync();
  if (backgroundStatus !== 'granted') {
    return false;
  }
  
  return true;
};

export let _foregroundInterval: ReturnType<typeof setInterval> | null = null;

const sendCurrentLocation = async (taskId: string) => {
  try {
    const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
    await api.post('/driver/location', {
      latitude: loc.coords.latitude,
      longitude: loc.coords.longitude,
      heading: loc.coords.heading,
      task_id: taskId,
    });
    console.log(`[LocationService] Foreground location sent for task ${taskId}`);
  } catch (e) {
    console.warn('[LocationService] Foreground send error:', e);
  }
};

export const startLocationTracking = async (taskId: string) => {
  try {
    const { status: foregroundStatus } = await Location.requestForegroundPermissionsAsync();
    if (foregroundStatus !== 'granted') {
      console.warn('[LocationService] Foreground permission denied, skipping tracking.');
      return;
    }

    // Simpan taskId agar background task tahu task mana yang sedang aktif
    await AsyncStorage.setItem('active_task_id', taskId);

    // Kirim lokasi saat ini segera (agar Find Driver langsung muncul)
    await sendCurrentLocation(taskId);

    // Background permission - bisa gagal di Expo Go iOS, jadi jangan block
    let backgroundGranted = false;
    try {
      const { status: backgroundStatus } = await Location.requestBackgroundPermissionsAsync();
      backgroundGranted = backgroundStatus === 'granted';
    } catch (e) {
      console.warn('[LocationService] Background permission not available (Expo Go?):', e);
    }

    if (backgroundGranted) {
      const isRegistered = await TaskManager.isTaskRegisteredAsync(LOCATION_TASK_NAME);
      if (!isRegistered) {
        await Location.startLocationUpdatesAsync(LOCATION_TASK_NAME, {
          accuracy: Location.Accuracy.Balanced,
          timeInterval: 10000,
          distanceInterval: 10,
          showsBackgroundLocationIndicator: true,
          foregroundService: {
            notificationTitle: "Memantau Lokasi",
            notificationBody: "Aplikasi sedang melacak lokasi untuk tugas pengiriman.",
          }
        });
        console.log('[LocationService] Background tracking started.');
      }
    } else {
      // Fallback: kirim lokasi via foreground interval setiap 15 detik
      console.warn('[LocationService] Background not available, using foreground interval.');
      if (_foregroundInterval) clearInterval(_foregroundInterval);
      _foregroundInterval = setInterval(() => sendCurrentLocation(taskId), 15000);
    }
  } catch (e) {
    console.error('[LocationService] startLocationTracking error (non-blocking):', e);
  }
};

export const stopLocationTracking = async () => {
  // Clear foreground interval fallback
  if (_foregroundInterval) {
    clearInterval(_foregroundInterval);
    _foregroundInterval = null;
  }
  await AsyncStorage.removeItem('active_task_id');
  const isRegistered = await TaskManager.isTaskRegisteredAsync(LOCATION_TASK_NAME);
  if (isRegistered) {
    await Location.stopLocationUpdatesAsync(LOCATION_TASK_NAME);
    console.log('[LocationService] Background tracking stopped.');
  }
};

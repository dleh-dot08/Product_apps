import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';

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
        const token = await AsyncStorage.getItem('token');
        let baseURL = await AsyncStorage.getItem('base_url') || 'http://192.168.1.10:8001/api';
        
        // Hanya kirim ke server jika ada task yang aktif dan ada token
        if (taskId && token) {
          await fetch(`${baseURL}/driver/location`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
              latitude: location.coords.latitude,
              longitude: location.coords.longitude,
              heading: location.coords.heading,
              task_id: taskId
            })
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

export const startLocationTracking = async (taskId: string) => {
  const hasPermissions = await requestLocationPermissions();
  if (!hasPermissions) {
    console.error('Permission for location denied');
    return;
  }

  // Simpan taskId agar background task tahu task mana yang sedang aktif
  await AsyncStorage.setItem('active_task_id', taskId);

  const isRegistered = await TaskManager.isTaskRegisteredAsync(LOCATION_TASK_NAME);
  if (!isRegistered) {
    await Location.startLocationUpdatesAsync(LOCATION_TASK_NAME, {
      accuracy: Location.Accuracy.Balanced,
      timeInterval: 10000, // Update setiap 10 detik
      distanceInterval: 10, // Atau update setiap pindah 10 meter
      showsBackgroundLocationIndicator: true,
      foregroundService: {
        notificationTitle: "Memantau Lokasi",
        notificationBody: "Aplikasi sedang melacak lokasi untuk tugas pengiriman.",
      }
    });
    console.log('[LocationService] Background tracking started.');
  }
};

export const stopLocationTracking = async () => {
  await AsyncStorage.removeItem('active_task_id');
  const isRegistered = await TaskManager.isTaskRegisteredAsync(LOCATION_TASK_NAME);
  if (isRegistered) {
    await Location.stopLocationUpdatesAsync(LOCATION_TASK_NAME);
    console.log('[LocationService] Background tracking stopped.');
  }
};

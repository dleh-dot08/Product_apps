// Layanan untuk mengambil koordinat GPS saat ini (Milestone Checkpoint)
export interface Coordinates {
  latitude: number;
  longitude: number;
  accuracy?: number;
}

export function getCurrentCoordinates(): Promise<Coordinates> {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation tidak didukung oleh browser ini'));
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        resolve({
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
          accuracy: position.coords.accuracy,
        });
      },
      (error) => {
        reject(error);
      },
      {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0,
      }
    );
  });
}

// Buka navigasi suara Google Maps langsung di aplikasi HP driver
export function openExternalGoogleMapsNavigation(lat: number, lng: number) {
  const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
  window.open(url, '_blank');
}

// Fitur Screen Wake Lock agar layar HP tidak mati saat driver meletakkan HP di holder
let wakeLock: any = null;

export async function requestScreenWakeLock() {
  try {
    if ('wakeLock' in navigator) {
      wakeLock = await (navigator as any).wakeLock.request('screen');
      console.log('Screen Wake Lock aktif: Layar HP akan tetap menyala');
    }
  } catch (err) {
    console.warn('Wake Lock error:', err);
  }
}

export function releaseScreenWakeLock() {
  if (wakeLock) {
    wakeLock.release();
    wakeLock = null;
    console.log('Screen Wake Lock dilepas');
  }
}

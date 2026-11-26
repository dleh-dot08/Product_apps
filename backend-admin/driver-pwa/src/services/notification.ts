import api from './api';

export async function requestNotificationPermission() {
  if (!('Notification' in window) || !('serviceWorker' in navigator)) {
    console.warn('Browser tidak mendukung Web Push Notification');
    return null;
  }

  const permission = await Notification.requestPermission();
  if (permission !== 'granted') {
    console.warn('Izin notifikasi ditolak oleh pengguna');
    return null;
  }

  try {
    const registration = await navigator.serviceWorker.ready;
    // Cek subscription yang sudah ada atau buat baru
    let subscription = await registration.pushManager.getSubscription();
    
    // Note: Jika menggunakan VAPID Key, pasang applicationServerKey di sini
    if (!subscription) {
      console.log('Push manager ready, siap menerima push subscription');
    }

    return subscription;
  } catch (err) {
    console.error('Gagal inisialisasi push notification:', err);
    return null;
  }
}

export async function registerPushTokenToBackend(pushToken: string) {
  try {
    await api.post('/driver/push-token', { token: pushToken });
    console.log('Push token berhasil dikirim ke backend Laravel');
  } catch (error) {
    console.error('Gagal mengirim push token ke backend:', error);
  }
}

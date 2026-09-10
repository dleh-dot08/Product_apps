// @ts-nocheck
import * as Application from 'expo-application';
import * as FileSystem from 'expo-file-system/legacy';
import * as IntentLauncher from 'expo-intent-launcher';
import { Alert } from 'react-native';

export type ProgressCallback = (progress: number | null) => void;

interface VersionResponse {
     latest_version: string;
     apk_url: string;
     changelog?: string;
     force_update?: boolean;
}

// URL endpoint API Laravel
const VERSION_CHECK_URL = 'https://driverapp.aqpa-indonesia.com/api/app-version';

/**
 * Memeriksa versi aplikasi ke server Laravel
 */
export async function checkAppUpdate(onProgress?: ProgressCallback): Promise<void> {
     try {
          const response = await fetch(VERSION_CHECK_URL);
          if (!response.ok) {
               return;
          }

          const data: VersionResponse = await response.json();
          const currentVersion = Application.nativeApplicationVersion;

          if (data.latest_version && data.latest_version !== currentVersion) {
               Alert.alert(
                    'Pembaruan Tersedia',
                    `Versi ${data.latest_version} telah tersedia.\n\nCatatan:\n${data.changelog || 'Pembaruan fitur & perbaikan bug.'}\n\nApakah Anda ingin memperbarui sekarang?`,
                    [
                         { text: 'Nanti', style: 'cancel' },
                         {
                              text: 'Perbarui',
                              onPress: () => {
                                   downloadAndInstall(data.apk_url, data.latest_version, onProgress);
                              },
                         },
                    ]
               );
          }
     } catch (error: any) {
          console.log('[Updater] Cek update dilewati:', error?.message);
     }
}

/**
 * Mengunduh APK lalu membuka installer bawaan Android
 */
async function downloadAndInstall(apkUrl: string, version: string, onProgress?: ProgressCallback): Promise<void> {
     // Menambahkan versi ke nama file agar tidak bentrok dengan sisa file unduhan lama yang mungkin gagal
     const targetPath = `${FileSystem.cacheDirectory}update_${version}.apk`;

     try {
          // Hapus file lama jika sudah ada untuk memastikan kita mengunduh file yang fresh
          const fileInfo = await FileSystem.getInfoAsync(targetPath);
          if (fileInfo.exists) {
               await FileSystem.deleteAsync(targetPath);
          }
          const downloadResumable = FileSystem.createDownloadResumable(
               apkUrl,
               targetPath,
               {},
               (downloadProgress) => {
                    const total = downloadProgress.totalBytesExpectedToWrite;
                    const current = downloadProgress.totalBytesWritten;
                    if (total > 0 && onProgress) {
                         const percent = Math.round((current / total) * 100);
                         onProgress(percent);
                    }
               }
          );

          const result = await downloadResumable.downloadAsync();

          if (!result || !result.uri) {
               throw new Error('Gagal mengunduh berkas instalasi APK.');
          }

          // Mengubah file lokal menjadi Content URI sistem Android
          const contentUri = await FileSystem.getContentUriAsync(result.uri);

          // Membuka jendela dialog install bawaan Android
          await IntentLauncher.startActivityAsync('android.intent.action.VIEW', {
               data: contentUri,
               flags: 1, // FLAG_GRANT_READ_URI_PERMISSION
               type: 'application/vnd.android.package-archive',
          });
     } catch (error: any) {
          Alert.alert('Gagal Memperbarui', error?.message || 'Terjadi kesalahan saat mengunduh berkas.');
     } finally {
          if (onProgress) {
               onProgress(null);
          }
     }
}
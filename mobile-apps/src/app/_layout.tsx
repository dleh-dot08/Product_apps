import { DarkTheme, DefaultTheme, ThemeProvider } from '@react-navigation/native';
import * as SplashScreen from 'expo-splash-screen';
import { AuthProvider, useAuth } from '../context/AuthContext';
import { CustomThemeProvider, useTheme } from '../context/ThemeContext';
import {
  useFonts,
  Inter_400Regular,
  Inter_500Medium,
  Inter_600SemiBold,
  Inter_700Bold,
  Inter_800ExtraBold,
  Inter_900Black,
} from '@expo-google-fonts/inter';
import { Stack, useRouter, useSegments } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { View, Text, ActivityIndicator, Modal, StyleSheet, Alert } from 'react-native';
import { checkAppUpdate } from '../services/updater';
import * as Updates from 'expo-updates';

SplashScreen.preventAutoHideAsync();

function RootLayoutNav() {
  const { user, isLoading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  const [fontsLoaded] = useFonts({
    Inter_400Regular,
    Inter_500Medium,
    Inter_600SemiBold,
    Inter_700Bold,
    Inter_800ExtraBold,
    Inter_900Black,
  });

  useEffect(() => {
    if (isLoading || !fontsLoaded) return;

    SplashScreen.hideAsync();

    const isLoginRoute = segments[0] === 'login';

    if (!user && !isLoginRoute) {
      router.replace('/login');
    } else if (user && isLoginRoute) {
      if ((user as any).role?.name?.toLowerCase() === 'admin') {
        router.replace('/(admin)' as any);
      } else {
        router.replace('/(driver)' as any);
      }
    }
  }, [user, isLoading, segments, fontsLoaded]);

  return <Stack screenOptions={{ headerShown: false }} />;
}

function ThemeApplier() {
  const { theme } = useTheme();
  return (
    <ThemeProvider value={theme === 'dark' ? DarkTheme : DefaultTheme}>
      <AuthProvider>
        <RootLayoutNav />
      </AuthProvider>
    </ThemeProvider>
  );
}

export default function RootLayout() {
  const [downloadProgress, setDownloadProgress] = useState<number | null>(null);
  const [otaStatus, setOtaStatus] = useState<string | null>(null);

  useEffect(() => {
    async function performUpdateCheck() {
      // 1. Coba periksa OTA Update (Hanya jalan di Production/APK hasil build, bukan Expo Go)
      if (!__DEV__) {
        try {
          const update = await Updates.checkForUpdateAsync();
          if (update.isAvailable) {
            setOtaStatus('Menerapkan pembaruan OTA (JS Bundle)...');
            await Updates.fetchUpdateAsync();
            setOtaStatus(null);
            
            Alert.alert(
              "Update Berhasil",
              "Pembaruan sistem telah selesai. Aplikasi akan dimuat ulang untuk menerapkan perubahan.",
              [{ text: "Muat Ulang", onPress: () => Updates.reloadAsync() }]
            );
            return; // Hentikan fungsi di sini agar tidak memanggil checkAppUpdate (APK)
          }
        } catch (error: any) {
          console.log("Error checking OTA:", error.message);
        }
      }

      // 2. Fallback: Jika tidak ada OTA atau terjadi error, periksa update APK utuh
      checkAppUpdate((progress: number | null) => {
        setDownloadProgress(progress);
      });
    }

    performUpdateCheck();
  }, []);

  return (
    <CustomThemeProvider>
      <ThemeApplier />

      <Modal visible={downloadProgress !== null || otaStatus !== null} transparent animationType="fade">
        <View style={styles.modalBackdrop}>
          <View style={styles.modalCard}>
            <ActivityIndicator size="large" color="#208AEF" />
            <Text style={styles.modalTitle}>Mengunduh Pembaruan...</Text>
            {otaStatus ? (
              <Text style={styles.modalSubtitle}>{otaStatus}</Text>
            ) : (
              <Text style={styles.modalSubtitle}>{downloadProgress}% selesai</Text>
            )}
          </View>
        </View>
      </Modal>
    </CustomThemeProvider>
  );
}

const styles = StyleSheet.create({
  modalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalCard: {
    backgroundColor: '#fff',
    padding: 24,
    borderRadius: 12,
    alignItems: 'center',
    width: '80%',
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginTop: 16,
  },
  modalSubtitle: {
    fontSize: 14,
    color: '#666',
    marginTop: 8,
  },
});
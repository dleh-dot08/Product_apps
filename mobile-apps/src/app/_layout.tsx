import { DarkTheme, DefaultTheme, ThemeProvider } from 'expo-router';
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
import { View, Text, ActivityIndicator, Modal, StyleSheet } from 'react-native';
import { checkAppUpdate } from '../services/updater';

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

  useEffect(() => {
    // Memeriksa update APK langsung ke backend Laravel
    checkAppUpdate((progress: number | null) => {
      setDownloadProgress(progress);
    });
  }, []);

  return (
    <CustomThemeProvider>
      <ThemeApplier />

      <Modal visible={downloadProgress !== null} transparent animationType="fade">
        <View style={styles.modalBackdrop}>
          <View style={styles.modalCard}>
            <ActivityIndicator size="large" color="#208AEF" />
            <Text style={styles.modalTitle}>Mengunduh Pembaruan...</Text>
            <Text style={styles.modalSubtitle}>{downloadProgress}% selesai</Text>
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
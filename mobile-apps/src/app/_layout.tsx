import { DarkTheme, DefaultTheme, ThemeProvider } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { AuthProvider, useAuth } from '../context/AuthContext';
import { CustomThemeProvider, useTheme } from '../context/ThemeContext';
import { Slot, useRouter, useSegments } from 'expo-router';
import { useEffect } from 'react';

SplashScreen.preventAutoHideAsync();

function RootLayoutNav() {
  const { user, isLoading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (isLoading) return;
    
    // Sembunyikan splash screen bawaan Expo jika sudah selesai loading
    SplashScreen.hideAsync();

    const inAuthGroup = segments[0] === '(dashboard)';

    if (!user && inAuthGroup) {
      // Redirect ke login jika belum login tapi mencoba akses (dashboard)
      router.replace('/login');
    } else if (user && !inAuthGroup) {
      // Redirect ke dashboard jika sudah login
      router.replace('/(dashboard)');
    }
  }, [user, isLoading, segments]);

  return <Slot />;
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

export default function TabLayout() {
  return (
    <CustomThemeProvider>
      <ThemeApplier />
    </CustomThemeProvider>
  );
}

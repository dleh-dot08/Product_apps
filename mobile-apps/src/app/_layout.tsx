import { DarkTheme, DefaultTheme, ThemeProvider } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { AuthProvider, useAuth } from '../context/AuthContext';
import { CustomThemeProvider, useTheme } from '../context/ThemeContext';
import { useFonts, Inter_400Regular, Inter_500Medium, Inter_600SemiBold, Inter_700Bold, Inter_800ExtraBold, Inter_900Black } from '@expo-google-fonts/inter';
import { Stack, useRouter, useSegments } from 'expo-router';
import { useEffect } from 'react';

SplashScreen.preventAutoHideAsync();

function RootLayoutNav() {
  const { user, isLoading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  const [fontsLoaded, fontError] = useFonts({
    Inter_400Regular,
    Inter_500Medium,
    Inter_600SemiBold,
    Inter_700Bold,
    Inter_800ExtraBold,
    Inter_900Black
  });

  useEffect(() => {
    if (isLoading || !fontsLoaded) return;
    
    // Sembunyikan splash screen bawaan Expo jika sudah selesai loading
    SplashScreen.hideAsync();

    const isLoginRoute = segments[0] === 'login';

    if (!user && !isLoginRoute) {
      // Redirect ke login jika belum login tapi mencoba akses rute lain
      router.replace('/login');
    } else if (user && isLoginRoute) {
      // Redirect ke dashboard masing-masing role
      if (user.role?.name?.toLowerCase() === 'admin') {
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

export default function TabLayout() {
  return (
    <CustomThemeProvider>
      <ThemeApplier />
    </CustomThemeProvider>
  );
}

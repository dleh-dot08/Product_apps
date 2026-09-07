import { ExpoConfig, ConfigContext } from 'expo/config';

export default ({ config }: ConfigContext): ExpoConfig => {
  const projectId = config.extra?.eas?.projectId;

  return {
    ...config,
    name: config.name || 'mobile-apps',
    slug: config.slug || 'driverapps',
    updates: {
      ...config.updates,
      url: projectId ? `https://u.expo.dev/${projectId}` : config.updates?.url,
      checkAutomatically: 'ON_LOAD',
      fallbackToCacheTimeout: 5000,
    },
    plugins: [
      ...(config.plugins || []),
      'expo-background-task',
      'expo-sqlite',
      'expo-sharing',
    ],
    extra: {
      ...config.extra,
      apiBaseUrl: process.env.EXPO_PUBLIC_API_BASE_URL,
      apiRouterKey: process.env.EXPO_PUBLIC_API_ROUTER_KEY,
    },
    ios: {
      ...config.ios,
      bundleIdentifier: 'com.aqpa.driverapp',
    },
  };
};
import type { ExpoConfig } from 'expo/config';

const appJson = require('./app.json').expo;

const projectId = appJson.extra?.eas?.projectId;

const useEasUpdates = process.env.OTA_PROVIDER === 'eas';

const updateUrl =
  useEasUpdates && projectId
    ? `https://u.expo.dev/${projectId}`
    : 'https://driverapp.aqpa-indonesia.com/updates';

const config: ExpoConfig = {
  ...appJson,

  runtimeVersion: '1.0.0',

  updates: {
    ...(appJson.updates ?? {}),
    url: updateUrl,
  },

  plugins: [
    ...(appJson.plugins ?? []),
    'expo-background-task',
    'expo-sqlite',
    'expo-sharing',
  ],

  extra: {
    ...(appJson.extra ?? {}),
    apiBaseUrl: process.env.EXPO_PUBLIC_API_BASE_URL,
    apiRouterKey: process.env.EXPO_PUBLIC_API_ROUTER_KEY,
  },

  ios: {
    ...(appJson.ios ?? {}),
    bundleIdentifier: 'com.aqpa.driverapp',
  },
};

export default config;
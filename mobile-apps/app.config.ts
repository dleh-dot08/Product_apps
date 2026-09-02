import type { ExpoConfig } from 'expo/config';

const config: ExpoConfig = {
  ...require('./app.json').expo,
  extra: {
    ...(require('./app.json').expo.extra ?? {}),
    apiBaseUrl: process.env.EXPO_PUBLIC_API_BASE_URL,
    apiRouterKey: process.env.EXPO_PUBLIC_API_ROUTER_KEY,
  },
  ios: {
    bundleIdentifier: 'com.aqpa.driverapp',
  },
};

export default config;

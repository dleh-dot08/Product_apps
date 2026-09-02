import Constants from 'expo-constants';
import { Platform } from 'react-native';

type ExpoExtra = {
  apiBaseUrl?: string;
  apiRouterKey?: string;
};

const expoExtra = (Constants.expoConfig?.extra ?? {}) as ExpoExtra;

const defaultBaseUrl =
  Platform.OS === 'android'
    ? 'http://10.0.2.2:8001/api'
    : 'http://127.0.0.1:8001/api';

const defaultRouterKey = 'cHJvZHVjdF9hcHBzX2FwaV9yb3V0ZXJfMjAyNg==';

export const API_BASE_URL =
  process.env.EXPO_PUBLIC_API_BASE_URL ||
  expoExtra.apiBaseUrl ||
  defaultBaseUrl;

export const API_ROUTER_KEY =
  process.env.EXPO_PUBLIC_API_ROUTER_KEY ||
  expoExtra.apiRouterKey ||
  defaultRouterKey;

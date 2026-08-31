import { Text } from '@/components/CustomText';
import React, { useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  ImageBackground,
  KeyboardAvoidingView,
  Platform,
  SafeAreaView,
  ScrollView,
  StatusBar,
  StyleSheet, TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import * as Device from 'expo-device';

import { useAuth } from '../context/AuthContext';

const REMOTE_ASSETS = {
  hero: {
    uri: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1400&q=85',
  },
};

export default function LoginScreen() {
  const [email, setEmail] = useState('admin@mail.com');
  const [password, setPassword] = useState('admin4321');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const { login } = useAuth();

  const handleLogin = async () => {
    if (!email.trim() || !password) {
      Alert.alert(
        'Data Belum Lengkap',
        'Silakan isi email dan password.',
      );

      return;
    }

    setLoading(true);

    try {
      const deviceName = Device.deviceName || 'Mobile App';

      await login(
        email.trim(),
        password,
        deviceName,
      );
    } catch (error: any) {
      const message =
        error?.response?.data?.message ||
        'Email atau password yang Anda masukkan salah.';

      Alert.alert('Login Gagal', message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar
        barStyle="light-content"
        backgroundColor="#063B8C"
      />

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={styles.keyboardContainer}
      >
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          <View style={styles.page}>
            <ImageBackground
              source={REMOTE_ASSETS.hero}
              resizeMode="cover"
              style={styles.hero}
              imageStyle={styles.heroImage}
            >
              <LinearGradient
                colors={[
                  'rgba(4, 43, 104, 0.40)',
                  'rgba(4, 55, 133, 0.76)',
                  '#074FAE',
                ]}
                locations={[0, 0.56, 1]}
                style={styles.heroOverlay}
              >
                <View style={styles.topBar}>
                  <View style={styles.brand}>
                    <View style={styles.logo}>
                      <Text style={styles.logoLetter}>
                        A
                      </Text>
                    </View>

                    <View>
                      <Text style={styles.companyName}>
                        AQPA INDONESIA
                      </Text>

                      <Text style={styles.companyCaption}>
                        Internal Logistics System
                      </Text>
                    </View>
                  </View>

                  <View style={styles.secureBadge}>
                    <Ionicons
                      name="shield-checkmark"
                      size={14}
                      color="#FFFFFF"
                    />

                    <Text style={styles.secureBadgeText}>
                      Secure
                    </Text>
                  </View>
                </View>

                <View style={styles.heroContent}>

                  <Text style={styles.heroTitle}>
                    DRIVER APPS
                  </Text>

                  <Text style={styles.heroDescription}>
                    Monitoring tugas pengiriman,
                    penjemputan, perjalanan dan laporan
                    driver dalam satu aplikasi.
                  </Text>

                  <View style={styles.featureRow}>
                    <View style={styles.featureItem}>
                      <Ionicons
                        name="location-outline"
                        size={15}
                        color="#FFFFFF"
                      />

                      <Text style={styles.featureText}>
                        Monitoring
                      </Text>
                    </View>

                    <View style={styles.featureDivider} />

                    <View style={styles.featureItem}>
                      <Ionicons
                        name="document-text-outline"
                        size={15}
                        color="#FFFFFF"
                      />

                      <Text style={styles.featureText}>
                        Reporting
                      </Text>
                    </View>

                    <View style={styles.featureDivider} />

                    <View style={styles.featureItem}>
                      <Ionicons
                        name="checkmark-circle-outline"
                        size={15}
                        color="#FFFFFF"
                      />

                      <Text style={styles.featureText}>
                        Tracking
                      </Text>
                    </View>
                  </View>
                </View>
              </LinearGradient>
            </ImageBackground>

            <View style={styles.content}>
              <View style={styles.loginCard}>
                <View style={styles.cardHeader}>
                  <View>
                    <Text style={styles.welcomeText}>
                      Selamat datang
                    </Text>

                    <Text style={styles.loginTitle}>
                      Masuk ke akun Driver
                    </Text>
                  </View>

                  <View style={styles.driverIcon}>
                    <Ionicons
                      name="person-outline"
                      size={22}
                      color="#0A5DCC"
                    />
                  </View>
                </View>

                <Text style={styles.loginDescription}>
                  Gunakan akun internal perusahaan untuk
                  mengakses tugas operasional Anda.
                </Text>

                <View style={styles.form}>
                  <View style={styles.fieldGroup}>
                    <Text style={styles.label}>
                      Email
                    </Text>

                    <View style={styles.inputContainer}>
                      <View style={styles.inputIcon}>
                        <Ionicons
                          name="mail-outline"
                          size={18}
                          color="#64748B"
                        />
                      </View>

                      <TextInput
                        value={email}
                        onChangeText={setEmail}
                        placeholder="nama@perusahaan.com"
                        placeholderTextColor="#94A3B8"
                        keyboardType="email-address"
                        autoCapitalize="none"
                        autoCorrect={false}
                        editable={!loading}
                        style={styles.input}
                      />
                    </View>
                  </View>

                  <View style={styles.fieldGroup}>
                    <Text style={styles.label}>
                      Password
                    </Text>

                    <View style={styles.inputContainer}>
                      <View style={styles.inputIcon}>
                        <Ionicons
                          name="lock-closed-outline"
                          size={18}
                          color="#64748B"
                        />
                      </View>

                      <TextInput
                        value={password}
                        onChangeText={setPassword}
                        placeholder="Masukkan password"
                        placeholderTextColor="#94A3B8"
                        secureTextEntry={!showPassword}
                        autoCapitalize="none"
                        editable={!loading}
                        style={[
                          styles.input,
                          styles.passwordInput,
                        ]}
                      />

                      <TouchableOpacity
                        activeOpacity={0.65}
                        disabled={loading}
                        onPress={() =>
                          setShowPassword(
                            (previous) => !previous,
                          )
                        }
                        style={styles.passwordButton}
                      >
                        <Ionicons
                          name={
                            showPassword
                              ? 'eye-off-outline'
                              : 'eye-outline'
                          }
                          size={20}
                          color="#64748B"
                        />
                      </TouchableOpacity>
                    </View>
                  </View>

                  <TouchableOpacity
                    activeOpacity={0.88}
                    disabled={loading}
                    onPress={handleLogin}
                    style={[
                      styles.loginButtonContainer,
                      loading &&
                      styles.loginButtonDisabled,
                    ]}
                  >
                    <LinearGradient
                      colors={[
                        '#1268E8',
                        '#074EBA',
                      ]}
                      start={{
                        x: 0,
                        y: 0,
                      }}
                      end={{
                        x: 1,
                        y: 1,
                      }}
                      style={styles.loginButton}
                    >
                      {loading ? (
                        <ActivityIndicator
                          size="small"
                          color="#FFFFFF"
                        />
                      ) : (
                        <>
                          <Text
                            style={
                              styles.loginButtonText
                            }
                          >
                            Masuk
                          </Text>

                          <View
                            style={
                              styles.loginArrow
                            }
                          >
                            <Ionicons
                              name="arrow-forward"
                              size={16}
                              color="#FFFFFF"
                            />
                          </View>
                        </>
                      )}
                    </LinearGradient>
                  </TouchableOpacity>
                </View>

                <View style={styles.cardDivider} />

                <View style={styles.deviceInfo}>
                  <View style={styles.deviceIcon}>
                    <Ionicons
                      name="phone-portrait-outline"
                      size={17}
                      color="#0A5DCC"
                    />
                  </View>

                  <View style={styles.deviceText}>
                    <Text style={styles.deviceTitle}>
                      Perangkat terdeteksi otomatis
                    </Text>

                    <Text style={styles.deviceDescription}>
                      Informasi perangkat digunakan
                      untuk keamanan akses akun.
                    </Text>
                  </View>

                  <Ionicons
                    name="checkmark-circle"
                    size={20}
                    color="#16A36A"
                  />
                </View>
              </View>

              <View style={styles.helpCard}>
                <View style={styles.helpIcon}>
                  <Ionicons
                    name="headset-outline"
                    size={18}
                    color="#475569"
                  />
                </View>

                <View style={styles.helpContent}>
                  <Text style={styles.helpTitle}>
                    Mengalami kendala login?
                  </Text>

                  <Text style={styles.helpDescription}>
                    Hubungi administrator atau tim IT
                    perusahaan.
                  </Text>
                </View>
              </View>

              <View style={styles.footer}>
                <View style={styles.footerBrand}>
                  <View style={styles.footerDot} />

                  <Text style={styles.footerText}>
                    AQPA Driver Management System
                  </Text>
                </View>

                <Text style={styles.versionText}>
                  Version 1.0.0
                </Text>
              </View>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#063B8C',
  },

  keyboardContainer: {
    flex: 1,
  },

  scrollContent: {
    flexGrow: 1,
  },

  page: {
    flex: 1,
    minHeight: '100%',
    backgroundColor: '#F4F7FB',
  },

  hero: {
    height: 390,
    backgroundColor: '#074FAE',
  },

  heroImage: {
    opacity: 1,
  },

  heroOverlay: {
    flex: 1,
    paddingHorizontal: 22,
    paddingTop: 18,
    paddingBottom: 62,
  },

  topBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },

  brand: {
    flexDirection: 'row',
    alignItems: 'center',
  },

  logo: {
    width: 42,
    height: 42,
    marginRight: 11,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 13,

    shadowColor: '#000000',
    shadowOffset: {
      width: 0,
      height: 3,
    },
    shadowOpacity: 0.12,
    shadowRadius: 6,
    elevation: 4,
  },

  logoLetter: {
    color: '#0756C6',
    fontSize: 19,
    fontWeight: '900',
  },

  companyName: {
    color: '#FFFFFF',
    fontSize: 14,
    fontWeight: '800',
    letterSpacing: 0.3,
  },

  companyCaption: {
    marginTop: 2,
    color: 'rgba(255, 255, 255, 0.75)',
    fontSize: 10,
    fontWeight: '500',
  },

  secureBadge: {
    flexDirection: 'row',
    gap: 5,
    alignItems: 'center',

    paddingHorizontal: 9,
    paddingVertical: 6,

    backgroundColor: 'rgba(255, 255, 255, 0.14)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.18)',
    borderRadius: 20,
  },

  secureBadgeText: {
    color: '#FFFFFF',
    fontSize: 10,
    fontWeight: '700',
  },

  heroContent: {
    flex: 1,
    justifyContent: 'flex-end',
  },

  portalBadge: {
    alignSelf: 'flex-start',
    flexDirection: 'row',
    gap: 6,
    alignItems: 'center',

    marginBottom: 14,

    paddingHorizontal: 10,
    paddingVertical: 6,

    backgroundColor: 'rgba(255, 255, 255, 0.14)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.17)',
    borderRadius: 20,
  },

  portalBadgeText: {
    color: '#FFFFFF',
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 0.7,
  },

  heroTitle: {
    color: '#FFFFFF',
    fontSize: 35,
    fontWeight: '900',
    lineHeight: 40,
    letterSpacing: -1,
  },

  heroDescription: {
    maxWidth: 345,
    marginTop: 10,

    color: 'rgba(255, 255, 255, 0.82)',

    fontSize: 12.5,
    fontWeight: '400',
    lineHeight: 19,
  },

  featureRow: {
    flexDirection: 'row',
    alignItems: 'center',

    marginTop: 21,
  },

  featureItem: {
    flexDirection: 'row',
    gap: 5,
    alignItems: 'center',
  },

  featureText: {
    color: '#FFFFFF',
    fontSize: 10.5,
    fontWeight: '600',
  },

  featureDivider: {
    width: 1,
    height: 14,

    marginHorizontal: 11,

    backgroundColor: 'rgba(255, 255, 255, 0.30)',
  },

  content: {
    marginTop: -35,
    paddingHorizontal: 15,
    paddingBottom: 24,
  },

  loginCard: {
    padding: 21,

    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: '#E7ECF3',
    borderRadius: 24,

    shadowColor: '#0F2857',
    shadowOffset: {
      width: 0,
      height: 10,
    },
    shadowOpacity: 0.10,
    shadowRadius: 22,

    elevation: 8,
  },

  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },

  welcomeText: {
    marginBottom: 3,

    color: '#64748B',

    fontSize: 11,
    fontWeight: '600',
  },

  loginTitle: {
    color: '#0F172A',

    fontSize: 21,
    fontWeight: '800',
    letterSpacing: -0.35,
  },

  driverIcon: {
    width: 43,
    height: 43,

    alignItems: 'center',
    justifyContent: 'center',

    backgroundColor: '#EAF3FF',
    borderRadius: 13,
  },

  loginDescription: {
    maxWidth: 310,

    marginTop: 10,
    marginBottom: 22,

    color: '#64748B',

    fontSize: 12,
    lineHeight: 18,
  },

  form: {
    width: '100%',
  },

  fieldGroup: {
    marginBottom: 16,
  },

  label: {
    marginBottom: 7,

    color: '#334155',

    fontSize: 11.5,
    fontWeight: '700',
  },

  inputContainer: {
    minHeight: 53,

    flexDirection: 'row',
    alignItems: 'center',

    backgroundColor: '#F8FAFD',
    borderWidth: 1,
    borderColor: '#DCE4EE',
    borderRadius: 13,
  },

  inputIcon: {
    width: 45,

    alignItems: 'center',
    justifyContent: 'center',
  },

  input: {
    flex: 1,
    height: 51,

    paddingRight: 14,

    color: '#0F172A',

    fontSize: 13.5,
    fontWeight: '500',
  },

  passwordInput: {
    paddingRight: 48,
  },

  passwordButton: {
    position: 'absolute',
    right: 0,

    width: 48,
    height: 52,

    alignItems: 'center',
    justifyContent: 'center',
  },

  loginButtonContainer: {
    overflow: 'hidden',

    marginTop: 6,

    borderRadius: 13,

    shadowColor: '#0756C6',
    shadowOffset: {
      width: 0,
      height: 6,
    },
    shadowOpacity: 0.24,
    shadowRadius: 10,

    elevation: 5,
  },

  loginButtonDisabled: {
    opacity: 0.7,
  },

  loginButton: {
    minHeight: 54,

    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },

  loginButtonText: {
    color: '#FFFFFF',

    fontSize: 14.5,
    fontWeight: '800',
  },

  loginArrow: {
    position: 'absolute',
    right: 17,

    width: 29,
    height: 29,

    alignItems: 'center',
    justifyContent: 'center',

    backgroundColor: 'rgba(255, 255, 255, 0.14)',
    borderRadius: 9,
  },

  cardDivider: {
    height: 1,

    marginTop: 23,
    marginBottom: 17,

    backgroundColor: '#EDF1F6',
  },

  deviceInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },

  deviceIcon: {
    width: 36,
    height: 36,

    marginRight: 10,

    alignItems: 'center',
    justifyContent: 'center',

    backgroundColor: '#EAF3FF',
    borderRadius: 10,
  },

  deviceText: {
    flex: 1,
  },

  deviceTitle: {
    color: '#334155',

    fontSize: 11,
    fontWeight: '700',
  },

  deviceDescription: {
    marginTop: 2,

    color: '#94A3B8',

    fontSize: 9.5,
    lineHeight: 14,
  },

  helpCard: {
    flexDirection: 'row',
    alignItems: 'center',

    marginTop: 13,
    padding: 14,

    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: '#E7ECF3',
    borderRadius: 16,
  },

  helpIcon: {
    width: 37,
    height: 37,

    marginRight: 10,

    alignItems: 'center',
    justifyContent: 'center',

    backgroundColor: '#F1F5F9',
    borderRadius: 10,
  },

  helpContent: {
    flex: 1,
  },

  helpTitle: {
    color: '#334155',

    fontSize: 11,
    fontWeight: '700',
  },

  helpDescription: {
    marginTop: 2,

    color: '#94A3B8',

    fontSize: 9.5,
    lineHeight: 14,
  },

  footer: {
    alignItems: 'center',

    marginTop: 20,
  },

  footerBrand: {
    flexDirection: 'row',
    alignItems: 'center',
  },

  footerDot: {
    width: 6,
    height: 6,

    marginRight: 7,

    backgroundColor: '#16A36A',
    borderRadius: 3,
  },

  footerText: {
    color: '#64748B',

    fontSize: 10,
    fontWeight: '600',
  },

  versionText: {
    marginTop: 4,

    color: '#A0AABA',

    fontSize: 9,
  },
});
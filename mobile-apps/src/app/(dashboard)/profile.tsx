import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert, Platform } from 'react-native';
import { useAuth } from '../../context/AuthContext';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

export default function ProfileScreen() {
  const { user, logout } = useAuth();
  const { theme, mode, setMode } = useTheme();
  const colors = Colors[theme];

  const handleLogout = () => {
    if (Platform.OS === 'web') {
      if (window.confirm('Apakah Anda yakin ingin keluar?')) {
        logout();
      }
    } else {
      Alert.alert('Konfirmasi', 'Apakah Anda yakin ingin keluar?', [
        { text: 'Batal', style: 'cancel' },
        { text: 'Keluar', style: 'destructive', onPress: logout },
      ]);
    }
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Profil Saya</Text>
      </View>

      <View style={[styles.profileCard, { backgroundColor: colors.backgroundElement }]}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{user?.name ? user.name.charAt(0).toUpperCase() : 'A'}</Text>
        </View>
        <Text style={[styles.userName, { color: colors.text }]}>{user?.name || 'Administrator'}</Text>
        <Text style={styles.userEmail}>{user?.email || 'admin@mail.com'}</Text>
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{user?.role?.name || 'Super Admin'}</Text>
        </View>
        <Text style={styles.userDivision}>{user?.division?.name || 'Belum Ditugaskan'}</Text>
      </View>

      <View style={styles.themeSection}>
        <Text style={[styles.sectionTitle, { color: colors.text }]}>Pengaturan Tema</Text>
        <View style={[styles.themeOptions, { backgroundColor: colors.backgroundElement }]}>
          {(['light', 'dark', 'system'] as const).map((t) => (
            <TouchableOpacity
              key={t}
              style={[
                styles.themeButton,
                mode === t && { backgroundColor: theme === 'dark' ? '#334155' : '#e2e8f0' }
              ]}
              onPress={() => setMode(t)}
            >
              <Ionicons 
                name={t === 'light' ? 'sunny' : t === 'dark' ? 'moon' : 'settings'} 
                size={20} 
                color={mode === t ? colors.tint : colors.icon} 
              />
              <Text style={[
                styles.themeButtonText, 
                { color: mode === t ? colors.tint : colors.text }
              ]}>
                {t.charAt(0).toUpperCase() + t.slice(1)}
              </Text>
            </TouchableOpacity>
          ))}
        </View>
      </View>

      <View style={styles.actionSection}>
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <Text style={styles.logoutButtonText}>Log Out</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    padding: 20,
    paddingTop: 10,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
  },
  profileCard: {
    margin: 20,
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#2563eb',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  avatarText: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#fff',
  },
  userName: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  userEmail: {
    fontSize: 14,
    color: '#94a3b8',
    marginBottom: 12,
  },
  badge: {
    backgroundColor: 'rgba(37, 99, 235, 0.2)',
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(37, 99, 235, 0.4)',
  },
  badgeText: {
    color: '#60a5fa',
    fontSize: 12,
    fontWeight: 'bold',
  },
  userDivision: {
    fontSize: 14,
    color: '#94a3b8',
    marginTop: 12,
  },
  themeSection: {
    paddingHorizontal: 20,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 10,
  },
  themeOptions: {
    flexDirection: 'row',
    borderRadius: 12,
    padding: 4,
  },
  themeButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    gap: 6,
  },
  themeButtonText: {
    fontSize: 14,
    fontWeight: '500',
  },
  actionSection: {
    padding: 20,
    marginTop: 'auto',
    marginBottom: 40, // Increased to account for custom tab bar
  },
  logoutButton: {
    backgroundColor: 'rgba(239, 68, 68, 0.1)',
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.3)',
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
  },
  logoutButtonText: {
    color: '#ef4444',
    fontWeight: '600',
    fontSize: 16,
  },
});

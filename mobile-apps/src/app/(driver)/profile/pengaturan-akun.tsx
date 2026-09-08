import React from 'react';
import { View, StyleSheet, TouchableOpacity, Switch } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useTheme } from '../../../context/ThemeContext';
import { Colors } from '../../../constants/theme';

export default function PengaturanAkunScreen() {
  const router = useRouter();
  const { theme, mode, setMode } = useTheme();
  const colors = Colors[theme];

  const toggleDarkMode = () => {
    // If currently dark (or system which resolves to dark), switch to light, else switch to dark
    const newMode = theme === 'dark' ? 'light' : 'dark';
    setMode(newMode);
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]} edges={['top']}>
      <View style={[styles.header, { backgroundColor: colors.backgroundElement, borderBottomColor: colors.backgroundSelected }]}>
        <TouchableOpacity onPress={() => router.back()} style={styles.headerButton}>
          <Ionicons name="arrow-back" size={24} color={colors.text} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Pengaturan Akun</Text>
        <View style={{ width: 32 }} />
      </View>
      <View style={styles.content}>
        
        <View style={[styles.settingRow, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <View style={styles.settingInfo}>
            <Ionicons name="moon-outline" size={24} color={colors.textSecondary} />
            <Text style={[styles.settingText, { color: colors.text }]}>Mode Gelap (Dark Mode)</Text>
          </View>
          <Switch 
            value={theme === 'dark'}
            onValueChange={toggleDarkMode}
            trackColor={{ false: '#D1D5DB', true: '#0756C6' }}
            thumbColor={'#FFFFFF'}
          />
        </View>

      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#F9FAFB' },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', padding: 16, backgroundColor: '#FFFFFF', borderBottomWidth: 1, borderBottomColor: '#E5E7EB' },
  headerButton: { padding: 4 },
  headerTitle: { fontSize: 18, fontWeight: '600', color: '#1F2937' },
  content: { flex: 1, padding: 20 },
  settingRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderRadius: 12,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  settingInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  settingText: {
    fontSize: 16,
    fontWeight: '500',
    marginLeft: 12,
  }
});

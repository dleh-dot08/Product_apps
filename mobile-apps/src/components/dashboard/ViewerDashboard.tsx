import { Text } from '@/components/CustomText';
import React from 'react';
import { View, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

export default function ViewerDashboard() {
  const { user } = useAuth();
  const router = useRouter();
  const { theme } = useTheme();
  const colors = Colors[theme];

  return (
    <ScrollView contentContainerStyle={styles.scrollContent}>
      
      <TouchableOpacity 
        activeOpacity={0.8} 
        style={[styles.profileCard, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}
        onPress={() => router.push('/profile')}
      >
        <View style={[styles.profileAvatarContainer, { backgroundColor: '#8b5cf6' }]}>
          <Text style={styles.profileAvatarText}>
            {user?.name ? user.name.charAt(0).toUpperCase() : 'V'}
          </Text>
        </View>
        <View style={styles.profileInfo}>
          <Text style={[styles.greeting, { color: colors.text }]}>Halo, {user?.name || 'Manager'} 👋</Text>
          <View style={[styles.roleBadge, { backgroundColor: 'rgba(139, 92, 246, 0.2)' }]}>
            <Text style={[styles.roleText, { color: '#8b5cf6' }]}>{user?.role?.name || 'Viewer'}</Text>
          </View>
          <Text style={[styles.divisionText, { color: colors.textSecondary }]}>
            Laporan Kinerja Hari Ini
          </Text>
        </View>
      </TouchableOpacity>

      <View style={styles.grid}>
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>124</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Total Order</Text>
        </View>
        
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>89%</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Selesai Packing</Text>
        </View>

        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>42</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Dalam Pengiriman</Text>
        </View>
        
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>Rp 120Jt</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Revenue Hari Ini</Text>
        </View>
      </View>

      <View style={[styles.chartSection, { backgroundColor: colors.backgroundElement }]}>
        <View style={styles.chartHeader}>
          <Ionicons name="stats-chart-outline" size={24} color={colors.tint} />
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Performa Pengiriman</Text>
        </View>
        <View style={styles.placeholderChart}>
          <Text style={{ color: colors.textSecondary }}>[Grafik Statistik Tampil Di Sini]</Text>
        </View>
      </View>

    </ScrollView>
  );
}

const styles = StyleSheet.create({
  scrollContent: {
    padding: 20,
    paddingBottom: 20,
  },
  greeting: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  profileCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderRadius: 16,
    marginBottom: 20,
    borderWidth: 1,
  },
  profileAvatarContainer: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  profileAvatarText: {
    color: '#ffffff',
    fontSize: 24,
    fontWeight: 'bold',
  },
  profileInfo: {
    flex: 1,
  },
  roleBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
    alignSelf: 'flex-start',
    marginBottom: 6,
  },
  roleText: {
    fontSize: 12,
    fontWeight: '600',
  },
  divisionText: {
    fontSize: 12,
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 20,
  },
  card: {
    width: '48%',
    padding: 16,
    borderRadius: 12,
    marginBottom: 16,
    borderWidth: 1,
  },
  cardValue: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 8,
  },
  cardTitle: {
    fontSize: 12,
  },
  chartSection: {
    borderRadius: 16,
    padding: 20,
  },
  chartHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginLeft: 8,
  },
  placeholderChart: {
    height: 150,
    backgroundColor: 'rgba(0,0,0,0.05)',
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderStyle: 'dashed',
    borderColor: '#ccc',
  }
});

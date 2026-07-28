import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

export default function DriverDashboard() {
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
        <View style={[styles.profileAvatarContainer, { backgroundColor: '#10b981' }]}>
          <Text style={styles.profileAvatarText}>
            {user?.name ? user.name.charAt(0).toUpperCase() : 'D'}
          </Text>
        </View>
        <View style={styles.profileInfo}>
          <Text style={[styles.greeting, { color: colors.text }]}>Halo, {user?.name || 'Driver'} 🚚</Text>
          <View style={[styles.roleBadge, { backgroundColor: 'rgba(16, 185, 129, 0.2)' }]}>
            <Text style={[styles.roleText, { color: '#10b981' }]}>{user?.role?.name || 'Pengiriman'}</Text>
          </View>
          <Text style={[styles.divisionText, { color: colors.textSecondary }]}>
            Kendaraan: B 1234 CD
          </Text>
        </View>
      </TouchableOpacity>

      <View style={styles.statusRow}>
        <View style={[styles.statusItem, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.statusValue, { color: '#ef4444' }]}>2</Text>
          <Text style={[styles.statusLabel, { color: colors.textSecondary }]}>Menunggu</Text>
        </View>
        <View style={[styles.statusItem, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.statusValue, { color: '#f59e0b' }]}>1</Text>
          <Text style={[styles.statusLabel, { color: colors.textSecondary }]}>Di Jalan</Text>
        </View>
        <View style={[styles.statusItem, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Text style={[styles.statusValue, { color: '#10b981' }]}>4</Text>
          <Text style={[styles.statusLabel, { color: colors.textSecondary }]}>Selesai</Text>
        </View>
      </View>

      <View style={[styles.deliverySection, { backgroundColor: colors.backgroundElement }]}>
        <Text style={[styles.sectionTitle, { color: colors.text }]}>Pengiriman Aktif</Text>
        
        <View style={[styles.deliveryCard, { borderColor: colors.backgroundSelected }]}>
          <View style={styles.deliveryHeader}>
            <Text style={[styles.deliveryId, { color: colors.text }]}>#TRX-98273</Text>
            <View style={styles.badgeInProgress}>
              <Text style={styles.badgeTextInProgress}>Menuju Lokasi</Text>
            </View>
          </View>
          
          <View style={styles.routeContainer}>
            <View style={styles.routeIcon}>
              <Ionicons name="location-outline" size={20} color="#3b82f6" />
            </View>
            <View style={styles.routeInfo}>
              <Text style={[styles.routeLabel, { color: colors.textSecondary }]}>Tujuan</Text>
              <Text style={[styles.routeAddress, { color: colors.text }]}>PT Maju Jaya Abadi</Text>
              <Text style={[styles.routeDetail, { color: colors.textSecondary }]}>Jl. Sudirman No. 45, Jakarta Selatan</Text>
            </View>
          </View>
          
          <TouchableOpacity style={styles.actionButton}>
            <Ionicons name="scan-outline" size={20} color="#fff" />
            <Text style={styles.actionButtonText}>Scan Resi / POD</Text>
          </TouchableOpacity>
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
  statusRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 20,
  },
  statusItem: {
    flex: 1,
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    alignItems: 'center',
    marginHorizontal: 4,
  },
  statusValue: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  statusLabel: {
    fontSize: 12,
  },
  deliverySection: {
    borderRadius: 16,
    padding: 20,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 16,
  },
  deliveryCard: {
    borderWidth: 1,
    borderRadius: 12,
    padding: 16,
  },
  deliveryHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  deliveryId: {
    fontSize: 16,
    fontWeight: 'bold',
  },
  badgeInProgress: {
    backgroundColor: '#f59e0b',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  badgeTextInProgress: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
  routeContainer: {
    flexDirection: 'row',
    marginBottom: 20,
  },
  routeIcon: {
    marginRight: 12,
    marginTop: 2,
  },
  routeInfo: {
    flex: 1,
  },
  routeLabel: {
    fontSize: 12,
    marginBottom: 2,
  },
  routeAddress: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  routeDetail: {
    fontSize: 14,
  },
  actionButton: {
    backgroundColor: '#10b981',
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 12,
    borderRadius: 8,
  },
  actionButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
    marginLeft: 8,
  }
});

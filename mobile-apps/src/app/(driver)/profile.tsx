import React from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, Platform, Alert, Dimensions } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { useAuth } from '../../context/AuthContext';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

const { width } = Dimensions.get('window');

export default function ProfileScreen() {
  const { user, logout } = useAuth();
  const router = useRouter();

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

  const getInitials = (name: string) => {
    if (!name) return 'A';
    return name.charAt(0).toUpperCase();
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      {/* Blue Header Background */}
      <View style={styles.headerBackground}>
        <View style={styles.headerTop}>
          <View style={{ width: 28 }} />
          <Text style={styles.headerTitle}>Profil</Text>
          <TouchableOpacity>
            <Ionicons name="settings-outline" size={24} color="#FFFFFF" />
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Hero Card */}
        <View style={styles.heroCard}>
          <View style={styles.heroHeader}>
            <View style={styles.avatarContainer}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{getInitials(user?.name || '')}</Text>
              </View>
            </View>
            <View style={styles.heroInfo}>
              <Text style={styles.userName}>{user?.name || 'Data Belum Tersedia'}</Text>
              <Text style={styles.userRole}>{user?.role?.name || 'Data Belum Tersedia'}</Text>
              <View style={styles.statusBadge}>
                <Ionicons name="checkmark-circle" size={14} color="#10B981" />
                <Text style={styles.statusText}>Aktif</Text>
              </View>
            </View>
            <TouchableOpacity style={styles.editButton} onPress={() => router.push('/edit-profil')}>
              <Ionicons name="pencil" size={20} color="#0756C6" />
            </TouchableOpacity>
          </View>

          <View style={styles.heroDetails}>
            <View style={styles.heroDetailItem}>
              <Text style={styles.detailLabel}>ID Driver</Text>
              <Text style={styles.detailValue}>{user?.driver_id || 'Data Belum Tersedia'}</Text>
            </View>
            <View style={styles.heroDetailItem}>
              <Text style={styles.detailLabel}>No. HP</Text>
              <Text style={styles.detailValue}>{user?.phone || 'Data Belum Tersedia'}</Text>
            </View>
          </View>
        </View>

        {/* Quick Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Ionicons name="clipboard-outline" size={24} color="#0756C6" style={styles.statIcon} />
            <Text style={styles.statLabel}>Total Tugas</Text>
            <Text style={styles.statSubLabel}>Selesai</Text>
            <Text style={styles.statValue}>{user?.stats?.completed_tasks || 0}</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="time-outline" size={24} color="#0756C6" style={styles.statIcon} />
            <Text style={styles.statLabel}>Tepat Waktu</Text>
            <Text style={styles.statValue}>{user?.stats?.on_time_percentage || 0}%</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="star-outline" size={24} color="#0756C6" style={styles.statIcon} />
            <Text style={styles.statLabel}>Rating</Text>
            <Text style={styles.statValue}>{user?.stats?.rating || 0}/5</Text>
            <View style={styles.starsRow}>
              {[1, 2, 3, 4, 5].map(i => <Ionicons key={i} name="star" size={10} color={i <= (user?.stats?.rating || 0) ? "#FBBF24" : "#E5E7EB"} />)}
            </View>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="speedometer-outline" size={24} color="#0756C6" style={styles.statIcon} />
            <Text style={styles.statLabel}>Jam Mengemudi</Text>
            <Text style={styles.statValue}>{user?.stats?.driving_hours || 0}</Text>
            <Text style={styles.statSubLabel}>jam</Text>
          </View>
        </View>

        {/* Informasi Pribadi */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>Informasi Pribadi</Text>
          
          <View style={styles.infoRow}>
            <Ionicons name="card-outline" size={20} color="#6B7280" />
            <Text style={styles.infoLabel}>No. SIM</Text>
            <Text style={styles.infoValue}>{user?.sim_number || 'Data Belum Tersedia'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Ionicons name="calendar-outline" size={20} color="#6B7280" />
            <Text style={styles.infoLabel}>Masa Berlaku SIM</Text>
            <Text style={styles.infoValue}>{user?.sim_expiry || 'Data Belum Tersedia'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Ionicons name="location-outline" size={20} color="#6B7280" />
            <Text style={styles.infoLabel}>Alamat</Text>
            <Text style={styles.infoValue}>{user?.address || 'Data Belum Tersedia'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Ionicons name="mail-outline" size={20} color="#6B7280" />
            <Text style={styles.infoLabel}>Email</Text>
            <Text style={styles.infoValue}>{user?.email || 'Data Belum Tersedia'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Ionicons name="call-outline" size={20} color="#6B7280" />
            <Text style={styles.infoLabel}>Kontak Darurat</Text>
            <View style={styles.infoValueColumn}>
              <Text style={styles.infoValue}>{user?.emergency_contact || 'Data Belum Tersedia'}</Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" />
          </View>
        </View>

        {/* Kendaraan Favorit */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>Kendaraan Favorit / Terakhir Digunakan</Text>
          <TouchableOpacity style={styles.vehicleCard}>
            <View style={styles.vehicleIconContainer}>
              <Ionicons name="bus" size={32} color="#4B5563" />
            </View>
            <View style={styles.vehicleInfo}>
              <Text style={styles.vehicleName}>{user?.vehicle?.name || 'Data Belum Tersedia'}</Text>
              <View style={styles.vehicleMeta}>
                <Text style={styles.vehiclePlate}>{user?.vehicle?.plate || '-'}</Text>
                {user?.vehicle?.status && (
                  <View style={styles.vehicleBadge}>
                    <Text style={styles.vehicleBadgeText}>{user.vehicle.status}</Text>
                  </View>
                )}
              </View>
            </View>
            <Ionicons name="chevron-forward" size={20} color="#9CA3AF" />
          </TouchableOpacity>
        </View>

        {/* Menu Akun */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>Menu Akun</Text>
          
          <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/edit-profil')}>
            <Ionicons name="pencil-outline" size={20} color="#4B5563" />
            <Text style={styles.menuItemText}>Edit Profil</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <View style={styles.divider} />
          
          <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/dokumen-driver')}>
            <Ionicons name="document-text-outline" size={20} color="#4B5563" />
            <Text style={styles.menuItemText}>Dokumen Driver</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <View style={styles.divider} />

          <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/riwayat-tugas')}>
            <Ionicons name="list-outline" size={20} color="#4B5563" />
            <Text style={styles.menuItemText}>Riwayat Tugas</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <View style={styles.divider} />

          <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/pengaturan-akun')}>
            <Ionicons name="settings-outline" size={20} color="#4B5563" />
            <Text style={styles.menuItemText}>Pengaturan Akun</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <View style={styles.divider} />

          <TouchableOpacity style={styles.menuItem} onPress={() => router.push('/bantuan')}>
            <Ionicons name="help-circle-outline" size={20} color="#4B5563" />
            <Text style={styles.menuItemText}>Bantuan</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <View style={styles.divider} />

          <TouchableOpacity style={styles.menuItem} onPress={handleLogout}>
            <Ionicons name="log-out-outline" size={20} color="#EF4444" />
            <Text style={[styles.menuItemText, { color: '#EF4444' }]}>Keluar / Logout</Text>
            <Ionicons name="chevron-forward" size={16} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  headerBackground: {
    backgroundColor: '#0756C6',
    height: 180,
    width: '100%',
    position: 'absolute',
    top: 0,
    borderBottomLeftRadius: 24,
    borderBottomRightRadius: 24,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 10,
  },
  headerTitle: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: '600',
  },
  scrollContent: {
    paddingTop: 60,
    paddingBottom: 100, // For custom tab bar
  },
  heroCard: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 20,
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  heroHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 20,
  },
  avatarContainer: {
    marginRight: 16,
  },
  avatar: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#D1D5DB', // Placeholder color since we don't have image URL
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  heroInfo: {
    flex: 1,
  },
  userName: {
    fontSize: 18,
    fontWeight: '700',
    color: '#1F2937',
    marginBottom: 4,
  },
  userRole: {
    fontSize: 14,
    color: '#4B5563',
    marginBottom: 8,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    alignSelf: 'flex-start',
    gap: 4,
  },
  statusText: {
    color: '#10B981',
    fontSize: 12,
    fontWeight: '600',
  },
  editButton: {
    padding: 8,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    borderRadius: 12,
  },
  heroDetails: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
    paddingTop: 16,
  },
  heroDetailItem: {
    flex: 1,
  },
  detailLabel: {
    fontSize: 12,
    color: '#6B7280',
    marginBottom: 4,
  },
  detailValue: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    marginTop: 16,
  },
  statCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    paddingVertical: 16,
    paddingHorizontal: 4,
    alignItems: 'center',
    width: (width - 40 - 24) / 4,
    borderWidth: 1,
    borderColor: '#F3F4F6',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 5,
    elevation: 2,
  },
  statIcon: {
    marginBottom: 8,
  },
  statLabel: {
    fontSize: 9,
    color: '#6B7280',
    textAlign: 'center',
    marginBottom: 2,
  },
  statSubLabel: {
    fontSize: 9,
    color: '#6B7280',
    textAlign: 'center',
    marginBottom: 4,
  },
  statValue: {
    fontSize: 16,
    fontWeight: '700',
    color: '#1F2937',
    marginTop: 'auto',
  },
  starsRow: {
    flexDirection: 'row',
    marginTop: 4,
  },
  sectionContainer: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 20,
    marginTop: 16,
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: '#F3F4F6',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 5,
    elevation: 2,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: '#1F2937',
    marginBottom: 16,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
    gap: 12,
  },
  infoLabel: {
    width: 100,
    fontSize: 13,
    color: '#6B7280',
  },
  infoValueColumn: {
    flex: 1,
  },
  infoValue: {
    flex: 1,
    fontSize: 13,
    fontWeight: '500',
    color: '#1F2937',
  },
  vehicleCard: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#F3F4F6',
    borderRadius: 12,
    padding: 12,
  },
  vehicleIconContainer: {
    width: 48,
    height: 48,
    backgroundColor: '#F3F4F6',
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  vehicleInfo: {
    flex: 1,
  },
  vehicleName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1F2937',
    marginBottom: 4,
  },
  vehicleMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  vehiclePlate: {
    fontSize: 13,
    color: '#4B5563',
  },
  vehicleBadge: {
    backgroundColor: '#EFF6FF',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 8,
  },
  vehicleBadgeText: {
    color: '#2563EB',
    fontSize: 10,
    fontWeight: '600',
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    gap: 12,
  },
  menuItemText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#1F2937',
  },
  divider: {
    height: 1,
    backgroundColor: '#F3F4F6',
  }
});

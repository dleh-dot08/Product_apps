import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';

import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '../../context/AuthContext';

export default function DashboardScreen() {
  const { user } = useAuth();
  const router = useRouter();

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        
        <TouchableOpacity 
          activeOpacity={0.8} 
          style={styles.profileCard}
          onPress={() => router.push('/profile')}
        >
          <View style={styles.profileAvatarContainer}>
            <Text style={styles.profileAvatarText}>
              {user?.name ? user.name.charAt(0).toUpperCase() : 'A'}
            </Text>
          </View>
          <View style={styles.profileInfo}>
            <Text style={styles.greeting}>Selamat datang, {user?.name || 'Administrator'} 👋</Text>
            <View style={styles.roleBadge}>
              <Text style={styles.roleText}>{user?.role?.name || 'Super Admin'}</Text>
            </View>
            <Text style={styles.divisionText}>
              Divisi: {user?.division?.name || 'Belum Ditugaskan'}
            </Text>
          </View>
        </TouchableOpacity>

        <View style={styles.grid}>
          <View style={[styles.card, { borderTopColor: '#3b82f6', borderTopWidth: 4 }]}>
            <Text style={styles.cardValue}>12</Text>
            <Text style={styles.cardTitle}>Proyek Aktif</Text>
          </View>
          
          <View style={[styles.card, { borderTopColor: '#10b981', borderTopWidth: 4 }]}>
            <Text style={styles.cardValue}>Rp 45Jt</Text>
            <Text style={styles.cardTitle}>Pendapatan Bulan Ini</Text>
          </View>

          <View style={[styles.card, { borderTopColor: '#f59e0b', borderTopWidth: 4 }]}>
            <Text style={styles.cardValue}>142</Text>
            <Text style={styles.cardTitle}>Total Barang di Gudang</Text>
          </View>
          
          <View style={[styles.card, { borderTopColor: '#ef4444', borderTopWidth: 4 }]}>
            <Text style={styles.cardValue}>3</Text>
            <Text style={styles.cardTitle}>Tagihan Tertunda</Text>
          </View>
        </View>

        <View style={styles.recentSection}>
          <Text style={styles.sectionTitle}>Aktivitas Terbaru</Text>
          <View style={styles.activityItem}>
            <View style={styles.activityDot}></View>
            <View>
              <Text style={styles.activityText}>Pembelian material "Semen 50kg"</Text>
              <Text style={styles.activityTime}>Hari ini, 10:45</Text>
            </View>
          </View>
          <View style={styles.activityItem}>
            <View style={[styles.activityDot, { backgroundColor: '#10b981' }]}></View>
            <View>
              <Text style={styles.activityText}>Tagihan #INV-001 Lunas</Text>
              <Text style={styles.activityTime}>Kemarin, 15:20</Text>
            </View>
          </View>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0b1320',
  },
  scrollContent: {
    padding: 20,
  },
  header: {
    marginBottom: 24,
  },
  greeting: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  profileCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#13233a',
    padding: 16,
    borderRadius: 16,
    marginBottom: 24,
    borderWidth: 1,
    borderColor: '#1e293b',
  },
  profileAvatarContainer: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#3b82f6',
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
    backgroundColor: 'rgba(59, 130, 246, 0.2)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
    alignSelf: 'flex-start',
    marginBottom: 6,
  },
  roleText: {
    color: '#60a5fa',
    fontSize: 12,
    fontWeight: '600',
  },
  divisionText: {
    color: '#94a3b8',
    fontSize: 12,
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  card: {
    backgroundColor: '#13233a',
    width: '48%',
    padding: 16,
    borderRadius: 12,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 8,
  },
  cardTitle: {
    fontSize: 12,
    color: '#94a3b8',
  },
  recentSection: {
    backgroundColor: '#13233a',
    borderRadius: 16,
    padding: 20,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 16,
  },
  activityItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  activityDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#3b82f6',
    marginRight: 12,
  },
  activityText: {
    color: '#e2e8f0',
    fontSize: 14,
    marginBottom: 2,
  },
  activityTime: {
    color: '#64748b',
    fontSize: 12,
  },
});

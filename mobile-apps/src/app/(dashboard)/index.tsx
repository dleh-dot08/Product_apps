import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '../../context/AuthContext';

export default function DashboardScreen() {
  const { user } = useAuth();

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        
        <View style={styles.header}>
          <Text style={styles.greeting}>Halo, {user?.name || 'Administrator'} 👋</Text>
          <Text style={styles.subtitle}>Selamat datang di Dashboard AQPA Indonesia</Text>
        </View>

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
  subtitle: {
    fontSize: 14,
    color: '#94a3b8',
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

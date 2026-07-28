import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from 'react-native';

import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';

export default function PackerDashboard() {
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
        <View style={styles.profileAvatarContainer}>
          <Text style={styles.profileAvatarText}>
            {user?.name ? user.name.charAt(0).toUpperCase() : 'P'}
          </Text>
        </View>
        <View style={styles.profileInfo}>
          <Text style={[styles.greeting, { color: colors.text }]}>Halo, {user?.name || 'Packer'} 👋</Text>
          <View style={styles.roleBadge}>
            <Text style={styles.roleText}>{user?.role?.name || 'Operator Staff'}</Text>
          </View>
          <Text style={[styles.divisionText, { color: colors.textSecondary }]}>
            Divisi: {user?.division?.name || 'Warehouse'}
          </Text>
        </View>
      </TouchableOpacity>

      <View style={styles.grid}>
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderTopColor: '#3b82f6', borderTopWidth: 4 }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>12</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Tugas Packing</Text>
        </View>
        
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderTopColor: '#10b981', borderTopWidth: 4 }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>45</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Selesai Hari Ini</Text>
        </View>

        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderTopColor: '#f59e0b', borderTopWidth: 4 }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>142</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Stok Material</Text>
        </View>
        
        <View style={[styles.card, { backgroundColor: colors.backgroundElement, borderTopColor: '#ef4444', borderTopWidth: 4 }]}>
          <Text style={[styles.cardValue, { color: colors.text }]}>3</Text>
          <Text style={[styles.cardTitle, { color: colors.textSecondary }]}>Material Habis</Text>
        </View>
      </View>

      <View style={[styles.recentSection, { backgroundColor: colors.backgroundElement }]}>
        <Text style={[styles.sectionTitle, { color: colors.text }]}>Aktivitas Packing</Text>
        <View style={styles.activityItem}>
          <View style={styles.activityDot}></View>
          <View>
            <Text style={[styles.activityText, { color: colors.text }]}>Packing Klien A Selesai</Text>
            <Text style={[styles.activityTime, { color: colors.textSecondary }]}>Hari ini, 10:45</Text>
          </View>
        </View>
        <View style={styles.activityItem}>
          <View style={[styles.activityDot, { backgroundColor: '#10b981' }]}></View>
          <View>
            <Text style={[styles.activityText, { color: colors.text }]}>Ambil material "Kayu Pallet"</Text>
            <Text style={[styles.activityTime, { color: colors.textSecondary }]}>Kemarin, 15:20</Text>
          </View>
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
    marginBottom: 24,
    borderWidth: 1,
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
    color: '#3b82f6',
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
    marginBottom: 24,
  },
  card: {
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
    marginBottom: 8,
  },
  cardTitle: {
    fontSize: 12,
  },
  recentSection: {
    borderRadius: 16,
    padding: 20,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
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
    fontSize: 14,
    marginBottom: 2,
  },
  activityTime: {
    fontSize: 12,
  },
});

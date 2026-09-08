import React, { useState } from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import * as Updates from 'expo-updates';

export default function PembaruanSistemScreen() {
  const router = useRouter();
  const [isChecking, setIsChecking] = useState(false);

  // Informasi update saat ini
  const updateId = Updates.updateId || 'Bawaan Pabrik (APK Asli)';
  const createdAt = Updates.createdAt;
  const isEmbedded = Updates.isEmbeddedLaunch;

  const formatDate = (date: Date | null | undefined) => {
    if (!date) return 'Belum ada update OTA';
    return date.toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  };

  const formatTime = (date: Date | null | undefined) => {
    if (!date) return '-';
    return date.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
    }) + ' WIB';
  };

  const handleSyncUpdate = async () => {
    try {
      setIsChecking(true);
      const update = await Updates.checkForUpdateAsync();
      
      if (update.isAvailable) {
        Alert.alert(
          'Pembaruan Tersedia',
          'Sistem menemukan pembaruan baru. Sedang mengunduh...',
        );
        await Updates.fetchUpdateAsync();
        
        Alert.alert(
          'Berhasil Diunduh',
          'Pembaruan telah berhasil diunduh. Aplikasi akan dimuat ulang untuk menerapkan versi terbaru.',
          [{ text: 'Muat Ulang Sekarang', onPress: () => Updates.reloadAsync() }]
        );
      } else {
        Alert.alert('Sistem Terkini', 'Sistem Anda sudah menggunakan versi yang paling baru. Tidak ada pembaruan saat ini.');
      }
    } catch (error: any) {
      Alert.alert('Gagal Memeriksa Update', error.message || 'Terjadi kesalahan saat menghubungi server OTA.');
    } finally {
      setIsChecking(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.headerBackground}>
        <View style={styles.headerTop}>
          <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
            <Ionicons name="arrow-back" size={24} color="#FFFFFF" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Informasi Sistem</Text>
          <View style={{ width: 24 }} />
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.card}>
          <View style={styles.iconContainer}>
            <Ionicons name="phone-portrait-outline" size={48} color="#0756C6" />
          </View>
          <Text style={styles.appTitle}>Aplikasi Driver</Text>
          <Text style={styles.statusText}>
            {isEmbedded ? 'Menjalankan Versi Dasar (APK)' : 'Menjalankan Versi OTA'}
          </Text>

          <View style={styles.infoBox}>
            <InfoRow icon="calendar-outline" label="Tanggal Update" value={formatDate(createdAt)} />
            <InfoRow icon="time-outline" label="Jam Update" value={formatTime(createdAt)} />
            <InfoRow icon="finger-print-outline" label="ID Update" value={updateId} isLast />
          </View>
        </View>

        <TouchableOpacity 
          style={[styles.syncButton, isChecking && styles.syncButtonDisabled]} 
          onPress={handleSyncUpdate}
          disabled={isChecking}
        >
          {isChecking ? (
            <ActivityIndicator color="#FFFFFF" />
          ) : (
            <>
              <Ionicons name="sync-outline" size={20} color="#FFFFFF" />
              <Text style={styles.syncButtonText}>Sinkronisasi Pembaruan</Text>
            </>
          )}
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

function InfoRow({ icon, label, value, isLast = false }: { icon: any, label: string, value: string, isLast?: boolean }) {
  return (
    <View style={[styles.infoRow, !isLast && styles.infoRowBorder]}>
      <View style={styles.infoLabelContainer}>
        <Ionicons name={icon} size={18} color="#6B7280" />
        <Text style={styles.infoLabel}>{label}</Text>
      </View>
      <Text style={styles.infoValue} numberOfLines={2}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  headerBackground: {
    backgroundColor: '#0756C6',
    height: 120,
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
  backButton: {
    padding: 4,
  },
  scrollContent: {
    paddingTop: 80,
    paddingBottom: 40,
  },
  card: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 20,
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  iconContainer: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#EFF6FF',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  appTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#1F2937',
    marginBottom: 4,
  },
  statusText: {
    fontSize: 14,
    color: '#10B981',
    fontWeight: '500',
    marginBottom: 24,
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  infoBox: {
    width: '100%',
    backgroundColor: '#F9FAFB',
    borderRadius: 12,
    padding: 16,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
  },
  infoRowBorder: {
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  infoLabelContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  infoLabel: {
    fontSize: 14,
    color: '#6B7280',
    fontWeight: '500',
  },
  infoValue: {
    fontSize: 14,
    color: '#1F2937',
    fontWeight: '600',
    textAlign: 'right',
    flex: 1,
    marginLeft: 16,
  },
  syncButton: {
    backgroundColor: '#0756C6',
    marginHorizontal: 20,
    marginTop: 24,
    borderRadius: 12,
    paddingVertical: 16,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 8,
    shadowColor: '#0756C6',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  syncButtonDisabled: {
    backgroundColor: '#93C5FD',
  },
  syncButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '600',
  },
});

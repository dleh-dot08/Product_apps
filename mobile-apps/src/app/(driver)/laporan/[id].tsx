import React, { useEffect, useState } from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, Dimensions, ActivityIndicator, Image, Modal } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import api from '../../../services/api';

const { width } = Dimensions.get('window');

export default function LaporanDetailScreen() {
  const router = useRouter();
  const { id } = useLocalSearchParams();

  const [task, setTask] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  useEffect(() => {
    const fetchTask = async () => {
      try {
        const res = await api.get(`/pickup/${id}`);
        if (res.data && res.data.data) {
          setTask(res.data.data);
        }
      } catch (error) {
        console.error('Failed to fetch task detail', error);
      } finally {
        setLoading(false);
      }
    };
    if (id) fetchTask();
  }, [id]);

  if (loading) {
    return (
      <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
        <ActivityIndicator size="large" color="#0756C6" />
      </View>
    );
  }

  if (!task) {
    return (
      <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
        <Text>Tugas tidak ditemukan</Text>
      </View>
    );
  }

  const reportId = task.reference_number || `TASK-${task.id}`;

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'assigned': return 'Menunggu';
      case 'on_route': return 'Di Perjalanan';
      case 'arrived': return 'Tiba';
      case 'delivered': return 'Selesai';
      case 'failed': return 'Gagal';
      case 'cancelled': return 'Dibatalkan';
      default: return status;
    }
  };

  const getStatusBgColor = (status: string) => {
    switch (status) {
      case 'assigned': return '#DBEAFE';
      case 'on_route': return '#FEF3C7';
      case 'arrived': return '#EDE9FE';
      case 'delivered': return '#D1FAE5';
      case 'failed': case 'cancelled': return '#FEE2E2';
      default: return '#F3F4F6';
    }
  };
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'assigned': return '#3B82F6';
      case 'on_route': return '#F59E0B'; 
      case 'arrived': return '#8B5CF6';
      case 'delivered': return '#10B981'; 
      case 'failed': case 'cancelled': return '#EF4444'; 
      default: return '#6B7280';
    }
  };

  const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
  };

  const formatRupiah = (amount: number) => {
    return 'Rp ' + amount.toLocaleString('id-ID');
  };

  const getFullImageUrl = (path: string) => {
    if (!path) return '';
    return path.startsWith('http') ? path : `${api.defaults.baseURL?.replace('/api', '')}/storage/${path}`;
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.headerButton}>
          <Ionicons name="arrow-back" size={24} color="#FFFFFF" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Detail Laporan</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Main Info Card */}
        <View style={styles.mainCard}>
          <View style={styles.mainCardHeader}>
            <Text style={styles.reportId}>{reportId}</Text>
            <View style={[styles.statusBadge, { backgroundColor: getStatusBgColor(task.status) }]}>
              <Text style={[styles.statusText, { color: getStatusColor(task.status) }]}>{getStatusLabel(task.status)}</Text>
            </View>
          </View>
          
          <Text style={styles.routeOrigin}>{task.pickup_name || '-'}</Text>
          <View style={styles.routeDestinationContainer}>
            <Ionicons name="arrow-forward" size={16} color="#6B7280" />
            <Text style={styles.routeDestination}>{task.destination || '-'}</Text>
          </View>

          <View style={styles.metaInfoRow}>
            <View style={styles.metaItem}>
              <Ionicons name="time-outline" size={16} color="#6B7280" />
              <Text style={styles.metaText}>Mulai: {formatDateTime(task.started_at)}</Text>
            </View>
          </View>
          <View style={styles.metaInfoRow}>
            <View style={styles.metaItem}>
              <Ionicons name="checkmark-done-outline" size={16} color="#6B7280" />
              <Text style={styles.metaText}>Selesai: {formatDateTime(task.completed_at)}</Text>
            </View>
          </View>
        </View>

        {/* Lampiran & Bukti Section (Input by Driver) */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>Data & Lampiran Tugas</Text>
          
          {(!task.attachments || task.attachments.length === 0) ? (
            <View style={styles.emptyCard}>
              <Text style={styles.emptyText}>Belum ada lampiran</Text>
            </View>
          ) : (
            task.attachments.map((att: any) => (
              <View key={att.id} style={styles.attachmentCard}>
                <View style={styles.attachmentHeader}>
                  <Ionicons name="image-outline" size={20} color="#0756C6" />
                  <Text style={styles.attachmentTitle}>{att.category.replace(/_/g, ' ')}</Text>
                </View>
                <TouchableOpacity onPress={() => setSelectedImage(getFullImageUrl(att.file_path))}>
                  <Image source={{ uri: getFullImageUrl(att.file_path) }} style={styles.attachmentImage} />
                </TouchableOpacity>
                {att.notes && <Text style={styles.attachmentNote}>Catatan: {att.notes}</Text>}
              </View>
            ))
          )}
        </View>

        {/* Laporan Keuangan */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>Laporan Keuangan</Text>
          
          {(!task.shift?.expenses || task.shift.expenses.length === 0) ? (
            <View style={styles.emptyCard}>
              <Text style={styles.emptyText}>Tidak ada laporan pengeluaran</Text>
            </View>
          ) : (
            <View style={styles.financeCard}>
              {task.shift.expenses.map((expense: any, idx: number) => (
                <View key={expense.id} style={[styles.financeItem, idx > 0 && styles.borderTop]}>
                  <View style={styles.financeHeader}>
                    <View>
                      <Text style={styles.financeCategory}>{expense.category}</Text>
                      {expense.description && <Text style={styles.financeDesc}>{expense.description}</Text>}
                    </View>
                    <Text style={styles.financeAmount}>{formatRupiah(Number(expense.amount))}</Text>
                  </View>
                  {expense.notes && <Text style={styles.financeNotes}>Catatan: {expense.notes}</Text>}
                  {expense.receipt_url && (
                    <TouchableOpacity onPress={() => setSelectedImage(getFullImageUrl(expense.receipt_url))} style={{ marginTop: 8 }}>
                      <Text style={{ color: '#0756C6', fontSize: 13, fontFamily: 'Inter-Medium', textDecorationLine: 'underline' }}>Lihat Bukti Upload</Text>
                    </TouchableOpacity>
                  )}
                </View>
              ))}
              <View style={styles.financeTotalRow}>
                <Text style={styles.financeTotalLabel}>Total Pengeluaran</Text>
                <Text style={styles.financeTotalValue}>
                  {formatRupiah(task.shift.expenses.reduce((acc: number, val: any) => acc + Number(val.amount), 0))}
                </Text>
              </View>
            </View>
          )}
        </View>
      </ScrollView>

      {/* Image Viewer Modal */}
      {selectedImage && (
        <Modal visible={true} transparent={true} onRequestClose={() => setSelectedImage(null)}>
          <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.9)', justifyContent: 'center', alignItems: 'center' }}>
            <TouchableOpacity 
              style={{ position: 'absolute', top: 50, right: 20, zIndex: 10, padding: 8 }}
              onPress={() => setSelectedImage(null)}
            >
              <Ionicons name="close" size={32} color="#FFF" />
            </TouchableOpacity>
            <Image 
              source={{ uri: selectedImage }} 
              style={{ width: '100%', height: '80%' }} 
              resizeMode="contain" 
            />
          </View>
        </Modal>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F3F4F6',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#0756C6',
    paddingHorizontal: 20,
    paddingVertical: 16,
  },
  headerButton: {
    padding: 4,
  },
  headerTitle: {
    color: '#FFFFFF',
    fontSize: 18,
    fontFamily: 'Inter-SemiBold',
  },
  scrollContent: {
    paddingBottom: 40,
  },
  mainCard: {
    backgroundColor: '#FFFFFF',
    margin: 20,
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
  },
  mainCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  reportId: {
    fontSize: 18,
    fontFamily: 'Inter-Bold',
    color: '#111827',
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
  },
  statusText: {
    fontSize: 12,
    fontFamily: 'Inter-SemiBold',
  },
  routeOrigin: {
    fontSize: 16,
    fontFamily: 'Inter-SemiBold',
    color: '#1F2937',
  },
  routeDestinationContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
    marginBottom: 16,
  },
  routeDestination: {
    fontSize: 16,
    fontFamily: 'Inter-SemiBold',
    color: '#1F2937',
    marginLeft: 8,
  },
  metaInfoRow: {
    flexDirection: 'row',
    marginBottom: 8,
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  metaText: {
    marginLeft: 6,
    fontSize: 14,
    fontFamily: 'Inter-Medium',
    color: '#4B5563',
  },
  sectionContainer: {
    paddingHorizontal: 20,
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 16,
    fontFamily: 'Inter-SemiBold',
    color: '#111827',
    marginBottom: 12,
  },
  emptyCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 24,
    alignItems: 'center',
  },
  emptyText: {
    fontFamily: 'Inter-Medium',
    color: '#9CA3AF',
  },
  attachmentCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  attachmentHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  attachmentTitle: {
    fontFamily: 'Inter-SemiBold',
    color: '#1F2937',
    marginLeft: 8,
    textTransform: 'capitalize'
  },
  attachmentImage: {
    width: '100%',
    height: 200,
    borderRadius: 8,
    backgroundColor: '#F3F4F6'
  },
  attachmentNote: {
    fontFamily: 'Inter-Medium',
    color: '#6B7280',
    marginTop: 8,
    fontSize: 13
  },
  financeCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 16,
  },
  financeItem: {
    paddingVertical: 12,
  },
  borderTop: {
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  financeHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  financeCategory: {
    fontFamily: 'Inter-SemiBold',
    color: '#1F2937',
    textTransform: 'capitalize'
  },
  financeDesc: {
    fontFamily: 'Inter-Regular',
    color: '#6B7280',
    fontSize: 13,
    marginTop: 2,
  },
  financeAmount: {
    fontFamily: 'Inter-SemiBold',
    color: '#111827',
  },
  financeNotes: {
    fontFamily: 'Inter-Medium',
    color: '#F59E0B',
    fontSize: 12,
    marginTop: 6,
  },
  financeTotalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#E5E7EB',
    marginTop: 4,
  },
  financeTotalLabel: {
    fontFamily: 'Inter-Bold',
    color: '#111827',
  },
  financeTotalValue: {
    fontFamily: 'Inter-Bold',
    color: '#0756C6',
    fontSize: 16,
  },
});

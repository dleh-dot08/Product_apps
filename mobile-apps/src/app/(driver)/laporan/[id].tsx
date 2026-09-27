import React, { useEffect, useState, useCallback } from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, Dimensions, ActivityIndicator, Image, Modal, TextInput, Alert, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as ImagePicker from 'expo-image-picker';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter, useFocusEffect } from 'expo-router';
import { useTheme } from '../../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import api from '../../../services/api';

const { width } = Dimensions.get('window');

export default function LaporanDetailScreen() {
  const router = useRouter();
  const { id } = useLocalSearchParams();
  const { theme } = useTheme();
  const colors = Colors[theme];
  const isDark = theme === 'dark';

  const [manifest, setManifest] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState<string | null>(null);
  const [showDepartureModal, setShowDepartureModal] = useState(false);
  const [showSerahTerimaModal, setShowSerahTerimaModal] = useState(false);
  const [showPenutupModal, setShowPenutupModal] = useState(false);
  const [showExpenseModal, setShowExpenseModal] = useState(false);
  const [expandedTasks, setExpandedTasks] = useState<Record<string, boolean>>({});

  const [expenseData, setExpenseData] = useState({
    amount: '',
    category: 'BBM',
    notes: '',
    receipt: null as any
  });
  const [submittingExpense, setSubmittingExpense] = useState(false);

  const [refreshing, setRefreshing] = useState(false);

  const fetchManifest = useCallback(async (isRefresh = false) => {
    if (!id) return;
    try {
      if (!isRefresh && !manifest) setLoading(true);
      const res = await api.get(`/driver/manifests/${id}`);
      if (res.data && res.data.data) {
        setManifest(res.data.data);
      }
    } catch (error) {
      console.error('Failed to fetch manifest detail', error);
    } finally {
      setLoading(false);
      if (isRefresh) setRefreshing(false);
    }
  }, [id, manifest]);

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    fetchManifest(true);
  }, [fetchManifest]);

  useFocusEffect(
    useCallback(() => {
      fetchManifest(false);
    }, [fetchManifest])
  );

  if (loading) {
    return (
      <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
        <ActivityIndicator size="large" color="#0756C6" />
      </View>
    );
  }

  if (!manifest) {
    return (
      <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
        <Text>Penugasan tidak ditemukan</Text>
      </View>
    );
  }

  const manifestIdStr = manifest.manifest_number || `DO-${manifest.id.substring(0,8).toUpperCase()}`;

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'draft': return 'Draft';
      case 'assigned': return 'Menunggu';
      case 'on_route': return 'Di Perjalanan';
      case 'completed': return 'Selesai';
      case 'cancelled': return 'Dibatalkan';
      case 'pending': return 'Terkendala';
      case 'failed': return 'Gagal';
      default: return status;
    }
  };

  const getStatusBgColor = (status: string) => {
    switch (status) {
      case 'draft': return '#E5E7EB';
      case 'assigned': return '#DBEAFE';
      case 'on_route': return '#FEF3C7';
      case 'completed': return '#D1FAE5';
      case 'cancelled': return '#FEE2E2';
      case 'pending': return '#FEF3C7';
      case 'failed': return '#FEE2E2';
      default: return '#F3F4F6';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'draft': return '#4B5563';
      case 'assigned': return '#3B82F6';
      case 'on_route': return '#F59E0B'; 
      case 'completed': return '#10B981'; 
      case 'cancelled': return '#EF4444'; 
      case 'pending': return '#F59E0B';
      case 'failed': return '#EF4444';
      default: return '#6B7280';
    }
  };

  const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
  };

  // Combine and sort tasks
  const tasks = [...(manifest.pickup_tasks || manifest.pickupTasks || []), ...(manifest.delivery_assignments || manifest.deliveryAssignments || [])].sort(
    (a, b) => new Date(a.assigned_at).getTime() - new Date(b.assigned_at).getTime()
  );

  const pickReceipt = async () => {
    let result = await ImagePicker.launchCameraAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      quality: 0.5,
    });

    if (!result.canceled && result.assets && result.assets.length > 0) {
      setExpenseData({ ...expenseData, receipt: result.assets[0] });
    }
  };

  const submitExpense = async () => {
    if (!expenseData.amount || !expenseData.category || !expenseData.receipt) {
      Alert.alert('Error', 'Harap isi jumlah, kategori, dan foto bukti');
      return;
    }
    
    setSubmittingExpense(true);
    try {
      const formData = new FormData();
      formData.append('amount', expenseData.amount);
      formData.append('category', expenseData.category);
      formData.append('notes', expenseData.notes);
      
      const fileUri = expenseData.receipt.uri;
      const fileName = fileUri.split('/').pop() || 'receipt.jpg';
      const fileType = 'image/jpeg';
      
      formData.append('receipt', {
        uri: fileUri,
        name: fileName,
        type: fileType,
      } as any);

      const res = await api.post(`/manifests/${id}/expenses`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      
      if (res.data?.success || res.status === 201) {
        Alert.alert('Sukses', 'Laporan keuangan berhasil diupload');
        setShowExpenseModal(false);
        setExpenseData({ amount: '', category: 'BBM', notes: '', receipt: null });
        // Refresh
        const refresh = await api.get(`/driver/manifests/${id}`);
        if (refresh.data && refresh.data.data) {
          setManifest(refresh.data.data);
        }
      }
    } catch (error: any) {
      Alert.alert('Error', error?.response?.data?.message || 'Gagal upload laporan keuangan');
    } finally {
      setSubmittingExpense(false);
    }
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]} edges={['top']}>
      {/* Header */}
      <View style={[styles.header, { backgroundColor: colors.backgroundElement, borderBottomColor: colors.backgroundSelected }]}>
        <TouchableOpacity onPress={() => router.back()} style={styles.headerButton}>
          <Ionicons name="arrow-back" size={24} color={colors.text} />
        </TouchableOpacity>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Detail Penugasan</Text>
        <View style={{ width: 24 }} />
      </View>

      <ScrollView 
        contentContainerStyle={styles.scrollContent} 
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[colors.tint]} />
        }
      >
        {/* Main Info Card */}
        <View style={[styles.mainCard, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <View style={[styles.mainCardHeader, { borderBottomColor: colors.backgroundSelected }]}>
            <Text style={[styles.reportId, { color: colors.text }]}>{manifestIdStr}</Text>
            <View style={[styles.statusBadge, { backgroundColor: getStatusBgColor(manifest.status) }]}>
              <Text style={[styles.statusText, { color: getStatusColor(manifest.status) }]}>{getStatusLabel(manifest.status)}</Text>
            </View>
          </View>
          
          <View style={styles.routeContainer}>
            <View style={styles.routeIcons}>
              <Ionicons name="car" size={20} color={colors.tint} />
            </View>
            <View style={styles.routeTexts}>
              <Text style={[styles.routeOrigin, { color: colors.text }]}>Kendaraan: {manifest.vehicle?.plate_number || '-'}</Text>
              <Text style={[styles.metaText, { color: colors.textSecondary }]}>Sopir: {manifest.driver?.name || '-'}</Text>
              {(manifest.co_driver || manifest.coDriver) ? (
                <Text style={[styles.metaText, { color: colors.textSecondary, marginTop: 2 }]}>
                  Co-Sopir: {(manifest.co_driver?.name || manifest.coDriver?.name) || '-'}
                </Text>
              ) : null}
            </View>
          </View>

          <View style={[styles.metaDivider, { backgroundColor: colors.backgroundSelected }]} />

          <View style={styles.metaInfoGrid}>
            <View style={styles.metaItem}>
              <View style={[styles.metaIconBg, isDark && { backgroundColor: '#1E3A8A' }]}>
                <Ionicons name="calendar" size={16} color={colors.tint} />
              </View>
              <View style={styles.metaTextContainer}>
                <Text style={[styles.metaLabel, { color: colors.textSecondary }]}>Tanggal Penugasan</Text>
                <Text style={[styles.metaText, { color: colors.text }]}>{formatDateTime(manifest.dispatch_date)}</Text>
              </View>
            </View>
          </View>
        </View>

        {/* Task List */}
        <View style={styles.sectionContainer}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Daftar Tugas ({tasks.length})</Text>
          {tasks.length === 0 ? (
            <View style={[styles.emptyCard, { backgroundColor: colors.backgroundElement }]}>
              <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Tidak ada tugas dalam DO ini</Text>
            </View>
          ) : (
            tasks.map((task: any, index: number) => (
              <TouchableOpacity key={task.id} style={[styles.taskCard, { backgroundColor: colors.backgroundElement }]} onPress={() => router.push(`/task/${task.id}`)}>
                <View style={styles.taskHeader}>
                  <Text style={[styles.taskTypeBadge, { backgroundColor: task.task_type === 'delivery' ? '#D1FAE5' : '#DBEAFE', color: task.task_type === 'delivery' ? '#065F46' : '#1E40AF' }]}>
                    {task.task_type === 'delivery' ? 'Delivery' : 'Pickup'}
                  </Text>
                  <Text style={[styles.taskStatus, { color: getStatusColor(task.status) }]}>{getStatusLabel(task.status)}</Text>
                </View>
                <Text style={[styles.taskTitle, { color: colors.text }]}>{task.destination || task.pickup_name}</Text>
                <Text style={[styles.taskRef, { color: colors.textSecondary }]}>No: {task.reference_number || '-'}</Text>
              </TouchableOpacity>
            ))
          )}
        </View>

        {/* Laporan Operasional */}
        <View style={styles.sectionContainer}>
          <Text style={[styles.sectionTitle, { color: colors.text }]}>Laporan Operasional</Text>
          <View style={[styles.opsCard, { backgroundColor: colors.backgroundElement }]}>
            
            <TouchableOpacity style={styles.opsItem} onPress={() => setShowDepartureModal(true)}>
              <View style={[styles.opsIconBg, { backgroundColor: '#DBEAFE' }]}>
                <Ionicons name="log-out" size={20} color="#1D4ED8" />
              </View>
              <View style={styles.opsTextContainer}>
                <Text style={[styles.opsTitle, { color: colors.text }]}>Laporan Keberangkatan</Text>
                <Text style={[styles.opsDesc, { color: colors.textSecondary }]}>Informasi clock-in dan keberangkatan</Text>
              </View>
              <Ionicons name="chevron-forward" size={20} color={colors.textSecondary} />
            </TouchableOpacity>

            <View style={[styles.metaDivider, { backgroundColor: colors.backgroundSelected }]} />

            <TouchableOpacity style={styles.opsItem} onPress={() => setShowSerahTerimaModal(true)}>
              <View style={[styles.opsIconBg, { backgroundColor: '#FEF3C7' }]}>
                <Ionicons name="swap-horizontal" size={20} color="#D97706" />
              </View>
              <View style={styles.opsTextContainer}>
                <Text style={[styles.opsTitle, { color: colors.text }]}>Laporan Serah Terima</Text>
                <Text style={[styles.opsDesc, { color: colors.textSecondary }]}>Pilih tugas untuk serah terima / bukti</Text>
              </View>
              <Ionicons name="chevron-forward" size={20} color={colors.textSecondary} />
            </TouchableOpacity>

            <View style={[styles.metaDivider, { backgroundColor: colors.backgroundSelected }]} />

            <TouchableOpacity style={styles.opsItem} onPress={() => setShowPenutupModal(true)}>
              <View style={[styles.opsIconBg, { backgroundColor: '#D1FAE5' }]}>
                <Ionicons name="log-in" size={20} color="#059669" />
              </View>
              <View style={styles.opsTextContainer}>
                <Text style={[styles.opsTitle, { color: colors.text }]}>Laporan Penutup</Text>
                <Text style={[styles.opsDesc, { color: colors.textSecondary }]}>Informasi kepulangan dan odometer akhir</Text>
              </View>
              <Ionicons name="chevron-forward" size={20} color={colors.textSecondary} />
            </TouchableOpacity>
          </View>
        </View>

        {/* Laporan Keuangan */}
        <View style={styles.sectionContainer}>
          <View style={styles.sectionHeaderRow}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>Laporan Keuangan</Text>
            <TouchableOpacity style={styles.addBtn} onPress={() => setShowExpenseModal(true)}>
              <Ionicons name="add" size={16} color="#FFF" />
              <Text style={styles.addBtnText}>Upload Keuangan</Text>
            </TouchableOpacity>
          </View>
          
          {(!manifest.shift?.expenses || manifest.shift.expenses.length === 0) ? (
            <View style={[styles.emptyCard, { backgroundColor: colors.backgroundElement }]}>
              <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Belum ada pengeluaran dilaporkan.</Text>
            </View>
          ) : (
            manifest.shift.expenses.map((expense: any) => (
              <View key={expense.id} style={[styles.taskCard, { backgroundColor: colors.backgroundElement, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }]}>
                <View>
                  <Text style={[styles.taskTitle, { color: colors.text }]}>{expense.category.toUpperCase()} - Rp {parseFloat(expense.amount).toLocaleString('id-ID')}</Text>
                  {expense.notes ? <Text style={[styles.taskRef, { color: colors.textSecondary }]}>{expense.notes}</Text> : null}
                </View>
                {expense.receipt_url && (
                  <TouchableOpacity onPress={() => setSelectedImage(expense.receipt_url)}>
                    <Image source={{ uri: expense.receipt_url }} style={{ width: 50, height: 50, borderRadius: 8, backgroundColor: '#E5E7EB' }} />
                  </TouchableOpacity>
                )}
              </View>
            ))
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

      {/* Departure Modal */}
      <Modal visible={showDepartureModal} transparent={true} animationType="slide" onRequestClose={() => setShowDepartureModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.backgroundElement }]}>
            <View style={[styles.modalHeader, { borderBottomColor: colors.backgroundSelected }]}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Data Clock-in Keberangkatan</Text>
              <TouchableOpacity onPress={() => setShowDepartureModal(false)}>
                <Ionicons name="close" size={24} color={colors.text} />
              </TouchableOpacity>
            </View>
            
            <ScrollView style={styles.modalBody}>
              {manifest.shift ? (
                <>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Waktu Clock-in:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{formatDateTime(manifest.shift.check_in_at)}</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Odometer Awal:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.start_odometer} km</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>BBM Awal:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.start_fuel ? manifest.shift.start_fuel + '%' : '-'}</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Catatan Keberangkatan:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.notes || '-'}</Text>
                  </View>

                  {manifest.shift.start_evidence_photo && (
                    <View style={{ marginTop: 16 }}>
                      <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8 }]}>Foto Bukti Kendaraan:</Text>
                      <TouchableOpacity onPress={() => setSelectedImage(manifest.shift.start_evidence_photo)}>
                        <Image source={{ uri: manifest.shift.start_evidence_photo }} style={{ width: '100%', height: 200, borderRadius: 8, backgroundColor: '#E5E7EB' }} resizeMode="cover" />
                      </TouchableOpacity>
                    </View>
                  )}
                </>
              ) : (
                <View style={{ padding: 20, alignItems: 'center' }}>
                  <Ionicons name="warning-outline" size={48} color="#F59E0B" />
                  <Text style={{ textAlign: 'center', marginTop: 12, color: colors.textSecondary, fontFamily: 'Inter-Medium' }}>
                    Data clock-in (shift) belum tersedia atau tidak ditemukan untuk penugasan ini.
                  </Text>
                </View>
              )}
            </ScrollView>
          </View>
        </View>
      </Modal>

      {/* Serah Terima Modal */}
      <Modal visible={showSerahTerimaModal} transparent={true} animationType="slide" onRequestClose={() => setShowSerahTerimaModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.backgroundElement }]}>
            <View style={[styles.modalHeader, { borderBottomColor: colors.backgroundSelected }]}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Pilih Serah Terima Tugas</Text>
              <TouchableOpacity onPress={() => setShowSerahTerimaModal(false)}>
                <Ionicons name="close" size={24} color={colors.text} />
              </TouchableOpacity>
            </View>
            
            <ScrollView style={styles.modalBody}>
              <Text style={{ color: colors.textSecondary, marginBottom: 16, fontFamily: 'Inter-Medium' }}>
                Silakan pilih tugas di bawah ini untuk melihat bukti atau mengisi Serah Terima:
              </Text>
              {tasks.map((task: any, index: number) => {
                const isCompleted = task.status === 'delivered' || task.status === 'completed';
                const isPending = task.status === 'pending';
                const isFailed = task.status === 'failed';
                const isExpanded = !!expandedTasks[task.id];
                
                let cardBorderColor: string = colors.backgroundSelected;
                let statusColor = getStatusColor(task.status);
                if (isCompleted) cardBorderColor = '#10B981';
                else if (isPending) cardBorderColor = '#F59E0B';
                else if (isFailed) cardBorderColor = '#EF4444';

                let actionText = 'Proses Serah Terima';
                let actionColor = '#0756C6';
                if (isCompleted) {
                  actionText = 'Lihat Bukti Serah Terima';
                  actionColor = '#10B981';
                } else if (isPending) {
                  actionText = 'Lihat Laporan Kendala';
                  actionColor = '#F59E0B';
                } else if (isFailed) {
                  actionText = 'Lihat Laporan Gagal';
                  actionColor = '#EF4444';
                }

                return (
                  <View key={task.id} style={[styles.taskCard, { backgroundColor: colors.background, borderColor: cardBorderColor }]}>
                    <TouchableOpacity 
                      onPress={() => {
                        setExpandedTasks(prev => ({ ...prev, [task.id]: !prev[task.id] }));
                      }}
                      activeOpacity={0.7}
                    >
                      <View style={styles.taskHeader}>
                      <Text style={[styles.taskTypeBadge, { backgroundColor: task.task_type === 'delivery' ? '#D1FAE5' : '#DBEAFE', color: task.task_type === 'delivery' ? '#065F46' : '#1E40AF' }]}>
                        {task.task_type === 'delivery' ? 'Delivery' : 'Pickup'}
                      </Text>
                      {isCompleted ? (
                        <Text style={[styles.taskStatus, { color: '#10B981' }]}>Sudah Selesai</Text>
                      ) : (
                        <Text style={[styles.taskStatus, { color: statusColor }]}>{getStatusLabel(task.status)}</Text>
                      )}
                    </View>
                    <Text style={[styles.taskTitle, { color: colors.text }]}>{task.destination || task.pickup_name}</Text>
                    <Text style={[styles.taskRef, { color: colors.textSecondary }]}>No: {task.reference_number || '-'}</Text>
                    
                    <View style={{ marginTop: 12, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
                      <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                        <Ionicons name="arrow-forward-circle" size={20} color={actionColor} />
                        <Text style={{ marginLeft: 6, color: actionColor, fontFamily: 'Inter-SemiBold', fontSize: 13 }}>
                          {actionText}
                        </Text>
                      </View>
                      <Ionicons name={isExpanded ? "chevron-up" : "chevron-down"} size={20} color={colors.textSecondary} />
                    </View>
                    </TouchableOpacity>

                    {/* Accordion Content */}
                    {isExpanded && (
                      <View style={{ marginTop: 16, paddingTop: 16, borderTopWidth: 1, borderTopColor: colors.backgroundSelected }}>
                        {isCompleted ? (
                          <>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Penerima:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.receiver_name || '-'}</Text>
                            </View>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Jabatan:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.receiver_role || '-'}</Text>
                            </View>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Kondisi Barang:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.item_condition || '-'}</Text>
                            </View>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Odometer Akhir:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.completed_odometer || '-'} km</Text>
                            </View>
                            
                            <Text style={[styles.infoLabel, { color: colors.textSecondary, marginTop: 12, marginBottom: 8 }]}>Foto Bukti:</Text>
                            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8 }}>
                              {task.attachments && task.attachments.length > 0 ? (
                                task.attachments
                                  .filter((att: any) => att.category === 'bukti_serah_terima')
                                  .map((att: any, idx: number) => {
                                    const baseStorageUrl = (api.defaults.baseURL || '').replace('/api', '') + '/storage/';
                                    const pathStr = att.file_url || att.file_path || '';
                                    const fileUrl = pathStr.startsWith('http') ? pathStr : `${baseStorageUrl}${pathStr}`;
                                    return (
                                      <TouchableOpacity key={idx} onPress={() => setSelectedImage(fileUrl)}>
                                        <Image source={{ uri: fileUrl }} style={{ width: 80, height: 80, borderRadius: 8, backgroundColor: '#E5E7EB' }} resizeMode="cover" />
                                      </TouchableOpacity>
                                    );
                                  })
                              ) : (
                                <Text style={{ color: colors.textSecondary, fontStyle: 'italic', fontSize: 13 }}>Tidak ada foto bukti</Text>
                              )}
                            </View>
                          </>
                        ) : isPending ? (
                          <>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Odometer Akhir:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.completed_odometer || '-'} km</Text>
                            </View>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Catatan Kendala:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.arrival_notes || task.failure_reason || '-'}</Text>
                            </View>
                            
                            <Text style={[styles.infoLabel, { color: colors.textSecondary, marginTop: 12, marginBottom: 8 }]}>Foto Bukti Kendala:</Text>
                            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8 }}>
                              {task.attachments && task.attachments.length > 0 ? (
                                task.attachments
                                  .filter((att: any) => att.category === 'bukti_kedatangan' || att.category === 'bukti_kendala')
                                  .map((att: any, idx: number) => {
                                    const baseStorageUrl = (api.defaults.baseURL || '').replace('/api', '') + '/storage/';
                                    const pathStr = att.file_url || att.file_path || '';
                                    const fileUrl = pathStr.startsWith('http') ? pathStr : `${baseStorageUrl}${pathStr}`;
                                    return (
                                      <TouchableOpacity key={idx} onPress={() => setSelectedImage(fileUrl)}>
                                        <Image source={{ uri: fileUrl }} style={{ width: 80, height: 80, borderRadius: 8, backgroundColor: '#E5E7EB' }} resizeMode="cover" />
                                      </TouchableOpacity>
                                    );
                                  })
                              ) : (
                                <Text style={{ color: colors.textSecondary, fontStyle: 'italic', fontSize: 13 }}>Tidak ada foto bukti</Text>
                              )}
                            </View>
                          </>
                        ) : isFailed ? (
                          <>
                            <View style={styles.infoRow}>
                              <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Catatan Gagal:</Text>
                              <Text style={[styles.infoValue, { color: colors.text }]}>{task.arrival_notes || task.failure_reason || 'Tugas tidak terkirim (kosong)'}</Text>
                            </View>

                            <Text style={[styles.infoLabel, { color: colors.textSecondary, marginTop: 12, marginBottom: 8 }]}>Foto Bukti Gagal:</Text>
                            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8 }}>
                              {task.attachments && task.attachments.length > 0 ? (
                                task.attachments
                                  .filter((att: any) => att.category === 'bukti_kedatangan' || att.category === 'bukti_kendala')
                                  .map((att: any, idx: number) => {
                                    const baseStorageUrl = (api.defaults.baseURL || '').replace('/api', '') + '/storage/';
                                    const pathStr = att.file_url || att.file_path || '';
                                    const fileUrl = pathStr.startsWith('http') ? pathStr : `${baseStorageUrl}${pathStr}`;
                                    return (
                                      <TouchableOpacity key={idx} onPress={() => setSelectedImage(fileUrl)}>
                                        <Image source={{ uri: fileUrl }} style={{ width: 80, height: 80, borderRadius: 8, backgroundColor: '#E5E7EB' }} resizeMode="cover" />
                                      </TouchableOpacity>
                                    );
                                  })
                              ) : (
                                <Text style={{ color: colors.textSecondary, fontStyle: 'italic', fontSize: 13 }}>Tidak ada foto bukti</Text>
                              )}
                            </View>
                          </>
                        ) : (
                          <View style={{ paddingVertical: 12, alignItems: 'center' }}>
                            <Text style={{ color: colors.textSecondary, textAlign: 'center', marginBottom: 12 }}>
                              Tugas ini belum selesai. Anda bisa mengisi serah terima melalui detail tugas.
                            </Text>
                            <TouchableOpacity 
                              style={[styles.primaryBtn, { paddingVertical: 8, paddingHorizontal: 16 }]} 
                              onPress={() => {
                                setShowSerahTerimaModal(false);
                                router.push(`/task/${task.id}`);
                              }}
                            >
                              <Text style={styles.primaryBtnText}>Buka Detail Tugas</Text>
                            </TouchableOpacity>
                          </View>
                        )}
                      </View>
                    )}
                  </View>
                );
              })}
            </ScrollView>
          </View>
        </View>
      </Modal>

      {/* Penutup Modal */}
      <Modal visible={showPenutupModal} transparent={true} animationType="slide" onRequestClose={() => setShowPenutupModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.backgroundElement }]}>
            <View style={[styles.modalHeader, { borderBottomColor: colors.backgroundSelected }]}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Data Clock-out Kepulangan</Text>
              <TouchableOpacity onPress={() => setShowPenutupModal(false)}>
                <Ionicons name="close" size={24} color={colors.text} />
              </TouchableOpacity>
            </View>
            
            <ScrollView style={styles.modalBody}>
              {manifest.shift && manifest.shift.status === 'completed' ? (
                <>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Waktu Clock-out:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{formatDateTime(manifest.shift.check_out_at)}</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Odometer Awal:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.start_odometer} km</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Odometer Akhir:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.end_odometer} km</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Total Jarak Tempuh:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>
                      {(manifest.shift.end_odometer && manifest.shift.start_odometer) 
                        ? (Number(manifest.shift.end_odometer) - Number(manifest.shift.start_odometer)) 
                        : '-'} km
                    </Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>BBM Akhir:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>{manifest.shift.end_fuel ? manifest.shift.end_fuel + '%' : '-'}</Text>
                  </View>
                  <View style={styles.infoRow}>
                    <Text style={[styles.infoLabel, { color: colors.textSecondary }]}>Sisa Saldo E-Toll:</Text>
                    <Text style={[styles.infoValue, { color: colors.text }]}>
                      {manifest.shift.etoll_balance ? 'Rp ' + manifest.shift.etoll_balance.toLocaleString('id-ID') : '-'}
                    </Text>
                  </View>

                  {manifest.shift.end_evidence_photo && (
                    <View style={{ marginTop: 16 }}>
                      <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8 }]}>Foto Bukti Kendaraan (Akhir):</Text>
                      <TouchableOpacity onPress={() => setSelectedImage(manifest.shift.end_evidence_photo)}>
                        <Image source={{ uri: manifest.shift.end_evidence_photo }} style={{ width: '100%', height: 200, borderRadius: 8, backgroundColor: '#E5E7EB' }} resizeMode="cover" />
                      </TouchableOpacity>
                    </View>
                  )}
                </>
              ) : (
                <View style={{ padding: 20, alignItems: 'center' }}>
                  <Ionicons name="warning-outline" size={48} color="#F59E0B" />
                  <Text style={{ textAlign: 'center', marginTop: 12, color: colors.textSecondary, fontFamily: 'Inter-Medium' }}>
                    Anda belum menyelesaikan Shift (Clock Out) untuk penugasan ini.
                  </Text>
                  <Text style={{ textAlign: 'center', marginTop: 8, color: colors.textSecondary, fontSize: 13 }}>
                    Silakan akhiri shift di menu Dashboard setelah semua tugas selesai.
                  </Text>
                </View>
              )}
            </ScrollView>
          </View>
        </View>
      </Modal>

      {/* Expense Modal */}
      <Modal visible={showExpenseModal} transparent={true} animationType="slide" onRequestClose={() => setShowExpenseModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: colors.backgroundElement }]}>
            <View style={[styles.modalHeader, { borderBottomColor: colors.backgroundSelected }]}>
              <Text style={[styles.modalTitle, { color: colors.text }]}>Upload Laporan Keuangan</Text>
              <TouchableOpacity onPress={() => setShowExpenseModal(false)}>
                <Ionicons name="close" size={24} color={colors.text} />
              </TouchableOpacity>
            </View>
            
            <ScrollView style={styles.modalBody}>
              <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8 }]}>Kategori:</Text>
              <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 16 }}>
                {['BBM', 'Tol', 'Parkir', 'Lainnya'].map(cat => (
                  <TouchableOpacity 
                    key={cat}
                    style={{ 
                      paddingHorizontal: 16, paddingVertical: 8, 
                      borderRadius: 20, 
                      backgroundColor: expenseData.category === cat ? '#0756C6' : colors.backgroundSelected 
                    }}
                    onPress={() => setExpenseData({...expenseData, category: cat})}
                  >
                    <Text style={{ color: expenseData.category === cat ? '#FFF' : colors.text }}>{cat}</Text>
                  </TouchableOpacity>
                ))}
              </View>

              <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8 }]}>Nominal (Rp):</Text>
              <TextInput 
                style={[styles.input, { backgroundColor: colors.background, color: colors.text, borderColor: colors.backgroundSelected }]}
                keyboardType="numeric"
                value={expenseData.amount}
                onChangeText={(val) => setExpenseData({...expenseData, amount: val})}
                placeholder="Contoh: 50000"
                placeholderTextColor={colors.textSecondary}
              />

              <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8, marginTop: 16 }]}>Keterangan (Opsional):</Text>
              <TextInput 
                style={[styles.input, { backgroundColor: colors.background, color: colors.text, borderColor: colors.backgroundSelected }]}
                value={expenseData.notes}
                onChangeText={(val) => setExpenseData({...expenseData, notes: val})}
                placeholder="Catatan tambahan"
                placeholderTextColor={colors.textSecondary}
              />

              <Text style={[styles.infoLabel, { color: colors.textSecondary, marginBottom: 8, marginTop: 16 }]}>Foto Bukti (Struk/Kwitansi):</Text>
              {expenseData.receipt ? (
                <View style={{ position: 'relative', marginBottom: 24 }}>
                  <Image source={{ uri: expenseData.receipt.uri }} style={{ width: '100%', height: 200, borderRadius: 8 }} />
                  <TouchableOpacity 
                    style={{ position: 'absolute', top: 8, right: 8, backgroundColor: 'rgba(0,0,0,0.5)', padding: 8, borderRadius: 20 }}
                    onPress={() => setExpenseData({...expenseData, receipt: null})}
                  >
                    <Ionicons name="trash" size={20} color="#FFF" />
                  </TouchableOpacity>
                </View>
              ) : (
                <TouchableOpacity 
                  style={{ width: '100%', height: 150, borderRadius: 8, borderWidth: 1, borderStyle: 'dashed', borderColor: colors.backgroundSelected, alignItems: 'center', justifyContent: 'center', marginBottom: 24 }}
                  onPress={pickReceipt}
                >
                  <Ionicons name="camera" size={32} color={colors.textSecondary} />
                  <Text style={{ color: colors.textSecondary, marginTop: 8, fontFamily: 'Inter-Medium' }}>Ambil Foto Bukti</Text>
                </TouchableOpacity>
              )}

              <TouchableOpacity 
                style={[styles.primaryBtn, { paddingVertical: 14, opacity: submittingExpense ? 0.7 : 1 }]} 
                onPress={submitExpense}
                disabled={submittingExpense}
              >
                {submittingExpense ? (
                  <ActivityIndicator color="#FFF" />
                ) : (
                  <Text style={styles.primaryBtnText}>Simpan Pengeluaran</Text>
                )}
              </TouchableOpacity>
            </ScrollView>
          </View>
        </View>
      </Modal>

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
  routeContainer: {
    flexDirection: 'row',
    marginBottom: 16,
    alignItems: 'center',
  },
  routeIcons: {
    alignItems: 'center',
    marginRight: 12,
  },
  routeTexts: {
    flex: 1,
  },
  routeOrigin: {
    fontSize: 15,
    fontFamily: 'Inter-SemiBold',
    color: '#1F2937',
    marginBottom: 4,
  },
  metaDivider: {
    height: 1,
    backgroundColor: '#F3F4F6',
    marginVertical: 12,
  },
  metaInfoGrid: {
    flexDirection: 'column',
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  metaIconBg: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: '#DBEAFE',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  metaTextContainer: {
    flex: 1,
  },
  metaLabel: {
    fontSize: 11,
    fontFamily: 'Inter-Medium',
    color: '#6B7280',
    marginBottom: 2,
  },
  metaText: {
    fontSize: 13,
    fontFamily: 'Inter-SemiBold',
    color: '#111827',
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
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
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
  taskCard: {
    backgroundColor: '#FFF',
    padding: 16,
    borderRadius: 12,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  taskHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  taskTypeBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
    fontSize: 11,
    fontFamily: 'Inter-SemiBold',
    overflow: 'hidden'
  },
  taskStatus: {
    fontSize: 12,
    fontFamily: 'Inter-Medium',
  },
  taskTitle: {
    fontSize: 14,
    fontFamily: 'Inter-SemiBold',
    marginBottom: 4,
  },
  taskRef: {
    fontSize: 12,
    fontFamily: 'Inter-Regular',
  },
  opsCard: {
    backgroundColor: '#FFF',
    borderRadius: 12,
    padding: 16,
  },
  opsItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
  },
  opsIconBg: {
    width: 40,
    height: 40,
    borderRadius: 20,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  opsTextContainer: {
    flex: 1,
  },
  opsTitle: {
    fontSize: 14,
    fontFamily: 'Inter-SemiBold',
    marginBottom: 2,
  },
  opsDesc: {
    fontSize: 12,
    fontFamily: 'Inter-Regular',
  },
  addBtn: {
    flexDirection: 'row',
    backgroundColor: '#0756C6',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    alignItems: 'center',
  },
  addBtnText: {
    color: '#FFF',
    fontFamily: 'Inter-Medium',
    fontSize: 12,
    marginLeft: 4,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '80%',
    paddingBottom: 20,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
    borderBottomWidth: 1,
  },
  modalTitle: {
    fontSize: 18,
    fontFamily: 'Inter-Bold',
  },
  modalBody: {
    padding: 20,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  infoLabel: {
    fontSize: 14,
    fontFamily: 'Inter-Medium',
  },
  infoValue: {
    fontSize: 14,
    fontFamily: 'Inter-SemiBold',
    flex: 1,
    textAlign: 'right',
    marginLeft: 16,
  },
  primaryBtn: {
    backgroundColor: '#0756C6',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  primaryBtnText: {
    color: '#FFF',
    fontFamily: 'Inter-SemiBold',
    fontSize: 14,
  },
  input: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontFamily: 'Inter-Regular',
    fontSize: 14,
  }
});

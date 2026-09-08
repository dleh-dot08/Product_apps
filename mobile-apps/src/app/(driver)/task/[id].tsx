import { Text } from '@/components/CustomText';
import React, { useCallback, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Image,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Platform, Linking } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

let MapView: any = null;
let Marker: any = null;

if (Platform.OS !== 'web') {
  try {
    const Maps = require('react-native-maps');
    MapView = Maps.default || Maps;
    Marker = Maps.Marker;
  } catch (e) {
    console.log('Error loading react-native-maps:', e);
  }
}

const safeFormatDate = (dateString?: string | null) => {
  if (!dateString) return '-';
  try {
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return '-';
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  } catch (e) { return '-'; }
};

const safeFormatTime = (dateString?: string | null) => {
  if (!dateString) return '-';
  try {
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return '-';
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')} WIB`;
  } catch (e) { return '-'; }
};
import { Colors } from '@/constants/theme';
import { useTheme } from '../../../context/ThemeContext';
import api from '../../../services/api';
import {
  ModalKeberangkatan,
  ModalPengeluaran,
  ModalTiba,
  ModalSerahTerima
} from '@/components/task/TaskModals';
import { startLocationTracking, stopLocationTracking } from '../../../services/LocationService';

const BRAND = {
  primary: '#0756C6',
  primaryDark: '#063B8C',
  primarySoft: '#EAF3FF',
  teal: '#0F9FA8',
  tealSoft: '#E7F8F8',
  success: '#16A36A',
  successSoft: '#EAF8F1',
  warning: '#F97316',
  warningSoft: '#FFF2E8',
  violet: '#6D48D7',
  violetSoft: '#F2ECFF',
  danger: '#EF4444',
  white: '#FFFFFF',
  page: '#F5F7FB',
  text: '#0F172A',
  textSecondary: '#475569',
  muted: '#64748B',
  subtle: '#94A3B8',
  border: '#E5EAF1',
  borderSoft: '#EDF1F6',
};

type TaskStatus = 'assigned' | 'on_route' | 'arrived' | 'delivered' | 'failed' | 'cancelled' | string;

type TaskItemDetail = {
  id: number | string;
  item_number?: string | null;
  item_description?: string | null;
  quantity?: number | string | null;
  unit?: string | null;
};

type TaskDetail = {
  id: number | string;
  reference_number?: string | null;
  task_type?: string | null;
  status?: TaskStatus;
  pickup_name?: string | null;
  pickup_location?: string | null;
  destination?: string | null;
  destination_name?: string | null;
  assigned_at?: string | null;

  vehicle?: {
    plate_number?: string | null;
    vehicle_name?: string | null;
    name?: string | null;
  } | null;

  driver?: {
    id?: number | string | null;
    name?: string | null;
    full_name?: string | null;
    employee_id?: string | null;
  } | null;

  item_number?: string | null;
  item_description?: string | null;
  quantity?: string | number | null;
  unit?: string | null;
  dispatch_date?: string | null;
  estimated_arrival?: string | null;
  proof_photo?: string | null;
  failure_reason?: string | null;
  completed_odometer?: number | null;
  notes?: string | null;

  priority?: string | null;
  pickup_pic_name?: string | null;
  pickup_point?: string | null;
  destination_pic_name?: string | null;
  destination_point?: string | null;
  
  // Delivery specific fields
  delivery_pickup_name?: string | null;
  delivery_sender_pic?: string | null;
  delivery_origin_point?: string | null;
  delivery_pickup_location?: string | null;
  customer_name?: string | null;
  delivery_receiver_pic?: string | null;
  delivery_target_point?: string | null;
  pickup_destination?: string | null;

  sales_order?: {
    customer_name?: string | null;
    source_data?: { address?: string | null; };
  } | null;

  assigner?: {
    name?: string | null;
    full_name?: string | null;
    phone?: string | null;
  } | null;

  assigned_by?: {
    name?: string | null;
    full_name?: string | null;
    phone?: string | null;
  } | null;

  items?: TaskItemDetail[] | null;
};

class ErrorBoundary extends React.Component<any, { hasError: boolean, error: any }> {
  constructor(props: any) {
    super(props);
    this.state = { hasError: false, error: null };
  }

  static getDerivedStateFromError(error: any) {
    return { hasError: true, error };
  }

  componentDidCatch(error: any, errorInfo: any) {
    console.error("ErrorBoundary caught an error", error, errorInfo);
  }

  render() {
    if (this.state.hasError) {
      return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', padding: 20, backgroundColor: '#FFF' }}>
          <Text style={{ color: 'red', fontSize: 18, fontWeight: 'bold' }}>Terjadi Kesalahan (Crash)</Text>
          <Text style={{ color: 'black', marginTop: 10, textAlign: 'center' }}>{String(this.state.error)}</Text>
        </View>
      );
    }
    return this.props.children;
  }
}

function TaskDetailScreenContent() {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const { theme } = useTheme();

  const colors = Colors[theme];
  const isDark = theme === 'dark';

  const [task, setTask] = useState<TaskDetail | null>(null);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  const [modalKeberangkatanVisible, setModalKeberangkatanVisible] = useState(false);
  const [modalPengeluaranVisible, setModalPengeluaranVisible] = useState(false);
  const [modalTibaVisible, setModalTibaVisible] = useState(false);
  const [modalSerahTerimaVisible, setModalSerahTerimaVisible] = useState(false);

  const pageBackground = isDark ? colors.background : BRAND.page;
  const cardBackground = isDark ? colors.backgroundElement : BRAND.white;
  const textColor = colors.text;
  const textMuted = colors.textSecondary;
  const borderColor = isDark ? colors.backgroundSelected : BRAND.border;

  const fetchDetail = useCallback(async () => {
    if (!id) return;
    try {
      setLoading(true);
      const response = await api.get(`/pickup/${id}`);
      if (response.data?.status === 'success') {
        setTask(response.data.data);
      } else {
        Alert.alert('Error', 'Gagal memuat detail tugas');
      }
    } catch (error) {
      console.error('Failed to fetch detail', error);
      Alert.alert('Error', 'Terjadi kesalahan jaringan');
    } finally {
      setLoading(false);
    }
  }, [id]);

  useEffect(() => {
    if (id) {
      fetchDetail();
    }
  }, [fetchDetail, id]);

  const handleAction = async (newStatus: string, payload?: any) => {
    try {
      setActionLoading(true);

      const formData = new FormData();
      formData.append('status', newStatus);
      formData.append('_method', 'PATCH');

      if (payload) {
        const keys = Object.keys(payload);
        for (const key of keys) {
          if (payload[key] === undefined || payload[key] === null || payload[key] === '') continue;

          // Checklist → kirim sebagai JSON string
          if (key === 'checklist_status') {
            formData.append('departure_checklist', JSON.stringify(payload[key]));
            continue;
          }

          if (key === 'arrival_checklist') {
            formData.append('arrival_checklist', JSON.stringify(payload[key]));
            continue;
          }

          // Attachments → kirim sebagai file array
          if (key === 'attachments') {
            const uris: string[] = (payload[key] as string[]).filter(Boolean);
            for (let idx = 0; idx < uris.length; idx++) {
              const uri = uris[idx];
              const filename = uri.split('/').pop() || `attachment_${idx}.jpg`;
              const match = /\.(\w+)$/.exec(filename);
              const mimeType = match ? `image/${match[1]}` : 'image/jpeg';

              if (Platform.OS === 'web') {
                // Web: convert blob URI ke Blob object
                try {
                  const resp = await fetch(uri);
                  const blob = await resp.blob();
                  formData.append('attachments[]', blob, filename);
                } catch (e) {
                  console.warn('Gagal convert blob:', e);
                }
              } else {
                // Native: gunakan {uri, name, type} pattern
                formData.append('attachments[]', { uri, name: filename, type: mimeType } as any);
              }
            }
            continue;
          }

          // File fields (legacy single-file categories)
          const fileFields = [
            'keberangkatan_depan', 'keberangkatan_muatan', 'keberangkatan_surat',
            'tiba_lokasi', 'tiba_gudang',
            'serah_terima_barang', 'serah_terima_penerima', 'serah_terima_surat', 'serah_terima_ttd'
          ];

          if (fileFields.includes(key)) {
            const uri = payload[key];
            const filename = uri.split('/').pop() || 'image.jpg';
            const match = /\.(\w+)$/.exec(filename);
            const mimeType = match ? `image/${match[1]}` : `image/jpeg`;

            if (Platform.OS === 'web') {
              try {
                const resp = await fetch(uri);
                const blob = await resp.blob();
                formData.append(key, blob, filename);
              } catch (e) {
                console.warn('Gagal convert blob:', e);
              }
            } else {
              formData.append(key, { uri, name: filename, type: mimeType } as any);
            }
          } else {
            formData.append(key, payload[key]);
          }
        }
      }

      const res = await api.post(`/pickup/${id}/status`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      if (res.data?.status === 'success' || res.status === 200) {
        Alert.alert('Berhasil', 'Status tugas diperbarui');

        // Mulai / Hentikan tracking GPS berdasarkan status tugas
        if (newStatus === 'on_route') {
          await startLocationTracking(String(id));
        } else if (newStatus === 'delivered' || newStatus === 'failed' || newStatus === 'cancelled') {
          await stopLocationTracking();
        }

        fetchDetail(); // Reload data
      }
    } catch (error) {
      console.error('Update status error:', error);
      Alert.alert('Gagal', 'Tidak dapat memperbarui status');
    } finally {
      setActionLoading(false);
    }
  };

  const handleExpenseSubmit = async (payload: any) => {
    try {
      setLoading(true);
      const formData = new FormData();

      const keys = Object.keys(payload);
      for (const key of keys) {
        if (key === 'receipt' && payload[key]) {
          const uri = payload[key];
          const filename = uri.split('/').pop() || 'receipt.jpg';
          const match = /\.(\w+)$/.exec(filename);
          const mimeType = match ? `image/${match[1]}` : `image/jpeg`;

          if (Platform.OS === 'web') {
            try {
              const resp = await fetch(uri);
              const blob = await resp.blob();
              formData.append('receipt', blob, filename);
            } catch (e) {
              console.warn('Gagal convert blob:', e);
            }
          } else {
            formData.append('receipt', { uri, name: filename, type: mimeType } as any);
          }
        } else if (payload[key] !== null && payload[key] !== undefined) {
          formData.append(key, payload[key]);
        }
      }

      const res = await api.post(`/pickup/${id}/expenses`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      if (res.data?.status === 'success' || res.status === 201) {
        Alert.alert('Berhasil', 'Pengeluaran berhasil disimpan');
      }
    } catch (error: any) {
      console.error('Submit expense error:', error);
      const msg = error.response?.data?.message || 'Tidak dapat menyimpan pengeluaran';
      Alert.alert('Gagal', msg);
    } finally {
      setLoading(false);
    }
  };

  if (loading || !task) {
    return (
      <View style={[styles.loadingContainer, { backgroundColor: pageBackground }]}>
        <ActivityIndicator size="large" color={BRAND.primary} />
      </View>
    );
  }

  const status = task.status ?? 'assigned';
  let statusLabel = status;
  let statusColor = BRAND.text;
  let statusBg = BRAND.border;

  switch (status) {
    case 'assigned':
      statusLabel = 'Menunggu';
      statusBg = BRAND.warningSoft;
      statusColor = BRAND.warning;
      break;
    case 'on_route':
    case 'arrived':
      statusLabel = 'Berjalan';
      statusBg = BRAND.primarySoft;
      statusColor = BRAND.primary;
      break;
    case 'delivered':
      statusLabel = 'Selesai';
      statusBg = BRAND.successSoft;
      statusColor = BRAND.success;
      break;
  }

  const isPickup = task.task_type === 'pickup';

  const openMap = () => {
    let query = '';
    if (isPickup) {
      query = task.pickup_location || task.pickup_name || '';
    } else {
      query = task.sales_order?.source_data?.address || task.destination || task.sales_order?.customer_name || '';
    }
    const encodedQuery = encodeURIComponent(query);
    const url = Platform.select({
      ios: `maps:0,0?q=${encodedQuery}`,
      android: `geo:0,0?q=${encodedQuery}`,
      default: `https://www.google.com/maps/search/?api=1&query=${encodedQuery}`
    });
    if (url) Linking.openURL(url);
  };

  // Bottom action button logic
  let btnLabel = '';
  let btnAction = () => { };
  if (status === 'assigned') {
    btnLabel = 'Mulai Perjalanan';
    btnAction = () => setModalKeberangkatanVisible(true);
  } else if (status === 'on_route') {
    btnLabel = 'Selesaikan Perjalanan';
    btnAction = () => setModalTibaVisible(true);
  } else if (status === 'arrived') {
    btnLabel = 'Bukti Serah Terima';
    btnAction = () => setModalSerahTerimaVisible(true);
  }

  return (
    <View style={[styles.container, { backgroundColor: pageBackground }]}>
      {/* Blue Header Area */}
      <LinearGradient
        colors={[
          'rgba(2, 43, 103, 0.95)',
          'rgba(5, 79, 183, 1)',
          '#0756C6',
        ]}
        locations={[0, 0.5, 1]}
        style={styles.headerArea}
      >
        <View style={styles.headerTop}>
          <TouchableOpacity onPress={() => router.canGoBack() && router.back()} style={styles.headerIcon}>
            <Ionicons name="arrow-back" size={24} color="#FFF" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Detail Tugas</Text>
          <TouchableOpacity style={styles.headerIcon}>
            <Ionicons name="ellipsis-vertical" size={24} color="#FFF" />
          </TouchableOpacity>
        </View>
      </LinearGradient>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={[styles.card, { backgroundColor: cardBackground, borderColor }]}>

          <View style={styles.cardHeader}>
            <Text style={[styles.taskRef, { color: textColor }]}>
              {task.reference_number || '-'}
            </Text>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
              {task.priority && task.priority !== 'normal' && (
                <View style={[styles.statusBadge, { backgroundColor: BRAND.danger + '20' }]}>
                  <Text style={[styles.statusBadgeText, { color: BRAND.danger, textTransform: 'capitalize' }]}>{task.priority}</Text>
                </View>
              )}
              <View style={[styles.statusBadge, { backgroundColor: statusBg }]}>
                <Text style={[styles.statusBadgeText, { color: statusColor }]}>{statusLabel}</Text>
              </View>
            </View>
          </View>
          <Text style={[styles.taskSubtitle, { color: textMuted }]}>
            {task.pickup_name || '-'} <Ionicons name="arrow-forward" /> {task.destination_name || (task.sales_order ? task.sales_order.customer_name : task.destination) || '-'}
          </Text>

          <View style={styles.dateTimeRow}>
            <Ionicons name="calendar-outline" size={14} color={textMuted} />
            <Text style={[styles.dateTimeText, { color: textMuted }]}>
              {safeFormatDate(task.dispatch_date || task.assigned_at)}
            </Text>
            <Ionicons name="time-outline" size={14} color={textMuted} style={{ marginLeft: 12 }} />
            <Text style={[styles.dateTimeText, { color: textMuted }]}>
              {safeFormatTime(task.dispatch_date || task.assigned_at)}
            </Text>
          </View>

          <View style={styles.divider} />

          {/* Timeline Route */}
          <View style={styles.timelineRow}>
            <View style={styles.timelineIconCol}>
              <View style={styles.timelineIconWrapper}>
                <Ionicons name="location" size={16} color={BRAND.primary} />
              </View>
              <View style={[styles.timelineLine, { backgroundColor: BRAND.primarySoft }]} />
              <View style={[styles.timelineIconWrapper, { backgroundColor: BRAND.tealSoft }]}>
                <Ionicons name="flag" size={16} color={BRAND.teal} />
              </View>
            </View>
            <View style={styles.timelineContentCol}>
              <View style={styles.timelineItem}>
                <View style={styles.timelineItemHeader}>
                  <Text style={[styles.timelineTitle, { color: BRAND.primary }]}>Lokasi {isPickup ? 'Pickup' : 'Awal'}</Text>
                  <Text style={[styles.timelineTime, { color: textMuted }]}>
                    {safeFormatDate(task.dispatch_date || task.assigned_at)} {safeFormatTime(task.dispatch_date || task.assigned_at)}
                  </Text>
                </View>
                <Text style={[styles.timelineLocName, { color: textColor }]}>
                  {isPickup ? task.pickup_name : task.pickup_name || '-'} 
                  {isPickup ? (task.pickup_point ? ` (${task.pickup_point})` : '') : (task.delivery_origin_point ? ` (${task.delivery_origin_point})` : '')}
                </Text>
                {(isPickup ? task.pickup_pic_name : task.delivery_sender_pic) && (
                  <Text style={[styles.timelineAddress, { color: textColor, fontWeight: '600' }]}>
                    PIC: {isPickup ? task.pickup_pic_name : task.delivery_sender_pic}
                  </Text>
                )}
                <Text style={[styles.timelineAddress, { color: textMuted }]}>
                  {isPickup ? task.pickup_location : task.pickup_location || '-'}
                </Text>
              </View>
              
              <View style={styles.timelineItem}>
                <View style={styles.timelineItemHeader}>
                  <Text style={[styles.timelineTitle, { color: BRAND.primary }]}>Lokasi Dropoff</Text>
                  <Text style={[styles.timelineTime, { color: textMuted }]}>
                    {task.estimated_arrival ? `${safeFormatDate(task.estimated_arrival)} ${safeFormatTime(task.estimated_arrival)}` : 'Estimasi'}
                  </Text>
                </View>
                <Text style={[styles.timelineLocName, { color: textColor }]}>
                  {isPickup 
                    ? task.destination_name || (task.sales_order ? task.sales_order.customer_name : task.destination) 
                    : task.destination || (task.sales_order ? task.sales_order.customer_name : '-') || '-'}
                  {isPickup ? (task.destination_point ? ` (${task.destination_point})` : '') : (task.delivery_target_point ? ` (${task.delivery_target_point})` : '')}
                </Text>
                {(isPickup ? task.destination_pic_name : task.delivery_receiver_pic) && (
                  <Text style={[styles.timelineAddress, { color: textColor, fontWeight: '600' }]}>
                    PIC: {isPickup ? task.destination_pic_name : task.delivery_receiver_pic}
                  </Text>
                )}
                <Text style={[styles.timelineAddress, { color: textMuted }]}>
                  {isPickup 
                    ? task.pickup_destination || task.destination || '-' 
                    : task.sales_order && task.sales_order.source_data?.address ? task.sales_order.source_data.address : '-'}
                </Text>
              </View>
            </View>
          </View>

          <View style={styles.divider} />

          {/* 4 Info Blocks */}
          <View style={styles.grid2x2}>
            <View style={[styles.gridItem, { backgroundColor: pageBackground, borderColor }]}>
              <View style={[styles.gridIconWrapper, { backgroundColor: BRAND.primarySoft }]}>
                <Ionicons name="document-text-outline" size={16} color={BRAND.primary} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={[styles.gridLabel, { color: textMuted }]}>Dokumen</Text>
                <Text style={[styles.gridValue, { color: textColor }]} numberOfLines={1}>{task.reference_number || '-'}</Text>
              </View>
            </View>
            <View style={[styles.gridItem, { backgroundColor: pageBackground, borderColor }]}>
              <View style={[styles.gridIconWrapper, { backgroundColor: BRAND.violetSoft }]}>
                <Ionicons name="bus-outline" size={16} color={BRAND.violet} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={[styles.gridLabel, { color: textMuted }]}>Kendaraan</Text>
                <Text style={[styles.gridValue, { color: textColor }]} numberOfLines={1}>{task.vehicle?.plate_number || '-'}</Text>
                <Text style={[styles.gridValueSub, { color: textMuted }]} numberOfLines={1}>{task.vehicle?.name || '-'}</Text>
              </View>
            </View>
            <View style={[styles.gridItem, { backgroundColor: pageBackground, borderColor }]}>
              <View style={[styles.gridIconWrapper, { backgroundColor: BRAND.tealSoft }]}>
                <Ionicons name="person-outline" size={16} color={BRAND.teal} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={[styles.gridLabel, { color: textMuted }]}>Driver</Text>
                <Text style={[styles.gridValue, { color: textColor }]} numberOfLines={1}>{task.driver?.full_name || task.driver?.name || '-'}</Text>
              </View>
            </View>
            <View style={[styles.gridItem, { backgroundColor: pageBackground, borderColor }]}>
              <View style={[styles.gridIconWrapper, { backgroundColor: BRAND.warningSoft }]}>
                <Ionicons name="cube-outline" size={16} color={BRAND.warning} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={[styles.gridLabel, { color: textMuted }]}>Muatan</Text>
                <Text style={[styles.gridValue, { color: textColor }]} numberOfLines={1}>{task.quantity ? Number(task.quantity).toString().replace('.', ',') : '-'} {task.unit || ''}</Text>
              </View>
            </View>
          </View>
        </View>

        {/* Route & Estimasi Map Placeholder */}
        <View style={[styles.card, { backgroundColor: cardBackground, borderColor }]}>
          <View style={styles.cardSectionHeader}>
            <Text style={[styles.cardSectionTitle, { color: textColor }]}>Rute & Estimasi</Text>
            <TouchableOpacity onPress={openMap}>
              <Text style={styles.linkText}>Lihat di Maps</Text>
            </TouchableOpacity>
          </View>
          <View style={[styles.mapPlaceholder, { backgroundColor: '#E2E8F0', overflow: 'hidden', justifyContent: 'center', alignItems: 'center' }]}>
            {Platform.OS === 'web' || !MapView ? (
              <Text style={{ color: textMuted, fontStyle: 'italic' }}>[Peta Aktif Hanya Tersedia di Native/HP]</Text>
            ) : (
              <MapView
                style={{ width: '100%', height: '100%' }}
                initialRegion={{
                  latitude: -6.200000, // Default to Jakarta
                  longitude: 106.816666,
                  latitudeDelta: 0.0922,
                  longitudeDelta: 0.0421,
                }}
              >
                <Marker
                  coordinate={{ latitude: -6.200000, longitude: 106.816666 }}
                  title={task.destination_name || task.destination || 'Tujuan'}
                />
              </MapView>
            )}
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 }}>
            <View>
              <Text style={[styles.estimasiLabel, { color: textMuted }]}>Berangkat</Text>
              <Text style={[styles.estimasiValue, { color: textColor }]}>
                {safeFormatDate(task.dispatch_date || task.assigned_at)}, {safeFormatTime(task.dispatch_date || task.assigned_at)}
              </Text>
            </View>
            <View style={{ alignItems: 'flex-end' }}>
              <Text style={[styles.estimasiLabel, { color: textMuted }]}>Estimasi Tiba</Text>
              <Text style={[styles.estimasiValue, { color: textColor }]}>
                {task.estimated_arrival ? `${safeFormatDate(task.estimated_arrival)}, ${safeFormatTime(task.estimated_arrival)}` : '-'}
              </Text>
            </View>
          </View>
        </View>

        {/* Progress Tugas */}
        <View style={[styles.card, { backgroundColor: cardBackground, borderColor }]}>
          <Text style={[styles.cardSectionTitle, { color: textColor, marginBottom: 16 }]}>Progress Tugas</Text>
          <View style={styles.progressRow}>
            {[
              { id: 1, label: 'Prepare', active: status === 'assigned', done: status !== 'assigned', action: () => setModalKeberangkatanVisible(true) },
              { id: 2, label: 'Pengeluaran', active: status === 'on_route' || status === 'arrived', done: false, action: () => setModalPengeluaranVisible(true) },
              { id: 3, label: 'Tiba', active: status === 'on_route', done: status === 'arrived' || status === 'delivered', action: () => setModalTibaVisible(true) },
              { id: 4, label: 'Selesai', active: status === 'arrived', done: status === 'delivered', action: () => setModalSerahTerimaVisible(true) },
            ].map((step, idx, arr) => {
              const isClickable = step.active || (step.id === 2 && (status === 'on_route' || status === 'arrived'));
              return (
                <TouchableOpacity
                  key={step.id}
                  style={styles.progressStepWrap}
                  activeOpacity={isClickable ? 0.7 : 1}
                  onPress={() => {
                    if (isClickable) {
                      step.action();
                    }
                  }}
                >
                  <View style={[styles.progressCircle, { 
                    backgroundColor: step.done ? BRAND.successSoft : (step.active ? BRAND.primarySoft : BRAND.borderSoft),
                    borderColor: step.done ? BRAND.success : (step.active ? BRAND.primary : 'transparent'),
                    borderWidth: step.done || step.active ? 2 : 0
                  }]}>
                    {step.done ? (
                      <Ionicons name="checkmark" size={16} color={BRAND.success} />
                    ) : (
                      <Text style={[styles.progressStepNum, { 
                        color: step.active ? BRAND.primary : textMuted,
                        fontWeight: step.active ? 'bold' : 'normal'
                      }]}>{step.id}</Text>
                    )}
                  </View>
                  <Text style={[styles.progressStepLabel, { color: step.done || step.active ? textColor : textMuted, textAlign: 'center', fontWeight: step.done || step.active ? '600' : '400' }]}>{step.label}</Text>
                  {idx < arr.length - 1 && (
                    <View style={[styles.progressLine, { backgroundColor: step.done ? BRAND.success : BRAND.borderSoft }]} />
                  )}
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* Daftar Barang Bawaan */}
        <View style={[styles.card, { backgroundColor: cardBackground, borderColor }]}>
          <Text style={[styles.cardSectionTitle, { color: textColor, marginBottom: 12 }]}>Daftar Barang Bawaan</Text>

          <View style={styles.tableHeader}>
            <Text style={[styles.tableColNo, { color: textMuted }]}>No</Text>
            <Text style={[styles.tableColDesc, { color: textMuted }]}>Deskripsi Barang</Text>
            <Text style={[styles.tableColQty, { color: textMuted }]}>Qty</Text>
          </View>

          {task.items && task.items.length > 0 ? (
            task.items.map((item, idx) => (
              <View key={item.id || idx} style={[styles.tableRow, { borderBottomColor: BRAND.border }]}>
                <Text style={[styles.tableColNo, { color: textColor }]}>{idx + 1}</Text>
                <View style={[styles.tableColDesc, { paddingRight: 8 }]}>
                  <Text style={[styles.itemName, { color: textColor }]}>{item.item_description || '-'}</Text>
                  <Text style={[styles.itemNumber, { color: textMuted }]}>{item.item_number || '-'}</Text>
                </View>
                <Text style={[styles.tableColQty, { color: textColor }]}>
                  {item.quantity || '-'} {item.unit || ''}
                </Text>
              </View>
            ))
          ) : (task.item_description || task.item_number) ? (
              <View style={[styles.tableRow, { borderBottomColor: BRAND.border }]}>
                <Text style={[styles.tableColNo, { color: textColor }]}>1</Text>
                <View style={[styles.tableColDesc, { paddingRight: 8 }]}>
                  <Text style={[styles.itemName, { color: textColor }]}>{task.item_description || '-'}</Text>
                  <Text style={[styles.itemNumber, { color: textMuted }]}>{task.item_number || '-'}</Text>
                </View>
                <Text style={[styles.tableColQty, { color: textColor }]}>
                  {task.quantity ? Number(task.quantity).toString().replace('.', ',') : '-'} {task.unit || ''}
                </Text>
              </View>
          ) : (
            <View style={{ paddingVertical: 16, alignItems: 'center' }}>
              <Text style={{ color: textMuted, fontStyle: 'italic' }}>Tidak ada data barang</Text>
            </View>
          )}
        </View>


        {/* Catatan & PIC */}
        <View style={[styles.card, { backgroundColor: cardBackground, borderColor, marginBottom: 100 }]}>
          <Text style={[styles.cardSectionTitle, { color: textColor, marginBottom: 8 }]}>Catatan</Text>
          <Text style={[styles.noteText, { color: textMuted }]}>
            {task.notes || '-'}
          </Text>

          <Text style={[styles.cardSectionTitle, { color: textColor, marginTop: 16, marginBottom: 8 }]}>Penugasan Oleh</Text>
          <View style={[styles.picCard, { borderColor }]}>
            <View style={styles.picAvatar}>
              <Ionicons name="person" size={18} color={BRAND.primary} />
            </View>
            <View style={{ flex: 1, marginLeft: 12 }}>
              <Text style={[styles.picName, { color: textColor }]}>{task.assigner?.full_name || task.assigner?.name || task.assigned_by?.full_name || '-'}</Text>
              <Text style={[styles.picRole, { color: textMuted }]}>Admin</Text>
            </View>
            <TouchableOpacity 
              style={styles.callButton}
              onPress={() => {
                const phone = task.assigner?.phone || task.assigned_by?.phone || '081234567890';
                Linking.openURL(`tel:${phone}`);
              }}
            >
              <Ionicons name="call" size={18} color={BRAND.primary} />
            </TouchableOpacity>
          </View>
        </View>

        {/* Laporan & Bukti */}
        {(task.proof_photo || task.failure_reason || task.completed_odometer) && (
          <View style={[styles.card, { backgroundColor: cardBackground, borderColor, marginBottom: 100 }]}>
            <Text style={[styles.cardSectionTitle, { color: textColor, marginBottom: 12 }]}>Laporan & Bukti Penyelesaian</Text>

            {task.completed_odometer && (
              <View style={{ marginBottom: 12 }}>
                <Text style={{ color: textMuted, fontSize: 12 }}>Odometer Selesai</Text>
                <Text style={{ color: textColor, fontSize: 14, fontWeight: '600' }}>{task.completed_odometer} KM</Text>
              </View>
            )}

            {task.failure_reason && (
              <View style={{ marginBottom: 12 }}>
                <Text style={{ color: BRAND.danger, fontSize: 12, fontWeight: '600' }}>Alasan Kegagalan / Kendala</Text>
                <Text style={{ color: textColor, fontSize: 14 }}>{task.failure_reason}</Text>
              </View>
            )}

            {task.proof_photo && (
              <View style={{ marginBottom: 8 }}>
                <Text style={{ color: textMuted, fontSize: 12, marginBottom: 8 }}>Bukti Foto</Text>
                <Image
                  source={{ uri: task.proof_photo.startsWith('http') ? task.proof_photo : `${api.defaults.baseURL?.replace('/api', '')}/storage/${task.proof_photo}` }}
                  style={{ width: '100%', height: 200, borderRadius: 8, backgroundColor: '#E2E8F0' }}
                  resizeMode="cover"
                />
              </View>
            )}
          </View>
        )}

        {/* Helper bottom spacer to ensure scrollability if the proof card is absent */}
        {!(task.proof_photo || task.failure_reason || task.completed_odometer) && (
          <View style={{ height: 100 }} />
        )}
      </ScrollView>

      {/* Sticky Bottom Action Button */}
      {btnLabel ? (
        <View style={[styles.bottomActionArea, { backgroundColor: cardBackground, borderTopColor: borderColor }]}>
          <TouchableOpacity
            style={[styles.actionBtn, { opacity: actionLoading ? 0.7 : 1 }]}
            onPress={btnAction}
            disabled={actionLoading}
          >
            {actionLoading ? (
              <ActivityIndicator color="#FFF" />
            ) : (
              <Text style={styles.actionBtnText}>{btnLabel}</Text>
            )}
          </TouchableOpacity>
        </View>
      ) : null}

      {/* Render Modals */}
      <ModalKeberangkatan
        visible={modalKeberangkatanVisible}
        onClose={() => setModalKeberangkatanVisible(false)}
        onSubmit={(payload: any) => {
          setModalKeberangkatanVisible(false);
          handleAction('on_route', payload);
        }}
        task={task}
      />
      <ModalPengeluaran
        visible={modalPengeluaranVisible}
        onClose={() => setModalPengeluaranVisible(false)}
        onSubmit={(payload: any) => {
          setModalPengeluaranVisible(false);
          handleExpenseSubmit(payload);
        }}
        task={task}
      />
      <ModalTiba
        visible={modalTibaVisible}
        onClose={() => setModalTibaVisible(false)}
        onSubmit={(payload: any) => {
          setModalTibaVisible(false);
          handleAction('arrived', payload);
        }}
        task={task}
      />
      <ModalSerahTerima
        visible={modalSerahTerimaVisible}
        onClose={() => setModalSerahTerimaVisible(false)}
        onSubmit={(payload: any) => {
          setModalSerahTerimaVisible(false);
          handleAction('delivered', payload);
        }}
        task={task}
      />
    </View>
  );
}

export default function TaskDetailScreen() {
  return (
    <ErrorBoundary>
      <TaskDetailScreenContent />
    </ErrorBoundary>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  headerArea: {
    paddingTop: 55,
    paddingBottom: 25,
    paddingHorizontal: 20,
    borderBottomLeftRadius: 24,
    borderBottomRightRadius: 24,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerTitle: {
    color: '#FFF',
    fontSize: 18,
    fontWeight: '700',
  },
  headerIcon: {
    padding: 4,
  },
  scrollContent: {
    padding: 16,
  },
  card: {
    borderRadius: 19,
    borderWidth: 1,
    paddingHorizontal: 16,
    paddingVertical: 18,
    marginBottom: 16,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 7 },
    shadowOpacity: 0.07,
    shadowRadius: 14,
    elevation: 4,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  taskRef: {
    fontSize: 18,
    fontWeight: '700',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusBadgeText: {
    fontSize: 12,
    fontWeight: '700',
  },
  taskSubtitle: {
    fontSize: 13,
    marginBottom: 12,
  },
  dateTimeRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  dateTimeText: {
    fontSize: 12,
    marginLeft: 6,
  },
  divider: {
    height: 1,
    backgroundColor: BRAND.border,
    marginVertical: 16,
  },
  timelineRow: {
    flexDirection: 'row',
  },
  timelineIconCol: {
    alignItems: 'center',
    marginRight: 16,
    paddingTop: 4,
  },
  timelineIconWrapper: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: BRAND.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    zIndex: 2,
  },
  timelineLine: {
    width: 2,
    height: 60,
    marginVertical: -6,
    zIndex: 1,
  },
  timelineContentCol: {
    flex: 1,
  },
  timelineItem: {
    marginBottom: 20,
  },
  timelineItemHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  timelineTitle: {
    fontSize: 12,
    fontWeight: '700',
  },
  timelineTime: {
    fontSize: 12,
  },
  timelineLocName: {
    fontSize: 14,
    fontWeight: '700',
    marginBottom: 2,
  },
  timelineAddress: {
    fontSize: 12,
  },
  grid2x2: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    rowGap: 12,
  },
  gridItem: {
    width: '48%',
    flexDirection: 'row',
    alignItems: 'center',
    padding: 10,
    borderRadius: 14,
    borderWidth: 1,
  },
  gridIconWrapper: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 8,
  },
  gridLabel: {
    fontSize: 10,
    marginBottom: 2,
  },
  gridValue: {
    fontSize: 12,
    fontWeight: '700',
  },
  gridValueSub: {
    fontSize: 10,
    marginTop: 1,
  },
  cardSectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  cardSectionTitle: {
    fontSize: 14,
    fontWeight: '700',
  },
  linkText: {
    fontSize: 12,
    color: BRAND.primary,
    fontWeight: '600',
  },
  mapPlaceholder: {
    height: 120,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  estimasiLabel: {
    fontSize: 11,
    marginBottom: 2,
  },
  estimasiValue: {
    fontSize: 14,
    fontWeight: '700',
  },
  progressRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  progressStepWrap: {
    alignItems: 'center',
    flex: 1,
    position: 'relative',
  },
  progressCircle: {
    width: 28,
    height: 28,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 8,
    zIndex: 2,
  },
  progressStepNum: {
    fontSize: 10,
    fontWeight: '700',
  },
  progressStepLabel: {
    fontSize: 9,
    textAlign: 'center',
  },
  progressLine: {
    position: 'absolute',
    top: 11,
    left: '50%',
    width: '100%',
    height: 2,
    zIndex: 1,
  },
  checkItem: {
    width: '50%',
    flexDirection: 'row',
    alignItems: 'center',
  },
  checkLabel: {
    fontSize: 12,
    marginLeft: 8,
    flex: 1,
  },
  tableHeader: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: BRAND.border,
    paddingBottom: 8,
    marginBottom: 8,
  },
  tableRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    paddingVertical: 10,
    alignItems: 'center',
  },
  tableColNo: {
    width: 30,
    fontSize: 12,
    fontWeight: '600',
  },
  tableColDesc: {
    flex: 1,
  },
  tableColQty: {
    width: 70,
    fontSize: 12,
    fontWeight: '600',
    textAlign: 'right',
  },
  itemName: {
    fontSize: 13,
    fontWeight: '600',
  },
  itemNumber: {
    fontSize: 11,
    marginTop: 2,
  },
  noteText: {
    fontSize: 13,
    lineHeight: 20,
  },
  picCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 14,
    borderWidth: 1,
  },
  picAvatar: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: BRAND.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  picName: {
    fontSize: 14,
    fontWeight: '700',
  },
  picRole: {
    fontSize: 12,
  },
  callButton: {
    padding: 8,
    backgroundColor: BRAND.primarySoft,
    borderRadius: 8,
  },
  bottomActionArea: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 16,
    borderTopWidth: 1,
    elevation: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
  },
  actionBtn: {
    backgroundColor: BRAND.primary,
    paddingVertical: 14,
    borderRadius: 16,
    alignItems: 'center',
    shadowColor: BRAND.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  actionBtnText: {
    color: '#FFF',
    fontSize: 16,
    fontWeight: '700',
  },
});

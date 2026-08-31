import React, { useState } from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, TextInput, Image, Dimensions, Modal } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';
import { useRouter, useFocusEffect } from 'expo-router';
import api from '../../services/api';
import { useCallback, useEffect } from 'react';
import { ActivityIndicator } from 'react-native';

const { width } = Dimensions.get('window');

type ReportStatus = 'Semua' | 'Menunggu' | 'Disetujui' | 'Ditolak';
type ReportType = 'Semua' | 'Delivery' | 'Return';

interface ReportItem {
  id: string;
  from: string;
  to: string;
  date: string;
  time: string;
  driver: string;
  status: ReportStatus;
  type: ReportType;
}

const MOCK_REPORTS: ReportItem[] = [
  { id: 'TRP-240515-01', from: 'Gudang Pusat Jakarta', to: 'Plant 2 Karawang', date: '15 Mei 2024', time: '07:30 WIB', driver: 'Budi Santoso', status: 'Menunggu', type: 'Delivery' },
  { id: 'TRP-240514-08', from: 'Gudang Support', to: 'Gudang Subang', date: '14 Mei 2024', time: '10:00 WIB', driver: 'Andi Setiawan', status: 'Disetujui', type: 'Delivery' },
  { id: 'TRP-240513-05', from: 'Plant 3 Cikarang', to: 'Gudang Pusat Jakarta', date: '13 Mei 2024', time: '09:15 WIB', driver: 'Dedi Kurniawan', status: 'Ditolak', type: 'Return' },
  { id: 'TRP-240512-03', from: 'Gudang Pusat Jakarta', to: 'Plant 1 Cikarang', date: '12 Mei 2024', time: '08:45 WIB', driver: 'Budi Santoso', status: 'Disetujui', type: 'Delivery' },
  { id: 'TRP-240511-02', from: 'Gudang Pusat Jakarta', to: 'Depot. Produksi Cikarang', date: '11 Mei 2024', time: '11:25 WIB', driver: 'Agus Setiawan', status: 'Menunggu', type: 'Return' },
];

const TABS: ReportStatus[] = ['Semua', 'Menunggu', 'Disetujui', 'Ditolak'];

export default function LaporanScreen() {
  const { theme } = useTheme();
  const colors = Colors[theme as keyof typeof Colors];
  const router = useRouter();
  const [activeTab, setActiveTab] = useState<ReportStatus>('Semua');
  const [searchQuery, setSearchQuery] = useState('');

  const [isFilterVisible, setFilterVisible] = useState(false);
  const [tempFilterType, setTempFilterType] = useState<ReportType>('Semua');
  const [tempFilterDate, setTempFilterDate] = useState<string>('Semua Waktu');
  
  const [activeFilterType, setActiveFilterType] = useState<ReportType>('Semua');
  const [activeFilterDate, setActiveFilterDate] = useState<string>('Semua Waktu');

  const [reports, setReports] = useState<ReportItem[]>(MOCK_REPORTS);
  const [loading, setLoading] = useState(true);

  useFocusEffect(
    useCallback(() => {
      const fetchTasks = async () => {
        try {
          setLoading(true);
          const res = await api.get('/pickup');
          if (res.data && res.data.data) {
            const mapped = res.data.data.map((task: any) => ({
              id: task.reference_number || `TASK-${task.id}`,
              real_id: task.id,
              from: task.pickup_name || '-',
              to: task.destination || '-',
              date: new Date(task.assigned_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
              time: new Date(task.assigned_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB',
              driver: task.driver?.name || 'Driver',
              status: task.status === 'assigned' ? 'Menunggu' : task.status === 'delivered' ? 'Disetujui' : task.status === 'failed' || task.status === 'cancelled' ? 'Ditolak' : 'Menunggu',
              type: task.task_type === 'delivery' ? 'Delivery' : 'Return'
            }));
            setReports(mapped);
          }
        } catch (error) {
          console.error(error);
        } finally {
          setLoading(false);
        }
      };
      fetchTasks();
    }, [])
  );

  const filteredReports = reports.filter(report => {
    const matchesTab = activeTab === 'Semua' || report.status === activeTab;
    const matchesSearch = report.id.toLowerCase().includes(searchQuery.toLowerCase()) || 
                          report.from.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          report.to.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesType = activeFilterType === 'Semua' || report.type === activeFilterType;
    const matchesDate = activeFilterDate === 'Semua Waktu' || report.date === activeFilterDate;
    
    return matchesTab && matchesSearch && matchesType && matchesDate;
  });

  const getStatusColor = (status: ReportStatus) => {
    switch (status) {
      case 'Menunggu': return '#F59E0B'; // Yellow/Orange
      case 'Disetujui': return '#10B981'; // Green
      case 'Ditolak': return '#EF4444'; // Red
      default: return '#6B7280';
    }
  };

  const getStatusBgColor = (status: ReportStatus) => {
    switch (status) {
      case 'Menunggu': return '#FEF3C7';
      case 'Disetujui': return '#D1FAE5';
      case 'Ditolak': return '#FEE2E2';
      default: return '#F3F4F6';
    }
  };

  const totalLaporan = reports.length;
  const menungguCount = reports.filter(r => r.status === 'Menunggu').length;
  const disetujuiCount = reports.filter(r => r.status === 'Disetujui').length;
  const ditolakCount = reports.filter(r => r.status === 'Ditolak').length;

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      {/* Blue Header Background */}
      <View style={styles.headerBackground}>
        <View style={styles.headerTop}>
          <TouchableOpacity>
            <Ionicons name="menu-outline" size={28} color="#FFFFFF" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Laporan</Text>
          <TouchableOpacity>
            <Ionicons name="notifications-outline" size={24} color="#FFFFFF" />
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Hero Card */}
        <View style={styles.heroCard}>
          <View style={styles.heroContent}>
            <View style={styles.heroTextContainer}>
              <Text style={styles.heroText}>Kelola laporan keseluruhan aktivitas driver dengan mudah dan terstruktur.</Text>
            </View>
            <View style={styles.heroImageContainer}>
              <Ionicons name="document-text" size={60} color="#0756C6" style={{ opacity: 0.8 }} />
              <Ionicons name="cube" size={30} color="#F59E0B" style={{ position: 'absolute', bottom: 10, left: -10 }} />
              <Ionicons name="car" size={40} color="#10B981" style={{ position: 'absolute', bottom: -5, right: -15 }} />
            </View>
          </View>
          <TouchableOpacity style={styles.heroButton}>
            <Ionicons name="add" size={20} color="#FFFFFF" style={{ marginRight: 8 }} />
            <Text style={styles.heroButtonText}>Buat Laporan Baru</Text>
          </TouchableOpacity>
        </View>

        {/* Summary Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Text style={styles.statLabel}>Total Laporan</Text>
            <Text style={[styles.statValue, { color: '#2563EB' }]}>{totalLaporan}</Text>
            <Text style={styles.statSubLabel}>Semua</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statLabel}>Menunggu</Text>
            <Text style={[styles.statValue, { color: '#F59E0B' }]}>{menungguCount}</Text>
            <Text style={styles.statSubLabel}>Perlu Diperiksa</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statLabel}>Disetujui</Text>
            <Text style={[styles.statValue, { color: '#10B981' }]}>{disetujuiCount}</Text>
            <Text style={styles.statSubLabel}>Selesai</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statLabel}>Ditolak</Text>
            <Text style={[styles.statValue, { color: '#EF4444' }]}>{ditolakCount}</Text>
            <Text style={styles.statSubLabel}>Perlu Revisi</Text>
          </View>
        </View>

        {/* Search & Filter */}
        <View style={styles.searchFilterContainer}>
          <View style={styles.searchBox}>
            <Ionicons name="search-outline" size={20} color="#9CA3AF" />
            <TextInput 
              style={styles.searchInput}
              placeholder="Cari laporan, lokasi, nomor TRP..."
              placeholderTextColor="#9CA3AF"
              value={searchQuery}
              onChangeText={setSearchQuery}
            />
          </View>
          <TouchableOpacity style={styles.filterButton} onPress={() => {
            setTempFilterType(activeFilterType);
            setTempFilterDate(activeFilterDate);
            setFilterVisible(true);
          }}>
            <Ionicons name="filter-outline" size={20} color="#2563EB" />
            <Text style={styles.filterText}>Filter</Text>
          </TouchableOpacity>
        </View>

        {/* Tabs */}
        <View style={styles.tabsContainer}>
          {TABS.map(tab => (
            <TouchableOpacity 
              key={tab} 
              style={[styles.tabButton, activeTab === tab && styles.tabButtonActive]}
              onPress={() => setActiveTab(tab)}
            >
              <Text style={[styles.tabText, activeTab === tab && styles.tabTextActive]}>{tab}</Text>
            </TouchableOpacity>
          ))}
        </View>

        {/* Report List */}
        <View style={styles.listContainer}>
          {filteredReports.map((report) => (
            <TouchableOpacity 
              key={report.id} 
              style={styles.reportCard}
              activeOpacity={0.7}
              onPress={() => router.push(`/laporan/${(report as any).real_id || report.id}` as any)} // Example ID routing
            >
              <View style={styles.reportHeader}>
                <View style={styles.reportIdContainer}>
                  <View style={[styles.iconBox, { backgroundColor: report.status === 'Ditolak' ? '#FEE2E2' : '#E0E7FF' }]}>
                    <Ionicons name="bus-outline" size={20} color={report.status === 'Ditolak' ? '#EF4444' : '#2563EB'} />
                  </View>
                  <Text style={styles.reportId}>{report.id}</Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: getStatusBgColor(report.status) }]}>
                  <Text style={[styles.statusText, { color: getStatusColor(report.status) }]}>{report.status}</Text>
                </View>
              </View>

              <View style={styles.routeContainer}>
                <Text style={styles.routeText}>{report.from}</Text>
                <Ionicons name="arrow-forward" size={16} color="#6B7280" style={{ marginHorizontal: 8 }} />
                <Text style={styles.routeText}>{report.to}</Text>
              </View>

              <View style={styles.reportMeta}>
                <View style={styles.metaItem}>
                  <Ionicons name="calendar-outline" size={14} color="#6B7280" />
                  <Text style={styles.metaText}>{report.date}</Text>
                </View>
                <View style={styles.metaItem}>
                  <Ionicons name="time-outline" size={14} color="#6B7280" />
                  <Text style={styles.metaText}>{report.time}</Text>
                </View>
              </View>

              <View style={styles.reportDriver}>
                <Ionicons name="person-outline" size={14} color="#6B7280" />
                <Text style={styles.driverText}>{report.driver}</Text>
                <Ionicons name="chevron-forward" size={20} color="#9CA3AF" style={{ marginLeft: 'auto' }} />
              </View>
            </TouchableOpacity>
          ))}

          {filteredReports.length === 0 && (
            <View style={styles.emptyState}>
              <Ionicons name="document-outline" size={48} color="#D1D5DB" />
              <Text style={styles.emptyText}>Tidak ada laporan ditemukan</Text>
            </View>
          )}

          <View style={styles.endOfList}>
             <Ionicons name="add-circle-outline" size={24} color="#D1D5DB" />
             <Text style={styles.endOfListText}>Tidak ada lagi laporan</Text>
          </View>
        </View>
      </ScrollView>

      {/* Filter Modal */}
      <Modal visible={isFilterVisible} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Filter Laporan</Text>
              <TouchableOpacity onPress={() => setFilterVisible(false)}>
                <Ionicons name="close" size={24} color="#374151" />
              </TouchableOpacity>
            </View>
            
            <Text style={styles.filterSectionTitle}>Jenis Laporan</Text>
            <View style={styles.filterOptionsContainer}>
              {['Semua', 'Delivery', 'Return'].map(t => (
                <TouchableOpacity 
                  key={t} 
                  style={[styles.filterOptionBtn, tempFilterType === t && styles.filterOptionBtnActive]}
                  onPress={() => setTempFilterType(t as ReportType)}
                >
                  <Text style={[styles.filterOptionText, tempFilterType === t && styles.filterOptionTextActive]}>{t}</Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={styles.filterSectionTitle}>Tanggal</Text>
            <View style={styles.filterOptionsContainer}>
              {['Semua Waktu', '15 Mei 2024', '14 Mei 2024', '13 Mei 2024'].map(d => (
                <TouchableOpacity 
                  key={d} 
                  style={[styles.filterOptionBtn, tempFilterDate === d && styles.filterOptionBtnActive]}
                  onPress={() => setTempFilterDate(d)}
                >
                  <Text style={[styles.filterOptionText, tempFilterDate === d && styles.filterOptionTextActive]}>{d}</Text>
                </TouchableOpacity>
              ))}
            </View>

            <TouchableOpacity 
              style={styles.applyFilterBtn}
              onPress={() => {
                setActiveFilterType(tempFilterType);
                setActiveFilterDate(tempFilterDate);
                setFilterVisible(false);
              }}
            >
              <Text style={styles.applyFilterBtnText}>Terapkan Filter</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
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
    paddingTop: 60, // Overlap the header
    paddingBottom: 100, // For custom tab bar
  },
  heroCard: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 20,
    borderRadius: 16,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
  },
  heroContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  heroTextContainer: {
    flex: 1,
    paddingRight: 10,
  },
  heroText: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
  },
  heroImageContainer: {
    width: 80,
    height: 80,
    justifyContent: 'center',
    alignItems: 'center',
    position: 'relative',
  },
  heroButton: {
    backgroundColor: '#0756C6',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 8,
  },
  heroButtonText: {
    color: '#FFFFFF',
    fontWeight: '600',
    fontSize: 14,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    marginTop: 20,
  },
  statCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 8,
    alignItems: 'center',
    width: (width - 40 - 24) / 4, // 4 cards with gaps
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 5,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  statLabel: {
    fontSize: 10,
    color: '#6B7280',
    marginBottom: 4,
    textAlign: 'center',
  },
  statValue: {
    fontSize: 20,
    fontWeight: '700',
    marginBottom: 4,
  },
  statSubLabel: {
    fontSize: 9,
    color: '#9CA3AF',
    textAlign: 'center',
  },
  searchFilterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginTop: 20,
    gap: 12,
  },
  searchBox: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    paddingHorizontal: 12,
    height: 44,
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  searchInput: {
    flex: 1,
    marginLeft: 8,
    fontSize: 14,
    color: '#1F2937',
  },
  filterButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#EFF6FF',
    paddingHorizontal: 16,
    borderRadius: 12,
    height: 44,
  },
  filterText: {
    color: '#2563EB',
    fontWeight: '600',
    marginLeft: 4,
    fontSize: 14,
  },
  tabsContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginTop: 16,
    justifyContent: 'space-between',
  },
  tabButton: {
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 20,
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  tabButtonActive: {
    backgroundColor: '#0756C6',
    borderColor: '#0756C6',
  },
  tabText: {
    fontSize: 13,
    color: '#6B7280',
    fontWeight: '500',
  },
  tabTextActive: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  listContainer: {
    paddingHorizontal: 20,
    marginTop: 16,
  },
  reportCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  reportHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  reportIdContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  iconBox: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  reportId: {
    fontSize: 12,
    fontWeight: '500',
    color: '#4B5563',
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 11,
    fontWeight: '600',
  },
  routeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    paddingLeft: 46,
    flexWrap: 'wrap',
  },
  routeText: {
    fontSize: 14,
    fontWeight: '700',
    color: '#1F2937',
  },
  reportMeta: {
    flexDirection: 'row',
    marginBottom: 12,
    paddingLeft: 46,
    gap: 16,
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  metaText: {
    fontSize: 12,
    color: '#6B7280',
  },
  reportDriver: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingLeft: 46,
    gap: 6,
  },
  driverText: {
    fontSize: 13,
    color: '#4B5563',
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    marginTop: 12,
    color: '#9CA3AF',
    fontSize: 14,
  },
  endOfList: {
    alignItems: 'center',
    paddingVertical: 20,
    gap: 8,
  },
  endOfListText: {
    color: '#9CA3AF',
    fontSize: 12,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
    paddingBottom: 40,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#1F2937',
  },
  filterSectionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 12,
  },
  filterOptionsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
    marginBottom: 24,
  },
  filterOptionBtn: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    backgroundColor: '#FFFFFF',
  },
  filterOptionBtnActive: {
    backgroundColor: '#EFF6FF',
    borderColor: '#2563EB',
  },
  filterOptionText: {
    fontSize: 13,
    color: '#6B7280',
    fontWeight: '500',
  },
  filterOptionTextActive: {
    color: '#2563EB',
    fontWeight: '600',
  },
  applyFilterBtn: {
    backgroundColor: '#0756C6',
    paddingVertical: 14,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 8,
  },
  applyFilterBtnText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '600',
  }
});

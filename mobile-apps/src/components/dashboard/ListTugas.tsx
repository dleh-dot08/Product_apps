import { Text } from '@/components/CustomText';
import React, { useCallback, useEffect, useMemo, useState } from 'react';
import {
  ActivityIndicator,
  FlatList,
  RefreshControl,
  StyleSheet, 
  TextInput,
  TouchableOpacity,
  View,
  Modal,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

import { Colors } from '@/constants/theme';
import { useTheme } from '../../context/ThemeContext';
import api from '../../services/api';

const BRAND = {
  primary: '#0756C6',
  primarySoft: '#EAF3FF',
  white: '#FFFFFF',
  page: '#F8FAFC',
  text: '#1E293B',
  muted: '#64748B',
  border: '#E2E8F0',
  success: '#10B981',
  successSoft: '#D1FAE5',
  warning: '#F59E0B',
  warningSoft: '#FEF3C7',
};

type TaskStatus = 'assigned' | 'on_route' | 'arrived' | 'delivered' | 'failed' | string;
type TaskType = 'delivery' | 'pickup' | string;

type DriverTask = {
  id: number | string;
  reference_number?: string | null;
  task_type?: TaskType;
  status?: TaskStatus;
  assigned_at?: string | null;
  pickup_date?: string | null;
  pickup_name?: string | null;
  pickup_location?: string | null;
  destination?: string | null;
  item_number?: string | null;
  item_description?: string | null;
  item_category?: string | null;
  category?: string | null;
  priority?: string | boolean | number | null;
  vehicle_plate_number?: string | null;
  vehicle_name?: string | null;
  quantity?: number | string | null;
  unit?: string | null;
  
  vehicle?: {
    plate_number?: string | null;
    vehicle_name?: string | null;
  } | null;

  driver?: {
    name?: string | null;
    employee_id?: string | null;
  } | null;
};

type PaginationMeta = {
  current_page?: number;
  last_page?: number;
  total?: number;
};

export default function ListTugas() {
  const router = useRouter();
  const { theme } = useTheme();

  const colors = Colors[theme];
  const isDark = theme === 'dark';

  const [tasks, setTasks] = useState<DriverTask[]>([]);
  const [meta, setMeta] = useState<PaginationMeta>({});
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [loadingMore, setLoadingMore] = useState(false);

  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  
  const [filterVisible, setFilterVisible] = useState(false);
  const [filterType, setFilterType] = useState('all'); // all, pickup, delivery
  const [filterStartDate, setFilterStartDate] = useState('');
  const [filterEndDate, setFilterEndDate] = useState('');

  const pageBackground = isDark ? colors.background : BRAND.page;
  const cardBackground = isDark ? colors.backgroundElement : BRAND.white;
  const textColor = colors.text;
  const textMuted = colors.textSecondary;
  const borderColor = isDark ? colors.backgroundSelected : BRAND.border;

  const fetchTasks = useCallback(
    async (pageNumber = 1, shouldRefresh = false) => {
      try {
        if (pageNumber === 1 && !shouldRefresh) {
          setLoading(true);
        }

        if (shouldRefresh) {
          setRefreshing(true);
        }

        const response = await api.get('/pickup', {
          params: {
            search,
            status: status === 'all' ? '' : status,
            type: filterType === 'all' ? '' : filterType,
            start_date: filterStartDate || undefined,
            end_date: filterEndDate || undefined,
            page: pageNumber,
          },
        });

        if (response.data?.status === 'success') {
          const newTasks: DriverTask[] = response.data.data ?? [];
          const responseMeta: PaginationMeta = response.data.meta ?? {};

          setTasks((previous) =>
            pageNumber === 1 ? newTasks : [...previous, ...newTasks],
          );
          setMeta(responseMeta);

          const currentPage = responseMeta.current_page ?? pageNumber;
          const lastPage = responseMeta.last_page ?? currentPage;

          setHasMore(currentPage < lastPage);
          setPage(currentPage);
        }
      } catch (error) {
        console.error('Failed to fetch tasks', error);
      } finally {
        setLoading(false);
        setRefreshing(false);
        setLoadingMore(false);
      }
    },
    [search, status],
  );

  useEffect(() => {
    const timeout = setTimeout(() => {
      fetchTasks(1);
    }, search ? 350 : 0);

    return () => clearTimeout(timeout);
  }, [fetchTasks, search, status]);

  const handleLoadMore = () => {
    if (!loadingMore && hasMore && !loading) {
      setLoadingMore(true);
      fetchTasks(page + 1);
    }
  };

  const handleRefresh = () => {
    fetchTasks(1, true);
  };

  const totalTasks = meta.total ?? tasks.length;

  const runningCount = useMemo(
    () =>
      tasks.filter(
        (task) => task.status === 'on_route' || task.status === 'arrived',
      ).length,
    [tasks],
  );

  const waitingCount = useMemo(
    () =>
      tasks.filter(
        (task) => task.status === 'assigned',
      ).length,
    [tasks],
  );

  const completedCount = useMemo(
    () =>
      tasks.filter(
        (task) => task.status === 'delivered',
      ).length,
    [tasks],
  );

  const renderItem = ({ item }: { item: DriverTask }) => (
    <TaskCard
      task={item}
      cardBackground={cardBackground}
      borderColor={borderColor}
      textColor={textColor}
      textMuted={textMuted}
      onPress={() => router.push(`/task/${item.id}` as any)}
    />
  );

  const TABS = [
    { id: 'all', label: 'Semua' },
    { id: 'assigned', label: 'Menunggu' },
    { id: 'on_route', label: 'Berjalan' },
    { id: 'delivered', label: 'Selesai' },
  ];

  return (
    <View style={[styles.container, { backgroundColor: pageBackground }]}>
      {/* Blue Header Area */}
      <View style={styles.headerArea}>
        <View style={styles.headerTop}>
          <TouchableOpacity onPress={() => router.canGoBack() && router.back()} style={styles.headerIcon}>
            <Ionicons name="menu" size={24} color="#FFF" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Tugas</Text>
          <View style={{ width: 24 }} />
        </View>
      </View>

      <FlatList
        data={tasks}
        keyExtractor={(item) => String(item.id)}
        renderItem={renderItem}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={styles.listContent}
        onEndReached={handleLoadMore}
        onEndReachedThreshold={0.4}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={handleRefresh}
            colors={[BRAND.primary]}
            tintColor={BRAND.primary}
          />
        }
        ListHeaderComponent={
          <View style={styles.headerContent}>
            {/* Segmented Tabs */}
            <View style={[styles.segmentedTabs, { backgroundColor: cardBackground, borderColor }]}>
              {TABS.map((tab) => {
                const isActive = status === tab.id || (tab.id === 'on_route' && status === 'arrived');
                return (
                  <TouchableOpacity
                    key={tab.id}
                    style={[
                      styles.tabButton,
                      isActive && styles.tabButtonActive
                    ]}
                    onPress={() => setStatus(tab.id)}
                  >
                    <Text style={[
                      styles.tabText,
                      { color: textMuted },
                      isActive && styles.tabTextActive
                    ]}>
                      {tab.label}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>

            {/* Search and Filter */}
            <View style={styles.searchRow}>
              <View style={[styles.searchContainer, { backgroundColor: cardBackground, borderColor }]}>
                <Ionicons name="search-outline" size={18} color={textMuted} />
                <TextInput
                  style={[styles.searchInput, { color: textColor }]}
                  placeholder="Cari rute, gudang, atau nomor TRP..."
                  placeholderTextColor={textMuted}
                  value={search}
                  onChangeText={setSearch}
                  returnKeyType="search"
                />
                {search.length > 0 && (
                  <TouchableOpacity onPress={() => setSearch('')}>
                    <Ionicons name="close-circle" size={18} color={textMuted} />
                  </TouchableOpacity>
                )}
              </View>
              <TouchableOpacity 
                style={[styles.filterButton, { backgroundColor: cardBackground, borderColor }]}
                onPress={() => setFilterVisible(true)}
              >
                <Ionicons name="options-outline" size={18} color={BRAND.primary} />
                <Text style={styles.filterButtonText}>Filter</Text>
              </TouchableOpacity>
            </View>

            {/* Summary Cards */}
            <View style={styles.summaryRow}>
              <View style={[styles.summaryCard, { backgroundColor: cardBackground, borderColor }]}>
                <Ionicons name="clipboard-outline" size={20} color={BRAND.primary} style={styles.summaryIcon} />
                <Text style={[styles.summaryLabel, { color: textMuted }]}>Total</Text>
                <Text style={[styles.summaryValue, { color: textColor }]}>{totalTasks}</Text>
                <Text style={[styles.summarySub, { color: textMuted }]}>Tugas</Text>
              </View>
              <View style={[styles.summaryCard, { backgroundColor: cardBackground, borderColor }]}>
                <Ionicons name="document-text-outline" size={20} color={BRAND.primary} style={styles.summaryIcon} />
                <Text style={[styles.summaryLabel, { color: textMuted }]}>Berjalan</Text>
                <Text style={[styles.summaryValue, { color: textColor }]}>{runningCount}</Text>
                <Text style={[styles.summarySub, { color: textMuted }]}>Tugas</Text>
              </View>
              <View style={[styles.summaryCard, { backgroundColor: cardBackground, borderColor }]}>
                <Ionicons name="time-outline" size={20} color={BRAND.warning} style={styles.summaryIcon} />
                <Text style={[styles.summaryLabel, { color: textMuted }]}>Menunggu</Text>
                <Text style={[styles.summaryValue, { color: textColor }]}>{waitingCount}</Text>
                <Text style={[styles.summarySub, { color: textMuted }]}>Tugas</Text>
              </View>
              <View style={[styles.summaryCard, { backgroundColor: cardBackground, borderColor }]}>
                <Ionicons name="checkmark-circle-outline" size={20} color={BRAND.success} style={styles.summaryIcon} />
                <Text style={[styles.summaryLabel, { color: textMuted }]}>Selesai</Text>
                <Text style={[styles.summaryValue, { color: textColor }]}>{completedCount}</Text>
                <Text style={[styles.summarySub, { color: textMuted }]}>Tugas</Text>
              </View>
            </View>
          </View>
        }
        ListEmptyComponent={
          loading ? (
            <View style={styles.emptyState}>
              <ActivityIndicator size="large" color={BRAND.primary} />
            </View>
          ) : (
            <View style={styles.emptyState}>
              <Ionicons name="document-text-outline" size={48} color={textMuted} />
              <Text style={[styles.emptyTitle, { color: textColor }]}>Tidak ada penugasan</Text>
              <Text style={[styles.emptyText, { color: textMuted }]}>Tugas yang sesuai filter akan tampil di sini.</Text>
            </View>
          )
        }
      />

      {/* Filter Modal */}
      <Modal
        visible={filterVisible}
        transparent={true}
        animationType="slide"
        onRequestClose={() => setFilterVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { backgroundColor: cardBackground }]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.modalTitle, { color: textColor }]}>Filter Tugas</Text>
              <TouchableOpacity onPress={() => setFilterVisible(false)}>
                <Ionicons name="close" size={24} color={textColor} />
              </TouchableOpacity>
            </View>

            <View style={styles.modalBody}>
              <Text style={[styles.filterLabel, { color: textColor }]}>Jenis Tugas</Text>
              <View style={styles.filterOptions}>
                <TouchableOpacity 
                  style={[styles.filterOptBtn, filterType === 'all' && styles.filterOptBtnActive, { borderColor }]}
                  onPress={() => setFilterType('all')}
                >
                  <Text style={[styles.filterOptText, { color: filterType === 'all' ? '#FFF' : textMuted }]}>Semua</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={[styles.filterOptBtn, filterType === 'pickup' && styles.filterOptBtnActive, { borderColor }]}
                  onPress={() => setFilterType('pickup')}
                >
                  <Text style={[styles.filterOptText, { color: filterType === 'pickup' ? '#FFF' : textMuted }]}>Penjemputan</Text>
                </TouchableOpacity>
                <TouchableOpacity 
                  style={[styles.filterOptBtn, filterType === 'delivery' && styles.filterOptBtnActive, { borderColor }]}
                  onPress={() => setFilterType('delivery')}
                >
                  <Text style={[styles.filterOptText, { color: filterType === 'delivery' ? '#FFF' : textMuted }]}>Pengantaran</Text>
                </TouchableOpacity>
              </View>

              <Text style={[styles.filterLabel, { color: textColor, marginTop: 20 }]}>Tanggal</Text>
              <View style={styles.dateFilterRow}>
                <View style={[styles.dateInputWrapper, { borderColor, backgroundColor: pageBackground }]}>
                  <Ionicons name="calendar-outline" size={16} color={textMuted} />
                  <TextInput
                    style={[styles.dateInput, { color: textColor }]}
                    placeholder="Dari (DD/MM/YYYY)"
                    placeholderTextColor={textMuted}
                    value={filterStartDate}
                    onChangeText={setFilterStartDate}
                  />
                </View>
                <Text style={{ color: textMuted, marginHorizontal: 8 }}>-</Text>
                <View style={[styles.dateInputWrapper, { borderColor, backgroundColor: pageBackground }]}>
                  <Ionicons name="calendar-outline" size={16} color={textMuted} />
                  <TextInput
                    style={[styles.dateInput, { color: textColor }]}
                    placeholder="Sampai (DD/MM/YYYY)"
                    placeholderTextColor={textMuted}
                    value={filterEndDate}
                    onChangeText={setFilterEndDate}
                  />
                </View>
              </View>
            </View>

            <View style={[styles.modalFooter, { borderTopColor: borderColor }]}>
              <TouchableOpacity 
                style={[styles.btnReset, { borderColor }]} 
                onPress={() => {
                  setFilterType('all');
                  setFilterStartDate('');
                  setFilterEndDate('');
                }}
              >
                <Text style={[styles.btnResetText, { color: textMuted }]}>Reset</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={styles.btnApply} 
                onPress={() => {
                  setFilterVisible(false);
                  fetchTasks(1, true);
                }}
              >
                <Text style={styles.btnApplyText}>Terapkan</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

function TaskCard({
  task,
  cardBackground,
  borderColor,
  textColor,
  textMuted,
  onPress,
}: {
  task: DriverTask;
  cardBackground: string;
  borderColor: string;
  textColor: string;
  textMuted: string;
  onPress: () => void;
}) {
  const getStatusDisplay = (status: string) => {
    switch (status) {
      case 'assigned':
        return { label: 'Menunggu', bg: BRAND.warningSoft, text: BRAND.warning };
      case 'on_route':
      case 'arrived':
        return { label: 'Berjalan', bg: BRAND.primarySoft, text: BRAND.primary };
      case 'delivered':
        return { label: 'Selesai', bg: BRAND.successSoft, text: BRAND.success };
      default:
        return { label: status || 'Unknown', bg: BRAND.border, text: BRAND.text };
    }
  };

  const statusObj = getStatusDisplay(task.status ?? '');
  
  // Is this a pickup or delivery? Assuming task_type or category determines it.
  const isPickup = task.task_type === 'pickup' || String(task.category).toLowerCase().includes('pickup');
  const typeColor = isPickup ? BRAND.warning : BRAND.success;
  
  const formatDate = (dateString?: string | null) => {
    if (!dateString) return '-';
    try {
      const d = new Date(dateString);
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch {
      return dateString;
    }
  };

  const formatTime = (dateString?: string | null) => {
    if (!dateString) return '-';
    try {
      const d = new Date(dateString);
      return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
    } catch {
      return '-';
    }
  };

  return (
    <TouchableOpacity
      activeOpacity={0.7}
      onPress={onPress}
      style={[
        styles.taskCard,
        {
          backgroundColor: cardBackground,
          borderColor: borderColor,
          borderLeftColor: typeColor,
          borderLeftWidth: 4,
        },
      ]}
    >
      {/* Card Header */}
      <View style={styles.cardHeader}>
        <Text style={[styles.taskRef, { color: textColor }]}>
          {task.reference_number || task.item_number || `-`}
        </Text>
        <View style={[styles.statusBadge, { backgroundColor: statusObj.bg }]}>
          <Text style={[styles.statusBadgeText, { color: statusObj.text }]}>
            {statusObj.label}
          </Text>
        </View>
      </View>

      {/* Origin -> Dest */}
      <View style={styles.routeRow}>
        <Text style={[styles.routeText, { color: textColor }]} numberOfLines={1}>
          {task.pickup_name || task.pickup_location || '-'}
        </Text>
        <Ionicons name="arrow-forward" size={14} color={textMuted} style={styles.routeArrow} />
        <Text style={[styles.routeText, { color: textColor }]} numberOfLines={1}>
          {task.destination || '-'}
        </Text>
      </View>

      {/* Date & Time */}
      <View style={styles.dateTimeRow}>
        <View style={styles.dateTimeItem}>
          <Ionicons name="calendar-outline" size={14} color={textMuted} />
          <Text style={[styles.dateTimeText, { color: textMuted }]}>{formatDate(task.assigned_at)}</Text>
        </View>
        <View style={styles.dateTimeItem}>
          <Ionicons name="time-outline" size={14} color={textMuted} />
          <Text style={[styles.dateTimeText, { color: textMuted }]}>{formatTime(task.assigned_at)}</Text>
        </View>
        <View style={styles.dateTimeItem}>
          <Ionicons name="hourglass-outline" size={14} color={textMuted} />
          <Text style={[styles.dateTimeText, { color: textMuted }]}>
            {task.assigned_at ? 'Estimasi' : '-'}
          </Text>
        </View>
      </View>

      <View style={styles.divider} />

      {/* Vehicle & Driver */}
      <View style={styles.metaRow}>
        <View style={styles.metaItem}>
          <Ionicons name="bus-outline" size={18} color={textMuted} />
          <View>
            <Text style={[styles.metaTitle, { color: textColor }]} numberOfLines={1}>
              {task.vehicle?.vehicle_name || task.vehicle_name || '-'}
            </Text>
            <Text style={[styles.metaSub, { color: textMuted }]} numberOfLines={1}>
              {task.vehicle?.plate_number || task.vehicle_plate_number || '-'}
            </Text>
          </View>
        </View>
        <View style={styles.metaItem}>
          <Ionicons name="person-outline" size={18} color={textMuted} />
          <View>
            <Text style={[styles.metaTitle, { color: textColor }]} numberOfLines={1}>
              {task.driver?.name || '-'}
            </Text>
            <Text style={[styles.metaSub, { color: textMuted }]} numberOfLines={1}>
              {task.driver?.employee_id || '-'}
            </Text>
          </View>
        </View>
        <Ionicons name="chevron-forward" size={20} color={textMuted} />
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  headerArea: {
    backgroundColor: BRAND.primary,
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: 20,
    borderBottomLeftRadius: 20,
    borderBottomRightRadius: 20,
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
  headerContent: {
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 8,
  },
  listContent: {
    paddingBottom: 40,
  },
  segmentedTabs: {
    flexDirection: 'row',
    borderRadius: 12,
    padding: 4,
    marginBottom: 16,
    borderWidth: 1,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
  },
  tabButton: {
    flex: 1,
    paddingVertical: 10,
    alignItems: 'center',
    borderRadius: 8,
  },
  tabButtonActive: {
    backgroundColor: BRAND.primary,
  },
  tabText: {
    fontSize: 13,
    fontWeight: '600',
  },
  tabTextActive: {
    color: '#FFF',
  },
  searchRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
    gap: 8,
  },
  searchContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    height: 48,
    borderRadius: 12,
    borderWidth: 1,
  },
  searchInput: {
    flex: 1,
    marginLeft: 8,
    fontSize: 13,
  },
  filterButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    height: 48,
    paddingHorizontal: 16,
    borderRadius: 12,
    borderWidth: 1,
    gap: 6,
  },
  filterButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: BRAND.primary,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 8,
    marginBottom: 16,
  },
  summaryCard: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 4,
    borderRadius: 12,
    borderWidth: 1,
  },
  summaryIcon: {
    marginBottom: 6,
  },
  summaryLabel: {
    fontSize: 10,
    fontWeight: '600',
    marginBottom: 2,
  },
  summaryValue: {
    fontSize: 18,
    fontWeight: '800',
    marginBottom: 2,
  },
  summarySub: {
    fontSize: 10,
  },
  taskCard: {
    marginHorizontal: 16,
    marginBottom: 12,
    borderRadius: 12,
    borderWidth: 1,
    padding: 16,
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  taskRef: {
    fontSize: 16,
    fontWeight: '700',
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: '700',
  },
  routeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  routeText: {
    fontSize: 13,
    fontWeight: '600',
    flex: 1,
  },
  routeArrow: {
    marginHorizontal: 8,
  },
  dateTimeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 16,
    marginBottom: 12,
  },
  dateTimeItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  dateTimeText: {
    fontSize: 12,
  },
  divider: {
    height: 1,
    backgroundColor: BRAND.border,
    marginVertical: 12,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    flex: 1,
  },
  metaTitle: {
    fontSize: 12,
    fontWeight: '600',
    marginBottom: 2,
  },
  metaSub: {
    fontSize: 11,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 40,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: '700',
    marginTop: 16,
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 14,
    textAlign: 'center',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    paddingTop: 16,
    paddingBottom: 24,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    marginBottom: 16,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
  },
  modalBody: {
    paddingHorizontal: 20,
    marginBottom: 24,
  },
  filterLabel: {
    fontSize: 14,
    fontWeight: '600',
    marginBottom: 10,
  },
  filterOptions: {
    flexDirection: 'row',
    gap: 10,
  },
  filterOptBtn: {
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
  },
  filterOptBtnActive: {
    backgroundColor: BRAND.primary,
    borderColor: BRAND.primary,
  },
  filterOptText: {
    fontSize: 13,
    fontWeight: '500',
  },
  dateFilterRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  dateInputWrapper: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 12,
    height: 44,
  },
  dateInput: {
    flex: 1,
    marginLeft: 8,
    fontSize: 13,
  },
  modalFooter: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingTop: 16,
    borderTopWidth: 1,
    gap: 12,
  },
  btnReset: {
    flex: 1,
    borderWidth: 1,
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
  },
  btnResetText: {
    fontWeight: '600',
  },
  btnApply: {
    flex: 2,
    backgroundColor: BRAND.primary,
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
  },
  btnApplyText: {
    color: '#FFF',
    fontWeight: '700',
  },
});

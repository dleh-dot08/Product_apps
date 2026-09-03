import { Text } from '@/components/CustomText';
import React, { useCallback, useMemo, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Image,
  ImageBackground,
  RefreshControl,
  ScrollView,
  StyleSheet, TouchableOpacity,
  View,
} from 'react-native';
import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useFocusEffect, useRouter } from 'expo-router';

import { Colors } from '@/constants/theme';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import api from '../../services/api';

const REMOTE_ASSETS = {
  hero: {
    uri: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1600&q=88',
  },
};

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

type TripStatus = 'assigned' | 'on_route' | 'arrived' | 'delivered' | string;
type TaskType = 'delivery' | 'pickup' | string;

type DashboardTask = {
  id: number | string;
  reference_number?: string | null;
  status?: TripStatus;
  task_type?: TaskType;
  pickup_name?: string | null;
  pickup_location?: string | null;
  destination?: string | null;
  assigned_at?: string | null;
  estimated_arrival_at?: string | null;
  item_category?: string | null;
  category?: string | null;
  vehicle?: {
    plate_number?: string | null;
    police_number?: string | null;
    name?: string | null;
    vehicle_name?: string | null;
  } | null;
  vehicle_plate_number?: string | null;
  vehicle_name?: string | null;
  quantity?: number | string | null;
  unit?: string | null;
  dispatch_date?: string | null;
  estimated_arrival?: string | null;
  proof_photo?: string | null;
  failure_reason?: string | null;
  completed_odometer?: number | null;
};

type DashboardData = {
  today_trips_count?: number;
  completed_trips_count?: number;
  in_progress_trips_count?: number;
  distance_today?: number;
  pending_reports_count?: number;
  today_tasks?: DashboardTask[];
  active_task?: DashboardTask | null;
  performance?: {
    on_time_percentage?: number;
    rating?: number;
    total_trip?: number;
    completed_trip?: number;
    on_time_trip?: number;
    total_on_time_trip?: number;
    issue_count?: number;
    fuel_efficiency?: number;
  };
};

type StatusConfig = {
  label: string;
  backgroundColor: string;
  textColor: string;
  accentColor: string;
};

type SummaryCardProps = {
  label: string;
  value: string | number;
  suffix: string;
  icon: keyof typeof Ionicons.glyphMap;
  color: string;
  softColor: string;
  cardBackground: string;
  borderColor: string;
  textColor: string;
};

type QuickActionProps = {
  title: string;
  icon: keyof typeof Ionicons.glyphMap;
  color: string;
  onPress: () => void;
};

export default function DriverDashboard() {
  const { user } = useAuth();
  const { theme } = useTheme();
  const router = useRouter();

  const colors = Colors[theme];
  const isDark = theme === 'dark';

  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const pageBackground = isDark ? colors.background : BRAND.page;
  const cardBackground = isDark ? colors.backgroundElement : BRAND.white;
  const textColor = colors.text;
  const mutedColor = colors.textSecondary;
  const borderColor = isDark ? colors.backgroundSelected : BRAND.border;

  const currentUser = user as any;

  const driverName = currentUser?.full_name || 'Driver';

  const driverPhoto =
    currentUser?.photo_url ||
    currentUser?.photo ||
    currentUser?.employee?.photo_url ||
    null;

  const driverRole =
    currentUser?.roleRelation?.name || currentUser?.role?.name || 'Driver';

  const driverInitial = driverName
    .split(' ')
    .filter(Boolean)
    .map((part: string) => part.charAt(0))
    .join('')
    .slice(0, 2)
    .toUpperCase();

  const fetchDashboard = useCallback(async () => {
    try {
      const response = await api.get('/driver/dashboard');

      if (response.data?.status === 'success') {
        setDashboardData(response.data.data);
      }
    } catch (error) {
      console.error('Failed to fetch driver dashboard', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useFocusEffect(
    useCallback(() => {
      fetchDashboard();
    }, [fetchDashboard]),
  );

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    fetchDashboard();
  }, [fetchDashboard]);

  const todayTasks = dashboardData?.today_tasks ?? [];
  const activeTask = dashboardData?.active_task ?? null;

  const vehiclePlate = activeTask?.vehicle_plate_number || activeTask?.vehicle?.police_number || activeTask?.vehicle?.plate_number || 'B 1234 CD';
  const vehicleName = activeTask?.vehicle_name || activeTask?.vehicle?.vehicle_name || activeTask?.vehicle?.name || 'Kendaraan Operasional';

  const onTime = Math.min(
    Math.max(dashboardData?.performance?.on_time_percentage ?? 0, 0),
    100,
  );

  const fuelEfficiency = dashboardData?.performance?.fuel_efficiency ?? 0;

  const openTask = (taskId: number | string) => {
    router.push(`/task/${taskId}` as any);
  };

  const showFeatureInfo = (feature: string) => {
    Alert.alert(
      feature,
      'Halaman ini siap dihubungkan ke modul terkait berikutnya.',
    );
  };

  return (
    <View style={[styles.root, { backgroundColor: pageBackground }]}>
      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={[BRAND.primary]}
            tintColor={BRAND.primary}
          />
        }
      >
        <ImageBackground
          source={REMOTE_ASSETS.hero}
          resizeMode="cover"
          style={styles.heroSection}
          imageStyle={styles.heroImage}
        >
          <LinearGradient
            colors={[
              'rgba(2, 43, 103, 0.65)',
              'rgba(5, 79, 183, 0.92)',
              '#0756C6',
            ]}
            locations={[0, 0.5, 1]}
            style={styles.heroOverlay}
          >
            <View style={styles.heroTopBar}>
              <TouchableOpacity
                activeOpacity={0.7}
                style={styles.heroIconButton}
                onPress={() => showFeatureInfo('Menu')}
              >
                <Ionicons name="menu-outline" size={30} color={BRAND.white} />
              </TouchableOpacity>

              <TouchableOpacity
                activeOpacity={0.7}
                style={styles.notificationButton}
                onPress={() => showFeatureInfo('Notifikasi')}
              >
                <Ionicons
                  name="notifications-outline"
                  size={24}
                  color={BRAND.white}
                />
                <View style={styles.notificationDot} />
              </TouchableOpacity>
            </View>

            <View style={styles.profileRow}>
              <View style={styles.profileIdentity}>
                <View style={styles.avatarShell}>
                  {driverPhoto ? (
                    <Image source={{ uri: driverPhoto }} style={styles.avatarImage} />
                  ) : (
                    <View style={styles.avatarFallback}>
                      <Text style={styles.avatarInitial}>{driverInitial}</Text>
                    </View>
                  )}
                  <View style={styles.avatarOnlineDot} />
                </View>

                <View style={styles.profileCopy}>
                  <Text style={styles.greeting}>{getGreeting()},</Text>
                  <Text numberOfLines={1} style={styles.driverName}>
                    {driverName}
                  </Text>

                  <View style={styles.onlineRow}>
                    <View style={styles.onlineDot} />
                    <Text style={styles.onlineText}>Online</Text>
                    <View style={styles.roleDivider} />
                    <Text numberOfLines={1} style={styles.roleText}>
                      {driverRole}
                    </Text>
                  </View>
                </View>
              </View>

              <View style={styles.vehicleChip}>
                <MaterialCommunityIcons
                  name="truck-outline"
                  size={21}
                  color={BRAND.white}
                />
                <View style={styles.vehicleChipCopy}>
                  <Text numberOfLines={1} style={styles.vehicleChipPlate}>
                    {vehiclePlate}
                  </Text>
                  <Text numberOfLines={1} style={styles.vehicleChipName}>
                    {vehicleName}
                  </Text>
                </View>
              </View>
            </View>

            <View style={styles.summaryGrid}>
              <SummaryCard
                label="Tugas Hari Ini"
                value={dashboardData?.today_trips_count ?? 0}
                suffix="Tugas"
                icon="clipboard-outline"
                color={BRAND.primary}
                softColor={BRAND.primarySoft}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
              />

              <SummaryCard
                label="Selesai"
                value={dashboardData?.completed_trips_count ?? 0}
                suffix="Tugas"
                icon="shield-checkmark-outline"
                color={BRAND.success}
                softColor={BRAND.successSoft}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
              />

              <SummaryCard
                label="Tepat Waktu"
                value={`${onTime}%`}
                suffix="Performa"
                icon="time-outline"
                color={BRAND.warning}
                softColor={BRAND.warningSoft}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
              />

              <SummaryCard
                label="Konsumsi BBM"
                value={formatDecimal(fuelEfficiency)}
                suffix="km/l rata-rata"
                icon="speedometer-outline"
                color={BRAND.violet}
                softColor={BRAND.violetSoft}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
              />
            </View>
          </LinearGradient>
        </ImageBackground>

        <View style={styles.dashboardContent}>
          {loading ? (
            <LoadingCard mutedColor={mutedColor} cardBackground={cardBackground} />
          ) : (
            <>
              <ActiveAssignmentCard
                task={activeTask}
                vehiclePlate={vehiclePlate}
                vehicleName={vehicleName}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
                onOpenTask={openTask}
              />

              <SectionHeader title="Aksi Cepat" textColor={textColor} />

              <View style={styles.quickActionGrid}>
                <QuickAction
                  title="Mulai Tugas"
                  icon="play-circle-outline"
                  color={BRAND.primary}
                  onPress={() => {
                    if (activeTask) {
                      openTask(activeTask.id);
                      return;
                    }

                    Alert.alert('Mulai Tugas', 'Belum ada penugasan aktif.');
                  }}
                />

                <QuickAction
                  title="Update Status"
                  icon="document-text-outline"
                  color={BRAND.teal}
                  onPress={() => {
                    if (activeTask) {
                      openTask(activeTask.id);
                      return;
                    }

                    Alert.alert('Update Status', 'Belum ada penugasan aktif.');
                  }}
                />

                <QuickAction
                  title="Laporkan Kendala"
                  icon="warning-outline"
                  color={BRAND.warning}
                  onPress={() => showFeatureInfo('Laporan Kendala')}
                />

                <QuickAction
                  title="Upload Bukti"
                  icon="camera-outline"
                  color={BRAND.violet}
                  onPress={() => showFeatureInfo('Upload Bukti')}
                />
              </View>

              <View style={styles.sectionHeaderRow}>
                <SectionHeader title="Jadwal Hari Ini" textColor={textColor} compact />

                <TouchableOpacity
                  activeOpacity={0.7}
                  onPress={() => router.push('/list-tugas')}
                >
                  <Text style={styles.seeAllText}>Lihat Semua</Text>
                </TouchableOpacity>
              </View>

              <View
                style={[
                  styles.taskList,
                  { backgroundColor: cardBackground, borderColor },
                ]}
              >
                {todayTasks.length > 0 ? (
                  todayTasks.map((task, index) => (
                    <TaskRow
                      key={task.id}
                      task={task}
                      index={index}
                      isLast={index === todayTasks.length - 1}
                      textColor={textColor}
                      borderColor={borderColor}
                      onPress={() => openTask(task.id)}
                    />
                  ))
                ) : (
                  <EmptySchedule textColor={textColor} />
                )}
              </View>

              <PerformanceCard
                dashboardData={dashboardData}
                cardBackground={cardBackground}
                borderColor={borderColor}
                textColor={textColor}
              />
            </>
          )}
        </View>

        <View style={styles.bottomSpacer} />
      </ScrollView>
    </View>
  );
}

function SummaryCard({
  label,
  value,
  suffix,
  icon,
  color,
  softColor,
  cardBackground,
  borderColor,
  textColor,
}: SummaryCardProps) {
  return (
    <View
      style={[
        styles.summaryCard,
        { backgroundColor: cardBackground, borderColor },
      ]}
    >
      <View style={[styles.summaryIcon, { backgroundColor: softColor }]}>
        <Ionicons name={icon} size={23} color={color} />
      </View>

      <View style={styles.summaryCopy}>
        <Text style={styles.summaryLabel}>{label}</Text>
        <Text numberOfLines={1} style={[styles.summaryValue, { color: textColor }]}>
          {value}
        </Text>
        <Text numberOfLines={1} style={styles.summarySuffix}>
          {suffix}
        </Text>
      </View>
    </View>
  );
}

function SectionHeader({
  title,
  textColor,
  compact = false,
}: {
  title: string;
  textColor: string;
  compact?: boolean;
}) {
  return (
    <View style={[styles.sectionHeader, compact && styles.sectionHeaderCompact]}>
      <Text style={[styles.sectionTitle, { color: textColor }]}>{title}</Text>
    </View>
  );
}

function ActiveAssignmentCard({
  task,
  vehiclePlate,
  vehicleName,
  cardBackground,
  borderColor,
  textColor,
  onOpenTask,
}: {
  task: DashboardTask | null;
  vehiclePlate: string;
  vehicleName: string;
  cardBackground: string;
  borderColor: string;
  textColor: string;
  onOpenTask: (taskId: number | string) => void;
}) {
  if (!task) {
    return (
      <View
        style={[
          styles.assignmentCard,
          { backgroundColor: cardBackground, borderColor },
        ]}
      >
        <EmptyAssignment textColor={textColor} />
      </View>
    );
  }

  const status = getStatusConfig(task.status ?? '');
  const type = getBusinessTypeConfig(task.task_type ?? '');

  return (
    <View
      style={[
        styles.assignmentCard,
        { backgroundColor: cardBackground, borderColor },
      ]}
    >
      <View style={styles.assignmentHeader}>
        <Text style={[styles.assignmentTitle, { color: textColor }]}>
          Penugasan Aktif
        </Text>

        <View style={styles.assignmentBadges}>
          <View style={[styles.typeBadge, { backgroundColor: type.background }]}>
            <Ionicons name="cube-outline" size={11} color={type.color} />
            <Text style={[styles.typeBadgeText, { color: type.color }]}>{type.label}</Text>
          </View>

          <View style={[styles.statusBadge, { backgroundColor: status.backgroundColor }]}>
            <Text style={[styles.statusBadgeText, { color: status.textColor }]}>
              {status.label}
            </Text>
          </View>
        </View>
      </View>

      <Text style={styles.assignmentReference}>
        {task.reference_number || `TRIP-${task.id}`}
      </Text>

      <View style={styles.horizontalRoute}>
        <RoutePoint
          label="Dari"
          title={task.pickup_name || 'Lokasi Pickup'}
          address={task.pickup_location || '-'}
          color={BRAND.primary}
          textColor={textColor}
        />

        <View style={styles.routeConnector}>
          <View style={styles.routeConnectorDot} />
          <View style={styles.routeConnectorLine} />
          <Ionicons name="arrow-forward" size={14} color="#7890A8" />
        </View>

        <RoutePoint
          label="Ke"
          title="Tujuan"
          address={task.destination || '-'}
          color={BRAND.primary}
          textColor={textColor}
        />
      </View>

      <View style={styles.assignmentDivider} />

      <View style={styles.assignmentMetaRow}>
        <AssignmentMeta
          icon="time-outline"
          label="Tgl Pengiriman"
          value={`${formatTime(task.dispatch_date || task.assigned_at) || '-'} WIB`}
          textColor={textColor}
        />

        <AssignmentMeta
          icon="timer-outline"
          label="Estimasi Tiba"
          value={`${formatTime(task.estimated_arrival || task.estimated_arrival_at) || '-'} WIB`}
          textColor={textColor}
        />

        <AssignmentMeta
          icon="car-outline"
          label="Kendaraan"
          value={vehiclePlate}
          secondary={vehicleName}
          textColor={textColor}
        />

        <AssignmentMeta
          icon="cube-outline"
          label="Qty Barang"
          value={task.quantity ? `${Number(task.quantity)} ${task.unit || 'Pcs'}` : '-'}
          textColor={textColor}
          isLast
        />
      </View>

      <TouchableOpacity
        activeOpacity={0.86}
        style={styles.primaryButton}
        onPress={() => onOpenTask(task.id)}
      >
        <Ionicons name="navigate-circle-outline" size={19} color={BRAND.white} />
        <Text style={styles.primaryButtonText}>Lihat Rute & Detail Tugas</Text>
      </TouchableOpacity>
    </View>
  );
}

function RoutePoint({
  label,
  title,
  address,
  color,
  textColor,
}: {
  label: string;
  title: string;
  address: string;
  color: string;
  textColor: string;
}) {
  return (
    <View style={styles.routePoint}>
      <Text style={styles.routeLabel}>{label}</Text>
      <View style={styles.routeTitleRow}>
        <Ionicons name="location" size={19} color={color} />
        <Text style={[styles.routeTitle, { color: textColor }]}>
          {title}
        </Text>
      </View>
      <Text style={styles.routeAddress}>
        {address}
      </Text>
    </View>
  );
}

function AssignmentMeta({
  icon,
  label,
  value,
  secondary,
  textColor,
  isLast,
}: {
  icon: keyof typeof Ionicons.glyphMap;
  label: string;
  value: string;
  secondary?: string;
  textColor: string;
  isLast?: boolean;
}) {
  return (
    <View style={[styles.assignmentMetaItem, isLast && { borderRightWidth: 0 }]}>
      <View style={styles.assignmentMetaLabelRow}>
        <Ionicons name={icon} size={17} color="#6E8099" />
        <Text style={styles.assignmentMetaLabel}>{label}</Text>
      </View>
      <Text style={[styles.assignmentMetaValue, { color: textColor }]}>
        {value}
      </Text>
      {secondary ? (
        <Text style={styles.assignmentMetaSecondary}>
          {secondary}
        </Text>
      ) : null}
    </View>
  );
}

function QuickAction({ title, icon, color, onPress }: QuickActionProps) {
  return (
    <TouchableOpacity
      activeOpacity={0.84}
      onPress={onPress}
      style={[styles.quickActionCard, { backgroundColor: color }]}
    >
      <View style={styles.quickActionIconCircle}>
        <Ionicons name={icon} size={25} color={BRAND.white} />
      </View>
      <Text style={styles.quickActionText}>{title}</Text>
    </TouchableOpacity>
  );
}

function TaskRow({
  task,
  index,
  isLast,
  textColor,
  borderColor,
  onPress,
}: {
  task: DashboardTask;
  index: number;
  isLast: boolean;
  textColor: string;
  borderColor: string;
  onPress: () => void;
}) {
  const status = getStatusConfig(task.status ?? '');
  const type = getBusinessTypeConfig(task.task_type ?? '');

  return (
    <TouchableOpacity
      activeOpacity={0.72}
      onPress={onPress}
      style={[
        styles.taskRow,
        !isLast && { borderBottomWidth: 1, borderBottomColor: borderColor },
      ]}
    >
      <View style={[styles.taskAccent, { backgroundColor: status.accentColor }]} />

      <View style={styles.taskTimeColumn}>
        <Text style={[styles.taskTime, { color: textColor }]}>
          {formatTime(task.dispatch_date || task.assigned_at) || '--:--'}
        </Text>
      </View>

      <View style={styles.taskBody}>
        <View style={styles.taskTopRow}>
          <Text numberOfLines={1} style={[styles.taskRouteTitle, { color: textColor }]}>
            {shorten(task.pickup_name || task.pickup_location || 'Lokasi Pickup', 20)}
            {'  →  '}
            {shorten(task.destination || 'Tujuan', 22)}
          </Text>

          <View style={[styles.taskStatusBadge, { backgroundColor: status.backgroundColor }]}>
            <Text style={[styles.taskStatusText, { color: status.textColor }]}>
              {status.label}
            </Text>
          </View>
        </View>

        <Text numberOfLines={1} style={styles.taskMetaText}>
          {type.label}  •  {task.item_category || task.category || 'Barang'}
        </Text>
      </View>

      <Ionicons name="chevron-forward" size={20} color="#52657F" />
    </TouchableOpacity>
  );
}

function PerformanceCard({
  dashboardData,
  cardBackground,
  borderColor,
  textColor,
}: {
  dashboardData: DashboardData | null;
  cardBackground: string;
  borderColor: string;
  textColor: string;
}) {
  const performance = dashboardData?.performance;
  const onTime = Math.min(Math.max(performance?.on_time_percentage ?? 0, 0), 100);
  const totalTrip = performance?.total_trip ?? dashboardData?.today_trips_count ?? 0;
  const completedTrip = performance?.completed_trip ?? dashboardData?.completed_trips_count ?? 0;
  const issueCount = performance?.issue_count ?? dashboardData?.pending_reports_count ?? 0;
  const fuelEfficiency = performance?.fuel_efficiency ?? 0;

  return (
    <View
      style={[
        styles.performanceCard,
        { backgroundColor: cardBackground, borderColor },
      ]}
    >
      <View style={styles.performanceHeader}>
        <Text style={[styles.performanceTitle, { color: textColor }]}>Performa Anda</Text>

        <View style={styles.periodChip}>
          <Ionicons name="calendar-outline" size={12} color={BRAND.primary} />
          <Text style={styles.periodChipText}>7 Hari Terakhir</Text>
          <Ionicons name="chevron-down" size={11} color={BRAND.primary} />
        </View>
      </View>

      <View style={styles.performanceScoreRow}>
        <Text style={styles.performanceScore}>{onTime}%</Text>
        <Text style={styles.performanceScoreLabel}>Skor Performa</Text>
        <View style={styles.progressTrack}>
          <View style={[styles.progressFill, { width: `${onTime}%` }]} />
        </View>
      </View>

      <View style={styles.performanceMetrics}>
        <PerformanceMetric
          icon="shield-checkmark-outline"
          color={BRAND.success}
          softColor={BRAND.successSoft}
          label="Tugas Selesai"
          value={`${completedTrip}`}
          subValue={`dari ${totalTrip}`}
          textColor={textColor}
        />

        <PerformanceMetric
          icon="time-outline"
          color={BRAND.primary}
          softColor={BRAND.primarySoft}
          label="Tepat Waktu"
          value={`${onTime}%`}
          subValue={
            performance?.on_time_trip !== undefined &&
              performance?.total_on_time_trip !== undefined
              ? `${performance.on_time_trip} dari ${performance.total_on_time_trip}`
              : 'Ketepatan'
          }
          textColor={textColor}
        />

        <PerformanceMetric
          icon="warning-outline"
          color={BRAND.warning}
          softColor={BRAND.warningSoft}
          label="Kendala"
          value={`${issueCount}`}
          subValue="Dilaporkan"
          textColor={textColor}
        />

        <PerformanceMetric
          icon="speedometer-outline"
          color={BRAND.violet}
          softColor={BRAND.violetSoft}
          label="Konsumsi BBM"
          value={`${formatDecimal(fuelEfficiency)} km/l`}
          subValue="Rata-rata"
          textColor={textColor}
        />
      </View>
    </View>
  );
}

function PerformanceMetric({
  icon,
  color,
  softColor,
  label,
  value,
  subValue,
  textColor,
}: {
  icon: keyof typeof Ionicons.glyphMap;
  color: string;
  softColor: string;
  label: string;
  value: string;
  subValue: string;
  textColor: string;
}) {
  return (
    <View style={styles.performanceMetric}>
      <View style={[styles.performanceMetricIcon, { backgroundColor: softColor }]}>
        <Ionicons name={icon} size={18} color={color} />
      </View>
      <Text numberOfLines={1} style={styles.performanceMetricLabel}>{label}</Text>
      <Text numberOfLines={1} style={[styles.performanceMetricValue, { color: textColor }]}>
        {value}
      </Text>
      <Text numberOfLines={1} style={styles.performanceMetricSubValue}>{subValue}</Text>
    </View>
  );
}

function LoadingCard({
  mutedColor,
  cardBackground,
}: {
  mutedColor: string;
  cardBackground: string;
}) {
  return (
    <View style={[styles.loadingCard, { backgroundColor: cardBackground }]}>
      <ActivityIndicator size="large" color={BRAND.primary} />
      <Text style={[styles.loadingText, { color: mutedColor }]}>Memuat dashboard...</Text>
    </View>
  );
}

function EmptyAssignment({ textColor }: { textColor: string }) {
  return (
    <View style={styles.emptyAssignment}>
      <View style={styles.emptyIcon}>
        <Ionicons name="car-outline" size={26} color={BRAND.primary} />
      </View>
      <Text style={[styles.emptyTitle, { color: textColor }]}>Tidak ada penugasan aktif</Text>
      <Text style={styles.emptyDescription}>
        Penugasan aktif akan tampil di sini setelah tugas diberikan.
      </Text>
    </View>
  );
}

function EmptySchedule({ textColor }: { textColor: string }) {
  return (
    <View style={styles.emptySchedule}>
      <Ionicons name="calendar-outline" size={25} color={BRAND.subtle} />
      <Text style={[styles.emptyTitle, { color: textColor }]}>Tidak ada tugas hari ini</Text>
      <Text style={styles.emptyDescription}>Saat ini belum ada tugas yang dijadwalkan untuk Anda hari ini.</Text>
    </View>
  );
}

function getStatusConfig(status: string): StatusConfig {
  switch (status) {
    case 'assigned':
      return {
        label: 'Dijadwalkan',
        backgroundColor: BRAND.primarySoft,
        textColor: BRAND.primary,
        accentColor: BRAND.primary,
      };

    case 'on_route':
      return {
        label: 'Sedang Berjalan',
        backgroundColor: BRAND.successSoft,
        textColor: BRAND.success,
        accentColor: BRAND.success,
      };

    case 'arrived':
      return {
        label: 'Tiba di Lokasi',
        backgroundColor: BRAND.warningSoft,
        textColor: BRAND.warning,
        accentColor: BRAND.warning,
      };

    case 'delivered':
      return {
        label: 'Selesai',
        backgroundColor: BRAND.successSoft,
        textColor: BRAND.success,
        accentColor: BRAND.success,
      };

    default:
      return {
        label: status || 'Dijadwalkan',
        backgroundColor: '#F1F5F9',
        textColor: BRAND.muted,
        accentColor: BRAND.subtle,
      };
  }
}

function getBusinessTypeConfig(type: string) {
  if (type === 'pickup') {
    return {
      label: 'PICKUP',
      background: BRAND.violetSoft,
      color: BRAND.violet,
    };
  }

  return {
    label: 'Distribusi',
    background: BRAND.primarySoft,
    color: BRAND.primary,
  };
}

function getGreeting() {
  const hour = new Date().getHours();

  if (hour < 11) return 'Selamat pagi';
  if (hour < 15) return 'Selamat siang';
  if (hour < 18) return 'Selamat sore';

  return 'Selamat malam';
}

function formatTime(dateString?: string | null) {
  if (!dateString) return '';

  const date = new Date(dateString);

  if (Number.isNaN(date.getTime())) return '';

  return date.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  });
}

function formatDecimal(value: number) {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1,
  }).format(Number(value || 0));
}

function shorten(value: string, maximumLength: number) {
  if (value.length <= maximumLength) return value;

  return `${value.substring(0, maximumLength)}...`;
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
  },

  heroSection: {
    minHeight: 270,
    backgroundColor: BRAND.primary,
  },
  heroImage: {
    opacity: 0.72,
  },
  heroOverlay: {
    flex: 1,
    paddingHorizontal: 15,
    paddingTop: 5,
    paddingBottom: 35,
  },
  heroTopBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  heroIconButton: {
    width: 42,
    height: 42,
    alignItems: 'center',
    justifyContent: 'center',
  },
  notificationButton: {
    position: 'relative',
    width: 42,
    height: 42,
    alignItems: 'center',
    justifyContent: 'center',
  },
  notificationDot: {
    position: 'absolute',
    top: 7,
    right: 7,
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: BRAND.danger,
    borderWidth: 1.5,
    borderColor: BRAND.white,
  },

  profileRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 8,
    marginBottom: 16,
  },
  profileIdentity: {
    flex: 1,
    minWidth: 0,
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatarShell: {
    position: 'relative',
    width: 72,
    height: 72,
    padding: 3,
    borderRadius: 36,
    backgroundColor: BRAND.white,
  },
  avatarImage: {
    width: '100%',
    height: '100%',
    borderRadius: 33,
  },
  avatarFallback: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 33,
    backgroundColor: BRAND.primarySoft,
  },
  avatarInitial: {
    color: BRAND.primary,
    fontSize: 22,
    fontWeight: '900',
  },
  avatarOnlineDot: {
    position: 'absolute',
    right: 1,
    bottom: 4,
    width: 13,
    height: 13,
    borderRadius: 7,
    backgroundColor: '#25D66F',
    borderWidth: 2,
    borderColor: BRAND.white,
  },
  profileCopy: {
    flex: 1,
    minWidth: 0,
    marginLeft: 11,
  },
  greeting: {
    color: 'rgba(255,255,255,0.84)',
    fontSize: 11,
    fontWeight: '500',
  },
  driverName: {
    marginTop: 1,
    color: BRAND.white,
    fontSize: 22,
    fontWeight: '900',
    letterSpacing: -0.5,
  },
  onlineRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
  },
  onlineDot: {
    width: 8,
    height: 8,
    marginRight: 5,
    borderRadius: 4,
    backgroundColor: '#24DC75',
  },
  onlineText: {
    color: BRAND.white,
    fontSize: 10,
    fontWeight: '700',
  },
  roleDivider: {
    width: 1,
    height: 11,
    marginHorizontal: 7,
    backgroundColor: 'rgba(255,255,255,0.35)',
  },
  roleText: {
    flexShrink: 1,
    color: 'rgba(255,255,255,0.72)',
    fontSize: 9,
    fontWeight: '500',
  },
  vehicleChip: {
    width: 129,
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 8,
    paddingHorizontal: 10,
    paddingVertical: 9,
    borderRadius: 14,
    backgroundColor: 'rgba(255,255,255,0.13)',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.22)',
  },
  vehicleChipCopy: {
    flex: 1,
    minWidth: 0,
    marginLeft: 7,
  },
  vehicleChipPlate: {
    color: BRAND.white,
    fontSize: 10.5,
    fontWeight: '800',
  },
  vehicleChipName: {
    marginTop: 2,
    color: 'rgba(255,255,255,0.78)',
    fontSize: 8,
    fontWeight: '500',
  },

  summaryGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 6,
  },
  summaryCard: {
    flex: 1,
    minHeight: 90,
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 4,
    paddingVertical: 12,
    borderWidth: 1,
    borderRadius: 14,
    shadowColor: '#0E2A54',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 3,
  },
  summaryIcon: {
    width: 38,
    height: 38,
    borderRadius: 19,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 6,
  },
  summaryCopy: {
    alignItems: 'center',
    width: '100%',
  },
  summaryLabel: {
    color: BRAND.muted,
    fontSize: 7.5,
    fontWeight: '600',
    textAlign: 'center',
  },
  summaryValue: {
    marginTop: 2,
    fontSize: 15,
    fontWeight: '900',
    letterSpacing: -0.3,
    textAlign: 'center',
  },
  summarySuffix: {
    marginTop: 1,
    color: BRAND.muted,
    fontSize: 7,
    fontWeight: '500',
    textAlign: 'center',
  },

  dashboardContent: {
    marginTop: -18,
    paddingHorizontal: 14,
  },
  loadingCard: {
    minHeight: 180,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 20,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.07,
    shadowRadius: 13,
    elevation: 4,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 11,
    fontWeight: '600',
  },

  assignmentCard: {
    paddingHorizontal: 16,
    paddingVertical: 11,
    borderWidth: 1,
    borderRadius: 19,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 7 },
    shadowOpacity: 0.07,
    shadowRadius: 14,
    elevation: 4,
  },
  assignmentHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  assignmentTitle: {
    fontSize: 17,
    fontWeight: '900',
    letterSpacing: -0.3,
  },
  assignmentBadges: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  typeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 7,
    paddingVertical: 4,
    borderRadius: 6,
  },
  typeBadgeText: {
    fontSize: 8,
    fontWeight: '800',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  statusBadgeText: {
    fontSize: 8,
    fontWeight: '800',
  },
  assignmentReference: {
    marginTop: 7,
    color: BRAND.subtle,
    fontSize: 8,
    fontWeight: '700',
  },

  horizontalRoute: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 16,
  },
  routePoint: {
    flex: 1,
    minWidth: 0,
  },
  routeLabel: {
    marginLeft: 27,
    color: BRAND.muted,
    fontSize: 8.5,
    fontWeight: '500',
  },
  routeTitleRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: 2,
  },
  routeTitle: {
    flex: 1,
    minWidth: 0,
    marginLeft: 7,
    fontSize: 11.5,
    fontWeight: '800',
    lineHeight: 15,
  },
  routeAddress: {
    marginTop: 3,
    marginLeft: 27,
    color: BRAND.muted,
    fontSize: 8.5,
    lineHeight: 12,
  },
  routeConnector: {
    width: 53,
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 3,
  },
  routeConnectorDot: {
    width: 8,
    height: 8,
    borderWidth: 2,
    borderColor: BRAND.primary,
    borderRadius: 4,
  },
  routeConnectorLine: {
    flex: 1,
    height: 0,
    borderTopWidth: 1.5,
    borderStyle: 'dashed',
    borderColor: '#B5C3D5',
    marginHorizontal: 4,
  },
  assignmentDivider: {
    height: 1,
    marginVertical: 10,
    backgroundColor: BRAND.borderSoft,
  },
  assignmentMetaRow: {
    flexDirection: 'row',
  },
  assignmentMetaItem: {
    flex: 1,
    minWidth: 0,
    minHeight: 45,
    paddingHorizontal: 5,
    borderRightWidth: 1,
    borderRightColor: BRAND.borderSoft,
  },
  assignmentMetaLabelRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  assignmentMetaLabel: {
    color: BRAND.muted,
    fontSize: 7.5,
    fontWeight: '500',
  },
  assignmentMetaValue: {
    marginTop: 5,
    fontSize: 10,
    fontWeight: '800',
  },
  assignmentMetaSecondary: {
    marginTop: 2,
    color: BRAND.muted,
    fontSize: 7.5,
    lineHeight: 10,
  },
  primaryButton: {
    minHeight: 48,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 7,
    marginTop: 13,
    backgroundColor: BRAND.primary,
    borderRadius: 10,
  },
  primaryButtonText: {
    color: BRAND.white,
    fontSize: 11.5,
    fontWeight: '800',
  },

  sectionHeader: {
    marginTop: 18,
    marginBottom: 8,
  },
  sectionHeaderCompact: {
    marginTop: 0,
    marginBottom: 0,
  },
  sectionTitle: {
    fontSize: 16.5,
    fontWeight: '900',
    letterSpacing: -0.2,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 18,
    marginBottom: 8,
  },
  seeAllText: {
    color: BRAND.primary,
    fontSize: 10.5,
    fontWeight: '800',
  },

  quickActionGrid: {
    flexDirection: 'row',
    gap: 8,
  },
  quickActionCard: {
    flex: 1,
    minHeight: 83,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 4,
    borderRadius: 13,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.12,
    shadowRadius: 8,
    elevation: 3,
  },
  quickActionIconCircle: {
    width: 37,
    height: 37,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 19,
    backgroundColor: 'rgba(255,255,255,0.10)',
  },
  quickActionText: {
    marginTop: 6,
    color: BRAND.white,
    fontSize: 8.8,
    fontWeight: '800',
    lineHeight: 12,
    textAlign: 'center',
  },

  taskList: {
    overflow: 'hidden',
    borderWidth: 1,
    borderRadius: 15,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
  },
  taskRow: {
    position: 'relative',
    minHeight: 63,
    flexDirection: 'row',
    alignItems: 'center',
    paddingLeft: 13,
    paddingRight: 10,
  },
  taskAccent: {
    position: 'absolute',
    top: 0,
    bottom: 0,
    left: 0,
    width: 3,
  },
  taskTimeColumn: {
    width: 53,
    paddingRight: 9,
    borderRightWidth: 1,
    borderRightColor: BRAND.borderSoft,
  },
  taskTime: {
    fontSize: 12.5,
    fontWeight: '900',
  },
  taskBody: {
    flex: 1,
    minWidth: 0,
    paddingLeft: 10,
  },
  taskTopRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  taskRouteTitle: {
    flex: 1,
    minWidth: 0,
    marginRight: 7,
    fontSize: 9.5,
    fontWeight: '700',
  },
  taskStatusBadge: {
    paddingHorizontal: 7,
    paddingVertical: 4,
    borderRadius: 6,
  },
  taskStatusText: {
    fontSize: 7.5,
    fontWeight: '800',
  },
  taskMetaText: {
    marginTop: 5,
    color: BRAND.muted,
    fontSize: 8.5,
    fontWeight: '500',
  },

  performanceCard: {
    marginTop: 14,
    padding: 15,
    borderWidth: 1,
    borderRadius: 17,
    shadowColor: '#10264B',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.05,
    shadowRadius: 11,
    elevation: 3,
  },
  performanceHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  performanceTitle: {
    fontSize: 15.5,
    fontWeight: '900',
  },
  periodChip: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 5,
    backgroundColor: BRAND.primarySoft,
    borderRadius: 7,
  },
  periodChipText: {
    color: BRAND.primary,
    fontSize: 8,
    fontWeight: '700',
  },
  performanceScoreRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 12,
  },
  performanceScore: {
    color: BRAND.success,
    fontSize: 24,
    fontWeight: '900',
  },
  performanceScoreLabel: {
    marginLeft: 8,
    color: BRAND.muted,
    fontSize: 8.5,
    fontWeight: '600',
  },
  progressTrack: {
    flex: 1,
    height: 8,
    overflow: 'hidden',
    marginLeft: 12,
    backgroundColor: '#E5E9EF',
    borderRadius: 5,
  },
  progressFill: {
    height: '100%',
    backgroundColor: BRAND.success,
    borderRadius: 5,
  },
  performanceMetrics: {
    flexDirection: 'row',
    marginTop: 16,
    paddingTop: 13,
    borderTopWidth: 1,
    borderTopColor: BRAND.borderSoft,
  },
  performanceMetric: {
    flex: 1,
    minWidth: 0,
    alignItems: 'center',
    paddingHorizontal: 3,
    borderRightWidth: 1,
    borderRightColor: BRAND.borderSoft,
  },
  performanceMetricIcon: {
    width: 33,
    height: 33,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 17,
  },
  performanceMetricLabel: {
    marginTop: 5,
    color: BRAND.muted,
    fontSize: 7.3,
    fontWeight: '600',
    textAlign: 'center',
  },
  performanceMetricValue: {
    marginTop: 2,
    fontSize: 11.5,
    fontWeight: '900',
    textAlign: 'center',
  },
  performanceMetricSubValue: {
    marginTop: 2,
    color: BRAND.muted,
    fontSize: 7.3,
    textAlign: 'center',
  },

  emptyAssignment: {
    alignItems: 'center',
    paddingVertical: 25,
  },
  emptySchedule: {
    alignItems: 'center',
    paddingVertical: 26,
  },
  emptyIcon: {
    width: 48,
    height: 48,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 15,
    backgroundColor: BRAND.primarySoft,
  },
  emptyTitle: {
    marginTop: 9,
    fontSize: 11.5,
    fontWeight: '800',
  },
  emptyDescription: {
    maxWidth: 250,
    marginTop: 3,
    color: BRAND.subtle,
    fontSize: 8.5,
    lineHeight: 12,
    textAlign: 'center',
  },
  bottomSpacer: {
    height: 28,
  },
});

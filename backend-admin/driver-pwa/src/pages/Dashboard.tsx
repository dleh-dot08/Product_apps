import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  Menu, 
  Bell, 
  MapPin, 
  Clock, 
  Navigation, 
  LogOut, 
  Truck, 
  CheckCircle2, 
  Calendar, 
  FileText, 
  AlertTriangle,
  PlayCircle,
  Camera,
  ShieldCheck,
  ChevronRight,
  TrendingUp,
  Award
} from 'lucide-react';
import api from '../services/api';
import { requestScreenWakeLock } from '../services/location';
import { BottomNav } from '../components/BottomNav';
import { ClockInModal } from '../components/modals/ClockInModal';
import { ClockOutModal } from '../components/modals/ClockOutModal';
import { ExpenseModal } from '../components/modals/ExpenseModal';
import { NotificationModal } from '../components/modals/NotificationModal';

const REMOTE_ASSETS = {
  hero: 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1600&q=88',
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
};

export const DashboardPage: React.FC = () => {
  const navigate = useNavigate();
  const [driverUser] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem('driver_user') || '{}');
    } catch {
      return {};
    }
  });

  const [dashboardData, setDashboardData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [, setRefreshing] = useState(false);

  // Modal states
  const [isClockInModalVisible, setClockInModalVisible] = useState(false);
  const [isClockOutModalVisible, setClockOutModalVisible] = useState(false);
  const [isExpenseModalVisible, setExpenseModalVisible] = useState(false);
  const [isNotificationModalVisible, setNotificationModalVisible] = useState(false);
  const [period, setPeriod] = useState<'today' | 'week' | 'month'>('today');

  const fetchDashboard = useCallback(async () => {
    try {
      setRefreshing(true);
      const res = await api.get(`/driver/dashboard?period=${period}`);
      if (res.data?.status === 'success' || res.data?.data) {
        setDashboardData(res.data.data || res.data);
      }
    } catch (err) {
      console.error('Failed to fetch dashboard', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [period]);

  useEffect(() => {
    requestScreenWakeLock();
    fetchDashboard();
  }, [fetchDashboard]);

  const handleLogout = () => {
    if (window.confirm('Apakah Anda yakin ingin keluar?')) {
      localStorage.removeItem('driver_token');
      localStorage.removeItem('driver_user');
      navigate('/login');
    }
  };

  const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat pagi';
    if (hour < 15) return 'Selamat siang';
    if (hour < 18) return 'Selamat sore';
    return 'Selamat malam';
  };

  const driverName = driverUser.name || 'Driver AQPA';
  const driverInitial = driverName
    .split(' ')
    .filter(Boolean)
    .map((part: string) => part.charAt(0))
    .join('')
    .slice(0, 2)
    .toUpperCase();

  const todayTasks: any[] = dashboardData?.today_tasks?.filter(
    (t: any) => !['failed', 'cancelled', 'terkendala'].includes(t.status?.toLowerCase() ?? '')
  ) ?? [];

  const activeTask = dashboardData?.active_task ?? null;
  const activeShift = dashboardData?.active_shift ?? null;

  const vehiclePlate = activeTask?.vehicle_plate_number || activeTask?.vehicle?.plate_number || activeShift?.vehicle_plate_number || (todayTasks.length > 0 ? todayTasks[0].vehicle_plate_number : null) || 'Belum Ada';
  const vehicleName = activeTask?.vehicle_name || activeTask?.vehicle?.vehicle_name || activeTask?.vehicle?.name || activeShift?.vehicle_name || (todayTasks.length > 0 ? todayTasks[0].vehicle_name : null) || '-';

  const onTime = Math.min(Math.max(dashboardData?.performance?.on_time_percentage ?? 0, 0), 100);

  const getStatusBadge = (status: string) => {
    switch (status?.toLowerCase()) {
      case 'on_route':
        return { label: 'Sedang Berjalan', bg: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
      case 'arrived':
        return { label: 'Tiba di Lokasi', bg: 'bg-amber-50 text-amber-700 border-amber-200' };
      case 'delivered':
        return { label: 'Selesai', bg: 'bg-sky-50 text-[#0756C6] border-sky-200' };
      default:
        return { label: 'Dijadwalkan', bg: 'bg-blue-50 text-blue-700 border-blue-200' };
    }
  };

  return (
    <div className="min-h-screen bg-[#F5F7FB] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-24 overflow-x-hidden">
      {/* Hero Section dengan Background Image & Gradasi Biru 1:1 */}
      <div 
        className="relative bg-cover bg-center min-h-[340px] flex flex-col justify-between p-5 pb-8"
        style={{ backgroundImage: `url(${REMOTE_ASSETS.hero})` }}
      >
        <div 
          className="absolute inset-0"
          style={{
            background: 'linear-gradient(180deg, rgba(2, 43, 103, 0.65) 0%, rgba(5, 79, 183, 0.92) 50%, #0756C6 100%)'
          }}
        />

        {/* Top Navbar */}
        <div className="relative z-10 flex items-center justify-between">
          <button 
            onClick={() => alert('Menu Operasional AQPA')}
            className="w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white border border-white/20 active:scale-95 transition"
          >
            <Menu className="w-5 h-5" />
          </button>

          <div className="flex items-center gap-2">
            <button 
              onClick={() => setNotificationModalVisible(true)}
              className="relative w-10 h-10 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white border border-white/20 active:scale-95 transition"
            >
              <Bell className="w-5 h-5" />
              {todayTasks.some(t => t.status === 'assigned') && (
                <span className="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-pulse" />
              )}
            </button>
            <button 
              onClick={handleLogout}
              className="w-10 h-10 rounded-xl bg-rose-500/20 backdrop-blur-md flex items-center justify-center text-rose-200 border border-rose-400/30 active:scale-95 transition"
              title="Logout"
            >
              <LogOut className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Profile Row & Vehicle Chip 1:1 */}
        <div className="relative z-10 flex items-center justify-between gap-3 mt-4">
          <div className="flex items-center gap-3">
            <div className="relative">
              <div className="w-13 h-13 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white font-black text-lg border-2 border-white/40 shadow-inner">
                {driverInitial}
              </div>
              <span className="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-[#0756C6]" />
            </div>

            <div>
              <p className="text-sky-200 text-xs font-medium">{getGreeting()},</p>
              <h2 className="text-white font-extrabold text-base leading-tight drop-shadow-sm line-clamp-1">
                {driverName}
              </h2>
              <div className="flex items-center gap-1.5 mt-0.5 text-[11px] text-white/90">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                <span className="font-semibold">Online</span>
                <span className="text-white/40">•</span>
                <span className="text-sky-100">{driverUser?.role?.name || 'Driver'}</span>
              </div>
            </div>
          </div>

          <div className="bg-black/25 backdrop-blur-md px-3 py-2 rounded-xl border border-white/15 text-right flex items-center gap-2">
            <Truck className="w-5 h-5 text-sky-200 shrink-0" />
            <div className="text-left">
              <p className="text-white font-mono font-bold text-xs leading-none">{vehiclePlate}</p>
              <p className="text-sky-200 text-[10px] mt-0.5 leading-none line-clamp-1">{vehicleName}</p>
            </div>
          </div>
        </div>

        {/* 4 Summary Cards Grid 1:1 */}
        <div className="relative z-10 grid grid-cols-4 gap-2 mt-4 pt-2">
          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-100 text-center flex flex-col justify-between">
            <span className="text-[10px] font-bold text-slate-400 leading-tight">Tugas Hari Ini</span>
            <p className="text-base font-black text-[#0756C6] my-0.5">
              {dashboardData?.today_trips_count ?? todayTasks.length ?? 0}
            </p>
            <span className="text-[9px] text-slate-400 font-medium">Tugas</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-100 text-center flex flex-col justify-between">
            <span className="text-[10px] font-bold text-slate-400 leading-tight">Selesai</span>
            <p className="text-base font-black text-emerald-600 my-0.5">
              {dashboardData?.completed_trips_count ?? 0}
            </p>
            <span className="text-[9px] text-emerald-600 font-medium">Tugas</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-100 text-center flex flex-col justify-between">
            <span className="text-[10px] font-bold text-slate-400 leading-tight">Tepat Waktu</span>
            <p className="text-base font-black text-amber-500 my-0.5">
              {onTime}%
            </p>
            <span className="text-[9px] text-amber-600 font-medium">Performa</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-100 text-center flex flex-col justify-between">
            <span className="text-[10px] font-bold text-slate-400 leading-tight">Jarak Hari Ini</span>
            <p className="text-base font-black text-purple-600 my-0.5">
              {Number(dashboardData?.distance_today ?? 0).toFixed(0)}
            </p>
            <span className="text-[9px] text-purple-600 font-medium">Km</span>
          </div>
        </div>
      </div>

      {/* Main Content Area */}
      <main className="px-4 -mt-3 relative z-20 space-y-4">
        {/* Shift Card (Clock In / Clock Out) 1:1 */}
        <section className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
          <div className="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 className="text-xs font-bold text-slate-700 tracking-wide uppercase">Status Shift</h4>
            <span className={`px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${
              activeShift ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'
            }`}>
              {activeShift ? 'Sedang Aktif' : 'Belum Mulai'}
            </span>
          </div>

          {!activeShift ? (
            <div className="text-center py-4">
              <div className="w-12 h-12 rounded-2xl bg-sky-50 text-[#0756C6] flex items-center justify-center mx-auto mb-2 border border-sky-100">
                <Clock className="w-6 h-6" />
              </div>
              <h5 className="text-xs font-bold text-slate-800">Anda belum Clock In</h5>
              <p className="text-[11px] text-slate-500 mt-1 max-w-xs mx-auto">
                Silakan Clock In terlebih dahulu untuk mengecek kondisi kendaraan dan memulai shift hari ini.
              </p>
              <button
                onClick={() => setClockInModalVisible(true)}
                className="mt-3.5 w-full py-3 bg-[#0756C6] hover:bg-[#063B8C] text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
              >
                <Clock className="w-4 h-4" />
                <span>Clock In (Mulai Shift)</span>
              </button>
            </div>
          ) : (
            <div className="mt-3">
              <div className="grid grid-cols-3 gap-2 border-b border-slate-100 pb-3 text-center">
                <div>
                  <p className="text-[10px] text-slate-400 font-semibold">Waktu Mulai</p>
                  <p className="text-xs font-bold text-slate-800 mt-0.5">
                    {activeShift.start_time ? new Date(activeShift.start_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB' : '-'}
                  </p>
                </div>
                <div>
                  <p className="text-[10px] text-slate-400 font-semibold">Odo Awal</p>
                  <p className="text-xs font-bold text-slate-800 mt-0.5">{activeShift.start_odometer || 0} KM</p>
                </div>
                <div>
                  <p className="text-[10px] text-slate-400 font-semibold">Kendaraan</p>
                  <p className="text-xs font-bold text-slate-800 mt-0.5 line-clamp-1">{activeShift.vehicle_plate_number || '-'}</p>
                </div>
              </div>

              <button
                onClick={() => setClockOutModalVisible(true)}
                className="mt-3 w-full py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
              >
                <LogOut className="w-4 h-4" />
                <span>Clock Out (Akhiri Shift)</span>
              </button>
            </div>
          )}
        </section>

        {/* Penugasan Aktif Card 1:1 */}
        {activeTask && (
          <section className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
              <h4 className="text-xs font-bold text-slate-700 tracking-wide uppercase">Penugasan Aktif</h4>
              <div className="flex items-center gap-1.5">
                <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                  {activeTask.task_type === 'pickup' ? 'PICKUP' : 'Distribusi'}
                </span>
                <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold border ${getStatusBadge(activeTask.status).bg}`}>
                  {getStatusBadge(activeTask.status).label}
                </span>
              </div>
            </div>

            <div className="mt-3 space-y-3">
              <div>
                <span className="text-[10px] font-mono font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                  {activeTask.reference_number || `TRIP-${activeTask.id}`}
                </span>
                <h4 className="text-sm font-bold text-slate-800 mt-1">
                  {activeTask.pickup_name || activeTask.destination || 'Pengiriman Barang Logistik'}
                </h4>
              </div>

              {/* Horizontal Route Timeline 1:1 */}
              <div className="p-3 bg-slate-50/80 rounded-xl border border-slate-200/60 flex items-center justify-between gap-2 text-xs">
                <div className="flex-1">
                  <span className="text-[10px] text-slate-400 font-bold block">Dari</span>
                  <p className="font-bold text-slate-800 line-clamp-1">{activeTask.pickup_name || 'Lokasi Pickup'}</p>
                  <p className="text-[10px] text-slate-500 line-clamp-1">{activeTask.pickup_location || '-'}</p>
                </div>
                <div className="w-6 h-6 rounded-full bg-sky-100 flex items-center justify-center text-[#0756C6] shrink-0">
                  <ChevronRight className="w-4 h-4" />
                </div>
                <div className="flex-1 text-right">
                  <span className="text-[10px] text-slate-400 font-bold block">Ke</span>
                  <p className="font-bold text-slate-800 line-clamp-1">Tujuan</p>
                  <p className="text-[10px] text-slate-500 line-clamp-1">{activeTask.destination || '-'}</p>
                </div>
              </div>

              {/* Action: Lihat Rute & Detail Tugas */}
              <button
                onClick={() => navigate(`/tasks/${activeTask.id}`)}
                className="w-full py-3 bg-[#0756C6] hover:bg-[#063B8C] text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
              >
                <Navigation className="w-4 h-4" />
                <span>Lihat Rute & Detail Tugas</span>
              </button>
            </div>
          </section>
        )}

        {/* Section: Aksi Cepat 1:1 */}
        <div>
          <h4 className="text-xs font-bold text-slate-700 tracking-wide uppercase mb-2.5">Aksi Cepat</h4>
          <div className="grid grid-cols-4 gap-2">
            <button
              onClick={() => {
                if (activeTask) navigate(`/tasks/${activeTask.id}`);
                else alert('Belum ada penugasan aktif.');
              }}
              className="p-3 rounded-2xl bg-[#0756C6] text-white flex flex-col items-center justify-center text-center shadow-sm active:scale-95 transition"
            >
              <PlayCircle className="w-6 h-6 mb-1" />
              <span className="text-[10px] font-bold leading-tight">Mulai Tugas</span>
            </button>

            <button
              onClick={() => {
                if (activeTask) navigate(`/tasks/${activeTask.id}`);
                else alert('Belum ada penugasan aktif.');
              }}
              className="p-3 rounded-2xl bg-[#0F9FA8] text-white flex flex-col items-center justify-center text-center shadow-sm active:scale-95 transition"
            >
              <FileText className="w-6 h-6 mb-1" />
              <span className="text-[10px] font-bold leading-tight">Update Status</span>
            </button>

            <button
              onClick={() => alert('Fitur Laporkan Kendala sedang dipersiapkan.')}
              className="p-3 rounded-2xl bg-[#F97316] text-white flex flex-col items-center justify-center text-center shadow-sm active:scale-95 transition"
            >
              <AlertTriangle className="w-6 h-6 mb-1" />
              <span className="text-[10px] font-bold leading-tight">Kendala</span>
            </button>

            <button
              onClick={() => setExpenseModalVisible(true)}
              className="p-3 rounded-2xl bg-[#6D48D7] text-white flex flex-col items-center justify-center text-center shadow-sm active:scale-95 transition"
            >
              <Camera className="w-6 h-6 mb-1" />
              <span className="text-[10px] font-bold leading-tight">Biaya / BBM</span>
            </button>
          </div>
        </div>

        {/* Section: Jadwal Hari Ini 1:1 */}
        <section className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
          <div className="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 className="text-xs font-bold text-slate-700 tracking-wide uppercase">Jadwal Hari Ini</h4>
            <button
              onClick={() => navigate('/tasks')}
              className="text-xs font-bold text-[#0756C6] hover:underline"
            >
              Lihat Semua
            </button>
          </div>

          <div className="mt-3 divide-y divide-slate-100">
            {todayTasks.length > 0 ? (
              todayTasks.map((task, idx) => (
                <div
                  key={idx}
                  onClick={() => navigate(`/tasks/${task.id}`)}
                  className="py-3 flex items-center justify-between first:pt-0 last:pb-0 cursor-pointer hover:bg-slate-50/50 rounded-lg transition px-1"
                >
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-sky-50 text-[#0756C6] flex flex-col items-center justify-center border border-sky-100 shrink-0">
                      <span className="text-xs font-black leading-none">
                        {task.dispatch_date ? new Date(task.dispatch_date).getDate() : idx + 1}
                      </span>
                      <span className="text-[8px] font-bold uppercase">
                        {task.dispatch_date ? new Date(task.dispatch_date).toLocaleString('id-ID', { month: 'short' }) : 'Hari'}
                      </span>
                    </div>

                    <div>
                      <h5 className="text-xs font-bold text-slate-800 line-clamp-1">
                        {task.pickup_name || task.destination || 'Pengiriman Barang'}
                      </h5>
                      <p className="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                        {task.destination || task.pickup_location || '-'}
                      </p>
                      <div className="flex items-center gap-2 mt-1">
                        <span className={`px-1.5 py-0.2 rounded text-[9px] font-bold border ${getStatusBadge(task.status).bg}`}>
                          {getStatusBadge(task.status).label}
                        </span>
                        <span className="text-[10px] text-slate-400 font-mono">
                          {task.reference_number || `TSK-${task.id}`}
                        </span>
                      </div>
                    </div>
                  </div>

                  <ChevronRight className="w-4 h-4 text-slate-300 shrink-0" />
                </div>
              ))
            ) : (
              <div className="py-6 text-center text-slate-400 text-xs">
                Tidak ada jadwal tugas tambahan hari ini.
              </div>
            )}
          </div>
        </section>

        {/* Section: Performa Driver 1:1 */}
        <section className="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
          <div className="flex items-center justify-between border-b border-slate-100 pb-3">
            <div className="flex items-center gap-2">
              <Award className="w-4 h-4 text-[#0756C6]" />
              <h4 className="text-xs font-bold text-slate-800 tracking-wide uppercase">Performa Anda</h4>
            </div>

            {/* Filter Period Tabs */}
            <div className="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg text-[10px] font-bold">
              {(['today', 'week', 'month'] as const).map((p) => (
                <button
                  key={p}
                  onClick={() => setPeriod(p)}
                  className={`px-2 py-1 rounded-md transition ${
                    period === p ? 'bg-white text-[#0756C6] shadow-xs' : 'text-slate-500'
                  }`}
                >
                  {p === 'today' ? 'Hari Ini' : p === 'week' ? 'Minggu Ini' : 'Bulan Ini'}
                </button>
              ))}
            </div>
          </div>

          {/* Big Score Row 1:1 */}
          <div className="text-center py-3">
            <span className="text-3xl font-black text-[#0756C6] font-['Inter',sans-serif]">{onTime}%</span>
            <span className="text-[11px] text-[#64748b] block font-medium mt-0.5">Skor Performa</span>
            <div className="w-full bg-[#EAF3FF] h-2 rounded-full mt-2.5 overflow-hidden max-w-xs mx-auto">
              <div 
                className="bg-[#0756C6] h-full rounded-full transition-all duration-500" 
                style={{ width: `${onTime}%` }} 
              />
            </div>
          </div>

          {/* 4 Performance Metrics Grid 1:1 */}
          <div className="grid grid-cols-4 gap-2 pt-2 border-t border-slate-100 text-center">
            <div className="flex flex-col items-center">
              <div className="w-8 h-8 rounded-full bg-[#EAF8F1] text-[#16A36A] flex items-center justify-center mb-1">
                <CheckCircle2 className="w-4 h-4" />
              </div>
              <span className="text-[9px] text-[#64748b] font-medium leading-tight">Tugas Selesai</span>
              <span className="text-xs font-bold text-slate-800 mt-0.5">{dashboardData?.completed_trips_count || 0}</span>
              <span className="text-[8px] text-[#64748b]">dari {dashboardData?.today_trips_count || todayTasks.length || 0}</span>
            </div>

            <div className="flex flex-col items-center">
              <div className="w-8 h-8 rounded-full bg-[#EAF3FF] text-[#0756C6] flex items-center justify-center mb-1">
                <Clock className="w-4 h-4" />
              </div>
              <span className="text-[9px] text-[#64748b] font-medium leading-tight">Ketepatan</span>
              <span className="text-xs font-bold text-slate-800 mt-0.5">{onTime}%</span>
              <span className="text-[8px] text-[#64748b]">Tepat Waktu</span>
            </div>

            <div className="flex flex-col items-center">
              <div className="w-8 h-8 rounded-full bg-[#FFF2E8] text-[#F97316] flex items-center justify-center mb-1">
                <AlertTriangle className="w-4 h-4" />
              </div>
              <span className="text-[9px] text-[#64748b] font-medium leading-tight">Kendala</span>
              <span className="text-xs font-bold text-slate-800 mt-0.5">{dashboardData?.performance?.kendala_trip || 0}</span>
              <span className="text-[8px] text-[#64748b]">Dilaporkan</span>
            </div>

            <div className="flex flex-col items-center">
              <div className="w-8 h-8 rounded-full bg-[#F2ECFF] text-[#6D48D7] flex items-center justify-center mb-1">
                <Navigation className="w-4 h-4" />
              </div>
              <span className="text-[9px] text-[#64748b] font-medium leading-tight">Jarak Tempuh</span>
              <span className="text-xs font-bold text-slate-800 mt-0.5">{Number(dashboardData?.distance_today ?? 0).toFixed(0)}</span>
              <span className="text-[8px] text-[#64748b]">Km</span>
            </div>
          </div>
        </section>
      </main>

      {/* Bottom Navigation Tab Bar 1:1 */}
      <BottomNav />

      {/* Modals */}
      <ClockInModal
        visible={isClockInModalVisible}
        onClose={() => setClockInModalVisible(false)}
        onSuccess={fetchDashboard}
        assignedVehicle={`${vehiclePlate} - ${vehicleName}`}
      />

      <ClockOutModal
        visible={isClockOutModalVisible}
        onClose={() => setClockOutModalVisible(false)}
        onSuccess={fetchDashboard}
      />

      <ExpenseModal
        visible={isExpenseModalVisible}
        onClose={() => setExpenseModalVisible(false)}
        onSuccess={fetchDashboard}
      />

      <NotificationModal
        visible={isNotificationModalVisible}
        onClose={() => setNotificationModalVisible(false)}
        tasks={todayTasks}
      />
    </div>
  );
};

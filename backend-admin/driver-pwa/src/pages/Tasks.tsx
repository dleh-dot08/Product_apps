import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  Search, 
  Filter, 
  X, 
  Clock, 
  MapPin, 
  Truck, 
  ChevronRight, 
  Calendar, 
  Loader2, 
  CheckCircle2, 
  AlertCircle,
  Package
} from 'lucide-react';
import api from '../services/api';
import { BottomNav } from '../components/BottomNav';

interface DriverTask {
  id: number | string;
  reference_number?: string | null;
  task_type?: string;
  status?: string;
  assigned_at?: string | null;
  pickup_date?: string | null;
  dispatch_date?: string | null;
  pickup_name?: string | null;
  pickup_location?: string | null;
  destination?: string | null;
  vehicle_plate_number?: string | null;
  vehicle_name?: string | null;
  quantity?: number | string | null;
  unit?: string | null;
}

export const TasksPage: React.FC = () => {
  const navigate = useNavigate();
  const [tasks, setTasks] = useState<DriverTask[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [filterType, setFilterType] = useState('all');
  const [isFilterOpen, setIsFilterOpen] = useState(false);

  const TABS = [
    { id: 'all', label: 'Semua' },
    { id: 'assigned', label: 'Menunggu' },
    { id: 'on_route', label: 'Berjalan' },
    { id: 'delivered', label: 'Selesai' },
    { id: 'pending', label: 'Tertunda' },
  ];

  const fetchTasks = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/pickup', {
        params: {
          search,
          status: status === 'all' ? '' : status,
          type: filterType === 'all' ? '' : filterType,
        },
      });

      if (res.data?.status === 'success') {
        setTasks(res.data.data || []);
      }
    } catch (err) {
      console.error('Failed to fetch tasks', err);
    } finally {
      setLoading(false);
    }
  }, [search, status, filterType]);

  useEffect(() => {
    const timer = setTimeout(() => {
      fetchTasks();
    }, 300);
    return () => clearTimeout(timer);
  }, [fetchTasks]);

  const getStatusBadge = (taskStatus?: string) => {
    switch (taskStatus?.toLowerCase()) {
      case 'on_route':
      case 'arrived':
        return { label: 'Berjalan', bg: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
      case 'delivered':
      case 'completed':
        return { label: 'Selesai', bg: 'bg-sky-50 text-[#0756C6] border-sky-200' };
      case 'pending':
        return { label: 'Tertunda', bg: 'bg-rose-50 text-rose-700 border-rose-200' };
      default:
        return { label: 'Menunggu', bg: 'bg-amber-50 text-amber-700 border-amber-200' };
    }
  };

  return (
    <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-24 overflow-x-hidden">
      {/* Header Top Bar 1:1 */}
      <header className="bg-[#0756C6] text-white px-5 py-4 sticky top-0 z-30 shadow-sm flex items-center justify-between">
        <h1 className="font-extrabold text-base tracking-wide">Daftar Tugas</h1>
        <span className="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/20 border border-white/20">
          {tasks.length} Total
        </span>
      </header>

      {/* Main Content */}
      <main className="p-4 space-y-3.5 flex-1">
        {/* Horizontal Segmented Tabs 1:1 */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
          {TABS.map((tab) => {
            const active = status === tab.id;
            return (
              <button
                key={tab.id}
                onClick={() => setStatus(tab.id)}
                className={`px-3 py-1.5 rounded-xl font-bold text-xs whitespace-nowrap transition border ${
                  active
                    ? 'bg-[#0756C6] text-white border-[#0756C6] shadow-sm'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                }`}
              >
                {tab.label}
              </button>
            );
          })}
        </div>

        {/* Search & Filter Bar 1:1 */}
        <div className="flex items-center gap-2">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Cari rute, gudang, atau nomor TRP..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0756C6]"
            />
            {search && (
              <button
                onClick={() => setSearch('')}
                className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          <button
            onClick={() => setIsFilterOpen(!isFilterOpen)}
            className={`p-2.5 rounded-xl border flex items-center gap-1.5 text-xs font-bold transition ${
              filterType !== 'all'
                ? 'bg-sky-50 text-[#0756C6] border-[#0756C6]'
                : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'
            }`}
          >
            <Filter className="w-4 h-4" />
            <span>Filter</span>
          </button>
        </div>

        {/* Filter Popup Dropdown */}
        {isFilterOpen && (
          <div className="p-3 bg-white rounded-xl border border-slate-200 shadow-md text-xs space-y-2 animate-in fade-in">
            <p className="font-bold text-slate-700">Tipe Pengiriman</p>
            <div className="flex gap-2">
              {[
                { id: 'all', label: 'Semua Tipe' },
                { id: 'delivery', label: 'Distribusi' },
                { id: 'pickup', label: 'Pickup' },
              ].map((t) => (
                <button
                  key={t.id}
                  onClick={() => {
                    setFilterType(t.id);
                    setIsFilterOpen(false);
                  }}
                  className={`px-3 py-1.5 rounded-lg font-bold border ${
                    filterType === t.id
                      ? 'bg-sky-50 text-[#0756C6] border-[#0756C6]'
                      : 'bg-slate-50 text-slate-600 border-slate-200'
                  }`}
                >
                  {t.label}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* Task Cards List 1:1 */}
        {loading ? (
          <div className="py-16 text-center text-slate-400">
            <Loader2 className="w-7 h-7 animate-spin mx-auto text-[#0756C6]" />
            <p className="text-xs mt-2 font-medium">Memuat tugas...</p>
          </div>
        ) : tasks.length > 0 ? (
          <div className="space-y-3">
            {tasks.map((task) => {
              const badge = getStatusBadge(task.status);
              const isPickup = task.task_type === 'pickup';

              return (
                <div
                  key={task.id}
                  onClick={() => navigate(`/tasks/${task.id}`)}
                  className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 hover:border-[#0756C6] active:scale-[0.99] transition cursor-pointer space-y-3"
                >
                  {/* Card Header (Reference, Type & Status Badge) */}
                  <div className="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span className="font-mono font-bold text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded">
                      {task.reference_number || `TRP-${task.id}`}
                    </span>

                    <div className="flex items-center gap-1.5">
                      <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                        isPickup ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200'
                      }`}>
                        {isPickup ? 'PICKUP' : 'Distribusi'}
                      </span>
                      <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold border ${badge.bg}`}>
                        {badge.label}
                      </span>
                    </div>
                  </div>

                  {/* Route (Dari -> Ke) */}
                  <div className="space-y-2 text-xs">
                    <div className="flex items-start gap-2">
                      <div className="w-2 h-2 rounded-full bg-[#0756C6] mt-1 shrink-0" />
                      <div>
                        <span className="text-[10px] text-slate-400 block leading-tight font-medium">Dari</span>
                        <p className="font-bold text-slate-800 leading-snug">{task.pickup_name || 'Lokasi Pickup'}</p>
                        <p className="text-[11px] text-slate-500 leading-tight line-clamp-1">{task.pickup_location || '-'}</p>
                      </div>
                    </div>

                    <div className="flex items-start gap-2">
                      <div className="w-2 h-2 rounded-full bg-rose-500 mt-1 shrink-0" />
                      <div>
                        <span className="text-[10px] text-slate-400 block leading-tight font-medium">Ke</span>
                        <p className="font-bold text-slate-800 leading-snug">{task.destination || 'Tujuan Pengiriman'}</p>
                      </div>
                    </div>
                  </div>

                  {/* Card Footer (Vehicle, Date, Arrow) */}
                  <div className="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <div className="flex items-center gap-1.5">
                      <Truck className="w-3.5 h-3.5 text-slate-400" />
                      <span className="font-medium">{task.vehicle_plate_number || 'Truk Logistik'}</span>
                    </div>

                    <div className="flex items-center gap-1 text-[#0756C6] font-bold">
                      <span>Detail Rute</span>
                      <ChevronRight className="w-3.5 h-3.5" />
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        ) : (
          <div className="py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-6">
            <Package className="w-10 h-10 mx-auto text-slate-300" />
            <h4 className="text-sm font-bold text-slate-700 mt-2">Tidak Ada Tugas</h4>
            <p className="text-xs text-slate-400 mt-1">Tidak ada tugas dengan filter yang dipilih.</p>
          </div>
        )}
      </main>

      <BottomNav />
    </div>
  );
};

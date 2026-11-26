import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  User, 
  Phone, 
  Mail, 
  MapPin, 
  CreditCard, 
  Calendar, 
  Star, 
  Clock, 
  CheckCircle2, 
  Truck, 
  LogOut, 
  ChevronRight, 
  ShieldCheck, 
  Settings, 
  HelpCircle,
  FileCheck,
  Edit3
} from 'lucide-react';
import { BottomNav } from '../components/BottomNav';

export const ProfilePage: React.FC = () => {
  const navigate = useNavigate();
  const [user, setUser] = useState<any>(() => {
    try {
      return JSON.parse(localStorage.getItem('driver_user') || '{}');
    } catch {
      return {};
    }
  });

  const handleLogout = () => {
    if (window.confirm('Apakah Anda yakin ingin keluar dari aplikasi?')) {
      localStorage.removeItem('driver_token');
      localStorage.removeItem('driver_user');
      navigate('/login');
    }
  };

  const driverName = user.name || 'Driver Operasional';
  const driverInitial = driverName
    .split(' ')
    .filter(Boolean)
    .map((part: string) => part.charAt(0))
    .join('')
    .slice(0, 2)
    .toUpperCase();

  return (
    <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-24 overflow-x-hidden">
      {/* Header Top Bar */}
      <header className="bg-[#0756C6] text-white px-5 py-4 sticky top-0 z-30 shadow-sm flex items-center justify-between">
        <h1 className="font-extrabold text-base tracking-wide">Profil Driver</h1>
        <button
          onClick={handleLogout}
          className="text-xs font-bold text-rose-200 hover:text-white flex items-center gap-1"
        >
          <LogOut className="w-4 h-4" />
          <span>Keluar</span>
        </button>
      </header>

      {/* Main Container */}
      <main className="p-4 space-y-4 flex-1">
        {/* Profile Card Header 1:1 */}
        <div className="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center gap-4">
          <div className="w-16 h-16 rounded-2xl bg-sky-50 text-[#0756C6] border-2 border-sky-100 flex items-center justify-center font-black text-xl shrink-0 shadow-inner">
            {driverInitial || 'DR'}
          </div>

          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-1.5">
              <h3 className="font-extrabold text-base text-slate-800 leading-tight truncate">
                {driverName}
              </h3>
              <ShieldCheck className="w-4 h-4 text-emerald-500 shrink-0" />
            </div>

            <p className="text-xs text-slate-400 font-mono mt-0.5">
              ID: {user.employee_id || user.id || 'DRV-AQPA'}
            </p>

            <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-[#0756C6] border border-sky-100 mt-1">
              {user.role?.name || 'Driver Utama'}
            </span>
          </div>
        </div>

        {/* 4 Quick Stats Grid 1:1 */}
        <div className="grid grid-cols-4 gap-2 text-center">
          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-200/70">
            <CheckCircle2 className="w-5 h-5 text-emerald-500 mx-auto" />
            <p className="text-sm font-black text-slate-800 mt-1">{user?.stats?.completed_tasks || 24}</p>
            <span className="text-[9px] text-slate-400 font-semibold block leading-tight">Tugas Selesai</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-200/70">
            <Clock className="w-5 h-5 text-amber-500 mx-auto" />
            <p className="text-sm font-black text-slate-800 mt-1">{user?.stats?.on_time_percentage || 98}%</p>
            <span className="text-[9px] text-slate-400 font-semibold block leading-tight">Tepat Waktu</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-200/70">
            <Star className="w-5 h-5 text-amber-400 fill-amber-400 mx-auto" />
            <p className="text-sm font-black text-slate-800 mt-1">4.9</p>
            <span className="text-[9px] text-slate-400 font-semibold block leading-tight">Rating</span>
          </div>

          <div className="bg-white rounded-2xl p-2.5 shadow-sm border border-slate-200/70">
            <Truck className="w-5 h-5 text-purple-500 mx-auto" />
            <p className="text-sm font-black text-slate-800 mt-1">{user?.stats?.driving_hours || 120}</p>
            <span className="text-[9px] text-slate-400 font-semibold block leading-tight">Jam Nyetir</span>
          </div>
        </div>

        {/* Section: Informasi Pribadi 1:1 */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3 text-xs">
          <h4 className="font-bold text-slate-700 uppercase tracking-wide border-b border-slate-100 pb-2">
            Informasi Pribadi
          </h4>

          <div className="space-y-2.5">
            <div className="flex items-center justify-between">
              <span className="text-slate-400 font-medium flex items-center gap-2">
                <CreditCard className="w-4 h-4 text-slate-400" /> No. SIM
              </span>
              <span className="font-bold text-slate-700">{user.sim_number || 'B2-92819281'}</span>
            </div>

            <div className="flex items-center justify-between">
              <span className="text-slate-400 font-medium flex items-center gap-2">
                <Calendar className="w-4 h-4 text-slate-400" /> Masa Berlaku SIM
              </span>
              <span className="font-bold text-slate-700">{user.sim_expiry || '12 Des 2028'}</span>
            </div>

            <div className="flex items-center justify-between">
              <span className="text-slate-400 font-medium flex items-center gap-2">
                <Mail className="w-4 h-4 text-slate-400" /> Email
              </span>
              <span className="font-bold text-slate-700">{user.email || 'driver@aqpa.com'}</span>
            </div>

            <div className="flex items-center justify-between">
              <span className="text-slate-400 font-medium flex items-center gap-2">
                <Phone className="w-4 h-4 text-slate-400" /> No. Handphone
              </span>
              <span className="font-bold text-slate-700">{user.phone || '0812-3456-7890'}</span>
            </div>
          </div>
        </div>

        {/* Section: Menu Navigasi Profil 1:1 */}
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/80 divide-y divide-slate-100 text-xs font-semibold overflow-hidden">
          <button
            onClick={() => alert('Fitur Ubah Profil')}
            className="w-full p-3.5 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"
          >
            <div className="flex items-center gap-2.5">
              <Edit3 className="w-4 h-4 text-[#0756C6]" />
              <span>Edit Profil</span>
            </div>
            <ChevronRight className="w-4 h-4 text-slate-300" />
          </button>

          <button
            onClick={() => alert('Dokumen SIM & KTP terverifikasi')}
            className="w-full p-3.5 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"
          >
            <div className="flex items-center gap-2.5">
              <FileCheck className="w-4 h-4 text-[#0756C6]" />
              <span>Dokumen Driver (SIM & KTP)</span>
            </div>
            <ChevronRight className="w-4 h-4 text-slate-300" />
          </button>

          <button
            onClick={() => alert('Bantuan Operasional: Hubungi Admin Dispatcher di WhatsApp')}
            className="w-full p-3.5 flex items-center justify-between text-slate-700 hover:bg-slate-50 transition"
          >
            <div className="flex items-center gap-2.5">
              <HelpCircle className="w-4 h-4 text-[#0756C6]" />
              <span>Bantuan & Dukungan</span>
            </div>
            <ChevronRight className="w-4 h-4 text-slate-300" />
          </button>
        </div>

        {/* Version info */}
        <div className="text-center text-[11px] text-slate-400 font-medium pt-2">
          AQPA Driver System v1.0.0 (PWA Edition)
        </div>
      </main>

      <BottomNav />
    </div>
  );
};

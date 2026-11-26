import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  ShieldCheck, 
  MapPin, 
  FileText, 
  CheckCircle2, 
  User, 
  Mail, 
  Lock, 
  Eye, 
  EyeOff, 
  ArrowRight, 
  Smartphone,
  Loader2,
  AlertCircle
} from 'lucide-react';
import api from '../services/api';
import { requestNotificationPermission } from '../services/notification';

const REMOTE_ASSETS = {
  hero: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1400&q=85',
};

export const LoginPage: React.FC = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email.trim() || !password) {
      setErrorMsg('Silakan isi email dan password.');
      return;
    }

    setLoading(true);
    setErrorMsg('');

    try {
      const userAgent = navigator.userAgent;
      const deviceName = userAgent.includes('Mobile') ? 'AQPA Driver PWA (Mobile)' : 'AQPA Driver PWA (Web)';

      const response = await api.post('/login', {
        email: email.trim(),
        password: password,
        device_name: deviceName,
      });

      if (response.data && response.data.token) {
        localStorage.setItem('driver_token', response.data.token);
        localStorage.setItem('driver_user', JSON.stringify(response.data.user || {}));

        // Minta izin notifikasi setelah login sukses
        requestNotificationPermission();

        navigate('/');
      } else {
        setErrorMsg('Respon login tidak valid');
      }
    } catch (err: any) {
      console.error(err);
      const resData = err.response?.data;
      if (resData?.errors) {
        const firstErrorKey = Object.keys(resData.errors)[0];
        setErrorMsg(resData.errors[firstErrorKey][0]);
      } else {
        setErrorMsg(resData?.message || 'Email atau password yang Anda masukkan salah.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#063B8C] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl overflow-x-hidden">
      {/* Hero Section dengan Background Image & Gradasi Biru */}
      <div 
        className="relative min-h-[360px] bg-cover bg-center flex flex-col justify-between p-6 pb-12"
        style={{ backgroundImage: `url(${REMOTE_ASSETS.hero})` }}
      >
        <div 
          className="absolute inset-0"
          style={{
            background: 'linear-gradient(180deg, rgba(4, 43, 104, 0.40) 0%, rgba(4, 55, 133, 0.76) 56%, #074FAE 100%)'
          }}
        />

        {/* Top Bar (Logo & Secure Badge) */}
        <div className="relative z-10 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30 p-1">
              <img 
                src="/aqpa-indonesia.png" 
                alt="AQPA Indonesia" 
                className="w-full h-full object-contain"
                onError={(e) => {
                  // Fallback jika logo belum termuat
                  (e.target as HTMLElement).style.display = 'none';
                }}
              />
            </div>
            <div>
              <h1 className="text-white font-black text-sm tracking-wide">AQPA INDONESIA</h1>
              <p className="text-sky-200 text-[11px] font-medium tracking-tight">Internal Logistics System</p>
            </div>
          </div>

          <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white text-xs font-semibold">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
            <span>Secure</span>
          </div>
        </div>

        {/* Hero Content Text */}
        <div className="relative z-10 mt-6">
          <h2 className="text-white text-2xl font-black tracking-wider drop-shadow-sm">
            DRIVER APPS
          </h2>
          <p className="text-sky-100 text-xs mt-1.5 leading-relaxed font-normal opacity-95">
            Monitoring tugas pengiriman, penjemputan, perjalanan dan laporan driver dalam satu aplikasi.
          </p>

          <div className="flex items-center gap-3 mt-4 pt-3 border-t border-white/15 text-white/90 text-[11px]">
            <div className="flex items-center gap-1.5 font-medium">
              <MapPin className="w-3.5 h-3.5 text-sky-300" />
              <span>Monitoring</span>
            </div>
            <div className="w-1 h-1 rounded-full bg-white/40" />
            <div className="flex items-center gap-1.5 font-medium">
              <FileText className="w-3.5 h-3.5 text-sky-300" />
              <span>Reporting</span>
            </div>
            <div className="w-1 h-1 rounded-full bg-white/40" />
            <div className="flex items-center gap-1.5 font-medium">
              <CheckCircle2 className="w-3.5 h-3.5 text-sky-300" />
              <span>Tracking</span>
            </div>
          </div>
        </div>
      </div>

      {/* Login Card Form (Menimpa hero bagian bawah) */}
      <div className="relative z-20 -mt-6 bg-[#F5F7FB] rounded-t-[32px] px-6 pt-7 pb-10 flex-1 flex flex-col justify-between">
        <div className="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
          <div className="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
              <p className="text-xs text-slate-400 font-medium">Selamat datang</p>
              <h3 className="text-lg font-bold text-slate-800">Masuk ke akun Driver</h3>
            </div>
            <div className="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-[#0A5DCC] border border-sky-100">
              <User className="w-5 h-5" />
            </div>
          </div>

          <p className="text-xs text-slate-500 mt-3 leading-relaxed">
            Gunakan akun internal perusahaan untuk mengakses tugas operasional Anda.
          </p>

          <form onSubmit={handleLogin} className="mt-5 space-y-4">
            {errorMsg && (
              <div className="p-3 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-2.5 text-rose-700 text-xs font-medium">
                <AlertCircle className="w-4 h-4 shrink-0 text-rose-500" />
                <span>{errorMsg}</span>
              </div>
            )}

            {/* Email Field */}
            <div>
              <label className="block text-xs font-bold text-slate-600 mb-1.5">
                Email
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Mail className="w-4 h-4" />
                </div>
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="nama@perusahaan.com"
                  disabled={loading}
                  className="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0756C6] focus:bg-white transition"
                  required
                />
              </div>
            </div>

            {/* Password Field */}
            <div>
              <label className="block text-xs font-bold text-slate-600 mb-1.5">
                Password
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Lock className="w-4 h-4" />
                </div>
                <input
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="Masukkan password"
                  disabled={loading}
                  className="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0756C6] focus:bg-white transition"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            {/* Tombol Masuk */}
            <button
              type="submit"
              disabled={loading}
              className="w-full mt-2 py-3.5 px-4 rounded-xl text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
              style={{
                background: 'linear-gradient(135deg, #1268E8 0%, #074EBA 100%)'
              }}
            >
              {loading ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin mr-1" />
                  <span>Memproses...</span>
                </>
              ) : (
                <>
                  <span>Masuk</span>
                  <div className="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                    <ArrowRight className="w-3 h-3 text-white" />
                  </div>
                </>
              )}
            </button>
          </form>

          {/* Device Info Footer inside Card */}
          <div className="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-400">
            <Smartphone className="w-4 h-4 text-slate-400" />
            <span>Terhubung ke Sistem Operasional AQPA</span>
          </div>
        </div>

        {/* Bottom Version */}
        <div className="text-center mt-6 text-[11px] text-slate-400 font-medium">
          AQPA Driver System v1.0.0
        </div>
      </div>
    </div>
  );
};

import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  FileText, 
  Search, 
  Filter, 
  Calendar, 
  Clock, 
  ChevronRight, 
  Truck, 
  Loader2, 
  ArrowRight,
  X,
  Package
} from 'lucide-react';
import api from '../services/api';
import { BottomNav } from '../components/BottomNav';

export const ReportsPage: React.FC = () => {
  const navigate = useNavigate();
  const [reports, setReports] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [activeTab, setActiveTab] = useState<'Semua' | 'Menunggu' | 'Disetujui' | 'Ditolak'>('Semua');

  const fetchReports = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/driver/manifests');
      const data = res.data?.data || res.data || [];
      setReports(data);
    } catch (err) {
      console.error('Failed to fetch manifests', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchReports();
  }, [fetchReports]);

  const filteredReports = reports.filter((r) => {
    const matchesSearch = searchQuery === '' || 
      (r.manifest_number && r.manifest_number.toLowerCase().includes(searchQuery.toLowerCase())) ||
      (r.from && r.from.toLowerCase().includes(searchQuery.toLowerCase())) ||
      (r.to && r.to.toLowerCase().includes(searchQuery.toLowerCase()));

    const statusStr = r.status || 'Menunggu';
    const matchesTab = activeTab === 'Semua' || statusStr.toLowerCase() === activeTab.toLowerCase();

    return matchesSearch && matchesTab;
  });

  return (
    <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-24 overflow-x-hidden">
      {/* Header Top Bar */}
      <header className="bg-[#0756C6] text-white px-5 py-4 sticky top-0 z-30 shadow-sm flex items-center justify-between">
        <h1 className="font-extrabold text-base tracking-wide">Laporan Manifest</h1>
        <span className="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/20 border border-white/20">
          {reports.length} Total
        </span>
      </header>

      {/* Main Container */}
      <main className="p-4 space-y-3.5 flex-1">
        {/* Hero Card 1:1 */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center justify-between gap-3">
          <div className="flex-1">
            <h3 className="text-xs font-bold text-slate-800 leading-tight">Ringkasan Laporan Driver</h3>
            <p className="text-[11px] text-slate-500 mt-1 leading-relaxed">
              Kelola laporan pengiriman & manifest operasional Anda dengan terstruktur.
            </p>
          </div>
          <div className="w-12 h-12 rounded-2xl bg-sky-50 text-[#0756C6] flex items-center justify-center shrink-0 border border-sky-100">
            <FileText className="w-6 h-6" />
          </div>
        </div>

        {/* Filter Tabs 1:1 */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
          {(['Semua', 'Menunggu', 'Disetujui', 'Ditolak'] as const).map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-3 py-1.5 rounded-xl font-bold text-xs whitespace-nowrap transition border ${
                activeTab === tab
                  ? 'bg-[#0756C6] text-white border-[#0756C6] shadow-sm'
                  : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
              }`}
            >
              {tab}
            </button>
          ))}
        </div>

        {/* Search Bar */}
        <div className="relative">
          <Search className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            placeholder="Cari nomor manifest, DO, atau rute..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0756C6]"
          />
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

        {/* Reports List */}
        {loading ? (
          <div className="py-16 text-center text-slate-400">
            <Loader2 className="w-7 h-7 animate-spin mx-auto text-[#0756C6]" />
            <p className="text-xs mt-2 font-medium">Memuat data laporan...</p>
          </div>
        ) : filteredReports.length > 0 ? (
          <div className="space-y-3">
            {filteredReports.map((report) => (
              <div
                key={report.id || report.real_id}
                onClick={() => navigate(`/reports/${report.id || report.real_id}`)}
                className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 hover:border-[#0756C6] active:scale-[0.99] transition cursor-pointer space-y-3"
              >
                <div className="flex items-center justify-between border-b border-slate-100 pb-2.5">
                  <div className="flex items-center gap-2">
                    <div className="w-7 h-7 rounded-lg bg-sky-50 text-[#0756C6] flex items-center justify-center font-bold">
                      <Truck className="w-4 h-4" />
                    </div>
                    <span className="font-mono font-bold text-xs text-slate-800">
                      {report.manifest_number || `DO-${report.id}`}
                    </span>
                  </div>

                  <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold border ${
                    report.status === 'Disetujui' || report.status === 'completed'
                      ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                      : report.status === 'Ditolak'
                      ? 'bg-rose-50 text-rose-700 border-rose-200'
                      : 'bg-amber-50 text-amber-700 border-amber-200'
                  }`}>
                    {report.status || 'Menunggu'}
                  </span>
                </div>

                {/* Route */}
                <div className="flex items-center justify-between text-xs text-slate-700">
                  <p className="font-bold flex-1 line-clamp-1">{report.from || 'Gudang Pusat'}</p>
                  <ArrowRight className="w-3.5 h-3.5 text-slate-400 mx-2 shrink-0" />
                  <p className="font-bold flex-1 text-right line-clamp-1">{report.to || 'Customer / Toko'}</p>
                </div>

                {/* Meta Footer */}
                <div className="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                  <div className="flex items-center gap-1.5">
                    <Calendar className="w-3.5 h-3.5 text-slate-400" />
                    <span>{report.date || report.dispatch_date || 'Hari ini'}</span>
                  </div>

                  <div className="flex items-center gap-1 text-[#0756C6] font-bold">
                    <span>Lihat Manifest</span>
                    <ChevronRight className="w-3.5 h-3.5" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-6">
            <Package className="w-10 h-10 mx-auto text-slate-300" />
            <h4 className="text-sm font-bold text-slate-700 mt-2">Tidak Ada Laporan</h4>
            <p className="text-xs text-slate-400 mt-1">Belum ada dokumen manifest dengan filter ini.</p>
          </div>
        )}
      </main>

      <BottomNav />
    </div>
  );
};

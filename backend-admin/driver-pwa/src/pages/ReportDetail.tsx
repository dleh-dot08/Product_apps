import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { 
  ArrowLeft, 
  FileText, 
  Calendar, 
  MapPin, 
  Truck, 
  Clock, 
  Plus, 
  X, 
  Loader2, 
  Camera, 
  AlertCircle 
} from 'lucide-react';
import api from '../services/api';

export const ReportDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const [manifest, setManifest] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isExpenseModalOpen, setIsExpenseModalOpen] = useState(false);
  const [submittingExpense, setSubmittingExpense] = useState(false);

  // Expense form
  const [expenseData, setExpenseData] = useState({
    category: 'BBM',
    amount: '',
    notes: '',
    receipt: null as File | null,
  });

  const fetchManifest = async () => {
    setLoading(true);
    try {
      const res = await api.get(`/driver/manifests/${id}`);
      if (res.data?.data) {
        setManifest(res.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch manifest', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchManifest();
  }, [id]);

  const handleAddExpense = async () => {
    if (!expenseData.amount) {
      alert('Harap masukkan nominal biaya');
      return;
    }

    setSubmittingExpense(true);
    try {
      const formData = new FormData();
      formData.append('category', expenseData.category);
      formData.append('amount', expenseData.amount);
      if (expenseData.notes) formData.append('notes', expenseData.notes);
      if (expenseData.receipt) formData.append('receipt', expenseData.receipt);

      await api.post(`/manifests/${id}/expenses`, formData);
      setIsExpenseModalOpen(false);
      setExpenseData({ category: 'BBM', amount: '', notes: '', receipt: null });
      fetchManifest();
      alert('Pengeluaran berhasil ditambahkan ke laporan!');
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal menambahkan pengeluaran');
    } finally {
      setSubmittingExpense(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-center p-4">
        <Loader2 className="w-8 h-8 animate-spin text-[#0756C6]" />
        <p className="text-xs text-slate-500 mt-2 font-medium">Memuat detail laporan...</p>
      </div>
    );
  }

  if (!manifest) {
    return (
      <div className="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-center p-4">
        <AlertCircle className="w-10 h-10 text-rose-500" />
        <p className="text-sm font-bold text-slate-800 mt-2">Laporan Tidak Ditemukan</p>
        <button
          onClick={() => navigate('/reports')}
          className="mt-4 px-4 py-2 bg-[#0756C6] text-white text-xs font-bold rounded-xl"
        >
          Kembali ke Daftar Laporan
        </button>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-16">
      {/* Header */}
      <header className="bg-[#0756C6] text-white px-4 py-3.5 sticky top-0 z-30 shadow-sm flex items-center justify-between">
        <div className="flex items-center gap-2">
          <button onClick={() => navigate('/reports')} className="p-1 text-white hover:bg-white/10 rounded-lg">
            <ArrowLeft className="w-5 h-5" />
          </button>
          <div>
            <h1 className="font-mono font-bold text-sm leading-tight">
              {manifest.manifest_number || `DO-${manifest.id}`}
            </h1>
            <p className="text-[10px] text-sky-200">Detail Dokumen Manifest</p>
          </div>
        </div>
        <span className="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 border border-white/20">
          {manifest.status || 'Aktif'}
        </span>
      </header>

      {/* Main Content */}
      <main className="p-4 space-y-4">
        {/* Info Card */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3 text-xs">
          <h4 className="font-bold text-slate-700 uppercase tracking-wide border-b border-slate-100 pb-2">
            Informasi Perjalanan
          </h4>
          <div className="grid grid-cols-2 gap-3 text-[11px]">
            <div>
              <span className="text-slate-400 block font-medium">Tanggal Dispatch</span>
              <p className="font-bold text-slate-800 mt-0.5">{manifest.dispatch_date || manifest.work_date || '-'}</p>
            </div>
            <div>
              <span className="text-slate-400 block font-medium">Kendaraan</span>
              <p className="font-bold text-slate-800 mt-0.5">{manifest.vehicle_plate_number || '-'}</p>
            </div>
            <div className="col-span-2">
              <span className="text-slate-400 block font-medium">Rute Operasional</span>
              <p className="font-bold text-slate-800 mt-0.5">{manifest.from || 'Gudang'} &rarr; {manifest.to || 'Tujuan'}</p>
            </div>
          </div>
        </div>

        {/* List Pengeluaran Manifest */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
          <div className="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wide">Daftar Pengeluaran</h4>
            <button
              onClick={() => setIsExpenseModalOpen(true)}
              className="px-2.5 py-1 bg-sky-50 text-[#0756C6] font-bold text-[11px] rounded-lg border border-sky-100 flex items-center gap-1"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>Tambah Biaya</span>
            </button>
          </div>

          {manifest.expenses && manifest.expenses.length > 0 ? (
            <div className="divide-y divide-slate-100 text-xs">
              {manifest.expenses.map((exp: any, i: number) => (
                <div key={i} className="py-2.5 flex items-center justify-between">
                  <div>
                    <p className="font-bold text-slate-800">{exp.category}</p>
                    <p className="text-[10px] text-slate-400">{exp.notes || 'Biaya operasional perjalanan'}</p>
                  </div>
                  <span className="font-bold text-slate-700">Rp {Number(exp.amount).toLocaleString('id-ID')}</span>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-[11px] text-slate-400 py-2">Belum ada pengeluaran yang dicatat di manifest ini.</p>
          )}
        </div>
      </main>

      {/* Modal Add Expense */}
      {isExpenseModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
          <div className="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4 shadow-2xl">
            <div className="flex items-center justify-between border-b pb-2">
              <h3 className="font-bold text-sm text-slate-800">Tambah Biaya Manifest</h3>
              <button onClick={() => setIsExpenseModalOpen(false)}><X className="w-4 h-4 text-slate-400" /></button>
            </div>
            <div className="space-y-3 text-xs">
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Kategori</label>
                <select
                  value={expenseData.category}
                  onChange={e => setExpenseData(prev => ({ ...prev, category: e.target.value }))}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl"
                >
                  <option value="BBM">BBM</option>
                  <option value="Tol">Tol</option>
                  <option value="Parkir">Parkir</option>
                  <option value="Tambal Ban">Tambal Ban</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Nominal (Rp) *</label>
                <input
                  type="number"
                  placeholder="50000"
                  value={expenseData.amount}
                  onChange={e => setExpenseData(prev => ({ ...prev, amount: e.target.value }))}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold"
                />
              </div>
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Foto Bukti / Struk</label>
                <input
                  type="file"
                  accept="image/*"
                  onChange={e => e.target.files && setExpenseData(prev => ({ ...prev, receipt: e.target.files![0] }))}
                  className="text-xs"
                />
              </div>
            </div>
            <button
              disabled={submittingExpense}
              onClick={handleAddExpense}
              className="w-full py-3 bg-[#0756C6] text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2"
            >
              {submittingExpense && <Loader2 className="w-4 h-4 animate-spin" />}
              <span>Simpan Pengeluaran</span>
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

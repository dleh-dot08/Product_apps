import React, { useState, useEffect, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { 
  ArrowLeft, 
  MapPin, 
  Navigation, 
  Phone, 
  Clock, 
  CheckCircle2, 
  AlertCircle, 
  Camera, 
  Loader2, 
  FileText, 
  Plus, 
  Trash2,
  X,
  Truck,
  Package,
  Calendar
} from 'lucide-react';
import SignatureCanvas from 'react-signature-canvas';
import api from '../services/api';
import { openExternalGoogleMapsNavigation, getCurrentCoordinates } from '../services/location';

export const TaskDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const [task, setTask] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  // Modals for Actions
  const [modalType, setModalType] = useState<'keberangkatan' | 'tiba' | 'serah_terima' | 'expense' | null>(null);

  // Form states
  const [odometer, setOdometer] = useState('');
  const [receiverName, setReceiverName] = useState('');
  const [notes, setNotes] = useState('');
  const [photos, setPhotos] = useState<File[]>([]);
  const [photoPreviews, setPhotoPreviews] = useState<string[]>([]);
  
  // Expense Form
  const [expenseCategory, setExpenseCategory] = useState('BBM');
  const [expenseAmount, setExpenseAmount] = useState('');
  const [expenseNotes, setExpenseNotes] = useState('');
  const [expenseReceipt, setExpenseReceipt] = useState<File | null>(null);

  const sigCanvas = useRef<SignatureCanvas | null>(null);

  const fetchTaskDetail = async () => {
    setLoading(true);
    try {
      const res = await api.get(`/pickup/${id}`);
      if (res.data?.status === 'success' || res.data?.data) {
        setTask(res.data.data || res.data);
      }
    } catch (err) {
      console.error('Failed to fetch task detail', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTaskDetail();
  }, [id]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      const selected = Array.from(e.target.files);
      setPhotos(prev => [...prev, ...selected].slice(0, 5));
      const previews = selected.map(f => URL.createObjectURL(f));
      setPhotoPreviews(prev => [...prev, ...previews].slice(0, 5));
    }
  };

  const handleUpdateStatus = async (newStatus: string) => {
    setActionLoading(true);
    try {
      const formData = new FormData();
      formData.append('status', newStatus);

      // Ambil GPS terbaru otomatis saat milestone checkpoint
      try {
        const coords = await getCurrentCoordinates();
        formData.append('latitude', String(coords.latitude));
        formData.append('longitude', String(coords.longitude));
      } catch (e) {
        console.warn('GPS not available:', e);
      }

      if (odometer) formData.append('odometer', odometer);
      if (receiverName) formData.append('recipient_name', receiverName);
      if (notes) formData.append('notes', notes);

      // Tanda tangan jika selesai
      if (sigCanvas.current && !sigCanvas.current.isEmpty()) {
        const sigBase64 = sigCanvas.current.getTrimmedCanvas().toDataURL('image/png');
        formData.append('signature_image', sigBase64);
      }

      photos.forEach((f) => {
        formData.append('photos[]', f);
      });

      await api.post(`/pickup/${id}/status`, formData);
      setModalType(null);
      resetForms();
      fetchTaskDetail();
      alert('Status tugas berhasil diperbarui!');
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal memperbarui status');
    } finally {
      setActionLoading(false);
    }
  };

  const handleAddExpense = async () => {
    if (!expenseAmount) {
      alert('Harap isi nominal biaya');
      return;
    }
    setActionLoading(true);
    try {
      const formData = new FormData();
      formData.append('category', expenseCategory);
      formData.append('amount', expenseAmount);
      if (expenseNotes) formData.append('notes', expenseNotes);
      if (expenseReceipt) formData.append('receipt', expenseReceipt);

      await api.post(`/pickup/${id}/expenses`, formData);
      setModalType(null);
      resetForms();
      fetchTaskDetail();
      alert('Pengeluaran berhasil dicatat!');
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal mencatat pengeluaran');
    } finally {
      setActionLoading(false);
    }
  };

  const resetForms = () => {
    setOdometer('');
    setReceiverName('');
    setNotes('');
    setPhotos([]);
    setPhotoPreviews([]);
    setExpenseAmount('');
    setExpenseNotes('');
    setExpenseReceipt(null);
    if (sigCanvas.current) sigCanvas.current.clear();
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-center p-4">
        <Loader2 className="w-8 h-8 animate-spin text-[#0756C6]" />
        <p className="text-xs text-slate-500 mt-2 font-medium">Memuat detail tugas...</p>
      </div>
    );
  }

  if (!task) {
    return (
      <div className="min-h-screen bg-[#F8FAFC] flex flex-col items-center justify-center p-4">
        <AlertCircle className="w-10 h-10 text-rose-500" />
        <p className="text-sm font-bold text-slate-800 mt-2">Tugas Tidak Ditemukan</p>
        <button
          onClick={() => navigate('/tasks')}
          className="mt-4 px-4 py-2 bg-[#0756C6] text-white text-xs font-bold rounded-xl"
        >
          Kembali ke Daftar Tugas
        </button>
      </div>
    );
  }

  const currentStatus = task.status?.toLowerCase() || 'assigned';
  const lat = task.destination_latitude || task.latitude || -6.2088;
  const lng = task.destination_longitude || task.longitude || 106.8456;

  return (
    <div className="min-h-screen bg-[#F8FAFC] flex flex-col justify-between max-w-md mx-auto relative shadow-2xl pb-28">
      {/* Header Top Bar */}
      <header className="bg-[#0756C6] text-white px-4 py-3.5 sticky top-0 z-30 shadow-sm flex items-center justify-between">
        <div className="flex items-center gap-2">
          <button onClick={() => navigate('/tasks')} className="p-1 text-white hover:bg-white/10 rounded-lg">
            <ArrowLeft className="w-5 h-5" />
          </button>
          <div>
            <h1 className="font-mono font-bold text-sm leading-tight">
              {task.reference_number || `TRP-${task.id}`}
            </h1>
            <p className="text-[10px] text-sky-200">Detail Penugasan</p>
          </div>
        </div>

        <span className="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 border border-white/20">
          {task.status || 'Dijadwalkan'}
        </span>
      </header>

      {/* Main Detail Container */}
      <main className="p-4 space-y-4">
        {/* Banner Navigasi Google Maps */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
          <div className="flex items-center justify-between">
            <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wide">Rute Pengiriman</h4>
            <span className="text-[10px] font-bold text-[#0756C6] flex items-center gap-1">
              <MapPin className="w-3.5 h-3.5" /> GPS Aktif
            </span>
          </div>

          <div className="p-3 bg-slate-50 rounded-xl space-y-2 text-xs border border-slate-200/60">
            <div className="flex items-start gap-2">
              <div className="w-2.5 h-2.5 rounded-full bg-[#0756C6] mt-1 shrink-0" />
              <div>
                <span className="text-[10px] text-slate-400 font-bold block">Pickup (Asal)</span>
                <p className="font-bold text-slate-800">{task.pickup_name || 'Lokasi Asal'}</p>
                <p className="text-[11px] text-slate-500">{task.pickup_location || '-'}</p>
              </div>
            </div>

            <div className="flex items-start gap-2 pt-1 border-t border-slate-200/50">
              <div className="w-2.5 h-2.5 rounded-full bg-rose-500 mt-1 shrink-0" />
              <div>
                <span className="text-[10px] text-slate-400 font-bold block">Delivery (Tujuan)</span>
                <p className="font-bold text-slate-800">{task.destination || 'Tujuan Pengiriman'}</p>
                <p className="text-[11px] text-slate-500">{task.destination_address || task.recipient_address || '-'}</p>
              </div>
            </div>
          </div>

          <button
            onClick={() => openExternalGoogleMapsNavigation(lat, lng)}
            className="w-full py-3 bg-sky-50 text-[#0756C6] hover:bg-sky-100 font-bold text-xs rounded-xl border border-sky-200 flex items-center justify-center gap-2 active:scale-95 transition"
          >
            <Navigation className="w-4 h-4" />
            <span>Buka Navigasi Suara Google Maps</span>
          </button>
        </div>

        {/* Info Barang & Muatan */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-2.5 text-xs">
          <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wide border-b border-slate-100 pb-2">
            Informasi Muatan
          </h4>
          <div className="grid grid-cols-2 gap-2 text-[11px]">
            <div>
              <span className="text-slate-400 block font-medium">Kategori Barang</span>
              <p className="font-bold text-slate-800 mt-0.5">{task.item_category || task.category || 'Barang Logistik'}</p>
            </div>
            <div>
              <span className="text-slate-400 block font-medium">Kuantitas</span>
              <p className="font-bold text-slate-800 mt-0.5">{task.quantity || 1} {task.unit || 'Colly'}</p>
            </div>
            <div className="col-span-2">
              <span className="text-slate-400 block font-medium">Keterangan / Deskripsi</span>
              <p className="text-slate-700 mt-0.5">{task.item_description || task.notes || 'Tidak ada catatan tambahan'}</p>
            </div>
          </div>
        </div>

        {/* Info Penerima */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-2.5 text-xs">
          <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wide border-b border-slate-100 pb-2">
            Penerima Barang
          </h4>
          <div className="space-y-1.5 text-[11px]">
            <p className="font-bold text-slate-800">{task.recipient_name || task.customer_name || 'Pelanggan / Toko Penerima'}</p>
            {task.recipient_phone && (
              <a 
                href={`tel:${task.recipient_phone}`}
                className="inline-flex items-center gap-1.5 text-[#0756C6] font-bold"
              >
                <Phone className="w-3.5 h-3.5" />
                <span>{task.recipient_phone}</span>
              </a>
            )}
          </div>
        </div>

        {/* Section Pengeluaran Task */}
        <div className="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
          <div className="flex items-center justify-between border-b border-slate-100 pb-2">
            <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wide">Pengeluaran Tugas</h4>
            <button
              onClick={() => setModalType('expense')}
              className="px-2.5 py-1 bg-sky-50 text-[#0756C6] font-bold text-[11px] rounded-lg border border-sky-100 flex items-center gap-1"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>Tambah Biaya</span>
            </button>
          </div>

          {task.expenses && task.expenses.length > 0 ? (
            <div className="divide-y divide-slate-100 text-xs">
              {task.expenses.map((exp: any, i: number) => (
                <div key={i} className="py-2 flex items-center justify-between">
                  <div>
                    <p className="font-bold text-slate-800">{exp.category}</p>
                    <p className="text-[10px] text-slate-400">{exp.notes || '-'}</p>
                  </div>
                  <span className="font-bold text-slate-700">Rp {Number(exp.amount).toLocaleString('id-ID')}</span>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-[11px] text-slate-400 py-1">Belum ada pengeluaran yang dicatat untuk tugas ini.</p>
          )}
        </div>
      </main>

      {/* Sticky Bottom Action Bar sesuai status 1:1 */}
      <div className="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-slate-200 px-4 py-3 z-30 shadow-lg">
        {currentStatus === 'assigned' && (
          <button
            onClick={() => setModalType('keberangkatan')}
            className="w-full py-3.5 bg-[#0756C6] hover:bg-[#063B8C] text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
          >
            <Truck className="w-4 h-4" />
            <span>Mulai Perjalanan (Berangkat)</span>
          </button>
        )}

        {currentStatus === 'on_route' && (
          <button
            onClick={() => setModalType('tiba')}
            className="w-full py-3.5 bg-[#0F9FA8] hover:bg-[#0d8b93] text-white font-bold text-xs rounded-xl shadow-md shadow-teal-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
          >
            <MapPin className="w-4 h-4" />
            <span>Konfirmasi Tiba di Lokasi</span>
          </button>
        )}

        {currentStatus === 'arrived' && (
          <button
            onClick={() => setModalType('serah_terima')}
            className="w-full py-3.5 bg-[#16A36A] hover:bg-[#138a5a] text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
          >
            <CheckCircle2 className="w-4 h-4" />
            <span>Selesaikan Pengiriman & Serah Terima</span>
          </button>
        )}

        {currentStatus === 'delivered' && (
          <div className="py-2 text-center text-emerald-700 font-bold text-xs flex items-center justify-center gap-1.5 bg-emerald-50 rounded-xl border border-emerald-200">
            <CheckCircle2 className="w-4 h-4" />
            <span>Pengiriman Telah Selesai</span>
          </div>
        )}
      </div>

      {/* Modal Keberangkatan */}
      {modalType === 'keberangkatan' && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
          <div className="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4 shadow-2xl">
            <div className="flex items-center justify-between border-b pb-2">
              <h3 className="font-bold text-sm text-slate-800">Mulai Perjalanan</h3>
              <button onClick={() => setModalType(null)}><X className="w-4 h-4 text-slate-400" /></button>
            </div>
            <div className="space-y-3 text-xs">
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Odometer Awal KM</label>
                <input
                  type="number"
                  placeholder="Contoh: 15400"
                  value={odometer}
                  onChange={e => setOdometer(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl"
                />
              </div>
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Foto Odometer / Kendaraan</label>
                <input type="file" accept="image/*" capture="environment" onChange={handleFileChange} className="text-xs" />
              </div>
            </div>
            <button
              disabled={actionLoading}
              onClick={() => handleUpdateStatus('on_route')}
              className="w-full py-3 bg-[#0756C6] text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2"
            >
              {actionLoading && <Loader2 className="w-4 h-4 animate-spin" />}
              <span>Mulai Jalan</span>
            </button>
          </div>
        </div>
      )}

      {/* Modal Tiba di Lokasi */}
      {modalType === 'tiba' && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
          <div className="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4 shadow-2xl">
            <div className="flex items-center justify-between border-b pb-2">
              <h3 className="font-bold text-sm text-slate-800">Konfirmasi Tiba di Lokasi</h3>
              <button onClick={() => setModalType(null)}><X className="w-4 h-4 text-slate-400" /></button>
            </div>
            <p className="text-xs text-slate-600">Ambil foto bukti kedatangan di lokasi tujuan:</p>
            <input type="file" accept="image/*" capture="environment" onChange={handleFileChange} className="text-xs" />
            <button
              disabled={actionLoading}
              onClick={() => handleUpdateStatus('arrived')}
              className="w-full py-3 bg-[#0F9FA8] text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2"
            >
              {actionLoading && <Loader2 className="w-4 h-4 animate-spin" />}
              <span>Konfirmasi Tiba</span>
            </button>
          </div>
        </div>
      )}

      {/* Modal Serah Terima + Signature Canvas */}
      {modalType === 'serah_terima' && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
          <div className="bg-white rounded-2xl w-full max-w-sm max-h-[90vh] overflow-y-auto p-5 space-y-4 shadow-2xl">
            <div className="flex items-center justify-between border-b pb-2">
              <h3 className="font-bold text-sm text-slate-800">Bukti Serah Terima</h3>
              <button onClick={() => setModalType(null)}><X className="w-4 h-4 text-slate-400" /></button>
            </div>
            <div className="space-y-3 text-xs">
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Nama Penerima Barang *</label>
                <input
                  type="text"
                  placeholder="Nama jelas penerima"
                  value={receiverName}
                  onChange={e => setReceiverName(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl"
                  required
                />
              </div>

              <div>
                <label className="font-bold text-slate-600 mb-1 block">Foto Bukti Barang & Penerima</label>
                <input type="file" accept="image/*" capture="environment" onChange={handleFileChange} className="text-xs" />
              </div>

              <div>
                <div className="flex items-center justify-between mb-1">
                  <label className="font-bold text-slate-600">Tanda Tangan Digital Penerima *</label>
                  <button
                    type="button"
                    onClick={() => sigCanvas.current?.clear()}
                    className="text-[10px] text-rose-600 font-bold underline"
                  >
                    Hapus
                  </button>
                </div>
                <div className="border border-slate-300 rounded-xl overflow-hidden bg-slate-50">
                  <SignatureCanvas
                    ref={sigCanvas}
                    canvasProps={{ className: 'w-full h-36 bg-slate-50' }}
                  />
                </div>
              </div>
            </div>

            <button
              disabled={actionLoading}
              onClick={() => handleUpdateStatus('delivered')}
              className="w-full py-3 bg-[#16A36A] text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2"
            >
              {actionLoading && <Loader2 className="w-4 h-4 animate-spin" />}
              <span>Selesaikan & Kirim Bukti</span>
            </button>
          </div>
        </div>
      )}

      {/* Modal Tambah Pengeluaran Task */}
      {modalType === 'expense' && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
          <div className="bg-white rounded-2xl w-full max-w-sm p-5 space-y-4 shadow-2xl">
            <div className="flex items-center justify-between border-b pb-2">
              <h3 className="font-bold text-sm text-slate-800">Biaya Tambahan Tugas</h3>
              <button onClick={() => setModalType(null)}><X className="w-4 h-4 text-slate-400" /></button>
            </div>
            <div className="space-y-3 text-xs">
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Kategori Biaya</label>
                <select
                  value={expenseCategory}
                  onChange={e => setExpenseCategory(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl"
                >
                  <option value="BBM">BBM</option>
                  <option value="Tol">Tol</option>
                  <option value="Parkir">Parkir</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Nominal (Rp) *</label>
                <input
                  type="number"
                  placeholder="50000"
                  value={expenseAmount}
                  onChange={e => setExpenseAmount(e.target.value)}
                  className="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold"
                />
              </div>
              <div>
                <label className="font-bold text-slate-600 mb-1 block">Foto Struk / Bukti</label>
                <input
                  type="file"
                  accept="image/*"
                  onChange={e => e.target.files && setExpenseReceipt(e.target.files[0])}
                  className="text-xs"
                />
              </div>
            </div>
            <button
              disabled={actionLoading}
              onClick={handleAddExpense}
              className="w-full py-3 bg-[#0756C6] text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2"
            >
              {actionLoading && <Loader2 className="w-4 h-4 animate-spin" />}
              <span>Simpan Biaya</span>
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

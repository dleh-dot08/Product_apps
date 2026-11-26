import React, { useState, useEffect, useCallback } from 'react';
import { ArrowLeft, X, Calendar, Loader2, Camera } from 'lucide-react';
import api from '../../services/api';

const BRAND = {
  primary: '#0756C6',
  primarySoft: '#EAF3FF',
  white: '#FFFFFF',
  text: '#0f172a',
  muted: '#64748b',
  border: '#e2e8f0',
  success: '#16a34a',
  warning: '#f59e0b',
  card: '#f8fafc',
};

interface Shift {
  id: number | string;
  work_date: string;
  vehicle_name?: string;
  vehicle_plate_number?: string;
  check_in_at?: string;
  check_out_at?: string;
  status: string;
  manifests?: string;
  manifest_list?: { id: string; manifest_number: string }[];
}

interface Props {
  visible: boolean;
  onClose: () => void;
  onSuccess?: () => void;
}

type Step = 'select_shift' | 'form';

export const ExpenseModal: React.FC<Props> = ({ visible, onClose, onSuccess }) => {
  const [step, setStep] = useState<Step>('select_shift');
  const [shifts, setShifts] = useState<Shift[]>([]);
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [selectedShift, setSelectedShift] = useState<Shift | null>(null);
  const [selectedManifest, setSelectedManifest] = useState<string | null>(null);

  // Form fields
  const [category, setCategory] = useState('BBM');
  const [customCategory, setCustomCategory] = useState('');
  const [amount, setAmount] = useState('');
  const [notes, setNotes] = useState('');
  const [receiptFile, setReceiptFile] = useState<File | null>(null);
  const [receiptPreview, setReceiptPreview] = useState<string | null>(null);

  const fetchShifts = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/driver/recent-shifts');
      setShifts(res.data?.data || []);
    } catch (err) {
      console.error('Fetch shifts error', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (visible) {
      setSelectedShift(null);
      setSelectedManifest(null);
      setCategory('BBM');
      setCustomCategory('');
      setAmount('');
      setNotes('');
      setReceiptFile(null);
      setReceiptPreview(null);
      setStep('select_shift');
      fetchShifts();
    }
  }, [visible, fetchShifts]);

  if (!visible) return null;

  const formatDate = (dateString?: string) => {
    if (!dateString) return '-';
    try {
      const d = new Date(dateString);
      if (isNaN(d.getTime())) return dateString;
      return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
      return dateString;
    }
  };

  const formatTime = (dateString?: string) => {
    if (!dateString) return '-';
    try {
      const d = new Date(dateString);
      if (isNaN(d.getTime())) return '-';
      return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    } catch {
      return '-';
    }
  };

  const handleSelectShift = (shift: Shift) => {
    setSelectedShift(shift);
    setStep('form');
  };

  const handleReceiptChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0];
      setReceiptFile(file);
      setReceiptPreview(URL.createObjectURL(file));
    }
  };

  const handleSubmit = async () => {
    if (!selectedShift) return;
    if (!amount || Number(amount) <= 0) {
      alert('Nominal pengeluaran harus diisi dengan benar');
      return;
    }

    setSubmitting(true);
    try {
      const formData = new FormData();
      formData.append('category', category === 'Lainnya' ? customCategory || 'other' : category.toLowerCase());
      formData.append('amount', amount);
      formData.append('notes', notes);
      formData.append('description', category === 'Lainnya' ? customCategory : category);

      if (selectedManifest) {
        formData.append('task_manifest_id', selectedManifest);
      }

      if (receiptFile) {
        formData.append('receipt', receiptFile);
      }

      await api.post(`/driver/shifts/${selectedShift.id}/expenses`, formData);
      alert('Pengeluaran berhasil disimpan.');
      onSuccess?.();
      onClose();
    } catch (err: any) {
      console.error('Submit expense error', err?.response?.data || err);
      alert(err.response?.data?.message || 'Tidak bisa menyimpan pengeluaran.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-end justify-center bg-black/50">
      <div className="bg-white rounded-t-[20px] w-full max-w-md max-h-[90vh] overflow-y-auto flex flex-col shadow-2xl animate-in slide-in-from-bottom">
        {/* Header 1:1 */}
        <div className="p-4 border-b border-[#e2e8f0] flex items-center justify-between sticky top-0 bg-white z-10">
          <button
            onClick={step === 'form' ? () => setStep('select_shift') : onClose}
            className="p-1 text-[#0f172a] hover:bg-slate-100 rounded-lg"
          >
            {step === 'form' ? <ArrowLeft className="w-6 h-6" /> : <X className="w-6 h-6" />}
          </button>
          <h3 className="text-base font-bold text-[#0f172a] font-['Inter',sans-serif]">
            {step === 'select_shift' ? 'Pilih Shift / Hari' : 'Pengeluaran Shift'}
          </h3>
          <div className="w-6" />
        </div>

        {step === 'select_shift' ? (
          /* STEP 1: Shift Picker 1:1 */
          <div className="p-4 space-y-3">
            <p className="text-xs text-[#64748b] leading-relaxed">
              Silakan pilih shift dalam kurun waktu 3 hari terakhir (H-3) untuk melampirkan pengeluaran tambahan.
            </p>

            {loading ? (
              <div className="py-12 text-center">
                <Loader2 className="w-8 h-8 animate-spin mx-auto text-[#0756C6]" />
                <p className="text-xs text-[#64748b] mt-2">Memuat riwayat shift...</p>
              </div>
            ) : shifts.length === 0 ? (
              <div className="py-12 text-center border border-dashed border-[#e2e8f0] rounded-xl my-4">
                <Calendar className="w-12 h-12 text-[#e2e8f0] mx-auto mb-2" />
                <p className="text-xs text-[#64748b]">Tidak ada riwayat shift di 3 hari terakhir.</p>
              </div>
            ) : (
              shifts.map((shift) => (
                <div
                  key={shift.id}
                  onClick={() => handleSelectShift(shift)}
                  className="bg-[#f8fafc] border border-[#e2e8f0] hover:border-[#0756C6] rounded-xl p-3.5 cursor-pointer transition space-y-1.5"
                >
                  <div className="flex items-center justify-between">
                    <span className="font-bold text-xs text-[#0f172a]">
                      Shift: {formatDate(shift.work_date)}
                    </span>
                    <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                      shift.status === 'active' ? 'bg-[#EAF3FF] text-[#0756C6]' : 'bg-emerald-50 text-emerald-700'
                    }`}>
                      {shift.status === 'active' ? 'Sedang Aktif' : 'Selesai'}
                    </span>
                  </div>

                  <p className="text-[11px] text-[#64748b]">
                    Kendaraan: {shift.vehicle_plate_number || '-'} {shift.vehicle_name ? `(${shift.vehicle_name})` : ''}
                  </p>

                  {shift.manifests && shift.manifests !== 'Tidak ada Delivery Order' && (
                    <p className="text-[11px] text-[#0756C6] font-medium">
                      Delivery Order: {shift.manifests}
                    </p>
                  )}

                  <div className="flex gap-4 text-[11px] text-[#0f172a] font-semibold pt-1 border-t border-[#e2e8f0]/60">
                    <span>Clock In: {formatTime(shift.check_in_at)}</span>
                    <span>Clock Out: {formatTime(shift.check_out_at)}</span>
                  </div>
                </div>
              ))
            )}
          </div>
        ) : (
          /* STEP 2: Expense Form 1:1 */
          <div className="p-4 space-y-4 text-xs">
            <div className="flex items-center gap-2 text-[#0756C6] bg-[#EAF3FF] p-2.5 rounded-lg font-medium text-xs">
              <Calendar className="w-4 h-4 shrink-0" />
              <span>Pengeluaran untuk Shift {selectedShift ? formatDate(selectedShift.work_date) : ''}</span>
            </div>

            {/* Total Box 1:1 */}
            <div className="bg-[#f8fafc] border border-[#e2e8f0] rounded-xl p-4 text-center">
              <span className="text-[12px] text-[#64748b] block font-medium">Total Pengeluaran (Nominal)</span>
              <span className="text-[26px] font-bold text-[#0756C6] block mt-1 font-['Inter',sans-serif]">
                Rp {parseInt(amount || '0').toLocaleString('id-ID')}
              </span>
            </div>

            <div>
              <label className="font-bold text-[#0f172a] mb-2 block">Jenis Pengeluaran</label>
              <div className="grid grid-cols-4 gap-2">
                {['BBM', 'Tol', 'Parkir', 'Lainnya'].map((cat) => (
                  <button
                    key={cat}
                    type="button"
                    onClick={() => setCategory(cat)}
                    className={`py-2 rounded-lg font-bold text-center border transition ${
                      category === cat
                        ? 'bg-[#EAF3FF] text-[#0756C6] border-[#0756C6]'
                        : 'bg-white text-[#64748b] border-[#e2e8f0]'
                    }`}
                  >
                    {cat}
                  </button>
                ))}
              </div>
            </div>

            {category === 'Lainnya' && (
              <div>
                <input
                  type="text"
                  placeholder="Nama kategori..."
                  value={customCategory}
                  onChange={e => setCustomCategory(e.target.value)}
                  className="w-full border border-[#e2e8f0] rounded-lg p-3 text-sm focus:outline-none focus:border-[#0756C6]"
                />
              </div>
            )}

            <div>
              <label className="font-bold text-[#0f172a] mb-2 block">Nominal (Rp)</label>
              <input
                type="number"
                placeholder="50000"
                value={amount}
                onChange={e => setAmount(e.target.value)}
                className="w-full border border-[#e2e8f0] rounded-lg p-3 text-sm font-semibold focus:outline-none focus:border-[#0756C6]"
              />
            </div>

            <div>
              <label className="font-bold text-[#0f172a] mb-2 block">Catatan (Opsional)</label>
              <textarea
                rows={2}
                placeholder="Contoh: Isi solar di SPBU Tol..."
                value={notes}
                onChange={e => setNotes(e.target.value)}
                className="w-full border border-[#e2e8f0] rounded-lg p-3 text-sm focus:outline-none focus:border-[#0756C6]"
              />
            </div>

            <div>
              <label className="font-bold text-[#0f172a] mb-2 block">Foto Struk / Bukti Transaksi</label>
              {receiptPreview ? (
                <div className="relative w-24 h-24 rounded-lg overflow-hidden border border-[#e2e8f0]">
                  <img src={receiptPreview} alt="Struk" className="w-full h-full object-cover" />
                  <button
                    onClick={() => {
                      setReceiptFile(null);
                      setReceiptPreview(null);
                    }}
                    className="absolute top-1 right-1 bg-white rounded-full p-0.5 text-[#ef4444] shadow"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>
              ) : (
                <label className="flex items-center justify-center p-3.5 border border-dashed border-[#0756C6] rounded-lg bg-[#EAF3FF] gap-2 cursor-pointer text-[#0756C6]">
                  <Camera className="w-5 h-5" />
                  <span className="text-sm font-medium">Foto Struk / Kwitansi</span>
                  <input
                    type="file"
                    accept="image/*"
                    capture="environment"
                    onChange={handleReceiptChange}
                    className="hidden"
                  />
                </label>
              )}
            </div>

            <div className="pt-2">
              <button
                type="button"
                disabled={submitting}
                onClick={handleSubmit}
                className="w-full py-3.5 bg-[#0756C6] text-white font-bold rounded-lg text-sm hover:bg-[#063B8C] transition flex items-center justify-center gap-2 disabled:opacity-50"
              >
                {submitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <span>Simpan Pengeluaran</span>}
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

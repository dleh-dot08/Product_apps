import React, { useState, useEffect } from 'react';
import { Camera, Plus, X, Loader2 } from 'lucide-react';
import api from '../../services/api';

const BRAND = {
  primary: '#0756C6',
  primarySoft: '#EAF3FF',
  white: '#FFFFFF',
  text: '#0f172a',
  border: '#e2e8f0',
  error: '#ef4444',
  danger: '#ef4444',
  muted: '#64748b',
};

interface Props {
  visible: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export const ClockOutModal: React.FC<Props> = ({ visible, onClose, onSuccess }) => {
  const [odometer, setOdometer] = useState('');
  const [photos, setPhotos] = useState<File[]>([]);
  const [photoPreviews, setPhotoPreviews] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!visible) {
      setOdometer('');
      setPhotos([]);
      setPhotoPreviews([]);
      setError('');
    }
  }, [visible]);

  if (!visible) return null;

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      const selected = Array.from(e.target.files);
      if (photos.length + selected.length > 10) {
        alert('Anda hanya bisa mengunggah maksimal 10 foto.');
        return;
      }
      setPhotos(prev => [...prev, ...selected].slice(0, 10));
      const previews = selected.map(f => URL.createObjectURL(f));
      setPhotoPreviews(prev => [...prev, ...previews].slice(0, 10));
    }
  };

  const removePhoto = (index: number) => {
    setPhotos(prev => prev.filter((_, i) => i !== index));
    setPhotoPreviews(prev => prev.filter((_, i) => i !== index));
  };

  const handleClockOut = async () => {
    if (!odometer) {
      setError('Odometer akhir wajib diisi');
      return;
    }
    setLoading(true);
    setError('');

    try {
      const formData = new FormData();
      formData.append('end_odometer', odometer);

      photos.forEach((file) => {
        formData.append('photos[]', file);
      });

      await api.post('/driver/shift/end', formData);
      onSuccess();
      onClose();
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal mengakhiri shift.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-5">
      <div className="bg-white rounded-[12px] w-full max-w-[420px] max-h-[85vh] overflow-y-auto p-5 shadow-xl animate-in fade-in">
        <h3 className="text-[18px] font-bold text-[#0f172a] mb-1 font-['Inter',sans-serif]">
          Akhiri Shift (Clock Out)
        </h3>
        <p className="text-[13px] text-[#64748b] mb-4 font-['Inter',sans-serif] leading-relaxed">
          Pastikan semua tugas hari ini sudah selesai. Masukkan odometer terakhir sebelum Anda menyerahkan kendaraan.
        </p>

        <div className="space-y-4">
          <div>
            <label className="text-[14px] font-medium text-[#0f172a] mb-2 block font-['Inter',sans-serif]">
              Odometer Akhir (KM) <span className="text-[#ef4444]">*</span>
            </label>
            <input
              type="number"
              placeholder="Contoh: 12550"
              value={odometer}
              onChange={e => setOdometer(e.target.value)}
              className="w-full border border-[#e2e8f0] rounded-lg p-3 text-sm focus:outline-none focus:border-[#0756C6]"
            />
          </div>

          <div>
            <label className="text-[14px] font-medium text-[#0f172a] mb-2 block font-['Inter',sans-serif]">
              Foto Kondisi Akhir (Maks 10, Opsional)
            </label>

            {photos.length > 0 ? (
              <div className="flex gap-3 overflow-x-auto pb-1">
                {photoPreviews.map((uri, idx) => (
                  <div key={idx} className="relative w-20 h-24 rounded-lg overflow-hidden shrink-0 border border-[#e2e8f0]">
                    <img src={uri} alt="Upload" className="w-full h-full object-cover rounded-lg" />
                    <button
                      type="button"
                      onClick={() => removePhoto(idx)}
                      className="absolute top-1 right-1 bg-white rounded-full p-0.5 shadow-sm text-[#ef4444]"
                    >
                      <X className="w-4 h-4" />
                    </button>
                  </div>
                ))}
                {photos.length < 10 && (
                  <label className="w-20 h-24 rounded-lg border border-dashed border-[#0756C6] bg-[#EAF3FF] flex items-center justify-center text-[#0756C6] shrink-0 cursor-pointer">
                    <Plus className="w-7 h-7" />
                    <input
                      type="file"
                      accept="image/*"
                      capture="environment"
                      multiple
                      onChange={handleFileChange}
                      className="hidden"
                    />
                  </label>
                )}
              </div>
            ) : (
              <label className="flex items-center justify-center p-3.5 border border-dashed border-[#0756C6] rounded-lg bg-[#EAF3FF] gap-2 cursor-pointer text-[#0756C6]">
                <Camera className="w-5 h-5" />
                <span className="text-sm font-medium">Ambil / Pilih Foto</span>
                <input
                  type="file"
                  accept="image/*"
                  capture="environment"
                  multiple
                  onChange={handleFileChange}
                  className="hidden"
                />
              </label>
            )}
          </div>

          {error && (
            <p className="text-[#ef4444] text-[12px] font-medium mt-1 mb-2">
              {error}
            </p>
          )}
        </div>

        {/* Footer 1:1 */}
        <div className="flex gap-3 mt-6">
          <button
            type="button"
            onClick={onClose}
            disabled={loading}
            className="flex-1 py-3.5 rounded-lg border border-[#e2e8f0] text-sm font-medium text-[#0f172a] hover:bg-slate-50 transition"
          >
            Batal
          </button>
          <button
            type="button"
            onClick={handleClockOut}
            disabled={loading}
            className="flex-1 py-3.5 rounded-lg bg-[#ef4444] text-white text-sm font-medium hover:bg-red-600 transition flex items-center justify-center gap-1.5 disabled:opacity-50"
          >
            {loading ? <Loader2 className="w-4 h-4 animate-spin" /> : <span>Clock Out</span>}
          </button>
        </div>
      </div>
    </div>
  );
};

import React from 'react';
import { X, Bell, CheckCircle2, AlertTriangle, Info } from 'lucide-react';

interface Props {
  visible: boolean;
  onClose: () => void;
  tasks?: any[];
}

export const NotificationModal: React.FC<Props> = ({ visible, onClose, tasks = [] }) => {
  if (!visible) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
      <div className="bg-white rounded-2xl w-full max-w-sm max-h-[85vh] overflow-y-auto p-5 shadow-2xl animate-in fade-in zoom-in-95">
        <div className="flex items-center justify-between border-b border-slate-100 pb-3">
          <div className="flex items-center gap-2">
            <Bell className="w-5 h-5 text-[#0756C6]" />
            <h3 className="font-extrabold text-slate-800 text-base">Notifikasi & Informasi</h3>
          </div>
          <button onClick={onClose} className="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="mt-4 space-y-3">
          {tasks.length > 0 ? (
            tasks.map((task, idx) => (
              <div key={idx} className="p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-xs space-y-1">
                <div className="flex items-center justify-between">
                  <span className="font-mono font-bold text-[#0756C6]">
                    {task.reference_number || `TSK-${task.id}`}
                  </span>
                  <span className="text-[10px] text-slate-400">{task.assigned_at || 'Hari ini'}</span>
                </div>
                <p className="font-semibold text-slate-700">
                  {task.pickup_name || task.destination || 'Tugas Baru Ditugaskan'}
                </p>
                <p className="text-[11px] text-slate-500">
                  Status saat ini: <strong className="text-slate-700">{task.status || 'Dijadwalkan'}</strong>
                </p>
              </div>
            ))
          ) : (
            <div className="py-8 text-center text-slate-400">
              <Info className="w-8 h-8 mx-auto text-slate-300" />
              <p className="text-xs mt-2 font-medium">Tidak ada notifikasi baru saat ini.</p>
            </div>
          )}
        </div>

        <div className="mt-5 pt-3 border-t border-slate-100 text-center">
          <button
            onClick={onClose}
            className="w-full py-2.5 bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 rounded-xl text-xs"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  );
};

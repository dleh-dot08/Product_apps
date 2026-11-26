import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { Home, ClipboardList, FileText, User } from 'lucide-react';

interface TabConfig {
  key: string;
  label: string;
  path: string;
  icon: React.ElementType;
}

const TABS: TabConfig[] = [
  { key: 'beranda', label: 'Beranda', path: '/', icon: Home },
  { key: 'tugas', label: 'Tugas', path: '/tasks', icon: ClipboardList },
  { key: 'laporan', label: 'Laporan', path: '/reports', icon: FileText },
  { key: 'profil', label: 'Profil', path: '/profile', icon: User },
];

export const BottomNav: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();

  const isActive = (path: string) => {
    if (path === '/') {
      return location.pathname === '/';
    }
    return location.pathname.startsWith(path);
  };

  return (
    <nav className="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-slate-200/90 px-6 py-2 flex items-center justify-between z-30 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
      {TABS.map((tab) => {
        const active = isActive(tab.path);
        const Icon = tab.icon;

        return (
          <button
            key={tab.key}
            onClick={() => navigate(tab.path)}
            className={`flex flex-col items-center justify-center py-1 px-2 min-w-[56px] transition-colors ${
              active ? 'text-[#0756C6]' : 'text-[#7C8DA5] hover:text-slate-600'
            }`}
          >
            <div className="relative">
              <Icon className="w-5 h-5 stroke-[2]" />
              {active && (
                <span className="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#0756C6]" />
              )}
            </div>
            <span className={`text-[11px] mt-1 font-semibold ${active ? 'text-[#0756C6]' : 'text-[#7C8DA5]'}`}>
              {tab.label}
            </span>
          </button>
        );
      })}
    </nav>
  );
};

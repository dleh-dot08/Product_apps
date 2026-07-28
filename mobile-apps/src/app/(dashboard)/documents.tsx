import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

type DocCategory = 'Tagihan' | 'Laporan' | 'Desain';
type FilterType = 'Semua' | DocCategory;

interface DocumentItem {
  id: string;
  title: string;
  date: string;
  size: string;
  category: DocCategory;
}

const mockDocuments: DocumentItem[] = [
  { id: '1', title: 'Invoice Klien A - INV001.pdf', date: '25 Jul 2026', size: '2.4 MB', category: 'Tagihan' },
  { id: '2', title: 'Laporan Penjualan Q2.xlsx', date: '20 Jul 2026', size: '1.1 MB', category: 'Laporan' },
  { id: '3', title: 'Desain Kemasan Kayu V2.ai', date: '15 Jul 2026', size: '15.8 MB', category: 'Desain' },
  { id: '4', title: 'Tagihan Material Semen.pdf', date: '10 Jul 2026', size: '850 KB', category: 'Tagihan' },
  { id: '5', title: 'Rekap Absensi Pekerja.pdf', date: '05 Jul 2026', size: '1.2 MB', category: 'Laporan' },
];

const FILTERS: FilterType[] = ['Semua', 'Tagihan', 'Laporan', 'Desain'];

export default function DocumentsScreen() {
  const { theme } = useTheme();
  const colors = Colors[theme];
  const [activeFilter, setActiveFilter] = useState<FilterType>('Semua');

  const filteredDocs = mockDocuments.filter(doc => {
    if (activeFilter === 'Semua') return true;
    return doc.category === activeFilter;
  });

  const getIconForCategory = (category: DocCategory) => {
    switch (category) {
      case 'Tagihan': return 'receipt-outline';
      case 'Laporan': return 'pie-chart-outline';
      case 'Desain': return 'color-palette-outline';
      default: return 'document-text-outline';
    }
  };

  const getIconColorForCategory = (category: DocCategory) => {
    switch (category) {
      case 'Tagihan': return '#ef4444'; // Red
      case 'Laporan': return '#10b981'; // Green
      case 'Desain': return '#3b82f6'; // Blue
      default: return colors.tint;
    }
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.header}>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Dokumen & Arsip</Text>
        <Text style={[styles.headerSubtitle, { color: colors.textSecondary }]}>Kelola semua file dan laporan Anda</Text>
      </View>

      <View style={styles.filterContainer}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterScroll}>
          {FILTERS.map(filter => (
            <TouchableOpacity
              key={filter}
              style={[
                styles.filterButton,
                { 
                  backgroundColor: activeFilter === filter ? colors.tint : colors.backgroundElement,
                  borderColor: colors.backgroundSelected
                }
              ]}
              onPress={() => setActiveFilter(filter)}
            >
              <Text style={[
                styles.filterText,
                { color: activeFilter === filter ? '#fff' : colors.text }
              ]}>
                {filter}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {filteredDocs.length === 0 ? (
          <View style={styles.emptyState}>
            <Ionicons name="folder-open-outline" size={64} color={colors.textSecondary} />
            <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Tidak ada dokumen ditemukan.</Text>
          </View>
        ) : (
          filteredDocs.map(doc => (
            <TouchableOpacity 
              key={doc.id} 
              activeOpacity={0.7}
              style={[styles.docCard, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}
            >
              <View style={[styles.iconContainer, { backgroundColor: getIconColorForCategory(doc.category) + '15' }]}>
                <Ionicons name={getIconForCategory(doc.category) as any} size={28} color={getIconColorForCategory(doc.category)} />
              </View>
              
              <View style={styles.docInfo}>
                <Text style={[styles.docTitle, { color: colors.text }]} numberOfLines={1}>
                  {doc.title}
                </Text>
                <View style={styles.docMeta}>
                  <Text style={[styles.docMetaText, { color: colors.textSecondary }]}>
                    {doc.date}
                  </Text>
                  <View style={styles.metaDot} />
                  <Text style={[styles.docMetaText, { color: colors.textSecondary }]}>
                    {doc.size}
                  </Text>
                </View>
              </View>

              <TouchableOpacity style={styles.actionButton}>
                <Ionicons name="download-outline" size={20} color={colors.textSecondary} />
              </TouchableOpacity>
            </TouchableOpacity>
          ))
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    paddingHorizontal: 20,
    marginBottom: 16,
    marginTop: 10,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 14,
  },
  filterContainer: {
    flexDirection: 'row',
    marginBottom: 10,
  },
  filterScroll: {
    paddingHorizontal: 20,
  },
  filterButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    marginRight: 8,
  },
  filterText: {
    fontWeight: '600',
    fontSize: 14,
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 100, // For custom tab bar
  },
  docCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
    marginBottom: 12,
  },
  iconContainer: {
    width: 50,
    height: 50,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  docInfo: {
    flex: 1,
    marginRight: 12,
  },
  docTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 6,
  },
  docMeta: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  docMetaText: {
    fontSize: 12,
  },
  metaDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: '#94a3b8',
    marginHorizontal: 8,
  },
  actionButton: {
    padding: 8,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    marginTop: 16,
    fontSize: 14,
  }
});

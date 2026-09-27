import React, { useState, useEffect, useCallback } from 'react';
import {
  Modal, View, TouchableOpacity, ScrollView, TextInput,
  StyleSheet, Alert, KeyboardAvoidingView, Platform, ActivityIndicator, Image,
} from 'react-native';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import * as DocumentPicker from 'expo-document-picker';
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
  status: string; // 'active' | 'completed'
  manifests?: string;
  manifest_list?: { id: string; manifest_number: string }[];
}

interface Props {
  visible: boolean;
  onClose: () => void;
  onSuccess?: () => void;
}

type Step = 'select_shift' | 'form';

// Helper for formatting date
const formatDate = (dateString: string) => {
  if (!dateString) return '-';
  try {
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return dateString;
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    });
  } catch (e) {
    return dateString;
  }
};

const formatTime = (dateString?: string) => {
  if (!dateString) return '-';
  try {
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return '-';
    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
  } catch (e) {
    return '-';
  }
};

export const ExpenseModal: React.FC<Props> = ({ visible, onClose, onSuccess }) => {
  const [step, setStep] = useState<Step>('select_shift');
  const [shifts, setShifts] = useState<Shift[]>([]);
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [selectedShift, setSelectedShift] = useState<Shift | null>(null);
  const [selectedManifest, setSelectedManifest] = useState<string | null>(null);

  // Form
  const [category, setCategory] = useState('BBM');
  const [customCategory, setCustomCategory] = useState('');
  const [amount, setAmount] = useState('');
  const [notes, setNotes] = useState('');
  const [receipt, setReceipt] = useState<string | null>(null);

  const fetchShifts = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/driver/recent-shifts'); 
      const data = res.data?.data || [];
      setShifts(data);
    } catch (err) {
      console.error('Fetch shifts error', err);
      // Fallback
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (visible) {
      resetForm();
      setStep('select_shift');
      fetchShifts();
    }
  }, [visible, fetchShifts]);

  const resetForm = () => {
    setSelectedShift(null);
    setCategory('BBM');
    setCustomCategory('');
    setAmount('');
    setNotes('');
    setReceipt(null);
  };

  const handleSelectShift = (shift: Shift) => {
    setSelectedShift(shift);
    setSelectedManifest(null);
    setStep('form');
  };

  const pickImage = () => {
    Alert.alert('Pilih Sumber', 'Ambil dari:', [
      {
        text: 'Kamera',
        onPress: async () => {
          const perm = await ImagePicker.requestCameraPermissionsAsync();
          if (!perm.granted) { Alert.alert('Izin Ditolak', 'Akses kamera diperlukan.'); return; }
          const result = await ImagePicker.launchCameraAsync({ mediaTypes: ['images'], quality: 0.5 });
          if (!result.canceled) setReceipt(result.assets[0].uri);
        },
      },
      {
        text: 'Galeri',
        onPress: async () => {
          const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
          if (!perm.granted) { Alert.alert('Izin Ditolak', 'Akses galeri diperlukan.'); return; }
          const result = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ['images'], quality: 0.5 });
          if (!result.canceled) setReceipt(result.assets[0].uri);
        },
      },
      {
        text: 'File Manager',
        onPress: async () => {
          const result = await DocumentPicker.getDocumentAsync({ type: ['application/pdf', 'image/*'], copyToCacheDirectory: true });
          if (!result.canceled && result.assets?.length) setReceipt(result.assets[0].uri);
        },
      },
      { text: 'Batal', style: 'cancel' },
    ]);
  };

  const handleSubmit = async () => {
    if (!amount || !selectedShift) {
      Alert.alert('Perhatian', 'Nominal wajib diisi.');
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

      if (receipt) {
        const filename = receipt.split('/').pop() || 'receipt.jpg';
        const ext = filename.split('.').pop()?.toLowerCase();
        const mimeType = ext === 'pdf' ? 'application/pdf' : `image/${ext === 'png' ? 'png' : 'jpeg'}`;
        formData.append('receipt', { uri: receipt, name: filename, type: mimeType } as any);
      }

      await api.post(`/driver/shifts/${selectedShift.id}/expenses`, formData);
      
      Alert.alert('Berhasil', 'Pengeluaran berhasil disimpan.');
      onSuccess?.();
      onClose();
    } catch (err: any) {
      console.error('Submit expense error', err?.response?.data || err);
      Alert.alert('Gagal', 'Tidak bisa menyimpan pengeluaran.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
        <View style={s.overlay}>
          <View style={s.container}>
            {/* Header */}
            <View style={s.header}>
              <TouchableOpacity onPress={step === 'form' ? () => setStep('select_shift') : onClose} style={s.iconBtn}>
                <Ionicons name={step === 'form' ? 'arrow-back' : 'close'} size={24} color={BRAND.text} />
              </TouchableOpacity>
              <Text style={s.title}>{step === 'select_shift' ? 'Pilih Shift / Hari' : 'Pengeluaran Shift'}</Text>
              <View style={{ width: 24 }} />
            </View>

            {step === 'select_shift' ? (
              /* ---- STEP 1: Shift Picker ---- */
              <ScrollView style={s.body}>
                <Text style={s.instructionText}>
                  Silakan pilih shift dalam kurun waktu 3 hari terakhir (H-3) untuk melampirkan pengeluaran tambahan.
                </Text>
                
                {loading ? (
                  <ActivityIndicator size="large" color={BRAND.primary} style={{ marginTop: 40 }} />
                ) : shifts.length === 0 ? (
                  <View style={s.emptyBox}>
                    <Ionicons name="calendar-outline" size={48} color={BRAND.border} />
                    <Text style={{ color: BRAND.muted, marginTop: 12 }}>Tidak ada riwayat shift di 3 hari terakhir.</Text>
                  </View>
                ) : (
                  shifts.map(shift => (
                    <TouchableOpacity key={shift.id} style={s.taskCard} onPress={() => handleSelectShift(shift)}>
                      <View style={s.taskCardTop}>
                        <Text style={s.taskRef}>Shift: {formatDate(shift.work_date)}</Text>
                        <View style={[s.statusBadge, { backgroundColor: shift.status === 'active' ? BRAND.primarySoft : BRAND.success + '18' }]}>
                          <View style={[s.statusDot, { backgroundColor: shift.status === 'active' ? BRAND.primary : BRAND.success }]} />
                          <Text style={[s.statusText, { color: shift.status === 'active' ? BRAND.primary : BRAND.success }]}>
                            {shift.status === 'active' ? 'Sedang Aktif' : 'Selesai'}
                          </Text>
                        </View>
                      </View>
                      <Text style={s.taskSub} numberOfLines={2}>
                        Kendaraan: {shift.vehicle_plate_number || '-'} {shift.vehicle_name ? `(${shift.vehicle_name})` : ''}
                      </Text>
                      {shift.manifests && shift.manifests !== 'Tidak ada Delivery Order' ? (
                        <Text style={[s.taskSub, { marginTop: 4, color: BRAND.primary }]} numberOfLines={2}>
                          Delivery Order : {shift.manifests}
                        </Text>
                      ) : null}
                      <View style={{ flexDirection: 'row', gap: 16, marginTop: 8 }}>
                        <Text style={[s.taskSub, { color: BRAND.text, fontWeight: '600' }]}>
                          Clock In: {formatTime(shift.check_in_at)}
                        </Text>
                        <Text style={[s.taskSub, { color: BRAND.text, fontWeight: '600' }]}>
                          Clock Out: {formatTime(shift.check_out_at)}
                        </Text>
                      </View>
                    </TouchableOpacity>
                  ))
                )}
                <View style={{ height: 30 }} />
              </ScrollView>
            ) : (
              /* ---- STEP 2: Expense Form ---- */
              <>
                <ScrollView style={s.body}>
                  <View style={s.selectedInfo}>
                    <Ionicons name="calendar" size={16} color={BRAND.primary} />
                    <Text style={s.selectedRef}>Pengeluaran untuk Shift {selectedShift ? formatDate(selectedShift.work_date) : ''}</Text>
                  </View>

                  {selectedShift?.manifest_list && selectedShift.manifest_list.length > 0 && (
                    <>
                      <Text style={[s.label, { marginTop: 16 }]}>Untuk Delivery Order (Opsional)</Text>
                      <View style={s.chipRow}>
                        {selectedShift.manifest_list.map((m) => (
                          <TouchableOpacity
                            key={m.id}
                            style={selectedManifest === m.id ? s.chipActive : s.chip}
                            onPress={() => setSelectedManifest(selectedManifest === m.id ? null : m.id)}
                          >
                            <Text style={selectedManifest === m.id ? s.chipTextActive : s.chipText}>
                              {m.manifest_number}
                            </Text>
                          </TouchableOpacity>
                        ))}
                      </View>
                    </>
                  )}

                  <View style={s.totalBox}>
                    <Text style={{ color: BRAND.muted, fontSize: 12 }}>Total Pengeluaran (Nominal)</Text>
                    <Text style={{ color: BRAND.primary, fontSize: 24, fontWeight: '700', marginTop: 4 }}>
                      Rp {parseInt(amount || '0').toLocaleString('id-ID')}
                    </Text>
                  </View>

                  <Text style={s.label}>Jenis Pengeluaran</Text>
                  <View style={s.chipRow}>
                    {['BBM', 'Tol', 'Parkir', 'Lainnya'].map(cat => (
                      <TouchableOpacity
                        key={cat}
                        style={category === cat ? s.chipActive : s.chip}
                        onPress={() => setCategory(cat)}
                      >
                        <Text style={category === cat ? s.chipTextActive : s.chipText}>{cat}</Text>
                      </TouchableOpacity>
                    ))}
                  </View>

                  {category === 'Lainnya' && (
                    <TextInput
                      style={s.input}
                      placeholder="Nama kategori..."
                      placeholderTextColor={BRAND.muted}
                      value={customCategory}
                      onChangeText={setCustomCategory}
                    />
                  )}

                  <Text style={s.label}>Nominal (Rp)</Text>
                  <TextInput
                    style={s.input}
                    placeholder="50000"
                    placeholderTextColor={BRAND.muted}
                    keyboardType="numeric"
                    value={amount}
                    onChangeText={setAmount}
                  />

                  <Text style={s.label}>Catatan</Text>
                  <TextInput
                    style={[s.input, { height: 70, textAlignVertical: 'top' }]}
                    placeholder="Opsional..."
                    placeholderTextColor={BRAND.muted}
                    multiline
                    value={notes}
                    onChangeText={setNotes}
                  />

                  <Text style={s.label}>Bukti / Struk</Text>
                  <TouchableOpacity style={s.uploadBtn} onPress={pickImage}>
                    {receipt ? (
                      <Image source={{ uri: receipt }} style={s.previewImg} />
                    ) : (
                      <View style={s.uploadPlaceholder}>
                        <Ionicons name="cloud-upload-outline" size={28} color={BRAND.muted} />
                        <Text style={{ color: BRAND.muted, fontSize: 12, marginTop: 6 }}>Tap untuk upload</Text>
                      </View>
                    )}
                  </TouchableOpacity>

                  <View style={{ height: 40 }} />
                </ScrollView>

                <View style={s.footer}>
                  <TouchableOpacity style={s.submitBtn} onPress={handleSubmit} disabled={submitting}>
                    {submitting ? (
                      <ActivityIndicator color={BRAND.white} />
                    ) : (
                      <Text style={s.submitBtnText}>Simpan Pengeluaran</Text>
                    )}
                  </TouchableOpacity>
                </View>
              </>
            )}
          </View>
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
};

const s = StyleSheet.create({
  overlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)', justifyContent: 'flex-end' },
  container: { backgroundColor: BRAND.white, borderTopLeftRadius: 20, borderTopRightRadius: 20, maxHeight: '92%', minHeight: '60%' },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 16, paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: BRAND.border },
  iconBtn: { padding: 4 },
  title: { fontSize: 16, fontWeight: '700', color: BRAND.text },
  body: { paddingHorizontal: 16, paddingTop: 12 },
  emptyBox: { alignItems: 'center', justifyContent: 'center', paddingVertical: 60 },
  instructionText: { color: BRAND.muted, fontSize: 13, marginBottom: 16, lineHeight: 20 },
  
  // Task/Shift card
  taskCard: { backgroundColor: BRAND.card, borderRadius: 12, padding: 14, marginBottom: 10, borderWidth: 1, borderColor: BRAND.border },
  taskCardTop: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 },
  taskRef: { fontSize: 14, fontWeight: '700', color: BRAND.text, flex: 1 },
  taskSub: { fontSize: 12, color: BRAND.muted },
  statusBadge: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 8, paddingVertical: 3, borderRadius: 99 },
  statusDot: { width: 6, height: 6, borderRadius: 3, marginRight: 5 },
  statusText: { fontSize: 10, fontWeight: '700' },
  
  // Form
  selectedInfo: { flexDirection: 'row', alignItems: 'center', gap: 6, backgroundColor: BRAND.primarySoft, padding: 10, borderRadius: 8, marginBottom: 12 },
  selectedRef: { fontSize: 13, fontWeight: '700', color: BRAND.primary },
  totalBox: { backgroundColor: BRAND.primarySoft, padding: 16, borderRadius: 8, alignItems: 'center', marginBottom: 16 },
  label: { fontSize: 13, fontWeight: '600', color: BRAND.text, marginBottom: 8 },
  chipRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 16 },
  chip: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, backgroundColor: BRAND.card, borderWidth: 1, borderColor: BRAND.border },
  chipActive: { paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, backgroundColor: BRAND.primary },
  chipText: { fontSize: 13, color: BRAND.text },
  chipTextActive: { fontSize: 13, color: BRAND.white, fontWeight: '600' },
  input: { borderWidth: 1, borderColor: BRAND.border, borderRadius: 10, paddingHorizontal: 14, paddingVertical: 10, fontSize: 14, color: BRAND.text, marginBottom: 16, backgroundColor: BRAND.card },
  uploadBtn: { borderWidth: 1.5, borderColor: BRAND.border, borderStyle: 'dashed', borderRadius: 12, overflow: 'hidden', marginBottom: 10 },
  uploadPlaceholder: { alignItems: 'center', justifyContent: 'center', paddingVertical: 28 },
  previewImg: { width: '100%', height: 160, resizeMode: 'cover' },
  footer: { paddingHorizontal: 16, paddingVertical: 12, borderTopWidth: 1, borderTopColor: BRAND.border },
  submitBtn: { backgroundColor: BRAND.primary, borderRadius: 12, paddingVertical: 14, alignItems: 'center' },
  submitBtnText: { color: BRAND.white, fontWeight: '700', fontSize: 14 },
});

export default ExpenseModal;

import React, { useState, useRef } from 'react';
import {
  Modal,
  View,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  TextInput,
  Image,
  Alert,
  Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Text } from '@/components/CustomText';
import * as ImagePicker from 'expo-image-picker';
import * as DocumentPicker from 'expo-document-picker';

const BRAND = {
  primary: '#0756C6',
  primarySoft: '#EAF3FF',
  white: '#FFFFFF',
  page: '#F8FAFC',
  text: '#1E293B',
  muted: '#64748B',
  border: '#E2E8F0',
  success: '#10B981',
  danger: '#EF4444',
  warning: '#F59E0B', // added warning color
};

const ImageUploadBox = ({ title = "Foto/File", value, onChange, onRemove, allowRemove }: { title?: string, value?: string | null, onChange?: (uri: string) => void, onRemove?: () => void, allowRemove?: boolean }) => {
  const [internalUri, setInternalUri] = useState<string | null>(null);
  const [isPdf, setIsPdf] = useState(false);

  const isWeb = Platform.OS === 'web';
  const fileInputRef = useRef(null as unknown as HTMLInputElement);
  const fileUri = value !== undefined ? value : internalUri;

  const handleWebChange = (e: any) => {
    const files = e.target.files as FileList;
    if (!files || files.length === 0) return;
    const file = files[0];
    const uri = URL.createObjectURL(file);
    setInternalUri(uri);
    const pdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
    setIsPdf(pdf);
    onChange?.(uri);
  };

  const handlePickFile = async () => {
    if (isWeb) {
      fileInputRef.current?.click();
      return;
    }
    Alert.alert(
      "Pilih Sumber",
      "Darimana Anda ingin mengambil file?",
      [
        {
          text: "Kamera",
          onPress: async () => {
            const permissionResult = await ImagePicker.requestCameraPermissionsAsync();
            if (permissionResult.granted === false) {
              Alert.alert("Izin Ditolak", "Anda perlu memberikan akses kamera.");
              return;
            }
            const result = await ImagePicker.launchCameraAsync({
              mediaTypes: ['images'],
              quality: 0.5,
            });
            if (!result.canceled) {
              const uri = result.assets[0].uri;
              setInternalUri(uri);
              setIsPdf(false);
              onChange?.(uri);
            }
          }
        },
        {
          text: "Galeri",
          onPress: async () => {
            const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync();
            if (permissionResult.granted === false) {
              Alert.alert("Izin Ditolak", "Anda perlu memberikan akses galeri.");
              return;
            }
            const result = await ImagePicker.launchImageLibraryAsync({
              mediaTypes: ['images'],
              quality: 0.5,
            });
            if (!result.canceled) {
              const uri = result.assets[0].uri;
              setInternalUri(uri);
              setIsPdf(false);
              onChange?.(uri);
            }
          }
        },
        {
          text: "File Manager (PDF/Gambar)",
          onPress: async () => {
            const result = await DocumentPicker.getDocumentAsync({
              type: ['application/pdf', 'image/*'],
              copyToCacheDirectory: true,
            });
            if (!result.canceled && result.assets && result.assets.length > 0) {
              const file = result.assets[0];
              const uri = file.uri;
              setInternalUri(uri);
              setIsPdf(file.mimeType === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf'));
              onChange?.(uri);
            }
          }
        },
        { text: "Batal", style: "cancel" }
      ]
    );
  };

  return (
    <TouchableOpacity style={styles.uploadBox} onPress={handlePickFile}>
      {fileUri ? (
        <>
          {isPdf ? (
            <View style={{ alignItems: 'center', justifyContent: 'center', flex: 1 }}>
              <Ionicons name="document-text" size={32} color={BRAND.danger} />
              <Text style={[styles.uploadText, { color: BRAND.danger, marginTop: 8 }]}>PDF Dipilih</Text>
            </View>
          ) : (
            <Image source={{ uri: fileUri }} style={{ width: '100%', height: '100%', borderRadius: 8 }} />
          )}
          {allowRemove && (
            <TouchableOpacity 
              style={{ position: 'absolute', top: -8, right: -8, backgroundColor: 'white', borderRadius: 12, padding: 2, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.2, shadowRadius: 1 }}
              onPress={(e) => { e.stopPropagation(); onRemove?.(); }}
            >
              <Ionicons name="close-circle" size={24} color={BRAND.danger} />
            </TouchableOpacity>
          )}
        </>
      ) : (
        <>
          <Ionicons name="cloud-upload-outline" size={24} color={BRAND.primary} />
          <Text style={styles.uploadText}>{title}</Text>
          {allowRemove && (
            <TouchableOpacity 
              style={{ position: 'absolute', top: -8, right: -8, backgroundColor: 'white', borderRadius: 12, padding: 2, elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.2, shadowRadius: 1 }}
              onPress={(e) => { e.stopPropagation(); onRemove?.(); }}
            >
              <Ionicons name="close-circle" size={24} color={BRAND.danger} />
            </TouchableOpacity>
          )}
        </>
      )}
      {isWeb && (
        <input
          type="file"
          accept="image/*,application/pdf"
          style={{ display: 'none' }}
          ref={fileInputRef}
          onChange={handleWebChange}
        />
      )}
    </TouchableOpacity>
  );
};

// 1. Modal Keberangkatan
// Added constant for checklist items
const DEPARTURE_CHECKLIST_ITEMS = [
  'Dokumen kendaraan lengkap',
  'Surat jalan / DO tersedia',
  'Kondisi ban baik',
  'Lampu & sein berfungsi',
  'Rem berfungsi normal',
  'Bahan bakar cukup',
];

const ARRIVAL_CHECKLIST_ITEMS = [
  'Driver tiba di lokasi tujuan',
  'Barang / muatan lengkap sesuai DO',
  'Segel / kemasan masih aman',
  'Penerima / PIC gudang siap'
];

type ChecklistStatus = 'check' | 'cross' | 'warning' | null;

export const ModalKeberangkatan = ({ visible, onClose, onSubmit, task }: any) => {
  const [startOdometer, setStartOdometer] = useState('');
  const [startFuel, setStartFuel] = useState('');
  const [notes, setNotes] = useState('');
  // Remove the three static proof states – we will use a dynamic list of attachments
  const [attachments, setAttachments] = useState<Array<string | null>>([]); // store file URIs

  // State untuk melacak status setiap item checklist berdasarkan index
  const [checklist, setChecklist] = useState<Record<number, ChecklistStatus>>({});

  // Fungsi rotasi status: null -> check -> cross -> warning -> null
  const handleToggleChecklist = (idx: number) => {
    setChecklist((prev) => {
      const current = prev[idx] || null;
      let next: ChecklistStatus = null;

      if (current === null) next = 'check';
      else if (current === 'check') next = 'cross';
      else if (current === 'cross') next = 'warning';
      else if (current === 'warning') next = null;

      return { ...prev, [idx]: next };
    });
  };

  // Helper render ikon dan warna berdasarkan status
  const renderCheckIcon = (status: ChecklistStatus) => {
    switch (status) {
      case 'check':
        return <Ionicons name="checkmark-circle" size={22} color={BRAND.success} />;
      case 'cross':
        return <Ionicons name="close-circle" size={22} color={BRAND.danger} />;
      case 'warning':
        return <Ionicons name="alert-circle" size={22} color={BRAND.warning} />;
      default:
        return <Ionicons name="square-outline" size={22} color={BRAND.muted} />;
    }
  };

  const handleSubmit = () => {
    // Buat checklist dengan label sebagai key, bukan index
    const checklistLabeled: Record<string, string> = {};
    DEPARTURE_CHECKLIST_ITEMS.forEach((label, idx) => {
      const status = checklist[idx] || 'belum_diisi';
      checklistLabeled[label] = status;
    });

    onSubmit({
      start_odometer: startOdometer,
      start_fuel: startFuel,
      departure_notes: notes,
      attachments: attachments.filter((uri) => uri), // send only populated URIs
      checklist_status: checklistLabeled,
      attachment_category: 'bukti_keberangkatan',
    });
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <View style={styles.modalBg}>
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity onPress={onClose} style={styles.iconBtn}>
              <Ionicons name="close" size={24} color={BRAND.text} />
            </TouchableOpacity>
            <Text style={styles.modalTitle}>Keberangkatan</Text>
            <View style={{ width: 24 }} />
          </View>

          <ScrollView style={styles.modalBody}>
            <View style={styles.cardHeader}>
              <Text style={styles.cardRef}>{task?.reference_number || '-'}</Text>
              <View style={styles.badge}><Text style={styles.badgeText}>Menunggu</Text></View>
            </View>

            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
              <Text style={styles.sectionTitle}>Checklist Keberangkatan</Text>
              <Text style={{ fontSize: 11, color: BRAND.muted }}>(Klik untuk ubah status)</Text>
            </View>

            {DEPARTURE_CHECKLIST_ITEMS.map((item, idx) => {
              const status = checklist[idx] || null;
              return (
                <TouchableOpacity
                  key={idx}
                  style={[styles.checkRow, { paddingVertical: 4 }]}
                  onPress={() => handleToggleChecklist(idx)}
                  activeOpacity={0.7}
                >
                  {renderCheckIcon(status)}
                  <Text style={[styles.checkText, status === 'cross' && { color: BRAND.danger }]}>
                    {item}
                  </Text>
                </TouchableOpacity>
              );
            })}

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Informasi Kendaraan</Text>
            <View style={{ flexDirection: 'row', gap: 12 }}>
              <View style={{ flex: 1 }}>
                <Text style={styles.inputLabel}>Odometer (KM)</Text>
                <TextInput
                  style={styles.input}
                  placeholder="Contoh: 34589"
                  keyboardType="numeric"
                  value={startOdometer}
                  onChangeText={setStartOdometer}
                />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.inputLabel}>BBM Awal</Text>
                <TextInput
                  style={styles.input}
                  placeholder="Contoh: 3/4"
                  value={startFuel}
                  onChangeText={setStartFuel}
                />
              </View>
            </View>

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Upload Bukti Keberangkatan <Text style={{ color: BRAND.primary }}>(Wajib)</Text></Text>
            {/* Upload Bukti Keberangkatan – cross‑platform */}
            <View style={styles.uploadContainer}>
              {/* Thumbnails for already selected attachments */}
              {attachments.map((uri, idx) => (
                <View key={idx} style={styles.thumbWrapper}>
                  {uri ? (
                    <Image source={{ uri }} style={styles.thumbImage} />
                  ) : (
                    <ImageUploadBox
                      title={`Bukti ${idx + 1}`}
                      value={uri}
                      onChange={(newUri) => {
                        setAttachments((prev) => {
                          const copy = [...prev];
                          copy[idx] = newUri;
                          return copy;
                        });
                      }}
                    />
                  )}
                  <TouchableOpacity
                    style={styles.removeBtn}
                    onPress={() => {
                      setAttachments((prev) => {
                        const copy = [...prev];
                        copy.splice(idx, 1);
                        return copy;
                      });
                    }}
                  >
                    <Ionicons name="close-circle" size={20} color={BRAND.danger} />
                  </TouchableOpacity>
                </View>
              ))}
              {/* Add new attachment */}
              {attachments.length < 10 && (
                Platform.OS === 'web' ? (
                  <View style={styles.addAttachmentWrapper}>
                    <TouchableOpacity
                      style={styles.addAttachmentBtn}
                      onPress={() => {
                        const input = document.getElementById('fileInput');
                        if (input) (input as HTMLInputElement).click();
                      }}
                    >
                      <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                      <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                    </TouchableOpacity>
                    <input
                      id="fileInput"
                      type="file"
                      multiple
                      accept="image/*,application/pdf"
                      style={{ display: 'none' }}
                      onChange={(e) => {
                        const files = (e.target as HTMLInputElement).files;
                        if (files) {
                          const newUris: any[] = [];
                          for (let i = 0; i < files.length && attachments.length + newUris.length < 10; i++) {
                            const file = files[i];
                            const uri = URL.createObjectURL(file);
                            newUris.push(uri);
                          }
                          setAttachments((prev) => [...prev, ...newUris]);
                        }
                      }}
                    />
                  </View>
                ) : (
                  <TouchableOpacity
                    style={styles.addAttachmentBtn}
                    onPress={() => setAttachments((prev) => [...prev, null])}
                  >
                    <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                    <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                  </TouchableOpacity>
                )
              )}
            </View>

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Catatan (Opsional)</Text>
            <TextInput
              style={[styles.input, { height: 80, textAlignVertical: 'top' }]}
              placeholder="Tulis catatan..."
              multiline
              value={notes}
              onChangeText={setNotes}
            />

            <View style={{ height: 40 }} />
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity style={styles.primaryBtn} onPress={handleSubmit}>
              <Text style={styles.primaryBtnText}>Konfirmasi Berangkat</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

// 2. Modal Pengeluaran
export const ModalPengeluaran = ({ visible, onClose, onSubmit, task }: any) => {
  const [category, setCategory] = useState('BBM');
  const [customCategory, setCustomCategory] = useState('');
  const [amount, setAmount] = useState('');
  const [notes, setNotes] = useState('');
  const [receipt, setReceipt] = useState<string | null>(null);

  const handleSubmit = () => {
    if (!amount) return; // simple validation
    onSubmit({
      category: category,
      description: customCategory,
      amount: amount,
      notes: notes,
      receipt: receipt
    });
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <View style={styles.modalBg}>
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity onPress={onClose} style={styles.iconBtn}>
              <Ionicons name="close" size={24} color={BRAND.text} />
            </TouchableOpacity>
            <Text style={styles.modalTitle}>Pengeluaran Perjalanan</Text>
            <View style={{ width: 24 }} />
          </View>
          
          <ScrollView style={styles.modalBody}>
            <View style={styles.cardHeader}>
              <Text style={styles.cardRef}>{task?.reference_number || '-'}</Text>
            </View>

            <View style={styles.expenseTotalBox}>
              <Text style={{ color: BRAND.muted, fontSize: 12 }}>Total Pengeluaran (Nominal)</Text>
              <Text style={{ color: BRAND.primary, fontSize: 24, fontWeight: '700', marginTop: 4 }}>
                Rp {parseInt(amount || '0').toLocaleString('id-ID')}
              </Text>
            </View>

            <Text style={styles.sectionTitle}>Jenis Pengeluaran</Text>
            <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 16 }}>
              {['BBM', 'Tol', 'Parkir', 'Lainnya'].map((cat) => (
                <TouchableOpacity 
                  key={cat} 
                  style={category === cat ? styles.chipActive : styles.chip}
                  onPress={() => setCategory(cat)}
                >
                  <Text style={category === cat ? styles.chipTextActive : styles.chipText}>{cat}</Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={styles.inputLabel}>Deskripsi / Lokasi</Text>
            <TextInput 
              style={styles.input} 
              placeholder={category === 'Lainnya' ? "Contoh: Tambal Ban" : "Contoh: SPBU KM 47 / Tol Pasteur"} 
              value={customCategory} 
              onChangeText={setCustomCategory} 
            />

            <Text style={styles.inputLabel}>Nominal</Text>
            <TextInput 
              style={styles.input} 
              placeholder="Rp 0" 
              keyboardType="numeric" 
              value={amount}
              onChangeText={setAmount}
            />
            
            <Text style={styles.inputLabel}>Catatan Tambahan</Text>
            <TextInput 
              style={[styles.input, styles.textArea]} 
              placeholder="Masukan catatan..." 
              multiline 
              numberOfLines={3} 
              value={notes}
              onChangeText={setNotes}
            />

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Upload Bukti / Struk</Text>
            <View style={{ width: 120, height: 120 }}>
              <ImageUploadBox title="Struk" value={receipt} onChange={setReceipt} />
            </View>
            
            <View style={{ height: 40 }} />
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity style={styles.primaryBtn} onPress={handleSubmit}>
              <Text style={styles.primaryBtnText}>Simpan Pengeluaran</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

// 3. Modal Tiba di Lokasi
export const ModalTiba = ({ visible, onClose, onSubmit, task }: any) => {
  const [notes, setNotes] = useState('');
  const [attachments, setAttachments] = useState<Array<string | null>>([]);
  const [checklist, setChecklist] = useState<Record<number, ChecklistStatus>>({});

  const handleToggleChecklist = (idx: number) => {
    setChecklist((prev) => {
      const current = prev[idx] || null;
      let next: ChecklistStatus = null;
      if (current === null) next = 'check';
      else if (current === 'check') next = 'cross';
      else if (current === 'cross') next = 'warning';
      else if (current === 'warning') next = null;
      return { ...prev, [idx]: next };
    });
  };

  const renderCheckIcon = (status: ChecklistStatus) => {
    switch (status) {
      case 'check': return <Ionicons name="checkmark-circle" size={22} color={BRAND.success} />;
      case 'cross': return <Ionicons name="close-circle" size={22} color={BRAND.danger} />;
      case 'warning': return <Ionicons name="alert-circle" size={22} color={BRAND.warning} />;
      default: return <Ionicons name="square-outline" size={22} color={BRAND.muted} />;
    }
  };

  const handleSubmit = () => {
    const checklistLabeled: Record<string, string> = {};
    ARRIVAL_CHECKLIST_ITEMS.forEach((label, idx) => {
      checklistLabeled[label] = checklist[idx] || 'belum_diisi';
    });

    onSubmit({
      arrival_notes: notes,
      attachments: attachments.filter((uri) => uri),
      arrival_checklist: checklistLabeled,
      attachment_category: 'bukti_kedatangan',
    });
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <View style={styles.modalBg}>
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity onPress={onClose} style={styles.iconBtn}>
              <Ionicons name="close" size={24} color={BRAND.text} />
            </TouchableOpacity>
            <Text style={styles.modalTitle}>Konfirmasi Tiba</Text>
            <View style={{ width: 24 }} />
          </View>
          
          <ScrollView style={styles.modalBody}>
            <View style={styles.cardHeader}>
              <Text style={styles.cardRef}>{task?.reference_number || '-'}</Text>
              <View style={styles.badge}><Text style={styles.badgeText}>On Route</Text></View>
            </View>

            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
              <Text style={styles.sectionTitle}>Checklist Tiba di Lokasi</Text>
              <Text style={{ fontSize: 11, color: BRAND.muted }}>(Klik untuk ubah status)</Text>
            </View>

            {ARRIVAL_CHECKLIST_ITEMS.map((item, idx) => {
              const status = checklist[idx] || null;
              return (
                <TouchableOpacity
                  key={idx}
                  style={[styles.checkRow, { paddingVertical: 4 }]}
                  onPress={() => handleToggleChecklist(idx)}
                  activeOpacity={0.7}
                >
                  <View style={{ width: 28, alignItems: 'center' }}>
                    {renderCheckIcon(status)}
                  </View>
                  <Text style={[
                    styles.checkText,
                    status === 'cross' && { color: BRAND.danger, fontWeight: '700' },
                    status === 'warning' && { color: BRAND.warning, fontWeight: '700' },
                  ]}>{item}</Text>
                </TouchableOpacity>
              );
            })}

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Catatan Tiba</Text>
            <TextInput
              style={[styles.input, { height: 80, textAlignVertical: 'top' }]}
              placeholder="Tuliskan catatan kedatangan atau kendala (opsional)..."
              placeholderTextColor="#94A3B8"
              multiline
              value={notes}
              onChangeText={setNotes}
            />

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Upload Bukti Tiba / Dokumen <Text style={{ color: BRAND.primary }}>(Wajib)</Text></Text>
            {/* Upload Bukti Tiba – cross‑platform */}
            <View style={styles.uploadContainer}>
              {/* Thumbnails for already selected attachments */}
              {attachments.map((uri, idx) => (
                <View key={idx} style={styles.thumbWrapper}>
                  {uri ? (
                    <Image source={{ uri }} style={styles.thumbImage} />
                  ) : (
                    <ImageUploadBox
                      title={`Bukti ${idx + 1}`}
                      value={uri}
                      onChange={(newUri) => {
                        setAttachments((prev) => {
                          const copy = [...prev];
                          copy[idx] = newUri;
                          return copy;
                        });
                      }}
                    />
                  )}
                  <TouchableOpacity
                    style={styles.removeBtn}
                    onPress={() => {
                      setAttachments((prev) => {
                        const copy = [...prev];
                        copy.splice(idx, 1);
                        return copy;
                      });
                    }}
                  >
                    <Ionicons name="close-circle" size={20} color={BRAND.danger} />
                  </TouchableOpacity>
                </View>
              ))}
              {/* Add new attachment */}
              {attachments.length < 10 && (
                Platform.OS === 'web' ? (
                  <View style={styles.addAttachmentWrapper}>
                    <TouchableOpacity
                      style={styles.addAttachmentBtn}
                      onPress={() => {
                        const input = document.getElementById('fileInputTiba');
                        if (input) (input as HTMLInputElement).click();
                      }}
                    >
                      <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                      <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                    </TouchableOpacity>
                    <input
                      id="fileInputTiba"
                      type="file"
                      multiple
                      accept="image/*,application/pdf"
                      style={{ display: 'none' }}
                      onChange={(e) => {
                        const files = (e.target as HTMLInputElement).files;
                        if (files) {
                          const newUris: any[] = [];
                          for (let i = 0; i < files.length && attachments.length + newUris.length < 10; i++) {
                            const file = files[i];
                            const uri = URL.createObjectURL(file);
                            newUris.push(uri);
                          }
                          setAttachments((prev) => [...prev, ...newUris]);
                        }
                      }}
                    />
                  </View>
                ) : (
                  <TouchableOpacity
                    style={styles.addAttachmentBtn}
                    onPress={() => setAttachments((prev) => [...prev, null])}
                  >
                    <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                    <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                  </TouchableOpacity>
                )
              )}
            </View>
            
            <View style={{ height: 40 }} />
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity style={styles.primaryBtn} onPress={handleSubmit}>
              <Text style={styles.primaryBtnText}>Konfirmasi Tiba</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

// 4. Modal Serah Terima
export const ModalSerahTerima = ({ visible, onClose, onSubmit, task }: any) => {
  const [receiverName, setReceiverName] = useState('');
  const [receiverRole, setReceiverRole] = useState('');
  const [itemCondition, setItemCondition] = useState('Baik');
  const [completedOdo, setCompletedOdo] = useState('');
  
  const [attachments, setAttachments] = useState<Array<string | null>>([]);
  const [signatureUri, setSignatureUri] = useState<string | null>(null);

  const handleSubmit = () => {
    const allAttachments = attachments.filter((uri) => uri);
    // If signature exists, we can either push it to attachments or send separately
    // The user suggested storing it in task_attachments, so let's push it if it exists.
    if (signatureUri) {
        allAttachments.push(signatureUri);
    }

    onSubmit({
      receiver_name: receiverName,
      receiver_role: receiverRole,
      item_condition: itemCondition,
      completed_odometer: completedOdo,
      attachments: allAttachments,
      attachment_category: 'bukti_serah_terima',
    });
  };

  return (
    <Modal visible={visible} animationType="slide" transparent>
      <View style={styles.modalBg}>
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity onPress={onClose} style={styles.iconBtn}>
              <Ionicons name="close" size={24} color={BRAND.text} />
            </TouchableOpacity>
            <Text style={styles.modalTitle}>Bukti Serah Terima</Text>
            <View style={{ width: 24 }} />
          </View>
          
          <ScrollView style={styles.modalBody}>
            <View style={styles.cardHeader}>
              <Text style={styles.cardRef}>{task?.reference_number || '-'}</Text>
            </View>

            <Text style={styles.sectionTitle}>Informasi Penerima</Text>
            <Text style={styles.inputLabel}>Nama Penerima</Text>
            <TextInput style={styles.input} placeholder="Contoh: Agus Setiawan" value={receiverName} onChangeText={setReceiverName} />
            <Text style={styles.inputLabel}>Jabatan / PIC</Text>
            <TextInput style={styles.input} placeholder="PIC Gudang" value={receiverRole} onChangeText={setReceiverRole} />
            <Text style={styles.inputLabel}>Odometer Akhir (KM)</Text>
            <TextInput style={styles.input} placeholder="Contoh: 34600" keyboardType="numeric" value={completedOdo} onChangeText={setCompletedOdo} />

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Kondisi Barang Saat Serah Terima</Text>
            <View style={{ flexDirection: 'row', gap: 8, marginBottom: 16 }}>
              <TouchableOpacity style={itemCondition === 'Baik' ? styles.chipActive : styles.chip} onPress={() => setItemCondition('Baik')}>
                <Text style={itemCondition === 'Baik' ? styles.chipTextActive : styles.chipText}>Baik</Text>
              </TouchableOpacity>
              <TouchableOpacity style={itemCondition === 'Rusak Ringan' ? styles.chipActive : styles.chip} onPress={() => setItemCondition('Rusak Ringan')}>
                <Text style={itemCondition === 'Rusak Ringan' ? styles.chipTextActive : styles.chipText}>Rusak Ringan</Text>
              </TouchableOpacity>
              <TouchableOpacity style={itemCondition === 'Rusak Berat' ? styles.chipActive : styles.chip} onPress={() => setItemCondition('Rusak Berat')}>
                <Text style={itemCondition === 'Rusak Berat' ? styles.chipTextActive : styles.chipText}>Rusak Berat</Text>
              </TouchableOpacity>
            </View>

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Upload Bukti Serah Terima <Text style={{ color: BRAND.primary }}>(Wajib)</Text></Text>
            {/* Upload Bukti Serah Terima – cross‑platform */}
            <View style={styles.uploadContainer}>
              {/* Thumbnails for already selected attachments */}
              {attachments.map((uri, idx) => (
                <View key={idx} style={styles.thumbWrapper}>
                  {uri ? (
                    <Image source={{ uri }} style={styles.thumbImage} />
                  ) : (
                    <ImageUploadBox
                      title={`Bukti ${idx + 1}`}
                      value={uri}
                      onChange={(newUri) => {
                        setAttachments((prev) => {
                          const copy = [...prev];
                          copy[idx] = newUri;
                          return copy;
                        });
                      }}
                    />
                  )}
                  <TouchableOpacity
                    style={styles.removeBtn}
                    onPress={() => {
                      setAttachments((prev) => {
                        const copy = [...prev];
                        copy.splice(idx, 1);
                        return copy;
                      });
                    }}
                  >
                    <Ionicons name="close-circle" size={20} color={BRAND.danger} />
                  </TouchableOpacity>
                </View>
              ))}
              {/* Add new attachment */}
              {attachments.length < 10 && (
                Platform.OS === 'web' ? (
                  <View style={styles.addAttachmentWrapper}>
                    <TouchableOpacity
                      style={styles.addAttachmentBtn}
                      onPress={() => {
                        const input = document.getElementById('fileInputSerahTerima');
                        if (input) (input as HTMLInputElement).click();
                      }}
                    >
                      <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                      <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                    </TouchableOpacity>
                    <input
                      id="fileInputSerahTerima"
                      type="file"
                      multiple
                      accept="image/*,application/pdf"
                      style={{ display: 'none' }}
                      onChange={(e) => {
                        const files = (e.target as HTMLInputElement).files;
                        if (files) {
                          const newUris: any[] = [];
                          for (let i = 0; i < files.length && attachments.length + newUris.length < 10; i++) {
                            const file = files[i];
                            const uri = URL.createObjectURL(file);
                            newUris.push(uri);
                          }
                          setAttachments((prev) => [...prev, ...newUris]);
                        }
                      }}
                    />
                  </View>
                ) : (
                  <TouchableOpacity
                    style={styles.addAttachmentBtn}
                    onPress={() => setAttachments((prev) => [...prev, null])}
                  >
                    <Ionicons name="add-circle-outline" size={34} color={BRAND.primary} />
                    <Text style={styles.addAttachmentText}>Tambah Bukti</Text>
                  </TouchableOpacity>
                )
              )}
            </View>

            <Text style={[styles.sectionTitle, { marginTop: 16 }]}>Foto Tanda Tangan Penerima (Opsional)</Text>
            <View style={{ width: 120, height: 120, marginBottom: 16 }}>
              <ImageUploadBox title="Tanda Tangan" value={signatureUri} onChange={setSignatureUri} />
            </View>
            
            <View style={{ height: 40 }} />
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity style={styles.primaryBtn} onPress={handleSubmit}>
              <Text style={styles.primaryBtnText}>Submit Laporan Tugas</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  modalBg: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContainer: {
    backgroundColor: BRAND.page,
    height: '90%',
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    overflow: 'hidden',
  },
  modalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 16,
    backgroundColor: BRAND.white,
    borderBottomWidth: 1,
    borderBottomColor: BRAND.border,
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: BRAND.text,
  },
  iconBtn: {
    padding: 4,
  },
  modalBody: {
    flex: 1,
    padding: 16,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: BRAND.white,
    padding: 12,
    borderRadius: 8,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: BRAND.border,
  },
  cardRef: {
    fontWeight: '700',
    fontSize: 14,
  },
  badge: {
    backgroundColor: BRAND.primarySoft,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
  },
  badgeText: {
    color: BRAND.primary,
    fontSize: 10,
    fontWeight: '700',
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: BRAND.text,
    marginBottom: 12,
  },
  checkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  checkText: {
    fontSize: 13,
    marginLeft: 8,
    color: BRAND.text,
  },
  inputLabel: {
    fontSize: 12,
    color: BRAND.text,
    marginBottom: 6,
    marginTop: 8,
    fontWeight: '600',
  },
  input: {
    backgroundColor: BRAND.white,
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    padding: 12,
    fontSize: 13,
    color: BRAND.text,
  },
  uploadRow: {
    flexDirection: 'row',
    gap: 8,
  },
  uploadBox: {
    flex: 1,
    height: 80,
    backgroundColor: BRAND.white,
    borderWidth: 1,
    borderColor: BRAND.primary,
    borderStyle: 'dashed',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  uploadText: {
    fontSize: 10,
    color: BRAND.primary,
    marginTop: 4,
  },
  modalFooter: {
    padding: 16,
    backgroundColor: BRAND.white,
    borderTopWidth: 1,
    borderTopColor: BRAND.border,
  },
  primaryBtn: {
    backgroundColor: BRAND.primary,
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
  },
  primaryBtnText: {
    color: BRAND.white,
    fontWeight: '700',
    fontSize: 14,
  },
  expenseTotalBox: {
    backgroundColor: BRAND.primarySoft,
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 16,
  },
  chipActive: {
    backgroundColor: BRAND.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
  },
  chipTextActive: {
    color: BRAND.white,
    fontSize: 12,
    fontWeight: '600',
  },
  chip: {
    backgroundColor: BRAND.white,
    borderWidth: 1,
    borderColor: BRAND.border,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
  },
  chipText: {
    color: BRAND.text,
    fontSize: 12,
    fontWeight: '600',
  },
  signatureBox: {
    height: 120,
    backgroundColor: BRAND.white,
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  uploadContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
    marginBottom: 8,
  },
  thumbWrapper: {
    width: 80,
    height: 80,
    borderRadius: 8,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: BRAND.border,
    backgroundColor: BRAND.white,
  },
  thumbImage: {
    width: '100%',
    height: '100%',
    borderRadius: 8,
  },
  removeBtn: {
    position: 'absolute',
    top: -6,
    right: -6,
    backgroundColor: BRAND.white,
    borderRadius: 10,
  },
  addAttachmentWrapper: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  addAttachmentBtn: {
    width: 80,
    height: 80,
    borderWidth: 1,
    borderColor: BRAND.primary,
    borderStyle: 'dashed',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: BRAND.primarySoft,
  },
  addAttachmentText: {
    fontSize: 9,
    color: BRAND.primary,
    marginTop: 4,
    textAlign: 'center',
  },
  textArea: {
    height: 80,
    textAlignVertical: 'top',
  }
});

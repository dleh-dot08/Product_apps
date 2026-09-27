import React, { useState, useEffect } from 'react';
import { Modal, View, TouchableOpacity, TextInput, StyleSheet, ActivityIndicator, Alert, ScrollView, Image } from 'react-native';
import { Text } from '@/components/CustomText';
import { Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import api from '../../services/api';

const BRAND = {
  primary: '#0756C6',
  primarySoft: '#EAF3FF',
  white: '#FFFFFF',
  text: '#0f172a',
  border: '#e2e8f0',
  error: '#ef4444',
  muted: '#64748b',
};

interface Vehicle {
  id: string;
  name: string;
  plate_number: string;
}

interface Props {
  visible: boolean;
  onClose: () => void;
  onSuccess: () => void;
  assignedVehicle?: string;
}

export const ClockInModal: React.FC<Props> = ({ visible, onClose, onSuccess, assignedVehicle }) => {
  const [odometer, setOdometer] = useState('');
  const [fuel, setFuel] = useState('');
  const [vehicleCondition, setVehicleCondition] = useState('Baik');
  const [notes, setNotes] = useState('');
  const [photos, setPhotos] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!visible) {
      // Reset state ketika modal ditutup
      setOdometer('');
      setFuel('');
      setVehicleCondition('Baik');
      setNotes('');
      setPhotos([]);
      setError('');
    }
  }, [visible]);

  const pickImage = () => {
    if (photos.length >= 10) {
      Alert.alert('Batas Maksimal', 'Anda hanya bisa mengunggah maksimal 10 foto.');
      return;
    }

    Alert.alert('Pilih Sumber', 'Ambil dari:', [
      {
        text: 'Kamera',
        onPress: async () => {
          const perm = await ImagePicker.requestCameraPermissionsAsync();
          if (!perm.granted) { Alert.alert('Izin Ditolak', 'Akses kamera diperlukan.'); return; }
          const result = await ImagePicker.launchCameraAsync({ mediaTypes: ['images'], quality: 0.5 });
          if (!result.canceled && result.assets?.[0]?.uri) {
            setPhotos(prev => [...prev, result.assets[0].uri]);
          }
        },
      },
      {
        text: 'Galeri',
        onPress: async () => {
          const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
          if (!perm.granted) { Alert.alert('Izin Ditolak', 'Akses galeri diperlukan.'); return; }
          const result = await ImagePicker.launchImageLibraryAsync({ 
            mediaTypes: ['images'], 
            quality: 0.5,
            allowsMultipleSelection: true,
            selectionLimit: 10 - photos.length 
          });
          if (!result.canceled && result.assets?.length > 0) {
            const newUris = result.assets.map(a => a.uri);
            setPhotos(prev => [...prev, ...newUris].slice(0, 10)); // Ensure max 10
          }
        },
      },
      { text: 'Batal', style: 'cancel' },
    ]);
  };

  const removePhoto = (index: number) => {
    setPhotos(prev => prev.filter((_, i) => i !== index));
  };

  const handleClockIn = async () => {
    if (!odometer) {
      setError('Odometer awal wajib diisi');
      return;
    }

    if (!fuel) {
      setError('Bensin awal wajib diisi');
      return;
    }

    if (vehicleCondition === 'Ada Kendala' && !notes) {
      setError('Catatan kendala wajib diisi');
      return;
    }
    
    setLoading(true);
    setError('');

    try {
      const formData = new FormData();

      formData.append('start_odometer', odometer);
      formData.append('start_fuel', fuel);
      formData.append('vehicle_condition', vehicleCondition);
      
      if (notes) {
        formData.append('notes', notes);
      }

      photos.forEach((photoUri, index) => {
        const filename = photoUri.split('/').pop() || `photo_${index}.jpg`;
        const ext = filename.split('.').pop()?.toLowerCase();
        const mimeType = ext === 'png' ? 'image/png' : 'image/jpeg';
        formData.append('photos[]', { uri: photoUri, name: filename, type: mimeType } as any);
      });

      await api.post('/driver/shift/start', formData);
      onSuccess();
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal memulai shift. Pastikan backend sudah siap.');
    } finally {
      setLoading(false);
    }
  };



  return (
    <Modal visible={visible} animationType="fade" transparent>
      <View style={styles.overlay}>
        <View style={styles.container}>
          <Text style={styles.title}>Mulai Shift (Clock In)</Text>
          
          {assignedVehicle && assignedVehicle !== 'Belum Ada - -' && (
            <View style={{ backgroundColor: BRAND.primarySoft, padding: 12, borderRadius: 8, marginBottom: 16 }}>
              <Text style={{ fontFamily: 'Inter-Medium', color: BRAND.primary, fontSize: 13 }}>
                Anda ditugaskan pada kendaraan:
              </Text>
              <Text style={{ fontFamily: 'Inter-Bold', color: BRAND.primary, fontSize: 16, marginTop: 2 }}>
                {assignedVehicle}
              </Text>
            </View>
          )}

          <ScrollView showsVerticalScrollIndicator={false}>
            <View style={[styles.inputGroup, { flexDirection: 'row', gap: 12 }]}>
              <View style={{ flex: 1 }}>
                <Text style={styles.label}>Odo Awal KM *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="12500"
                  keyboardType="numeric"
                  value={odometer}
                  onChangeText={setOdometer}
                />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.label}>Bensin Awal % *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="80"
                  keyboardType="numeric"
                  value={fuel}
                  onChangeText={setFuel}
                />
              </View>
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.label}>Kondisi Kendaraan</Text>
              <View style={styles.radioGroup}>
                {['Baik', 'Ada Kendala'].map((opt) => (
                  <TouchableOpacity
                    key={opt}
                    style={[styles.radioBtn, vehicleCondition === opt && styles.radioBtnActive]}
                    onPress={() => setVehicleCondition(opt)}
                  >
                    <Text style={[styles.radioText, vehicleCondition === opt && styles.radioTextActive]}>{opt}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>

            {vehicleCondition === 'Ada Kendala' && (
              <View style={styles.inputGroup}>
                <Text style={styles.label}>Catatan Kendala <Text style={{color: BRAND.error}}>*</Text></Text>
                <TextInput
                  style={[styles.input, { height: 80, textAlignVertical: 'top' }]}
                  placeholder="Deskripsikan kendala kendaraan..."
                  multiline
                  value={notes}
                  onChangeText={setNotes}
                />
              </View>
            )}

            <View style={styles.inputGroup}>
              <Text style={styles.label}>Foto Kondisi Kendaraan (Maks 10, Opsional)</Text>
              
              {photos.length > 0 && (
                <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.photosScroll}>
                  {photos.map((uri, idx) => (
                    <View key={idx} style={styles.photoContainer}>
                      <Image source={{ uri }} style={styles.photoPreview} />
                      <TouchableOpacity style={styles.removePhotoBtn} onPress={() => removePhoto(idx)}>
                        <Ionicons name="close-circle" size={24} color={BRAND.error} />
                      </TouchableOpacity>
                    </View>
                  ))}
                  {photos.length < 10 && (
                    <TouchableOpacity style={[styles.uploadBtn, styles.uploadBtnSmall]} onPress={pickImage}>
                      <Ionicons name="add" size={28} color={BRAND.primary} />
                    </TouchableOpacity>
                  )}
                </ScrollView>
              )}

              {photos.length === 0 && (
                <TouchableOpacity style={styles.uploadBtn} onPress={pickImage}>
                  <Ionicons name="camera-outline" size={24} color={BRAND.primary} />
                  <Text style={styles.uploadBtnText}>Ambil / Pilih Foto</Text>
                </TouchableOpacity>
              )}
            </View>

            {error ? <Text style={styles.errorText}>{error}</Text> : null}
          </ScrollView>

          <View style={styles.footer}>
            <TouchableOpacity style={styles.cancelBtn} onPress={onClose} disabled={loading}>
              <Text style={styles.cancelBtnText}>Batal</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.submitBtn} onPress={handleClockIn} disabled={loading}>
              {loading ? <ActivityIndicator color={BRAND.white} /> : <Text style={styles.submitBtnText}>Clock In</Text>}
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    padding: 20,
  },
  container: {
    backgroundColor: BRAND.white,
    borderRadius: 12,
    padding: 20,
    maxHeight: '80%',
  },
  title: {
    fontSize: 18,
    fontFamily: 'Inter-Bold',
    color: BRAND.text,
    marginBottom: 20,
  },
  inputGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 14,
    fontFamily: 'Inter-Medium',
    color: BRAND.text,
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    padding: 12,
    fontFamily: 'Inter-Regular',
  },
  selectBtn: {
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    padding: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: BRAND.white,
  },
  selectBtnText: {
    fontFamily: 'Inter-Medium',
    color: BRAND.text,
  },
  placeholderText: {
    fontFamily: 'Inter-Regular',
    color: BRAND.muted,
  },
  vehicleListContainer: {
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    marginTop: 4,
    backgroundColor: BRAND.white,
    maxHeight: 150,
  },
  vehicleItem: {
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: BRAND.border,
  },
  vehicleItemActive: {
    backgroundColor: BRAND.primarySoft,
  },
  vehicleItemText: {
    fontFamily: 'Inter-Regular',
    color: BRAND.text,
  },
  vehicleItemTextActive: {
    fontFamily: 'Inter-Medium',
    color: BRAND.primary,
  },
  radioGroup: {
    flexDirection: 'row',
    gap: 12,
  },
  radioBtn: {
    flex: 1,
    padding: 12,
    borderWidth: 1,
    borderColor: BRAND.border,
    borderRadius: 8,
    alignItems: 'center',
  },
  radioBtnActive: {
    borderColor: BRAND.primary,
    backgroundColor: BRAND.primarySoft,
  },
  radioText: {
    fontFamily: 'Inter-Medium',
    color: BRAND.text,
  },
  radioTextActive: {
    color: BRAND.primary,
  },
  uploadBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 14,
    borderWidth: 1,
    borderStyle: 'dashed',
    borderColor: BRAND.primary,
    borderRadius: 8,
    backgroundColor: BRAND.primarySoft,
    gap: 8,
  },
  uploadBtnSmall: {
    width: 80,
    height: 100,
    padding: 0,
    marginLeft: 8,
  },
  uploadBtnText: {
    fontFamily: 'Inter-Medium',
    color: BRAND.primary,
  },
  photosScroll: {
    flexDirection: 'row',
  },
  photoContainer: {
    position: 'relative',
    borderRadius: 8,
    overflow: 'hidden',
    width: 80,
    height: 100,
    marginRight: 12,
  },
  photoPreview: {
    width: '100%',
    height: '100%',
    borderRadius: 8,
  },
  removePhotoBtn: {
    position: 'absolute',
    top: 4,
    right: 4,
    backgroundColor: BRAND.white,
    borderRadius: 12,
  },
  footer: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 20,
  },
  cancelBtn: {
    flex: 1,
    padding: 14,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: BRAND.border,
    alignItems: 'center',
  },
  cancelBtnText: {
    fontFamily: 'Inter-Medium',
    color: BRAND.text,
  },
  submitBtn: {
    flex: 1,
    padding: 14,
    borderRadius: 8,
    backgroundColor: BRAND.primary,
    alignItems: 'center',
  },
  submitBtnText: {
    fontFamily: 'Inter-Medium',
    color: BRAND.white,
  },
  errorText: {
    color: BRAND.error,
    fontSize: 12,
    fontFamily: 'Inter-Medium',
    marginTop: 4,
    marginBottom: 8,
  },
});

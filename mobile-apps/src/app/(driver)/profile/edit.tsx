import React, { useState } from 'react';
import { View, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Text } from '@/components/CustomText';
import { useAuth } from '../../../context/AuthContext';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

export default function EditProfileScreen() {
  const { user, updateProfile } = useAuth();
  const router = useRouter();

  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');
  const [password, setPassword] = useState('');
  const [passwordConfirm, setPasswordConfirm] = useState('');
  
  const [isLoading, setIsLoading] = useState(false);

  const handleSave = async () => {
    if (!name.trim() || !email.trim()) {
      Alert.alert('Error', 'Nama dan Email tidak boleh kosong.');
      return;
    }

    if (password && password !== passwordConfirm) {
      Alert.alert('Error', 'Password dan Konfirmasi Password tidak cocok.');
      return;
    }

    setIsLoading(true);
    try {
      await updateProfile({
        name,
        email,
        ...(password ? { password } : {})
      });
      Alert.alert('Sukses', 'Profil berhasil diperbarui.', [
        { text: 'OK', onPress: () => router.back() }
      ]);
    } catch (error: any) {
      const errMsg = error.response?.data?.message || 'Gagal memperbarui profil.';
      Alert.alert('Error', errMsg);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      {/* Blue Header Background */}
      <View style={styles.headerBackground}>
        <View style={styles.headerTop}>
          <TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
            <Ionicons name="arrow-back" size={24} color="#FFFFFF" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Edit Profil</Text>
          <View style={{ width: 24 }} />
        </View>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.formCard}>
          
          <View style={styles.inputGroup}>
            <Text style={styles.label}>Nama Lengkap</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="person-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={name}
                onChangeText={setName}
                placeholder="Masukkan nama lengkap"
                placeholderTextColor="#9CA3AF"
              />
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Email</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="mail-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={email}
                onChangeText={setEmail}
                placeholder="Masukkan email"
                keyboardType="email-address"
                autoCapitalize="none"
                placeholderTextColor="#9CA3AF"
              />
            </View>
          </View>

          <View style={styles.divider} />
          
          <Text style={styles.sectionTitle}>Ubah Password (Opsional)</Text>
          <Text style={styles.sectionSub}>Kosongkan jika tidak ingin mengubah password.</Text>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Password Baru</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="lock-closed-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={password}
                onChangeText={setPassword}
                placeholder="Masukkan password baru"
                secureTextEntry
                placeholderTextColor="#9CA3AF"
              />
            </View>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Konfirmasi Password Baru</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="shield-checkmark-outline" size={20} color="#6B7280" style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                value={passwordConfirm}
                onChangeText={setPasswordConfirm}
                placeholder="Ketik ulang password baru"
                secureTextEntry
                placeholderTextColor="#9CA3AF"
              />
            </View>
          </View>

          <TouchableOpacity 
            style={[styles.saveButton, isLoading && styles.saveButtonDisabled]} 
            onPress={handleSave}
            disabled={isLoading}
          >
            {isLoading ? (
              <ActivityIndicator color="#FFF" />
            ) : (
              <Text style={styles.saveButtonText}>Simpan Perubahan</Text>
            )}
          </TouchableOpacity>

        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  headerBackground: {
    backgroundColor: '#0756C6',
    height: 140,
    width: '100%',
    position: 'absolute',
    top: 0,
    borderBottomLeftRadius: 24,
    borderBottomRightRadius: 24,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 10,
  },
  backBtn: {
    padding: 4,
  },
  headerTitle: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: '600',
  },
  scrollContent: {
    paddingTop: 80,
    paddingBottom: 40,
  },
  formCard: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 20,
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
    borderWidth: 1,
    borderColor: '#F3F4F6',
  },
  inputGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 13,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 8,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#D1D5DB',
    borderRadius: 12,
    backgroundColor: '#F9FAFB',
    height: 48,
    paddingHorizontal: 12,
  },
  inputIcon: {
    marginRight: 10,
  },
  input: {
    flex: 1,
    fontSize: 14,
    color: '#1F2937',
  },
  divider: {
    height: 1,
    backgroundColor: '#E5E7EB',
    marginVertical: 20,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: '#1F2937',
    marginBottom: 4,
  },
  sectionSub: {
    fontSize: 12,
    color: '#6B7280',
    marginBottom: 16,
  },
  saveButton: {
    backgroundColor: '#0756C6',
    height: 48,
    borderRadius: 12,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 20,
  },
  saveButtonDisabled: {
    opacity: 0.7,
  },
  saveButtonText: {
    color: '#FFFFFF',
    fontSize: 15,
    fontWeight: '700',
  },
});

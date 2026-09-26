import axios from 'axios';
import api from './api';

export interface MinioPresignedUrlResponse {
  success: boolean;
  url?: string;
  path?: string;
  message?: string;
}

export interface MinioConfirmUploadResponse {
  success: boolean;
  message?: string;
  attachment?: any;
}

/**
 * Service untuk mengelola interaksi dengan MinIO via Backend
 */
const minioService = {
  /**
   * Mengambil Pre-signed URL dari backend
   */
  getPresignedUrl: async (fileName: string, contentType: string, deliveryNumber: string): Promise<MinioPresignedUrlResponse> => {
    try {
      const response = await api.post('/storage/presigned-url', {
        file_name: fileName,
        content_type: contentType,
        delivery_number: deliveryNumber
      });
      return response.data;
    } catch (error) {
      console.error('Error getting presigned URL:', error);
      throw error;
    }
  },

  /**
   * Mengupload file langsung ke MinIO menggunakan Pre-signed URL
   */
  uploadToMinio: async (presignedUrl: string, file: File): Promise<void> => {
    try {
      // Gunakan axios standar tanpa auth interceptors backend
      // karena kita ngehit server MinIO secara langsung
      await axios.put(presignedUrl, file, {
        headers: {
          'Content-Type': file.type
        }
      });
    } catch (error) {
      console.error('Error uploading file to MinIO:', error);
      throw error;
    }
  },

  /**
   * Konfirmasi ke backend bahwa upload berhasil, sehingga backend bisa mencatat ke database
   */
  confirmUpload: async (taskId: number, filePath: string, documentType: string): Promise<MinioConfirmUploadResponse> => {
    try {
      const response = await api.post('/storage/confirm-upload', {
        task_id: taskId,
        file_path: filePath,
        document_type: documentType
      });
      return response.data;
    } catch (error) {
      console.error('Error confirming upload:', error);
      throw error;
    }
  },

  /**
   * Alur lengkap upload: Dapatkan URL -> Upload -> Konfirmasi
   */
  uploadDocument: async (file: File, taskId: number, deliveryNumber: string, documentType: string): Promise<MinioConfirmUploadResponse> => {
    // 1. Dapatkan presigned URL
    const response = await minioService.getPresignedUrl(file.name, file.type, deliveryNumber);
    
    if (!response.success || !response.url || !response.path) {
      throw new Error(response.message || 'Gagal mendapatkan URL upload');
    }

    // 2. Upload file ke MinIO menggunakan URL tersebut
    await minioService.uploadToMinio(response.url, file);

    // 3. Beri tahu backend untuk menyimpan record ke database
    const result = await minioService.confirmUpload(taskId, response.path, documentType);
    
    return result;
  }
};

export default minioService;

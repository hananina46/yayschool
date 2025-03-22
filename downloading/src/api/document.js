import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';
const config = {
  headers: { 'content-type': 'multipart/form-data' },  // Menggunakan multipart/form-data
};

function getToken() {
  return localStorage.getItem('token');
}

// Membuat instance Axios dengan token otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${getToken()}`, // Menambahkan token ke header Authorization
  },
});

// Fungsi untuk mendapatkan daftar dokumen
export const getDocuments = async () => {
  try {
    const response = await axiosInstance.get('/documents');
    return response.data;
  } catch (error) {
    console.error('Error fetching documents:', error);
    throw error;
  }
};

// Fungsi untuk membuat dokumen baru
export const createDocument = async (documentData) => {
  try {
    const formData = new FormData(); // Membuat FormData untuk multipart/form-data
    formData.append('document_type_id', documentData.document_type_id);
    formData.append('user_id', documentData.user_id);
    formData.append('note', documentData.note);
    formData.append('file', documentData.file); // Menambahkan file ke FormData

    const response = await axiosInstance.post('/documents', formData, config);
    return response.data;
  } catch (error) {
    console.error('Error creating document:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail dokumen berdasarkan ID
export const getDocumentById = async (documentId) => {
  try {
    const response = await axiosInstance.get(`/documents/${documentId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching document with ID ${documentId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui data dokumen
export const updateDocument = async (documentId, documentData) => {
  try {
    const formData = new FormData();
    formData.append('document_type_id', documentData.document_type_id);
    formData.append('user_id', documentData.user_id);
    formData.append('note', documentData.note);
    if (documentData.file) {
      formData.append('file', documentData.file);  // Jika ada file, tambahkan
    }

    const response = await axiosInstance.put(`/documents/${documentId}`, formData, config);
    return response.data;
  } catch (error) {
    console.error(`Error updating document with ID ${documentId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus dokumen
export const deleteDocument = async (documentId) => {
  try {
    const response = await axiosInstance.delete(`/documents/${documentId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting document with ID ${documentId}:`, error);
    throw error;
  }
};

//documents/user/{userId}
// Fungsi untuk mendapatkan daftar dokumen berdasarkan user ID
export const getDocumentsByUserId = async (userId) => {
  try {
    const response = await axiosInstance.get(`/documents/user/${userId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching documents with user ID ${userId}:`, error);
    throw error;
  }
};
import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';
const config = {
  headers: { 'content-type': 'multipart/form-data' },
};

function getToken() {
  return localStorage.getItem('token');
}

// Fungsi untuk membuat instance Axios dengan token otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${getToken()}`, // Menambahkan token ke header Authorization
  },
});

// Fungsi untuk mendapatkan daftar jenis dokumen
export const getDocumentTypes = async () => {
  try {
    const response = await axiosInstance.get('/document-types');
    return response.data;
  } catch (error) {
    console.error('Error fetching document types:', error);
    throw error;
  }
};

// Fungsi untuk membuat jenis dokumen baru
export const createDocumentType = async (documentTypeData) => {
  try {
    const response = await axiosInstance.post('/document-types', documentTypeData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error('Error creating document type:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail jenis dokumen berdasarkan ID
export const getDocumentTypeById = async (documentTypeId) => {
  try {
    const response = await axiosInstance.get(`/document-types/${documentTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching document type with ID ${documentTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui data jenis dokumen
export const updateDocumentType = async (documentTypeId, updatedData) => {
  try {
    const response = await axiosInstance.put(`/document-types/${documentTypeId}`, updatedData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error(`Error updating document type with ID ${documentTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus jenis dokumen
export const deleteDocumentType = async (documentTypeId) => {
  try {
    const response = await axiosInstance.delete(`/document-types/${documentTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting document type with ID ${documentTypeId}:`, error);
    throw error;
  }
};

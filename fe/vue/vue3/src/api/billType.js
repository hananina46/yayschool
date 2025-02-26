import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';
const config = {
  headers: { 'content-type': 'multipart/form-data' },
};

function getToken() {
  return localStorage.getItem('token');
}

// Membuat instance Axios dengan token otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${getToken()}`,
  },
});

// Fungsi untuk mendapatkan daftar jenis tagihan (Bill Types)
export const getBillTypes = async () => {
  try {
    const response = await axiosInstance.get('/bill-types');
    return response.data;
  } catch (error) {
    console.error('Error fetching bill types:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail jenis tagihan berdasarkan ID
export const getBillTypeById = async (billTypeId) => {
  try {
    const response = await axiosInstance.get(`/bill-types/${billTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching bill type with ID ${billTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk membuat jenis tagihan baru
export const createBillType = async (billTypeData) => {
  try {
    const response = await axiosInstance.post('/bill-types', billTypeData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error('Error creating bill type:', error);
    throw error;
  }
};

// Fungsi untuk memperbarui jenis tagihan
export const updateBillType = async (billTypeId, billTypeData) => {
  try {
    const response = await axiosInstance.put(`/bill-types/${billTypeId}`, billTypeData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error(`Error updating bill type with ID ${billTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus jenis tagihan
export const deleteBillType = async (billTypeId) => {
  try {
    const response = await axiosInstance.delete(`/bill-types/${billTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting bill type with ID ${billTypeId}:`, error);
    throw error;
  }
};

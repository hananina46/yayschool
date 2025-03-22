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

// Fungsi untuk mendapatkan daftar jenis nilai (Grade Types)
export const getGradeTypes = async () => {
  try {
    const response = await axiosInstance.get('/grade-types');
    return response.data;
  } catch (error) {
    console.error('Error fetching grade types:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail jenis nilai berdasarkan ID
export const getGradeTypeById = async (gradeTypeId) => {
  try {
    const response = await axiosInstance.get(`/grade-types/${gradeTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching grade type with ID ${gradeTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk membuat jenis nilai baru
export const createGradeType = async (gradeTypeData) => {
  try {
    const response = await axiosInstance.post('/grade-types', gradeTypeData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error('Error creating grade type:', error);
    throw error;
  }
};

// Fungsi untuk memperbarui jenis nilai
export const updateGradeType = async (gradeTypeId, gradeTypeData) => {
  try {
    const response = await axiosInstance.put(`/grade-types/${gradeTypeId}`, gradeTypeData, {
      headers: { 'Content-Type': 'application/json' },
    });
    return response.data;
  } catch (error) {
    console.error(`Error updating grade type with ID ${gradeTypeId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus jenis nilai
export const deleteGradeType = async (gradeTypeId) => {
  try {
    const response = await axiosInstance.delete(`/grade-types/${gradeTypeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting grade type with ID ${gradeTypeId}:`, error);
    throw error;
  }
};

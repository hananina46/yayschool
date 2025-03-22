import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';

function getToken() {
  return localStorage.getItem('token');
}

// Membuat instance Axios dengan otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${getToken()}`,
  },
});

// Fungsi untuk mendapatkan daftar ekstrakurikuler (with supervisor)
export const getExtracurriculars = async () => {
  try {
    const response = await axiosInstance.get('/extracurricular-names');
    return response.data;
  } catch (error) {
    console.error('Error fetching extracurricular list:', error);
    throw error;
  }
};

// Fungsi untuk menambahkan ekstrakurikuler baru
export const createExtracurricular = async (extracurricularData) => {
  try {
    const response = await axiosInstance.post('/extracurricular-names', extracurricularData);
    return response.data;
  } catch (error) {
    console.error('Error creating extracurricular:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail ekstrakurikuler berdasarkan ID
export const getExtracurricularById = async (extracurricularId) => {
  try {
    const response = await axiosInstance.get(`/extracurricular-names/${extracurricularId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching extracurricular with ID ${extracurricularId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui ekstrakurikuler
export const updateExtracurricular = async (extracurricularId, updatedData) => {
  try {
    const response = await axiosInstance.put(`/extracurricular-names/${extracurricularId}`, updatedData);
    return response.data;
  } catch (error) {
    console.error(`Error updating extracurricular with ID ${extracurricularId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus ekstrakurikuler
export const deleteExtracurricular = async (extracurricularId) => {
  try {
    const response = await axiosInstance.delete(`/extracurricular-names/${extracurricularId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting extracurricular with ID ${extracurricularId}:`, error);
    throw error;
  }
};
 
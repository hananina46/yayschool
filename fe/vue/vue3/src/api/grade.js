import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';
const config = {
  headers: { 'content-type': 'application/json' },
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

// Fungsi untuk mendapatkan daftar nilai (grades)
export const getGrades = async () => {
  try {
    const response = await axiosInstance.get('/grades');
    return response.data;
  } catch (error) {
    console.error('Error fetching grades:', error);
    throw error;
  }
};

// Fungsi untuk membuat data nilai baru
export const createGrade = async (gradeData) => {
  try {
    const response = await axiosInstance.post('/grades', gradeData, config);
    return response.data;
  } catch (error) {
    console.error('Error creating grade:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail nilai berdasarkan ID
export const getGradeById = async (gradeId) => {
  try {
    const response = await axiosInstance.get(`/grades/${gradeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching grade with ID ${gradeId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui data nilai
export const updateGrade = async (gradeId, gradeData) => {
  try {
    const response = await axiosInstance.put(`/grades/${gradeId}`, gradeData, config);
    return response.data;
  } catch (error) {
    console.error(`Error updating grade with ID ${gradeId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus nilai
export const deleteGrade = async (gradeId) => {
  try {
    const response = await axiosInstance.delete(`/grades/${gradeId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting grade with ID ${gradeId}:`, error);
    throw error;
  }
};

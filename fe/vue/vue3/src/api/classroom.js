import axios from 'axios'

const API_URL = import.meta.env.VITE_BASE_URL + '/api'
const config = {
  headers: { 'content-type': 'multipart/form-data' },
}
function getToken() {
  return localStorage.getItem('token')
}

// Fungsi untuk membuat instance Axios dengan token otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${getToken()}`, // Menambahkan token ke header Authorization
  },
})

// Fungsi untuk mendapatkan daftar kelas
export const getSchoolClasses = async () => {
  try {
    const response = await axiosInstance.get('/school-classes');
    return response.data;
  } catch (error) {
    console.error('Error fetching school classes:', error);
    throw error;
  }
};

// Fungsi untuk membuat kelas baru
export const createSchoolClass = async (classData) => {
  try {
    const response = await axiosInstance.post('/school-classes', classData, config);
    return response.data;
  } catch (error) {
    console.error('Error creating school class:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail kelas tertentu
export const getSchoolClassById = async (classId) => {
  try {
    const response = await axiosInstance.get(`/school-classes/${classId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching class with ID ${classId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui data kelas
export const updateSchoolClass = async (classId, updatedData) => {
  try {
    const response = await axiosInstance.put(`/school-classes/${classId}`, updatedData, config);
    return response.data;
  } catch (error) {
    console.error(`Error updating class with ID ${classId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus kelas
export const deleteSchoolClass = async (classId) => {
  try {
    const response = await axiosInstance.delete(`/school-classes/${classId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting class with ID ${classId}:`, error);
    throw error;
  }
};

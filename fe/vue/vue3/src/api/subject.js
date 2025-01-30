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

// Fungsi untuk mendapatkan daftar mata pelajaran
export const getSubjects = async () => {
    try {
      const response = await axiosInstance.get('/subjects');
      return response.data;
    } catch (error) {
      console.error('Error fetching subjects:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail mata pelajaran berdasarkan ID
  export const getSubjectById = async (subjectId) => {
    try {
      const response = await axiosInstance.get(`/subjects/${subjectId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching subject with ID ${subjectId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat mata pelajaran baru
  export const createSubject = async (subjectData) => {
    try {
      const response = await axiosInstance.post('/subjects', subjectData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating subject:', error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data mata pelajaran
  export const updateSubject = async (subjectId, subjectData) => {
    try {
      const response = await axiosInstance.put(`/subjects/${subjectId}`, subjectData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating subject with ID ${subjectId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus mata pelajaran
  export const deleteSubject = async (subjectId) => {
    try {
      const response = await axiosInstance.delete(`/subjects/${subjectId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting subject with ID ${subjectId}:`, error);
      throw error;
    }
  };
  
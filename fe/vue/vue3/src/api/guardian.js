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

// Fungsi untuk mendapatkan daftar wali (guardian)
export const getGuardians = async () => {
    try {
      const response = await axiosInstance.get('/guardians');
      return response.data;
    } catch (error) {
      console.error('Error fetching guardians:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail wali berdasarkan ID
  export const getGuardianById = async (guardianId) => {
    try {
      const response = await axiosInstance.get(`/guardians/${guardianId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching guardian with ID ${guardianId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat data wali baru
  export const createGuardian = async (guardianData) => {
    try {
      const formData = new FormData();
      formData.append('name', guardianData.name);
      formData.append('email', guardianData.email);
      if (guardianData.phone) formData.append('phone', guardianData.phone);
      if (guardianData.address) formData.append('address', guardianData.address);
      
      // Menambahkan daftar ID siswa (jika ada)
      if (guardianData.student_ids && guardianData.student_ids.length > 0) {
        guardianData.student_ids.forEach((id, index) => {
          formData.append(`student_ids[${index}]`, id);
        });
      }
  
      const response = await axiosInstance.post('/guardians', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating guardian:', error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data wali
  export const updateGuardian = async (guardianId, guardianData) => {
    try {
      const formData = new FormData();
      formData.append('name', guardianData.name);
      formData.append('email', guardianData.email);
      if (guardianData.phone) formData.append('phone', guardianData.phone);
      if (guardianData.address) formData.append('address', guardianData.address);
      
      // Menambahkan daftar ID siswa (jika ada)
      if (guardianData.student_ids && guardianData.student_ids.length > 0) {
        guardianData.student_ids.forEach((id, index) => {
          formData.append(`student_ids[${index}]`, id);
        });
      }
  
      const response = await axiosInstance.post(`/guardians/${guardianId}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating guardian with ID ${guardianId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus wali
  export const deleteGuardian = async (guardianId) => {
    try {
      const response = await axiosInstance.delete(`/guardians/${guardianId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting guardian with ID ${guardianId}:`, error);
      throw error;
    }
  };
  
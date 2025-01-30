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

// Fungsi untuk mendapatkan daftar guru
export const getTeachers = async () => {
    try {
      const response = await axiosInstance.get('/teachers');
      return response.data;
    } catch (error) {
      console.error('Error fetching teachers:', error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat data guru baru
  export const createTeacher = async (teacherData) => {
    try {
      const formData = new FormData();
      formData.append('name', teacherData.name);
      formData.append('email', teacherData.email);
      if (teacherData.phone) formData.append('phone', teacherData.phone);
      if (teacherData.address) formData.append('address', teacherData.address);
      if (teacherData.profile_photo) {
        formData.append('profile_photo', teacherData.profile_photo);
      }
  
      const response = await axiosInstance.post('/teachers', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating teacher:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail guru berdasarkan ID
  export const getTeacherById = async (teacherId) => {
    try {
      const response = await axiosInstance.get(`/teachers/${teacherId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching teacher with ID ${teacherId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data guru
  export const updateTeacher = async (teacherId, teacherData) => {
    try {
      const formData = new FormData();
      formData.append('name', teacherData.name);
      formData.append('email', teacherData.email);
      if (teacherData.phone) formData.append('phone', teacherData.phone);
      if (teacherData.address) formData.append('address', teacherData.address);
      if (teacherData.profile_photo) {
        formData.append('profile_photo', teacherData.profile_photo);
      }
  
      const response = await axiosInstance.post(`/teachers/${teacherId}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating teacher with ID ${teacherId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus guru
  export const deleteTeacher = async (teacherId) => {
    try {
      const response = await axiosInstance.delete(`/teachers/${teacherId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting teacher with ID ${teacherId}:`, error);
      throw error;
    }
  };
  
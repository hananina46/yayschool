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

// Fungsi untuk mendapatkan daftar siswa
export const getStudents = async () => {
    try {
      const response = await axiosInstance.get('/students');
      return response.data;
    } catch (error) {
      console.error('Error fetching students:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail siswa berdasarkan ID
  export const getStudentById = async (studentId) => {
    try {
      const response = await axiosInstance.get(`/students/${studentId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching student with ID ${studentId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat data siswa baru
  export const createStudent = async (studentData) => {
    try {
      const formData = new FormData();
      formData.append('name', studentData.name);
      formData.append('email', studentData.email);
      if (studentData.class_id) formData.append('class_id', studentData.class_id);
      if (studentData.nisn) formData.append('nisn', studentData.nisn);
      if (studentData.dob) formData.append('dob', studentData.dob);
      if (studentData.gender) formData.append('gender', studentData.gender);
      if (studentData.phone) formData.append('phone', studentData.phone);
      if (studentData.address) formData.append('address', studentData.address);
      if (studentData.profile_photo) {
        formData.append('profile_photo', studentData.profile_photo); // Upload file foto
      }
  
      const response = await axiosInstance.post('/students', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating student:', error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data siswa
  export const updateStudent = async (studentId, studentData) => {
    try {
      const formData = new FormData();
      formData.append('name', studentData.name);
      formData.append('email', studentData.email);
      if (studentData.class_id) formData.append('class_id', studentData.class_id);
      if (studentData.nisn) formData.append('nisn', studentData.nisn);
      if (studentData.dob) formData.append('dob', studentData.dob);
      if (studentData.gender) formData.append('gender', studentData.gender);
      if (studentData.phone) formData.append('phone', studentData.phone);
      if (studentData.address) formData.append('address', studentData.address);
      if (studentData.profile_photo) {
        formData.append('profile_photo', studentData.profile_photo); // Update file foto
      }
  
      const response = await axiosInstance.post(`/students/${studentId}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating student with ID ${studentId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus siswa
  export const deleteStudent = async (studentId) => {
    try {
      const response = await axiosInstance.delete(`/students/${studentId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting student with ID ${studentId}:`, error);
      throw error;
    }
  };
  
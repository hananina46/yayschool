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

// Fungsi untuk mendapatkan daftar tahun akademik
export const getAcademicYears = async () => {
    try {
      const response = await axiosInstance.get('/academic-years');
      return response.data;
    } catch (error) {
      console.error('Error fetching academic years:', error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat tahun akademik baru
  export const createAcademicYear = async (academicYearData) => {
    try {
      const response = await axiosInstance.post('/academic-years', academicYearData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating academic year:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail tahun akademik berdasarkan ID
  export const getAcademicYearById = async (academicYearId) => {
    try {
      const response = await axiosInstance.get(`/academic-years/${academicYearId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching academic year with ID ${academicYearId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data tahun akademik
  export const updateAcademicYear = async (academicYearId, updatedData) => {
    try {
      const response = await axiosInstance.put(`/academic-years/${academicYearId}`, updatedData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating academic year with ID ${academicYearId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus tahun akademik
  export const deleteAcademicYear = async (academicYearId) => {
    try {
      const response = await axiosInstance.delete(`/academic-years/${academicYearId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting academic year with ID ${academicYearId}:`, error);
      throw error;
    }
  };
  
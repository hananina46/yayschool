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

// Fungsi untuk mendapatkan daftar jadwal pelajaran
export const getSchedules = async () => {
    try {
      const response = await axiosInstance.get('/schedules');
      return response.data;
    } catch (error) {
      console.error('Error fetching schedules:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail jadwal berdasarkan ID
  export const getScheduleById = async (scheduleId) => {
    try {
      const response = await axiosInstance.get(`/schedules/${scheduleId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching schedule with ID ${scheduleId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk membuat jadwal pelajaran baru
  export const createSchedule = async (scheduleData) => {
    try {
      const response = await axiosInstance.post('/schedules', scheduleData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating schedule:', error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data jadwal pelajaran
  export const updateSchedule = async (scheduleId, scheduleData) => {
    try {
      const response = await axiosInstance.put(`/schedules/${scheduleId}`, scheduleData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating schedule with ID ${scheduleId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus jadwal pelajaran
  export const deleteSchedule = async (scheduleId) => {
    try {
      const response = await axiosInstance.delete(`/schedules/${scheduleId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting schedule with ID ${scheduleId}:`, error);
      throw error;
    }
  };
  
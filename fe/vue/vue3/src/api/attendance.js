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

// Fungsi untuk mendapatkan daftar kehadiran
export const getAttendances = async () => {
    try {
      const response = await axiosInstance.get('/attendances');
      return response.data;
    } catch (error) {
      console.error('Error fetching attendances:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan detail kehadiran berdasarkan ID
  export const getAttendanceById = async (attendanceId) => {
    try {
      const response = await axiosInstance.get(`/attendances/${attendanceId}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching attendance with ID ${attendanceId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk mencatat kehadiran baru
  export const createAttendance = async (attendanceData) => {
    try {
      const response = await axiosInstance.post('/attendances', attendanceData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error('Error creating attendance:', error);
      throw error;
    }
  };
  
  // Fungsi untuk memperbarui data kehadiran
  export const updateAttendance = async (attendanceId, attendanceData) => {
    try {
      const response = await axiosInstance.put(`/attendances/${attendanceId}`, attendanceData, {
        headers: { 'Content-Type': 'application/json' },
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating attendance with ID ${attendanceId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk menghapus data kehadiran
  export const deleteAttendance = async (attendanceId) => {
    try {
      const response = await axiosInstance.delete(`/attendances/${attendanceId}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting attendance with ID ${attendanceId}:`, error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan kehadiran berdasarkan kelas dan tanggal
  export const getAttendanceByClass = async (classId, date) => {
    try {
      const response = await axiosInstance.get('/attendance/by-class', {
        params: { class_id: classId, date: date },
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching attendance by class:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan kehadiran berdasarkan periode waktu
  export const getAttendanceByPeriod = async (classId, startDate, endDate) => {
    try {
      const response = await axiosInstance.get('/attendance/by-period', {
        params: { class_id: classId, start_date: startDate, end_date: endDate },
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching attendance by period:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan ringkasan kehadiran harian
  export const getDailyAttendanceSummary = async (date) => {
    try {
      const response = await axiosInstance.get('/attendance/daily-summary', {
        params: { date: date },
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching daily attendance summary:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan ringkasan kehadiran siswa berdasarkan periode
  export const getStudentAttendanceSummary = async (studentId, startDate, endDate) => {
    try {
      const response = await axiosInstance.get('/attendance/student-summary', {
        params: { student_id: studentId, start_date: startDate, end_date: endDate },
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching student attendance summary:', error);
      throw error;
    }
  };
  
  // Fungsi untuk mendapatkan ringkasan kehadiran guru berdasarkan periode
  export const getTeacherAttendanceSummary = async (teacherId, startDate, endDate) => {
    try {
      const response = await axiosInstance.get('/attendance/teacher-summary', {
        params: { teacher_id: teacherId, start_date: startDate, end_date: endDate },
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching teacher attendance summary:', error);
      throw error;
    }
  };
  
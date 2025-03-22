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

// Fungsi untuk mendapatkan daftar anggota ekstrakurikuler berdasarkan tenant
export const getExtracurricularMembers = async () => {
  try {
    const response = await axiosInstance.get('/extracurricular-members');
    return response.data;
  } catch (error) {
    console.error('Error fetching extracurricular members:', error);
    throw error;
  }
};

// Fungsi untuk menambahkan siswa ke ekstrakurikuler
export const createExtracurricularMember = async (memberData) => {
  try {
    const response = await axiosInstance.post('/extracurricular-members', memberData);
    return response.data;
  } catch (error) {
    console.error('Error adding extracurricular member:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail anggota ekstrakurikuler berdasarkan ID
export const getExtracurricularMemberById = async (memberId) => {
  try {
    const response = await axiosInstance.get(`/extracurricular-members/${memberId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching extracurricular member with ID ${memberId}:`, error);
    throw error;
  }
};

// Fungsi untuk mendapatkan anggota ekstrakurikuler berdasarkan ID ekstrakurikuler
export const getMembersByExtracurricular = async (extracurricularId) => {
  try {
    const response = await axiosInstance.get(`/extracurricular-members/extracurricular/${extracurricularId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching members for extracurricular ID ${extracurricularId}:`, error);
    throw error;
  }
};

// Fungsi untuk memperbarui anggota ekstrakurikuler
export const updateExtracurricularMember = async (memberId, updatedData) => {
  try {
    const response = await axiosInstance.put(`/extracurricular-members/${memberId}`, updatedData);
    return response.data;
  } catch (error) {
    console.error(`Error updating extracurricular member with ID ${memberId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus anggota dari ekstrakurikuler
export const deleteExtracurricularMember = async (memberId) => {
  try {
    const response = await axiosInstance.delete(`/extracurricular-members/${memberId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting extracurricular member with ID ${memberId}:`, error);
    throw error;
  }
};

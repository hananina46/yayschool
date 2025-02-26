import axios from 'axios';

const API_URL = import.meta.env.VITE_BASE_URL + '/api';

function getToken() {
  return localStorage.getItem('token');
}

// Membuat instance Axios dengan token otentikasi
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    Authorization: `Bearer ${getToken()}`,
  },
});

// Fungsi untuk mendapatkan daftar Assigned Bills
export const getAssignedBills = async () => {
  try {
    const response = await axiosInstance.get('/assigned-bills');
    return response.data;
  } catch (error) {
    console.error('Error fetching assigned bills:', error);
    throw error;
  }
};

// Fungsi untuk mendapatkan detail Assigned Bill berdasarkan ID
export const getAssignedBillById = async (assignedBillId) => {
  try {
    const response = await axiosInstance.get(`/assigned-bills/${assignedBillId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching assigned bill with ID ${assignedBillId}:`, error);
    throw error;
  }
};

// Fungsi untuk membuat Assigned Bill baru dengan Upload Gambar
export const createAssignedBill = async (assignedBillData) => {
  try {
    const formData = new FormData();
    formData.append('student_id', assignedBillData.student_id);
    formData.append('academic_year_id', assignedBillData.academic_year_id);
    formData.append('bill_type_id', assignedBillData.bill_type_id);
    formData.append('status', assignedBillData.status);
    formData.append('discount', assignedBillData.discount);
    formData.append('note', assignedBillData.note || '');
    formData.append('payment_method', assignedBillData.payment_method || '');
    formData.append('payment_url', assignedBillData.payment_url || '');
    
    if (assignedBillData.payment_proof) {
      formData.append('payment_proof', assignedBillData.payment_proof); // File upload
    }

    const response = await axiosInstance.post('/assigned-bills', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  } catch (error) {
    console.error('Error creating assigned bill:', error);
    throw error;
  }
};

// Fungsi untuk memperbarui Assigned Bill dengan Upload Gambar
export const updateAssignedBill = async (assignedBillId, assignedBillData) => {
  try {
    const formData = new FormData();
    formData.append('student_id', assignedBillData.student_id);
    formData.append('academic_year_id', assignedBillData.academic_year_id);
    formData.append('bill_type_id', assignedBillData.bill_type_id);
    formData.append('status', assignedBillData.status);
    formData.append('discount', assignedBillData.discount);
    formData.append('note', assignedBillData.note || '');
    formData.append('payment_method', assignedBillData.payment_method || '');
    formData.append('payment_url', assignedBillData.payment_url || '');
    
    if (assignedBillData.payment_proof) {
      formData.append('payment_proof', assignedBillData.payment_proof); // File upload
    }

    const response = await axiosInstance.post(`/assigned-bills/${assignedBillId}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  } catch (error) {
    console.error(`Error updating assigned bill with ID ${assignedBillId}:`, error);
    throw error;
  }
};

// Fungsi untuk menghapus Assigned Bill
export const deleteAssignedBill = async (assignedBillId) => {
  try {
    const response = await axiosInstance.delete(`/assigned-bills/${assignedBillId}`);
    return response.data;
  } catch (error) {
    console.error(`Error deleting assigned bill with ID ${assignedBillId}:`, error);
    throw error;
  }
};

// Fungsi untuk mendapatkan Assigned Bills berdasarkan Student ID
export const getAssignedBillByStudentId = async (studentId) => {
  try {
    const response = await axiosInstance.get(`/assigned-bills/student/${studentId}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching assigned bill with student ID ${studentId}:`, error);
    throw error;
  }
};

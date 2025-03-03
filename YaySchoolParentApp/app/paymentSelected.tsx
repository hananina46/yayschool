import React, { useEffect, useState } from 'react';
import { 
  View, Text, Image, ActivityIndicator, Alert, StyleSheet, ScrollView , Modal, TouchableOpacity 
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { Stack, useLocalSearchParams } from 'expo-router';

const BASE_URL = 'https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/api/parent_area/student';

interface Student {
  id: number;
  name: string;
  nisn: string;
  profile_photo: string;
  class: {
    name: string;
    academic_year: {
      name: string;
    };
  };
  bills: {
    id: number;
    status: string;
    discount: number;
    note: string | null;
    bill_type: { name: string; amount: number };
  }[];
}

export default function PaymentDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const [student, setStudent] = useState<Student | null>(null);
  const [loading, setLoading] = useState(true);
  const [modalVisible, setModalVisible] = useState(false);
const [selectedBill, setSelectedBill] = useState<Student["bills"][0] | null>(null);


  useEffect(() => {
    const fetchStudent = async () => {
      try {
        const token = await AsyncStorage.getItem('token');
        if (!token) {
          Alert.alert('Error', 'Token not found.');
          return;
        }

        const response = await axios.get(`${BASE_URL}/${id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        setStudent(response.data);
      } catch (error) {
        console.error('Error fetching student details:', error);
        Alert.alert('Failed to fetch data', 'Please try again later.');
      } finally {
        setLoading(false);
      }
    };

    fetchStudent();
  }, [id]);

  if (loading) {
    return <ActivityIndicator size="large" color="#FF1493" />;
  }

  if (!student) {
    return <Text style={styles.errorText}>Data not found.</Text>;
  }

  return (
    <>
      <Stack.Screen options={{ title: 'Payment Details' }} />

      <LinearGradient colors={['#FFF', '#E3F2FD']} style={styles.container}>
        <ScrollView contentContainerStyle={styles.scrollContainer}>
          {/* Student Details */}
          <View style={styles.studentCard}>
            <Image 
              source={{ uri: `https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/storage/${student.profile_photo}` }} 
              style={styles.profilePhoto} 
            />
            <Text style={styles.name}>{student.name}</Text>
            <Text style={styles.info}>NISN: {student.nisn}</Text>
            <Text style={styles.info}>Class: {student.class.name}</Text>
            <Text style={styles.info}>Academic Year: {student.class.academic_year.name}</Text>
          </View>

          {/* Payment Table */}
          <View style={styles.tableContainer}>
            <Text style={styles.sectionTitle}>Payment History</Text>
            <View style={styles.table}>
              {/* Table Header */}
              <View style={[styles.row, styles.headerRow]}>
                <Text style={[styles.cell, styles.headerText, styles.flex2]}>Type</Text>
                <Text style={[styles.cell, styles.headerText, styles.flex1]}>Amount</Text>
                <Text style={[styles.cell, styles.headerText, styles.flex1]}>Status</Text>
              </View>

              {/* Table Data */}
              {student.bills.length > 0 ? (
                student.bills.map((bill, index) => (
                    <TouchableOpacity 
                    key={index} 
                    style={styles.row} 
                    onPress={() => {
                      setSelectedBill(bill);
                      setModalVisible(true);
                    }}
                  >
                    <Text style={[styles.cell, styles.flex2]} numberOfLines={1} ellipsizeMode="tail">
                      {bill.bill_type.name}
                    </Text>
                    <Text style={[styles.cell, styles.flex2]}>
                      Rp {bill.bill_type.amount.toLocaleString()}
                    </Text>
                    <Text style={[styles.cell, styles.flex1, bill.status === 'pending' ? styles.statusPending : styles.statusPaid]}>
                      {bill.status === 'pending' ? 'Unpaid' : 'Paid'}
                    </Text>
                  </TouchableOpacity>
                  
                ))
              ) : (
                <Text style={styles.emptyText}>No payment records available.</Text>
              )}
            </View>
          </View>
          <Modal
  animationType="fade"
  transparent={true}
  visible={modalVisible}
  onRequestClose={() => setModalVisible(false)}
>
  <View style={styles.modalOverlay}>
    <View style={styles.modalContainer}>
      <Text style={styles.modalTitle}>Payment Details</Text>
      
      {selectedBill && (
        <View style={styles.modalContent}>
          <View style={styles.modalRow}>
            <Text style={styles.modalLabel}>Type</Text>
            <Text style={styles.modalValue}>{selectedBill.bill_type.name}</Text>
          </View>
          <View style={styles.modalRow}>
            <Text style={styles.modalLabel}>Amount</Text>
            <Text style={styles.modalValue}>Rp {selectedBill.bill_type.amount.toLocaleString()}</Text>
          </View>
            <View style={styles.modalRow}>
                <Text style={styles.modalLabel}>Bill Created at</Text>
                <Text style={styles.modalValue}>
                    {selectedBill.created_at}
                </Text>
            </View>
          <View style={styles.modalRow}>
            <Text style={styles.modalLabel}>Discount</Text>
            <Text style={styles.modalValue}>
              {selectedBill.discount > 0 ? `Rp ${selectedBill.discount.toLocaleString()}` : '-'}
            </Text>
          </View>
          <View style={styles.modalRow}>
            <Text style={styles.modalLabel}>Status</Text>
            <Text style={[styles.modalValue, selectedBill.status === 'pending' ? styles.statusPending : styles.statusPaid]}>
              {selectedBill.status === 'pending' ? 'Unpaid' : 'Paid'}
            </Text>
          </View>
          <View style={styles.modalRow}>
            <Text style={styles.modalLabel}>Notes</Text>
            <Text style={styles.modalValue}>{selectedBill.note ? selectedBill.note : '-'}</Text>
          </View>
        </View>
      )}

      {/* Close Button */}
      <TouchableOpacity onPress={() => setModalVisible(false)} style={styles.modalCloseButton}>
        <Text style={styles.modalCloseText}>Close</Text>
      </TouchableOpacity>
    </View>
  </View>
</Modal>


        </ScrollView>
      </LinearGradient>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  scrollContainer: { alignItems: 'center', paddingBottom: 20 },
  
  // Student Card
  studentCard: { 
    alignItems: 'center', 
    backgroundColor: '#E3F2FD', 
    padding: 20, 
    borderRadius: 10, 
    width: '100%', 
    marginBottom: 20 
  },
  profilePhoto: { width: 100, height: 100, borderRadius: 50, marginBottom: 10 },
  name: { fontSize: 22, fontWeight: 'bold', color: '#1976D2', marginBottom: 5 },
  info: { fontSize: 16, color: '#333' },

  // Table Styling
  tableContainer: { width: '100%', marginBottom: 20 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#0D47A1', marginBottom: 5 },
  table: { backgroundColor: '#FFF', borderRadius: 10, overflow: 'hidden', borderWidth: 1, borderColor: '#BBDEFB' },
  
  // Table Rows
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#BBDEFB',
  },
  headerRow: { backgroundColor: '#90CAF9' },
  cell: { fontSize: 14, color: '#333', textAlign: 'left', flexShrink: 1 },
  headerText: { fontWeight: 'bold', color: '#0D47A1' },
  
  // Column Flexibility
  flex2: { flex: 2 }, // Lebih lebar untuk Type agar tidak turun ke bawah
  flex1: { flex: 1 }, // Lebih sempit untuk Amount dan Status agar rapi

  // Status Styling
  statusPending: { color: '#D32F2F', fontWeight: 'bold' },
  statusPaid: { color: '#388E3C', fontWeight: 'bold' },

  emptyText: { fontSize: 13, color: '#666', textAlign: 'center', marginVertical: 10 },
  errorText: { fontSize: 18, color: 'red', textAlign: 'center', marginTop: 20 },
  modalOverlay: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  modalContainer: {
    width: '90%', // Modal lebih lebar
    backgroundColor: '#FFF',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 6,
  },
  modalTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#1976D2',
    marginBottom: 15,
    textAlign: 'center',
  },
  modalContent: {
    width: '100%',
    paddingVertical: 10,
  },
  modalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
  },
  modalLabel: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    width: '40%',
    textAlign: 'left',
  },
  modalValue: {
    fontSize: 16,
    color: '#333',
    width: '60%',
    textAlign: 'right',
  },
  modalCloseButton: {
    marginTop: 20,
    backgroundColor: '#1976D2',
    paddingVertical: 12,
    paddingHorizontal: 40,
    borderRadius: 8,
  },
  modalCloseText: {
    color: '#FFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  
  // Status Styling
});

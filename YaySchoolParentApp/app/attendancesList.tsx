import React, { useEffect, useState } from 'react';
import { 
  View, Text, FlatList, ActivityIndicator, Alert, StyleSheet, Dimensions, TouchableOpacity, Modal, TextInput 
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useLocalSearchParams, Stack } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';
import DateTimePicker from '@react-native-community/datetimepicker';


const { width } = Dimensions.get('window');
const BASE_URL = 'https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/api/parent_area/presence';

interface Attendance {
  id: number;
  date: string;
  status: string;
  notes: string | null;
  schedule: {
    description: string;
    subject: { name: string };
    class: { name: string };
  };
  teacher: { name: string };
}

export default function AttendancesList() {
  const { id } = useLocalSearchParams();
  const [loading, setLoading] = useState(true);
  const [attendances, setAttendances] = useState<Attendance[]>([]);
  const [filteredAttendances, setFilteredAttendances] = useState<Attendance[]>([]);
  const [selectedAttendance, setSelectedAttendance] = useState<Attendance | null>(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [isPickingStartDate, setIsPickingStartDate] = useState(true);
  const [startDate, setStartDate] = useState(new Date());
  const [endDate, setEndDate] = useState(new Date());
  const [studentData, setStudentData] = useState(null);
  const [loadingStudent, setLoadingStudent] = useState(true);

  
  



  useEffect(() => {
    const fetchData = async () => {
      try {
        const token = await AsyncStorage.getItem('token');
        if (!token) throw new Error('Token tidak ditemukan');

        const response = await axios.get(`${BASE_URL}/${id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        setAttendances(response.data);
        setFilteredAttendances(response.data.slice(0, itemsPerPage));
      } catch (error) {
        console.error('Error fetching data:', error);
        Alert.alert('Gagal mengambil data', 'Silakan coba lagi nanti.');
      } finally {
        setLoading(false);
      }
    };

    const fetchStudentData = async () => {
        try {
          const token = await AsyncStorage.getItem('token');
          if (!token) throw new Error('Token tidak ditemukan');
    
          const response = await axios.get(
            `https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/api/parent_area/student/${id}`,
            {
              headers: { Authorization: `Bearer ${token}` },
            }
          );
    
          if (response.data) {
            setStudentData(response.data); // Langsung set objek, bukan array
          }
        } catch (error) {
          console.error('Error fetching student data:', error);
        } finally {
          setLoadingStudent(false);
        }
      };
    
      fetchStudentData();

    fetchData();

  }, [id]);

  const openModal = (attendance: Attendance) => {
    setSelectedAttendance(attendance);
    setModalVisible(true);
  };

  const applyFilter = () => {
    const formattedStartDate = startDate.toISOString().split('T')[0];
    const formattedEndDate = endDate.toISOString().split('T')[0];
  
    const filtered = attendances.filter(item => 
      item.date >= formattedStartDate && item.date <= formattedEndDate
    );
    setFilteredAttendances(filtered.slice(0, itemsPerPage));
    setCurrentPage(1);
  };
  
  

  const totalPages = Math.ceil(attendances.length / itemsPerPage);

  return (
    <>
      <Stack.Screen options={{ title: 'Attendance Records' }} />
      <LinearGradient colors={['#FFF', '#FFE4E1']} style={styles.container}>
        {loadingStudent ? (
  <ActivityIndicator size="large" color="#FF1493" />
) : studentData ? (
  <View style={styles.studentCard}>
    <Text style={styles.studentName}>{studentData.name}</Text>
    <Text style={styles.studentDetail}>
      <Text style={styles.bold}>Number:</Text> {studentData.nisn}
    </Text>
    <Text style={styles.studentDetail}>
      <Text style={styles.bold}>Birthdate:</Text> {studentData.dob}
    </Text>
    <Text style={styles.studentDetail}>
      <Text style={styles.bold}>Gender:</Text> {studentData.gender === 'F' ? 'Perempuan' : 'Laki-laki'}
    </Text>

  </View>
) : (
  <Text style={styles.errorText}>Data siswa tidak ditemukan</Text>
)}



        {/* Filter Tanggal */}
        <View style={styles.filterContainer}>
            <TouchableOpacity onPress={() => { setShowDatePicker(true); setIsPickingStartDate(true); }} style={styles.dateButton}>
                <Text style={styles.dateText}>
                {startDate.toISOString().split('T')[0]} - {endDate.toISOString().split('T')[0]}
                </Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.filterButton} onPress={applyFilter}>
                <Text style={styles.filterText}>Filter</Text>
            </TouchableOpacity>
        </View>



        {loading ? (
          <ActivityIndicator size="large" color="#FF1493" />
        ) : (
          <>
            <FlatList
              data={filteredAttendances}
              keyExtractor={(item) => item.id.toString()}
              renderItem={({ item }) => (
                <TouchableOpacity style={styles.listItem} onPress={() => openModal(item)}>
                  <View>
                    <Text style={styles.dateText}>{item.date}</Text>
                    <Text style={[styles.statusText, item.status === 'present' ? styles.present : styles.absent]}>
                      {item.status}
                    </Text>
                  </View>
                </TouchableOpacity>
              )}
            />

            {/* Pagination */}
            <View style={styles.pagination}>
              <TouchableOpacity 
                disabled={currentPage === 1} 
                onPress={() => setCurrentPage(currentPage - 1)} 
                style={[styles.pageButton, currentPage === 1 && styles.disabledButton]}
              >
                <Text style={styles.pageText}>Previous</Text>
              </TouchableOpacity>

              <Text style={styles.pageNumber}>Page {currentPage} of {totalPages}</Text>

              <TouchableOpacity 
                disabled={currentPage === totalPages} 
                onPress={() => setCurrentPage(currentPage + 1)} 
                style={[styles.pageButton, currentPage === totalPages && styles.disabledButton]}
              >
                <Text style={styles.pageText}>Next</Text>
              </TouchableOpacity>
            </View>
          </>
        )}

        {/* Modal untuk Detail */}
        <Modal visible={modalVisible} transparent={true} animationType="slide">
          <View style={styles.modalContainer}>
            <View style={styles.modalContent}>
              {selectedAttendance && (
                <>
                  <Text style={styles.modalTitle}>Attendance Detail</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Date:</Text> {selectedAttendance.date}</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Status:</Text> {selectedAttendance.status}</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Subject:</Text> {selectedAttendance.schedule.subject.name}</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Class:</Text> {selectedAttendance.schedule.class.name}</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Teacher:</Text> {selectedAttendance.teacher.name}</Text>
                  <Text style={styles.modalText}><Text style={styles.bold}>Notes:</Text> {selectedAttendance.notes ? selectedAttendance.notes : '-'}</Text>

                  <TouchableOpacity onPress={() => setModalVisible(false)} style={styles.closeButton}>
                    <Text style={styles.closeText}>Close</Text>
                  </TouchableOpacity>
                </>
              )}
            </View>
          </View>
        </Modal>
      </LinearGradient>
      {showDatePicker && (
  <DateTimePicker
    value={isPickingStartDate ? startDate : endDate}
    mode="date"
    display="default"
    onChange={(event, selectedDate) => {
      if (selectedDate) {
        if (isPickingStartDate) {
          setStartDate(selectedDate);
          setIsPickingStartDate(false); // Lanjut ke tanggal akhir
        } else {
          setEndDate(selectedDate);
          setShowDatePicker(false); // Selesai memilih rentang
        }
      } else {
        setShowDatePicker(false); // Jika dibatalkan
      }
    }}
  />
)}


    </>
  );
}

const styles = StyleSheet.create({
    container: { 
      flex: 1, 
      padding: 20,
      backgroundColor: '#FFF' 
    },
    title: { 
      fontSize: 22, 
      fontWeight: 'bold', 
      color: '#FF1493', 
      textAlign: 'center', 
      marginBottom: 20 
    },
    
    // Filter Section
    filterContainer: { 
      flexDirection: 'row', 
      justifyContent: 'space-between', 
      marginBottom: 15 
    },
    input: { 
      backgroundColor: '#FFF', 
      padding: 10, 
      borderRadius: 5, 
      borderWidth: 1, 
      borderColor: '#CCC', 
      width: '30%' 
    },
    filterButton: { 
      backgroundColor: '#FF1493', 
      padding: 10, 
      borderRadius: 5, 
      alignItems: 'center' 
    },
    filterText: { 
      color: '#FFF', 
      fontWeight: 'bold' 
    },
    dateButton: {
        backgroundColor: '#FFF',
        padding: 10,
        borderRadius: 5,
        borderWidth: 1,
        borderColor: '#CCC',
        width: '80%',
        alignItems: 'center',
      },
      dateText: {
        fontSize: 16,
        color: '#333',
      },
      
      
  
    // List Item
    listItem: { 
      backgroundColor: '#FFF', 
      padding: 15, 
      borderRadius: 10, 
      marginBottom: 10, 
      elevation: 5, 
      flexDirection: 'row', 
      justifyContent: 'space-between', 
      alignItems: 'center', 
      borderWidth: 1, 
      borderColor: '#FFB6C1' 
    },
    dateText: { 
      fontSize: 16, 
      fontWeight: 'bold', 
      color: '#333' 
    },
    statusText: { 
      fontSize: 14, 
      fontWeight: 'bold', 
      textTransform: 'capitalize' 
    },
    present: { 
      color: '#2E8B57' 
    },
    absent: { 
      color: '#DC143C' 
    },
  
    // Pagination
    pagination: { 
      flexDirection: 'row', 
      justifyContent: 'center', 
      alignItems: 'center', 
      marginTop: 15 
    },
    pageButton: { 
      backgroundColor: '#FF1493', 
      padding: 10, 
      borderRadius: 5, 
      marginHorizontal: 5 
    },
    disabledButton: { 
      backgroundColor: '#FFC0CB' 
    },
    pageText: { 
      color: '#FFF', 
      fontWeight: 'bold' 
    },
    pageNumber: { 
      fontSize: 16, 
      fontWeight: 'bold', 
      color: '#FF1493', 
      marginHorizontal: 10 
    },
  
    // Modal Styling
    modalContainer: { 
      flex: 1, 
      justifyContent: 'center', 
      alignItems: 'center', 
      backgroundColor: 'rgba(0,0,0,0.5)' 
    },
    modalContent: { 
      backgroundColor: '#FFF', 
      padding: 20, 
      borderRadius: 15, 
      width: width * 0.85, 
      elevation: 10 
    },
    modalTitle: { 
      fontSize: 20, 
      fontWeight: 'bold', 
      color: '#FF1493', 
      marginBottom: 10, 
      textAlign: 'center' 
    },
    modalText: { 
      fontSize: 16, 
      color: '#333', 
      marginBottom: 5 
    },
    bold: { 
      fontWeight: 'bold', 
      color: '#FF1493' 
    },
    closeButton: { 
      backgroundColor: '#FF1493', 
      padding: 10, 
      borderRadius: 5, 
      marginTop: 15, 
      alignItems: 'center' 
    },
    closeText: { 
      color: '#FFF', 
      fontWeight: 'bold' 
    } ,
    studentCard: {
        backgroundColor: '#FFF',
        padding: 15,
        borderRadius: 10,
        marginBottom: 15,
        elevation: 5,
        borderWidth: 1,
        borderColor: '#FFB6C1',
      },
      studentName: {
        fontSize: 20,
        fontWeight: 'bold',
        color: '#FF1493',
        textAlign: 'center',
      },
      studentDetail: {
        fontSize: 16,
        color: '#333',
        marginTop: 5,
      },
      errorText: {
        color: '#DC143C',
        fontSize: 16,
        textAlign: 'center',
      },
      bold: {
        fontWeight: 'bold',
      },
      
  });
  

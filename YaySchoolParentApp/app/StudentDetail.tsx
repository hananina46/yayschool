import React, { useEffect, useState } from 'react';
import { 
  View, Text, Image, ActivityIndicator, Alert, StyleSheet, ScrollView 
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { Stack, useLocalSearchParams } from 'expo-router';

const BASE_URL = 'https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/api/parent_area/student';

interface Student {
  id: number;
  name: string;
  email: string;
  nisn: string;
  dob: string;
  gender: string;
  phone: string;
  address: string;
  profile_photo: string;
  class: {
    name: string;
    academic_year: {
      name: string;
      start_date: string;
      end_date: string;
    };
  };
}

export default function StudentDetail() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const [student, setStudent] = useState<Student | null>(null);
  const [loading, setLoading] = useState(true);

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
      <Stack.Screen options={{ title: 'Student Details' }} />

      <LinearGradient colors={['#FFF', '#FFE4E1']} style={styles.container}>
        <ScrollView contentContainerStyle={styles.scrollContainer}>
          <Image 
            source={{ uri: `https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/storage/${student.profile_photo}` }} 
            style={styles.profilePhoto} 
          />
          <Text style={styles.name}>{student.name}</Text>

          {/* Student Details Table */}
          <View style={styles.tableContainer}>
            <Text style={styles.sectionTitle}>Student Information</Text>
            <View style={styles.table}>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>NISN</Text>
                <Text style={styles.cellValue}>{student.nisn}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Email</Text>
                <Text style={styles.cellValue}>{student.email}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Phone</Text>
                <Text style={styles.cellValue}>{student.phone}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Address</Text>
                <Text style={styles.cellValue}>{student.address}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Gender</Text>
                <Text style={styles.cellValue}>{student.gender === 'F' ? 'Female' : 'Male'}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Date of Birth</Text>
                <Text style={styles.cellValue}>{student.dob}</Text>
              </View>
            </View>
          </View>

          {/* Class Details Table */}
          <View style={styles.tableContainer}>
            <Text style={styles.sectionTitle}>Class Information</Text>
            <View style={styles.table}>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Class</Text>
                <Text style={styles.cellValue}>{student.class.name}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Academic Year</Text>
                <Text style={styles.cellValue}>{student.class.academic_year.name}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>Start Date</Text>
                <Text style={styles.cellValue}>{student.class.academic_year.start_date}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.cellLabel}>End Date</Text>
                <Text style={styles.cellValue}>{student.class.academic_year.end_date}</Text>
              </View>
            </View>
          </View>

          {/* Documents Section */}
          <View style={styles.tableContainer}>
            <Text style={styles.sectionTitle}>Documents</Text>
            <View style={styles.card}>
              <Text style={styles.info}>All student documents are stored securely.</Text>
              <Text style={styles.info}>Please contact the administration for access.</Text>
            </View>
          </View>
        </ScrollView>
      </LinearGradient>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  scrollContainer: { alignItems: 'center', paddingBottom: 20 },
  profilePhoto: { width: 120, height: 120, borderRadius: 60, marginBottom: 15 },
  name: { fontSize: 24, fontWeight: 'bold', color: '#FF1493', marginBottom: 10 },
  tableContainer: { width: '100%', marginBottom: 20 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#FF1493', marginBottom: 5 },
  table: { backgroundColor: '#FFF', borderRadius: 10, overflow: 'hidden', borderWidth: 1, borderColor: '#FF1493' },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#FF1493',
  },
  cellLabel: { fontSize: 16, fontWeight: '600', color: '#FF1493', width: '40%' },
  cellValue: { fontSize: 16, color: '#333', width: '60%', textAlign: 'right' },
  card: {
    backgroundColor: '#E3F2FD',
    padding: 15,
    borderRadius: 10,
    width: '100%',
    alignItems: 'center',
  },
  info: { fontSize: 16, color: '#333', textAlign: 'center' },
  errorText: { fontSize: 18, color: 'red', textAlign: 'center', marginTop: 20 },
});


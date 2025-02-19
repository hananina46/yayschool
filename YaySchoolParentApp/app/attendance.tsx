import React, { useEffect, useState } from 'react';
import { 
  View, Text, FlatList, Image, ActivityIndicator, Alert, StyleSheet, Dimensions, TouchableOpacity 
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { Stack, useRouter } from 'expo-router';

const { width } = Dimensions.get('window');
const BASE_URL = 'https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/api/parent_area/students';

interface Student {
  id: number;
  name: string;
  profile_photo: string;
}

export default function Attendances() {
  const [students, setStudents] = useState<Student[]>([]);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    const fetchStudents = async () => {
      try {
        const token = await AsyncStorage.getItem('token');
        if (!token) {
          Alert.alert('Error', 'Token tidak ditemukan.');
          return;
        }

        const response = await axios.get(BASE_URL, {
          headers: { Authorization: `Bearer ${token}` },
        });

        setStudents(response.data);
      } catch (error) {
        console.error('Error fetching students:', error);
        Alert.alert('Gagal mengambil data', 'Silakan coba lagi nanti.');
      } finally {
        setLoading(false);
      }
    };

    fetchStudents();
  }, []);

  const handleSelectStudent = (id: number) => {
    router.push({ pathname: '/attendancesList', params: { id } });
  };

  return (
    <>
      {/* Title di Header */}
      <Stack.Screen options={{ title: 'Student Attendance' }} />

      <LinearGradient colors={['#FFF', '#FFE4E1']} style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.title}>Student Attendance Overview</Text>
          <Text style={styles.subtitle}>Manage and track your children's attendance records</Text>
        </View>

        {loading ? (
          <ActivityIndicator size="large" color="#FF1493" />
        ) : (
          <FlatList
            data={students}
            keyExtractor={(item) => item.id.toString()}
            renderItem={({ item }) => (
              <TouchableOpacity style={styles.card} onPress={() => handleSelectStudent(item.id)}>
                <Image 
                  source={{ uri: `https://redesigned-invention-jjrv5v4g697j3pgq7-8000.app.github.dev/storage/${item.profile_photo}` }} 
                  style={styles.profilePhoto} 
                />
                <Text style={styles.studentName}>{item.name}</Text>
              </TouchableOpacity>
            )}
          />
        )}
      </LinearGradient>
    </>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  header: { alignItems: 'center', marginBottom: 20 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#FF1493' },
  subtitle: { fontSize: 14, color: '#666', marginTop: 5, textAlign: 'center' },
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFF',
    padding: 15,
    borderRadius: 15,
    marginBottom: 10,
    shadowColor: '#FF69B4',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.15,
    shadowRadius: 10,
    elevation: 6,
    borderWidth: 1,
    borderColor: '#FFB6C1',
  },
  profilePhoto: { width: 55, height: 55, borderRadius: 30, marginRight: 15 },
  studentName: { fontSize: 16, fontWeight: '600', color: '#333' },
});

import React, { useState } from 'react';
import { 
  View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, Dimensions, StatusBar 
} from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import { LinearGradient } from 'expo-linear-gradient';

const { width, height } = Dimensions.get('window');
const base_url = process.env.EXPO_PUBLIC_BASE_URL;

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const router = useRouter();

  const handleLogin = async () => {
    try {
      const response = await axios.post(`${base_url}/api/parent-login`, { email: email.toLowerCase(), password });
      if (response.status === 200) {
        const data = response.data;
        await AsyncStorage.setItem('token', data.token);
        Alert.alert('Login berhasil');
        router.replace('/'); 
      } else {
        Alert.alert('Login gagal');
      }
    } catch (error) {
      Alert.alert('Error', error.message);
    }
  };

  return (
    <>
      {/* Sembunyikan Status Bar */}
      <StatusBar hidden={true} />
      <LinearGradient colors={['#FF69B4', '#000000']} style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.greeting}>Welcome Back</Text>
          <Text style={styles.signIn}>Sign in to continue</Text>
        </View>

        {/* Kotak Form */}
        <View style={styles.formContainer}>
          <Text style={styles.formTitle}>YaySchool Parent Hub</Text>
          
          <Text style={styles.label}>Gmail</Text>
          <TextInput
            style={styles.input}
            value={email}
            onChangeText={setEmail}
            placeholder="yourmail@gmail.com"
            keyboardType="email-address"
            autoCapitalize="none"
            placeholderTextColor="#999"
          />

          <Text style={styles.label}>Password</Text>
          <TextInput
            style={styles.input}
            value={password}
            onChangeText={setPassword}
            placeholder="********"
            secureTextEntry
            placeholderTextColor="#999"
          />

          <TouchableOpacity onPress={() => Alert.alert('Lupa password?')}>
            <Text style={styles.forgotPassword}>Forgot password?</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.loginButton} onPress={handleLogin}>
            <LinearGradient colors={['#FF1493', '#C71585']} style={styles.loginGradient}>
              <Text style={styles.loginText}>SIGN IN</Text>
            </LinearGradient>
          </TouchableOpacity>

          <Text style={styles.signupText}>
            Don’t have an account? <Text style={styles.signupLink}>Sign up</Text>
          </Text>
        </View>
      </LinearGradient>
    </>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  header: {
    position: 'absolute',
    top: height * 0.15,
    left: 20,
  },
  greeting: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#FFF',
  },
  signIn: {
    fontSize: 18,
    color: '#EEE',
    marginTop: 5,
  },
  formContainer: {
    backgroundColor: '#FFF',
    borderRadius: 20,
    padding: 25,
    width: width * 0.9,
    alignSelf: 'center',
    shadowColor: '#FF69B4',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 8,
  },
  formTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 20,
    color: '#333',
  },
  label: {
    fontSize: 16,
    fontWeight: '500',
    color: '#FF1493',
    marginBottom: 5,
  },
  input: {
    backgroundColor: '#F9F9F9',
    padding: 12,
    borderRadius: 10,
    marginBottom: 20,
    borderWidth: 1,
    borderColor: '#DDD',
    color: '#000',
  },
  forgotPassword: {
    textAlign: 'right',
    color: '#C71585',
    marginBottom: 20,
  },
  loginButton: {
    borderRadius: 25,
    overflow: 'hidden',
    marginBottom: 20,
  },
  loginGradient: {
    padding: 15,
    alignItems: 'center',
    borderRadius: 25,
  },
  loginText: {
    color: '#FFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  signupText: {
    textAlign: 'center',
    color: '#555',
  },
  signupLink: {
    color: '#FF1493',
    fontWeight: '600',
  },
});


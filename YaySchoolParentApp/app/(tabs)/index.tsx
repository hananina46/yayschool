import React from "react";
import { View, Text, TextInput, TouchableOpacity, ScrollView, StatusBar } from "react-native";
import Icon from "react-native-vector-icons/FontAwesome";
import { LinearGradient } from "expo-linear-gradient";
import { useRouter } from "expo-router";
import { useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';



export default function HomeScreen() {
  const router = useRouter();

  useEffect(() => {
    const checkAuth = async () => {
      const token = await AsyncStorage.getItem('token');
      if (!token) {
        router.replace('/login');
      }
    };
  });

  const sections = [
    { title: "Absensi", icon: "check-circle", color: "#FF4D6D", route: "/attendance" },
    //payment
    { title: "Billing", icon: "money", color: "#10B981", route: "/payment" },
    //my student
    { title: "Students", icon: "user", color: "#2563EB", route: "/mystudent" },
  ];

  const newsList = [
    { title: "Live Classes", description: "Join live lessons from teachers.", icon: "video-camera", color: "#FF4D6D", route: "/live-classes" },
    { title: "Homework Help", description: "Get guidance for homework.", icon: "pencil", color: "#2563EB", route: "/homework-help" },
    { title: "Exam Schedule", description: "View upcoming exams and tests.", icon: "calendar", color: "#F59E0B", route: "/exam-schedule" },
    { title: "Parent-Teacher Chat", description: "Communicate with teachers.", icon: "comments", color: "#10B981", route: "/parent-teacher-chat" },
    { title: "School Announcements", description: "Stay updated with news.", icon: "bullhorn", color: "#8B5CF6", route: "/school-announcements" },
    { title: "Student Portfolio", description: "Track your child’s progress.", icon: "folder-open", color: "#EC4899", route: "/student-portfolio" },
  ];

  return (
    <LinearGradient colors={["#FFE6EC", "#FFFFFF"]} style={{ flex: 1 }}>
      <StatusBar hidden={true} />
      <View style={{ flex: 1, padding: 16 }}>
        {/* Header */}
        <View style={{ backgroundColor: "#FF4D6D", padding: 20, borderRadius: 15, marginBottom: 20 }}>
          <Text style={{ color: "#FFFFFF", fontSize: 22, fontWeight: "bold" }}>Welcome back</Text>
          <Text style={{ color: "#FFE0E6", fontSize: 14, marginTop: 4 }}>Monitor your child's learning progress</Text>
          <TextInput
            style={{
              backgroundColor: "#FFFFFF",
              borderRadius: 10,
              padding: 12,
              fontSize: 14,
              marginTop: 10,
              color: "#333",
            }}
            placeholder="Search..."
            placeholderTextColor="#999"
          />
        </View>

        {/* Quick Access Menu (Tanpa Bayangan) */}
        <View style={{ flexDirection: "row", justifyContent: "space-between", marginTop: 20 }}>
          {sections.map((section, index) => (
            <TouchableOpacity
              key={index}
              style={{
                flex: 1,
                marginHorizontal: 5,
                backgroundColor: section.color + "20",
                borderRadius: 15,
                justifyContent: "center",
                alignItems: "center",
                padding: 20,
              }}
              onPress={() => router.push(section.route)}
            >
              <Icon name={section.icon} size={32} color={section.color} />
              <Text style={{ fontSize: 14, fontWeight: "600", marginTop: 8, color: "#333" }}>
                {section.title}
              </Text>
            </TouchableOpacity>
          ))}
        </View>

        {/* News List */}
        <ScrollView style={{ marginTop: 20 }}>
          <Text style={{ fontSize: 18, fontWeight: "bold", marginBottom: 10, color: "#E63946" }}>
            Education Hub
          </Text>
          {newsList.map((news, index) => (
            <TouchableOpacity 
              key={index}
              onPress={() => router.push(news.route)}
              style={{ 
                backgroundColor: "#FFFFFF", 
                borderRadius: 16, 
                padding: 16, 
                marginBottom: 12,
                flexDirection: "row",
                alignItems: "center",
                shadowColor: "#000",
                shadowOpacity: 0.1,
                shadowRadius: 4,
                elevation: 2,
              }}
            >
              <View style={{
                backgroundColor: `${news.color}30`,
                width: 48,
                height: 48,
                borderRadius: 12,
                alignItems: "center",
                justifyContent: "center",
                marginRight: 16
              }}>
                <Icon name={news.icon} size={20} color={news.color} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={{ fontSize: 16, fontWeight: "600", color: "#333", marginBottom: 4 }}>
                  {news.title}
                </Text>
                <Text style={{ fontSize: 14, color: "#666", lineHeight: 20 }}>
                  {news.description}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>
    </LinearGradient>
  );
}

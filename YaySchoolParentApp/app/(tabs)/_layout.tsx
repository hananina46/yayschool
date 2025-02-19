import { Tabs } from 'expo-router';
import React from 'react';
import { Platform } from 'react-native';

import { HapticTab } from '@/components/HapticTab';
import { IconSymbol } from '@/components/ui/IconSymbol';
import TabBarBackground from '@/components/ui/TabBarBackground';
import { useColorScheme } from '@/hooks/useColorScheme';

export default function TabLayout() {
  const colorScheme = useColorScheme();

  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: '#FF4D6D', // Warna tombol aktif (pink terang)
        tabBarInactiveTintColor: '#555', // Warna tombol tidak aktif (abu-abu gelap)
        headerShown: false,
        tabBarButton: HapticTab,
        tabBarBackground: TabBarBackground,
        tabBarStyle: Platform.select({
          ios: {
            position: 'absolute',
            backgroundColor: '#FFFFFF', // Background tetap putih agar bersih
            borderTopColor: '#FF4D6D', // Garis atas tab bar pink
            borderTopWidth: 1.5,
          },
          default: {
            backgroundColor: '#FFFFFF', // Background tetap putih agar bersih
            borderTopColor: '#FF4D6D', // Garis atas tab bar pink
            borderTopWidth: 1.5,
          },
        }),
      }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Home',
          tabBarIcon: ({ color }) => <IconSymbol size={28} name="house.fill" color={color} />,
          tabBarLabelStyle: { fontSize: 12, fontWeight: 'bold', color: '#FF4D6D' }, // Warna teks aktif
        }}
      />
      <Tabs.Screen
        name="explore"
        options={{
          title: 'Explore',
          tabBarIcon: ({ color }) => <IconSymbol size={28} name="paperplane.fill" color={color} />,
          tabBarLabelStyle: { fontSize: 12, fontWeight: 'bold', color: '#555' }, // Warna teks tidak aktif
        }}
      />
    </Tabs>
  );
}

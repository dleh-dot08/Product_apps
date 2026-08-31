import React from 'react';
import { Tabs } from 'expo-router';
import CustomTabBar from '../../components/CustomTabBar';
import { useAuth } from '../../context/AuthContext';

export default function DashboardLayout() {
  const { user } = useAuth();
  
  const isDriver = user?.division?.id === 2;
  const isPacker = (user?.division?.id === 1 || user?.division?.id === 5) && user?.role?.id === 5;
  const isViewer = !isDriver && !isPacker;

  return (
    <Tabs tabBar={(props: any) => <CustomTabBar {...props} />} screenOptions={{ headerShown: false }}>
      <Tabs.Screen name="index" options={{ title: 'Beranda' }} />
      <Tabs.Screen 
        name="list-tugas" 
        options={{ 
          title: 'Tugas',
        }} 
      />

      <Tabs.Screen 
        name="documents" 
        options={{ 
          title: 'Laporan',
        }} 
      />
      <Tabs.Screen 
        name="laporan/[id]" 
        options={{ 
          href: null,
        }} 
      />
      <Tabs.Screen name="profile" options={{ title: 'Profil' }} />
    </Tabs>
  );
}

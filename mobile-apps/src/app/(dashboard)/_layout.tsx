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
      <Tabs.Screen name="index" options={{ title: 'Home' }} />
      <Tabs.Screen 
        name="calendar" 
        options={{ 
          title: 'Calendar',
          href: isDriver || isViewer ? null : '/calendar'
        }} 
      />
      <Tabs.Screen 
        name="add" 
        options={{ 
          title: 'Add',
          href: isViewer ? null : '/add'
        }} 
      />
      <Tabs.Screen 
        name="documents" 
        options={{ 
          title: 'Documents',
          href: isViewer ? null : '/documents' 
        }} 
      />
      <Tabs.Screen name="profile" options={{ title: 'Profile' }} />
    </Tabs>
  );
}

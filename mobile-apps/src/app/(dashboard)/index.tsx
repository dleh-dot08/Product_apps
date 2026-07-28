import React from 'react';
import { StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';

import PackerDashboard from '../../components/dashboard/PackerDashboard';
import DriverDashboard from '../../components/dashboard/DriverDashboard';
import ViewerDashboard from '../../components/dashboard/ViewerDashboard';

export default function DashboardScreen() {
  const { user } = useAuth();
  const { theme } = useTheme();
  const colors = Colors[theme];

  // Logic to determine the correct dashboard based on Division and Role
  // Divisi: 2 (Driver / Pengiriman)
  // Divisi: 1 (Logistik) / 5 (Warehouse) + Role: 5 (Operator Staff) -> Packer
  
  const isDriver = user?.division?.id === 2;
  const isPacker = (user?.division?.id === 1 || user?.division?.id === 5) && user?.role?.id === 5;
  
  let DashboardComponent = ViewerDashboard; // Default for Manager/Admin/SPV

  if (isDriver) {
    DashboardComponent = DriverDashboard;
  } else if (isPacker) {
    DashboardComponent = PackerDashboard;
  }

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]}>
      <DashboardComponent />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});

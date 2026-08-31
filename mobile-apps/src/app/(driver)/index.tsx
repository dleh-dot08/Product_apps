import React, { ComponentType, useMemo } from 'react';
import { StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';

import { Colors } from '@/constants/theme';

import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';

import DriverDashboard from '../../components/dashboard/DriverDashboard';
import PackerDashboard from '../../components/dashboard/PackerDashboard';
import ViewerDashboard from '../../components/dashboard/ViewerDashboard';

/**
 * Division IDs
 *
 * Sesuaikan dengan master division di backend.
 */
const DIVISION = {
  LOGISTIC: 1,
  DRIVER: 2,
  WAREHOUSE: 5,
} as const;

/**
 * Role IDs
 *
 * Sesuaikan dengan master role di backend.
 */
const ROLE = {
  OPERATOR_STAFF: 5,
} as const;

type DashboardComponent = ComponentType;

/**
 * Normalize ID karena beberapa API bisa mengembalikan
 * integer maupun numeric string.
 */
function normalizeId(value: unknown): number | null {
  if (value === null || value === undefined || value === '') {
    return null;
  }

  const id = Number(value);

  return Number.isFinite(id) ? id : null;
}

/**
 * Menentukan dashboard berdasarkan user.
 *
 * Urutan rule penting:
 * 1. Driver
 * 2. Packer
 * 3. Viewer / Management
 *
 * Dashboard baru seperti AdminDashboard, SupervisorDashboard,
 * MaintenanceDashboard, dan lainnya bisa ditambahkan di sini
 * tanpa mengubah struktur screen.
 */
function resolveDashboard(
  divisionId: number | null,
  roleId: number | null,
): DashboardComponent {
  const isDriver = divisionId === DIVISION.DRIVER;

  if (isDriver) {
    return DriverDashboard;
  }

  const isWarehouseDivision =
    divisionId === DIVISION.LOGISTIC ||
    divisionId === DIVISION.WAREHOUSE;

  const isPacker =
    isWarehouseDivision &&
    roleId === ROLE.OPERATOR_STAFF;

  if (isPacker) {
    return PackerDashboard;
  }

  /**
   * Default:
   * Admin / Manager / SPV / Viewer.
   *
   * Nanti bisa dipisahkan menjadi:
   *
   * if (isAdmin) {
   *     return AdminDashboard;
   * }
   *
   * if (isSupervisor) {
   *     return SupervisorDashboard;
   * }
   *
   * if (isManager) {
   *     return ManagerDashboard;
   * }
   */
  return ViewerDashboard;
}

export default function DashboardScreen() {
  const { user } = useAuth();
  const { theme } = useTheme();

  const colors = Colors[theme];

  const divisionId = normalizeId(user?.division?.id);
  const roleId = normalizeId(user?.role?.id);

  const DashboardComponent = useMemo(
    () => resolveDashboard(divisionId, roleId),
    [divisionId, roleId],
  );

  return (
    <SafeAreaView
      edges={['top']}
      style={[
        styles.container,
        {
          backgroundColor: colors.background,
        },
      ]}
    >
      <DashboardComponent />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});
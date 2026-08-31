import React from 'react';
import { View, StyleSheet } from 'react-native';
import ListTugas from '../../components/dashboard/ListTugas';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '../../constants/theme';

export default function ListTugasScreen() {
  const { theme } = useTheme();
  const colors = Colors[theme];
  const isDark = theme === 'dark';

  return (
    <View style={[styles.container, { backgroundColor: isDark ? colors.background : '#f8fafc' }]}>
      {/* ListTugas component will render its own header or we can rely on standard tab styles */}
      <ListTugas />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  }
});

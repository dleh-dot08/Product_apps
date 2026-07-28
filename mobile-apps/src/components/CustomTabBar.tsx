import React, { useState } from 'react';
import { View, TouchableOpacity, StyleSheet } from 'react-native';
import { BottomTabBarProps } from '@react-navigation/bottom-tabs';
import Svg, { Path } from 'react-native-svg';
import { useTheme } from '../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

const TAB_BAR_HEIGHT = 70;

const CustomTabBar = ({ state, descriptors, navigation }: BottomTabBarProps) => {
  const { theme } = useTheme();
  const colors = Colors[theme];
  const [barWidth, setBarWidth] = useState(0);

  // Creates a curve in the middle
  const getPath = () => {
    if (barWidth === 0) return '';
    return `
      M 0 0
      L ${(barWidth / 2) - 40} 0
      C ${(barWidth / 2) - 20} 0, ${(barWidth / 2) - 35} 40, ${barWidth / 2} 40
      C ${(barWidth / 2) + 35} 40, ${(barWidth / 2) + 20} 0, ${(barWidth / 2) + 40} 0
      L ${barWidth} 0
      L ${barWidth} ${TAB_BAR_HEIGHT}
      L 0 ${TAB_BAR_HEIGHT}
      Z
    `;
  };

  return (
    <View 
      style={styles.container} 
      onLayout={(e) => setBarWidth(e.nativeEvent.layout.width)}
    >
      {barWidth > 0 && (
        <Svg width={barWidth} height={TAB_BAR_HEIGHT} style={styles.background}>
          <Path d={getPath()} fill={theme === 'dark' ? '#222' : '#e6e0fc'} />
        </Svg>
      )}

      <View style={styles.content}>
        {state.routes.map((route: any, index: number) => {
          const { options } = descriptors[route.key];
          
          if ((options as any).href === null) {
            return null;
          }

          const isFocused = state.index === index;

          const onPress = () => {
            const event = navigation.emit({
              type: 'tabPress',
              target: route.key,
              canPreventDefault: true,
            });

            if (!isFocused && !event.defaultPrevented) {
              navigation.navigate(route.name);
            }
          };

          let iconName: any = 'home';
          if (route.name === 'index') iconName = isFocused ? 'home' : 'home-outline';
          if (route.name === 'calendar') iconName = isFocused ? 'calendar' : 'calendar-outline';
          if (route.name === 'documents') iconName = isFocused ? 'document-text' : 'document-text-outline';
          if (route.name === 'profile') iconName = isFocused ? 'person' : 'person-outline';

          const color = isFocused ? '#5e35b1' : (theme === 'dark' ? '#aaa' : '#888');

          if (route.name === 'add') {
            return (
              <TouchableOpacity
                key={route.key}
                onPress={onPress}
                activeOpacity={0.8}
                style={styles.addButtonContainer}
              >
                <View style={styles.addButton}>
                  <Ionicons name="add" size={32} color="#fff" />
                </View>
              </TouchableOpacity>
            );
          }

          return (
            <TouchableOpacity
              key={route.key}
              onPress={onPress}
              style={styles.tabButton}
            >
              <Ionicons name={iconName} size={24} color={color} />
            </TouchableOpacity>
          );
        })}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    maxWidth: 800, // Handle web wide screens
    alignSelf: 'center', // Center it if constrained
    height: TAB_BAR_HEIGHT,
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  background: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
  },
  content: {
    flexDirection: 'row',
    height: TAB_BAR_HEIGHT,
    alignItems: 'center',
    justifyContent: 'space-around',
    paddingHorizontal: 10,
  },
  tabButton: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    height: '100%',
  },
  addButtonContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  addButton: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: '#6b3ce3', // from image
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 40,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
});

export default CustomTabBar;

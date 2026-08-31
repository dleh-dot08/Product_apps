import { Text } from '@/components/CustomText';
import React from 'react';
import {
  Platform,
  StyleSheet, TouchableOpacity,
  View,
} from 'react-native';
import { BottomTabBarProps } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';

import { Colors } from '@/constants/theme';

import { useTheme } from '../context/ThemeContext';

const TAB_BAR_HEIGHT = 64;

const PRIMARY_COLOR = '#0756C6';
const INACTIVE_COLOR = '#7C8DA5';

type IoniconName = keyof typeof Ionicons.glyphMap;

type TabVisualConfig = {
  activeIcon: IoniconName;
  inactiveIcon: IoniconName;
};

const TAB_CONFIG: Record<string, TabVisualConfig> = {
  index: {
    activeIcon: 'home',
    inactiveIcon: 'home-outline',
  },
  'list-tugas': {
    activeIcon: 'clipboard',
    inactiveIcon: 'clipboard-outline',
  },
  documents: {
    activeIcon: 'document-text',
    inactiveIcon: 'document-text-outline',
  },
  profile: {
    activeIcon: 'person',
    inactiveIcon: 'person-outline',
  },
};

export default function CustomTabBar({
  state,
  descriptors,
  navigation,
}: BottomTabBarProps) {
  const { theme } = useTheme();

  const colors = Colors[theme];
  const isDark = theme === 'dark';

  const backgroundColor = isDark
    ? colors.backgroundElement
    : '#FFFFFF';

  const borderColor = isDark
    ? colors.backgroundSelected
    : '#E8EDF4';

  const inactiveColor = isDark
    ? '#94A3B8'
    : INACTIVE_COLOR;

  return (
    <SafeAreaView
      edges={['bottom']}
      style={[
        styles.safeArea,
        {
          backgroundColor,
          borderTopColor: borderColor,
        },
      ]}
    >
      <View style={styles.tabBar}>
        {state.routes.map((route, index) => {
          const { options } = descriptors[route.key];

          const ALLOWED_TABS = ['index', 'list-tugas', 'documents', 'profile'];

          if (!ALLOWED_TABS.includes(route.name) || (options as any).href === null) {
            return null;
          }

          const isFocused = state.index === index;

          const config =
            TAB_CONFIG[route.name] ??
            TAB_CONFIG.index;

          const iconName = isFocused
            ? config.activeIcon
            : config.inactiveIcon;

          const tabColor = isFocused
            ? PRIMARY_COLOR
            : inactiveColor;

          const label =
            typeof options.title === 'string'
              ? options.title
              : getDefaultLabel(route.name);

          const onPress = () => {
            const event = navigation.emit({
              type: 'tabPress',
              target: route.key,
              canPreventDefault: true,
            });

            if (
              !isFocused &&
              !event.defaultPrevented
            ) {
              navigation.navigate(route.name);
            }
          };

          const onLongPress = () => {
            navigation.emit({
              type: 'tabLongPress',
              target: route.key,
            });
          };

          return (
            <TouchableOpacity
              key={route.key}
              activeOpacity={0.7}
              accessibilityRole="button"
              accessibilityState={
                isFocused
                  ? { selected: true }
                  : {}
              }
              accessibilityLabel={
                options.tabBarAccessibilityLabel
              }
              testID={
                options.tabBarButtonTestID
              }
              onPress={onPress}
              onLongPress={onLongPress}
              style={styles.tabButton}
            >
              <View
                style={[
                  styles.tabContent,
                  isFocused &&
                  styles.tabContentActive,
                ]}
              >
                <View
                  style={[
                    styles.iconContainer,
                    isFocused && {
                      backgroundColor:
                        '#EAF3FF',
                    },
                  ]}
                >
                  <Ionicons
                    name={iconName}
                    size={
                      isFocused
                        ? 23
                        : 22
                    }
                    color={tabColor}
                  />
                </View>

                <Text
                  numberOfLines={1}
                  style={[
                    styles.tabLabel,
                    {
                      color: tabColor,
                    },
                    isFocused &&
                    styles.tabLabelActive,
                  ]}
                >
                  {label}
                </Text>
              </View>
            </TouchableOpacity>
          );
        })}
      </View>
    </SafeAreaView>
  );
}

function getDefaultLabel(routeName: string): string {
  switch (routeName) {
    case 'index':
      return 'Beranda';

    case 'list-tugas':
      return 'Tugas';

    case 'documents':
      return 'Laporan';

    case 'profile':
      return 'Profil';

    default:
      return routeName;
  }
}

const styles = StyleSheet.create({
  safeArea: {
    borderTopWidth: 1,

    shadowColor: '#0F172A',
    shadowOffset: {
      width: 0,
      height: -4,
    },
    shadowOpacity: 0.06,
    shadowRadius: 12,

    elevation: 12,
  },

  tabBar: {
    height: TAB_BAR_HEIGHT,

    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-around',

    paddingHorizontal: 8,
    paddingTop: 5,
  },

  tabButton: {
    flex: 1,
    height: '100%',

    alignItems: 'center',
    justifyContent: 'center',
  },

  tabContent: {
    minWidth: 62,

    alignItems: 'center',
    justifyContent: 'center',

    paddingHorizontal: 7,
    paddingVertical: 4,

    borderRadius: 16,
  },

  tabContentActive: {
    transform: [
      {
        translateY: -1,
      },
    ],
  },

  iconContainer: {
    position: 'relative',

    width: 38,
    height: 32,

    alignItems: 'center',
    justifyContent: 'center',

    borderRadius: 12,
  },

  activeDot: {
    position: 'absolute',
    bottom: 1,

    width: 4,
    height: 4,

    backgroundColor: PRIMARY_COLOR,

    borderRadius: 2,
  },

  tabLabel: {
    marginTop: 2,

    fontSize: 9.5,
    fontWeight: '500',
    letterSpacing: 0.1,
  },

  tabLabelActive: {
    fontWeight: '800',
  },
});
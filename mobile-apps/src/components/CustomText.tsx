import React from 'react';
import { Text as RNText, TextProps, StyleSheet, Dimensions, PixelRatio, Platform } from 'react-native';

const { width } = Dimensions.get('window');
// 375 adalah standar lebar layar (misal iPhone X / HP ukuran sedang)
const scale = width / 375;

export function normalize(size: number) {
  const newSize = size * scale;
  if (Platform.OS === 'ios') {
    return Math.round(PixelRatio.roundToNearestPixel(newSize));
  } else {
    // Android kadang merender teks sedikit lebih besar
    return Math.round(PixelRatio.roundToNearestPixel(newSize)) - 1;
  }
}

export function Text(props: TextProps) {
  // 1. Flatten styles to read properties easily
  const flatStyle = StyleSheet.flatten(props.style || {}) || {};
  
  // 2. Extract fontWeight
  const fw = flatStyle.fontWeight;
  
  // 3. Determine the correct Inter font family
  let fontFamily = 'Inter_400Regular'; // Default (Regular, 400, normal)
  
  if (fw === '500') fontFamily = 'Inter_500Medium';
  else if (fw === '600') fontFamily = 'Inter_600SemiBold';
  else if (fw === '700' || fw === 'bold') fontFamily = 'Inter_700Bold';
  else if (fw === '800') fontFamily = 'Inter_800ExtraBold';
  else if (fw === '900') fontFamily = 'Inter_900Black';
  
  // 4. Remove fontWeight & extract fontSize to scale it
  const { fontWeight, fontSize, ...restStyle } = flatStyle as any;

  const normalizedFontSize = fontSize ? normalize(fontSize) : undefined;

  return (
    <RNText 
      {...props} 
      allowFontScaling={false} // Mencegah user membesarkan font dari pengaturan sistem HP
      style={[
        restStyle, 
        { fontFamily }, 
        normalizedFontSize ? { fontSize: normalizedFontSize } : {}
      ]} 
    />
  );
}

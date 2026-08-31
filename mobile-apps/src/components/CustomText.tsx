import React from 'react';
import { Text as RNText, TextProps, StyleSheet } from 'react-native';

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
  
  // 4. Remove fontWeight from the style to prevent Android's "faux bold" 
  // which makes custom bold fonts look messy.
  const { fontWeight, ...restStyle } = flatStyle as any;

  return (
    <RNText {...props} style={[restStyle, { fontFamily }]} />
  );
}

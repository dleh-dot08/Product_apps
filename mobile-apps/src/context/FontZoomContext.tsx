import React, { createContext, useContext, useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';

type FontZoomContextType = {
  fontZoom: number;
  setFontZoom: (zoom: number) => void;
};

const FontZoomContext = createContext<FontZoomContextType>({
  fontZoom: 1,
  setFontZoom: () => {},
});

export const useFontZoom = () => useContext(FontZoomContext);

export const FontZoomProvider: React.FC<{children: React.ReactNode}> = ({ children }) => {
  const [fontZoom, setFontZoomState] = useState(1);

  useEffect(() => {
    const loadZoom = async () => {
      try {
        const savedZoom = await AsyncStorage.getItem('font-zoom-level');
        if (savedZoom) {
          setFontZoomState(parseFloat(savedZoom));
        }
      } catch (e) {
        console.error('Failed to load font zoom', e);
      }
    };
    loadZoom();
  }, []);

  const setFontZoom = async (zoom: number) => {
    setFontZoomState(zoom);
    try {
      await AsyncStorage.setItem('font-zoom-level', zoom.toString());
    } catch (e) {
      console.error('Failed to save font zoom', e);
    }
  };

  return (
    <FontZoomContext.Provider value={{ fontZoom, setFontZoom }}>
      {children}
    </FontZoomContext.Provider>
  );
};

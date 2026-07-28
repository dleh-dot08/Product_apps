import React, { useState, useRef, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, FlatList } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Calendar, LocaleConfig } from 'react-native-calendars';
import { useTheme } from '../../context/ThemeContext';
import { Colors } from '@/constants/theme';
import { Ionicons } from '@expo/vector-icons';

// Setup Indonesian locale
LocaleConfig.locales['id'] = {
  monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
  monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'],
  dayNames: ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
  dayNamesShort: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
  today: 'Hari ini'
};
LocaleConfig.defaultLocale = 'id';

type TaskStatus = 'To Do' | 'In Progress' | 'Done';
type FilterType = 'All' | TaskStatus;

interface Task {
  id: string;
  title: string;
  time: string;
  status: TaskStatus;
}

const mockTasks: Task[] = [
  { id: '1', title: 'Meeting dengan Klien A', time: '09:00 - 10:00', status: 'Done' },
  { id: '2', title: 'Review Desain Kemasan', time: '11:00 - 12:30', status: 'In Progress' },
  { id: '3', title: 'Laporan Bulanan', time: '14:00 - 16:00', status: 'To Do' },
  { id: '4', title: 'Cek Stok Gudang', time: '16:30 - 17:00', status: 'To Do' },
];

const FILTERS: FilterType[] = ['All', 'To Do', 'In Progress', 'Done'];

// Helper to generate a range of dates for the horizontal strip
const generateDateRange = (daysBefore: number, daysAfter: number) => {
  const dates = [];
  for (let i = -daysBefore; i <= daysAfter; i++) {
    const d = new Date();
    d.setDate(d.getDate() + i);
    dates.push(d);
  }
  return dates;
};

export default function CalendarScreen() {
  const { theme } = useTheme();
  const colors = Colors[theme];
  
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);
  const [activeFilter, setActiveFilter] = useState<FilterType>('All');
  const [isExpanded, setIsExpanded] = useState(false);
  const [holidays, setHolidays] = useState<Record<string, string>>({});
  
  const dateList = useRef(generateDateRange(14, 30)).current;
  const flatListRef = useRef<FlatList<Date>>(null);

  useEffect(() => {
    // Fetch Indonesian Public Holidays
    const fetchHolidays = async () => {
      try {
        const year = new Date().getFullYear();
        const res = await fetch(`https://date.nager.at/api/v3/PublicHolidays/${year}/ID`);
        const data = await res.json();
        
        const holidayMap: Record<string, string> = {};
        if (Array.isArray(data)) {
          data.forEach((h: any) => {
            holidayMap[h.date] = h.localName || h.name;
          });
        }
        setHolidays(holidayMap);
      } catch (err) {
        console.log('Error fetching holidays', err);
      }
    };
    fetchHolidays();

    // Scroll to the center of the list (index 14 = today) after mounting
    setTimeout(() => {
      flatListRef.current?.scrollToIndex({
        index: 14,
        animated: true,
        viewPosition: 0.5 // Center the item
      });
    }, 100); // Small delay ensures layout is ready
  }, []);

  const filteredTasks = mockTasks.filter(task => {
    if (activeFilter === 'All') return true;
    return task.status === activeFilter;
  });

  const getStatusColor = (status: TaskStatus) => {
    switch (status) {
      case 'Done': return '#10b981'; // green
      case 'In Progress': return '#3b82f6'; // blue
      case 'To Do': return '#f59e0b'; // orange
    }
  };

  const getDayName = (date: Date) => {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    return days[date.getDay()];
  };

  const getMonthName = (date: Date) => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[date.getMonth()];
  };

  const toISODate = (date: Date) => {
    return date.toISOString().split('T')[0];
  };

  const isWeekend = (date: Date) => {
    const day = date.getDay();
    return day === 0 || day === 6; // Sunday or Saturday
  };

  const todayISO = new Date().toISOString().split('T')[0];
  const isNotToday = selectedDate !== todayISO;

  const handleGoToToday = () => {
    setSelectedDate(todayISO);
    flatListRef.current?.scrollToIndex({
      index: 14,
      animated: true,
      viewPosition: 0.5
    });
    // If expanded, we might want to keep it expanded or close it. 
    // Closing it makes sense since we jump to today.
    setIsExpanded(false); 
  };

  // Generate marked dates for the full calendar
  const getMarkedDates = () => {
    const marked: any = {};
    
    // Mark holidays with a red dot
    Object.keys(holidays).forEach(date => {
      marked[date] = { 
        marked: true, 
        dotColor: '#ef4444', 
        customStyles: {
          text: { color: '#ef4444', fontWeight: 'bold' }
        }
      };
    });

    // We can also color the selected date
    if (marked[selectedDate]) {
      marked[selectedDate] = { 
        ...marked[selectedDate], 
        selected: true, 
        selectedColor: colors.tint,
        customStyles: {
          text: { color: '#ffffff' }
        }
      };
    } else {
      marked[selectedDate] = { 
        selected: true, 
        selectedColor: colors.tint 
      };
    }

    return marked;
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]}>
      <View style={styles.header}>
        <View style={styles.headerTitleRow}>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Jadwal & Tugas</Text>
          <View style={{ flexDirection: 'row', alignItems: 'center' }}>
            {isNotToday && (
              <TouchableOpacity 
                style={[styles.todayButton, { backgroundColor: colors.tint + '15' }]} 
                onPress={handleGoToToday}
              >
                <Text style={[styles.todayButtonText, { color: colors.tint }]}>Hari Ini</Text>
              </TouchableOpacity>
            )}
            <TouchableOpacity 
              style={[styles.expandButton, { backgroundColor: colors.backgroundElement }]} 
              onPress={() => setIsExpanded(!isExpanded)}
            >
              <Ionicons name={isExpanded ? "chevron-up" : "calendar-outline"} size={20} color={colors.tint} />
              <Text style={[styles.expandButtonText, { color: colors.tint }]}>
                {isExpanded ? 'Tutup' : 'Kalender'}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>

      {/* Tampilan Kalender Utuh */}
      {isExpanded && (
        <View style={[styles.calendarContainer, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
          <Calendar
            current={selectedDate}
            markingType={'custom'}
            onDayPress={(day: any) => {
              setSelectedDate(day.dateString);
              setIsExpanded(false);
            }}
            markedDates={getMarkedDates()}
            theme={{
              backgroundColor: colors.backgroundElement,
              calendarBackground: colors.backgroundElement,
              textSectionTitleColor: colors.textSecondary,
              selectedDayBackgroundColor: colors.tint,
              selectedDayTextColor: '#ffffff',
              todayTextColor: colors.tint,
              dayTextColor: colors.text,
              textDisabledColor: colors.textSecondary + '80', // opacity
              monthTextColor: colors.text,
              arrowColor: colors.tint,
            }}
          />
        </View>
      )}

      {/* Tampilan Lis Tanggal Horizontal (Seperti Foto) */}
      {!isExpanded && (
        <View style={styles.horizontalDateContainer}>
          <FlatList
            ref={flatListRef}
            data={dateList}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.horizontalDateScroll}
            keyExtractor={(item, index) => index.toString()}
            getItemLayout={(data, index) => (
              { length: 82, offset: 82 * index, index } // 70 width + 12 margin
            )}
            renderItem={({ item: d, index }) => {
              const iso = toISODate(d);
              const isSelected = iso === selectedDate;
              const isRedDate = isWeekend(d) || holidays[iso];
              
              let textColor: string = colors.text;
              if (isSelected) textColor = '#ffffff';
              else if (isRedDate) textColor = '#ef4444'; // Red for weekend/holiday

              let subTextColor: string = colors.textSecondary;
              if (isSelected) subTextColor = '#ddd6fe';
              else if (isRedDate) subTextColor = '#ef4444';

              return (
                <TouchableOpacity
                  activeOpacity={0.7}
                  onPress={() => {
                    setSelectedDate(iso);
                    flatListRef.current?.scrollToIndex({
                      index,
                      animated: true,
                      viewPosition: 0.5
                    });
                  }}
                  style={[
                    styles.dateCard,
                    { 
                      backgroundColor: isSelected ? '#5b21b6' : colors.backgroundElement,
                      borderColor: isSelected ? '#5b21b6' : colors.backgroundSelected,
                    }
                  ]}
                >
                  <Text style={[styles.dateMonth, { color: subTextColor }]}>
                    {getMonthName(d)}
                  </Text>
                  <Text style={[styles.dateNumber, { color: textColor }]}>
                    {d.getDate()}
                  </Text>
                  <Text style={[styles.dateDay, { color: subTextColor }]}>
                    {getDayName(d)}
                  </Text>
                </TouchableOpacity>
              );
            }}
          />
        </View>
      )}
      
      <View style={styles.filterContainer}>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterScroll}>
          {FILTERS.map(filter => (
            <TouchableOpacity
              key={filter}
              style={[
                styles.filterButton,
                { 
                  backgroundColor: activeFilter === filter ? colors.tint : colors.backgroundElement,
                  borderColor: colors.backgroundSelected
                }
              ]}
              onPress={() => setActiveFilter(filter)}
            >
              <Text style={[
                styles.filterText,
                { color: activeFilter === filter ? '#fff' : colors.text }
              ]}>
                {filter}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.taskList}>
          {holidays[selectedDate] && (
            <View style={[styles.holidayAlert, { backgroundColor: '#ef444420', borderColor: '#ef4444' }]}>
              <Ionicons name="flag-outline" size={20} color="#ef4444" />
              <Text style={styles.holidayAlertText}>Libur Nasional: {holidays[selectedDate]}</Text>
            </View>
          )}
          
          <Text style={[styles.sectionTitle, { color: colors.text }]}>
            Tugas untuk {selectedDate}
          </Text>

          {filteredTasks.length === 0 ? (
            <View style={styles.emptyState}>
              <Ionicons name="calendar-clear-outline" size={48} color={colors.textSecondary} />
              <Text style={[styles.emptyText, { color: colors.textSecondary }]}>Tidak ada tugas untuk filter ini.</Text>
            </View>
          ) : (
            filteredTasks.map(task => (
              <View key={task.id} style={[styles.taskCard, { backgroundColor: colors.backgroundElement, borderColor: colors.backgroundSelected }]}>
                <View style={[styles.statusIndicator, { backgroundColor: getStatusColor(task.status) }]} />
                <View style={styles.taskInfo}>
                  <Text style={[styles.taskTitle, { color: colors.text }]}>{task.title}</Text>
                  <Text style={[styles.taskTime, { color: colors.textSecondary }]}>
                    <Ionicons name="time-outline" size={14} color={colors.textSecondary} /> {task.time}
                  </Text>
                </View>
                <View style={[styles.statusBadge, { backgroundColor: getStatusColor(task.status) + '20' }]}>
                  <Text style={[styles.statusText, { color: getStatusColor(task.status) }]}>{task.status}</Text>
                </View>
              </View>
            ))
          )}
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    paddingHorizontal: 20,
    marginBottom: 10,
    marginTop: 10,
  },
  headerTitleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
  },
  todayButton: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    marginRight: 8,
  },
  todayButtonText: {
    fontWeight: '600',
    fontSize: 14,
  },
  expandButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  expandButtonText: {
    marginLeft: 6,
    fontWeight: '600',
    fontSize: 14,
  },
  horizontalDateContainer: {
    marginBottom: 20,
  },
  horizontalDateScroll: {
    paddingHorizontal: 20,
    paddingVertical: 10,
  },
  dateCard: {
    width: 70,
    height: 90,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
    borderWidth: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  dateMonth: {
    fontSize: 12,
    fontWeight: '600',
    marginBottom: 4,
  },
  dateNumber: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  dateDay: {
    fontSize: 12,
  },
  calendarContainer: {
    marginHorizontal: 20,
    borderRadius: 16,
    borderWidth: 1,
    overflow: 'hidden',
    marginBottom: 24,
    paddingBottom: 8,
  },
  filterContainer: {
    flexDirection: 'row',
    marginBottom: 10,
  },
  filterScroll: {
    paddingHorizontal: 20,
  },
  filterButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    marginRight: 8,
  },
  filterText: {
    fontWeight: '600',
    fontSize: 14,
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 100, // For custom tab bar
  },
  taskList: {
    flex: 1,
  },
  holidayAlert: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 12,
    borderWidth: 1,
    marginBottom: 16,
  },
  holidayAlertText: {
    color: '#ef4444',
    fontWeight: 'bold',
    marginLeft: 8,
    fontSize: 14,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 16,
  },
  taskCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    marginBottom: 12,
  },
  statusIndicator: {
    width: 4,
    height: 40,
    borderRadius: 2,
    marginRight: 12,
  },
  taskInfo: {
    flex: 1,
  },
  taskTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  taskTime: {
    fontSize: 13,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  statusText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    marginTop: 12,
    fontSize: 14,
  }
});

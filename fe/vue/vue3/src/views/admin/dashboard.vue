<template>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Analytics</span>
            </li>
        </ul>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- School Statistics -->
            <div class="panel lg:col-span-2">
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">School Statistics</h5>
                <apexchart height="300" :options="barChart" :series="barChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
            </div>

            <!-- Today's Attendance -->
            <div class="panel">
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">Today's Attendance</h5>
                <apexchart height="300" :options="donutChart" :series="donutChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
            </div>

            <!-- Average Grades -->
            <div class="panel">
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">Average Grades</h5>
                <apexchart height="300" :options="lineChart" :series="lineChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
            </div>

            <!-- Student Gender Statistics -->
            <div class="panel">
                <h5 class="font-semibold text-lg dark:text-white-light mb-5">Student Gender Distribution</h5>
                <apexchart height="300" :options="pieChart" :series="pieChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import apexchart from 'vue3-apexcharts';
import { getDashboardStatistics } from '@/api/dashboard.js';
import { getStudents } from '@/api/student.js';

const statistics = ref({
    total_students: 0,
    total_teachers: 0,
    total_classes: 0,
    total_subjects: 0,
    attendance_today: { present: 0, absent: 0, sick: 0, permission: 0 },
    average_grades: [],
});

const maleCount = ref(0);
const femaleCount = ref(0);

// Fetch data from API
const fetchStatistics = async () => {
    try {
        const data = await getDashboardStatistics();
        statistics.value = data;
        updateChartData();
    } catch (error) {
        console.error('Failed to fetch dashboard statistics:', error);
    }
};

const fetchStudentData = async () => {
    try {
        const students = await getStudents();
        maleCount.value = students.filter(student => student.gender === 'M').length;
        femaleCount.value = students.filter(student => student.gender === 'F').length;
        updateGenderChart();
    } catch (error) {
        console.error('Failed to fetch student data:', error);
    }
};

// Update main charts
const updateChartData = () => {
    barChartSeries.value = [
        { name: 'Total', data: [statistics.value.total_students, statistics.value.total_teachers, statistics.value.total_classes, statistics.value.total_subjects] }
    ];
    
    donutChartSeries.value = [
        statistics.value.attendance_today.present,
        statistics.value.attendance_today.absent,
        statistics.value.attendance_today.sick,
        statistics.value.attendance_today.permission
    ];
    
    lineChartSeries.value = statistics.value.average_grades.map(grade => ({
        name: grade.subject.name,
        data: [grade.average_score]
    }));
};

// Update gender chart
const updateGenderChart = () => {
    pieChartSeries.value = [maleCount.value, femaleCount.value];
};

onMounted(() => {
    fetchStatistics();
    fetchStudentData();
});

// Chart Configurations
const barChart = computed(() => ({
    chart: { type: 'bar', height: 300 },
    colors: ['#2196f3'],
    xaxis: {
        categories: ['Students', 'Teachers', 'Classes', 'Subjects'],
        axisBorder: { color: '#e0e6ed' }
    },
    tooltip: { theme: 'light' }
}));

const barChartSeries = ref([]);

const donutChart = computed(() => ({
    chart: { type: 'donut', height: 300 },
    labels: ['Present', 'Absent', 'Sick', 'Permission'],
    colors: ['#00ab55', '#e7515a', '#e2a03f', '#805dca'],
    legend: { position: 'bottom' }
}));

const donutChartSeries = ref([]);

const lineChart = computed(() => ({
    chart: { type: 'line', height: 300 },
    colors: ['#4361ee'],
    xaxis: { categories: ['Average Score'] },
    stroke: { width: 2, curve: 'smooth' },
    tooltip: { theme: 'light' }
}));

const lineChartSeries = ref([]);

const pieChart = computed(() => ({
    chart: { type: 'pie', height: 300 },
    labels: ['Male', 'Female'],
    colors: ['#4361ee', '#e7515a'],
    legend: { position: 'bottom' }
}));

const pieChartSeries = ref([]);
</script>


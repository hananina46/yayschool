<template>
    <div class="panel mt-6 pb-0">
        <h5 class="font-semibold text-lg dark:text-white-light mb-5">Attendance Records</h5>
        
        <!-- Filter Date, Search, dan Tombol Make Attendance -->
        <div class="mb-4 flex flex-col md:flex-row items-center gap-4">
            <div class="flex items-center gap-4">
                <label for="attendanceDate" class="text-sm font-medium">Select Date:</label>
                <input v-model="selectedDate" type="date" id="attendanceDate" class="form-input" />
            </div>
            <div class="flex items-center gap-4 md:ml-auto">
                <input v-model="search" type="text" placeholder="Search..." class="form-input" />
                <button @click="goToMakeAttendance" class="btn btn-primary">Make Attendance</button>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1f2a37] p-5 rounded-md shadow">
            <vue3-datatable
                :rows="filteredAttendances"
                :columns="attendanceCols"
                :totalRows="filteredAttendances.length"
                :sortable="true"
                :search="search"
                skin="whitespace-nowrap bh-table-hover"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { getAttendances } from '@/api/attendance';

const router = useRouter();
const attendances = ref([]);
const search = ref('');
const selectedDate = ref('');

const attendanceCols = ref([
    { field: 'student.name', title: 'Student Name', hide: false },
    { field: 'student.nisn', title: 'NISN', hide: false },
    { field: 'schedule.subject.name', title: 'Subject', hide: false },
    { field: 'schedule.day', title: 'Day', hide: false },
    { field: 'date', title: 'Date', hide: false },
    { field: 'status', title: 'Status', hide: false },
    { field: 'notes', title: 'Notes', hide: false },
]);

const filteredAttendances = computed(() => {
    if (!selectedDate.value) {
        return attendances.value;
    }
    return attendances.value.filter(attendance => attendance.date === selectedDate.value);
});

const fetchAttendances = async () => {
    try {
        const data = await getAttendances();
        attendances.value = data;
    } catch (error) {
        console.error('Failed to fetch attendances:', error);
    }
};

const goToMakeAttendance = () => {
    router.push('/school/make-attendance');
};

onMounted(() => {
    fetchAttendances();
});
</script>

<template>
    <div class="panel mt-6 pb-0">
        <h5 class="font-semibold text-lg dark:text-white-light mb-5">Make Attendance</h5>

        <TransitionRoot appear :show="showModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-[51]">
                <DialogOverlay class="fixed inset-0 bg-[black]/60" />
                <div class="fixed inset-0 overflow-y-auto flex items-center justify-center px-4 py-8">
                    <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
                        <div class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] p-5">Select Class and Schedule</div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Class</label>
                                <select v-model="selectedClass" @change="handleClassChange" class="form-input w-full">
                                    <option value="">Select Class</option>
                                    <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                                </select>
                            </div>
                            <div v-if="schedules.length > 0">
                                <label class="block text-sm font-medium mb-2">Schedule</label>
                                <select v-model="selectedSchedule" class="form-input w-full">
                                    <option value="">Select Schedule</option>
                                    <option v-for="schedule in schedules" :key="schedule.id" :value="schedule.id">
                                        {{ schedule.subject.name }} - {{ schedule.day }} ({{ schedule.start_time }} - {{ schedule.end_time }})
                                    </option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="btn btn-primary" @click="confirmSelection" :disabled="!selectedClass || !selectedSchedule">Next</button>
                            </div>
                        </div>
                    </DialogPanel>
                </div>
            </Dialog>
        </TransitionRoot>

        <div v-if="students.length > 0" class="bg-white dark:bg-[#1f2a37] p-5 rounded-md shadow mt-4">
            <h6 class="font-semibold text-base mb-2">Class: {{ className }}</h6>
            <h6 class="font-semibold text-base mb-4">Schedule: {{ scheduleDetail }}</h6>
            <h6 class="font-semibold text-base mb-4">Students Attendance</h6>
            <form @submit.prevent="submitAttendance">
                <div class="mb-2 flex items-center justify-between font-medium border-b pb-2">
                    <span>Student</span>
                    <span>Attendance</span>
                </div>
                <div v-for="student in students" :key="student.id" class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">{{ student.name }} ({{ student.nisn }})</p>
                        <p class="text-xs text-gray-500">{{ student.email }}</p>
                    </div>
                    <select v-model="attendance[student.id]" class="form-input w-32">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="sick">Sick</option>
                        <option value="permission">Permission</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-4">Submit Attendance</button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'; // Import useRouter untuk navigasi
import { getSchoolClasses } from '@/api/classroom';
import { createAttendance } from '@/api/attendance';
import Swal from 'sweetalert2';
import { TransitionRoot, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';

const router = useRouter(); // Inisialisasi router
const showModal = ref(true);
const classes = ref([]);
const schedules = ref([]);
const students = ref([]);
const selectedClass = ref('');
const selectedSchedule = ref('');
const attendance = ref({});
const className = ref('');
const scheduleDetail = ref('');
const teacherId = ref(0);
const currentDate = new Date().toISOString().split('T')[0];

const fetchClasses = async () => {
    try {
        classes.value = await getSchoolClasses();
    } catch (error) {
        console.error('Failed to fetch classes:', error);
    }
};

const handleClassChange = () => {
    const selected = classes.value.find(cls => cls.id === selectedClass.value);
    schedules.value = selected ? selected.schedules : [];
    students.value = [];
    attendance.value = {};
};

const confirmSelection = () => {
    const selected = classes.value.find(cls => cls.id === selectedClass.value);
    const schedule = schedules.value.find(s => s.id === selectedSchedule.value);
    students.value = selected.students || [];
    className.value = selected.name;
    scheduleDetail.value = `${schedule.subject.name} - ${schedule.day} (${schedule.start_time} - ${schedule.end_time})`;
    teacherId.value = schedule.teacher.id;
    showModal.value = false;
};

const submitAttendance = async () => {
    try {
        const attendanceData = Object.keys(attendance.value).map(studentId => ({
            schedule_id: selectedSchedule.value,
            student_id: parseInt(studentId),
            teacher_id: teacherId.value,
            date: currentDate,
            status: attendance.value[studentId],
            notes: attendance.value[studentId] === 'present' ? 'Hadir tepat waktu' : '',
        }));

        for (const data of attendanceData) {
            await createAttendance(data);
        }

        Swal.fire({ title: 'Success!', text: 'Attendance submitted successfully.', icon: 'success' }).then(() => {
            router.push('/school/attendance'); // Kembali ke attendances.vue setelah sukses
        });
        router.push('/school/attendance');
    } catch (error) {
        console.error('Failed to submit attendance:', error);
        Swal.fire({ title: 'Error!', text: 'Failed to submit attendance.', icon: 'error' });
    }
};

onMounted(() => {
    fetchClasses();
});
</script>


<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex items-center mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Student Detail - {{ student.name }}</h5>
                <button class="btn btn-outline-primary" @click="goBack">← Back</button>
            </div>

            <!-- Student Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Student Info -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h6 class="font-bold mb-3">Student Info</h6>
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                        <tr>
                            <td colspan="2" class="text-center p-4">
                                <img
                                    v-if="student.profile_photo"
                                    :src="`${baseURL}/storage/${student.profile_photo}`"
                                    alt="Profile Photo"
                                    class="w-32 h-32 rounded-full border-4 border-gray-300 shadow-md mx-auto"
                                />
                                <div v-else class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xl font-bold mx-auto">
                                    {{ student.name ? student.name.charAt(0) : '?' }}
                                </div>
                            </td>
                        </tr>
                        <tr><td class="p-2 font-semibold">Name</td><td class="p-2">{{ student.name || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">Email</td><td class="p-2">{{ student.email || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">Class</td><td class="p-2">{{ student.class?.name || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">NISN</td><td class="p-2">{{ student.nisn || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">Gender</td><td class="p-2">{{ student.gender === 'M' ? 'Male' : 'Female' }}</td></tr>
                        <tr><td class="p-2 font-semibold">DOB</td><td class="p-2">{{ student.dob || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">Phone</td><td class="p-2">{{ student.phone || '-' }}</td></tr>
                        <tr><td class="p-2 font-semibold">Address</td><td class="p-2">{{ student.address || '-' }}</td></tr>
                    </table>
                </div>

                <!-- Guardians -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h6 class="font-bold mb-3">Guardians</h6>
                    <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                        <tr>
                            <th class="p-2 border border-gray-300 dark:border-gray-600">Name</th>
                            <th class="p-2 border border-gray-300 dark:border-gray-600">Phone</th>
                        </tr>
                        <tr v-for="guardian in student.guardians" :key="guardian.id">
                            <td class="p-2 border border-gray-300 dark:border-gray-600">{{ guardian.name }}</td>
                            <td class="p-2 border border-gray-300 dark:border-gray-600">{{ guardian.phone }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <h6 class="font-bold">Grades</h6>
                <vue3-datatable
                    :rows="student.grades"
                    :columns="gradeCols"
                    :totalRows="student.grades?.length"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                />
            </div>

            <div class="mt-6">
                <h6 class="font-bold">Bills</h6>
                <vue3-datatable
                    :rows="student.bills"
                    :columns="billCols"
                    :totalRows="student.bills?.length"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { getStudentById } from '@/api/student';
const baseURL = import.meta.env.VITE_BASE_URL; // Simpan VITE_BASE_URL ke dalam variabel

const route = useRoute();
const router = useRouter();
const studentId = route.params.studentId;

const student = ref({});
const gradeCols = ref([
    { field: 'subject.name', title: 'Subject', hide: false },
    //class.name
    { field: 'class.name', title: 'Class', hide: false },
    //academic_year.name
    { field: 'academic_year.name', title: 'Academic Year', hide: false },
    { field: 'type', title: 'Grade Type', hide: false },
    { field: 'score', title: 'Score', hide: false },
    { field: 'remarks', title: 'Remarks', hide: false },
]);

const billCols = ref([
    //academic_year.name
    { field: 'academic_year.name', title: 'Academic Year', hide: false },

    { field: 'bill_type.name', title: 'Bill Type', hide: false },
    //bill_type.amount
    { field: 'bill_type.amount', title: 'Amount', hide: false },
    { field: 'discount', title: 'Discount', hide: false },
    { field: 'status', title: 'Status', hide: false },
    { field: 'payment_method', title: 'Payment Method', hide: false },
    { field: 'payment_proof', title: 'Payment Proof', hide: false, slotName: 'payment_proof' },
]);

const fetchStudentDetail = async () => {
    try {
        student.value = await getStudentById(studentId);
    } catch (error) {
        console.error('Failed to fetch student details:', error);
    }
};

const goBack = () => {
    if (window.history.length > 1) {
        router.go(-1);
    } else {
        router.push({ name: 'student' }); // Redirect ke daftar siswa jika tidak ada history sebelumnya
    }
};

onMounted(fetchStudentDetail);
</script>

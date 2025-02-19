<template>
    <div class="panel mt-6 pb-0">
        <h5 class="font-semibold text-lg dark:text-white-light mb-5">Classroom Details</h5>
        <div v-if="classroom" class="space-y-4">
            <div class="bg-white dark:bg-[#1f2a37] p-5 rounded-md shadow">
                <h6 class="font-semibold text-base mb-3">General Information</h6>
                <p><strong>Class Name:</strong> {{ classroom.name }}</p>
                <p><strong>Academic Year:</strong> {{ classroom.academic_year.name }} ({{ classroom.academic_year.start_date }} - {{ classroom.academic_year.end_date }})</p>
                <p><strong>Teacher:</strong> {{ classroom.teacher.name }} ({{ classroom.teacher.email }})</p>
            </div>

            <div class="bg-white dark:bg-[#1f2a37] p-5 rounded-md shadow">
                <h6 class="font-semibold text-base mb-3">Schedules</h6>
                <ul>
                    <li v-for="schedule in classroom.schedules" :key="schedule.id" class="mb-2">
                        {{ schedule.day }} ({{ schedule.start_time }} - {{ schedule.end_time }}): {{ schedule.subject.name }} - {{ schedule.teacher.name }}
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-[#1f2a37] p-5 rounded-md shadow">
                <h6 class="font-semibold text-base mb-3">Students</h6>
                <vue3-datatable
                    :rows="classroom.students"
                    :columns="studentCols"
                    :totalRows="classroom.students?.length"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                >
                    <template #profile_photo="data">
                        <img :src="data.value.profile_photo" alt="Profile Photo" class="w-10 h-10 rounded-full object-cover" />
                    </template>
                </vue3-datatable>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400">
            Loading classroom details...
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { getSchoolClassById } from '@/api/classroom';

const route = useRoute();
const classroom = ref(null);

const studentCols = ref([
    { field: 'profile_photo', title: 'Photo', hide: false, slotName: 'profile_photo' },
    { field: 'name', title: 'Name', hide: false },
    { field: 'email', title: 'Email', hide: false },
    { field: 'nisn', title: 'NISN', hide: false },
    { field: 'phone', title: 'Phone', hide: false },
    { field: 'address', title: 'Address', hide: false },
]);

const fetchClassroomDetails = async () => {
    try {
        const id = route.params.id;
        const response = await getSchoolClassById(id);
        classroom.value = response;
    } catch (error) {
        console.error('Failed to fetch classroom details:', error);
    }
};

onMounted(() => {
    fetchClassroomDetails();
});
</script>

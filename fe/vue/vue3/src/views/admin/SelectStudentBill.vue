<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Select Student</h5>
                <div>
                    <input v-model="search" type="text" class="form-input" placeholder="Search student..." />
                </div>
            </div>

            <div class="datatable">
                <vue3-datatable
                    :rows="filteredStudents"
                    :columns="cols"
                    :totalRows="filteredStudents?.length"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                >
                    <template #actions="data">
                        <button class="btn btn-primary" @click="viewStudentBills(data.value.id)">
                            View Bills
                        </button>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { getStudents } from '@/api/student';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Select Student' });

const router = useRouter();
const search = ref('');
const students = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Student Name', hide: false },
    { field: 'email', title: 'Email', hide: false },
    { field: 'class.name', title: 'Class', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const fetchStudents = async () => {
    try {
        students.value = await getStudents();
    } catch (error) {
        console.error('Failed to fetch students:', error);
    }
};

const filteredStudents = computed(() => {
    if (!search.value) return students.value;
    return students.value.filter(student =>
        student.name.toLowerCase().includes(search.value.toLowerCase()) ||
        student.email.toLowerCase().includes(search.value.toLowerCase()) ||
        student.class?.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const viewStudentBills = (studentId) => {
    router.push({ name: 'assigned_student_bill_view', params: { studentId } }); 
};

onMounted(fetchStudents);
</script>

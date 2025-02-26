<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex items-center mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Assigned Bills for {{ studentName }}</h5>
                <button class="btn btn-outline-primary" @click="goBack">Back</button>
            </div>

            <div class="datatable">
                <vue3-datatable
                    :rows="assignedBills"
                    :columns="cols"
                    :totalRows="assignedBills?.length"
                    :sortable="true"
                    skin="whitespace-nowrap bh-table-hover"
                >
                    <template #actions="data">
                        <button class="text-red-500 hover:text-red-700" @click="confirmDelete(data.value.id)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </template>
                </vue3-datatable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import Swal from 'sweetalert2';
import { getAssignedBillByStudentId, deleteAssignedBill } from '@/api/assignedBill';
import { getStudentById } from '@/api/student';
import { useMeta } from '@/composables/use-meta';

const route = useRoute();
const router = useRouter();
const studentId = route.params.studentId;

useMeta({ title: 'Assigned Bills' });

const studentName = ref('');
const assignedBills = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'academic_year.name', title: 'Academic Year', hide: false },
    { field: 'bill_type.name', title: 'Bill Type', hide: false },
    { field: 'bill_type.amount', title: 'Amount', hide: false },
    { field: 'discount', title: 'Discount', hide: false },
    { field: 'status', title: 'Status', hide: false },
    { field: 'note', title: 'Note', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const fetchAssignedBills = async () => {
    try {
        assignedBills.value = await getAssignedBillByStudentId(studentId);
    } catch (error) {
        console.error('Failed to fetch assigned bills:', error);
    }
};

const fetchStudentName = async () => {
    try {
        const student = await getStudentById(studentId);
        studentName.value = student.name;
    } catch (error) {
        console.error('Failed to fetch student details:', error);
    }
};

const confirmDelete = async (id) => {
    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonText: 'Delete',
    }).then(async (result) => {
        if (result.isConfirmed) {
            await deleteAssignedBill(id);
            fetchAssignedBills();
        }
    });
};

const goBack = () => {
    router.push({ name: 'select_student_bill' });
};

onMounted(() => {
    fetchStudentName();
    fetchAssignedBills();
});
</script>

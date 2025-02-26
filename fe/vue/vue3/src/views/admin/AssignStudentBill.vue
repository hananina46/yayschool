<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Assign Students to Bill</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showAssignModal" class="btn btn-primary">Assign Bill</button>
                </div>
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
    <button class="text-blue-500 hover:text-blue-700" @click="editAssignedBill(data.value)">
        <ion-icon name="create-outline"></ion-icon>
    </button>
    <button class="text-red-500 hover:text-red-700" @click="confirmDelete(data.value.id)">
        <ion-icon name="trash-outline"></ion-icon>
    </button>
</template>

                </vue3-datatable>
            </div>
        </div>

        <!-- Modal for Assign Bill -->
        <TransitionRoot appear :show="showModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-[51]">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <DialogOverlay class="fixed inset-0 bg-[black]/60" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-start justify-center px-4 py-8">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
                                <button
                                    type="button"
                                    class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                    @click="closeModal"
                                >
                                    <ion-icon name="close-outline"></ion-icon>
                                </button>
                                <div class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                    Assign Students to Bill
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="assignStudents">
                                        <div class="form-group mb-4">
                                            <label class="block text-sm font-medium">Students</label>
                                            <VueMultiselect 
                                                v-model="formData.student_ids" 
                                                :options="students" 
                                                :multiple="true"
                                                :close-on-select="false"
                                                track-by="id"
                                                label="name"
                                                placeholder="Search & Select Students"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="block text-sm font-medium">Academic Year</label>
                                            <select v-model="formData.academic_year_id" class="form-input mt-1" required>
                                                <option v-for="year in academicYears" :key="year.id" :value="year.id">
                                                    {{ year.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="block text-sm font-medium">Bill Type</label>
                                            <select v-model="formData.bill_type_id" class="form-input mt-1" required>
                                                <option v-for="bill in billTypes" :key="bill.id" :value="bill.id">
                                                    {{ bill.name }} - {{ bill.amount | currency }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="block text-sm font-medium">Discount</label>
                                            <input v-model="formData.discount" type="number" min="0" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
    <label class="block text-sm font-medium">Payment Method</label>
    <select v-model="formData.payment_method" class="form-input mt-1">
        <option value="VA">Virtual Account</option>
        <option value="manual_transfer">Manual Transfer</option>
        <option value="gift_card">Gift Card</option>
        <option value="credit_card">Credit Card</option>
    </select>
</div>

<div class="form-group mb-4">
    <label class="block text-sm font-medium">Payment Proof</label>
    <input type="file" @change="handleFileUpload" class="form-input mt-1" />
</div>

                                        <div class="form-group mb-4">
                                            <label class="block text-sm font-medium">Note</label>
                                            <textarea v-model="formData.note" class="form-input mt-1"></textarea>
                                        </div>
                                        <div class="flex justify-end">
    <button type="button" @click="closeModal" class="btn btn-outline-danger">Cancel</button>
    <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4">
        {{ editMode ? 'Update' : 'Assign' }}
    </button>
</div>

                                    </form>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import VueMultiselect from 'vue-multiselect';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';
import Swal from 'sweetalert2';
import { getStudents } from '@/api/student';
import { getAcademicYears } from '@/api/academicYear';
import { getBillTypes } from '@/api/billType';
import { createAssignedBill, getAssignedBills, deleteAssignedBill } from '@/api/assignedBill';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Assign Students to Bill' });

const search = ref('');
const assignedBills = ref([]);
const students = ref([]);
const academicYears = ref([]);
const billTypes = ref([]);
const editMode = ref(false);
const selectedBillId = ref(null);

const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'student.name', title: 'Student', hide: false },
    { field: 'academic_year.name', title: 'Academic Year', hide: false },
    { field: 'bill_type.name', title: 'Bill Type', hide: false },
    //amount
    { field: 'bill_type.amount', title: 'Amount', hide: false },
    //status
    {field: 'status', title: 'Status', hide: false},
    { field: 'discount', title: 'Discount', hide: false },
    { field: 'note', title: 'Note', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const handleFileUpload = (event) => {
    formData.value.payment_proof = event.target.files[0];
};


const showModal = ref(false);
const formData = ref({
    student_ids: [],
    academic_year_id: '',
    bill_type_id: '',
    discount: 0,
    note: '',
});

const fetchData = async () => {
    assignedBills.value = await getAssignedBills();
    students.value = await getStudents();
    academicYears.value = await getAcademicYears();
    billTypes.value = await getBillTypes();
};
const editAssignedBill = (billData) => {
    editMode.value = true;
    selectedBillId.value = billData.id;
    formData.value = {
        student_ids: [{ id: billData.student.id, name: billData.student.name }],
        academic_year_id: billData.academic_year.id,
        bill_type_id: billData.bill_type.id,
        discount: billData.discount,
        note: billData.note,
        payment_method: billData.payment_method,
        payment_proof: null, // Karena gambar tidak bisa langsung dimasukkan
    };
    showModal.value = true;
};

const showAssignModal = () => {
    editMode.value = false;
    selectedBillId.value = null;
    formData.value = {
        student_ids: [],
        academic_year_id: '',
        bill_type_id: '',
        discount: 0,
        note: '',
        payment_method: '',
        payment_proof: null,
    };
    showModal.value = true;
};


const saveBill = async () => {
    try {
        if (editMode.value) {
            await updateAssignedBill(selectedBillId.value, formData.value);
        } else {
            for (const student of formData.value.student_ids) {
                await createAssignedBill({
                    student_id: student.id,
                    academic_year_id: formData.value.academic_year_id,
                    bill_type_id: formData.value.bill_type_id,
                    status: 'pending',
                    discount: formData.value.discount,
                    note: formData.value.note,
                    payment_method: formData.value.payment_method,
                    payment_proof: formData.value.payment_proof,
                });
            }
        }
        fetchData();
        closeModal();
    } catch (error) {
        console.error('Failed to save bill:', error);
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
            fetchData();
        }
    });
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(fetchData);
</script>

<style>
@import 'vue-multiselect/dist/vue-multiselect.css';
</style>

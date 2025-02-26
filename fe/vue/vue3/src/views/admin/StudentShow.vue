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

            <!-- Documents Table -->
<div class="mt-6">
    

    <div class="mt-6">
    <h6 class="font-bold">Documents</h6>
    <button @click="showAddDocumentModal = true" class="btn btn-primary">Add Document</button>

    <vue3-datatable
        :rows="documents"
        :columns="documentCols"
        :totalRows="documents?.length"
        :sortable="true"
        skin="whitespace-nowrap bh-table-hover"
    >
        <template #actions="data">
            <!-- View Button -->
            <button class="text-blue-500 hover:text-blue-700" @click="viewFile(data.value)">
                <ion-icon name="eye-outline"></ion-icon> View
            </button>
            
            <!-- Delete Button -->
            <button class="text-red-500 hover:text-red-700" @click="confirmDeleteDocument(data.value.id)">
                <ion-icon name="trash-outline"></ion-icon> Delete
            </button>
        </template>
    </vue3-datatable>
</div>

</div>
<TransitionRoot appear :show="showAddDocumentModal" as="template">
    <Dialog as="div" @close="closeAddDocumentModal" class="relative z-[51]">
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
                            @click="closeAddDocumentModal"
                        >
                            <ion-icon name="close-outline"></ion-icon>
                        </button>
                        <div class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                            Add Document
                        </div>
                        <div class="p-5">
                            <form @submit.prevent="saveDocument">
                                <div class="form-group mb-4">
                                    <label class="block text-sm font-medium">Document Type</label>
                                    <select v-model="formData.document_type_id" class="form-input mt-1" required>
                                        <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                                            {{ type.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="block text-sm font-medium">Note</label>
                                    <textarea v-model="formData.note" class="form-input mt-1"></textarea>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="block text-sm font-medium">File</label>
                                    <input type="file" @change="handleFileUpload" class="form-input mt-1" required />
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" @click="closeAddDocumentModal" class="btn btn-outline-danger">Cancel</button>
                                    <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4">Add Document</button>
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';
import { getStudentById } from '@/api/student';
import { getDocumentsByUserId, createDocument, deleteDocument } from '@/api/document';
import { getDocumentTypes } from '@/api/documentType';
import Swal from 'sweetalert2';  // Import SweetAlert2



const baseURL = import.meta.env.VITE_BASE_URL; // Simpan VITE_BASE_URL ke dalam variabel
const showAddDocumentModal = ref(false); // Menyimpan status modal untuk tambah dokumen

const route = useRoute();
const router = useRouter();
const studentId = route.params.studentId;
const documents = ref([]);
const documentTypes = ref([]);

const student = ref({});
const fetchDocuments = async () => {
    console.log('fetchDocuments:' + student.value);
    try {
        documents.value = await getDocumentsByUserId(student.value.user_id);
        console.log('documents', documents.value);
    } catch (error) {
        console.error('Failed to fetch documents:', error);
    }
};

const fetchDocumentTypes = async () => {
    try {
        documentTypes.value = await getDocumentTypes();
    } catch (error) {
        console.error('Failed to fetch document types:', error);
    }
};
const documentCols = ref([
    { field: 'document_type.name', title: 'Document Type', hide: false },
    { field: 'note', title: 'Note', hide: false },
    { field: 'path', title: 'File', hide: false },
    { field: 'actions', title: 'Actions', hide: false }  // Actions column
]);


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

const viewFile = (document) => {
    if (document && document.path) {
        // Open the file in a new tab
        window.open(`${baseURL}/${document.path}`, '_blank');
    } else {
        console.error('File path not available');
    }
};


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
        fetchDocuments();

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

const formData = ref({
    document_type_id: '',
    note: '',
    file: null, // Untuk menyimpan file
});

// Fungsi untuk meng-upload file
const handleFileUpload = (event) => {
    formData.value.file = event.target.files[0];
};

// Fungsi untuk menyimpan dokumen baru
const saveDocument = async () => {
    try {
        const documentData = {
            document_type_id: formData.value.document_type_id,
            user_id: student.value.user_id,
            note: formData.value.note,
            file: formData.value.file,
        };
        await createDocument(documentData); // Menggunakan API createDocument
        closeAddDocumentModal();
        fetchDocuments(); // Refresh daftar dokumen
    } catch (error) {
        console.error('Error adding document:', error);
    }
};

// Fungsi untuk menutup modal
const closeAddDocumentModal = () => {
    showAddDocumentModal.value = false;
};
const confirmDeleteDocument = (documentId) => {
  Swal.fire({
    icon: 'warning',
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    customClass: { popup: 'sweet-alerts' },
  }).then(async (result) => {
    if (result.isConfirmed) {
      await deleteDocument(documentId);
      fetchDocuments();
    }
  });
};

//onmounted
onMounted(() => {
    fetchStudentDetail();
    fetchDocumentTypes(); 
});
</script>

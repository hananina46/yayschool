<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Student</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Student</button>
                    <div>
                        <input v-model="search" type="text" class="form-input" placeholder="Search..." />
                    </div>
                </div>
            </div>

            <div class="datatable">
                <vue3-datatable
                    :rows="rows"
                    :columns="cols"
                    :totalRows="rows?.length"
                    :sortable="true"
                    :search="search"
                    skin="whitespace-nowrap bh-table-hover"
                >
                    <template #class="data">
                        {{ data.value.class.name }}
                    </template>
                    <template #actions="data">
                        <button class="text-blue-500 hover:text-blue-700" @click="showStudentDetail(data.value.id)">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editStudent(data.value)">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="text-red-500 hover:text-red-700" @click="confirmDelete(data.value.id)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </template>
                </vue3-datatable>
            </div>
        </div>

        <!-- Modal for Create/Edit -->
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
                                    {{ editMode ? 'Edit Student' : 'Add New Student' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveStudent">
                                        <div class="form-group mb-4">
                                            <label for="studentName" class="block text-sm font-medium">Name</label>
                                            <input v-model="formData.name" type="text" id="studentName" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="studentEmail" class="block text-sm font-medium">Email</label>
                                            <input v-model="formData.email" type="email" id="studentEmail" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="classId" class="block text-sm font-medium">Class</label>
                                            <select v-model="formData.class_id" id="classId" class="form-input mt-1" required>
                                                <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                                    {{ classItem.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="nisn" class="block text-sm font-medium">NISN</label>
                                            <input v-model="formData.nisn" type="text" id="nisn" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="gender" class="block text-sm font-medium">Gender</label>
                                            <select v-model="formData.gender" id="gender" class="form-input mt-1">
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="dob" class="block text-sm font-medium">Date of Birth</label>
                                            <input v-model="formData.dob" type="date" id="dob" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="phone" class="block text-sm font-medium">Phone</label>
                                            <input v-model="formData.phone" type="text" id="phone" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="address" class="block text-sm font-medium">Address</label>
                                            <textarea v-model="formData.address" id="address" class="form-input mt-1"></textarea>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="profilePhoto" class="block text-sm font-medium">Profile Photo</label>
                                            <input @change="handleFileUpload" type="file" id="profilePhoto" class="form-input mt-1" />
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="button" @click="closeModal" class="btn btn-outline-danger">Cancel</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4">Save</button>
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

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';
import Swal from 'sweetalert2';
import { getStudents, createStudent, updateStudent, deleteStudent } from '@/api/student';
import { getSchoolClasses } from '@/api/classroom';
import { useMeta } from '@/composables/use-meta';
import { useRouter } from 'vue-router';

const router = useRouter();

const showStudentDetail = (studentId) => {
    router.push({ name: 'student_show', params: { studentId } });
};


useMeta({ title: 'Student' });

const search = ref('');
const rows = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Name', hide: false },
    { field: 'email', title: 'Email', hide: false },
    { field: 'class', title: 'Class', hide: false, slotName: 'class' },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({
    id: null,
    name: '',
    email: '',
    class_id: null,
    nisn: '',
    dob: '',
    gender: 'M',
    phone: '',
    address: '',
    profile_photo: null,
});
const classes = ref([]);

const fetchStudents = async () => {
    try {
        rows.value = await getStudents();
    } catch (error) {
        console.error('Failed to fetch students:', error);
    }
};

const fetchClasses = async () => {
    try {
        classes.value = await getSchoolClasses();
    } catch (error) {
        console.error('Failed to fetch classes:', error);
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.profile_photo = file;
    }
};

const showCreateModal = () => {
    editMode.value = false;
    formData.value = {
        id: null,
        name: '',
        email: '',
        class_id: null,
        nisn: '',
        dob: '',
        gender: 'M',
        phone: '',
        address: '',
        profile_photo: null,
    };
    showModal.value = true;
};

const editStudent = (studentData) => {
    editMode.value = true;
    formData.value = {
        id: studentData.id,
        name: studentData.name,
        email: studentData.email,
        class_id: studentData.class.id,
        nisn: studentData.nisn,
        dob: studentData.dob,
        gender: studentData.gender,
        phone: studentData.phone,
        address: studentData.address,
        profile_photo: null,
    };
    showModal.value = true;
};

const saveStudent = async () => {
    try {
        if (editMode.value) {
            await updateStudent(formData.value.id, formData.value);
        } else {
            await createStudent(formData.value);
        }
        fetchStudents();
        closeModal();
    } catch (error) {
        console.error('Failed to save student:', error);
    }
};

const confirmDelete = (id) => {
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
            await deleteStudent(id);
            Swal.fire({
                title: 'Deleted!',
                text: 'The student has been deleted.',
                icon: 'success',
                customClass: { popup: 'sweet-alerts' },
            });
            fetchStudents();
        }
    });
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(() => {
    fetchStudents();
    fetchClasses();
});
</script>

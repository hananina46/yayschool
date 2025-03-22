<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Classroom</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Class</button>
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
                    <template #teacher="data">
                        {{ data.value.teacher.name }}
                    </template>
                    <template #actions="data">
                        <button class="text-blue-500 hover:text-blue-700" @click="viewClass(data.value.id)">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editClass(data.value)">
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
                                <div
                                    class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]"
                                >
                                    {{ editMode ? 'Edit Class' : 'Add New Class' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveClass">
                                        <div class="form-group mb-4">
                                            <label for="className" class="block text-sm font-medium">Class Name</label>
                                            <input v-model="formData.name" type="text" id="className" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="teacherId" class="block text-sm font-medium">Teacher</label>
                                            <select v-model="formData.teacher_id" id="teacherId" class="form-input mt-1" required>
                                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                                    {{ teacher.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="academicYearId" class="block text-sm font-medium">Academic Year</label>
                                            <select v-model="formData.academic_year_id" id="academicYearId" class="form-input mt-1" required>
                                                <option v-for="year in academicYears" :key="year.id" :value="year.id">
                                                    {{ year.name }}
                                                </option>
                                            </select>
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
import { getSchoolClasses, createSchoolClass, updateSchoolClass, deleteSchoolClass } from '@/api/classroom';
import { getTeachers } from '@/api/teacher';
import { getAcademicYears } from '@/api/academicYear';
import { useMeta } from '@/composables/use-meta';
import { useRouter } from 'vue-router';

const router = useRouter();

const viewClass = (id) => {
    router.push(`/view-classroom/${id}`);
};

useMeta({ title: 'Classroom' });

const search = ref('');
const rows = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Class Name', hide: false },
    { field: 'teacher', title: 'Teacher', hide: false, slotName: 'teacher' },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({ id: null, name: '', teacher_id: null, academic_year_id: null });
const teachers = ref([]);
const academicYears = ref([]);

const fetchClasses = async () => {
    try {
        rows.value = await getSchoolClasses();
    } catch (error) {
        console.error('Failed to fetch classes:', error);
    }
};

const fetchTeachers = async () => {
    try {
        teachers.value = await getTeachers();
    } catch (error) {
        console.error('Failed to fetch teachers:', error);
    }
};

const fetchAcademicYears = async () => {
    try {
        academicYears.value = await getAcademicYears();
    } catch (error) {
        console.error('Failed to fetch academic years:', error);
    }
};

const showCreateModal = () => {
    editMode.value = false;
    formData.value = { id: null, name: '', teacher_id: null, academic_year_id: null };
    showModal.value = true;
};

const editClass = (classData) => {
    editMode.value = true;
    formData.value = {
        id: classData.id,
        name: classData.name,
        teacher_id: classData.teacher.id,
        academic_year_id: classData.academic_year_id,
    };
    showModal.value = true;
};

const saveClass = async () => {
    try {
        if (editMode.value) {
            await updateSchoolClass(formData.value.id, formData.value);
        } else {
            await createSchoolClass(formData.value);
        }
        fetchClasses();
        closeModal();
    } catch (error) {
        console.error('Failed to save class:', error);
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
            await deleteClass(id);
            Swal.fire({
                title: 'Deleted!',
                text: 'The class has been deleted.',
                icon: 'success',
                customClass: { popup: 'sweet-alerts' },
            });
        }
    });
};

const deleteClass = async (id) => {
    try {
        await deleteSchoolClass(id);
        fetchClasses();
    } catch (error) {
        console.error('Failed to delete class:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(() => {
    fetchClasses();
    fetchTeachers();
    fetchAcademicYears();
});
</script>

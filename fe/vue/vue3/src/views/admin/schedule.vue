<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Schedule</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Schedule</button>
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
                    <template #subject="data">
                        {{ data.value.subject.name }}
                    </template>
                    <template #teacher="data">
                        {{ data.value.teacher.name }}
                    </template>
                    <template #actions="data">
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editSchedule(data.value)">
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
                                    {{ editMode ? 'Edit Schedule' : 'Add New Schedule' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveSchedule">
                                        <div class="form-group mb-4">
                                            <label for="classId" class="block text-sm font-medium">Class</label>
                                            <select v-model="formData.class_id" id="classId" class="form-input mt-1" required>
                                                <option v-for="classItem in classes" :key="classItem.id" :value="classItem.id">
                                                    {{ classItem.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="subjectId" class="block text-sm font-medium">Subject</label>
                                            <select v-model="formData.subject_id" id="subjectId" class="form-input mt-1" required>
                                                <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                                                    {{ subject.name }}
                                                </option>
                                            </select>
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
                                            <label for="day" class="block text-sm font-medium">Day</label>
                                            <input v-model="formData.day" type="text" id="day" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="startTime" class="block text-sm font-medium">Start Time</label>
                                            <input v-model="formData.start_time" type="time" id="startTime" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="endTime" class="block text-sm font-medium">End Time</label>
                                            <input v-model="formData.end_time" type="time" id="endTime" class="form-input mt-1" required />
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
import { getSchedules, createSchedule, updateSchedule, deleteSchedule } from '@/api/schedule';
import { getTeachers } from '@/api/teacher';
import { getSubjects } from '@/api/subject';
import { getSchoolClasses } from '@/api/classroom';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Schedule' });

const search = ref('');
const rows = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'class', title: 'Class', hide: false, slotName: 'class' },
    { field: 'subject', title: 'Subject', hide: false, slotName: 'subject' },
    { field: 'teacher', title: 'Teacher', hide: false, slotName: 'teacher' },
    { field: 'day', title: 'Day', hide: false },
    { field: 'start_time', title: 'Start Time', hide: false },
    { field: 'end_time', title: 'End Time', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({ id: null, class_id: null, subject_id: null, teacher_id: null, day: '', start_time: '', end_time: '' });
const teachers = ref([]);
const subjects = ref([]);
const classes = ref([]);

const fetchData = async () => {
    try {
        rows.value = await getSchedules();
        teachers.value = await getTeachers();
        subjects.value = await getSubjects();
        classes.value = await getSchoolClasses();
    } catch (error) {
        console.error('Failed to fetch data:', error);
    }
};

// ** Function to show Create Modal **
const showCreateModal = () => {
    editMode.value = false;
    formData.value = { id: null, class_id: null, subject_id: null, teacher_id: null, day: '', start_time: '', end_time: '' };
    showModal.value = true;
};

// ** Function to Edit Schedule **
const editSchedule = (schedule) => {
    editMode.value = true;
    formData.value = { 
        id: schedule.id, 
        class_id: schedule.class.id, 
        subject_id: schedule.subject.id, 
        teacher_id: schedule.teacher.id, 
        day: schedule.day, 
        start_time: schedule.start_time, 
        end_time: schedule.end_time 
    };
    showModal.value = true;
};

// ** Function to Save (Create/Update) Schedule **
const saveSchedule = async () => {
    try {
        if (editMode.value) {
            await updateSchedule(formData.value.id, formData.value);
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Schedule has been updated successfully.',
                timer: 2000,
                showConfirmButton: false,
            });
        } else {
            await createSchedule(formData.value);
            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: 'New schedule has been created successfully.',
                timer: 2000,
                showConfirmButton: false,
            });
        }
        fetchData();
        closeModal();
    } catch (error) {
        console.error('Failed to save schedule:', error);
    }
};

// ** Function to Confirm & Delete Schedule **
const confirmDelete = (id) => {
    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
    }).then(async (result) => {
        if (result.isConfirmed) {
            await deleteSchedule(id);
            Swal.fire({
                title: 'Deleted!',
                text: 'The schedule has been deleted.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
            fetchData();
        }
    });
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(fetchData);
</script>



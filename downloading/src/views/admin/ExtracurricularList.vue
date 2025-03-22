<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Extracurricular List</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Extracurricular</button>
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
                    <template #actions="data">
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editExtracurricular(data.value)">
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
                                    {{ editMode ? 'Edit Extracurricular' : 'Add New Extracurricular' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveExtracurricular">
                                        <div class="form-group mb-4">
                                            <label for="name" class="block text-sm font-medium">Extracurricular Name</label>
                                            <input v-model="formData.name" type="text" id="name" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="supervisor" class="block text-sm font-medium">Supervisor</label>
                                            <select v-model="formData.supervisor_id" id="supervisor" class="form-input mt-1" required>
                                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                                    {{ teacher.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="description" class="block text-sm font-medium">Description</label>
                                            <textarea v-model="formData.description" id="description" class="form-input mt-1"></textarea>
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

<script setup>
import { ref, onMounted } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';
import Swal from 'sweetalert2';
import { getExtracurriculars, createExtracurricular, updateExtracurricular, deleteExtracurricular } from '@/api/extracurricularList';
import { getTeachers } from '@/api/teacher';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Extracurricular List' });

const search = ref('');
const rows = ref([]);
const teachers = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Extracurricular Name', hide: false },
    { field: 'supervisor.name', title: 'Supervisor', hide: false },
    { field: 'description', title: 'Description', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({
    id: null,
    name: '',
    supervisor_id: '',
    description: '',
});

const fetchExtracurriculars = async () => {
    try {
        rows.value = await getExtracurriculars();
    } catch (error) {
        console.error('Failed to fetch extracurriculars:', error);
    }
};

const confirmDelete = async (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await deleteExtracurricular(id);
                fetchExtracurriculars(); // Refresh list
                Swal.fire('Deleted!', 'The extracurricular has been deleted.', 'success');
            } catch (error) {
                console.error('Failed to delete extracurricular:', error);
                Swal.fire('Error!', 'Failed to delete extracurricular.', 'error');
            }
        }
    });
};


const fetchTeachers = async () => {
    try {
        teachers.value = await getTeachers();
    } catch (error) {
        console.error('Failed to fetch teachers:', error);
    }
};

const showCreateModal = () => {
    editMode.value = false;
    formData.value = {
        id: null,
        name: '',
        supervisor_id: '',
        description: '',
    };
    showModal.value = true;
};

const editExtracurricular = (extracurricularData) => {
    editMode.value = true;
    formData.value = {
        id: extracurricularData.id,
        name: extracurricularData.name,
        supervisor_id: extracurricularData.supervisor.id,
        description: extracurricularData.description,
    };
    showModal.value = true;
};

const saveExtracurricular = async () => {
    try {
        if (editMode.value) {
            await updateExtracurricular(formData.value.id, formData.value);
        } else {
            await createExtracurricular(formData.value);
        }
        fetchExtracurriculars();
        closeModal();
    } catch (error) {
        console.error('Failed to save extracurricular:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(() => {
    fetchExtracurriculars();
    fetchTeachers();
});
</script>

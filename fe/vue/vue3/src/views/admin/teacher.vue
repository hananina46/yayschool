<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Teacher</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Teacher</button>
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
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editTeacher(data.value)">
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
                                    {{ editMode ? 'Edit Teacher' : 'Add New Teacher' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveTeacher">
                                        <div class="form-group mb-4">
                                            <label for="teacherName" class="block text-sm font-medium">Name</label>
                                            <input v-model="formData.name" type="text" id="teacherName" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="teacherEmail" class="block text-sm font-medium">Email</label>
                                            <input v-model="formData.email" type="email" id="teacherEmail" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="teacherPhone" class="block text-sm font-medium">Phone</label>
                                            <input v-model="formData.phone" type="text" id="teacherPhone" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="teacherAddress" class="block text-sm font-medium">Address</label>
                                            <input v-model="formData.address" type="text" id="teacherAddress" class="form-input mt-1" />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="profilePhoto" class="block text-sm font-medium">Profile Photo</label>
                                            <input type="file" id="profilePhoto" class="form-input mt-1" @change="handleFileChange" />
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
import { getTeachers, createTeacher, updateTeacher, deleteTeacher } from '@/api/teacher';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Teacher' });

const search = ref('');
const rows = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Name', hide: false },
    { field: 'email', title: 'Email', hide: false },
    { field: 'phone', title: 'Phone', hide: false },
    { field: 'address', title: 'Address', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({ id: null, name: '', email: '', phone: '', address: '', profile_photo: null });

const fetchTeachers = async () => {
    try {
        rows.value = await getTeachers();
    } catch (error) {
        console.error('Failed to fetch teachers:', error);
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        formData.value.profile_photo = file;
    }
};

const showCreateModal = () => {
    editMode.value = false;
    formData.value = { id: null, name: '', email: '', phone: '', address: '', profile_photo: null };
    showModal.value = true;
};

const editTeacher = (teacher) => {
    editMode.value = true;
    formData.value = { id: teacher.id, name: teacher.name, email: teacher.email, phone: teacher.phone, address: teacher.address };
    showModal.value = true;
};

const saveTeacher = async () => {
    try {
        if (editMode.value) {
            await updateTeacher(formData.value.id, formData.value);
        } else {
            await createTeacher(formData.value);
        }
        fetchTeachers();
        closeModal();
    } catch (error) {
        console.error('Failed to save teacher:', error);
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
            try {
                await deleteTeacher(id);
                fetchTeachers();
                Swal.fire('Deleted!', 'Teacher has been deleted.', 'success');
            } catch (error) {
                console.error('Failed to delete teacher:', error);
            }
        }
    });
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(() => {
    fetchTeachers();
});
</script>

<style scoped>
/* Tambahkan styling jika perlu */
</style>

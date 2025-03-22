<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
                <h5 class="font-semibold text-lg dark:text-white-light">Bill Types</h5>
                <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
                    <button @click="showCreateModal" class="btn btn-primary">Add New Bill Type</button>
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
                        <button class="text-yellow-500 hover:text-yellow-700" @click="editBillType(data.value)">
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
                                    {{ editMode ? 'Edit Bill Type' : 'Add New Bill Type' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveBillType">
                                        <div class="form-group mb-4">
                                            <label for="billTypeName" class="block text-sm font-medium">Bill Type Name</label>
                                            <input v-model="formData.name" type="text" id="billTypeName" class="form-input mt-1" required />
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="amount" class="block text-sm font-medium">Amount</label>
                                            <input v-model="formData.amount" type="number" min="0" id="amount" class="form-input mt-1" required />
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
import { getBillTypes, createBillType, updateBillType, deleteBillType } from '@/api/billType';
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Bill Types' });

const search = ref('');
const rows = ref([]);
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'name', title: 'Bill Type Name', hide: false },
    { field: 'amount', title: 'Amount', hide: false },
    { field: 'description', title: 'Description', hide: false },
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);

const showModal = ref(false);
const editMode = ref(false);
const formData = ref({
    id: null,
    name: '',
    amount: '',
    description: '',
});

const fetchBillTypes = async () => {
    try {
        rows.value = await getBillTypes();
    } catch (error) {
        console.error('Failed to fetch bill types:', error);
    }
};

const showCreateModal = () => {
    editMode.value = false;
    formData.value = {
        id: null,
        name: '',
        amount: '',
        description: '',
    };
    showModal.value = true;
};

const editBillType = (billTypeData) => {
    editMode.value = true;
    formData.value = {
        id: billTypeData.id,
        name: billTypeData.name,
        amount: billTypeData.amount,
        description: billTypeData.description,
    };
    showModal.value = true;
};

const saveBillType = async () => {
    try {
        if (editMode.value) {
            await updateBillType(formData.value.id, formData.value);
        } else {
            await createBillType(formData.value);
        }
        fetchBillTypes();
        closeModal();
    } catch (error) {
        console.error('Failed to save bill type:', error);
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
            await deleteBillType(id);
            fetchBillTypes();
        }
    });
};

const closeModal = () => {
    showModal.value = false;
};

onMounted(() => {
    fetchBillTypes();
});
</script>

<template>
  <div>
    <!-- Header & DataTable -->
    <div class="panel pb-0 mt-6">
      <div class="flex md:items-center md:flex-row flex-col mb-5 gap-5">
        <h5 class="font-semibold text-lg dark:text-white-light">Grades</h5>
        <div class="flex items-center gap-5 ltr:ml-auto rtl:mr-auto">
          <button @click="showCreateModal" class="btn btn-primary">Add New Grade</button>
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
            <button class="text-yellow-500 hover:text-yellow-700" @click="editGrade(data.value)">
              <ion-icon name="create-outline"></ion-icon>
            </button>
            <button class="text-red-500 hover:text-red-700" @click="confirmDelete(data.value.id)">
              <ion-icon name="trash-outline"></ion-icon>
            </button>
          </template>
        </vue3-datatable>
      </div>
    </div>

    <!-- Modal Create/Edit -->
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
                <div class="text-lg font-bold bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3">
                  {{ editMode ? 'Edit Grade' : 'Add New Grade' }}
                </div>
                <div class="p-5">
                  <form @submit.prevent="saveGrade">
                    <!-- Student Selection -->
                    <div class="form-group mb-4">
                      <label for="student" class="block text-sm font-medium">Student</label>
                      <select v-model="formData.student_id" id="student" class="form-input mt-1" required>
                        <option v-for="student in students" :key="student.id" :value="student.id">
                          {{ student.name }}
                        </option>
                      </select>
                    </div>
                    <!-- Subject Selection -->
                    <div class="form-group mb-4">
                      <label for="subject" class="block text-sm font-medium">Subject</label>
                      <select
                        v-model="formData.subject_id"
                        id="subject"
                        class="form-input mt-1"
                        required
                        @change="filterGradeTypes"
                      >
                        <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                          {{ subject.name }}
                        </option>
                      </select>
                    </div>
                    <!-- Grade Type (Dynamic Filtering) -->
                    <div class="form-group mb-4">
                      <label for="gradeType" class="block text-sm font-medium">Grade Type</label>
                      <select
                        v-model="formData.grade_type_id"
                        id="gradeType"
                        class="form-input mt-1"
                        :disabled="filteredGradeTypes.length === 0"
                      >
                        <option v-for="gradeType in filteredGradeTypes" :key="gradeType.id" :value="gradeType.id">
                          {{ gradeType.name }}
                        </option>
                      </select>
                      <p v-if="filteredGradeTypes.length === 0" class="text-red-500 text-sm mt-1">
                        No grade types available for this subject.
                      </p>
                    </div>
                    <!-- Academic Year Selection -->
<!-- Academic Year Selection -->
<div class="form-group mb-4">
  <label for="academicYear" class="block text-sm font-medium">Academic Year</label>
  <select v-model="formData.academic_years" id="academicYear" class="form-input mt-1" required>
    <option v-for="year in academicYears" :key="year.id" :value="year.id">
      {{ year.name }}
    </option>
  </select>
</div>


                    <!-- Score & Remarks -->
                    <div class="form-group mb-4">
                      <label for="score" class="block text-sm font-medium">Score</label>
                      <input v-model="formData.score" type="number" id="score" class="form-input mt-1" required />
                    </div>
                    <div class="form-group mb-4">
                      <label for="remarks" class="block text-sm font-medium">Remarks</label>
                      <input v-model="formData.remarks" type="text" id="remarks" class="form-input mt-1" />
                    </div>
                    <!-- Action Buttons -->
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
import { ref, onMounted, watch } from 'vue';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, DialogPanel } from '@headlessui/vue';
import Swal from 'sweetalert2';
import { getGrades, createGrade, updateGrade, deleteGrade } from '@/api/grade';
import { getStudents } from '@/api/student';
import { getSubjects } from '@/api/subject';
import { getGradeTypes } from '@/api/gradeTypes';
import { getAcademicYears } from '@/api/academicYear';


// Data Binding
const academicYears = ref([]);
const search = ref('');
const rows = ref([]);
const students = ref([]);
const subjects = ref([]);
const gradeTypes = ref([]);
const filteredGradeTypes = ref([]);

// Data Modal
const showModal = ref(false);
const editMode = ref(false);
const formData = ref({
    id: null,
    student_id: '',
    class_id: '',
    subject_id: '',
    grade_type_id: '',
    academic_years: '', // Ubah dari academic_year_id ke academic_years
    score: '',
    remarks: ''
});



// Kolom untuk DataTable
const cols = ref([
    { field: 'id', title: 'ID', isUnique: true, hide: false },
    { field: 'student.name', title: 'Student Name', hide: false },
    { field: 'subject.name', title: 'Subject', hide: false },
    { field: 'grade_type.name', title: 'Grade Type', hide: false },
    { field: 'score', title: 'Score', hide: false },
    { field: 'remarks', title: 'Remarks', hide: false },
    { field: 'academic_year.name', title: 'Academic Year', hide: false }, // Tambahkan kolom tahun akademik
    { field: 'actions', title: 'Actions', hide: false, slotName: 'actions' },
]);


// Fetch Data dari API
const fetchData = async () => {
    try {
        rows.value = await getGrades();
        students.value = await getStudents();
        subjects.value = await getSubjects();
        gradeTypes.value = await getGradeTypes();
        academicYears.value = await getAcademicYears();
        console.log('Grades:', rows.value);
    } catch (error) {
        console.error('Failed to fetch data:', error);
    }
};

// Fungsi untuk Menampilkan Modal Tambah
const showCreateModal = () => {
    editMode.value = false;
    formData.value = { id: null, student_id: '', class_id: '', subject_id: '', grade_type_id: '', score: '', remarks: '' };
    filteredGradeTypes.value = [];
    showModal.value = true;
};

// Fungsi untuk Menampilkan Modal Edit
const editGrade = (grade) => {
    editMode.value = true;
    formData.value = {
        id: grade.id,
        student_id: grade.student.id,
        class_id: grade.class.id,
        subject_id: grade.subject.id,
        grade_type_id: grade.grade_type ? grade.grade_type.id : '',
        academic_years: grade.academic_year ? grade.academic_year.id : '', // Ubah dari academic_year_id ke academic_years
        score: grade.score,
        remarks: grade.remarks
    };
    filterGradeTypes();
    showModal.value = true;
};


// Fungsi untuk Mengisi `class_id` Secara Otomatis Berdasarkan Siswa yang Dipilih
const updateClassId = () => {
    const selectedStudent = students.value.find(student => student.id === formData.value.student_id);
    formData.value.class_id = selectedStudent ? selectedStudent.class_id : '';
};

// Fungsi untuk Filter Grade Type Berdasarkan Subject yang Dipilih
const filterGradeTypes = () => {
    if (formData.value.subject_id) {
        filteredGradeTypes.value = gradeTypes.value.filter(gt => gt.subject.id === formData.value.subject_id);
    } else {
        filteredGradeTypes.value = [];
    }
};

// Fungsi untuk Menyimpan Data (Tambah/Edit)
const saveGrade = async () => {
    console.log('Saving grade:', formData.value);
    try {
        updateClassId(); // Pastikan class_id diambil dari student sebelum menyimpan

        if (editMode.value) {
            await updateGrade(formData.value.id, formData.value); // Pastikan academic_years dikirim
            Swal.fire('Success', 'Grade updated successfully!', 'success');
        } else {
            await createGrade(formData.value); // Pastikan academic_years dikirim
            Swal.fire('Success', 'Grade created successfully!', 'success');
        }
        fetchData();
        closeModal();
    } catch (error) {
        console.error('Failed to save grade:', error);
        Swal.fire('Error', 'Failed to save grade!', 'error');
    }
};


// Fungsi untuk Konfirmasi dan Hapus Data
const confirmDelete = (id) => {
    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await deleteGrade(id);
                fetchData();
                Swal.fire('Deleted!', 'Grade has been deleted.', 'success');
            } catch (error) {
                console.error('Failed to delete grade:', error);
                Swal.fire('Error', 'Failed to delete grade!', 'error');
            }
        }
    });
};

// Fungsi untuk Menutup Modal
const closeModal = () => {
    showModal.value = false;
};

// Watcher untuk Mengupdate Grade Types Saat Subject Dipilih
watch(() => formData.value.subject_id, filterGradeTypes);

// Watcher untuk Mengupdate Class ID Saat Student Dipilih
watch(() => formData.value.student_id, updateClassId);

// Fetch Data Saat Komponen Dimuat
onMounted(() => {
    fetchData();
});
</script>
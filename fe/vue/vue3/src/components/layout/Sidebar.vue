<template>
    <div :class="{ 'dark text-white-dark': store.semidark }">
      <nav class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300">
        <div class="bg-white dark:bg-[#0e1726] h-full">
          <div class="flex justify-between items-center px-4 py-3">
            <router-link to="/" class="main-logo flex items-center shrink-0">
              <img class="w-8 ml-[5px] flex-none" src="/assets/images/yay.png" alt="" />
              <span class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-semibold align-middle lg:inline dark:text-white-light">YaySchool</span>
            </router-link>
            <a
              href="javascript:;"
              class="collapse-icon w-8 h-8 rounded-full flex items-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light transition duration-300 rtl:rotate-180 hover:text-primary"
              @click="store.toggleSidebar()"
            >
              <ion-icon name="chevron-down" class="m-auto rotate-90"></ion-icon>
            </a>
          </div>
          <perfect-scrollbar
            :options="{
              swipeEasing: true,
              wheelPropagation: false,
            }"
            class="h-[calc(100vh-80px)] relative"
          >
            <ul class="relative font-semibold space-y-0.5 p-4 py-0">
              <template v-for="(section, sectionIndex) in menu">
                <h2 v-if="section.title" class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                  <ion-icon name="minus" class="w-4 h-5 flex-none hidden" />
                  <span>{{ section.title }}</span>
                </h2>
  
                <li v-for="(item, itemIndex) in section.items" :key="itemIndex" class="menu nav-item">
                  <router-link
                    :to="item.link"
                    class="group w-full"
                    @click="toggleMobileMenu"
                  >
                    <div class="flex items-center">
                      <ion-icon :name="item.icon" class="group-hover:!text-primary shrink-0"></ion-icon>
                      <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{ item.label }}</span>
                    </div>
                  </router-link>
                </li>
              </template>
            </ul>
          </perfect-scrollbar>
        </div>
      </nav>
    </div>
  </template>
  
  <script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import { useAppStore } from '@/stores/index';
  
    const store = useAppStore();
  
    // Defining menu structure
    const menu = ref([
      {
        title: 'School Setting',
        items: [
          { label: 'Academic Year', link: '/school/academic-year', icon: 'school' },
          { label: 'Classroom', link: '/school/class', icon: 'school' },
          { label: 'Subject', link: '/school/subject', icon: 'book' },
          { label: 'Schedule', link: '/school/schedule', icon: 'calendar' },
        ],
      },
      {
        title: 'Personal Data',
        items: [
          { label: 'Teacher', link: '/school/teacher', icon: 'person' },
          { label: 'Student', link: '/school/student', icon: 'person' },
          { label: 'Guardian', link: '/school/guardian', icon: 'person' },
        ],
      },
      {
        title: 'Presence',
        items: [
          { label: 'Attendance', link: '/school/attendance', icon: 'checkmark-circle' },
        ],
      },
      {
        title: 'Grade',
        items: [
          { label: 'Grade Types', link: '/school/grade-types', icon: 'school' },
          { label: 'Grade Book', link: '/school/grade', icon: 'book' },
        ],
      },
      {
        title: 'Billing and Payment',
        items: [
          { label: 'Bill Types', link: '/school/bill-types', icon: 'cash' },
          { label: 'Assign Student Bill', link: '/school/assign-student-bill', icon: 'cash' },
          { label: 'Select Student Bill', link: '/school/select-student-bill', icon: 'cash' },
        ],
      },
      {
        title: 'Document Types',
        items: [
          { label: 'Document Types', link: '/school/document-types', icon: 'document' },
        ],
      },
      {
  title: 'Extracurricular',
  items: [
    { label: 'Extracurricular List', link: '/school/extracurricular-list', icon: 'trophy' },
    { label: 'Extracurricular Members', link: '/school/extracurricular-members', icon: 'people' },
    { label: 'Extracurricular Grades', link: '/school/extracurricular-grades', icon: 'clipboard' },
  ], 
},
      {
        title: 'Supports',
        items: [
          { label: 'Documentation', link: 'https://vristo.sbthemes.com', icon: 'document-text' },
        ],
      },
    ]);
  
    const toggleMobileMenu = () => {
      if (window.innerWidth < 1024) {
        store.toggleSidebar();
      }
    };
  
    onMounted(() => {
      const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
      if (selector) {
        selector.classList.add('active');
        const ul: any = selector.closest('ul.sub-menu');
        if (ul) {
          let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link') || [];
          if (ele.length) {
            ele = ele[0];
            setTimeout(() => {
              ele.click();
            });
          }
        }
      }
    });
  </script>
  
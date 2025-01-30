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
                        <icon-carets-down class="m-auto rotate-90" />
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
                        <li class="menu nav-item">
                            <button
                                type="button"
                                class="nav-link group w-full"
                                :class="{ active: activeDropdown === 'dashboard' }"
                                @click="activeDropdown === 'dashboard' ? (activeDropdown = null) : (activeDropdown = 'dashboard')"
                            >
                                <div class="flex items-center">
                                    <icon-menu-dashboard class="group-hover:!text-primary shrink-0" />
                                    <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                                        {{ $t('dashboard') }}
                                    </span>
                                </div>
                                <div :class="{ 'rtl:rotate-90 -rotate-90': activeDropdown !== 'dashboard' }">
                                    <icon-caret-down />
                                </div>
                            </button>
                            <vue-collapsible :isOpen="activeDropdown === 'dashboard'">
                                <ul class="sub-menu text-gray-500">
                                    <li>
                                        <router-link to="/school/dashboard" @click="toggleMobileMenu">{{ $t('dashboard') }}</router-link>
                                    </li>

                                </ul>
                            </vue-collapsible>
                        </li>
                        <h2 class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                            <icon-minus class="w-4 h-5 flex-none hidden" />
                            <span>{{ $t('School Setting') }}</span>
                        </h2>
                        <li class="nav-item">
                            <ul>
                                <li class="nav-item">
                                    <router-link to="/school/academic-year" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-year class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Academic Year')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                
                                <li class="nav-item">
                                    <router-link to="/school/class" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-class class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Classroom')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/school/subject" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-subject class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Subject')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/school/schedule" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-schedule class="group-hover:!text-primary shrink-0" />
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Schedule')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                <!--academic year-->
                                
                            </ul>
                        </li>

                        <h2 class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                            <icon-minus class="w-4 h-5 flex-none hidden" />
                            <span>{{ $t('Personal Data') }}</span>
                        </h2>

                        <li class="menu nav-item">
                            <li class="nav-item">
                                    <router-link to="/school/teacher" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-teacher class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Teacher')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/school/student" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-student class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Student')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/school/guardian" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-guardian class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Guardian')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>
                        </li>   
                        <h2 class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                            <icon-minus class="w-4 h-5 flex-none hidden" />
                            <span>{{ $t('Presence') }}</span>
                        </h2>
                        <li class="menu nav-item">
                            <li class="nav-item">
                                    <router-link to="/school/attendance" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <icon-menu-attendance class="group-hover:!text-primary shrink-0" />

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('Attendance')
                                            }}</span>
                                        </div>
                                    </router-link>
                            </li>


                        </li>
                        <h2 class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                            <icon-minus class="w-4 h-5 flex-none hidden" />
                            <span>{{ $t('supports') }}</span>
                        </h2>

                        <li class="menu nav-item">
                            <a href="https://vristo.sbthemes.com" target="_blank" class="nav-link group">
                                <div class="flex items-center">
                                    <icon-menu-documentation class="group-hover:!text-primary shrink-0" />

                                    <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                        $t('documentation')
                                    }}</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </perfect-scrollbar>
            </div>
        </nav>
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';

    import { useAppStore } from '@/stores/index';
    import VueCollapsible from 'vue-height-collapsible/vue3';

    import IconCaretsDown from '@/components/icon/icon-carets-down.vue';
    import IconMenuDashboard from '@/components/icon/menu/icon-menu-dashboard.vue';
    import IconMinus from '@/components/icon/icon-minus.vue';
    import IconClass from '@/components/icon/icon-classroom.vue';
    import IconMenuChat from '@/components/icon/menu/icon-menu-chat.vue';
    import IconMenuMailbox from '@/components/icon/menu/icon-menu-mailbox.vue';
    import IconMenuTodo from '@/components/icon/menu/icon-menu-todo.vue';
    import IconMenuNotes from '@/components/icon/menu/icon-menu-notes.vue';
    import IconMenuScrumboard from '@/components/icon/menu/icon-menu-scrumboard.vue';
    import IconMenuContacts from '@/components/icon/menu/icon-menu-contacts.vue';
    import IconMenuInvoice from '@/components/icon/menu/icon-menu-invoice.vue';
    import IconCaretDown from '@/components/icon/icon-caret-down.vue';
    import IconMenuCalendar from '@/components/icon/menu/icon-menu-calendar.vue';
    import IconMenuClass from '@/components/icon/menu/icon-menu-class.vue';
    import IconMenuYear from '@/components/icon/menu/icon-menu-year.vue';
    import iconMenuTeacher from '../icon/menu/icon-menu-teacher.vue';
    import iconMenuStudent from '../icon/menu/icon-menu-student.vue';
    import iconMenuGuardian from '../icon/menu/icon-menu-guardian.vue';
    import iconMenuSubject from '../icon/menu/icon-menu-subject.vue';
    import iconMenuSchedule from '../icon/menu/icon-menu-schedule.vue';
    import iconMenuAttendance from '../icon/menu/icon-menu-attendance.vue';
    import IconMenuDocumentation from '@/components/icon/menu/icon-menu-documentation.vue';

    const store = useAppStore();
    const activeDropdown: any = ref('');
    const subActive: any = ref('');

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

    const toggleMobileMenu = () => {
        if (window.innerWidth < 1024) {
            store.toggleSidebar();
        }
    };
</script>

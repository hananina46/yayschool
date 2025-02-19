<template>
    <div>
        <!-- Background Overlay -->
        <div
            class="fixed inset-0 bg-[black]/60 z-[51] px-4 hidden transition-[display]"
            :class="{ '!block': showCustomizer }"
            @click="showCustomizer = false"
        ></div>

        <!-- Sidebar Chat -->
        <nav
            class="bg-white fixed ltr:-right-[400px] rtl:-left-[400px] top-0 bottom-0 w-full max-w-[400px] shadow-lg transition-[right] duration-300 z-[51] dark:bg-[#0e1726] p-4 flex flex-col"
            :class="{ 'ltr:!right-0 rtl:!left-0': showCustomizer }"
        >
            <!-- Tombol Buka/Tutup -->
            <a
    href="javascript:;"
    class="bg-pink-500 absolute ltr:-left-12 rtl:-right-12 top-0 bottom-0 my-auto w-12 h-10 flex justify-center items-center text-white cursor-pointer rounded-full"
    @click="showCustomizer = !showCustomizer"
>
    <icon-chatting class="w-5 h-5" />
</a>


            <!-- Header -->
            <div class="text-center pb-5 border-b border-gray-200 dark:border-gray-700">
                <h4 class="mb-1 dark:text-white">AI Chat</h4>
                <p class="text-white-dark">Bicara dengan AI</p>
            </div>

            <!-- Chat Container -->
            <div class="flex-1 overflow-y-auto space-y-4 p-3" ref="chatContainer">
                <div v-for="(message, index) in messages" :key="index" class="flex items-start gap-3">
                    <!-- Avatar -->
                    <div class="w-8 h-8 flex justify-center items-center rounded-full" 
                         :class="message.role === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'">
                        {{ message.role === 'user' ? 'U' : 'AI' }}
                    </div>

                    <!-- Pesan -->
                    <div class="p-3 rounded-lg max-w-[75%]" 
                         :class="message.role === 'user' ? 'bg-blue-100 text-blue-900' : 'bg-gray-100 text-gray-900'">
                        <span v-html="formatMessage(message.content)"></span>
                    </div>
                </div>

                <!-- Loader -->
                <div v-if="loading" class="flex items-center gap-2 text-gray-500">
                    <span class="animate-pulse">AI sedang mengetik...</span>
                </div>
            </div>

            <!-- Input Chat -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-3">
                <div class="flex items-center gap-2">
                    <input 
                        v-model="userMessage" 
                        type="text" 
                        class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500" 
                        placeholder="Ketik pesan..." 
                        @keyup.enter="sendMessage"
                    />
                    <button 
                        class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                        @click="sendMessage"
                        :disabled="loading"
                    >
                        Kirim
                    </button>
                </div>
            </div>
        </nav>
    </div> 
</template>

<script lang="ts" setup>
import { ref, nextTick } from 'vue';
import { useAppStore } from '@/stores/index';
import IconSettings from '@/components/icon/icon-settings.vue';
import iconChatting from './icon/icon-chatting.vue';

// Ambil API URL dari .env
const API_URL = import.meta.env.VITE_BASE_URL + "/api/yay";

const store = useAppStore();
const showCustomizer = ref(false);
const userMessage = ref("");
const messages = ref<Array<{ role: string, content: string }>>([]);
const loading = ref(false);
const chatContainer = ref(null);

// Fungsi Kirim Pesan
const sendMessage = async () => {
    if (!userMessage.value.trim()) return;

    // Tambahkan pesan pengguna ke dalam chat
    messages.value.push({ role: "user", content: userMessage.value });

    // Simpan pesan yang dikirim
    const userText = userMessage.value;
    userMessage.value = "";

    // Scroll ke bawah otomatis
    await nextTick();
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;

    // Tampilkan loader
    loading.value = true;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ messages: [...messages.value] })
        });

        const data = await response.json();
        console.log(data);
        const aiResponse = data.content || "Maaf, terjadi kesalahan.";

        // Tambahkan pesan dari AI ke dalam chat
        messages.value.push({ role: "assistant", content: aiResponse });

        // Scroll ke bawah otomatis setelah AI membalas
        await nextTick();
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    } catch (error) {
        console.error("Chat Error:", error);
        messages.value.push({ role: "assistant", content: "Maaf, terjadi kesalahan." });
    } finally {
        loading.value = false;
    }
};

// Fungsi untuk Memformat Pesan AI
const formatMessage = (text: string): string => {
    // Ganti **teks** menjadi bold
    text = text.replace(/\*\*(.*?)\*\*/g, "<b>$1</b>");

    // Format list dengan angka atau bullet
    text = text.replace(/\d+\.\s/g, "<br>▪ ");
    text = text.replace(/(\n-|\n•)/g, "<br>▪ ");

    // Ubah \n menjadi <br> agar tampil sebagai paragraf
    return text.replace(/\n/g, "<br>");
};
</script>

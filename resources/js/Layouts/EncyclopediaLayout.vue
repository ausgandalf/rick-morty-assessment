<script setup>
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const serverOnline = ref(null);
let statusInterval = null;

async function checkServerStatus() {
    try {
        const { data } = await axios.get('/api/status');
        serverOnline.value = data.online;
    } catch {
        serverOnline.value = false;
    }
}

onMounted(() => {
    checkServerStatus();
    statusInterval = setInterval(checkServerStatus, 60_000);
});

onUnmounted(() => {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
});

const statusLightClass = computed(() => {
    if (serverOnline.value === null) {
        return 'bg-amber-500 animate-pulse';
    }

    return serverOnline.value ? 'bg-emerald-500' : 'bg-red-500';
});

const statusLabel = computed(() => {
    if (serverOnline.value === null) {
        return 'Checking…';
    }

    return serverOnline.value ? 'Online' : 'Offline';
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
                <Link href="/" class="text-xl font-bold text-emerald-400">
                    Rick & Morty Encyclopedia
                </Link>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400">Powered by the Rick and Morty API</span>
                    <div
                        class="flex items-center gap-1.5"
                        :title="`Server Status: ${statusLabel}`"
                        aria-live="polite"
                    >
                        <span class="text-xs text-slate-400">Server Status</span>
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="statusLightClass"
                            role="status"
                            :aria-label="`Server Status: ${statusLabel}`"
                        />
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <slot />
        </main>
    </div>
</template>
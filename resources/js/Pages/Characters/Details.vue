<script setup>
import EncyclopediaLayout from '@/Layouts/EncyclopediaLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    character: Object,
    error: String,
});
</script>

<template>
    <Head :title="character?.name ?? 'Character'" />

    <EncyclopediaLayout>
        <Link href="/" class="mb-6 inline-block text-emerald-400 hover:text-emerald-300">← Back to list</Link>

        <div
            v-if="error"
            class="rounded-xl border border-red-900/50 bg-red-950/30 px-6 py-12 text-center"
        >
            <p class="text-base font-medium text-red-300">{{ error }}</p>
            <button
                v-if="error !== 'Character not found.'"
                type="button"
                class="mt-4 rounded-lg bg-emerald-500 px-4 py-2 font-semibold text-slate-950 hover:bg-emerald-400"
                @click="router.reload()"
            >
                Try again
            </button>
        </div>

        <div v-else class="grid gap-8 md:grid-cols-2">
            <img :src="character.image" :alt="character.name" class="rounded-xl border border-slate-800" />

            <div>
                <h1 class="text-4xl font-bold">{{ character.name }}</h1>
                <dl class="mt-6 space-y-3">
                    <div><dt class="text-slate-400">Status</dt><dd>{{ character.status }}</dd></div>
                    <div><dt class="text-slate-400">Species</dt><dd>{{ character.species }}</dd></div>
                    <div><dt class="text-slate-400">Origin</dt><dd>{{ character.origin }}</dd></div>
                    <div><dt class="text-slate-400">Location</dt><dd>{{ character.location }}</dd></div>
                    <div><dt class="text-slate-400">Gender</dt><dd>{{ character.gender }}</dd></div>
                    <div><dt class="text-slate-400">Type</dt><dd>{{ character.type }}</dd></div>
                </dl>

                <h2 class="mt-8 text-xl font-semibold">Episodes</h2>
                <ul class="mt-3 max-h-64 space-y-2 overflow-y-auto text-sm text-slate-300">
                    <li v-for="episode in character.episodes" :key="episode.id">
                        {{ episode.episode }} — {{ episode.name }}
                    </li>
                </ul>
            </div>
        </div>
    </EncyclopediaLayout>
</template>

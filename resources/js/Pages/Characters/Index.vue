<script setup>
import EncyclopediaLayout from '@/Layouts/EncyclopediaLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    characters: Array,
    meta: Object,
    filters: Object,
    error: String,
});

const isLoading = ref(false);
const listKey = ref(0);
const errorMessage = ref(props.error ?? null);

watch(
    () => props.error,
    (error) => {
        errorMessage.value = error ?? null;
    },
);

const form = reactive({
    name: props.filters.name,
    status: props.filters.status,
    species: props.filters.species,
    gender: props.filters.gender,
    type: props.filters.type,
});

function fetchCharacters(params) {
    isLoading.value = true;
    errorMessage.value = null;

    router.get('/', params, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        },
        onSuccess: (page) => {
            listKey.value += 1;
            errorMessage.value = page.props.error ?? null;
        },
        onError: () => {
            errorMessage.value = 'Sorry, something went wrong, try to search again.';
        },
    });
}

function search() {
    fetchCharacters({ ...form, page: 1 });
}

function reset() {
    form.name = '';
    form.status = '';
    form.species = '';
    form.gender = '';
    form.type = '';

    fetchCharacters({});
}

function goToPage(page) {
    fetchCharacters({ ...form, page });
}

function statusClass(status) {
    return {
        Alive: 'bg-emerald-500',
        Dead: 'bg-red-500',
        unknown: 'bg-slate-500',
    }[status] || 'bg-slate-500';
}

function genderClass(gender) {
    return {
        Male: 'bg-blue-500',
        Female: 'bg-pink-500',
        Genderless: 'bg-gray-500',
        unknown: 'bg-slate-500',
    }[gender] || 'bg-slate-500';
}

const perPage = 20;

const showingFrom = computed(() => {
    if (props.meta.count === 0 || props.characters.length === 0) {
        return 0;
    }

    return (props.meta.current_page - 1) * perPage + 1;
});

const showingTo = computed(() => {
    if (props.meta.count === 0 || props.characters.length === 0) {
        return 0;
    }

    return showingFrom.value + props.characters.length - 1;
});
</script>

<template>
    <Head title="Characters" />

    <EncyclopediaLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Characters</h1>
            <p class="mt-2 text-slate-400">Browse, search, and filter the multiverse.</p>
        </div>

        <form @submit.prevent="search" class="mb-8 grid gap-4 rounded-xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-3">
            <input
                v-model="form.name"
                type="text"
                placeholder="Search name..."
                class="rounded-lg border-slate-700 bg-slate-950 px-3 py-2"
            />
            <select v-model="form.status" class="rounded-lg border-slate-700 bg-slate-950 px-3 py-2">
                <option value="">All statuses</option>
                <option value="alive">Alive</option>
                <option value="dead">Dead</option>
                <option value="unknown">Unknown</option>
            </select>
            <input
                v-model="form.species"
                type="text"
                placeholder="Species..."
                class="rounded-lg border-slate-700 bg-slate-950 px-3 py-2"
            />
            <select v-model="form.gender" class="rounded-lg border-slate-700 bg-slate-950 px-3 py-2">
                <option value="">All genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="genderless">Genderless</option>
                <option value="unknown">Unknown</option>
            </select>
            <input
                v-model="form.type"
                type="text"
                placeholder="Type..."
                class="rounded-lg border-slate-700 bg-slate-950 px-3 py-2"
            />
            <div class="grid gap-2 md:grid-cols-2">
                <button
                    type="submit"
                    class="rounded-lg bg-emerald-500 px-4 py-2 font-semibold text-slate-950 hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="isLoading"
                >
                    {{ isLoading ? 'Searching…' : 'Search' }}
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-slate-800 px-4 py-2 font-semibold text-slate-200 hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="isLoading"
                    @click="reset"
                >
                    Reset
                </button>
            </div>
        </form>

        <div v-if="!errorMessage" class="mb-6 flex flex-wrap items-center justify-between gap-2 text-sm text-slate-400">
            <span>
                <span class="font-semibold text-slate-200">{{ meta.count }}</span>
                {{ meta.count === 1 ? 'character' : 'characters' }} total
            </span>
            <span v-if="meta.count > 0">
                Showing
                <span class="font-semibold text-slate-200">{{ showingFrom }}–{{ showingTo }}</span>
                of
                <span class="font-semibold text-slate-200">{{ meta.count }}</span>
            </span>
            <span v-else>No results</span>
        </div>

        <div v-if="isLoading" class="rounded-xl border border-slate-800 bg-slate-900 px-6 py-16 text-center">
            <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-2 border-slate-700 border-t-emerald-400" />
            <p class="text-sm font-medium text-slate-300">Searching characters…</p>
            <p class="mt-1 text-xs text-slate-500">Fetching results from the multiverse</p>
        </div>

        <div
            v-else-if="errorMessage"
            class="rounded-xl border border-red-900/50 bg-red-950/30 px-6 py-12 text-center"
        >
            <p class="text-base font-medium text-red-300">{{ errorMessage }}</p>
            <button
                type="button"
                class="mt-4 rounded-lg bg-emerald-500 px-4 py-2 font-semibold text-slate-950 hover:bg-emerald-400"
                @click="search"
            >
                Try again
            </button>
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                v-for="(character, index) in characters"
                :key="`${listKey}-${character.id}`"
                :href="`/characters/${character.id}`"
                class="character-card overflow-hidden rounded-xl border border-slate-800 bg-slate-900 transition hover:border-emerald-500"
                :style="{ animationDelay: `${index * 70}ms` }"
            >
                <span
                    class="block px-3 py-1 text-center text-xs font-semibold uppercase tracking-wide text-white"
                    :class="genderClass(character.gender)"
                >
                    {{ character.gender }}
                </span>
                <img :src="character.image" :alt="character.name" class="h-48 w-full object-cover" />
                <div class="p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full" :class="statusClass(character.status)"></span>
                        <h2 class="font-semibold">{{ character.name }}</h2>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">{{ character.species }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ character.type }}</p>
                </div>
            </Link>
        </div>

        <div v-if="!isLoading && !errorMessage && characters.length === 0" class="rounded-xl border border-slate-800 bg-slate-900 p-8 text-center text-slate-400">
            No characters found.
        </div>

        <div class="mt-8 flex items-center justify-between">
            <button
                type="button"
                :disabled="!meta.prev || isLoading"
                class="rounded-lg bg-slate-800 px-4 py-2 disabled:opacity-40"
                @click="goToPage(meta.current_page - 1)"
            >
                Previous
            </button>
            <span class="text-sm text-slate-400">Page {{ meta.current_page }} of {{ meta.pages }}</span>
            <button
                type="button"
                :disabled="!meta.next || isLoading"
                class="rounded-lg bg-slate-800 px-4 py-2 disabled:opacity-40"
                @click="goToPage(meta.current_page + 1)"
            >
                Next
            </button>
        </div>
    </EncyclopediaLayout>
</template>

<style scoped>
@keyframes card-fade-in {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.character-card {
    opacity: 0;
    animation: card-fade-in 0.45s ease forwards;
}
</style>
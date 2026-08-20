<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Settings, Trash2 } from 'lucide-vue-next';
import { nextTick, onMounted, ref } from 'vue';
import ShoppingListLayout from '@/layouts/ShoppingListLayout.vue';

defineOptions({
    layout: ShoppingListLayout,
});

interface ShoppingList {
    id: number;
    name: string;
    budget_limit_in_pence: number | null;
    total_in_pence: number;
    is_over_budget: boolean;
}

const shoppingLists = ref<ShoppingList[]>([]);

const newListName = ref('');
const listNameInput = ref<HTMLInputElement | null>(null);

const showCreateForm = ref(false);

const isLoading = ref(true);
const isCreating = ref(false);

const error = ref('');
const successMessage = ref('');

const showAccessibilitySettings = ref(false);

const highContrast = ref(false);
const largeText = ref(false);
const reducedMotion = ref(false);
const increasedSpacing = ref(false);

const settingsCloseButton = ref<HTMLButtonElement | null>(null);

// Get all shopping lists
async function fetchShoppingLists() {
    error.value = '';

    try {
        const response = await fetch('/api/shopping-lists', {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Unable to load shopping lists.');
        }

        shoppingLists.value = await response.json();
    } catch {
        error.value = 'Unable to load shopping lists.';
    } finally {
        isLoading.value = false;
    }
}

// Delete shopping list
async function deleteShoppingList(shoppingList: ShoppingList) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${shoppingList.name}"?`,
    );

    if (!confirmed) {
        return;
    }

    error.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(
            `/api/shopping-lists/${shoppingList.id}`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        if (!response.ok) {
            throw new Error('Unable to delete shopping list.');
        }

        shoppingLists.value = shoppingLists.value.filter(
            (list) => list.id !== shoppingList.id,
        );

        successMessage.value = `${shoppingList.name} deleted successfully.`;
    } catch {
        error.value = 'Unable to delete shopping list.';
    }
}

// Create shopping list
async function createShoppingList() {
    const name = newListName.value.trim();

    if (!name) {
        return;
    }

    isCreating.value = true;
    error.value = '';
    successMessage.value = '';

    try {
        const response = await fetch('/api/shopping-lists', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name,
            }),
        });

        if (!response.ok) {
            throw new Error('Unable to create shopping list.');
        }

        const shoppingList: ShoppingList = await response.json();

        shoppingLists.value.unshift(shoppingList);

        successMessage.value = `${shoppingList.name} created successfully.`;

        newListName.value = '';
        showCreateForm.value = false;
    } catch {
        error.value = 'Unable to create shopping list.';
    } finally {
        isCreating.value = false;
    }
}

async function toggleCreateForm() {
    showCreateForm.value = !showCreateForm.value;

    if (showCreateForm.value) {
        await nextTick();

        listNameInput.value?.focus();
    }
}

async function openCreateForm() {
    showCreateForm.value = true;

    await nextTick();

    listNameInput.value?.focus();
}

async function openAccessibilitySettings() {
    showAccessibilitySettings.value = true;

    await nextTick();

    settingsCloseButton.value?.focus();
}

function closeAccessibilitySettings() {
    showAccessibilitySettings.value = false;
}

function handleSettingsKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        closeAccessibilitySettings();
    }
}

// Returns money in correct format
function formatMoney(value: number | null | undefined) {
    if (value === null || value === undefined) {
        return '£0.00';
    }

    return `£${(Number(value) / 100).toFixed(2)}`;
}

onMounted(fetchShoppingLists);
</script>

<template>
    <main
        class="page"
        :class="{
        'high-contrast': highContrast,
        'large-text': largeText,
        'reduced-motion': reducedMotion,
        'increased-spacing': increasedSpacing,
    }"
    >
        <div class="container">
            <header class="header">
                <div>
                    <p class="eyebrow">Your shopping</p>

                    <h1>Shopping Lists</h1>

                    <p class="subtitle">
                        Manage your lists and keep track of your spending.
                    </p>
                </div>

                <div class="header-actions">
                    <button
                        type="button"
                        class="icon-button"
                        aria-label="Open accessibility settings"
                        aria-haspopup="dialog"
                        :aria-expanded="showAccessibilitySettings"
                        title="Accessibility settings"
                        @click="openAccessibilitySettings"
                    >
                        <Settings
                            :size="20"
                            aria-hidden="true"
                        />
                    </button>

                    <button
                        type="button"
                        class="primary-button"
                        aria-controls="create-list-form"
                        :aria-expanded="showCreateForm"
                        @click="toggleCreateForm"
                    >
                        <span
                            class="plus"
                            aria-hidden="true"
                        >
                            +
                        </span>

                        Create new list
                    </button>
                </div>
            </header>

            <section
                v-if="showCreateForm"
                id="create-list-form"
                class="create-card"
                aria-labelledby="create-list-heading"
            >
                <h2 id="create-list-heading">
                    Create shopping list
                </h2>

                <form @submit.prevent="createShoppingList">
                    <label for="list-name">
                        List name
                    </label>

                    <div class="create-row">
                        <input
                            id="list-name"
                            ref="listNameInput"
                            v-model="newListName"
                            type="text"
                            maxlength="255"
                            placeholder="e.g. Weekly shop"
                            autocomplete="off"
                            required
                        />

                        <button
                            type="submit"
                            class="primary-button"
                            :disabled="isCreating"
                        >
                            {{
                                isCreating
                                    ? 'Creating...'
                                    : 'Create'
                            }}
                        </button>
                    </div>
                </form>
            </section>

            <p
                v-if="error"
                class="error"
                role="alert"
            >
                {{ error }}
            </p>

            <p
                v-if="successMessage"
                class="sr-only"
                role="status"
                aria-live="polite"
            >
                {{ successMessage }}
            </p>

            <p
                v-if="isLoading"
                class="loading"
                role="status"
                aria-live="polite"
            >
                Loading shopping lists...
            </p>

            <section
                v-else
                class="lists"
                aria-labelledby="shopping-lists-heading"
            >
                <h2
                    id="shopping-lists-heading"
                    class="sr-only"
                >
                    Your shopping lists
                </h2>

                <article
                    v-for="shoppingList in shoppingLists"
                    :key="shoppingList.id"
                    class="list-card"
                >
                    <div class="list-content">
                        <div class="list-heading">
                            <h3>
                                {{ shoppingList.name }}
                            </h3>

                            <span
                                v-if="shoppingList.is_over_budget"
                                class="over-budget"
                                :aria-label="
                                    `${shoppingList.name} is over budget`
                                "
                            >
                                <span aria-hidden="true">
                                    ⚠
                                </span>

                                Over budget
                            </span>
                        </div>

                        <div class="list-details">
                            <span>
                                Total

                                <strong>
                                    {{
                                        formatMoney(
                                            shoppingList.total_in_pence,
                                        )
                                    }}
                                </strong>
                            </span>

                            <span
                                v-if="
                                    shoppingList.budget_limit_in_pence !==
                                        null &&
                                    shoppingList.budget_limit_in_pence !==
                                        undefined
                                "
                            >
                                Budget

                                <strong>
                                    {{
                                        formatMoney(
                                            shoppingList.budget_limit_in_pence,
                                        )
                                    }}
                                </strong>
                            </span>
                        </div>
                    </div>

                    <div class="list-actions">
                        <Link
                            :href="
                                `/shopping-lists/${shoppingList.id}`
                            "
                            class="view-button"
                            :aria-label="
                                `View shopping list ${shoppingList.name}`
                            "
                        >
                            View list

                            <span aria-hidden="true">
                                →
                            </span>
                        </Link>

                        <button
                            type="button"
                            class="delete-button"
                            :aria-label="
                                `Delete shopping list ${shoppingList.name}`
                            "
                            :title="
                                `Delete ${shoppingList.name}`
                            "
                            @click="
                                deleteShoppingList(shoppingList)
                            "
                        >
                            <Trash2
                                :size="19"
                                aria-hidden="true"
                            />
                        </button>
                    </div>
                </article>

                <div
                    v-if="shoppingLists.length === 0"
                    class="empty-state"
                >
                    <h3>
                        No shopping lists yet
                    </h3>

                    <p>
                        Create your first shopping list to get started.
                    </p>

                    <button
                        type="button"
                        class="primary-button"
                        @click="openCreateForm"
                    >
                        Create shopping list
                    </button>
                </div>
            </section>
        </div>
        <div
            v-if="showAccessibilitySettings"
            class="settings-overlay"
            @click.self="closeAccessibilitySettings"
            @keydown="handleSettingsKeydown"
        >
            <section
                class="settings-panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="accessibility-settings-heading"
                aria-describedby="accessibility-settings-description"
            >
                <header class="settings-header">
                    <div>
                        <p class="settings-eyebrow">
                            Display preferences
                        </p>

                        <h2 id="accessibility-settings-heading">
                            Accessibility settings
                        </h2>

                        <p id="accessibility-settings-description">
                            Adjust how the shopping list interface is displayed.
                        </p>
                    </div>

                    <button
                        ref="settingsCloseButton"
                        type="button"
                        class="settings-close"
                        aria-label="Close accessibility settings"
                        @click="closeAccessibilitySettings"
                    >
                        ×
                    </button>
                </header>

                <div class="settings-options">
                    <label class="setting-option">
                <span class="setting-description">
                    <strong>High contrast</strong>

                    <small>
                        Increase contrast between text, controls and backgrounds.
                    </small>
                </span>

                        <input
                            v-model="highContrast"
                            type="checkbox"
                        />
                    </label>

                    <label class="setting-option">
                <span class="setting-description">
                    <strong>Larger text</strong>

                    <small>
                        Increase text size throughout the shopping list.
                    </small>
                </span>

                        <input
                            v-model="largeText"
                            type="checkbox"
                        />
                    </label>

                    <label class="setting-option">
                <span class="setting-description">
                    <strong>Reduced motion</strong>

                    <small>
                        Remove non-essential movement and transitions.
                    </small>
                </span>

                        <input
                            v-model="reducedMotion"
                            type="checkbox"
                        />
                    </label>

                    <label class="setting-option">
                <span class="setting-description">
                    <strong>Increased spacing</strong>

                    <small>
                        Add more space between text and interface elements.
                    </small>
                </span>

                        <input
                            v-model="increasedSpacing"
                            type="checkbox"
                        />
                    </label>
                </div>

                <div class="accessibility-notes">
                    <h3>Built-in accessibility</h3>

                    <ul>
                        <li>Semantic HTML structure</li>
                        <li>Keyboard accessible controls</li>
                        <li>Associated form labels</li>
                        <li>Visible keyboard focus</li>
                        <li>Screen-reader announcements</li>
                        <li>WCAG 2.2 AA considered</li>
                    </ul>
                </div>
            </section>
        </div>
    </main>
</template>

<style scoped>
.page {
    min-height: 100vh;
    padding: 56px 24px;
    background: linear-gradient(
        180deg,
        #f8fafc 0%,
        #f4f6f8 100%
    );
    color: #172033;
}

.container {
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
}

.header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 32px;
    margin-bottom: 34px;
}

.eyebrow {
    margin: 0 0 6px;
    color: #2563eb;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.header h1 {
    margin: 0;
    font-size: clamp(32px, 4vw, 42px);
    font-weight: 700;
    letter-spacing: -0.03em;
    line-height: 1.1;
}

.subtitle {
    margin: 10px 0 0;
    color: #667085;
    font-size: 16px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.primary-button,
.icon-button,
.view-button,
.delete-button {
    transition:
        background 0.15s ease,
        border-color 0.15s ease,
        color 0.15s ease,
        transform 0.15s ease,
        box-shadow 0.15s ease;
}

.primary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 0 18px;
    border: 1px solid #2563eb;
    border-radius: 10px;
    background: #2563eb;
    color: #fff;
    font: inherit;
    font-weight: 650;
    cursor: pointer;
    box-shadow: 0 2px 5px rgb(37 99 235 / 18%);
}

.primary-button:hover:not(:disabled) {
    background: #1d4ed8;
    border-color: #1d4ed8;
    transform: translateY(-1px);
}

.primary-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.plus {
    font-size: 20px;
    line-height: 1;
}

.icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    padding: 0;
    border: 1px solid #d8dee8;
    border-radius: 10px;
    background: #fff;
    color: #475467;
    cursor: pointer;
}

.icon-button:hover {
    background: #f8fafc;
    color: #172033;
    border-color: #c6ceda;
}

.create-card {
    margin-bottom: 24px;
    padding: 22px;
    background: #fff;
    border: 1px solid #e2e7ee;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgb(16 24 40 / 4%);
}

.create-card h2 {
    margin: 0 0 18px;
    font-size: 18px;
}

.create-card label {
    display: block;
    margin-bottom: 7px;
    color: #344054;
    font-size: 14px;
    font-weight: 650;
}

.create-row {
    display: flex;
    gap: 10px;
}

.create-row input {
    flex: 1;
    min-height: 46px;
    padding: 0 13px;
    border: 1px solid #cfd6df;
    border-radius: 9px;
    background: #fff;
    color: #172033;
    font: inherit;
}

.create-row input::placeholder {
    color: #667085;
}

.lists {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.list-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    padding: 24px 26px;
    background: #fff;
    border: 1px solid #e2e7ee;
    border-radius: 14px;
    box-shadow: 0 1px 2px rgb(16 24 40 / 3%);
    transition:
        border-color 0.15s ease,
        box-shadow 0.15s ease,
        transform 0.15s ease;
}

.list-card:hover {
    border-color: #d3dae5;
    box-shadow: 0 5px 16px rgb(16 24 40 / 6%);
    transform: translateY(-1px);
}

.list-content {
    min-width: 0;
}

.list-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 13px;
}

.list-card h3 {
    margin: 0;
    color: #172033;
    font-size: 19px;
    font-weight: 650;
}

.list-details {
    display: flex;
    align-items: center;
    gap: 26px;
}

.list-details span {
    display: flex;
    flex-direction: column;
    gap: 3px;
    color: #667085;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.list-details strong {
    color: #263247;
    font-size: 15px;
    font-weight: 650;
    letter-spacing: normal;
    text-transform: none;
}

.list-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.view-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    flex-shrink: 0;
    min-height: 42px;
    padding: 0 15px;
    border: 1px solid #e1e6ed;
    border-radius: 9px;
    background: #f8fafc;
    color: #263247;
    text-decoration: none;
    font-weight: 650;
}

.view-button:hover {
    background: #eef2f7;
    border-color: #d3dae5;
}

.delete-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    padding: 0;
    border: 1px solid #fecaca;
    border-radius: 9px;
    background: #fff;
    color: #dc2626;
    cursor: pointer;
}

.delete-button:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}

.over-budget {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    border-radius: 999px;
    background: #fef2f2;
    color: #b42318;
    font-size: 12px;
    font-weight: 700;
}

.error {
    padding: 12px 14px;
    margin: 0 0 20px;
    border: 1px solid #fecaca;
    border-radius: 9px;
    background: #fef2f2;
    color: #991b1b;
    font-weight: 600;
}

.loading {
    color: #475467;
}

.empty-state {
    padding: 64px 24px;
    text-align: center;
    background: #fff;
    border: 1px dashed #ccd4df;
    border-radius: 14px;
}

.empty-state h3 {
    margin: 0 0 8px;
    font-size: 20px;
}

.empty-state p {
    margin: 0 0 20px;
    color: #667085;
}

/*
 * Visible keyboard focus.
 *
 * This is deliberately strong so keyboard users can clearly
 * identify which interactive element currently has focus.
 */
button:focus-visible,
a:focus-visible,
input:focus-visible {
    outline: 3px solid #1d4ed8;
    outline-offset: 3px;
}

.create-row input:focus-visible {
    border-color: #2563eb;
}

.settings-overlay {
    position: fixed;
    z-index: 1000;
    inset: 0;
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 24px;
    background: rgb(15 23 42 / 35%);
}

.settings-panel {
    width: min(430px, 100%);
    max-height: calc(100vh - 48px);
    padding: 24px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #e2e7ee;
    border-radius: 16px;
    color: #172033;
    box-shadow: 0 20px 50px rgb(15 23 42 / 18%);
}

.settings-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e8ecf1;
}

.settings-eyebrow {
    margin: 0 0 5px;
    color: #2563eb;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.settings-header h2 {
    margin: 0;
    font-size: 23px;
}

.settings-header p:not(.settings-eyebrow) {
    margin: 8px 0 0;
    color: #667085;
    line-height: 1.5;
}

.settings-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    padding: 0;
    border: 1px solid #d8dee8;
    border-radius: 9px;
    background: #fff;
    color: #475467;
    font-size: 25px;
    line-height: 1;
    cursor: pointer;
}

.settings-close:hover {
    background: #f8fafc;
    color: #172033;
}

.settings-options {
    display: flex;
    flex-direction: column;
    margin-top: 10px;
}

.setting-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 18px 4px;
    border-bottom: 1px solid #eef1f5;
    cursor: pointer;
}

.setting-description {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.setting-description strong {
    color: #263247;
    font-size: 15px;
}

.setting-description small {
    max-width: 310px;
    color: #667085;
    font-size: 13px;
    line-height: 1.45;
}

.setting-option input {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    accent-color: #2563eb;
}

.accessibility-notes {
    margin-top: 22px;
    padding: 18px;
    background: #f8fafc;
    border: 1px solid #e5eaf0;
    border-radius: 12px;
}

.accessibility-notes h3 {
    margin: 0 0 12px;
    font-size: 15px;
}

.accessibility-notes ul {
    display: grid;
    gap: 7px;
    margin: 0;
    padding-left: 20px;
    color: #475467;
    font-size: 13px;
}

 /* Accessibility modes */
.large-text {
    font-size: 125%;
}

.large-text p,
.large-text span,
.large-text label,
.large-text input,
.large-text button,
.large-text a {
    font-size: 1.15em;
}
.large-text h1 {
    font-size: 3rem;
}

.large-text h2 {
    font-size: 1.75rem;
}

.large-text h3 {
    font-size: 1.5rem;
}

.high-contrast {
    background: #fff;
    color: #000;
}

.high-contrast .header h1,
.high-contrast .list-card h3,
.high-contrast .list-details strong,
.high-contrast .setting-description strong {
    color: #000;
}

.high-contrast .subtitle,
.high-contrast .list-details span,
.high-contrast .setting-description small {
    color: #1f2937;
}

.high-contrast .list-card,
.high-contrast .create-card,
.high-contrast .settings-panel {
    border: 2px solid #000;
}

.high-contrast .primary-button {
    background: #0037a6;
    border-color: #0037a6;
}

.increased-spacing {
    line-height: 1.7;
}

.increased-spacing .list-card {
    padding-top: 30px;
    padding-bottom: 30px;
}

.increased-spacing .lists {
    gap: 20px;
}

.increased-spacing .list-details {
    gap: 34px;
}

.reduced-motion *,
.reduced-motion *::before,
.reduced-motion *::after {
    scroll-behavior: auto !important;
    transition: none !important;
    animation: none !important;
}

.reduced-motion .primary-button:hover,
.reduced-motion .list-card:hover {
    transform: none;
}

/*
 * Content intended for screen readers but not visually displayed.
 */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/*
 * Respect the user's operating-system motion preference.
 */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }

    .primary-button:hover,
    .list-card:hover {
        transform: none;
    }
}

@media (max-width: 640px) {
    .page {
        padding: 32px 16px;
    }

    .header {
        align-items: stretch;
        flex-direction: column;
        gap: 20px;
    }

    .header-actions {
        width: 100%;
    }

    .primary-button {
        flex: 1;
    }

    .list-card {
        align-items: stretch;
        flex-direction: column;
    }

    .list-details {
        gap: 18px;
    }

    .view-button {
        width: 100%;
    }

    .create-row {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .settings-overlay {
        align-items: flex-end;
        padding: 0;
    }

    .settings-panel {
        width: 100%;
        max-height: 90vh;
        border-radius: 18px 18px 0 0;
    }
}
</style>

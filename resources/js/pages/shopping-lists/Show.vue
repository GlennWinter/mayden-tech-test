<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Check, Plus, Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import AccessibilitySettings from '@/components/AccessibilitySettings.vue';
import { useAccessibility } from '@/composables/useAccessibility';

import ShoppingListLayout from '@/layouts/ShoppingListLayout.vue';

defineOptions({
    layout: ShoppingListLayout,
});
const { highContrast, largeText, reducedMotion, increasedSpacing } =
    useAccessibility();

interface ShoppingListItem {
    id: number;
    shopping_list_id: number;
    name: string;
    price_in_pence: number;
    quantity: number;
    is_purchased: boolean;
}

interface ShoppingList {
    id: number;
    name: string;
    budget_limit_in_pence: number | null;
    total_in_pence: number;
    is_over_budget: boolean;
    items: ShoppingListItem[];
}

const props = defineProps<{
    shoppingListId: number;
}>();

const shoppingList = ref<ShoppingList | null>(null);

const itemName = ref('');
const itemPrice = ref<number | null>(null);
const itemQuantity = ref(1);

const isLoading = ref(true);
const isAddingItem = ref(false);

const error = ref('');
const successMessage = ref('');

async function fetchShoppingList() {
    error.value = '';

    try {
        const response = await fetch(
            `/api/shopping-lists/${props.shoppingListId}`,
            {
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        if (!response.ok) {
            throw new Error('Unable to load shopping list.');
        }

        shoppingList.value = await response.json();
    } catch {
        error.value = 'Unable to load shopping list.';
    } finally {
        isLoading.value = false;
    }
}

async function addItem() {
    if (!shoppingList.value || !itemName.value.trim()) {
        return;
    }

    if (itemPrice.value === null || itemPrice.value < 0) {
        error.value = 'Please enter a valid price.';

        return;
    }

    isAddingItem.value = true;
    error.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(
            `/api/shopping-lists/${shoppingList.value.id}/items`,
            {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: itemName.value.trim(),
                    price_in_pence: Math.round(itemPrice.value * 100),
                    quantity: itemQuantity.value,
                }),
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => null);
            const message = data?.errors?.name?.[0] ?? data?.message ?? 'Unable to add item.';

            throw new Error(message);
        }

        const item: ShoppingListItem = await response.json();

        await fetchShoppingList();

        itemName.value = '';
        itemPrice.value = null;
        itemQuantity.value = 1;

        successMessage.value = `${item.name} added successfully.`;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unable to add item.';
    } finally {
        isAddingItem.value = false;
    }
}

async function togglePurchased(item: ShoppingListItem) {
    if (!shoppingList.value) {
        return;
    }

    error.value = '';

    try {
        const response = await fetch(
            `/api/shopping-lists/${shoppingList.value.id}/items/${item.id}`,
            {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    is_purchased: !item.is_purchased,
                }),
            },
        );

        if (!response.ok) {
            throw new Error('Unable to update item.');
        }

        await response.json();

        await fetchShoppingList();
    } catch {
        error.value = 'Unable to update item.';
    }
}

async function deleteItem(item: ShoppingListItem) {
    if (!shoppingList.value) {
        return;
    }

    const confirmed = window.confirm(
        `Are you sure you want to remove "${item.name}"?`,
    );

    if (!confirmed) {
        return;
    }

    error.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(
            `/api/shopping-lists/${shoppingList.value.id}/items/${item.id}`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                },
            },
        );

        if (!response.ok) {
            throw new Error('Unable to delete item.');
        }

        await fetchShoppingList();

        successMessage.value = `${item.name} removed successfully.`;
    } catch {
        error.value = 'Unable to delete item.';
    }
}

function formatMoney(value: number) {
    return `£${(value / 100).toFixed(2)}`;
}

onMounted(fetchShoppingList);
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
                    <Link href="/" class="back-link">
                        ← Back to shopping lists
                    </Link>

                    <h1>
                        {{ shoppingList?.name ?? 'Shopping List' }}
                    </h1>

                    <p class="subtitle">
                        Add items, mark them as purchased and keep track of your
                        spending.
                    </p>
                </div>
                <AccessibilitySettings />
            </header>

            <p v-if="error" class="error" role="alert">
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

            <p v-if="isLoading" role="status" aria-live="polite">
                Loading shopping list...
            </p>

            <template v-else-if="shoppingList">
                <section class="summary" aria-label="Shopping list summary">
                    <div class="summary-item">
                        <span>Total</span>
                        <strong>{{ formatMoney(shoppingList.total_in_pence) }}</strong>
                    </div>

                    <div
                        v-if="shoppingList.budget_limit_in_pence !== null"
                        class="summary-item"
                    >
                        <span>Budget</span>
                        <strong>
                            {{
                                formatMoney(shoppingList.budget_limit_in_pence)
                            }}
                        </strong>
                    </div>

                    <div
                        v-if="shoppingList.budget_limit_in_pence !== null"
                        class="summary-item"
                    >
                        <span>Remaining</span>

                        <strong :class="{ negative: shoppingList.is_over_budget }">
                            {{
                                formatMoney(
                                    shoppingList.budget_limit_in_pence -
                                    shoppingList.total_in_pence,
                                )
                            }}
                        </strong>
                    </div>
                </section>

                <div
                    v-if="shoppingList.is_over_budget"
                    class="budget-warning"
                    role="alert"
                >
                    You are over your budget by
                    <strong>
                        {{
                            formatMoney(
                                shoppingList.total_in_pence -
                                (shoppingList.budget_limit_in_pence ?? 0),
                            )
                        }}
                    </strong>
                </div>

                <section
                    class="add-item-card"
                    aria-labelledby="add-item-heading"
                >
                    <div class="section-heading">
                        <div>
                            <h2 id="add-item-heading">Add item</h2>

                            <p>Add something to your shopping list.</p>
                        </div>

                        <Plus :size="22" aria-hidden="true" />
                    </div>

                    <form class="add-item-form" @submit.prevent="addItem">
                        <div class="form-field item-name-field">
                            <label for="item-name"> Item name </label>

                            <input
                                id="item-name"
                                v-model="itemName"
                                type="text"
                                maxlength="255"
                                placeholder="e.g. Milk"
                                required
                            />
                        </div>

                        <div class="form-field">
                            <label for="item-price"> Price </label>

                            <div class="price-input">
                                <span aria-hidden="true">£</span>

                                <input
                                    id="item-price"
                                    v-model.number="itemPrice"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00"
                                    required
                                />
                            </div>
                        </div>

                        <div class="form-field quantity-field">
                            <label for="item-quantity"> Quantity </label>

                            <input
                                id="item-quantity"
                                v-model.number="itemQuantity"
                                type="number"
                                min="1"
                                step="1"
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            class="primary-button"
                            :disabled="isAddingItem"
                        >
                            <Plus :size="18" aria-hidden="true" />

                            {{ isAddingItem ? 'Adding...' : 'Add item' }}
                        </button>
                    </form>
                </section>

                <section class="items-section" aria-labelledby="items-heading">
                    <div class="items-heading">
                        <div>
                            <h2 id="items-heading">Items</h2>

                            <p>
                                {{ shoppingList.items.length }}
                                {{
                                    shoppingList.items.length === 1
                                        ? 'item'
                                        : 'items'
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="shoppingList.items.length === 0"
                        class="empty-state"
                    >
                        <h3>Your shopping list is empty</h3>

                        <p>Add your first item using the form above.</p>
                    </div>

                    <div v-else class="items">
                        <article
                            v-for="item in shoppingList.items"
                            :key="item.id"
                            class="item-card"
                            :class="{ purchased: item.is_purchased }"
                        >
                            <button
                                type="button"
                                class="purchase-button"
                                :class="{ checked: item.is_purchased }"
                                :aria-label="
                                    item.is_purchased
                                        ? `Mark ${item.name} as not purchased`
                                        : `Mark ${item.name} as purchased`
                                "
                                :aria-pressed="item.is_purchased"
                                @click="togglePurchased(item)"
                            >
                                <Check
                                    v-if="item.is_purchased"
                                    :size="18"
                                    aria-hidden="true"
                                />
                            </button>

                            <div class="item-content">
                                <h3>{{ item.name }}</h3>

                                <div class="item-meta">
                                    <span>
                                        {{ formatMoney(item.price_in_pence) }}
                                    </span>

                                    <span v-if="item.quantity > 1">
                                        Qty {{ item.quantity }}
                                    </span>

                                    <span v-if="item.quantity > 1">
                                        {{
                                            formatMoney(
                                                item.price_in_pence *
                                                item.quantity,
                                            )
                                        }}
                                        total
                                    </span>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="delete-button"
                                :aria-label="`Remove ${item.name}`"
                                :title="`Remove ${item.name}`"
                                @click="deleteItem(item)"
                            >
                                <Trash2 :size="19" aria-hidden="true" />
                            </button>
                        </article>
                    </div>
                </section>
            </template>
        </div>
    </main>
</template>

<style scoped>
.page {
    min-height: 100vh;
    padding: 56px 24px;
    background: linear-gradient(180deg, #f8fafc 0%, #f4f6f8 100%);
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
    gap: 24px;
    margin-bottom: 30px;
}

.header-content {
    min-width: 0;
    flex: 1;
}

.back-link {
    display: inline-block;
    margin-bottom: 14px;
    color: #475467;
    text-decoration: none;
    font-weight: 600;
}

.back-link:hover {
    color: #2563eb;
}

.header h1 {
    margin: 0;
    font-size: clamp(32px, 4vw, 42px);
    letter-spacing: -0.03em;
}

.subtitle {
    margin: 10px 0 0;
    color: #667085;
}

.summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    margin-bottom: 20px;
    background: #fff;
    border: 1px solid #e2e7ee;
    border-radius: 14px;
    overflow: hidden;
}

.summary-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 20px 22px;
}

.summary-item + .summary-item {
    border-left: 1px solid #e2e7ee;
}

.summary-item span {
    color: #667085;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.summary-item strong {
    font-size: 21px;
}

.negative {
    color: #b42318;
}

.budget-warning {
    margin-bottom: 20px;
    padding: 14px 16px;
    border: 1px solid #fecaca;
    border-radius: 10px;
    background: #fef2f2;
    color: #991b1b;
}

.add-item-card,
.items-section {
    margin-bottom: 20px;
    padding: 24px;
    background: #fff;
    border: 1px solid #e2e7ee;
    border-radius: 14px;
}

.section-heading,
.items-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
}

.section-heading h2,
.items-heading h2 {
    margin: 0;
    font-size: 20px;
}

.section-heading p,
.items-heading p {
    margin: 5px 0 0;
    color: #667085;
}

.add-item-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 160px 110px auto;
    align-items: end;
    gap: 12px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.form-field label {
    color: #344054;
    font-size: 14px;
    font-weight: 650;
}

.form-field input {
    width: 100%;
    min-height: 46px;
    padding: 0 12px;
    border: 1px solid #cfd6df;
    border-radius: 9px;
    background: #fff;
    color: #172033;
    font: inherit;
}

.price-input {
    position: relative;
}

.price-input span {
    position: absolute;
    top: 50%;
    left: 12px;
    color: #667085;
    transform: translateY(-50%);
    pointer-events: none;
}

.price-input input {
    padding-left: 29px;
}

.primary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 46px;
    padding: 0 18px;
    border: 1px solid #2563eb;
    border-radius: 9px;
    background: #2563eb;
    color: #fff;
    font: inherit;
    font-weight: 650;
    cursor: pointer;
    transition:
        background 0.15s ease,
        border-color 0.15s ease,
        transform 0.15s ease;
}

.primary-button:hover:not(:disabled) {
    background: #1d4ed8;
    border-color: #1d4ed8;
    transform: translateY(-1px);
}

.primary-button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.items {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.item-card {
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 82px;
    padding: 18px;
    border: 1px solid #e2e7ee;
    border-radius: 11px;
    background: #fff;
    transition:
        background 0.15s ease,
        border-color 0.15s ease,
        opacity 0.15s ease;
}

.item-card:hover {
    border-color: #d3dae5;
    background: #fbfcfd;
}

.item-card.purchased {
    background: #f8fafc;
}

.item-card.purchased .item-content h3 {
    color: #667085;
    text-decoration: line-through;
    text-decoration-thickness: 2px;
}

.item-card.purchased .item-meta {
    opacity: 0.7;
}

.purchase-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 2px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    color: #fff;
    cursor: pointer;
    transition:
        background 0.15s ease,
        border-color 0.15s ease;
}

.purchase-button:hover {
    border-color: #2563eb;
}

.purchase-button.checked {
    border-color: #16a34a;
    background: #16a34a;
}

.item-content {
    min-width: 0;
    flex: 1;
}

.item-content h3 {
    margin: 0 0 6px;
    color: #172033;
    font-size: 18px;
    font-weight: 650;
    line-height: 1.4;
}

.item-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    color: #667085;
    font-size: 15px;
    line-height: 1.5;
}

.delete-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    padding: 0;
    border: 1px solid #fecaca;
    border-radius: 9px;
    background: #fff;
    color: #dc2626;
    cursor: pointer;
    transition:
        background 0.15s ease,
        border-color 0.15s ease,
        color 0.15s ease;
}

.delete-button:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}

.empty-state {
    padding: 45px 20px;
    text-align: center;
    border: 1px dashed #ccd4df;
    border-radius: 11px;
}

.empty-state h3 {
    margin: 0 0 7px;
}

.empty-state p {
    margin: 0;
    color: #667085;
}

.error {
    margin-bottom: 20px;
    padding: 12px 14px;
    border: 1px solid #fecaca;
    border-radius: 9px;
    background: #fef2f2;
    color: #991b1b;
    font-weight: 600;
}

/*
 * Visible keyboard focus.
 */
button:focus-visible,
a:focus-visible,
input:focus-visible {
    outline: 3px solid #1d4ed8;
    outline-offset: 3px;
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
        transition: none !important;
        animation: none !important;
    }

    .primary-button:hover {
        transform: none;
    }
}

@media (max-width: 760px) {
    .page {
        padding: 32px 16px;
    }

    .summary {
        grid-template-columns: 1fr;
    }

    .summary-item + .summary-item {
        border-top: 1px solid #e2e7ee;
        border-left: 0;
    }

    .add-item-form {
        grid-template-columns: 1fr;
    }

    .primary-button {
        width: 100%;
    }

    .item-card {
        gap: 12px;
        padding: 16px;
    }
}
</style>

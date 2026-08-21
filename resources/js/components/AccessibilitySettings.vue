<script setup lang="ts">
import { Settings } from 'lucide-vue-next';
import { nextTick, ref } from 'vue';

import { useAccessibility } from '@/composables/useAccessibility';

const showSettings = ref(false);
const closeButton = ref<HTMLButtonElement | null>(null);

const { highContrast, largeText, reducedMotion, increasedSpacing } =
    useAccessibility();

async function openSettings() {
    showSettings.value = true;

    await nextTick();

    closeButton.value?.focus();
}

function closeSettings() {
    showSettings.value = false;
}

function handleKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        closeSettings();
    }
}
</script>

<template>
    <button
        type="button"
        class="accessibility-button"
        aria-label="Open accessibility settings"
        aria-haspopup="dialog"
        :aria-expanded="showSettings"
        title="Accessibility settings"
        @click="openSettings"
    >
        <Settings :size="20" aria-hidden="true" />
    </button>

    <div
        v-if="showSettings"
        class="settings-overlay"
        @click.self="closeSettings"
        @keydown="handleKeydown"
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
                    <p class="settings-eyebrow">Display preferences</p>

                    <h2 id="accessibility-settings-heading">
                        Accessibility settings
                    </h2>

                    <p id="accessibility-settings-description">
                        Adjust how the shopping list interface is displayed.
                    </p>
                </div>

                <button
                    ref="closeButton"
                    type="button"
                    class="settings-close"
                    aria-label="Close accessibility settings"
                    @click="closeSettings"
                >
                    ×
                </button>
            </header>

            <div class="settings-options">
                <label class="setting-option">
                    <span class="setting-description">
                        <strong>High contrast</strong>
                        <small>
                            Increase contrast between text, controls and
                            backgrounds.
                        </small>
                    </span>

                    <input v-model="highContrast" type="checkbox" />
                </label>

                <label class="setting-option">
                    <span class="setting-description">
                        <strong>Larger text</strong>
                        <small>
                            Increase text size throughout the application.
                        </small>
                    </span>

                    <input v-model="largeText" type="checkbox" />
                </label>

                <label class="setting-option">
                    <span class="setting-description">
                        <strong>Reduced motion</strong>
                        <small>
                            Remove non-essential movement and transitions.
                        </small>
                    </span>

                    <input v-model="reducedMotion" type="checkbox" />
                </label>

                <label class="setting-option">
                    <span class="setting-description">
                        <strong>Increased spacing</strong>
                        <small>
                            Add more space between text and interface elements.
                        </small>
                    </span>

                    <input v-model="increasedSpacing" type="checkbox" />
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
</template>

<style scoped>
.accessibility-button {
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

.accessibility-button:hover {
    background: #f8fafc;
    color: #172033;
    border-color: #c6ceda;
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

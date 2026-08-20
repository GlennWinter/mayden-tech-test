import { ref, watch } from 'vue';

const STORAGE_KEY = 'accessibility-preferences';

interface AccessibilityPreferences {
    highContrast: boolean;
    largeText: boolean;
    reducedMotion: boolean;
    increasedSpacing: boolean;
}

const defaults: AccessibilityPreferences = {
    highContrast: false,
    largeText: false,
    reducedMotion: false,
    increasedSpacing: false,
};

function loadPreferences(): AccessibilityPreferences {
    const saved = localStorage.getItem(STORAGE_KEY);

    if (!saved) {
        return defaults;
    }

    try {
        return {
            ...defaults,
            ...JSON.parse(saved),
        };
    } catch {
        return defaults;
    }
}

const preferences = loadPreferences();

const highContrast = ref(preferences.highContrast);
const largeText = ref(preferences.largeText);
const reducedMotion = ref(preferences.reducedMotion);
const increasedSpacing = ref(preferences.increasedSpacing);

watch(
    [highContrast, largeText, reducedMotion, increasedSpacing],
    () => {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                highContrast: highContrast.value,
                largeText: largeText.value,
                reducedMotion: reducedMotion.value,
                increasedSpacing: increasedSpacing.value,
            }),
        );
    },
);

export function useAccessibility() {
    return {
        highContrast,
        largeText,
        reducedMotion,
        increasedSpacing,
    };
}

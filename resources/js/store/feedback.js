import { defineStore } from 'pinia';

export const useFeedbackStore = defineStore('feedback', {
    state: () => ({
        open: false,
        message: '',
        color: 'success',
        timeout: 4000,
    }),

    actions: {
        notify({ message, color = 'success', timeout = 4000 }) {
            this.message = message;
            this.color = color;
            this.timeout = timeout;
            this.open = true;
        },

        success(message) {
            this.notify({ message, color: 'success' });
        },

        error(message) {
            this.notify({ message, color: 'error', timeout: 6000 });
        },

        close() {
            this.open = false;
        },
    },
});

import { defineStore } from 'pinia';
import authService from '../services/auth.service';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('auth_token'),
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => Boolean(state.token),
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;

            try {
                const { data } = await authService.login(credentials);
                this.token = data.token;
                localStorage.setItem('auth_token', data.token);
            } catch (error) {
                this.error = error.response?.data?.message || 'Nao foi possivel autenticar.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await authService.logout();
                }
            } finally {
                this.token = null;
                localStorage.removeItem('auth_token');
            }
        },
    },
});

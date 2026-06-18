import { defineStore } from 'pinia';
import dashboardService from '../services/dashboard.service';

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        totals: {
            patients_total: 0,
            addresses_total: 0,
        },
        loading: false,
    }),

    actions: {
        async fetchTotals() {
            this.loading = true;

            try {
                const { data } = await dashboardService.totals();
                this.totals = data.data || this.totals;
            } finally {
                this.loading = false;
            }
        },
    },
});

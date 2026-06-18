import { defineStore } from 'pinia';
import patientService from '../services/patient.service';

export const usePatientsStore = defineStore('patients', {
    state: () => ({
        items: [],
        meta: {},
        current: null,
        loading: false,
    }),

    actions: {
        async fetch(params) {
            this.loading = true;

            try {
                const { data } = await patientService.list(params);
                this.items = data.data || [];
                this.meta = data.meta || {};
            } finally {
                this.loading = false;
            }
        },

        async fetchOne(id) {
            this.loading = true;

            try {
                const { data } = await patientService.get(id);
                this.current = data.data;
                return this.current;
            } finally {
                this.loading = false;
            }
        },

        async save(payload, id = null) {
            const request = id ? patientService.update(id, payload) : patientService.create(payload);
            const { data } = await request;
            return data;
        },

        async delete(id) {
            const { data } = await patientService.delete(id);
            return data;
        },
    },
});

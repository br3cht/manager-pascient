import { defineStore } from 'pinia';
import addressService from '../services/address.service';

export const useAddressesStore = defineStore('addresses', {
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
                const { data } = await addressService.list(params);
                this.items = data.data || [];
                this.meta = data.meta || {};
            } finally {
                this.loading = false;
            }
        },

        async fetchOne(id) {
            this.loading = true;

            try {
                const { data } = await addressService.get(id);
                this.current = data.data;
                return this.current;
            } finally {
                this.loading = false;
            }
        },

        async save(payload, id = null) {
            const request = id ? addressService.update(id, payload) : addressService.create(payload);
            const { data } = await request;
            return data;
        },

        async delete(id) {
            const { data } = await addressService.delete(id);
            return data;
        },
    },
});

<template>
    <div>
        <div class="d-flex flex-column flex-md-row align-md-center justify-space-between mb-4">
            <div>
                <h1 class="text-h5 font-weight-bold mb-1">Enderecos</h1>
                <p class="text-body-2 grey--text text--darken-1 mb-0">Consulte, filtre e mantenha os enderecos cadastrados.</p>
            </div>
            <v-btn color="primary" class="mt-3 mt-md-0" :to="{ name: 'enderecos.create' }">
                <v-icon left>mdi-plus</v-icon>
                Novo endereco
            </v-btn>
        </div>

        <v-card outlined class="pa-4 mb-4 filters">
            <v-row>
                <v-col cols="12" md="8">
                    <v-text-field
                        v-model="filters.search"
                        label="Buscar por logradouro, CEP, bairro ou cidade"
                        outlined
                        dense
                        hide-details
                        prepend-inner-icon="mdi-magnify"
                    />
                </v-col>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="filters.state"
                        :items="states"
                        label="UF"
                        outlined
                        dense
                        clearable
                        hide-details
                    />
                </v-col>
            </v-row>
        </v-card>

        <BaseTable
            :headers="headers"
            :items="store.items"
            :loading="store.loading"
            :sort-by="query.sort_by"
            :sort-dir="query.sort_dir"
            @sort="updateQuery"
        >
            <template #cell-zip_code="{ item }">{{ formatCep(item.zip_code) }}</template>
            <template #actions="{ item }">
                <v-btn icon color="primary" :to="{ name: 'enderecos.edit', params: { id: item.id } }">
                    <v-icon>mdi-pencil-outline</v-icon>
                </v-btn>
                <v-btn icon color="error" @click="askDelete(item)">
                    <v-icon>mdi-delete-outline</v-icon>
                </v-btn>
            </template>
        </BaseTable>

        <Pagination :meta="store.meta" :page="query.page" @change="updateQuery({ page: $event })" />

        <ConfirmModal
            v-model="confirm.open"
            :message="`Deseja excluir o endereco ${confirm.item ? confirm.item.street : ''}?`"
            :loading="confirm.loading"
            @confirm="deleteItem"
        />
    </div>
</template>

<script>
import BaseTable from '../../components/BaseTable.vue';
import Pagination from '../../components/Pagination.vue';
import ConfirmModal from '../../components/ConfirmModal.vue';
import { useAddressesStore } from '../../store/addresses';
import { useFeedbackStore } from '../../store/feedback';
import { BRAZILIAN_STATES } from '../../constants/states';

export default {
    name: 'AddressIndex',

    components: { BaseTable, Pagination, ConfirmModal },

    data() {
        return {
            states: BRAZILIAN_STATES,
            filters: {
                search: '',
                state: '',
            },
            query: {
                page: 1,
                per_page: 15,
                sort_by: 'id',
                sort_dir: 'asc',
            },
            headers: [
                { text: 'ID', value: 'id' },
                { text: 'Logradouro', value: 'street' },
                { text: 'CEP', value: 'zip_code' },
                { text: 'Bairro', value: 'neighborhood' },
                { text: 'Cidade', value: 'city' },
                { text: 'UF', value: 'state' },
            ],
            confirm: {
                open: false,
                loading: false,
                item: null,
            },
            debounce: null,
        };
    },

    computed: {
        store() {
            return useAddressesStore();
        },

        feedback() {
            return useFeedbackStore();
        },
    },

    watch: {
        '$route.query': {
            immediate: true,
            handler() {
                this.syncFromRoute();
                this.fetch();
            },
        },

        filters: {
            deep: true,
            handler() {
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    this.updateQuery({ search: this.filters.search || undefined, state: this.filters.state || undefined, page: 1 });
                }, 400);
            },
        },
    },

    methods: {
        syncFromRoute() {
            this.query = {
                page: Number(this.$route.query.page || 1),
                per_page: Number(this.$route.query.per_page || 15),
                sort_by: this.$route.query.sort_by || 'id',
                sort_dir: this.$route.query.sort_dir || 'asc',
            };
            this.filters.search = this.$route.query.search || '';
            this.filters.state = this.$route.query.state || '';
        },

        fetch() {
            this.store.fetch({ ...this.query, search: this.filters.search || undefined, state: this.filters.state || undefined });
        },

        updateQuery(partial) {
            this.$router.push({ query: { ...this.$route.query, ...partial } }).catch(() => {});
        },

        formatCep(value) {
            return String(value || '').replace(/^(\d{5})(\d{3})$/, '$1-$2');
        },

        askDelete(item) {
            this.confirm.item = item;
            this.confirm.open = true;
        },

        async deleteItem() {
            this.confirm.loading = true;

            try {
                const result = await this.store.delete(this.confirm.item.id);
                this.feedback.success(result?.message || 'Endereco excluido com sucesso.');
                this.confirm.open = false;
                this.fetch();
            } catch (error) {
                this.feedback.error(error.response?.data?.message || 'Nao foi possivel excluir o endereco.');
            } finally {
                this.confirm.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.filters {
    border-radius: 8px;
}
</style>

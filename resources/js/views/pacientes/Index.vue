<template>
    <div>
        <div class="d-flex flex-column flex-md-row align-md-center justify-space-between mb-4">
            <div>
                <h1 class="text-h5 font-weight-bold mb-1">Pacientes</h1>
                <p class="text-body-2 grey--text text--darken-1 mb-0">Busque por nome, CPF, CNS ou telefone.</p>
            </div>
            <v-btn color="primary" class="mt-3 mt-md-0" :to="{ name: 'pacientes.create' }">
                <v-icon left>mdi-plus</v-icon>
                Novo paciente
            </v-btn>
        </div>

        <v-card outlined class="pa-4 mb-4 filters">
            <v-row>
                <v-col cols="12" md="8">
                    <v-text-field
                        v-model="filters.search"
                        label="Buscar por nome, CPF, CNS ou telefone"
                        outlined
                        dense
                        hide-details
                        prepend-inner-icon="mdi-magnify"
                    />
                </v-col>
                <v-col cols="12" md="4">
                    <v-select
                        v-model="filters.gender"
                        :items="genders"
                        item-text="label"
                        item-value="value"
                        label="Genero"
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
            <template #cell-cpf="{ item }">{{ formatCpf(item.cpf) }}</template>
            <template #cell-cns="{ item }">{{ formatCns(item.cns) }}</template>
            <template #cell-phone="{ item }">{{ formatPhone(item.phone) }}</template>
            <template #cell-gender="{ item }">{{ genderLabel(item.gender) }}</template>
            <template #cell-address_id="{ item }">{{ item.address ? `${item.address.city}/${item.address.state}` : item.address_id }}</template>
            <template #actions="{ item }">
                <v-btn icon color="primary" :to="{ name: 'pacientes.edit', params: { id: item.id } }">
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
            :message="`Deseja excluir o paciente ${confirm.item ? confirm.item.name : ''}?`"
            :loading="confirm.loading"
            @confirm="deleteItem"
        />
    </div>
</template>

<script>
import BaseTable from '../../components/BaseTable.vue';
import Pagination from '../../components/Pagination.vue';
import ConfirmModal from '../../components/ConfirmModal.vue';

export default {
    name: 'PatientIndex',

    components: { BaseTable, Pagination, ConfirmModal },

    data() {
        return {
            filters: {
                search: '',
                gender: '',
            },
            query: {
                page: 1,
                per_page: 15,
                sort_by: 'id',
                sort_dir: 'asc',
            },
            genders: [
                { label: 'Masculino', value: 'M' },
                { label: 'Feminino', value: 'F' },
                { label: 'Outro', value: 'O' },
            ],
            headers: [
                { text: 'ID', value: 'id' },
                { text: 'Nome', value: 'name' },
                { text: 'CPF', value: 'cpf' },
                { text: 'CNS', value: 'cns' },
                { text: 'Genero', value: 'gender' },
                { text: 'Telefone', value: 'phone' },
                { text: 'Endereco', value: 'address_id' },
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
            return this.$store.state.patients;
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
                    this.updateQuery({ search: this.filters.search || undefined, gender: this.filters.gender || undefined, page: 1 });
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
            this.filters.gender = this.$route.query.gender || '';
        },

        fetch() {
            this.$store.dispatch('patients/fetch', { ...this.query, search: this.filters.search || undefined, gender: this.filters.gender || undefined });
        },

        updateQuery(partial) {
            this.$router.push({ query: { ...this.$route.query, ...partial } }).catch(() => {});
        },

        formatCpf(value) {
            return String(value || '').replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
        },

        formatCns(value) {
            return String(value || '').replace(/^(\d{3})(\d{4})(\d{4})(\d{4})$/, '$1 $2 $3 $4');
        },

        formatPhone(value) {
            return String(value || '').replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        },

        genderLabel(value) {
            return this.genders.find((gender) => gender.value === value)?.label || value;
        },

        askDelete(item) {
            this.confirm.item = item;
            this.confirm.open = true;
        },

        async deleteItem() {
            this.confirm.loading = true;

            try {
                const result = await this.$store.dispatch('patients/delete', this.confirm.item.id);
                this.$store.dispatch('feedback/success', result?.message || 'Paciente excluido com sucesso.');
                this.confirm.open = false;
                this.fetch();
            } catch (error) {
                this.$store.dispatch('feedback/error', error.response?.data?.message || 'Nao foi possivel excluir o paciente.');
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

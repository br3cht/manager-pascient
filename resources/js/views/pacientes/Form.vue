<template>
    <div>
        <div class="d-flex align-center justify-space-between mb-4">
            <div>
                <h1 class="text-h5 font-weight-bold mb-1">{{ title }}</h1>
                <p class="text-body-2 grey--text text--darken-1 mb-0">Preencha identificacao, contato e endereco do paciente.</p>
            </div>
        </div>

        <v-card outlined class="pa-4 pa-md-6 form-card">
            <ValidationObserver ref="observer" v-slot="{ invalid }">
                <v-form @submit.prevent="submit">
                    <v-row>
                        <v-col cols="12" md="8">
                            <BaseInput v-model="form.name" label="Nome completo" rules="required" />
                        </v-col>
                        <v-col cols="12" md="4">
                            <BaseInput v-model="form.birth_date" label="Data de nascimento" rules="required|not_future" type="date" />
                        </v-col>
                        <v-col cols="12" md="4">
                            <BaseInput v-model="form.cpf" label="CPF" rules="required|cpf" mask="cpf" />
                        </v-col>
                        <v-col cols="12" md="4">
                            <BaseInput v-model="form.cns" label="CNS" rules="required|cns" mask="cns" />
                        </v-col>
                        <v-col cols="12" md="4">
                            <BaseInput v-model="form.phone" label="Telefone" rules="phone_br" mask="phone" />
                        </v-col>
                        <v-col cols="12" md="4">
                            <ValidationProvider v-slot="{ errors }" name="Genero" rules="required" slim>
                                <v-select
                                    v-model="form.gender"
                                    :items="genders"
                                    item-text="label"
                                    item-value="value"
                                    label="Genero"
                                    outlined
                                    dense
                                    hide-details="auto"
                                    :error-messages="errors"
                                />
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12" md="8">
                            <ValidationProvider v-slot="{ errors }" name="Endereco" rules="required" slim>
                                <v-autocomplete
                                    v-model="form.address_id"
                                    :items="addressOptions"
                                    item-text="label"
                                    item-value="id"
                                    label="Endereco"
                                    placeholder="Digite logradouro, bairro ou cidade"
                                    outlined
                                    dense
                                    hide-details="auto"
                                    :loading="addresses.loading"
                                    :search-input.sync="addressSearch"
                                    :error-messages="errors"
                                    no-filter
                                    clearable
                                />
                            </ValidationProvider>
                        </v-col>
                    </v-row>

                    <v-alert v-if="error" type="error" dense text>{{ error }}</v-alert>

                    <div class="d-flex justify-end mt-4">
                        <v-btn text class="mr-2" :to="{ name: 'pacientes.index' }">Cancelar</v-btn>
                        <v-btn type="submit" color="primary" :loading="loading" :disabled="invalid">
                            <v-icon left>mdi-content-save-outline</v-icon>
                            Salvar
                        </v-btn>
                    </div>
                </v-form>
            </ValidationObserver>
        </v-card>
    </div>
</template>

<script>
import BaseInput from '../../components/BaseInput.vue';
import { useAddressesStore } from '../../store/addresses';
import { usePatientsStore } from '../../store/patients';
import { useFeedbackStore } from '../../store/feedback';

export default {
    name: 'PatientForm',

    components: { BaseInput },

    props: {
        id: { type: [String, Number], default: null },
    },

    data() {
        return {
            form: {
                name: '',
                cpf: '',
                cns: '',
                birth_date: '',
                gender: '',
                phone: '',
                address_id: null,
            },
            genders: [
                { label: 'Masculino', value: 'M' },
                { label: 'Feminino', value: 'F' },
                { label: 'Outro', value: 'O' },
            ],
            loading: false,
            error: null,
            addressSearch: '',
            addressSearchTimer: null,
        };
    },

    computed: {
        store() {
            return usePatientsStore();
        },

        addresses() {
            return useAddressesStore();
        },

        feedback() {
            return useFeedbackStore();
        },

        isEditing() {
            return Boolean(this.id);
        },

        title() {
            return this.isEditing ? 'Editar paciente' : 'Novo paciente';
        },

        addressOptions() {
            return this.addresses.items.map((address) => ({
                id: address.id,
                label: `${address.street}, ${address.neighborhood} - ${address.city}/${address.state}`,
            }));
        },
    },

    async created() {
        await this.addresses.fetch({ page: 1, per_page: 20, sort_by: 'city', sort_dir: 'asc' });

        if (this.isEditing) {
            await this.load();
        }
    },

    watch: {
        addressSearch(value) {
            clearTimeout(this.addressSearchTimer);
            this.addressSearchTimer = setTimeout(() => {
                this.addresses.fetch({
                    page: 1,
                    per_page: 20,
                    sort_by: 'city',
                    sort_dir: 'asc',
                    search: value || undefined,
                });
            }, 400);
        },
    },

    methods: {
        async load() {
            const patient = await this.store.fetchOne(this.id);
            this.form = {
                name: patient.name || '',
                cpf: patient.cpf || '',
                cns: patient.cns || '',
                birth_date: patient.birth_date || '',
                gender: patient.gender || '',
                phone: patient.phone || '',
                address_id: patient.address_id || null,
            };

            if (patient.address && !this.addresses.items.some((item) => item.id === patient.address.id)) {
                this.addresses.items = [patient.address, ...this.addresses.items];
            }
        },

        async submit() {
            const valid = await this.$refs.observer.validate();
            if (!valid) return;

            this.loading = true;
            this.error = null;

            try {
                const result = await this.store.save({ ...this.form, phone: this.form.phone || null }, this.id);
                this.feedback.success(result?.message || (this.isEditing ? 'Paciente atualizado com sucesso.' : 'Paciente cadastrado com sucesso.'));
                this.$router.push({ name: 'pacientes.index' });
            } catch (error) {
                this.error = error.response?.data?.message || 'Nao foi possivel salvar o paciente.';
                this.feedback.error(this.error);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.form-card {
    border-radius: 8px;
}
</style>

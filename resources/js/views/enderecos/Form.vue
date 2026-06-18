<template>
    <div>
        <div class="d-flex align-center justify-space-between mb-4">
            <div>
                <h1 class="text-h5 font-weight-bold mb-1">{{ title }}</h1>
                <p class="text-body-2 grey--text text--darken-1 mb-0">Informe os dados do endereco e use o CEP para preencher ViaCEP.</p>
            </div>
        </div>

        <v-card outlined class="pa-4 pa-md-6 form-card">
            <ValidationObserver ref="observer" v-slot="{ invalid }">
                <v-form @submit.prevent="submit">
                    <v-row>
                        <v-col cols="12" md="4">
                            <BaseInput
                                v-model="form.zip_code"
                                label="CEP"
                                rules="required|cep"
                                mask="cep"
                                :loading="cepLoading"
                                @blur="lookupCep"
                            />
                        </v-col>
                        <v-col cols="12" md="8">
                            <BaseInput v-model="form.street" label="Logradouro" rules="required" />
                        </v-col>
                        <v-col cols="12" md="5">
                            <BaseInput v-model="form.neighborhood" label="Bairro" rules="required" />
                        </v-col>
                        <v-col cols="12" md="5">
                            <BaseInput v-model="form.city" label="Cidade" rules="required" />
                        </v-col>
                        <v-col cols="12" md="2">
                            <ValidationProvider v-slot="{ errors }" name="UF" rules="required" slim>
                                <v-select
                                    v-model="form.state"
                                    :items="states"
                                    label="UF"
                                    outlined
                                    dense
                                    hide-details="auto"
                                    :error-messages="errors"
                                />
                            </ValidationProvider>
                        </v-col>
                    </v-row>

                    <v-alert v-if="error" type="error" dense text>{{ error }}</v-alert>

                    <div class="d-flex justify-end mt-4">
                        <v-btn text class="mr-2" :to="{ name: 'enderecos.index' }">Cancelar</v-btn>
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
import axios from 'axios';
import BaseInput from '../../components/BaseInput.vue';
import { BRAZILIAN_STATES } from '../../constants/states';

export default {
    name: 'AddressForm',

    components: { BaseInput },

    props: {
        id: { type: [String, Number], default: null },
    },

    data() {
        return {
            states: BRAZILIAN_STATES,
            form: {
                zip_code: '',
                street: '',
                neighborhood: '',
                city: '',
                state: '',
            },
            loading: false,
            cepLoading: false,
            error: null,
        };
    },

    computed: {
        isEditing() {
            return Boolean(this.id);
        },

        title() {
            return this.isEditing ? 'Editar endereco' : 'Novo endereco';
        },
    },

    created() {
        if (this.isEditing) {
            this.load();
        }
    },

    methods: {
        async load() {
            const address = await this.$store.dispatch('addresses/fetchOne', this.id);
            this.form = {
                zip_code: address.zip_code || '',
                street: address.street || '',
                neighborhood: address.neighborhood || '',
                city: address.city || '',
                state: address.state || '',
            };
        },

        async lookupCep() {
            if (this.form.zip_code.length !== 8) return;

            this.cepLoading = true;

            try {
                const { data } = await axios.get(`https://viacep.com.br/ws/${this.form.zip_code}/json/`);
                if (!data.erro) {
                    this.form.street = data.logradouro || this.form.street;
                    this.form.neighborhood = data.bairro || this.form.neighborhood;
                    this.form.city = data.localidade || this.form.city;
                    this.form.state = data.uf || this.form.state;
                }
            } finally {
                this.cepLoading = false;
            }
        },

        async submit() {
            const valid = await this.$refs.observer.validate();
            if (!valid) return;

            this.loading = true;
            this.error = null;

            try {
                const result = await this.$store.dispatch('addresses/save', { payload: { ...this.form, state: this.form.state.toUpperCase() }, id: this.id });
                this.$store.dispatch('feedback/success', result?.message || (this.isEditing ? 'Endereco atualizado com sucesso.' : 'Endereco cadastrado com sucesso.'));
                this.$router.push({ name: 'enderecos.index' });
            } catch (error) {
                this.error = error.response?.data?.message || 'Nao foi possivel salvar o endereco.';
                this.$store.dispatch('feedback/error', this.error);
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

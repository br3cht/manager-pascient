<template>
    <v-main class="login-page">
        <v-container class="fill-height" fluid>
            <v-row justify="center" align="center">
                <v-col cols="12" sm="8" md="5" lg="4">
                    <v-card class="pa-6" outlined>
                        <div class="mb-6">
                            <div class="text-h5 font-weight-bold primary--text">Conecta SUS</div>
                            <div class="text-body-2 grey--text text--darken-1">Acesse para gerenciar pacientes e enderecos.</div>
                        </div>

                        <ValidationObserver ref="observer" v-slot="{ invalid }">
                            <v-form @submit.prevent="submit">
                                <BaseInput v-model="form.email" label="E-mail" rules="required|email" type="email" />
                                <BaseInput v-model="form.password" class="mt-4" label="Senha" rules="required" type="password" />

                                <v-alert v-if="auth.error" type="error" dense text class="mt-4">{{ auth.error }}</v-alert>

                                <v-btn
                                    type="submit"
                                    color="primary"
                                    block
                                    large
                                    class="mt-6"
                                    :loading="auth.loading"
                                    :disabled="invalid"
                                >
                                    Entrar
                                </v-btn>
                            </v-form>
                        </ValidationObserver>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-main>
</template>

<script>
import BaseInput from '../components/BaseInput.vue';
import { useAuthStore } from '../store/auth';

export default {
    name: 'Login',

    components: { BaseInput },

    data() {
        return {
            form: {
                email: '',
                password: '',
            },
        };
    },

    computed: {
        auth() {
            return useAuthStore();
        },
    },

    methods: {
        async submit() {
            const valid = await this.$refs.observer.validate();
            if (!valid) return;

            await this.auth.login(this.form);
            this.$router.push(this.$route.query.redirect || { name: 'dashboard' });
        },
    },
};
</script>

<style scoped>
.login-page {
    background: #eef6fb;
    min-height: 100vh;
}
</style>

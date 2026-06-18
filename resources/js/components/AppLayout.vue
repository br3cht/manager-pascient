<template>
    <v-main class="app-shell">
        <v-navigation-drawer v-model="drawer" app color="primary" dark width="260">
            <v-list-item class="py-4">
                <v-list-item-content>
                    <v-list-item-title class="text-h6 font-weight-bold">Conecta SUS</v-list-item-title>
                    <v-list-item-subtitle>Gestao de pacientes</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>

            <v-divider class="primary lighten-2" />

            <v-list nav dense class="mt-3">
                <v-list-item v-for="item in menu" :key="item.to" :to="item.to" link exact>
                    <v-list-item-icon><v-icon>{{ item.icon }}</v-icon></v-list-item-icon>
                    <v-list-item-title>{{ item.label }}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app color="white" elevation="1">
            <v-app-bar-nav-icon class="d-md-none" @click="drawer = !drawer" />
            <v-toolbar-title class="primary--text font-weight-bold">{{ pageTitle }}</v-toolbar-title>
            <v-spacer />
            <v-btn text color="primary" :loading="auth.loading" @click="logout">
                <v-icon left>mdi-logout</v-icon>
                Sair
            </v-btn>
        </v-app-bar>

        <v-container fluid class="pa-4 pa-md-6">
            <router-view />
        </v-container>

        <v-snackbar
            :value="feedback.open"
            :color="feedback.color"
            :timeout="feedback.timeout"
            top
            right
            @input="feedback.close()"
        >
            {{ feedback.message }}
            <template #action="{ attrs }">
                <v-btn text v-bind="attrs" @click="feedback.close()">Fechar</v-btn>
            </template>
        </v-snackbar>
    </v-main>
</template>

<script>
import { useAuthStore } from '../store/auth';
import { useFeedbackStore } from '../store/feedback';

export default {
    name: 'AppLayout',

    data() {
        return {
            drawer: true,
            menu: [
                { label: 'Dashboard', to: '/dashboard', icon: 'mdi-view-dashboard-outline' },
                { label: 'Enderecos', to: '/enderecos', icon: 'mdi-map-marker-outline' },
                { label: 'Pacientes', to: '/pacientes', icon: 'mdi-account-heart-outline' },
            ],
        };
    },

    computed: {
        auth() {
            return useAuthStore();
        },

        feedback() {
            return useFeedbackStore();
        },

        pageTitle() {
            const titles = {
                dashboard: 'Dashboard',
                'enderecos.index': 'Enderecos',
                'enderecos.create': 'Novo endereco',
                'enderecos.edit': 'Editar endereco',
                'pacientes.index': 'Pacientes',
                'pacientes.create': 'Novo paciente',
                'pacientes.edit': 'Editar paciente',
            };

            return titles[this.$route.name] || 'Conecta SUS';
        },
    },

    methods: {
        async logout() {
            await this.auth.logout();
            this.$router.push({ name: 'login' });
        },
    },
};
</script>

<style scoped>
.app-shell {
    background: #f4f8fb;
    min-height: 100vh;
}
</style>

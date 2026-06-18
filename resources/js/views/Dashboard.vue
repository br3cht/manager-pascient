<template>
    <div>
        <v-row>
            <v-col v-for="card in cards" :key="card.label" cols="12" md="6" lg="4">
                <v-card outlined class="pa-5 stat-card">
                    <div class="d-flex align-center justify-space-between">
                        <div>
                            <div class="text-body-2 grey--text text--darken-1">{{ card.label }}</div>
                            <div class="text-h4 font-weight-bold primary--text mt-1">{{ card.value }}</div>
                        </div>
                        <v-avatar color="primary" size="48">
                            <v-icon dark>{{ card.icon }}</v-icon>
                        </v-avatar>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'Dashboard',

    computed: {
        dashboard() {
            return this.$store.state.dashboard;
        },

        cards() {
            return [
                { label: 'Pacientes cadastrados', value: this.dashboard.totals.patients_total, icon: 'mdi-account-heart-outline' },
                { label: 'Enderecos cadastrados', value: this.dashboard.totals.addresses_total, icon: 'mdi-map-marker-outline' },
            ];
        },
    },

    created() {
        this.$store.dispatch('dashboard/fetchTotals');
    },
};
</script>

<style scoped>
.stat-card {
    border-radius: 8px;
}
</style>

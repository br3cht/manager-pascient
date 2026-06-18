<template>
    <v-row class="not-found" align="center" justify="center">
        <v-col cols="12" md="6" class="text-center">
            <v-icon size="96" color="primary">mdi-compass-off-outline</v-icon>
            <h1 class="text-h3 font-weight-bold primary--text mt-4">404</h1>
            <p class="text-body-1 grey--text text--darken-2 mb-6">A pagina que voce procura nao foi encontrada.</p>
            <v-btn color="primary" :to="{ name: 'dashboard' }">
                <v-icon left>mdi-home-outline</v-icon>
                Voltar ao inicio
            </v-btn>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'NotFound',
};
</script>

<style scoped>
.not-found {
    min-height: 60vh;
}
</style>

<template>
    <v-dialog :value="value" max-width="420" @input="$emit('input', $event)">
        <v-card>
            <v-card-title class="text-h6">{{ title }}</v-card-title>
            <v-card-text>{{ message }}</v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn text @click="$emit('input', false)">Cancelar</v-btn>
                <v-btn color="error" :loading="loading" @click="$emit('confirm')">Excluir</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ConfirmModal',

    props: {
        value: { type: Boolean, default: false },
        title: { type: String, default: 'Confirmar exclusao' },
        message: { type: String, default: 'Deseja excluir este registro?' },
        loading: { type: Boolean, default: false },
    },
};
</script>

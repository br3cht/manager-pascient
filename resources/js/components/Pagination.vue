<template>
    <div class="d-flex flex-column flex-md-row align-md-center justify-space-between mt-4">
        <div class="text-body-2 grey--text text--darken-1 mb-3 mb-md-0">
            {{ label }}
        </div>
        <v-pagination
            :value="page"
            :length="lastPage"
            :total-visible="7"
            color="primary"
            @input="$emit('change', $event)"
        />
    </div>
</template>

<script>
export default {
    name: 'Pagination',

    props: {
        meta: { type: Object, default: () => ({}) },
        page: { type: Number, default: 1 },
    },

    computed: {
        lastPage() {
            return Number(this.meta.last_page || 1);
        },

        label() {
            const total = Number(this.meta.total || 0);
            const from = this.meta.from || 0;
            const to = this.meta.to || 0;

            return total ? `Exibindo ${from} a ${to} de ${total}` : 'Nenhum resultado';
        },
    },
};
</script>

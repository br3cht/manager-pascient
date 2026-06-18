<template>
    <v-card outlined class="table-card">
        <v-progress-linear v-if="loading" indeterminate color="primary" />
        <v-simple-table>
            <thead>
                <tr>
                    <th
                        v-for="header in headers"
                        :key="header.value"
                        :class="{ sortable: header.sortable !== false }"
                        @click="sort(header)"
                    >
                        <span>{{ header.text }}</span>
                        <v-icon v-if="isSorted(header)" small color="primary">
                            {{ sortDir === 'asc' ? 'mdi-arrow-up' : 'mdi-arrow-down' }}
                        </v-icon>
                    </th>
                    <th v-if="$scopedSlots.actions" class="text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!loading && !items.length">
                    <td :colspan="headers.length + ($scopedSlots.actions ? 1 : 0)" class="text-center py-8 grey--text">
                        Nenhum registro encontrado.
                    </td>
                </tr>
                <tr v-for="item in items" :key="item.id">
                    <td v-for="header in headers" :key="header.value">
                        <slot :name="`cell-${header.value}`" :item="item">
                            {{ item[header.value] }}
                        </slot>
                    </td>
                    <td v-if="$scopedSlots.actions" class="text-right text-no-wrap">
                        <slot name="actions" :item="item" />
                    </td>
                </tr>
            </tbody>
        </v-simple-table>
    </v-card>
</template>

<script>
export default {
    name: 'BaseTable',

    props: {
        headers: { type: Array, required: true },
        items: { type: Array, default: () => [] },
        loading: { type: Boolean, default: false },
        sortBy: { type: String, default: 'id' },
        sortDir: { type: String, default: 'asc' },
    },

    methods: {
        isSorted(header) {
            return header.value === this.sortBy && header.sortable !== false;
        },

        sort(header) {
            if (header.sortable === false) return;

            const nextDir = this.sortBy === header.value && this.sortDir === 'asc' ? 'desc' : 'asc';
            this.$emit('sort', { sort_by: header.value, sort_dir: nextDir });
        },
    },
};
</script>

<style scoped>
.table-card {
    border-radius: 8px;
    overflow: hidden;
}

th.sortable {
    cursor: pointer;
    user-select: none;
}
</style>

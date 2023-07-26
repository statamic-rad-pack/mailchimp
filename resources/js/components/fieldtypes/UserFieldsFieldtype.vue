<template>
    <div class="user-field-fieldtype-wrapper">
        <v-select
            append-to-body
            v-model="selected"
            :clearable="true"
            :options="fields"
            :reduce="(option) => option.id"
            :searchable="true"
            @input="$emit('input', $event)"
        />
    </div>
</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],

    data() {
        return {
            selected: null,
            fields: [],
        }
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },

    methods: {
        refreshFields() {
            this.$axios
                .get(cp_url(`/mailchimp/user-fields`))
                .then(response => {
                    this.fields = response.data;
                });
        }
    }
};
</script>

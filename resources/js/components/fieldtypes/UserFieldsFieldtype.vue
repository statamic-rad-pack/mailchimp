<template>
    <div class="user-field-fieldtype-wrapper">
        <ui-combobox
            class="w-full"
            clearable="true"
            :label="__('Choose')"
            v-model="selected"
            :options="fields"
            optionValue="id"
            searchable="true"
        />
    </div>
</template>

<script>
import { FieldtypeMixin as Fieldtype } from '@statamic/cms';

export default {

    mixins: [Fieldtype],

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
                })
                .catch(() => { this.fields = []; });
        }
    },

    watch: {
      selected(selected) {
        this.update(selected);
      }
    }
};
</script>

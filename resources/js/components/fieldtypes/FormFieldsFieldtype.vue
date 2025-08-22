<template>
    <div class="form-field-fieldtype-wrapper">
        <ui-description v-if="!form">{{ __('Select form') }}</ui-description>

        <ui-combobox
            v-if="showFieldtype && form"
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
            showFieldtype: true,
            fields: [],
        }
    },

    watch: {
        form(form) {
            this.showFieldtype = false;

            this.refreshFields();

            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        form() {
            return StatamicConfig.urlPath.split('/')[1] ?? '';
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },


    methods: {
        refreshFields() {
            this.$axios
                .get(cp_url(`/mailchimp/form-fields/${this.form}`))
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

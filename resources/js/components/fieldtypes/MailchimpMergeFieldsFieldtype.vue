<template>
    <div class="mailchimp-merge-fields-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!list">{{ __('Select audience') }}</small>

        <v-select
            append-to-body
            v-if="showFieldtype && list"
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
            fields: [],
            selected: null,
            showFieldtype: true,
        }
    },

    watch: {
        list(list) {
            this.showFieldtype = false;

            this.refreshFields();

            this.$nextTick(() => this.showFieldtype = true);
        }
    },

    computed: {
        key() {            
            let matches = this.namePrefix.match(/([a-z]*?)\[(.*?)\]/);
            
            if (matches[1] == 'mailchimp') { // form page
                return 'mailchimp.settings.audience_id.0';                
            }
            
            return matches[0].replace('[', '.').replace(']', '.') + 'audience_id.0';
        },

        list() {
            return data_get(this.$store.state.publish[this.storeName].values, this.key)
        },
    },

    mounted() {
        this.selected = this.value;
        this.refreshFields();
    },

    methods: {
        refreshFields() {
            this.$axios
                .get(cp_url(`/mailchimp/merge-fields/${this.list}`))
                .then(response => {
                    this.fields = response.data;
                })
                .catch(() => { this.fields = []; });
        }
    }
};
</script>

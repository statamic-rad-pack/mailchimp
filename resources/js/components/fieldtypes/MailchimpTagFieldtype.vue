<template>
    <div class="mailchimp-tag-fieldtype-wrapper">
        <small class="help-block text-grey-60" v-if="!list">{{ __('Select audience') }}</small>

        <v-select
            append-to-body
            v-if="showFieldtype && list"
            v-model="selected"
            :clearable="true"
            :options="tags"
            :reduce="(option) => option.id"
            :placeholder="__('Choose...')"
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
            showFieldtype: true,
            tags: [],
        }
    },

    watch: {
        list(list) {
            this.showFieldtype = false;

            this.refreshTags();

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
            return data_get(
                this.$store.state.publish[this.storeName].values,
                this.key
            )
        },

    },

    mounted() {
        this.selected = this.value;
        this.refreshTags();
    },

    methods: {
        refreshTags() {
            this.$axios
                .get(cp_url(`/mailchimp/tags/${this.list}`))
                .then(response => {
                    this.tags = response.data ?? [];
                })
                .catch(() => { this.tags = []; });
        }
    }
};
</script>

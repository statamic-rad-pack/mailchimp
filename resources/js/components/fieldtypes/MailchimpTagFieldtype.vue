<template>
    <div class="mailchimp-tag-fieldtype-wrapper">
        <ui-description v-if="!list">{{ __('Select audience') }}</ui-description>

        <ui-combobox
            v-if="showFieldtype && list"
            class="w-full"
            clearable="true"
            :label="__('Choose')"
            v-model="selected"
            :options="tags"
            optionValue="id"
            searchable="true"
        />
    </div>
</template>

<script>
import { FieldtypeMixin as Fieldtype } from '@statamic/cms';
import { publishContextKey } from '@statamic/cms/ui';

export default {

    mixins: [Fieldtype],

    inject: {
        publishContext: { from: publishContextKey },
    },

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
            return 'mailchimp.settings.audience_id.0';
        },
        
        list() {
            return this.publishContext.values.value[this.key] ?? '';
        },

    },

    mounted() {
        this.selected = this.value;
        this.refreshTags();
    },

    methods: {
        refreshTags() {
            if (! this.list) {
                this.tags = [];
                return;
            }

            this.$axios
                .get(cp_url(`/mailchimp/tags/${this.list}`))
                .then(response => {
                    this.tags = response.data ?? [];
                })
                .catch(() => { this.tags = []; });
        }
    },

    watch: {
      selected(selected) {
        this.update(selected);
      }
    }
};
</script>

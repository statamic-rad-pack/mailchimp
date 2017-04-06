Vue.component('mailchimp-fieldtype', {
    template: `
        <div>
            <div v-if="loading" class="loading loading-basic">
                <span class="icon icon-circular-graph animation-spin"></span> {{ translate('cp.loading') }}
            </div>

            <div v-else class="row">
                <div class="col-xs-4">Form: <suggest-fieldtype :data.sync="data.form" :config="config" :suggestions-prop="forms"></suggest-fieldtype></div> 
                <div v-if="data.form" class="col-xs-4">
                    Check Permission: <toggle-fieldtype :data.sync="data.check_permission"></toggle-fieldtype>
                </div>
                <div v-if="data.check_permission && !repopulatingFields" class="col-xs-4">
                    Permission Field: <suggest-fieldtype :data.sync="data.permission_field" :config="config" :suggestions-prop="fields"></suggest-fieldtype>
                </div>
        </div>
    `,

    props: ['data'],

    data: function() {
        return {
            loading: true,
            repopulatingFields: false,
            forms: [],
            fields: [],
            config: {
                type: 'suggest',
                max_items: 1
            },
        }
    },

    methods: {
        getForms: function() {
            this.$http.get('/!/Mailchimp/forms', function(data) {
                this.forms = data;
                this.loading = false;
                this.getFields();
            });
        },
        getFields: function() {
            let formName = this.data.form ? this.data.form[0] : null;

            if (formName) {
                this.repopulatingFields = true;
                
                this.$nextTick(function() {
                    let selectedForm = this.forms.filter(function(form) {
                        return form.value == formName;
                    })[0];

                    this.fields = selectedForm.fields;
                    this.repopulatingFields = false;
                });
            }
        },
        resetFields: function() {
            this.fields = [];
            this.data.permission_field = null;
        },
    },

    watch: {
        'data.form': function() {
            this.resetFields();
            this.getFields();
        },
        'data.check_permission': function() {
            this.resetFields();
        }
    },

    ready: function() {
        this.getForms();
    }
});
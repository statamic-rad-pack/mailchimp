import TagField from './components/fieldtypes/MailchimpTagFieldtype.vue';
import FormField from './components/fieldtypes/FormFieldFieldtype.vue';
import MergeFieldsField from './components/fieldtypes/MailchimpMergeFieldsFieldtype.vue';


Statamic.booting(() => {
    Statamic.$components.register('mailchimp_tag-fieldtype', TagField);
    Statamic.$components.register('form_field-fieldtype', FormField);
    Statamic.$components.register('mailchimp_merge_fields-fieldtype', MergeFieldsField);
});

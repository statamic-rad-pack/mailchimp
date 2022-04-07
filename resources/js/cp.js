import TagField from './components/fieldtypes/MailchimpTagFieldtype.vue';
import FormField from './components/fieldtypes/FormFieldFieldtype.vue';
import MergeFieldsField from './components/fieldtypes/MailchimpMergeFieldsFieldtype.vue';
import UserFieldsField from './components/fieldtypes/UserFieldsFieldtype.vue';


Statamic.booting(() => {
    Statamic.$components.register('mailchimp_tag-fieldtype', TagField);
    Statamic.$components.register('form_field-fieldtype', FormField);
    Statamic.$components.register('mailchimp_merge_fields-fieldtype', MergeFieldsField);
    Statamic.$components.register('user_fields-fieldtype', UserFieldsField);
});

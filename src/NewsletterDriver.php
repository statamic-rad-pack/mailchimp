<?php

namespace StatamicRadPack\Mailchimp;

use DrewM\MailChimp\MailChimp;

/**
 * Newsletter
 */
class NewsletterDriver
{
    /** @var \DrewM\MailChimp\MailChimp */
    protected $mailChimp;

    /** @var \StatamicRadPack\Mailchimp\NewsletterListCollection */
    protected $lists;

    /**
     * Creates a new newsletter instance
     */
    public function __construct(MailChimp $mailChimp, NewsletterListCollection $lists)
    {
        $this->mailChimp = $mailChimp;

        $this->lists = $lists;
    }

    /**
     * Subscribe
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function subscribe(string $email, array $mergeFields = [], string $listName = '', array $options = [])
    {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $mergeFields, $options);

        $response = $this->mailChimp->post("lists/{$list->getId()}/members", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * Subscribe pending
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function subscribePending(string $email, array $mergeFields = [], string $listName = '', array $options = [])
    {
        $options = array_merge($options, ['status' => 'pending']);

        return $this->subscribe($email, $mergeFields, $listName, $options);
    }

    /**
     * Subscribe or update
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function subscribeOrUpdate(string $email, array $mergeFields = [], string $listName = '', array $options = [])
    {
        $list = $this->lists->findByName($listName);

        $options = $this->getSubscriptionOptions($email, $mergeFields, $options);

        $response = $this->mailChimp->put("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * Get members
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function getMembers(string $listName = '', array $parameters = [])
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members", $parameters);
    }

    /**
     * Get a member
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function getMember(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");
    }

    /**
     * Get activity for a member
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function getMemberActivity(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/activity");
    }

    /**
     * Determine is member exists
     *
     *
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function hasMember(string $email, string $listName = ''): bool
    {
        $response = $this->getMember($email, $listName);

        if (! isset($response['email_address'])) {
            return false;
        }

        if (strtolower($response['email_address']) != strtolower($email)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if email is subscribed
     *
     *
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function isSubscribed(string $email, string $listName = ''): bool
    {
        $response = $this->getMember($email, $listName);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        if ($response['status'] != 'subscribed') {
            return false;
        }

        return true;
    }

    /**
     * Unsubscribe
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function unsubscribe(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->patch("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", [
            'status' => 'unsubscribed',
        ]);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * Change member's email address
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function updateEmailAddress(string $currentEmailAddress, string $newEmailAddress, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->patch("lists/{$list->getId()}/members/{$this->getSubscriberHash($currentEmailAddress)}", [
            'email_address' => $newEmailAddress,
        ]);

        return $response;
    }

    /**
     * Remove an email
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function delete(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->delete("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}");

        return $response;
    }

    /**
     * Delete permanently
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function deletePermanently(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->post("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/actions/delete-permanent");

        return $response;
    }

    /**
     * Get tags
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function getTags(string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        return $this->mailChimp->get("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/tags");
    }

    /**
     * Add one or more tags
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function addTags(array $tags, string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $payload = collect($tags)->map(function ($tag) {
            return ['name' => $tag, 'status' => 'active'];
        })->toArray();

        return $this->mailChimp->post("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/tags", [
            'tags' => $payload,
        ]);
    }

    /**
     * Remove tags
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function removeTags(array $tags, string $email, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $payload = collect($tags)->map(function ($tag) {
            return ['name' => $tag, 'status' => 'inactive'];
        })->toArray();

        return $this->mailChimp->post("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}/tags", [
            'tags' => $payload,
        ]);
    }

    /**
     * Create a new campaign
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function createCampaign(
        string $fromName,
        string $replyTo,
        string $subject,
        string $html = '',
        string $listName = '',
        array $options = [],
        array $contentOptions = []
    ) {
        $list = $this->lists->findByName($listName);

        $defaultOptions = [
            'type' => 'regular',
            'recipients' => [
                'list_id' => $list->getId(),
            ],
            'settings' => [
                'subject_line' => $subject,
                'from_name' => $fromName,
                'reply_to' => $replyTo,
            ],
        ];

        $options = array_merge($defaultOptions, $options);

        $response = $this->mailChimp->post('campaigns', $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        if ($html === '') {
            return $response;
        }

        if (! $this->updateContent($response['id'], $html, $contentOptions)) {
            return false;
        }

        return $response;
    }

    /**
     * Update content of a campaign
     *
     *
     * @return array|bool
     */
    public function updateContent(string $campaignId, string $html, array $options = [])
    {
        $defaultOptions = compact('html');

        $options = array_merge($defaultOptions, $options);

        $response = $this->mailChimp->put("campaigns/{$campaignId}/content", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }

    /**
     * Return the API driver
     */
    public function getApi(): MailChimp
    {
        return $this->mailChimp;
    }

    /**
     * Returns the last error
     *
     * @return string|false
     */
    public function getLastError()
    {
        return $this->mailChimp->getLastError();
    }

    /**
     * Determine if last action succeeded
     */
    public function lastActionSucceeded(): bool
    {
        return $this->mailChimp->success();
    }

    /**
     * Returns a subscribed email's hash
     */
    protected function getSubscriberHash(string $email): string
    {
        return $this->mailChimp->subscriberHash($email);
    }

    /**
     * Return subscription options
     */
    protected function getSubscriptionOptions(string $email, array $mergeFields, array $options): array
    {
        $defaultOptions = [
            'email_address' => $email,
            'status' => 'subscribed',
            'email_type' => 'html',
        ];

        if (count($mergeFields)) {
            $defaultOptions['merge_fields'] = $mergeFields;
        }

        $options = array_merge($defaultOptions, $options);

        return $options;
    }

    /**
     * Returns the marketing permissions
     *
     *
     * @return array|false
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function getMarketingPermissions(string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $response = $this->mailChimp->get("lists/{$list->getId()}/members");

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        $marketingPermissions = collect($response['members'][0]['marketing_permissions'])
            ->map(function ($permission) {
                return [
                    'text' => $permission['text'],
                    'id' => $permission['marketing_permission_id'],
                ];
            })
            ->toArray();

        return $marketingPermissions;
    }

    /**
     * Set the marketing permissions for an email
     *
     *
     * @return array|bool
     *
     * @throws Exceptions\InvalidNewsletterList
     */
    public function setMarketingPermission(string $email, string $permission, bool $bool, string $listName = '')
    {
        $list = $this->lists->findByName($listName);

        $id = $list->getMarketingPermission($permission);

        $permissions = [
            'marketing_permissions' => [
                [
                    'marketing_permission_id' => $id,
                    'enabled' => $bool,
                ],
            ],
        ];

        $options = $this->getSubscriptionOptions($email, [], $permissions);

        $response = $this->mailChimp->put("lists/{$list->getId()}/members/{$this->getSubscriberHash($email)}", $options);

        if (! $this->lastActionSucceeded()) {
            return false;
        }

        return $response;
    }
}

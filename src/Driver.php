<?php

namespace StatamicRadPack\Mailchimp;

class Driver extends \Spatie\Newsletter\Drivers\MailChimpDriver
{
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

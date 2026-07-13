<?php
declare(strict_types=1);

namespace SmartEmailing\Api;

use SmartEmailing\Api\Model\ChangeEmailAddress;
use SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartEmailing\Api\Model\Search\Contacts as SearchContact;
use SmartEmailing\Api\Model\Search\SingleContact as SearchSingleContact;
use SmartEmailing\Exception\AllowedTypeException;
use SmartEmailing\Util\Helpers;

/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts
 * @package SmartEmailing\Api
 */
class Contacts extends AbstractApi
{
    public const IN_LISTS_STATUS_CONFIRMED = 'confirmed';
    public const IN_LISTS_STATUS_UNSUBSCRIBED = 'unsubscribed';

    /**
     * Confirm a pending double-opt-in request.
     *
     * @param string $requestId DOI request ID to confirm
     */
    public function confirmDoubleOptIn(string $requestId): Response
    {
        return new Response($this->post('double-opt-in-confirmation', ['id' => $requestId]));
    }

    /**
     * Get contacts filtered by contactlist membership.
     *
     * @param array $listIds contactlist IDs to filter by
     * @param string|null $status one of the IN_LISTS_STATUS_* constants
     */
    public function getInLists(
        array $listIds = [],
        ?string $status = null,
        int $limit = 500,
        int $offset = 0
    ): Response {
        if ($status !== null) {
            AllowedTypeException::check(
                $status,
                [self::IN_LISTS_STATUS_CONFIRMED, self::IN_LISTS_STATUS_UNSUBSCRIBED]
            );
        }

        $query = [];

        if (\count($listIds) > 0) {
            $query['listIds'] = \implode(',', $listIds);
        }

        if ($status !== null) {
            $query['status'] = $status;
        }

        $query['limit'] = $limit;
        $query['offset'] = $offset;

        return new Response($this->get('contacts/in-lists', $query));
    }

    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Change_Contacts_e_mail_address
     */
    public function changeEmailAddress(ChangeEmailAddress $changeEmailAddress): Response
    {
        return new Response($this->post('change-emailaddress', $changeEmailAddress->toArray()));
    }

    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Forget_contact
     */
    public function forgetContact(int $idContact): Response
    {
        return new Response(
            $this->delete(
                $this->replaceUrlParameters(
                    'contacts/forget/:id',
                    [
                    'id' => $idContact,
                    ]
                )
            )
        );
    }

    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Get_Contacts_with_lists_and_customfield_values
     */
    public function getList(?SearchContact $search = null): Response
    {
        $search ??= new SearchContact();
        return new Response($this->get('contacts', $search->getAsQuery()));
    }

    /**
     * Get single contact by e-mail address (API v3 addresses contacts by e-mail, not id).
     *
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts-Get_Single_contact_with_lists_and_customfield_values
     */
    public function getSingle(string $emailAddress, ?SearchSingleContact $search = null): Response
    {
        Helpers::validateEmail($emailAddress);
        $search ??= new SearchSingleContact();
        return new Response(
            $this->get(
                'contacts/' . \rawurlencode($emailAddress),
                $search->getAsQuery()
            )
        );
    }
}

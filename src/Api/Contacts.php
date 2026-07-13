<?php
declare(strict_types=1);

namespace SmartEmailing\Api;

use SmartEmailing\Api\Model\ChangeEmailAddress;
use SmartEmailing\Api\Model\Response\BaseResponse as Response;
use SmartEmailing\Api\Model\Search\Contacts as SearchContact;
use SmartEmailing\Api\Model\Search\SingleContact as SearchSingleContact;
use SmartEmailing\Util\Helpers;

/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts
 * @package SmartEmailing\Api
 */
class Contacts extends AbstractApi
{
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

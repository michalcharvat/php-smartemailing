<?php
declare(strict_types=1);

namespace SmartEmailing\Api;

use SmartEmailing\Api\Model\Response\BaseResponse as Response;

/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html
 * @package SmartEmailing\Api
 */
class Account extends AbstractApi
{
    /**
     * Get basic information about the account.
     */
    public function getInfo(): Response
    {
        return new Response($this->get('account-info'));
    }
}

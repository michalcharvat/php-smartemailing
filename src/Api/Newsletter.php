<?php
declare(strict_types=1);

namespace SmartEmailing\Api;

use SmartEmailing\Api\Model\Newsletter as NewsletterModel;
use SmartEmailing\Api\Model\Response\BaseResponse as Response;

/**
 * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Newsletter
 * @package SmartEmailing\Api
 */
class Newsletter extends AbstractApi
{
    /**
     * @see https://app.smartemailing.cz/docs/api/v3/index.html#api-Newsletter-Create_newsletter
     */
    public function create(NewsletterModel $newsletter): Response
    {
        return new Response($this->post('newsletter', $newsletter->toArray()));
    }

    /**
     * Get list of finished newsletters.
     *
     * @param int|null $id filter by newsletter ID
     * @param int|null $emailId filter by email ID
     */
    public function getList(?int $id = null, ?int $emailId = null, int $limit = 500, int $offset = 0): Response
    {
        $query = [];

        if ($id !== null) {
            $query['filter[id][eq]'] = $id;
        }

        if ($emailId !== null) {
            $query['filter[email_id][eq]'] = $emailId;
        }

        $query['limit'] = $limit;
        $query['offset'] = $offset;

        return new Response($this->get('newsletter', $query));
    }
}

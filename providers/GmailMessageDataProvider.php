<?php

namespace machour\yii2\google\gmail\providers;

use Google_Service_Gmail_Thread;
use Yii;

/**
 * Class GmailMessageDataProvider
 *
 * @package app\components
 */
class GmailMessageDataProvider extends GmailDataProvider
{

    /**
     * Returns the full email message data with body content parsed in the
     * payload field; the raw field is not used. (default)
     */
    const FORMAT_FULL = 'full';

    /**
     * Returns only email message ID, labels, and email headers.
     */
    const FORMAT_METADATA = 'metadata';

    /**
     * Returns only email message ID and labels; does not return the email
     * headers, body, or payload.
     */
    const FORMAT_MINIMAL = 'minimal';

    /**
     * Returns the full email message data with body content in the raw field as
     * a base64url encoded string; the payload field is not used.
     */
    const FORMAT_RAW = 'raw';

    /**
     * @var
     */
    public $threadId = null;

    /**
     * @var Google_Service_Gmail_Thread
     */
    private $thread;

    /**
     * @inheritdoc
     */
    protected function getApi()
    {
        return $this->service->users_messages;
    }

    /**
     * @inheritdoc
     */
    protected function getListAndPagers($parameters)
    {
        $this->thread = $this->service->users_threads->get($this->userId, $this->threadId, [
            'format' => 'full'
        ]);

        return [
            'list' => $this->thread->getMessages(),
            'next' => null,
        ];
    }

    /**
     * Returns a value indicating the total number of data models in this data provider.
     * @return integer total number of data models in this data provider.
     */
    protected function prepareTotalCount()
    {
        return count($this->thread->getMessages());
    }

}
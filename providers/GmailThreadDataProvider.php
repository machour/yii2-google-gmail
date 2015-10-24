<?php

namespace machour\yii2\google\gmail\providers;

use Yii;

/**
 * Class GmailThreadDataProvider
 *
 * @package app\components
 */
class GmailThreadDataProvider extends GmailDataProvider
{
    /**
     * @inheritdoc
     */
    protected function getApi()
    {
        return $this->service->users_threads;
    }

    /**
     * @inheritdoc
     */
    protected function getListAndPagers($parameters)
    {
        /** @var \Google_Service_Gmail_ListThreadsResponse $usersThreads */
        $usersThreads = $this->api->listUsersThreads($this->userId, $parameters);

        return [
            'list' => $usersThreads->getThreads(),
            'next' => $usersThreads->nextPageToken,
        ];
    }

    /**
     * Returns a value indicating the total number of data models in this data provider.
     * @return integer total number of data models in this data provider.
     */
    protected function prepareTotalCount()
    {
        return $this->service->users->getProfile($this->userId)->threadsTotal;
    }

}
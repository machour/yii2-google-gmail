<?php

namespace machour\yii2\google\gmail\providers;

use Google_Service_Gmail;
use Yii;
use yii\data\BaseDataProvider;

abstract class GmailDataProvider extends BaseDataProvider
{

    /**
     * @var Google_Service_Gmail $service The Service instance
     */
    public $service;

    /**
     * @var
     */
    public $userId = 'me';

    /**
     * Gets the GMail API to be used
     *
     * @return Google_Service_Gmail
     */
    abstract protected function getApi();

    /**
     * Gets the list of objects from the current API
     *
     * @param $parameters
     * @return Google_Service_Gmail
     */
    abstract protected function getListAndPagers($parameters);

    /**
     * @var string The session key
     */
    public $sessionKey = 'gmail';

    /**
     * @var string The format to return the message in.
     */
    public $format = 'full';

    /**
     * Prepares the data models that will be made available in the current page.
     * @return array the available data models
     */
    protected function prepareModels()
    {
        $pagination = $this->getPagination();
        $pagination->setPageSize(4);

        $pageSize = $pagination->getPageSize();

        $currentPage = $pagination->getPage() + 1;

        if (!Yii::$app->session->has($this->sessionKey)) {
            Yii::$app->session->set($this->sessionKey, [
                $pageSize => [
                    1 => null,
                ]
            ]);
        }

        $bag = Yii::$app->session->get($this->sessionKey);

        $models = [];


        $currentToken = isset($bag[$pageSize][$currentPage]) ? $bag[$pageSize][$currentPage] : null;

        if ($currentToken === null && $currentPage > 1) {
            // An unknown page was requested, get back to first page
            // & reset session
            $currentPage = 1;
            Yii::$app->session->set($this->sessionKey, [
                $pageSize => [
                    1 => null,
                ]
            ]);
            $bag = Yii::$app->session->get($this->sessionKey);
            $pagination->setPage(0);
        }

        $lp = $this->getListAndPagers([
            'maxResults' => $pagination->getPageSize(),
            'labelIds' => 'INBOX',
            'pageToken' => $currentToken
        ]);

        $list = $lp['list'];
        $bag[$pageSize][$currentPage + 1] = $lp['next'];

        Yii::$app->session->set($this->sessionKey, $bag);

        $count = count($list);

        if ($count != 0) {
            $this->service->getClient()->setUseBatch(true);
            $batch = $this->service->createBatch();
            for ($i = 1; $i <= $count; $i++) {
                $batch->add($this->api->get($this->userId, $list[$i - 1]->getId(), ['format' => $this->format]), $i);
            }

            $results = $batch->execute();
            $this->service->getClient()->setUseBatch(false);
            for ($i = 1; $i <= $count; $i++) {
                $models[] = $results['response-' . $i];
            }

        }

        return $models;
    }

    /**
     * Prepares the keys associated with the currently available data models.
     * @param array $models the available data models
     * @return array the keys
     */
    protected function prepareKeys($models)
    {
        return array_keys($models);
    }

}
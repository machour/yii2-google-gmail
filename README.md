# yii2-google-gmail

Data providers, widgets and helpers suited for the official GMail Api v1.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist machour/yii2-google-gmail "*"
```

or add

```
"machour/yii2-google-gmail": "*"
```

to the require section of your `composer.json` file.

Usage
-----

**Displaying your latest threads in a grid**

Controller :

```php
public function actionIndex()
{
    $pagination = new Pagination();
    $dataProvider = new GmailThreadDataProvider([
        // Service must be a Google_Service_Gmail instance
        // Here, machour/yii2-google-apiclient is used to get that instance
        'service' => Yii::$app->gmail->getService(),
        'pagination' => $pagination,
    ]);
    $pagination->totalCount = $dataProvider->getTotalCount();

    return $this->render('index', [
        'dataProvider' => $dataProvider
    ]);
}
```

View file :

```php

use machour\yii2\google\gmail\widgets\GmailGridView;
use machour\yii2\google\gmail\helpers\GmailHelper as GH;
use yii\bootstrap\Html;

echo GmailGridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'date',
            'value' => function ($thread, $key, $index, $widget) {
                /** @var Google_Service_Gmail_Thread $thread */
                return GH::getMessageHeader(GH::getThreadLastMessage($thread), 'Date');
            }
        ],
        [
            'attribute' => 'sender',
            'value' => function ($thread, $key, $index, $widget) {
                /** @var Google_Service_Gmail_Thread $thread */
                return GH::getThreadLastParticipant($thread);
            }
        ],
        [
            'attribute' => 'subject',
            'value' => function ($thread, $key, $index, $widget) {
                /** @var Google_Service_Gmail_Thread $thread */
                $subject = GH::getThreadSubject($thread);
                if (!$subject) {
                    $subject = '<i>' . Yii::t('app', 'No subject') . '</i>';
                }
                return $subject;
            },
            'format' => 'html',
        ],
        [
            'attribute' => 'Number of messages',
            'value' => function ($thread, $key, $index, $widget) {
                /** @var Google_Service_Gmail_Thread $thread */
                return GH::getThreadMessagesCount($thread);
            }
        ],
        [
            'attribute' => 'Actions',
            'value' => function ($thread, $key, $index, $widget) {
                /** @var Google_Service_Gmail_Thread $thread */
                return Html::a('View thread', ['gmail/thread', 'id' => $thread->getId()]);
            },
            'format' => 'html',
        ]

    ],
]);
```

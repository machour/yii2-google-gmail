<?php

namespace machour\yii2\google\gmail\widgets;

use yii\grid\GridView;

class GmailGridView extends GridView
{

    /**
     * @var array
     */
    public $pager = [
        'class' => 'machour\yii2\google\gmail\widgets\GmailPager'
    ];
}
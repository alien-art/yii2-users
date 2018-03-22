<?php

namespace alien\users;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class UsersAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@alien/users/assets';

    public $js = [
        'js/users.js'
    ];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];
}

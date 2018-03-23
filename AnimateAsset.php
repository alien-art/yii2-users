<?php

namespace alien\users;

use yii\web\AssetBundle;

/**
 * Description of AnimateAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.5
 */
class AnimateAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@alien/users/assets/rbac';
    /**
     * @inheritdoc
     */
    public $css = [
        'animate.css',
    ];

}

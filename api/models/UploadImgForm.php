<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 16/04/2016
 * Time: 08:07
 */

namespace api\models;


use yii\base\Model;

class UploadImgForm extends Model
{
    public $image_file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // image file
            [['image_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }
}
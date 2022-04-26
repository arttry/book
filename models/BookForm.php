<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class BookForm extends Model
{
    public $book;
    public $search;
    public $title;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['book'], 'string'],
            [['search'], 'string'],
            [['title'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'book' => 'Введите автора книги',
            'search' => 'Поиск',
            'title' => 'Наименование'
        ];
    }

}

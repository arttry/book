<?php

namespace app\controllers;

use app\models\Book;
use app\models\BookAuthor;
use app\models\BookForm;
use app\models\BookGenre;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\db\Query;
use yii\data\ArrayDataProvider;
use app\models\Genre;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new BookForm();

        $query = new Query();
        $searchQuery = $query->select(['b.id', 'b.title', 'b.description', 'group_concat(g.genre, "") as genre', 'ba.author'])
            ->from(['book as b'])
            ->leftJoin('book_author as ba', 'b.id = ba.book_id')
            ->leftJoin('book_genre as bg', 'b.id = bg.book_id')
            ->leftJoin('genre as g', 'bg.genre_id = g.id')
            ->groupBy('b.id')
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchQuery,
        ]);
        $dataProvider->pagination->pageSize = 2000;

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAjaxSearchBook()
    {
        if (Yii::$app->request->post()) {
            $search = Yii::$app->request->post('search');
            $book = Yii::$app->request->post('book');
            if (!empty(Yii::$app->request->post('title'))) {
                $query = new Query();
                $searchQuery = $query->select(['b.id', 'b.title', 'b.description', 'group_concat(g.genre, "") as genre', 'ba.author'])
                    ->from(['book as b'])
                    ->leftJoin('book_author as ba', 'b.id = ba.book_id')
                    ->leftJoin('book_genre as bg', 'b.id = bg.book_id')
                    ->leftJoin('genre as g', 'bg.genre_id = g.id')
                    ->where('b.title = "' . (Yii::$app->request->post('title')) . '"')
                    ->groupBy('b.id')
                    ->all();

            } // поиск по авторам
            else if ($search == 1) {
                $query = new Query();
                $searchQuery = $query->select(['b.id', 'b.title', 'b.description', 'group_concat(g.genre, "") as genre', 'ba.author'])
                    ->from(['book as b'])
                    ->leftJoin('book_author as ba', 'b.id = ba.book_id')
                    ->leftJoin('book_genre as bg', 'b.id = bg.book_id')
                    ->leftJoin('genre as g', 'bg.genre_id = g.id')
                    ->where(
                        'ba.author like "%' . $book . '%"'
                    )
                    ->groupBy('b.id')
                    ->all();
            } else if ($search == 2) {
                $query = new Query();
                $searchQuery = $query->select(['b.id', 'b.title', 'b.description', 'group_concat(g.genre, "") as genre', 'ba.author'])
                    ->from(['book as b'])
                    ->leftJoin('book_author as ba', 'b.id = ba.book_id')
                    ->leftJoin('book_genre as bg', 'b.id = bg.book_id')
                    ->leftJoin('genre as g', 'bg.genre_id = g.id')
                    ->where(
                        'g.genre like "%' . $book . '%"'
                    )
                    ->groupBy('b.id')
                    ->all();
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $searchQuery,
            ]);
            $dataProvider->pagination->pageSize = 2000;
            return $this->renderAjax('_search', [
                'dataProvider' => $dataProvider,
            ]);
        }
    }


    /*
     * сохранение изображений
     */
    private function saveImage($img, $path)
    {
        $curl = curl_init($img);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        $content = curl_exec($curl);
        curl_close($curl);
        if (file_exists($path)) :
            unlink($path);
        endif;
        $fp = fopen($path, 'x');
        fwrite($fp, $content);
        fclose($fp);
    }

    /*
     * парсер
     */
    public function actionCurl()
    {
        Genre::deleteAll();
        BookAuthor::deleteAll();
        Genre::deleteAll();
        Book::deleteAll();
        // меняем ссылку для скачивания другой страницы
        $ch = curl_init('https://www.litmir.me/bs');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
        //echo $html;

        for ($i = 0; $i < 25; $i++) {

            // название
            $pos3 = strpos($html, '<span itemprop="name">');
            if ($pos3 !== false) {
                $title = substr($html, $pos3 + 22, 300);
                $pos4 = strpos($title, '</span>');
                if ($pos4 !== false) {
                    $title2 = substr($title, 0, $pos4);
//                        echo $title2 . '<br>';
                }
                $html = substr_replace($html, '', $pos3, 500);
            }

            // описаниие
            $pos5 = strpos($html, '<div itemprop="description" class="description"><div class="BBHtmlCode"><div class="BBHtmlCodeInner">');
            if ($pos5 !== false) {
                $desc = substr($html, $pos5 + 104, 3000);
                $pos6 = strpos($desc, '</div></div></div></div>');
                if ($pos6 !== false) {
                    $desc2 = substr($desc, 0, $pos6);
//                        echo $desc2 . '<br>';
                }
                $html = substr_replace($html, '', $pos5, 3500);
                $book = new Book();
                $book->title = $title2;
                $book->description = $desc2;
                $book->save();
            }

            // вытаскиваем изображения
            $pos = strpos($html, '/data/Book/0/');
            if($i == 24){
                echo 24 . $pos . '<br>';
                var_dump($pos);
            }
            if ($pos !== false) {
                $image = substr($html, $pos, 150);
                $pos2 = strpos($image, '.jpg');
                if ($pos2 !== false) {
                    $image2 = substr($image, 0, $pos2 + 4);
                    echo 'https://www.litmir.me' . $image2 . '<br>';
                    // получаем auto_increment для сохранения image
//                    $connection = Yii::$app->getDb();
//                    $sql = 'SELECT auto_increment FROM information_schema.tables WHERE table_name="book"';
//                    $command = $connection->createCommand($sql);
//                    $search = $command->queryAll();
//                    $search[0]['AUTO_INCREMENT']
                    $this->saveImage('https://www.litmir.me' . $image2, 'image/' . $book->id . '.jpg');
                    $html = substr_replace($html, '', $pos, 150);
                }
            }

            // жанры
            $pos7 = strpos($html, '<span itemprop="genre" class="desc2">');
            if ($pos7 !== false) {
                $genre = substr($html, $pos7 + 37, 1000);
                $pos8 = strpos($genre, '</span>');
                if ($pos8 !== false) {
                    $genre2 = substr($genre, 0, $pos8);
                    $genre2 = strip_tags($genre2);
                    $genre2 = str_replace('...', '', $genre2);
//                        echo ($genre2) . '<br>'; // строка через запятую с жанрами
                    $genreArr = explode(',', $genre2);
                    foreach ($genreArr as $g) {
                        $mGenre = Genre::find()->where(['genre' => $g])->one();
                        if ($mGenre === null) {
                            $mg = new Genre();
                            $mg->genre = $g;
                            $mg->save();
                            $mbg = new BookGenre();
                            $mbg->book_id = $book->id;
                            $mbg->genre_id = $mg->id;
                            $mbg->save();
                        } else {
                            $mbg = new BookGenre();
                            $mbg->book_id = $book->id;
                            $mbg->genre_id = $mGenre->id;
                            $mbg->save();
                        }
                    }
                }
                $html = substr_replace($html, '', $pos7, 1500);
            }

            // авторы
            $pos9 = strpos($html, '<span itemprop="author" itemscope itemtype="http://schema.org/Person" class="desc2">');
            if ($pos9 !== false) {
                $author = substr($html, $pos9 + 84, 300);
                $pos10 = strpos($author, '</span>');
                if ($pos10 !== false) {
                    $author2 = substr($author, 0, $pos10);
                    $author2 = strip_tags($author2);
                    $ba = new BookAuthor();
                    $ba->book_id = $book->id;
                    $ba->author = $author2;
                    $ba->save();
                }
                $html = substr_replace($html, '', $pos9, 500);
            }
        }
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

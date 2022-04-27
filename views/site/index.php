<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\grid\GridView;
/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
//            'template' => "{label}\n{input}\n{error}",
//            'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
//            'inputOptions' => ['class' => 'col-lg-3 form-control'],
//            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
        ],
    ]); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'search')->radioList(['1' => 'По авторам', 2 => 'По жанрам'], ['labelOptions'=>['style'=>'display:inline']]); ?>

    <?= $form->field($model, 'book')->textInput() ?>

    <label id="genre" style="text-decoration: underline">Жанры</label>
    <div id="genre-all" style="display: none;">
        <?php
        $genres = \app\models\Genre::find()->all();
        foreach ($genres as $genre){
            echo Html::checkbox($genre->id, false, ['label' => $genre->genre, 'class' => 'genre']);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ?>
    </div>

    <br/>
    <label id="author" style="text-decoration: underline">Авторы</label>
    <div id="author-all" style="display: none;">
        <?php
        $authors = \app\models\BookAuthor::find()->groupBy('author')->all();
        foreach ($authors as $author){
            echo Html::checkbox($author->id, false, ['label' => $author->author, 'class' => 'author']);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ?>
    </div>

    <div class="form-group">
        <?= Html::Button('Искать', ['class' => 'btn btn-primary', 'id' => 'search-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div id="content">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-condensed table-hover'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => 'Изображения',
                'value' => function($data) {
                    return '<img src="/image/'.$data['id'].'.jpg">';
                },
                'format' => 'raw',
            ],
            [
                'header' => 'Название',
                'attribute' => 'title',
            ],
            [
                'header' => 'Описание',
                'attribute' => 'description',
                'format' => 'html',
            ],
            [
                'header' => 'Авторы',
                'value' => function($data) {
//                    $rand = rand(1, 100000);
//                    return '<span id="spoiler-'.$rand.'" style="display: none">'.$data['author'].'</span><button spoiler="'.$rand.'" class="btn btn-success spoiler">Показать</button>';
                    return $data['author'];
                },
                'format' => 'raw',
            ],
            [
                'header' => 'Жанры',
                'value' => function($data) {
//                    $rand = rand(1, 100000);
//                    return '<span id="spoiler-'.$rand.'" style="display: none">'.$data['genre'].'</span><button spoiler="'.$rand.'" class="btn btn-success spoiler">Показать</button>';
                    return $data['genre'];
                },
                'format' => 'raw',
            ],
        ],
    ]);
    ?>
</div>

<script>
    var urlSearchBook = "<?php echo Url::to(['site/ajax-search-book']); ?>";
</script>

<?php
$js = Yii::getAlias('@web') . '/script.js';
$this->registerJsFile($js, ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
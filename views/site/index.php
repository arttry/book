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
                    $rand = rand(1, 100000);
                    return '<span id="spoiler-'.$rand.'" style="display: none">'.$data['author'].'</span><button spoiler="'.$rand.'" class="btn btn-success spoiler">Показать</button>';
                },
                'format' => 'raw',
            ],
            [
                'header' => 'Жанры',
                'value' => function($data) {
                    $rand = rand(1, 100000);
                    return '<span id="spoiler-'.$rand.'" style="display: none">'.$data['genre'].'</span><button spoiler="'.$rand.'" class="btn btn-success spoiler">Показать</button>';
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
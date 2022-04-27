<?php
use yii\grid\GridView;
?>

<div class="book-index">
    <h1>Книги</h1>
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

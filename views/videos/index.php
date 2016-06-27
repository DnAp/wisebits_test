<?php
/**
 * @var $this yii\web\View
 * @var $pagination yii\data\Pagination
 * @var $videoRows app\models\Videos[]
 * @var $sort string
 */

use yii\bootstrap\Html;

?>
<h1>videos/index</h1>
<form class="form-inline">
    <div class="form-group">
        <label for="sort">Сначала</label>
        <?= Html::dropDownList('sort', $sort, [
            'date_desc' => 'Новые',
            'date_asc' => 'Старые',
            'views_desc' => 'Популярные',
            'views_asc' => 'Не популярные'
        ], ['class' => 'form-control']) ?>
    </div>
    <div class="form-group">
        <label for="sort">Кол-во</label>
        <?= Html::dropDownList('per-page', $pagination->getPageSize(), [9=>9, 12=>12, 15=>15], ['class' => 'form-control']) ?>
    </div>
    <button type="submit" class="btn btn-default">Изменить настройки</button>
</form>


<div class="row">
    <? foreach ($videoRows as $key => $video): ?>
    <? if ($key % 3 == 0): ?>
</div>

<div class="row">
    <? endif; ?>
    <div class="col-sm-4">
        <h3><?= $video->title ?></h3>
        <p>
            <img src="https://www.gravatar.com/avatar/<?= $video->thumbnail ?>?d=identicon&f=y&s=200" align="left"
                 class="video-thumbnail"/>
            ID: <?= $video->id ?><br/>
            Длительность: <?= Yii::$app->getFormatter()->asTime($video->duration) ?><br/>
            Дата: <?= Yii::$app->getFormatter()->asDate($video->date) ?><br/>
            Просмотров: <?= $video->views ?>
        </p>
    </div>
    <? endforeach; ?>
</div>

<?

echo \yii\widgets\LinkPager::widget([
    'pagination' => $pagination,
    'firstPageLabel' => true,
    'lastPageLabel' => true,
]);
?>
<p>
    Отработало за <?= sprintf('%0.5f', Yii::getLogger()->getElapsedTime()) ?> с.
    Скушано памяти: <?= round(memory_get_peak_usage() / (1024 * 1024), 2) . "MB" ?>
</p>
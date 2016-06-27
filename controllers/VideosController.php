<?php

namespace app\controllers;

use app\models\Videos;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class VideosController extends Controller
{
    public function actionIndex($page = 1)
    {
        $page = (int)$page;
        $pageSize = (int)Yii::$app->request->getQueryParam('per-page', 9);

        $sort = Yii::$app->request->getQueryParam('sort', 'date_desc');
        if (strpos($sort, '_') === false) {
            throw new NotFoundHttpException();
        }
        list($orderBy, $direction) = explode('_', $sort);

        if (!in_array($orderBy, ['views', 'date']) || !in_array($direction, ['asc', 'desc'])) {
            throw new NotFoundHttpException();
        }
        $direction = $direction == 'asc' ? SORT_ASC : SORT_DESC;

        $videoCount = Videos::getCount($orderBy);
        $videoRows = Videos::findByPage($orderBy, $page, $pageSize, $videoCount, $direction);

        $pagination = new Pagination([
            'totalCount' => $videoCount,
            'pageSize' => $pageSize,
        ]);
        return $this->render('index', [
            'pagination' => $pagination,
            'videoRows' => $videoRows,
            'sort' => $sort,
        ]);

    }

}

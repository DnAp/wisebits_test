<?php
/**
 * Created by PhpStorm.
 * User: dnap
 * Date: 25.06.16
 * Time: 22:51
 */

namespace app\commands;

use app\models\Videos;
use Faker\Generator;
use Faker\Provider\ru_RU\Text;
use yii\console\Controller;


class VideosController extends Controller
{
    public function actionMakeFakeData($rowCount = 10000000, $bathSize = 10000)
    {
        // it is slow? try TABLESAMPLE
        $fakerGenerator = new Generator();
        $fakerText = new Text($fakerGenerator);
        $firstDate = strtotime('2000-01-01');

        $rowCount = (int)$rowCount;
        echo "Make data";
        $rows = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $rows[] = [
                'title' => $fakerText->realText(60),
                'thumbnail' => md5(mt_rand()),
                'duration' => mt_rand(10, 10800),
                'views' => mt_rand(0, 1000000),
                'date' => date('Y-m-d H:i:s', mt_rand($firstDate, time())),
            ];
            if (count($rows) == $bathSize) {
                Videos::batchInsert($rows);
                $rows = [];
                echo '.';
            }
        }
        if (count($rows) > 0) {
            Videos::batchInsert($rows);
        }
        echo "\nRefresh index";
        Videos::refreshIndex();
        echo PHP_EOL;
    }

    public function actionAmmoGenerator($countAmmo = 1000)
    {
        $testFile = fopen(__DIR__ . '/../../ammo.txt', 'w');

        $sort = ['views_asc', 'views_desc', 'date_asc', 'date_desc'];
        $countVideo = Videos::getCount('views_asc');

        for ($i = 0; $i < $countAmmo; $i++) {
            $url = sprintf("http://wisebits.dev/videos/%s?sort=%s\n", mt_rand(1, $countVideo), $sort[mt_rand(0, 3)]);
            fwrite($testFile, $url);
        }
        fclose($testFile);

    }
}

<?php

namespace app\models;

use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "videos".
 *
 * @property integer $id
 * @property string $title
 * @property string $thumbnail
 * @property integer $duration
 * @property integer $views
 * @property string $inserted
 */
class Videos extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName($indexTable = false)
    {
        return $indexTable ? 'videos_index_v1' : 'videos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'thumbnail'], 'required'],
            [['duration', 'views'], 'integer'],
            [['date'], 'datetime'], // Why it was safe?
            [['title'], 'string', 'max' => 255],
            [['thumbnail'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'thumbnail' => 'Thumbnail',
            'duration' => 'Duration',
            'views' => 'Views',
            'date' => 'Date',
        ];
    }

    public static function batchInsert($rows)
    {
        if (count($rows) == 0) {
            return true;
        }
        $columns = array_keys(reset($rows));

        $command = self::getDb()->createCommand()
            ->batchInsert(static::tableName(), $columns, $rows);

        return $command->execute();
    }

    private static function orderToColumn($orderBy)
    {
        if (!in_array($orderBy, ['views', 'date'])) {
            throw new ErrorException('Invalid order value');
        }
        return $orderBy . '_number';
    }

    public static function getCount($orderBy)
    {
        $column = self::orderToColumn($orderBy);
        $query = self::find()
            ->from(self::tableName(true))
            ->select(new Expression('MAX(' . $column . ')'));
        return $query->scalar();
    }

    public static function findByPage($orderBy, $page, $pageSize, $countRows, $sort = SORT_ASC)
    {
        $page--;
        if ($sort == SORT_ASC) {
            $startNumber = $pageSize * $page + 1;
            $endNumber = $startNumber + $pageSize - 1;
        } else {
            $endNumber = ($countRows - $pageSize * $page);
            $startNumber = $endNumber - $pageSize + 1;
        }

        $column = self::orderToColumn($orderBy);
        $subQuery = self::find()
            ->select('id')
            ->from(self::tableName(true))
            ->where(['between', $column, $startNumber, $endNumber]);

        $query = self::find()
            ->where(['in', 'id', $subQuery])
            ->orderBy([$orderBy => $sort]);

        return $query->all();
    }

    public static function refreshIndex()
    {
        return self::getDb()->createCommand('REFRESH MATERIALIZED VIEW ' . self::tableName(true))->execute();
    }
}

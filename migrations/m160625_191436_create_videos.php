<?php

use yii\db\Migration;

/**
 * Handles the creation for table `videos`.
 */
class m160625_191436_create_videos extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('videos', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'thumbnail' => $this->string(32)->notNull(),
            'duration' => $this->smallInteger(),
            'views'=>$this->integer(),
            'date' => $this->timestamp()
        ]);

        $indexTableName = 'videos_index_v1';
        $sql = "CREATE MATERIALIZED VIEW videos_index_v1 AS 
            SELECT 
                id, 
                row_number() OVER (ORDER BY views) AS views_number,
                row_number() OVER (ORDER BY date) AS date_number
            FROM videos";

        $this->db->createCommand($sql)->execute();
        $this->createIndex($indexTableName.'_views', $indexTableName, ['views_number']);
        $this->createIndex($indexTableName.'_date', $indexTableName, ['date_number']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('videos');
    }
}

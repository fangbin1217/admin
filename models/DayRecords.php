<?php

namespace app\models;
use Yii;

class DayRecords  extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'day_records';
    }

    static public function getLastWeekRecords($memberId) {
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        $startday = date('Y-m-d', strtotime('-7 day'));
        return DayRecords::find()->select(['record_date', 'amount'])->where(['member_id'=>$memberId])->andWhere(['>=', 'record_date', $startday])->andWhere(['<=', 'record_date', $yestoday])->orderBy(['record_date'=>SORT_ASC])->asArray()->all();
    }
}

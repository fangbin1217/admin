<?php

namespace app\models;
use Yii;

class MonthIncomes  extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'month_incomes';
    }

    static public function getAllRecords($memberId) {
        return MonthIncomes::find()->select(['record_date', 'amount'])->where(['member_id'=>$memberId])->asArray()->all();
    }

    static public function getIncomes() {

    }
}

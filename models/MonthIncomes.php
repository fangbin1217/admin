<?php

namespace app\models;
use Yii;

class MonthIncomes  extends \yii\db\ActiveRecord
{

    static public $stateList = [
        0 => '未结算', 1 => '已结算'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'month_incomes';
    }

    static public function getAllRecords($memberId) {
        return MonthIncomes::find()->select(['record_date', 'money', 'state'])->where(['member_id'=>$memberId])->orderBy(['id'=>SORT_DESC])->asArray()->all();
    }

    static public function getIncomes($memberId) {
        $result = [
            'nostateIncome' => 0.00, 'statedIncome' => 0.00, 'statedList' => []
        ];
        $records = self::getAllRecords($memberId);
        foreach ($records as &$val) {
            if ($val['state'] == 1) {
                $result['statedIncome'] = bcadd($result['statedIncome'], $val['money'], 2);
                $result['statedList'][] = [
                    'date' => $val['record_date'].'-25', 'money' => $val['money']
                ];
            } else {
                $result['nostateIncome'] = bcadd($result['nostateIncome'], $val['money'], 2);
            }
        }
        return $result;
    }

    static public function setLastMonthIncomes() {


    }
}

<?php

namespace app\models;
use Yii;

class DayIncomes  extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'day_incomes';
    }

    static public function getYestodayRecord($memberId) {
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        return DayIncomes::find()->select(['record_date', 'money'])->where(['member_id'=>$memberId, 'record_date'=>$yestoday])->asArray()->one();
    }

    static public function getLastWeekRecords($memberId) {
        $yestoday = date('Y-m-d', strtotime('-1 day'));
        $startday = date('Y-m-d', strtotime('-7 day'));
        return DayRecords::find()->select(['record_date', 'money'])->where(['member_id'=>$memberId])->andWhere(['>=', 'record_date', $startday])->andWhere(['<=', 'record_date', $yestoday])->orderBy(['record_date'=>SORT_ASC])->asArray()->all();
    }

    static public function getAllRecords($memberId) {
        return DayIncomes::find()->select(['record_date', 'money'])->where(['member_id'=>$memberId])->orderBy(['record_date'=>SORT_ASC])->asArray()->all();
    }

    static public function getIncomes($memberId) {
        $result = [
            'yestodayIncome' => 0.00,
            'totalIncome' => 0.00,
            'lastWeekX' => [],
            'lastWeekY' => [],
        ];
        $lastWeekList = [];
        $lastWeekY = [];
        $yestoday = '';
        for ($i=7;$i>0;$i--) {
            $tmpDay = date('Y-m-d', strtotime('-'.$i.' days'));
            $lastWeekList[$tmpDay] = 0.00;
            if ($i == 1) {
                $yestoday = $tmpDay;
            }
        }

        $records = self::getAllRecords($memberId);
        if ($records) {
            foreach ($records as $val) {
                if ($yestoday == $val['record_date']) {
                    $result['yestodayIncome'] = $val['money'];
                }
                if (isset($lastWeekList[$val['record_date']])) {
                    $lastWeekList[$val['record_date']] = $val['money'];
                }
                $result['totalIncome'] = bcadd($result['totalIncome'], $val['money'], 2);
            }
        }

        foreach ($lastWeekList as $kk=>$vv) {
            $result['lastWeekX'][] = $kk;
            $result['lastWeekY'][] = $vv;
        }
        return $result;

    }
}

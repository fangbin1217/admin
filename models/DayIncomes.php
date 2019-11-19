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

    static public function setBannerDayIncomes($incomes, $date = '', $isAdmin = 0) {
        if (!$isAdmin) {
            return false;
        }
        if ($date) {
            $date = date('Y-m-d', strtotime(trim($date)));
        } else {
            $date = date('Y-m-d', strtotime('-1 day'));
        }
        $DayIncomes = BannerDayIncomes::find()->where(['record_date'=>$date])->asArray()->one();
        if ($DayIncomes) {
            $DayIncomes2 = BannerDayIncomes::find()->where(['id'=>$DayIncomes['id']])->one();
            $DayIncomes2->money = $incomes;
            $DayIncomes2->update_time = date('Y-m-d H:i:s');
            return $DayIncomes2->save();
        } else {
            $DayIncomes2 = new BannerDayIncomes();
            $DayIncomes2->money = $incomes;
            $DayIncomes2->record_date = $date;
            $DayIncomes2->update_time = date('Y-m-d H:i:s');
            return $DayIncomes2->save();
        }
    }

    static public function setBannerMonthIncomes($incomes, $date = '', $isAdmin = 0) {
        if (!$isAdmin) {
            return false;
        }
        if ($date) {
            $date = date('Y-m', strtotime(trim($date)));
        } else {
            $date = date('Y-m', strtotime('-1 month'));
        }

        $DayIncomes = BannerMonthIncomes::find()->where(['record_date'=>$date])->asArray()->one();
        if ($DayIncomes) {
            $DayIncomes2 = BannerMonthIncomes::find()->where(['id'=>$DayIncomes['id']])->one();
            $DayIncomes2->money = $incomes;
            $DayIncomes2->update_time = date('Y-m-d H:i:s');
            return $DayIncomes2->save();
        } else {
            $DayIncomes2 = new BannerMonthIncomes();
            $DayIncomes2->money = $incomes;
            $DayIncomes2->record_date = $date;
            $DayIncomes2->update_time = date('Y-m-d H:i:s');
            return $DayIncomes2->save();
        }
    }

    //同步脚本 日
    static public function setMemberDayIncomes($date = '') {
        //（memberId访问量/总访问量） * 总收益

        if ($date) {
            $date = date('Y-m-d', strtotime(trim($date)));
        } else {
            $date = date('Y-m-d', strtotime('-1 day'));
        }

        $bannerMoney = 0.00;
        $BannerDayIncomes = BannerDayIncomes::find()->where(['record_date'=>$date])->asArray()->one();
        if ($BannerDayIncomes) {
            $bannerMoney = $BannerDayIncomes['money'];
        }

        if (bccomp($bannerMoney, 0, 2) < 1) {
            return false;
        }

        $totalAmount = 0;
        $DayRecords = DayRecords::find()->select(['member_id', 'amount'])->where(['record_date'=>$date])->asArray()->all();
        if ($DayRecords) {
            foreach ($DayRecords as $val) {
                $totalAmount += $val['amount'];
            }

            if ($totalAmount) {
                try {
                    $trans = Yii::$app->getDb()->beginTransaction();
                    $now = date('Y-m-d H:i:s');
                    $datas = [];
                    foreach ($DayRecords as $val) {
                        $tmpMoney = bcmul(bcdiv($val['amount'], $totalAmount, 4), $bannerMoney, 2);
                        $datas[] = ['memberId'=>$val['member_id'], 'date'=>$date, 'money'=>$tmpMoney];
                    }
                    foreach ($datas as $vv) {
                        $DayIncomes = DayIncomes::find()->where(['member_id'=>$vv['memberId'], 'record_date'=>$vv['date']])->one();
                        if ($DayIncomes) {
                            $DayIncomes->money = $vv['money'];
                            $DayIncomes->update_time = $now;
                            $DayIncomes->save();
                        } else {
                            $DayIncomes = new DayIncomes();
                            $DayIncomes->member_id = $vv['memberId'];
                            $DayIncomes->record_date = $vv['date'];
                            $DayIncomes->money = $vv['money'];
                            $DayIncomes->update_time = $now;
                            $DayIncomes->save();
                        }
                    }
                    $trans->commit();
                    return true;
                } catch (Exception $E) {
                    $trans->rollBack();
                    return false;
                }
            }
        }
        return false;
    }

    //同步脚本 月
    static public function setMemberMonthIncomes($date = '') {
        //（memberId访问量/总访问量） * 总收益

        if ($date) {
            $date = date('Y-m', strtotime(trim($date)));
        } else {
            $date = date('Y-m', strtotime('-1 month'));
        }

        $bannerMoney = 0.00;
        $BannerMonthIncomes = BannerMonthIncomes::find()->where(['record_date'=>$date])->asArray()->one();
        if ($BannerMonthIncomes) {
            $bannerMoney = $BannerMonthIncomes['money'];
        }

        if (bccomp($bannerMoney, 0, 2) < 1) {
            return false;
        }

        $startDay = date('Y-m-01', strtotime($date));
        $endDay = date('Y-m-01', strtotime("+1 month", strtotime($date)));
        $totalAmount = 0;
        $DayRecords = DayRecords::find()->select(['member_id', 'amount'])->where(['>=', 'record_date', $startDay])->andWhere(['<', 'record_date', $endDay])->asArray()->all();

        if ($DayRecords) {
            foreach ($DayRecords as $val) {
                $totalAmount += $val['amount'];
            }
            if ($totalAmount) {
                try {
                    $trans = Yii::$app->getDb()->beginTransaction();
                    $now = date('Y-m-d H:i:s');
                    $datas = [];
                    foreach ($DayRecords as $val) {
                        $tmpMoney = bcmul(bcdiv($val['amount'], $totalAmount, 4), $bannerMoney, 2);
                        $datas[] = ['memberId'=>$val['member_id'], 'date'=>$date, 'money'=>$tmpMoney];
                    }
                    foreach ($datas as $vv) {
                        $DayIncomes = MonthIncomes::find()->where(['member_id'=>$vv['memberId'], 'record_date'=>$vv['date']])->one();
                        if ($DayIncomes) {
                            $DayIncomes->money = $vv['money'];
                            $DayIncomes->update_time = $now;
                            $DayIncomes->save();
                        } else {
                            $DayIncomes = new MonthIncomes();
                            $DayIncomes->member_id = $vv['memberId'];
                            $DayIncomes->record_date = $vv['date'];
                            $DayIncomes->money = $vv['money'];
                            $DayIncomes->update_time = $now;
                            $DayIncomes->save();
                        }
                    }
                    $trans->commit();
                    return true;
                } catch (Exception $E) {
                    $trans->rollBack();
                    return false;
                }
            }
        }
        return false;
    }


    static public  function setState($monthIncomeId, $isAdmin = 0) {
        if (!$isAdmin || !$monthIncomeId) {
            return false;
        }
        $MonthIncomes = MonthIncomes::find()->where(['id'=>$monthIncomeId])->one();
        if ($MonthIncomes) {
            $MonthIncomes->state = 1;
            $MonthIncomes->update_time = date('Y-m-d H:i:s');
            return $MonthIncomes->save();
        }
        return false;
    }

    static public function getAllNostateRecords($isAdmin = 0) {
        if (!$isAdmin) {
            return [];
        }
        $MonthIncomes = MonthIncomes::find()->select(['id', 'member_id', 'record_date', 'money'])->where(['state'=>0])->orderBy(['id'=>SORT_DESC])->asArray()->all();
        return $MonthIncomes;
    }
}

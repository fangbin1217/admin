<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\DayIncomes;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class EverymonthController extends Controller
{

    /**
     * 每月1号执行一次 （统计上月未结算数据）
     * @param string $date
     * @return int
     */
    public function actionIndex($date = '')
    {

        $MonthIncomes = DayIncomes::setMemberMonthIncomes($date);
        echo $MonthIncomes."success\n";
        return ExitCode::OK;
    }


}

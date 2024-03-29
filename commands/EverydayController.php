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
class EverydayController extends Controller
{
    /**
     * 设置会员每日收益
     * @param string $date
     * @return int
     */
    public function actionIndex($date = '')
    {
        $DayIncomes = DayIncomes::setMemberDayIncomes($date);
        echo $DayIncomes."success\n";
        return ExitCode::OK;
    }


}

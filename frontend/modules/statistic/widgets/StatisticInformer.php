<?php
namespace app\modules\statistic\widgets;

use app\modules\statistic\assets\WidgetAsset;
use common\models\helpers\ConstantsHelper;
use common\models\Playlist;
use common\models\search\BalancestatisticsSearch;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class StatisticInformer extends \yii\base\Widget
{

    public $text = NULL;
    public $user_id = NULL;
    private $offerUrl = NULL;
    private $period = NULL;



    public function init()
    {
        parent::init();

        WidgetAsset::register($this->getView());

        if ($this->offerUrl == NULL) {
            $this->offerUrl = Url::toRoute(["/cart/default/index"]);
        }
        
        if ($this->text === NULL) {
            $this->text = 'Плейлисты';
        }
        if ($this->user_id === NULL) {
            $this->user_id = -1;
        }
        if ($this->period === NULL) {
            $this->period = $this->setPeriodfield(Yii::$app->request->queryParams);
        }

        return true;
    }

    public function run()
    {
        $playlists=Playlist::find()->where(['user_id'=>$this->user_id, 'status'=>Playlist::STATUS_ON])->all();
        $search=new   BalancestatisticsSearch();

        if(!empty($this->period)){ // для смещения по времени
            $search_result= $search->searchCountPeroiod($this->user_id,$this->period);
            $newProfit=$this->getNewProfitPeriod($this->period);
        }else{
            $search_result= $search->searchCount($this->user_id);
            $newProfit=$this->getNewProfit();
        }
        return $this->render('statisticInformer/index',['playlists'=>$playlists,'search_result'=>$search_result,'newProfit'=>$newProfit,'user_id'=>$this->user_id] );
    }
        private function  setPeriodfield($params){
         $listAccessParams=[ ConstantsHelper::STATTICTIC_FILTER_PREIOD_WEEK,
             ConstantsHelper::STATTICTIC_FILTER_PREIOD_3_MONTH,
             ConstantsHelper::STATTICTIC_FILTER_PREIOD_MONTH,
             ConstantsHelper::STATTICTIC_FILTER_PREIOD_YEAR,
             ];
         if(isset($params['stat-period']) and  in_array($params['stat-period'],$listAccessParams)) return $params['stat-period'];
         return false;
    }

    public function getNewProfit()
    {
        $profit=0;
                   $user=User::find()->where(['id'=>$this->user_id])->one();
                   if($user){
                     return  round( $user->newprofit,2)  ;
                   }
                 return $profit;
    }
    public function getNewProfitPeriod($period)
    {
        $profit=0;
        $user=User::find()->where(['id'=>$this->user_id])->one();
        if($user){
            return  round( $user->getNewprofitPeriod($period),2)  ;
        }
        return $profit;
    }
}


<?php
namespace frontend\controllers;





use common\models\overiden\RegistrationForm;
use dektrium\user\Finder;
//use dektrium\user\models\RegistrationForm;
use dektrium\user\models\LoginForm;
use Yii;
use dektrium\user\controllers\SecurityController as  OverriddeneSecurityController;
use dektrium\user\Module;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;


class UsecurityController  extends OverriddeneSecurityController
{


//Я ошибся, Yii::$app->getModule('user')->manager - вот так можно обратиться к модулю user.



    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }


        $this->module=Yii::$app->getModule('user');
        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);
            return $this->goBack();
        }

        return $this->renderAjax('@app/views/account/overriden/security/login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }




}

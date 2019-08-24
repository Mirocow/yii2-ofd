<?php

namespace mirocow\ofd\controllers;

use mirocow\ofd\models\Settings;
use Yii;

class SettingsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new Settings();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }
}
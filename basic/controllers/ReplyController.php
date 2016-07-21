<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\WxNum;
use app\models\WxReply;



class ReplyController extends Controller
{
    public function init(){

        parent::init();
        $session = Yii::$app->session;
        $view = Yii::$app->getView();
        $view->params['name'] =$session->get('name');
        if (!$session->has('name')){
            return $this->redirect(['/login/index']);
        }
    }

    public $enableCsrfValidation = false;
    public $layout='left';
    public function actionNews(){
//        $connection = Yii::$app->db;
//        $connection->createCommand("select * from ")->queryAll();
        $rows = (new \yii\db\Query())->select(['w_id','w_name' ])->from('wx_num')->all();
        $rows = (new \yii\db\Query())->select(['w_id','w_name' ])->from('wx_num')->all();
        return $this->render('news',['info'=>$rows]);

    }

    public  function  actionHui(){
        $request=Yii::$app->request;
        $get=$request->get();
         unset($get['r']);
//        print_r($get);die;
        str_replace(htmlspecialchars($get['g_reply']),$get['g_reply'],$get);
        $connection = Yii::$app->db;
        $connection->createCommand()->insert('wx_reply',$get)->execute();
        $WxReply=new WxReply();
        $rows=$WxReply->find()->where(['w_id'=>$get['w_id']])->asArray()->all();
        echo json_encode($rows);

    }

    //显示规则
    public  function  actionGuize(){
//        echo 1;die;

        $request=Yii::$app->request;
        $w_id=$request->get('w_id');
        $WxReply=new WxReply();
        $rows=$WxReply->find()->where(['w_id'=>$w_id])->asArray()->all();
        echo json_encode($rows);
    }
    //删除规则
    public  function  actionDel(){
        $request=Yii::$app->request;
        $WxReply=new WxReply();
        $id=$request->get('id');
        $WxReply->findOne($id)->delete();
    }


}

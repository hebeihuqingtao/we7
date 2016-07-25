<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\WxNum;
use app\models\WxReply;
use yii\web\UploadedFile;

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

        $rows = (new \yii\db\Query())->select(['w_id','w_name' ])->from('wx_num')->all();
        return $this->render('news',['info'=>$rows]);

    }

    public  function  actionHui(){
        $session = Yii::$app->session;
        $request=Yii::$app->request;
        $WxReply=new WxReply();
        $g_type=$request->post('g_type');                 //当前的类型
        $w_id=$request->post('w_id');                      //当前对应的微信id
        $WxReply->w_id=$w_id;
        $WxReply->g_rule=$request->post('g_rule');      //当前定义的规则
        $WxReply->g_reply=$request->post('g_reply');   //当前回复的内容
        $WxReply->g_type=$g_type;
//说明是文字
        if($g_type==1){
            $WxReply->save();
        }else if($g_type==2||$g_type==3){
            if($g_type==2){
//说明是图片  最大为一兆   定义一个大小
                if($_FILES['file']['size']>1024*1024){
                    echo 2;              //说明图片太大

                    return false;
                }
            }
            if($g_type==3){
                if($_FILES['file']['size']>1024*1024*2){
                    echo 3;              //说明语音太大
                    return false;
                }
            }
            $upload=new UploadedFile(); //实例化上传类
//            print_r($upload);
//            return false;
            $name=$upload->getInstanceByName('file'); //获取文件原名称
            $name=pathinfo($name);
            $name=uniqid().'.'.$name['extension'];
            $img=$_FILES['file']; //获取上传文件参数
            $upload->tempName=$img['tmp_name']; //设置上传的文件的临时名称
            $img_path='../uploads/'.$name; //设置上传文件的路径名称(这里的数据进行入库)
            // echo $img_path;die;
            $upload->saveAs($img_path); //保存文件
//从session里提取access_token
            $access_token=$session->get('access_token'.$w_id);
            $json =$this->media_id($access_token,$img_path,$g_type);
            $media_id=json_decode($json,true);

            if(array_key_exists('media_id',$media_id)){
                $WxReply->g_reply=$media_id['media_id'];   //当前回复的内容
                $WxReply->g_type=$g_type;
                $WxReply->save();
            }else{
                echo 4;
                return false;
            }
        }
        $rows=$WxReply->find()->where(['w_id'=>$w_id])->asArray()->all();
        echo json_encode($rows);

    }

//获取media_id
    public function media_id($access_token,$img_path,$g_type){
        if($g_type==2){
            $type='image';
        }else{
            $type='voice';
        }
        $url= "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";

        $data=array(
            'file'=>'@'.$img_path
        );
        $ch = curl_init();   //1.初始化
        curl_setopt($ch, CURLOPT_URL, $url); //2.请求地址
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');//3.请求方式
//4.参数如下
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');//模拟浏览器
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));//gzip解压内容
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);//6.执行
//  echo $tmpInfo;die;
        if (curl_errno($ch)) {//7.如果出错
            var_dump(curl_error($ch)) ;
        }
        curl_close($ch);//8.关闭
//return ;
        return $tmpInfo;



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

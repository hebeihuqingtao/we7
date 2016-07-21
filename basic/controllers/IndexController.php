<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\WxNum;
//---------------------主页展示的控制器--------------------------------------------->
class IndexController extends Controller{

//构造函数
    public function init(){

        parent::init();
        $session = Yii::$app->session;
        $view = Yii::$app->getView();
        $view->params['name'] =$session->get('name');
        if (!$session->has('name')){
            return $this->redirect(['/login/index']);
        }
    }
    public  $enableCsrfValidation = false;
//进行首页的展示
    public $layout='left';
    public function actionList(){

        return $this->render('list');

    }
//退出
    public function actionLogout(){
        $session = Yii::$app->session;
        $session->remove('name');
        return $this->redirect(['/login/index']);
    }

//微信的添加页面展示
    public function actionAdd(){

        return $this->render('form_validation');
    }
//接值进行添加入库
    public function actionAddinfo(){
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        //生成url地址
        //生成token
        $num= $request->post();
        $num['w_token']= uniqid();

//            print_r($_SERVER);die;
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $connection->createCommand()->insert('wx_num',$num)->execute();

        $id=$connection->getLastInsertID();
        $wxnum=WxNum::findOne($id);
       $wxnum->w_url=$protocol.$_SERVER['HTTP_HOST'].str_replace('index','weixin',$_SERVER['SCRIPT_NAME']).'?&id='.base64_encode($id);
//        print_r($_SERVER);
//        die;
        $ree=$wxnum->save();

        if($ree == 1){
            $this->redirect(['/index/numlist']);
        }else{
            echo "<script>alert('添加失败');location.href='index.php?r=index/add'</script>";
        }

    }

//列表展示

    public function actionNumlist(){
        $query = WxNum::find()->orderBy('w_id')->all();
        return $this->render('numlist', [
            'countries' => $query,
        ]);

    }
//删除
    public function actionDel_w(){

        $request = Yii::$app->request;
        $id= $request->get('w_id');       //接受要删除的id
        if(WxNum::deleteAll(['w_id'=>$id])){
            echo 1;
        }else{
            echo 2;
        }
    }
//微信号修改前的查询
    public function actionGetone(){
        $request = Yii::$app->request;
        $id= $request->get('w_id');       //接受要删除的id
        $info=WxNum::findOne(['w_id'=>$id])->oldAttributes;
        return $this->render('update',
            ['info'=>$info]
        );
    }
//真正的接值修改入库
    public function actionUpdateok(){
        //  echo 1;die;
        $request = Yii::$app->request;

        $w_id = $request->post('w_id');
        $wx_num = WxNum::findOne($w_id);
        $wx_num->w_name=$request->post('w_name');
        $wx_num->w_appid=$request->post('w_appid');

        $wx_num->save();
        return $this->redirect(['/index/numlist']);
    }
//自定义菜单的展示
    public function actionMyself(){
        $rows = (new \yii\db\Query())->select(['w_id','w_name' ])->from('wx_num')->all();

        return $this->render('myself',['info'=>$rows]);
    }
//验证appid和serveid
    public function actionCheck(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $w_id=$request->get('w_id');
        $rows = (new \yii\db\Query())->select(['w_appid','w_serveid' ])->from('wx_num')->where(['w_id' =>$w_id])
            ->one();
        if (!$session->has('access_token'.$w_id)){
            $obj= file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$rows[w_appid]&secret=$rows[w_serveid]");
            $js=json_decode($obj,true);
            if(array_key_exists('access_token',$js)){
                $session->set('access_token'.$w_id,$js['access_token']);
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 1;
        }
    }
//开始自定义
    public function actionZdy(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $who=$request->post("who");
        $Access_token=$session->get('access_token'.$who);
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$Access_token;
        $zuo=$request->post("zuo");
        $zhong=$request->post("zhong");
        $you=$request->post("you");
        $data=' {
     "button":[
     {
          "type":"click",
          "name":"'.$zuo.'",
          "key":"V1001_TODAY_MUSIC"
      },
      {
          "type":"click",
          "name":"'.$zhong.'",
          "key":"V1001_TODAY_MUSIC"
      },

            {
               "type":"click",
               "name":"'.$you.'",
               "key":"V1001_GOOD"
            }]

 }';
        $a= $this->weixinPost($url,$data,"POST");

        $array=json_decode($a,true);
        //   print_r($js);
        if($array['errcode']==0){
            $this->redirect(['/index/myself']);
        }else{
            echo "<script>alert('失败');location.href='index.php?r=index/myself'</script>";
        }


    }

    private function weixinPost($url,$data,$method){
        $ch = curl_init();	 //1.初始化curl
        curl_setopt($ch, CURLOPT_URL, $url); //2.请求地址
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);//3.请求方式
        //4.参数如下
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

        if($method=="POST"){//5.post方式的时候添加数据
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);//6.执行

        if (curl_errno($ch)) {//7.如果出错
            return curl_error($ch);
        }
        curl_close($ch);//8.关闭
        return $tmpInfo;
    }








}
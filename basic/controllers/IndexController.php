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
//            print_r($js);die;
            https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID
            if(array_key_exists('access_token',$js)){
              $session->set('access_token'.$w_id,$js['access_token']);

                echo 1;
            }else{
                echo 0;
            }
        }else{
//            echo $session->get('access_token'.$w_id);
            echo 1;
        }
    }
//开始自定义
    public function actionZdy(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $who=$request->post("who");
        $session->set('w_id',$who);
        $Access_token=$session->get('access_token'.$who);
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$Access_token;
            //  $dat = $request->post();
            //分别接收各个值
            //左
            $arr_left_name  =  $request->post('left');       //左name值
            $arr_left_count = count($arr_left_name);       //左name值长度
            $arr_left_type  =  $request->post('leftgenre');  //左type值
            $arr_left_key   =  $request->post('leftmold');   //左key值或者url值
            $arr_left   = array(); //左
            $arr_centre = array(); //中
            $arr_right  = array(); //右
            $pid = 0;
            if($arr_left_count==1){
                foreach($arr_left_name as $k=>$v){
                    $arr_left[$k]['type'] = $arr_left_type[$k];
                    $arr_left[$k]['name'] = $v;
                    $arr_left[$k]['pid']  = $pid;
                    if($arr_left_type[$k]=='click'){
                        $arr_left[$k]['key'] = $arr_left_key[$k];
                    }else{
                        $arr_left[$k]['url'] = $arr_left_key[$k];
                    }
                }
            }else{
                $kk = 0;
                $i  = 0;
                foreach($arr_left_name as $k=>$v){
                    $kk = $k-1;
                    if($kk<0){
                        $arr_left[$k]['name'] = $v;
                        $arr_left[$k]['pid']  = $i;
                        $i +=1;
                        $session->set('left', $i);
                    }else{
                        $pid = $session->get('left');
                        $i +=1;
                        $arr_left[$k]['type'] = $arr_left_type[$kk];
                        $arr_left[$k]['name'] = $v;
                        $arr_left[$k]['pid']  = $pid;
                        if($arr_left_type[$kk]=='click'){
                            $arr_left[$k]['key'] = $arr_left_key[$kk];
                        }else{
                            $arr_left[$k]['url'] = $arr_left_key[$kk];
                        }
                    }
                }
            }
            //中
            $arr_centre_name  =  $request->post('centre');       //中name值
            $arr_centre_count = count($arr_centre_name);         //中name值长度
            $arr_centre_type  =  $request->post('centregenre');  //中type值
            $arr_centre_key   =  $request->post('centremold');   //中key值或者url值

            if($arr_centre_count==1){
                foreach($arr_centre_name as $k=>$v){
                    $arr_centre[$k]['type'] = $arr_centre_type[$k];
                    $arr_centre[$k]['name'] = $v;
                    $arr_centre[$k]['pid']  = $pid;
                    if($arr_centre_type[$k]=='click'){
                        $arr_centre[$k]['key'] = $arr_centre_key[$k];
                    }else{
                        $arr_centre[$k]['url'] = $arr_centre_key[$k];
                    }
                }
            }else{
                $kk = 0;
                $i  = 0;
                foreach($arr_centre_name as $k=>$v){
                    $kk = $k-1;
                    if($kk<0){
                        $arr_centre[$k]['name'] = $v;
                        $arr_centre[$k]['pid']  = $i;
                        $i +=1;
                        $session->set('centre', $i);
                    }else{
                        $pid = $session->get('centre');
                        $i +=1;
                        $arr_centre[$k]['type'] = $arr_centre_type[$kk];
                        $arr_centre[$k]['name'] = $v;
                        $arr_centre[$k]['pid']  = $pid;
                        if($arr_centre_type[$kk]=='click'){
                            $arr_centre[$k]['key'] = $arr_centre_key[$kk];
                        }else{
                            $arr_centre[$k]['url'] = $arr_centre_key[$kk];
                        }
                    }
                }
            }
            //右
            $arr_right_name  =  $request->post('right');       //右name值
            $arr_right_count = count($arr_right_name);         //右name值长度
            $arr_right_type  =  $request->post('rightgenre');  //右type值
            $arr_right_key   =  $request->post('rightmold');   //右key值或者url值
            // print_r($dat);die;
            if($arr_right_count==1){
                foreach($arr_right_name as $k=>$v){
                    $arr_right[$k]['type'] = $arr_right_type[$k];

                    $arr_right[$k]['name'] = $v;
                    $arr_right[$k]['pid']  = $pid;
                    if($arr_right_type[$k]=='click'){
                        $arr_right[$k]['key'] = $arr_right_key[$k];
                    }else{
                        $arr_right[$k]['url'] = $arr_right_key[$k];
                    }
                }
            }else{
                $kk = 0;
                $i  = 0;
                foreach($arr_right_name as $k=>$v){
                    $kk = $k-1;
                    if($kk<0){
                        $arr_right[$k]['name'] = $v;
                        $arr_right[$k]['pid']  = $i;
                        $i +=1;

                    }else{
                        $pid = $session->get('right');
                        $i +=1;
                        $arr_right[$k]['type'] = $arr_right_type[$kk];
                        $arr_right[$k]['name'] = $v;
                        $arr_right[$k]['pid']  = $pid;
                        if($arr_right_type[$kk]=='click'){
                            $arr_right[$k]['key'] = $arr_right_key[$kk];
                        }else{
                            $arr_right[$k]['url'] = $arr_right_key[$kk];
                        }
                    }
                }
            }
            $arr_11 = array();//指明数组(去子父级pid)
            $arr_2 = array();//指明数组
            //左边
            if($arr_left){
                $arr_left_k = array();
                //  $arr_left_parent = array();
                foreach ($arr_left as $k=>$v){
                    $arr_left_k[$k+1] = $v;
                }
                //  print_r($arr_left_k);die;
                $ii = 0;
                $ll = 0;
                foreach($arr_left_k as $k=>$v){
                    $arr_left_1 = array();
                    if($v['pid']==0){
                        $ii += 1;
                        $arr_left_1[$ii-1] = $v;
                        unset($arr_left_1[$ii-1]['pid']);
                        $session->set('left_name', $v['name']);
                    }elseif($v['pid']!=0 ){
                        foreach($arr_left_k as $kk=>$vv){
                            if($v['pid']==$kk) {
                                $left_name =  $session->get('left_name');
                                $arr_left_1[$ii - 1]['name'] = $left_name;
                                $arr_left_1[$ii - 1]['sub_button'][] = $v;
                            }
                        }
                    }
                }
                //   $arr_1[] = $arr_1;
                //  print_r($arr_left_1);die;
                //去除子父级关系pid
                foreach($arr_left_1 as $k=>$v){
                    if(count($v)==2){
                        foreach($v['sub_button'] as $kk=>$vv){
                            //   print_r($vv);//输出查看
                            unset($vv['pid']);
                            $v['sub_button'][$kk] = $vv;
                        }
                        $arr_11[]=$v;
                        $arr_2[] = $arr_11;
                    }else{
                        $arr_2[] = $arr_left_1;
                    }
                }


            }
            //  print_r($arr_1);die;//输出查看
            //中间
            if($arr_centre){
                $arr_centre_k = array();
                $arr_centre_parent = array();
                foreach ($arr_centre as $k=>$v){
                    $arr_centre_k[$k+1] = $v;
                }
                $ii = 0;
                $ll = 0;
                foreach($arr_centre_k as $k=>$v){
                    $arr_centre_1 = array();
                    if($v['pid']==0){
                        $ii += 1;
                        $arr_centre_1[$ii-1] = $v;
                        unset($arr_centre_1[$ii-1]['pid']);
                        $session->set('centre_name', $v['name']);
                    }elseif($v['pid']!=0 ){
                        foreach($arr_centre_k as $kk=>$vv){
                            if($v['pid']==$kk) {
                                $centre_name =  $session->get('centre_name');
                                $arr_centre_1[$ii - 1]['name'] = $centre_name;
                                $arr_centre_1[$ii - 1]['sub_button'][] = $v;
                            }
                        }
                    }
                }
                //去除子父级关系pid(中间)
                foreach($arr_centre_1 as $k=>$v){
                    if(count($v)==2){
                        foreach($v['sub_button'] as $kk=>$vv){
                            //   print_r($vv);//输出查看
                            unset($vv['pid']);
                            $v['sub_button'][$kk] = $vv;
                        }
                        $arr_11[]=$v;
                        $arr_2[] = $arr_11;
                    }else{
                        $arr_2[] = $arr_centre_1;
                    }
                }
            }
            //右面
            if($arr_right){
                $arr_right_k = array();
                $arr_right_parent = array();
                foreach ($arr_right as $k=>$v){
                    $arr_right_k[$k+1] = $v;
                }
                $ii = 0;
                $ll = 0;
                foreach($arr_right_k as $k=>$v){
                    $arr_right_1 = array();
                    if($v['pid']==0){
                        $ii += 1;
                        $arr_right_1[$ii-1] = $v;
                        unset($arr_right_1[$ii-1]['pid']);
                        $session->set('right_name', $v['name']);
                    }elseif($v['pid']!=0 ){
                        foreach($arr_right_k as $kk=>$vv){
                            if($v['pid']==$kk) {
                                $right_name =  $session->get('right_name');
                                $arr_right_1[$ii - 1]['name'] = $right_name;
                                $arr_right_1[$ii - 1]['sub_button'][] = $v;
                            }
                        }
                    }
                }
                //去除子父级关系pid
                foreach($arr_right_1 as $k=>$v){
                    if(count($v)==2){
                        foreach($v['sub_button'] as $kk=>$vv){
                            //   print_r($vv);//输出查看
                            unset($vv['pid']);
                            $v['sub_button'][$kk] = $vv;
                        }
                    }

                    $arr_11 = $v;
                }
                $arr_2[] = $arr_right_1;
            }
            //数组转换想要的四维数组
            $arr_3 = array();
            $arr_4 = array();
            foreach ($arr_2 as $k=>$v){
                foreach($v as $kk=>$vv){
                }
                $arr_3[$k] = $vv;
            }
            //去除未设定值得菜单栏
            foreach($arr_3 as $k=>$v){
                if(array_key_exists('sub_button',$v)){
                    foreach($v['sub_button'] as $kk=>$vv){
                        if(array_key_exists('type',$vv) && $vv['type']=='0'){
                            unset($arr_3[$k]);
                        }
                    }
                }
            }

            //   print_r($arr_3);//输出查看
            $arr_4['button'] = $arr_3;
        $data = json_encode($arr_4,JSON_UNESCAPED_UNICODE);
//        echo json_decode($data);
//            echo $data;//输出查看
//            die;


        $a= $this->weixinPost($url,$data,"POST");

//        print_r($a);die;
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
<?php
/**
  * wechat php test
  */

//define your token
$id=base64_decode($_GET['id']);


include 'pdo.php';

$data=$pdo->query("select w_token from wx_num where w_id=$id")->fetch(PDO::FETCH_ASSOC);
$token=$data['w_token'];
//print_r($data);die;

define("TOKEN", $token);
define("w_id", $id);
$wechatObj = new wechatCallbackapiTest();
    $wechatObj->valid($pdo);

class wechatCallbackapiTest
{
	public function valid($pdo)
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature($pdo)){
        	echo $echoStr;
            $this->responseMsg($pdo);//开启自动回复
        	exit;
        }
    }

    public function responseMsg($pdo)
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();

                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            $g_reply = $pdo->query("SELECT g_reply FROM wx_reply WHERE g_rule='".$keyword."' AND w_id=".w_id)->fetch();//从数据库中查询回复

            if(!empty( $keyword ))
                {
                    if(!empty($g_reply)){
                        $contentStr = $g_reply['g_reply'];
                    }else{
                        //机器人回复
                        $url = "http://www.tuling123.com/openapi/api?key=a0e8ba0ef95e48a1f49ebdd5eabb31bb&info=".$keyword;
                        $jsoninfo = file_get_contents($url);
                        $setmsg = json_decode($jsoninfo,true);
                        $contentStr = $setmsg['text'];
                    }
                    $msgType = "text";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
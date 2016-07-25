<?php

/**
 * wechat php test
 */
include('./pdo.php');
$id=base64_decode($_GET['id']);
$a=$pdo->query("select w_token from wx_num where w_id = $id")->fetch();
//print_r($a);die;
$token=$a['w_token'];
//echo $token;die;
//define your token
define("TOKEN", $token);
define("w_id", $id);
$wechatObj = new wechatCallbackapiTest();


$echoStr = $_GET["echostr"];
if($echoStr){
    $wechatObj->valid($pdo);
}else{
    $wechatObj->responseMsg($pdo);
}

class wechatCallbackapiTest
{
    public function valid($pdo)
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature($pdo)){
            echo $echoStr;
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
            $imgTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                            </Image>
                        </xml>";

            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
						</xml>";
            $voiceTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Voice>
                            <MediaId><![CDATA[%s]]></MediaId>
                            </Voice>
                          </xml>";
            $g_reply = $pdo->query("SELECT g_reply,g_type FROM wx_reply WHERE g_rule='".$keyword."' AND w_id=".w_id)->fetch();//从数据库中查询回复
            if(!empty($keyword)){
                if (!empty($g_reply)){
                    //回复的是文字
                    if($g_reply['g_type']==1){
                        $contentStr = $g_reply['g_reply'];
                        $msgType = "text";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }else if($g_reply['g_type']==2){
//回复图片需要一个mi
                        $msgType="image";
                        $mediaId = $g_reply['g_reply'];
                        $resultStr = sprintf($imgTpl, $fromUsername, $toUsername, $time, $msgType, $mediaId);
                        echo $resultStr;
                    }else if($g_reply['g_type']==3){
                        //回复语音
                        $msgType="voice";
                        $mediaId = $g_reply['g_reply'];
                        $resultStr = sprintf($voiceTpl, $fromUsername, $toUsername, $time, $msgType, $mediaId);
                        echo $resultStr;
                    }

                }else{
                    //机器人回复
                    $url = "http://www.tuling123.com/openapi/api?key=f94557e9fee1f8bf78d93a365e599947&info=" . $keyword;
                    $jsoninfo = file_get_contents($url);
                    $setmsg = json_decode($jsoninfo, true);
                    $contentStr = $setmsg['text'];
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }

//            }else{
//
//                }
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
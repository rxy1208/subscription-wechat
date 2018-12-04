<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin_gz32");
$wechatObj = new wechatCallbackapiTest();
// $wechatObj->valid();
// $wechatObj->responseMsg();

class wechatCallbackapiTest
{
    //使用构造方法,判断是否为第一次连接
    public function __construct(){
        if(isset($_GET["echostr"])){
            //是首次连接
            $this->valid();
        }else{
            $this->responseMsg();
        }
    }

	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    //记录报文日志文件
    /**
     *      @param    $content    string      表示记录报文的内容
     *      @param    $type       int         表示记录的的内容类型;   0表示接收的报文;1表示回复的报文
     */
    private function record($content, $type=0){

        $str = '';
        if($type == 0){
            $str .= '接收:'.PHP_EOL;//end of line
        }elseif ($type==1) {
            $str .= '回复:' .PHP_EOL;
        }

        $str .= $content . PHP_EOL;
        $str .= '==============================='.date('Y-m-d H:i:s') . PHP_EOL;

        //将内容写进日志文件中
        file_put_contents('./record.log' , $str ,FILE_APPEND);//追加写入
    }


    #被动回复音乐消息
    private function responseMusic($obj, $music='http://phfh5k.natappfree.cc/m1.mp3', $mediaId='nmj5cjtFQxhmQfe8iCfvYP8prkdEpVK4VI0W--yckFEDiyuQQLM64epf8Y1vNB_6'){ 
        
        $xml = '<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                            <Title><![CDATA[需要人陪]]></Title>
                            <Description><![CDATA[小宏宏唱的歌]]></Description>
                            <MusicUrl><![CDATA[%s]]></MusicUrl>
                            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                            <ThumbMediaId><![CDATA[]]></ThumbMediaId>
                        </Music>
                    </xml>';
        $time = time();
        //替换 ToUserName  FromUserName  CreateTime     MusicUrl      HQMusicUrl
        return sprintf($xml, $obj->FromUserName, $obj->ToUserName, $time,$music,$music,$mediaId);
    }

    #被动回复文本消息
    private function responseText($obj, $content='你是大猪哦!'){ 
        
        $xml = '<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>';
        $time = time();
        //                   替换          ToUserName               FromUserName  CreateTime     Content
        return sprintf($xml, $obj->FromUserName, $obj->ToUserName, $time,         $content);
        }

    #被动回复图文消息
    private function responseImgText($obj){ 
        
        $xml = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                        <item>
                            <Title><![CDATA[你是猪吗]]></Title>
                            <Description><![CDATA[一只大笨猪啊!]]></Description>
                            <PicUrl><![CDATA[http://imgm.photophoto.cn/023/086/017/0860170032.jpg]]></PicUrl>
                            <Url><![CDATA[http://47.106.67.244/1.html]]></Url>
                        </item>
                    </Articles>
                </xml>';
        $time = time();
        //替换占位符   sprintf(内容,占位1,占位2,...)
        return sprintf($xml, $obj->FromUserName, $obj->ToUserName, $time);
    }


    public function responseMsg()
    {
		//get post data, May be due to the different environments
        //php7以上已经弃用
		// $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input"); 

        //记录接收的报文
        $this->record($postStr);

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;//发送者的OpenID
                $toUsername = $postObj->ToUserName;//开发者的微信号
                $keyword = trim($postObj->Content);//内容
                $time = time();

                //新增会话记录
                // $dsn = 'mysql:dbname=test;host=127.0.0.1;port=3306';
                // $user = 'root';
                // $password = 'root';
                // $pdo = new PDO($dsn,$user,$password);
                // $sql = "insert into ";
                // echo "<pre />";
                // $res = $pdo->exec($sql);

                #根据用户不同内容回复相应内容
                switch ($postObj ->MsgType) {
                    case 'text'://接收到文本信息
                        if(strstr($keyword,'音乐-')){
                            $resultMsg = $this->responseMusic($postObj);
                        }else{
                            $resultMsg = $this->responseText($postObj);
                        }
                        break;
                    
                    case 'image'://接收到图片信息
                        $resultMsg = $this->responseImgText($postObj);
                        break;
                }

                $this->record($resultMsg,1);//记录回复报文
                echo $resultMsg;//发回复报文给用户
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
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
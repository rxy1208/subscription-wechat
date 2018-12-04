<?php
include './curl.php';//引入curl函数

class Wechat{
	
	const appID = 'wx8eab75f80d200404';//开发者ID
	const appsecret = '0b503f31a33e730b18448d22ea122519';//开发者密码appsecret

	//获取access_token
	public function getToken(){

		//定义存储access_token的文件
		$filePath = __DIR__.'/'.self::appID.'.txt';

		//如果距离上次access_token没有超过2小时,说明accesstoken还在有效期内
		if( is_file($filePath) && filetime($filePath)+7200>time()){
			return file_get_contents($filePath);
		}
		//如果文件不存在或者access_token过期
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		//占位符替换
		$url = sprintf($url,self::appID,self::appsecret);
		//发送get请求微信api
		$json = curl($url);
		//接收微信api返回的参数
		$data = json_decode($json);
		//输出微信api的access_token
		return $data->access_token;
	}

	//创建个性化菜单
	public function getMeun(){
		//获取token
		$token = this->getToken();

		//请求微信api
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token='.$token;

		$json ={
			"button":[
				{    
					"type":"click",
					"name":"今日歌曲",
					"key":"V1001_TODAY_MUSIC" 
				},
				{
					"name":"菜单",
					"sub_button":[
						{            
						"type":"view",
						"name":"搜索",
						"url":"http://www.soso.com/"
						},
						{
						"type":"miniprogram",
						"name":"wxa",
						"url":"http://mp.weixin.qq.com",
						"appid":"wx286b93c14bbf93aa",
						"pagepath":"pages/lunar/index"
						},
						{
						"type":"click",
						"name":"赞一下我们",
						"key":"V1001_GOOD"
						}
					]
				}
			],
			"matchrule":{
				"tag_id":"2",
				"sex":"1",
				"country":"中国",
				"province":"广东",
				"city":"广州",
				"client_platform_type":"2",
				"language":"zh_CN"
			}
			}
	}
}
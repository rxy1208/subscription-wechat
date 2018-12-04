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
}
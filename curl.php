<?php

	function curl($url,$postData=[],$file=''){
		//如果$file不为空则需要上传文件
		if($file !==''){
			$postData['pic'] = new CURLFile($file);
		}
		//使用curl模拟get请求
		#1. 初始化curl请求
		$ch = curl_init();

		#2. 设置curl选项
		// 设置请求URL地址
		curl_setopt($ch,CURLOPT_URL,$url);
		// 不获取header头信息
		curl_setopt($ch,CURLOPT_HEADER,0);
		// 结果不直接返回到终端
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		// 设置curl不进行证书的检测
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);

		// 超时时间 秒
		curl_setopt($ch,CURLOPT_TIMEOUT,10);
		// 设置请求的浏览器
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)Chrome/55.0.2883.87 Safari/537.36');

		// 发起POST请求
		curl_setopt($ch,CURLOPT_POST,1);
		// post发送的数据，注意http_build_query可以将$data数组数据格式化成http传输数据的格式
		curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);

		#3. 执行curl连接
		$data = curl_exec($ch);

		//获取curl连接的相关参数
		$info = curl_getinfo($ch);

		#4. 关闭curl连接
		curl_close($ch);

		//如果状态码为200,则成功请求
		if($info['http_code']==200){
			return $data;
		}
		return false;
	}

	
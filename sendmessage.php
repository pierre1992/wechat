<?php
define("TOKEN", "weixin");//你微信定义的token
define("APPID", "wx69fed71fdd195779");//你微信定义的appid
define("APPSECRET","9d797775be926212bb5c10d6f066e2e0");//你微信公众号的appsecret
error_reporting(E_ALL ^ E_DEPRECATED);
$mysql_server_name='rm-bp1w8d0x64h1g6dd0.mysql.rds.aliyuncs.com'; //改成自己的mysql数据库服务器
$mysql_username='kfd'; //改成自己的mysql数据库用户名
$mysql_password='hGs92bdeuH6s'; //改成自己的mysql数据库密码
$mysql_database='kfd-db'; //改成自己的mysql数据库名

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ; //连接数据库
mysql_query("set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.
mysql_select_db($mysql_database); //打开数据库
$sql ="select openid from kfd_account where type != '医生' and type != '普通会员' "; //SQL语句
$result = mysql_query($sql,$conn); //查询
while($rs = mysql_fetch_assoc($result)){
            $data[] = $rs;
}
date_default_timezone_set('Asia/Shanghai'); 
$time = date('Y年m月d日 H:i',time());
$content = "服用提醒——".$time;
$touser = "oIFrTs66i6xXYaAdsfqpTRnPDTaY";



function https_post($url,$data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
       return 'Errno'.curl_error($curl);
    }
    curl_close($curl);
    return $result;
}

function _reply_customer($touser,$content){
    
    //更换成自己的APPID和APPSECRET
    $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
    
    $json=https_post($TOKEN_URL,$data = null);
    $result=json_decode($json);
    
    $ACC_TOKEN=$result->access_token;
	$data = '{
    "touser":"'.$touser.'",
    "msgtype":"news",
    "news":{
        "articles": [
         {
             "title":"'.$content.'",
             "description":"点击查看",
             "url":"http://kfd-wx.luckyxp.cn/#/reminder",
             "picurl":"http://kfd-wx.luckyxp.cn/static/remind.jpg"
         }
         ]
    }
}';
    
    
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;
    
    $result = https_post($url,$data);
    $final = json_decode($result);
    return $final;
}

  
$now = time();

foreach($data as $value){
	_reply_customer($value['openid'],$content);
	$sql = "insert into kfd_tips (openid,remindtime) values ('".$value['openid']."','$now')";
	mysql_query($sql);
}
	
mysql_close(); //关闭MySQL连接




	
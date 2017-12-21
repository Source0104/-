<?php
/**
 * 验证码领红包模块微站定义
 *
 * @author 赵龙
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
define('MB_ROOT', IA_ROOT . '/addons/yzm_hb');

class Yzm_hbModuleSite extends WeModuleSite {
	public function doMobileYzm() {
	//这个操作被定义用来呈现 功能封面
		global $_W, $_GPC;
   		include $this->template('index');	

	}	
	public function doMobileYanzheng() {
	//这个操作被定义用来呈现 功能封面
		global $_W, $_GPC;
		$number=$_GPC['number'];	
		$uniacid=$_GPC['i'];
		$chong = pdo_fetch("SELECT * FROM " . tablename("yzm") ." where number='".$number."' and uniacid='".$uniacid."' ");
		
		if($chong==''){
			message('该号码不存在，请重新输入',$this->createMobileUrl('Yzm','', true),'error');
		}else{
			if($chong['flag']==1){
				message('该号码奖金已被领取，请重新输入',$this->createMobileUrl('Yzm','', true),'error');

			}else{
      	/* $config=pdo_fetch("select * from".tablename('yzm_setting')."where uniacid='".$_W['uniacid']."'");
 		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';						//API地址
        $pars = array();
        $pars['nonce_str'] = random(32);															//随机字符串
        $pars['mch_billno'] = $config['mchid'] . date('Ymd') . sprintf('%010d', $record['id']);		//商户订单号
        $pars['mch_id'] = $config['mchid'];														    //商户号
        $pars['wxappid'] = $config['appid'];														//公众账号appid
        $pars['nick_name'] = '华氏生物';					//红包发送者名称
        $pars['send_name'] = '华氏生物';					//红包发送者名称
        $pars['re_openid'] = $_W['openid'];				//收红包的openid
        $pars['total_amount'] = $chong['money']*100;					//付款金额，分为单位
        $pars['total_num'] = 1;
        $pars['wishing'] = '恭喜您已领取红包';			//红包祝福语
        $pars['client_ip'] = $config['ip'];				//IP地址
        $pars['act_name'] = '验证码领红包';			//活动名称
        $pars['remark'] = '恭喜您已领取红包';			//备注
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key={$api['password']}";
        $pars['sign'] = strtoupper(md5($string1));			//签名
        $xml = arrayToXml($pars);
        $extras = array();
        $extras['CURLOPT_CAINFO'] = MB_ROOT . '/cert/rootca.pem.' . $uniacid;
        $extras['CURLOPT_SSLCERT'] = MB_ROOT . '/cert/apiclient_cert.pem.' . $uniacid;
        $extras['CURLOPT_SSLKEY'] = MB_ROOT . '/cert/apiclient_key.pem.' . $uniacid;
		
        $resp = $this->curl_post_ssl($url,$xml,  $extras);
       
        //$pars['logo_imgurl'] = tomedia($gift['tag']['image']);
        $pars['share_content'] = $gift['tag']['content'];
        $pars['share_imgurl'] = tomedia($gift['tag']['image']);
		
        $pars['share_url'] = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('entry', array('owner' => $user['uid'], 'actid' => $activity['actid'])), 2);
		 */
		 
		           $config=pdo_fetch("select * from".tablename('yzm_setting')."where uniacid='".$_W['uniacid']."'");
                $money = $chong['money']*100; //最低1元，单位分
                $sender = "华氏生物";
                $obj2 = array();
                $obj2['wxappid'] = $config['appid']; //appid
                $obj2['mch_id']=$config['mchid'];
                // $obj2['mch_id'] = $config['mchid'];　　//商户id
              $obj2['mch_billno'] = $config['mchid'].date('YmdHis').rand(1000,9999);
                $obj2['client_ip'] = "121.41.27.32";
                $obj2['re_openid'] = $_W['openid'];
                $obj2['total_amount'] = $money;
                $obj2['min_value'] = $money;
                $obj2['max_value'] = $money;
                $obj2['total_num'] = 1;
                $obj2['nick_name'] = $sender;
                $obj2['send_name'] = $sender;
                $obj2['wishing'] = "恭喜发财";
                $obj2['act_name'] = $sender."红包";
                $obj2['remark'] = $sender."红包";
                $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
                $res=$this->pay($url, $obj2);
			 $data=array(
					'flag'=>'1',
					);
				pdo_update('yzm',$data,array('id'=>$chong['id']));
				message('您已成功领取，请前往公众号查看',$this->createMobileUrl('Yzm','', true),'success'); 
			}
		}
		
	}
	
	 function pay($url,$obj) {
    $obj['nonce_str'] = $this->create_noncestr();  //创建随机字符串
    $stringA = $this->create_qianming($obj,false);  //创建签名
    $stringSignTemp = $stringA."&key=K6X0joFqFzu2j25qq5U6X5XxxQx6ojjY";  //签名后加api
    $sign = strtoupper(md5($stringSignTemp));  //签名加密并大写
    $obj['sign'] = $sign;  //将签名传入数组
 
    $postXml = $this->arrayToXml($obj);  //将参数转为xml格式
    $responseXml = $this->curl_post_ssl($url,$postXml);  //提交请求
    return $responseXml;
  }
    function arrayToXml($arr) {
    $xml = "<xml>";
    foreach ($arr as $key=>$val) {
      if (is_numeric($val)) {
        $xml.="<".$key.">".$val."</".$key.">";
      } else {
        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
      }
    }
    $xml.="</xml>";
    return $xml;
  }
   function create_noncestr($length=32) {
    //创建随机字符
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for($i=0;$i<$length;$i++) {
      $str.=substr($chars, mt_rand(0,strlen($chars)-1),1);
    }
    return $str;
  }
function create_qianming($arr,$urlencode) {
    $buff = "";
    ksort($arr); //对传进来的数组参数里面的内容按照字母顺序排序，a在前面，z在最后（字典序）
    foreach ($arr as $k=>$v) {
      if(null!=$v && "null" != $v && "sign" != $k) {  //签名不要转码
        if ($urlencode) {
          $v = urlencode($v);
        }
        $buff.=$k."=".$v."&";
      }
    }
    if (strlen($buff)>0) {
      $reqPar = substr($buff,0,strlen($buff)-1); //去掉末尾符号“&”
    }
    return $reqPar;
  }
  function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
  {
    $ch = curl_init();
    //超时时间
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    //cert 与 key 分别属于两个.pem文件
    //请确保您的libcurl版本是否支持双向认证，版本高于7.20.1
	echo dirname(__FILE__).''.DIRECTORY_SEPARATOR.'cert/apiclient_cert.pem.128';
    curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__).''.DIRECTORY_SEPARATOR.'cert/apiclient_cert.pem.128');
    curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__).''.DIRECTORY_SEPARATOR.'cert/apiclient_key.pem.128');
    curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).''.DIRECTORY_SEPARATOR.'cert/rootca.pem.128');
    if( count($aHeader) >= 1 ){
      curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    $data = curl_exec($ch);
    if($data){
      curl_close($ch);
      return $data;
    }
    else {
      $error = curl_errno($ch);
      echo "call faild, errorCode:$error\n";
      curl_close($ch);
      return false;
    }
  }
/*  public function arrayToXml($arr) {
    $xml = "<xml>";
    foreach ($arr as $key=>$val) {
      if (is_numeric($val)) {
        $xml.="<".$key.">".$val."</".$key.">";
      } else {
        $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
      }
    }
    $xml.="</xml>";
    return $xml;
  }
 function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
  {
    //超时时间
     $ch = curl_init();
   curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    //cert 与 key 分别属于两个.pem文件
    //请确保您的libcurl版本是否支持双向认证，版本高于7.20.1
    curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__).DIRECTORY_SEPARATOR.
        'zhengshu'.DIRECTORY_SEPARATOR.'apiclient_cert.pem');
    curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__).DIRECTORY_SEPARATOR.
        'zhengshu'.DIRECTORY_SEPARATOR.'apiclient_key.pem');
    curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).DIRECTORY_SEPARATOR.
        'zhengshu'.DIRECTORY_SEPARATOR.'rootca.pem');
    if( count($aHeader) >= 1 ){
      curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    $data = curl_exec($ch);
    if($data){
      curl_close($ch);
      return $data;
    }
    else {
      $error = curl_errno($ch);
      echo "call faild, errorCode:$error\n";
      curl_close($ch);
      return false;
    }
  } */

	public function doWebyzm_setting() {
        global $_W, $_GPC;
        if (checksubmit()) {
        	
            load()->func('file');
            mkdirs(MB_ROOT . '/cert');
            $r = true;
            if (!empty($_GPC['cert'])) {
                $ret = file_put_contents(MB_ROOT . '/cert/apiclient_cert.pem.' . $_W['uniacid'], trim($_GPC['cert']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['key'])) {
                $ret = file_put_contents(MB_ROOT . '/cert/apiclient_key.pem.' . $_W['uniacid'], trim($_GPC['key']));
                $r = $r && $ret;
            }
            if (!empty($_GPC['ca'])) {
                $ret = file_put_contents(MB_ROOT . '/cert/rootca.pem.' . $_W['uniacid'], trim($_GPC['ca']));
                $r = $r && $ret;
            }
            if (!$r) {
                message('证书保存失败, 请保证 /addons/yzm_hb/cert/ 目录可写');
            }
            $input = array_elements(array('appid', 'appsecret', 'mchid', 'password', 'ip'), $_GPC);
            $input['appid'] = trim($input['appid']);
            $input['appsecret'] = trim($input['appsecret']);
            $input['mchid'] = trim($input['mchid']);
            $input['password'] = trim($input['password']);
            $input['ip'] = trim($input['ip']);
            $input['uniacid']=$_W['uniacid'];
        	$a=pdo_fetch("select * from".tablename('yzm_setting')."where uniacid='".$_W['uniacid']."'");
        	if($a){
        		  $a=pdo_update('yzm_setting',$input,array('uniacid'=>$_W['uniacid']));
        	}else{
        	      $a=pdo_insert('yzm_setting',$input);
        	}
        	if ($a) {
                message('保存参数成功', $this->createWebUrl('yzm_setting','', true),'success');
            }else{
    	        message('保存参数失败', $this->createWebUrl('yzm_setting','', true),'error');
            }
        }
        $config=pdo_fetch("select * from".tablename('yzm_setting')."where uniacid='".$_W['uniacid']."'");
        if (empty($config['ip'])) {
            $config['ip'] = $_SERVER['SERVER_ADDR'];
        }
        include $this->template('yzm_setting');
    }
	public function doWebShengcheng() {
		global $_W, $_GPC;
		   $wheresql="";
		   if($_GPC['linqu']!=''){
		   	  $wheresql .=" AND flag ='".$_GPC['linqu']."'";
		   }
		   if($_GPC['money']!=''){
		   	  $wheresql .=" AND money ='".$_GPC['money']."'";
		   }if($_GPC['star']!=''){
		   	  $wheresql .="AND time >='".strtotime($_GPC['star'])."'";
		   }if($_GPC['stop']!=''){
		   	  $wheresql .="AND time <='".strtotime($_GPC['stop'])."'";
		   }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
		$chong = pdo_fetchall("SELECT * FROM " . tablename("yzm") ." where 1 $wheresql and uniacid='".$_GPC['__uniacid']."' ORDER BY flag , id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename("yzm") ." where 1 $wheresql and uniacid='".$_GPC['__uniacid']."' ORDER BY flag");
		$pager = pagination($total, $pindex, $psize);
   		    if ($_GPC['out_put'] == 'output') {
            $sql = "SELECT * FROM " . tablename("yzm") ." where 1 $wheresql and uniacid='".$_GPC['__uniacid']."' ORDER BY flag,id desc";
                $list = pdo_fetchall($sql, $paras);
                $i = 0;
                foreach ($list as $key => $value) {
                    $arr[$i]['number'] = $value['number'];
                    $arr[$i]['money'] = $value['money'];
                    $arr[$i]['time'] = date('Y-m-d',$value['time']);
                    $i++;
                }
                $this->exportexcel($arr, array('验证码', '红包金额' , '生成时间'), time());
                exit();
            }
   		include $this->template('shengcheng');	
	}
	    protected function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }
    }
	public function doWebyzm_scheng(){
		global $_W, $_GPC;
		include $this->template('yzm_scheng');	
	}
	//生成验证码
	public function doWebyzm_yzm(){
		global $_W, $_GPC;
		$number=$_GPC['number'];
		$money=$_GPC['money'];
		 $chars_array = array( 
	    "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
	    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", 
	    "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", 
	    "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", 
	    "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", 
	    "S", "T", "U", "V", "W", "X", "Y", "Z", 
 	 	); 
		$charsLen = count($chars_array) - 1; 
		for($i=0;$i<$number;$i++){
			 $outputstr = ""; 
			  for ($x=0; $x<6; $x++) 
			  { 
			    $outputstr .= $chars_array[mt_rand(0, $charsLen)]; 
			  }
$chong = pdo_fetch("SELECT number FROM " . tablename("yzm") ." where  uniacid='".$_GPC['__uniacid']."' and number= '".$outputstr."' ");
				if($chong==''){
					$data=array(
						'number'=>$outputstr,
						'uniacid'=>$_GPC['__uniacid'],
						'money'=>$money,
						'time'=>time(),
					);
                    pdo_insert('yzm',$data);
				}else{
					$i--;
				}
		}
        message('生成完毕，请查看',$this->createWebUrl('Shengcheng','', true),'success');
	}
}
<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class AuthController extends Controller
{
    public $enableCsrfValidation = false;    
    
    /*
    [
        'r' => 'auth',
        'msg_signature' => 'df39c7b1f7faa629b95bc0a6ca346002e3ce8a65',
        'timestamp' => '1492585060',
        'nonce' => '938321678',
        'echostr' => 'pxf6EB5S6EUI4AGyJ+u4WvvQQwDV4yx7KSTmpiwYU3okE5Rm4p/h9PLtSyOZWwOVTWGr7oUJvZFpupeIws5rJg==',
    ],

    <xml><ToUserName><![CDATA[tj4a1744a4a878638f]]></ToUserName>
    <Encrypt><![CDATA[lY7+o/dRNf+ZxSq5S27Vujur9E9pf8C0leeNJ6A2VdZSuUXeRzFeaKw55Nj37K7W1SBHzAHpCFGUfeENmMqb06SBjL8RyBIVLbjFU/+YzHjEgQSCj5Hl2coIQwIo7Y3dAyuzHhxvqLcmqfhxqrWNRfvtjOALxjUMigrJ55hfCHUvkgksh6O229OdiApLks/fX0WBB1nlIxtUyvuq9Bw0XlO51gO/pY7lw58CQXXGkFl6yYfzkauAGuhsgQDyjwN/yTvIbPBBA68aWeRg4DFY5667ZVlrCP1DOr0JDy0gX0rGh4kA/RNrUBx0NnlocKIL9z5JLcnF6KgtyDnnnF17TrNvroY/MiEnMapGEjsqv17hW7G7fgbDHq4BmgL1hQJV]]></Encrypt>
    <AgentID><![CDATA[]]></AgentID>
    </xml>  
    */

    // 'token' => 'JnSfFvpT', 'encodingaeskey' => 'yCI1zBU7mt717hsAhjTCLAHyBFWTSxrut6diEJrGDBl',
    // http://wxe-admin.buy027.com/index.php?r=auth&msg_signature=07f52619ab9263c28d9cd3d06d08dd3403cfc502&timestamp=1492593989&nonce=1115140030&echostr=FdjUVzAJx2xzCg783ZPTpwBxxeDPNbRxGS3F5JCNFJf7kgL99U2%2FT6%2Fiie5TNWNzEX%2FXd%2FOQTO0JnPAGoidoCA%3D%3D
    // http://wxe-admin.buy027.com/index.php?r=auth&msg_signature=9919802f1ccdfb6db39ed94e17d14d36bdf47daf&timestamp=1492657418&nonce=1255866065
    // http://127.0.0.1/wxe/backend/web/index.php?r=auth&msg_signature=15b8fda4ed73960ba24dfb5ac2a1f3f8393600a6&timestamp=1492671419&nonce=850142663&echostr=1dWk%2BX164z8hF9XumK2AfWZXZxs21uhZh44PFo44jLATqidO03fBov6TDiXqs4coOwL7Wqm4WdwaMZVThvpf6g%3D%3D
    public function actionIndex()
    {
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);                
        //yii::error([$_GET['echostr'], urldecode($_GET['echostr'])]);

        $options = [
            'token' => 'JnSfFvpT',
            'encodingaeskey' => 'yCI1zBU7mt717hsAhjTCLAHyBFWTSxrut6diEJrGDBl',
            'appid' => 'tj8c2445c93840db09',    // suitid
            'appsecret' => '7lZbxTFWPeoMuLrxuXuA-9bGsOuKmS_6_A87VrKaEFc2divG7Uu8dh6O9BZey67T',  // suit secret
            'agentid'=> 0,
            'debug' => true,
            'logcallback'=>'yii::error',
        ];
        
        $this->checkEcho();
        return 'success';
    }    

    public function checkEcho()
    {
        if (isset($_GET["echostr"])) {
            $msg_signature = $_GET['msg_signature'];
            $timestamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];
            $echostr = $_GET['echostr'];
            
            $wxcpt = new WXBizMsgCrypt('JnSfFvpT', 'yCI1zBU7mt717hsAhjTCLAHyBFWTSxrut6diEJrGDBl', 'wx0b4f26d460868a25');
            $errCode = $wxcpt->VerifyURL($msg_signature, $timestamp, $nonce, $echostr, $sEchoStr);
            if ($errCode == 0) {
                 yii::error('ok=' . $sEchoStr);
                 die($sEchoStr);
                 //return $sEchoStr;
            } else {
                 yii::error('verify error, ' . $errCode);
            }
        }
    }
    
    public function actionCallback()
    {
        /*
        [
            'r' => 'auth/callback',
            'corpid' => 'wx0b4f26d460868a25',
            'msg_signature' => 'cba9794f8f87a7abb9df1f19f94d6da0afdacdce',
            'timestamp' => '1492662188',
            'nonce' => '1417942269',
            'echostr' => 'HEFucIQmdSR7RtbIaFi6RhEAClGcCSNaCPzZORXGwIsSd0EPBd0EJNGX0inA3dddc8bmHWeTZm7dUThw/G6ReA==',
        ],
        */
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);           
        $this->checkEcho();
        
        return 'success';
    }

    //http://wxe-admin.buy027.com/index.php?r=auth/business&auth_code=1602990382020935aa4fee78aebe1d29
    public function actionBusiness()
    {
        yii::error([$_GET, $_POST, file_get_contents("php://input")]);                
        return 'success';        
    }
    
}

class WXBizMsgCrypt
{
	private $m_sToken;
	private $m_sEncodingAesKey;
	private $m_sCorpid;

	/**
	 * 构造函数
	 * @param $token string 公众平台上，开发者设置的token
	 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
	 * @param $Corpid string 公众平台的Corpid
	 */
//	public function WXBizMsgCrypt($token, $encodingAesKey, $Corpid)
       public function __construct($token, $encodingAesKey, $Corpid)
	{
        	yii::error($encodingAesKey);
		$this->m_sToken = $token;
		$this->m_sEncodingAesKey = $encodingAesKey;
		$this->m_sCorpid = $Corpid;
	}
	
    /*
	*验证URL
    *@param sMsgSignature: 签名串，对应URL参数的msg_signature
    *@param sTimeStamp: 时间戳，对应URL参数的timestamp
    *@param sNonce: 随机串，对应URL参数的nonce
    *@param sEchoStr: 随机串，对应URL参数的echostr
    *@param sReplyEchoStr: 解密之后的echostr，当return返回0时有效
    *@return：成功0，失败返回对应的错误码
	*/
	public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr)
	{
		if (strlen($this->m_sEncodingAesKey) != 43) {
    		        yii::error('mylen=' . strlen($this->m_sEncodingAesKey));
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($this->m_sEncodingAesKey);
		//verify msg_signature
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		$signature = $array[1];
		if ($signature != $sMsgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$result = $pc->decrypt($sEchoStr, $this->m_sCorpid);
		if ($result[0] != 0) {
			return $result[0];
		}
		$sReplyEchoStr = $result[1];

		return ErrorCode::$OK;
	}
	/**
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
	 *                      当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function EncryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
	{
		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//加密
		$array = $pc->encrypt($sReplyMsg, $this->m_sCorpid);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}

		if ($sTimeStamp == null) {
			$sTimeStamp = time();
		}
		$encrypt = $array[1];

		//生成安全签名
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}
		$signature = $array[1];

		//生成发送的xml
		$xmlparse = new XMLParse;
		$sEncryptMsg = $xmlparse->generate($encrypt, $signature, $sTimeStamp, $sNonce);
		return ErrorCode::$OK;
	}


	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $msgSignature string 签名串，对应URL参数的msg_signature
	 * @param $timestamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $postData string 密文，对应POST请求的数据
	 * @param &$msg string 解密后的原文，当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function DecryptMsg($sMsgSignature, $sTimeStamp = null, $sNonce, $sPostData, &$sMsg)
	{
		if (strlen($this->m_sEncodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//提取密文
		$xmlparse = new XMLParse;
		$array = $xmlparse->extract($sPostData);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		if ($sTimeStamp == null) {
			$sTimeStamp = time();
		}

		$encrypt = $array[1];
		$touser_name = $array[2];

		//验证安全签名
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		$signature = $array[1];
		if ($signature != $sMsgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$result = $pc->decrypt($encrypt, $this->m_sCorpid);
		if ($result[0] != 0) {
			return $result[0];
		}
		$sMsg = $result[1];

		return ErrorCode::$OK;
	}

}

class XMLParse
{

	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取出的加密消息字符串
	 */
	public function extract($xmltext)
	{
		try {
			$xml = new DOMDocument();
			$xml->loadXML($xmltext);
			$array_e = $xml->getElementsByTagName('Encrypt');
			$array_a = $xml->getElementsByTagName('ToUserName');
			$encrypt = $array_e->item(0)->nodeValue;
			$tousername = $array_a->item(0)->nodeValue;
			return array(0, $encrypt, $tousername);
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ParseXmlError, null, null);
		}
	}

	/**
	 * 生成xml消息
	 * @param string $encrypt 加密后的消息密文
	 * @param string $signature 安全签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 */
	public function generate($encrypt, $signature, $timestamp, $nonce)
	{
		$format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
		return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
	}

}

class SHA1
{
	/**
	 * 用SHA1算法生成安全签名
	 * @param string $token 票据
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 * @param string $encrypt 密文消息
	 */
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
	{
		//排序
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
			return array(ErrorCode::$OK, sha1($str));
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ComputeSignatureError, null);
		}
	}

}


class ErrorCode
{
	public static $OK = 0;
	public static $ValidateSignatureError = -40001;
	public static $ParseXmlError = -40002;
	public static $ComputeSignatureError = -40003;
	public static $IllegalAesKey = -40004;
	public static $ValidateCorpidError = -40005;
	public static $EncryptAESError = -40006;
	public static $DecryptAESError = -40007;
	public static $IllegalBuffer = -40008;
	public static $EncodeBase64Error = -40009;
	public static $DecodeBase64Error = -40010;
	public static $GenReturnXmlError = -40011;
}

class PKCS7Encoder
{
	public static $block_size = 32;

	/**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	function encode($text)
	{
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen($text);
		//计算需要填充的位数
		$amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		//获得补位所用的字符
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	/**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	function decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > PKCS7Encoder::$block_size) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}

}

/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
	public $key;

	function __construct($k)
	{
		$this->key = base64_decode($k . "=");
	}

	/**
	 * 对明文进行加密
	 * @param string $text 需要加密的明文
	 * @return string 加密后的密文
	 */
	public function encrypt($text, $corpid)
	{

		try {
			//获得16位随机字符串，填充到明文之前
			$random = $this->getRandomStr();
			$text = $random . pack("N", strlen($text)) . $text . $corpid;
			// 网络字节序
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			//使用自定义的填充方式对明文进行补位填充
			$pkc_encoder = new PKCS7Encoder;
			$text = $pkc_encoder->encode($text);
			mcrypt_generic_init($module, $this->key, $iv);
			//加密
			$encrypted = mcrypt_generic($module, $text);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);

			//print(base64_encode($encrypted));
			//使用BASE64对加密后的字符串进行编码
			return array(ErrorCode::$OK, base64_encode($encrypted));
		} catch (Exception $e) {
			print $e;
			return array(ErrorCode::$EncryptAESError, null);
		}
	}

	/**
	 * 对密文进行解密
	 * @param string $encrypted 需要解密的密文
	 * @return string 解密得到的明文
	 */
	public function decrypt($encrypted, $corpid)
	{

		try {
			//使用BASE64对需要解密的字符串进行解码
			$ciphertext_dec = base64_decode($encrypted);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			mcrypt_generic_init($module, $this->key, $iv);

			//解密
			$decrypted = mdecrypt_generic($module, $ciphertext_dec);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
			return array(ErrorCode::$DecryptAESError, null);
		}


		try {
			//去除补位字符
			$pkc_encoder = new PKCS7Encoder;
			$result = $pkc_encoder->decode($decrypted);
			//去除16位随机字符串,网络字节序和AppId
			if (strlen($result) < 16)
				return "";
			$content = substr($result, 16, strlen($result));
			$len_list = unpack("N", substr($content, 0, 4));
			$xml_len = $len_list[1];
			$xml_content = substr($content, 4, $xml_len);
			$from_corpid = substr($content, $xml_len + 4);
		} catch (Exception $e) {
			print $e;
			return array(ErrorCode::$IllegalBuffer, null);
		}
		if ($from_corpid != $corpid)
			return array(ErrorCode::$ValidateCorpidError, null);
		return array(0, $xml_content);

	}


	/**
	 * 随机生成16位字符串
	 * @return string 生成的字符串
	 */
	function getRandomStr()
	{

		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}

}


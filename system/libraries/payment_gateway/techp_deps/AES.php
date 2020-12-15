<?php

class AES
{
    const M_CBC = 'cbc';
    const M_CFB = 'cfb';
    const M_ECB = 'ecb';
    const M_NOFB = 'nofb';
    const M_OFB = 'ofb';
    const M_STREAM = 'stream';
    const AES_CIPHER_128 = 'aes-128';
    const AES_CIPHER_192 = 'aes-192';
    const AES_CIPHER_256 = 'aes-256';

    /**
     * Sets all the required data.
     * @param string $data
     * @param string $key
     * @param int $blockSize
     * @param string $mode
     * @param string $iv
     */
    function __construct($data = null, $key = null, $blockSize = null, $mode = null, $iv)
    {
        $this->data = $data;
        $this->key = $key;
        $this->cipher = $blockSize;
        $this->mode = $mode;
        $this->IV = $iv;
    }

    /**
     * @param set all data in variables
     */
    public function __set($field,$value)
    {
        switch ($field) {
            case 'cipher':
                $this->$field = $this->setBlockSize($value);
                break;

            case 'mode':
                $this->$field = $this->setMode($value);
                break;
            
            default:
                $this->$field = $value;
                break;
        }
    }

    /**
     * @param handles error for undefined access to variables
     */
    public function __get($variable)
    {
            if (!isset($this->$variable)) {
               return "Variable not found";
            }
    }

    /**
     * @param int $blockSize
     */
    public function setBlockSize($value)
    {
        switch ($value) {
            case 128 :
                $cipher = AES::AES_CIPHER_128;
                break;
            case 192 :
                $cipher = AES::AES_CIPHER_192;
                break;
            case 256 :
                $cipher = AES::AES_CIPHER_256;
                break;
        }
        return $cipher;
    }

    /**
     * @param string $mode
     */
    public function setMode($value)
    {
        switch ($value) {
            case AES::M_CBC :
                $mode = AES::M_CBC;
                break;
            case AES::M_CFB :
                $mode = AES::M_CFB;
                break;
            case AES::M_ECB :
                $mode = AES::M_ECB;
                break;
            case AES::M_NOFB :
                $mode = AES::M_NOFB;
                break;
            case AES::M_OFB :
                $mode = AES::M_OFB;
                break;
            case AES::M_STREAM :
                $mode = AES::M_STREAM;
                break;
            default :
                $mode = AES::M_ECB;
                break;
        }
        return $mode;
    }

    /**
     * This function checks whether the mandatory values are set and returns true. Returns false otherwise.
     * @return boolean
     */
    public function validateParams()
    {
        if ($this->data != null && $this->key != null && $this->cipher != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function encrypts the data according to the key, mode and iv provided
     * and convert it into base64 encoded format
     * @return string
     * @throws Exception
     */
	public function encrypt()
    {
        if ($this->validateParams()) 
        {           
            $str = $this->data;
            $cipher = $this->cipher."-".$this->mode;

            if (in_array($cipher, openssl_get_cipher_methods()))
            {
                $ivlen = openssl_cipher_iv_length($cipher);
                if (empty($this->IV)) 
                {
                    $this->IV = openssl_random_pseudo_bytes($ivlen);
                } 
                $rt = openssl_encrypt($str, $cipher, $this->key, $options=0, $this->IV);               
            }            
            return $rt;
        } 
        else 
        {
            throw new Exception('Provide valid details to get transaction token');
        }
    }

    /**
     * This function decrypts the data according to the key, mode and iv provided
     * @return string
     * @throws Exception
     */
	public function decrypt()
    {
            //store $cipher, $iv, and $tag for decryption later
                $cipher = $this->cipher."-".$this->mode;                
                $ivlen = openssl_cipher_iv_length($cipher);                
				if (empty($this->IV)) 
                {                
                    $iv = substr($this->data, 0, $ivlen);
                } 
                else 
                {
                    $iv = $this->IV;
                }
                $rt = openssl_decrypt($this->data, $cipher, $this->key, $options=0, $iv);
                return $rt;
    }

    /**
     * This function encrypts the data according to the key, mode and iv provided
     * and convert it into hex encoded format
     * @see http://in2.php.net/manual/en/function.bin2hex.php
     * @return string
     * @throws Exception
     */
	public function encryptHex()
    {       
            $str = $this->data;
            $cipher = $this->cipher."-".$this->mode; 

            if (in_array($cipher, openssl_get_cipher_methods()))
            {
                $ivlen = openssl_cipher_iv_length($cipher);
               // $iv = openssl_random_pseudo_bytes($ivlen);
                if (empty($this->IV)) {
                $iv = openssl_random_pseudo_bytes($ivlen);
                } else {
                    $iv = $this->IV;
                }
                $cyper_text = openssl_encrypt($str, $cipher, $this->key, $options=0, $iv);
                $rt = bin2hex($cyper_text);                
            }

        return $rt;
    }

    /**
     * This function decrypts the data according to the key, mode and iv provided
     * @return string
     * @throws Exception
     */
	public function decryptHex()
    {       
        //store $cipher, $iv, and $tag for decryption later
                $cipher = $this->cipher."-".$this->mode; 
                //$c = base64_decode($this->data);
                $ivlen = openssl_cipher_iv_length($cipher);
                
                if (empty($this->IV)) {
                $iv = substr($c, 0, $ivlen);
                } else {
                    $iv = $this->IV;
                }                
                $hexToBinStr = self::hex2bin($this->data);
                $rt = openssl_decrypt($hexToBinStr, $cipher, $this->key, $options=0, $iv);


        //return $this->unpad($rt);
        return $rt;
    }

    /**
     * This function converts hex value to string
     * @return string
     */
	public static function hex2bin($hexdata) {
        $bindata = '';
        $length = strlen($hexdata);
        for ($i=0; $i < $length; $i += 2)
        {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }

	/**
	 * Sets pad_method
	 */
	public function require_pkcs5()
    {
        $this->pad_method = 'pkcs5';
    }

    /**
     * @param string $str
     * @param string  $ext
     * @return string
     */
    protected function pad_or_unpad($str, $ext)
    {
        if (is_null($this->pad_method))
        {
            return $str;
        }
        else
        {
            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';
			if (is_callable($func_name) && function_exists('mcrypt_get_block_size'))
            {
                $size = @mcrypt_get_block_size($this->cipher, $this->mode);
                return call_user_func($func_name, $str, $size);
            }
        }

        return $str;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function pad($str)
    {
        return $this->pad_or_unpad($str, '');
    }

    /**
     * @param string $str
     * @return string
     */
    protected function unpad($str)
    {
        return $this->pad_or_unpad($str, 'un');
    }

	/**
	 * @param string $text
	 * @param int $blocksize
	 * @return string
	 */
	public static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * @param string $text
     * @return string
     */
    public static function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * provab
 *
 * Travel Portal Application
 *
 * @package		provab
 * @author		Arjun J<arjun.provab@gmail.com>
 * @copyright	Copyright (c) 2013 - 2014
 * @link		http://provab.com
 */

class Provab_Mailer {

	/**
	 * Provab Email Class
	 *
	 * Permits email to be sent using Mail, Sendmail, or SMTP.
	 *
	 * @package		provab
	 * @subpackage	Libraries
	 * @category	Libraries
	 * @author		Arjun J<arjun.provab@gmail.com>
	 * @link		http://provab.com
	 */
	public $CI;                     //instance of codeigniter super object
	public $mailer_status;         //mailer status which indicates if the mail should be sent or not
	public $mail_configuration;    //mail configurations defined by user

	/**
	 * Constructor - Loads configurations and get ci super object reference
	 */
	public function __construct($data='')
	{
		if (valid_array($data) == true and intval($data['id']) > 0) {
			$id = intval($data['id']);
		} else {
			$id = GENERAL_EMAIL;
		}

		$this->CI =& get_instance();
		$return_data = $this->CI->custom_db->single_table_records('email_configuration','*' ,array('origin' => $id));
		$this->mail_configuration = $return_data['data'][0];
	}

	/**
	 *Initialize mailer to send mail
	 *
	 *return array containing the status of initialization
	 */
	public function initialize_mailer()
	{
		$status = false;
		$message = 'Please Contact Admin To Setup Mail Configuration';
		$this->mailer_status = false;

		if (is_array($this->mail_configuration) == true and count($this->mail_configuration) > 0) {
			if (intval($this->mail_configuration['status']) == 1 ) {
				if (isset($this->mail_configuration['username']) == true and empty($this->mail_configuration['username']) == false and
				isset($this->mail_configuration['password']) == true and empty($this->mail_configuration['password']) == false
				) {
					//set mail configurations
					$config['useragent'] = 'PHPMailer';
					$config['smtp_user'] = trim($this->mail_configuration['username']);
					$config['smtp_pass'] = trim($this->mail_configuration['password']);
					$config['smtp_port'] = isset($this->mail_configuration['port']) == true ? $this->mail_configuration['port'] : 465;
					$config['smtp_host'] = isset($this->mail_configuration['host']) == true ? $this->mail_configuration['host'] : 'ssl://smtp.gmail.com';

					$config['wordwrap'] = FALSE;
					$config['mailtype'] = 'html';
					$config['charset'] = 'iso-8859-1';
					$config['crlf'] = "\r\n";
					$config['newline'] = "\r\n";
					$config['protocol'] = 'sendmail';
					//$config['protocol'] = 'smtp';
					$this->CI->load->library('email', $config);
					$from = isset($this->mail_configuration['from']) == true ? $this->mail_configuration['from'] : PROJECT_NAME;
					$this->CI->email->from($this->mail_configuration['username'], $from);
					$this->CI->email->set_newline("\r\n");

					//set cc and bcc
					if (isset($this->mail_configuration['bcc']) == true and empty($this->mail_configuration['bcc']) == false) {
						$this->CI->email->bcc($this->mail_configuration['bcc']);
					}
					if (isset($this->mail_configuration['cc']) == true and empty($this->mail_configuration['cc']) == false) {
						$this->CI->email->cc($this->mail_configuration['cc']);
					}
					$this->mailer_status = true;
					$status = true;
					$message = 'Continue To Send Mail';
				}
			}
		}
		return array('status' => $status, 'message' => $message);
	}

	/**
	 *send mail to the user
	 *
	 *@param string $to_email     email id to which the mail has to be delivered
	 *@param string $mail_subject mail subject which has to be sent in the mail
	 *@param string $mail_message mail message which has to be sent in the mail body
	 *@return boolean status of sending mail
	 *@$attachment for single attachment pass file name , for multiple pass as array of filenames
	 *$cc and $bcc pass as array
	 */
	public function send_mail($to_email, $mail_subject, $mail_message, $attachment='', $cc='', $bcc='')
	{
		$status = false;
		//initializing mailer configurations
		$ini_status = $this->initialize_mailer();
		$message = $ini_status['message'];
		//sending mail based on mailer status
		if ($this->mailer_status == true) {
			if ($to_email != '' && $mail_message != '' && $mail_subject != '') {
				//set mail data
				$this->CI->email->to(trim(strip_tags($to_email)));
				$this->CI->email->subject(trim($mail_subject));
				$this->CI->email->message($mail_message);
				//and attachment
				if (empty($attachment) == false) {
					if(valid_array($attachment)) {
						//for multple attachements
						foreach($attachment as $k => $v) {
							if(empty($v) == false) {
								$this->CI->email->attach($v);
							}
						}
					} else if(strlen($attachment) > 1){
						//for single attachements
						$this->CI->email->attach($attachment);
					}
				}
				//add CC
				if(empty($cc) == false && valid_array($cc)) {
					$ccEmail = '';
					//Validating Email
					foreach($cc as $k => $v) {
						if(filter_var($v, FILTER_VALIDATE_EMAIL) == true) {
							$ccEmail[] = trim($v);							
						}
					}
					if(valid_array($ccEmail)) {
						$this->CI->email->cc($ccEmail);
					}					
				}				
			    //add BCC
				if(empty($bcc) == false && valid_array($bcc)) {
				    $bccEmail = '';
					//Validating Email
					foreach($bcc as $k => $v) {
						if(filter_var($v, FILTER_VALIDATE_EMAIL) == true) {
							$bccEmail[] = trim($v);							
						}
					}
					if(valid_array($bccEmail)) {
						$this->CI->email->bcc($bccEmail);
					}
				}
				$result = $this->CI->email->send();
				if($result) {
					$status = true;
					$message = 'Mail Sent Successfully'; 
				} else {
					$status = false;
					$message = $this->CI->email->print_debugger();
				}
			} else {
				$status = false;
				$message = 'Please Provide To Email Address, Mail Subject And Mail Message';
			}
		}
		return array('status' => $status, 'message' => $message);
	}
	//Pravinkumar
public function sendmail($to_email, $mail_subject, $mail_message)
	{
		//set to_email id to which you want to receive mails
		$from_email = trim($this->mail_configuration['username']);
		$from_name = ucfirst($this->mail_configuration['from']);
				
		//configure email settings
		/*	for localhost
		 * $config['protocol'] = 'smtp';
		 * $config['charset'] = 'iso-8859-1';
		 */
		/*	for server
		 * $config['protocol'] = 'gsmtp';
		 * $config['charset'] = 'utf-8';
		 */
		$config['useragent'] = 'PHPMailer';
		$config['protocol'] = 'smtp'; //'smtp' for localhost
		$config['smtp_host'] = 'ssl://smtp.gmail.com';
		$config['smtp_port'] = '465';
		$config['smtp_user'] = trim($this->mail_configuration['username']);
		$config['smtp_pass'] = trim($this->mail_configuration['password']);
		$config['mailtype'] = 'html';
		$config['charset'] = 'iso-8859-1'; //'iso-8859-1' for local
		$config['wordwrap'] = TRUE;
		$config['newline'] = "\r\n"; //use double quotes
		$GLOBALS['CI']->load->library('email', $config);
		$GLOBALS['CI']->email->initialize($config);
		
		$frommail = $GLOBALS['CI']->email->from($from_email,$from_name);
		$tomail = $GLOBALS['CI']->email->to($to_email);
		$mailcontent = $GLOBALS['CI']->email->message($mail_message);

		//$GLOBALS['CI']->email->cc('');
		$GLOBALS['CI']->email->subject($mail_subject);
		if($GLOBALS['CI']->email->send()){
			$status = true;
			$message = 'Mail Sent Successfully';
		} else {
			$status = false;
			$message = $GLOBALS['CI']->email->print_debugger;
		}
		return array('status' => $status, 'message' => $message);
	}
}
?>

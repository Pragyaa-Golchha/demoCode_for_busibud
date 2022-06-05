<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Register Controller
 */
class CpdRegisterController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Common');
	


	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		
		if($this->Session->check('loginUser')){
			$this->redirect(Router::url('/dashboard/', true));
		}
		//----------------------------//
		// Get Register Page Contents //
		//----------------------------//
			
			$current_page = $this->Common->getContentDetails('register');
			$this->set('current_page',$current_page);
			
			$this->set('title_for_layout',stripslashes($current_page['MasterPage']['meta_title']));
			$this->set('meta_keyword_for_layout',stripslashes($current_page['MasterPage']['meta_keywords']));
			$this->set('meta_desc_for_layout',stripslashes($current_page['MasterPage']['meta_desc']));
			$this->set('meta_canonical_url',stripslashes($current_page['MasterPage']['meta_desc']));
			
		//pr($current_page);
		//---------------------------------//
		// Get Register Page Contents Ends//
		//-------------------------------//
		
		if ($this->request->is('post')) {
								
			$this->loadModel("CpdMember");
			
			$check_email = $this->CpdMember->find('first',array('conditions' => array('CpdMember.email'=>$this->request->data['CpdMember']['email'])));
			if(count($check_email) > 0){
				$this->Session->setFlash(__('<div class="errorpanel" style="margin:0;">Email Id already Registered, Try Another.</div>'));
			}else{
				$this->CpdMember->create();
				$this->request->data['CpdMember']['password'] = md5($this->request->data['CpdMember']['password']);
				$this->request->data['CpdMember']['is_active'] = 0;
				if ($this->CpdMember->save($this->request->data)) {
					$regId = $this->CpdMember->id;
					if($regId > 0){
					$cpdMember = $this->CpdMember->find('first',array('conditions' => array('CpdMember.member_id'=>$regId)));
					//pr($cpdMember);
						$this->loadModel('SiteSetting');
						$hcontact_options = array(
						'conditions' => array('SiteSetting.id' => 1),
						'limit' => 1
						);
						$siteSettings = $this->SiteSetting->find('first',$hcontact_options);
						//echo $siteSettings;
						//echo "oops";exit();
						$table_template= '<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #ccc;"><tbody><tr><td style="padding:15px 10px;background: #DFDFDF;font-family: Arial, Helvetica, sans-serif;font-size: 14px;text-align: center;color: #000; font-weight:bold;">NEW CPD REGISTRATION </td></tr><tr><td style="vertical-align:bottom;border-top: 1px solid #ddd;font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align:left;"> <table cellpadding="0" cellspacing="0"><tbody><tr><th width="60%" style="text-align:left; font-size:13px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; border-right:1px solid #ccc; padding:10px; border-bottom:1px solid #ccc; color:#000;">Company Name</th> <td width="40%" style="text-align:left; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; padding:10px; border-bottom:1px solid #ccc; color:#000;">'.h($cpdMember["CpdMember"]["company_name"]).'</td></tr><tr> <th width="60%" style="text-align:left; font-size:13px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; border-right:1px solid #ccc; padding:10px; border-bottom:1px solid #ccc; color:#000;">Contact Person </th> <td width="40%" style="text-align:left; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; padding:10px; border-bottom:1px solid #ccc; color:#000;">'.h($cpdMember["CpdMember"]["contact_person"]).'</td></tr><tr> <th width="60%" style="text-align:left; font-size:13px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; border-right:1px solid #ccc; padding:10px; border-bottom:1px solid #ccc; color:#000;">Fax <small></small></th> <td width="40%" style="text-align:left; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; padding:10px; border-bottom:1px solid #ccc; color:#000;">'.h($cpdMember["CpdMember"]["fax"]).'</td></tr><tr> <th width="60%" style="text-align:left; font-size:13px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; border-right:1px solid #ccc; padding:10px; border-bottom:1px solid #ccc; color:#000;">Email Id <small></small></th> <td width="40%" style="text-align:left; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; padding:10px; border-bottom:1px solid #ccc; color:#000;">'.h($cpdMember["CpdMember"]["email"]).'</td></tr><tr> <th width="60%" style="text-align:left; font-size:13px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; border-right:1px solid #ccc; padding:10px; border-bottom:1px solid #ccc; color:#000;">Address <small></small></th> <td width="40%" style="text-align:left; font-size:13px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; line-height:18px; vertical-align:top; padding:10px; border-bottom:1px solid #ccc; color:#000;">'.h($cpdMember["CpdMember"]["postal_address"])." ,".h($cpdMember["CpdMember"]["city"])." ,".h($cpdMember["CpdMember"]["country"]).'</td></tr></tbody> </table></td></tr></tbody></table>';
						$sender_email = $siteSettings['SiteSetting']['email'];
						//$hpcna_email='admin@man.com.na';
						$this->loadModel('SiteSetting');
						$contact_options = array(
								'conditions' => array('SiteSetting.id' => 1),
								'limit' => 1
								);
						$var=$this->SiteSetting->find('first',$contact_options);
						$hpcna_email=$var['SiteSetting']['email'];
					    $sender_email = $var['SiteSetting']['from_email'];
						$this->loadModel("AdminEmailAccount");
						$admin_email_list =$this->AdminEmailAccount->find('first',
 array('fields' => array('AdminEmailAccount.id', 'AdminEmailAccount.email_id'),'conditions' => array('id'=>$sender_email,'is_active' => 1))); 
						
						$sender_mail = $admin_email_list['AdminEmailAccount']['email_id'];
						$Email = new CakeEmail();
						$Email->from(array($sender_mail => ' MAN Site'));
						//$Email->to('in.priyaranjan@gmail.com');
						//$Email->to('pragyaagolchha90@gmail.com');
						$Email->to($hpcna_email); 
						$Email->subject('New CPD has been registered to MAN site');
						$Email->emailFormat('both');
						//$Email->sendAs('html');
						$Email->send($table_template);
						
						
						
						
						
						
						/*$this->Session->write('loginUser',$regId);
						$this->redirect(Router::url('/dashboard', true));*/
						$this->Session->setFlash(__('<div class="successpanel" style="margin:0;">Your Application has been submitted. You will be notified on approval of your application.</div>'));
						$this->redirect(Router::url('/cpdregister', true));
					}else{
						$this->Session->setFlash(__('<div class="errorpanel" style="margin:0;">Error in Application submission, try again.</div>'));
					}
				}else{
					$this->Session->setFlash(__('<div class="errorpanel">Error in Application submission, try again.</div>'));
				}
			}
		}
		$this->layout="front_layout";
	}
	
	public function login($email,$pass){
		$this->loadModel("CpdMember");
		
		$password   = md5($pass);
		$checklogin = $this->CpdMember->find('first', array('conditions' => array('email' => $email, 'password' => $password, 'is_active' => 1)));
		if(count($checklogin)>0){
			$this->Session->write('cpdloginUser',$checklogin['CpdMember']['member_id']);
			echo 'PERFECT';
		}else{
			echo 'INVALID';
		}
		exit;
	}
	
	public function forgot_password($email=''){
		$this->loadModel("CpdMember");
		$email = $this->request->data['id'];
		
		$this->loadModel('AdminEmailAccount');
		$this->loadModel('SiteSetting');
		$settings_options = array(
		'conditions' => array('SiteSetting.id' => 1),
		'limit' => 1
		);
		$settings_options = $this->SiteSetting->find('first',$settings_options);
		$from_email_pos = $settings_options['SiteSetting']['from_email'];
		$from_email_arr =  array(
		'conditions' => array('AdminEmailAccount.id' => $from_email_pos),
		'limit' => 1
		);
		$settings_options_email = $this->AdminEmailAccount->find('first',$from_email_arr);
		$from_email = $settings_options_email['AdminEmailAccount']['email_id'];
		
		
		
		
		
		$checkemail = $this->CpdMember->find('first', array('conditions' => array('email' => $email, 'is_active' => 1)));
		if(count($checkemail)>0){
			//-------------
			// Send Mail //
			//------------
			$site_url = 'http://'.$_SERVER['SERVER_NAME'].Router::url('/');
			$reset_link = $site_url."cpdregister/reset_password/".base64_encode($checkemail['CpdMember']['member_id'])."/".base64_encode($checkemail['CpdMember']['email'])."/".rand(4,7);
			$message="Dear ".$checkemail['CpdMember']['contact_person'];
			$message.="<br><br> You can reset your password by clicking on the following link";
			$message.="<br>Reset Link: <a href='".$reset_link."'>".$reset_link."</a>";
			$message.="<br><br>Yours Sincerely,<br>Medical Association of Namibia";
			
			$Email = new CakeEmail();
			$Email->from(array($from_email => 'Medical Association of Namibia'));
			//$Email->to('in.priyaranjan@gmail.com');
			$Email->to($email);
			$Email->subject('Reset Your Password | Medical Association of Namibia');
			$Email->emailFormat('both');
			$Email->send($message);
			
			echo 1;
		}else{
			echo 0;
		}
		exit;
	}
	
	public function reset_password($member_id = null,$encode_email = null)
	{
		$this->loadModel("CpdMember");
		//pr($this->request->params);
		if($member_id != ''){
		$member_id = base64_decode($member_id);
		$email_id = base64_decode($encode_email);
		$current_pass = $this->CpdMember->find('first',array('conditions' => 
			array('CpdMember.member_id' => $member_id)));
		if(count($current_pass) > 0){
			if ($this->request->is(array('post', 'put'))){	
				$new_pass = md5($this->request->data['CpdMember']['new_password']);
				$this->CpdMember->save(array('member_id' => $member_id,'password' => $new_pass));
				$this->Session->setFlash(__('<div class="successpanel" style="margin:0;">Your Password reset Successfully.</div>'));	
				unset($this->request->data);
			    $this->redirect(Router::url('/cpdregister/reset_password/success:true', true));
			}
		}else{
			echo $member_id; echo $email_id;
			$this->Session->setFlash(__('<div class="errorpanel" style="margin:0;">Invalid Link</div>'));
			$this->redirect(Router::url('/register/reset_password/success:false', true));
		}
	 }
	 $this->layout="cpd_reset_password";
		
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
	public function facebook()
	{
		if ($this->session->has_userdata('fbAccessToken')) {
			$accessToken = $this->session->fbAccessToken;
		} else {
			$helper = $this->fb->getRedirectLoginHelper();
			try {
				$accessToken = $helper->getAccessToken();
				$this->session->set_userdata('fbAccessToken', $accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: ' . $e->getMessage();
				exit();
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit();
			}
		}

		if (! isset($accessToken)) {
			if ($helper->getError()) {
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
			} else {
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
			exit();
		}

		$oAuth2Client = $this->fb->getOAuth2Client();

		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		$tokenMetadata->validateAppId($this->config->item('fb_app_id'));
		$tokenMetadata->validateExpiration();

		if (! $accessToken->isLongLived()) {
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
				$this->session->set_userdata('fbAccessToken', $accessToken);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
				exit();
			}
		}

		try {
			$response = $this->fb->get('/me?fields=id,name,email,age_range,gender,locale,picture,timezone', $accessToken->getValue());
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit();
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit();
		}

		$user = $response->getGraphUser();

		$input['fb_id'] = $user['id'];
		$input['name'] = $user['name'];
		if (isset($user['email'])) $input['email'] = $user['email'];
		if (isset($user['age_range']['min'])) $input['age_range']['min'] = $user['age_range']['min'];
		if (isset($user['gender'])) $input['gender'] = $user['gender'];
		if (isset($user['locale'])) $input['locale'] = $user['locale'];
		$input['picture'] = $user['picture']['url'];
		if (isset($user['timezone'])) $input['timezone'] = $user['timezone'];

		if ($this->mongodb->users->count(array('fb_id' => $input['fb_id'])) == 0) {
			$this->mongodb->users->insert($input);
		} else {
			$this->mongodb->users->update(
				array('fb_id' => $input['fb_id']),
				array('$set' => $input),
				array('fsync' => TRUE)
			);
			$save = $this->mongodb->users->findOne(array('fb_id' => $input['fb_id']), array('_id'));
			$input['_id'] = $save['_id'];
		}

		$this->session->set_userdata('loggedIn', json_decode(json_encode($input)));

		redirect($this->session->last_page(1));
	}

	public function registration()
	{
		$data = array();

		if ($this->session->has_userdata('loggedIn')) {
			$user = $this->mongodb->users->findOne(array('_id' => new MongoId($this->session->loggedIn->_id->{'$id'})));

			if (!isset($user['email'])) {
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
			}
			if (!isset($user['password'])) {
				$this->form_validation->set_rules('password', 'Password', 'required');
			}
			if (!isset($user['phone'])) {
				$this->form_validation->set_rules('phone', 'HP', 'required');
			}
		} else {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}

		if ($this->form_validation->run() == FALSE) {
			$data['errors'] = validation_errors();
		} else {
			if (isset($user)) {
				if (!isset($user['email'])) $input['email'] = $this->input->post('email', TRUE);
				if (!isset($user['password'])) $input['password'] = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
				if (!isset($user['phone'])) $input['phone'] = $this->input->post('phone', TRUE);

				$this->mongodb->users->update(array('_id' => new MongoId($user['_id']->{'$id'})), array('$set' => $input));
			}
			
			$data['success'] = 'Selamat! Pendaftaran Anda telah lengkap';
		}

		$layout['contents'] = $this->load->view('auth/registration', $data, TRUE);
		$this->load->view('layout', $layout);
	}

	public function logout()
	{
		$this->session->unset_userdata('loggedIn');
		if ($this->uri->segment(1) == 'admin') {
			redirect();
		} else {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}
	}

	public function login()
	{
		if ($this->session->has_userdata('loggedIn')) {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}

		$post = $this->input->post(NULL, TRUE);
		$data['post'] = $post;

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data['errors'] = validation_errors();
		} else {
			$user = $this->mongodb->users->findOne(array('email' => $post['email']));

			if (password_verify($post['password'], $user['password'])) {
				unset($user['password']);
				$this->session->set_userdata('loggedIn', json_decode(json_encode($user)));
				redirect($this->session->last_page(1));
			}
			$data['errors'] = 'Kata sandi tidak cocok';
		}

		$layout['contents'] = $this->load->view('auth/login', $data, TRUE);
		$this->load->view('layout', $layout);
	}

	public function register()
	{
		if ($this->session->has_userdata('loggedIn')) {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}

		$post = $this->input->post(NULL, TRUE);
		$data['post'] = $post;

		$this->form_validation->set_rules('name', 'Nama', 'required');
		$this->form_validation->set_rules('gender', 'Kelamin', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('phone', 'HP', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data['errors'] = validation_errors();
		} else {
			$input['fb_id'] = '';
			$input['name'] = $post['name'];
			$input['email'] = $post['email'];
			$input['age_range'] = array();
			$input['gender'] = $post['gender'];
			$input['locale'] = 'id_ID';
			$input['picture'] = '';
			$input['timezone'] = 7;
			$input['phone'] = $post['phone'];
			$input['password'] = password_hash($post['password'], PASSWORD_BCRYPT);

			$this->mongodb->users->update(
				array('email' => $post['email']),
				array('$setOnInsert' => $input),
				array('fsync' => TRUE, 'upsert' => TRUE)
			);

			$user = $this->mongodb->users->findOne(array('email' => $post['email']));
			unset($user['password']);
			$this->session->set_userdata('loggedIn', json_decode(json_encode($user)));

			$data['success'] = 'Sukses melakukan pendaftaran.<br><a href="'.site_url($this->session->last_page(1)).'" class="btn btn-primary">kembali ke halaman sebelumnya</a>';
		}
		$layout['title']	= 'Register';
		$layout['contents'] = $this->load->view('auth/register', $data, TRUE);
		$this->load->view('layout', $layout);
	}

	public function email_check($str = '')
	{
		if ($this->mongodb->users->count(array('email' => $str)) > 0)
		{
				$this->form_validation->set_message('email_check', 'Your email has been used by another user.');
				return FALSE;
		}
		else
		{				
				if(cek_email_domain($str)){
					return TRUE;
				}else{
					return FALSE;
				}
		}
	}

	public function email_forget($str = '')
	{
		if ($this->mongodb->users->count(array('email' => $str)) > 0)
		{
				if(cek_email_domain($str)){
					return TRUE;
				}else{
					return FALSE;
				}				
		}
		else
		{				
				$this->form_validation->set_message('email_forget', 'Your email has not been registered.');
				return FALSE;
		}
	}

	public function email_forget_ajax()
	{
		if ($this->mongodb->users->count(array('email' => $this->input->get('email'))) > 0)
		{
				if(cek_email_domain($this->input->get('email'))){
					echo 'true';
				}else{
					echo 'false';
				}				
		}
		else
		{				
				echo 'false';
		}
	}

	public function phone_check(){
		if($this->mongodb->users->count(array('phone' => $this->input->get('phone'))) > 0){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	public function email_check_validate(){
		if($this->mongodb->users->count(array('email' => $this->input->get('email'))) > 0){
			echo 'false';
		}else{
			if(cek_email_domain($this->input->get('email'))){
				echo 'true';	
			}else{
				echo 'false';
			}
			
		}
	}
	public function email_check_subscribe(){
		if($this->mongodb->subscriber->count(array('email' => $this->input->get('emailsubscribe'))) > 0){
			echo 'false';
		}else{
			if(cek_email_domain($this->input->get('emailsubscribe'))){
				echo 'true';	
			}else{
				echo 'false';
			}
		}
	}
	public function just_email_check(){
		if(cek_email_domain($this->input->get('email'))){
			echo 'true';	
		}else{
			echo 'false';
		}
	}
	public function subscribe(){
		print_r($this->input->post('emailsubscribe'));
		$this->mongodb->subscriber->insert(array('email'=>$this->input->post('emailsubscribe')));

		redirect(base_url().'success_subscribe');
	}
	public function forgetpassword(){
		if ($this->session->has_userdata('loggedIn')) {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}

		$post = $this->input->post(NULL, TRUE);
		$data['post'] = $post;

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_forget');

		if ($this->form_validation->run() == FALSE) {
			$data['errors'] = validation_errors();
		} else {			
			$toemail = $this->input->post('email');
			$fromemail = 'noreply@travelnego.com';
			$subject   = '[Travelnego] Konfirmasi Reset Password Anda';
			$verifikasi_id   = md5($toemail.time().'travelnegoBarokah3321');
			$emaildata['verifikasi_id'] = $verifikasi_id;
			$users = $this->mongodb->users->findOne(array('email'=>$toemail));
			$emaildata['name'] = $users['name'];
			$mesg = $this->load->view('auth/forgetpassword_email', $emaildata, TRUE);

			$this->load->library('email');

			$this->email->clear(TRUE);
			$this->email->initialize($this->config->item('emailrobot'));			
			$this->email->to($toemail);
			$this->email->from($fromemail, 'Travelnego.com');
			$this->email->subject($subject);
			$this->email->message($mesg);

			if ($this->email->send()) {
				$this->mongodb->users->update(
					array('email' => $toemail),
					array('$set' => array('verifikasi_code'=>$verifikasi_id))
				);
				$data['success'] = 'Silahkan periksa email anda untuk verifikasi reset password';
			} else {
				$data['success'] = 'Verifikasi reset password anda gagal';
			}
		}
		$layout['contents'] = $this->load->view('auth/forgetpassword', $data, TRUE);
		$this->load->view('layout', $layout);
	}
	public function resetpassword(){
		if ($this->session->has_userdata('loggedIn')) {
			if (preg_match("/auth/i", $this->session->last_page(1))) {
				redirect();
			} else {
				redirect($this->session->last_page(1));
			}
		}

		$post = $this->input->post(NULL, TRUE);
		$data['post'] = $post;

		$verifikasi_id = $this->uri->segment(3);
		
		if ($this->mongodb->users->count(array('verifikasi_code' => $verifikasi_id)) == 0) {		
			$data['success'] = 'Kode verifikasi verifikasi anda telah expired atau tidak valid!';
		}else{
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('konfirmasipassword', 'Konfirmasi Password', 'required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$data['errors'] = validation_errors();
			} else {
				$password = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
				$this->mongodb->users->update(
					array('verifikasi_code' => $verifikasi_id),
					array('$set' => array('password'=>$password))
				);				
				$user = $this->mongodb->users->findOne(array('verifikasi_code' => $verifikasi_id));
				$this->mongodb->users->update(
					array('verifikasi_code' => $verifikasi_id),
					array('$set' => array('verifikasi_code'=>''))
				);				
				unset($user['password']);
				$this->session->set_userdata('loggedIn', json_decode(json_encode($user)));
				$data['success'] = 'Proses reset password anda berhasil. Kembali ke <a href="'.base_url().'">home</a>';
			}	
		}
		
		$layout['contents'] = $this->load->view('auth/resetpassword', $data, TRUE);
		$this->load->view('layout', $layout);
	}
	public function test(){
		$users = $this->mongodb->users->find();
		foreach ($users as $u) {
			echo '<pre>';
			print_r($u);
		}

	}
	public function check_adult_birth()
	{			
		foreach ($_GET['adultbirth'] as $value) {
			$age = $value;
		}	
		$different = time() - strtotime($age);
		$total 	   = $different / 86400 / 360;
		if($total <= 12){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	public function check_child_birth()
	{	
		foreach ($_GET['childbirth'] as $value) {
			$age = $value;
		}	
		$different = time() - strtotime($age);
		$total 	   = $different / 86400 / 360;
		if(($total <= 12) && ($total >= 2)){
			echo 'true';
		}else{
			echo 'false';
		}
	}
	public function check_infant_birth()
	{	
		foreach ($_GET['infantbirth'] as $value) {
			$age = $value;
		}	
		$different = time() - strtotime($age);
		$total 	   = $different / 86400 / 360;
		if($total >= 2){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	public function check_passport_birth()
	{	
		if(isset($_GET['passportexp'])){
			$age = $_GET['passportexp'];
		}else{
			foreach ($_GET as $value) {
				$age = $value[0];			
			}	
		}
		
		$different = strtotime($age) - time();
		$total 	   = $different / 86400 / 30;
		
		if($total <= 6){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	public function check_birth(){		
		$birth = strtotime($this->input->get('birth'));
		if($birth >= time()){
			echo 'false';
		}else{
			echo 'true';
		}
	}

}
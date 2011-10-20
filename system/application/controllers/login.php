<?php
/*
 * This file is part of Compost.
 *
 * Compost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Compost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Compost.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Login - handle authentication
 */
class Login extends MY_Controller {
    /**
     * Start the session
     */
    function Login() {
        parent::MY_Controller();
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");
    }

    /**
     * /login/
     * Displays the login form.
     */
    function index() {
			
			if(!$this->data['site_title']) {
				$this->data['pagename'] = 'Site Setup';
				$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];

				//Page Description
				$this->data['description'] = 'Use this form to create your site settings.';
				$this->load->view('first_setup', $this->data);
			} else {
				if( isset($_SESSION['userrole']) ) {
					redirect(base_url().index_page().'admin');
				}
				//show the login form
				//Page Title
				$this->data['pagename'] = 'Login';
				$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
				//Highlighted Tab
				$this->data['tab'] = 'login';
				//Page Description
				$this->data['description'] = 'Use the form below to log in.';
				//Sidebar Menu
				$this->data['menu'] = array();
				$this->data['menu'][] = array(
						'link'	=> 'login/forgot',
						'name'	=> 'Forgot Your Password?'
				);

				//Load the View
				$this->load->view('login_view', $this->data);
			}
    }

    /**
     * /login/authenticate/
     * Expected: $_POST
     * Check credentials and log in
     */
    function authenticate() {
        if (!isset($_POST['username'])) {
            redirect(base_url().index_page().'login');
        }
        
        //get the POST data
        $username = $_POST['username'];
        $password = $_POST['password'];
        $referrer = $_POST['referrer'];

        //check the credentials
        $companyid = $this->User_model->GetAuthentication($username, $password);
        $userid = $this->User_model->GetUserId($username);
        if ($companyid) {
            //set the session variable
            //client or admin?
            $_SESSION['userrole'] = $companyid;
            $_SESSION['userid'] = $userid;

            //redirect to the referring page
            if(!isset($referrer) || $referrer == false) {
                redirect(base_url().index_page().'admin');
            }
            else {
                redirect(base_url().index_page().$referrer);
            }

        } else {
            //try again
            $this->data['message'] = '<span class="red">Your Username or Password is incorrect.</span>';
            $this->index();
        }
    }

    /**
     * /login/logout/
     * Logs the user out.
     */
    function logout() {
        
        //clear authentication session variable
        session_destroy();
        //set up the login page -- $this->index();
        redirect(base_url().index_page().'login');
    }

    /**
     * /login/forgot/
     * Displays the "forgot password" form.
     */
    function forgot() {
        //set up the forgot password page
        
        //Page Title
        $this->data['pagename'] = 'Forgot Your Password?';
        $this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
        //Highlighted Tab
        $this->data['tab'] = 'login';
        //Page Description
        $this->data['description'] = 'Enter your username and email address, and we will email you a new password.';
        //Sidebar Menu
        $this->data['menu'] = array();
        $this->data['menu'][] = array(
                'link'	=> 'login',
                'name'	=> 'Return to Login'
        );
        $this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

        //Load the View
        $this->load->view('forgot_view', $this->data);
    }

    /**
     * /login/resend
     * Expected: $_POST
     * Resends the credentials to the email address on file. Redirects to login page.
     */
    function resend() {
        //validate
        $valid = true;
        $errors = array();

        if (!isset($_POST['username']) || strlen($_POST['username']) == 0) {
            $valid = false;
            $errors['username'] = true;
        }
        if (!isset($_POST['email']) || strlen($_POST['email']) == 0) {
            $valid = false;
            $errors['email'] = true;
        }

        
        if ($valid) {
            //now check to see if the username and email match up
            $newpassword = $this->User_model->SetPassword($_POST['username'], $_POST['email']);
            if ($newpassword) {
                //success! send the email
                $to = $_POST['email'];
                $subject = 'Compost - Your new password';
                $body = 'Your new password is '.$newpassword.'. Use it wisely. -Compost Admin';
                if (mail($to, $subject, $body)) {
                    $this->data['message'] = "Your new password has been sent to ".$_POST['email'].".";
                } else {
                    //database problem
                    $this->data['message'] = "<span class='red'>There was a problem sending the email. Our fault, not yours.</span>";
                }
                $this->index();
            } else {
                //auth failure.
                $this->data['errors'] = array(
                        'email' => true,
                        'username' => true,
                );
                $this->forgot();
            }
        } else {
            $this->data['errors'] = $errors;
            $this->forgot();
        }
    }

	function setup(){
		if(!$_POST){
			redirect(base_url().index_page().'admin');
		}
		//validate
		$valid = true;
		$errors = array();
		if (!isset($_POST['Site_Title']) || strlen($_POST['Site_Title']) == 0) {
			$valid = false;
			$errors['Site Title'] = true;
		}
		if (!isset($_POST['UserName']) || strlen($_POST['UserName']) == 0) {
			$valid = false;
			$errors['UserName'] = true;
		}
		if (!isset($_POST['UserEmail']) || strlen($_POST['UserEmail']) == 0) {
			$valid = false;
			$errors['UserEmail'] = true;
		}
		if (!isset($_POST['UserPassword']) || strlen($_POST['UserPassword']) == 0) {
			$valid = false;
			$errors['UserPassword'] = true;
		}
		if (!isset($_POST['RetypePassword']) || strlen($_POST['RetypePassword']) == 0) {
			$valid = false;
			$errors['RetypePassword'] = true;
		}
		if ($_POST['RetypePassword'] != $_POST['UserPassword']) {
			$valid = false;
			$errors['RetypePassword'] = true;
		}
		if ($valid) {
			$settings = array();
			$settings['Site Title'] = $_POST['Site_Title'];
			$result = $this->Settings_model->SetSettings($settings);
			$this->load->model('User_model');
			$userResult = $this->User_model->SetUser($_POST['UserName'], $_POST['UserPassword'], $_POST['UserEmail'], '-1', '-1');
			
			if($userResult && $result) {
				redirect(base_url() . index_page() . 'admin/');
			} else { 
				if (!$userResult) {
					echo 'An error occurred when attempting to create the admin user.<br />';
				}
				if (!$result) {
					echo 'An error occurred when attempting to modify the site settings.<br />';
				}
			}
		} else {
			//validation fails
			$this->data['pagename'] = 'Site Setup';
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
			
			$this->data['errors'] = $errors;
			$this->data['message'] = "<span class='red'>Please fill in all fields, and make you retype your password correctly.</span>";
			$this->load->view('first_setup', $this->data);
		}
	}

}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */
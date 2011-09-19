<?
/*
#############################################################################################################################

	Title:		User
	Author(s):	Randy Apuzzo

	Created:	2011-09-16
	Modified:	2011-09-17

	Description: Basic user for Zesty Website Front-end's
	
	
	- password reset
	- promotional subscribe boolean
	- update name/email
		
#############################################################################################################################
*/

	class User extends Util {
		
################################################################################################################################
#	Constants
################################################################################################################################
		
		private const TABLE_NAME 	= "users";
		
		private const MYSQL_HOST 	= "localhost";
		private const MYSQL_USER 	= "username";
		private const MYSQL_PASS 	= "password";
		private const MYSQL_DB 		= "database";

################################################################################################################################
#	Setup Methods
################################################################################################################################

		# -------------------------------------------------------------------------------------------------
		#	Construct
		#		authorize the user, or creates the user
		# -------------------------------------------------------------------------------------------------
		
			public function __construct($user_email=false,$user_pass=false){
				
				// setup mysqli database handler (dbh)
				$this->dbh = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
				
				// if user_email isnt present and $_POST exists, create the user
				if(!$user_email && $_POST['password_confirm']) $this->registerUser($_POST); 
				
				// if both user pass and email are pass, attempt to authorize
				if($user_email && $user_pass){
					
					// result is a object if success, fase other wise
					$authorize_result = $this->authorizeUser($user_email, $user_pass);	
					if($authorize_result) $this->sessionUser($authorize_result);
				} 
				
				// check the user ip (to make sure it hasn't changed)
				if ($_SESSION['user_ip'] != $_SERVER['REMOTE_ADDR']) $this->logout();		
							
			}

################################################################################################################################
#	Methods
################################################################################################################################


		# -------------------------------------------------------------------------------------------------
		#	authorizeUser
		#		cross references the database with user credentials
		#		return $user object OR false
		# -------------------------------------------------------------------------------------------------	
		
			public function authorizeUser($user_email, $user_pass){
								
				global $dbh;
				
				// hash the password if it isnt already a stored hash in the session
				if(!$_SESSION['user_password']) $user_pass = $this->hashPassword($user_pass);
				
				// Escape bad strings for email
				$user_email = $dbh->real_escape_string($user_email);
				
				// check if user is in the database				
				
				$user = $this->dbh->query("SELECT * FROM `".self::TABLE_NAME."` WHERE email = '{$user_email}' AND password = '{$user_pass}' LIMIT 1");
				
				// if we find a valid user in the database, return the user object
				if ( $user->num_rows > 0 ) {
				
					return $user->fetch_object();
				
				} else {
					
					// If not, restart the session
					$this->restartSession();
					
					// and return false
					return false;
				}
				
							
			}
			
			
		# -------------------------------------------------------------------------------------------------
		#	restartSession
		#		change the user password
		# -------------------------------------------------------------------------------------------------	
		
			private function restartSession(){
				
				// If there is a cart store it to restore
				if (isset($_SESSION['CART'])) $save_cart = $_SESSION['CART'];
				
				// restart session
				session_destroy();
				session_regenerate_id();
				session_start();
				
				// If there is a saved cart to restore, do it
				if (isset($save_cart)) $_SESSION['CART'] = $save_cart;

				
			}	

		# -------------------------------------------------------------------------------------------------
		#	logout
		#		change the user password
		# -------------------------------------------------------------------------------------------------	
		
			public function logout(){
				
				$this->restartSession();				
			}	

		# -------------------------------------------------------------------------------------------------
		#	registerUser
		#		Creates a new user in the system
		#		returns BOOLEAN
		# -------------------------------------------------------------------------------------------------	
		
			public function registerUser($post_value){
				
				// !!
				// Run some troll/ inject tests
				// if no good, return false
				// !!
				
				
				// remove exccess
				unset($post_value['email_confirm']);
				unset($post_value['password_confirm']);
				
				// start SQL
				$sql = "INSERT INTO `" . self::TABLE_NAME . "` SET ";
					
				// loop through the post_value results, put them into SQL
				foreach($post_value as $key => $value){
					// encode the password
					if($key == 'password') $value = $this->hashPassword($value);
					// append to the statement
					$sql_append .= " `{$key}` = '{$value}',";
				}
				
				// finish the query
				$sql .= rtrim($sql_append,",");
				
				// create the user
				$this->dbh->query($sql);
				
				// !!
				// send email confirmation
				// !!
				
				
			}	

		# -------------------------------------------------------------------------------------------------
		#	changePassword
		#		change the user password
		# -------------------------------------------------------------------------------------------------	
		
			private function changePassword($new_password){
				
				$this->updateValue('password',$new_password);
								
			}		
		
			
		# -------------------------------------------------------------------------------------------------
		#	resetPassword
		#		send the user variables into the $_SESSION
		# -------------------------------------------------------------------------------------------------	
		
			public function resetPassword($email, $secret_answer){
			
				// check the secret question/answer
				
				// generate new random password
				
				// email new password
				
			}				
					
		# -------------------------------------------------------------------------------------------------
		#	sessionUser
		#		send the user variables into the $_SESSION
		# -------------------------------------------------------------------------------------------------	
		
			private function sessionUser($user_object){
				// loop and set
				foreach($user_object as $key =>$value){
					// set to session
					$_SESSION['user_'.$key] = $value;
					// set to user
					$this->$key = $value;
				}
				$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
				$this->user_ip = $_SERVER['REMOTE_ADDR'];
				
			}		


################################################################################################################################
#	Utility Methods
################################################################################################################################


		# -------------------------------------------------------------------------------------------------
		#	hashPassword
		#		encrypt the password
		# -------------------------------------------------------------------------------------------------	
		
			public function hashPassword($password,$salt="3Lwfe#&"){
				
				return hash('whirlpool',$password.$salt);
								
			}	


		# -------------------------------------------------------------------------------------------------
		#	updateValue
		#		Description
		# -------------------------------------------------------------------------------------------------	
		
			private function updateValue($key,$value){
				
				$this->dbh->query("UPDATE `" . self::TABLE_NAME . "` SET `{$key}` = '{$value}' WHERE `id` = {$this->id}");
				
			}	

	  	
/* ------------------------------------------------------------------------------------------------------------------------------
 *	END OF CLASS:
 * ------------------------------------------------------------------------------------------------------------------------------ */
 }
?>
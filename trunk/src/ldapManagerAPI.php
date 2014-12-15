<?	

/**
 * CLASSE PHP PARA MANIPULAÇÃO DA BASE  
 * Version 1.0
 * 
 * PHP Version 5 with SSL and LDAP support
 * 
 * @category ToolsAndUtilities
 * @package ldapManagerAPI
 * @author Jonas Cavalcanti / Maxlane
 * @revision $Revision: 169 $
 * @version 1.0
 */

class ldapManager{

	/**
    * Servidor da base LDAP
    * @var string
    */
	protected $ldaphost = NULL;
	
	/**
    * Porta default LDAP non-SSL connections
    * @var string
    */
	protected $ldapport = NULL;
	
	/**
    * Domain Name da Base LDAP
    * @var string
    */
	protected $ldapbasedn = NULL;
	
	/**
    * Nome do root da base LDAP
    * @var string
    */
	protected $ldaprootdn = NULL;
	
	/**
    * Password do usuario administrador da base LDAP
    * @var string
    */
	protected $ldappasswdrootdn = NULL;
	
	
	protected $conn = NULL;
	
	//ldapManager Constructor
	function ldapManager($host,$port,$basedn,$rootdn,$passdn){
	
		$this->ldaphost = "ldap://".$host;
		$this->ldapport = $port;
		$this->ldapbasedn = $basedn;
		$this->ldaprootdn = $rootdn;
		$this->ldappasswdrootdn = $passdn;
	}
	
	
	/**
    * Abre conexao com a base LDAP
    * 
    * @return void
    */
	public function getConnectLdap(){
	
		$conexaoLdap = NULL;
			
		$conexaoLdap = ldap_connect($this->ldaphost, $this->ldapport) or die("Could not connect to $this->ldaphost");
		
		if($conexaoLdap){
			
			ldap_set_option($conexaoLdap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($conexaoLdap, LDAP_OPT_REFERRALS, 0);
			
			$ldapbind = ldap_bind($conexaoLdap, "cn=".$this->ldaprootdn.",".$this->ldapbasedn, $this->ldappasswdrootdn);

	        if (!$ldapbind)
	        	throw new Exception("Could not authentication to ".$this->ldaphost);
	     }
	     else
	     	throw new Exception("Could not connect to ".$this->ldaphost);
	     	
		return  $conexaoLdap;
	}
	
	/**
    * Conection close LDAP
    * 
    * @return void
    */
	public function closeConnectLdap(){
		ldap_close($this->conn);
	}
	
	/**
    * Return a users array
    * 
    * ex.: getUsers(conection,"uid",array("uid"));
    * @param array $arrayParam
    * @return array
    */
	public function getUsers($conn, $fieldFilter, $arrayParametrs){
				
		$listFilterUsers = NULL;
		$arrayUsers = array();
		
		$struturaBaseDNFilter = "ou=Users,".$this->ldapbasedn;
	 
		$list = ldap_list($conn, $struturaBaseDNFilter, $fieldFilter."=*", $arrayParametrs);
		
		$listFilterUsers = ldap_get_entries($conn, $list);
		var_dump($listFilterUsers);
		for ($i=0; $i < count($listFilterUsers); $i++) 
          $arrayUsers[$i] = $listFilterUsers[$i][$fieldFilter][0];
		  
		return $arrayUsers;
		
	}
	
	/**
    * Users ADD LDAP
    * 
    * 
    * @param connection $conn,String $name,String $user,String mail,String $pass
    * @return boolean
    */
	public function userAddLdap($conn,$name,$user,$dateUser,$mail,$pass){
		
		$r = FALSE;
				
		$info["objectClass"][0] = "top";
		$info["objectClass"][2] = "posixAccount";
		$info["objectClass"][1] = "inetOrgPerson";
		$info["objectClass"][3] = "shadowAccount";
		$info["objectClass"][2] = "organizationalPerson";
		$info["cn"] = $user;
		$info["givenName"] = $name;
		$info["surname"] = $name;
		$info["mail"] = $mail;
		$info["initials"] = $dateUser;
		
				
		$r = ldap_add($conn,"uid=$user,ou=Users,".$this->ldapbasedn,$info);
		
		if($r){
			$password = "{SHA}".base64_encode(pack("H*",sha1($pass)));
			$entry["userPassword"] = "$password";
			ldap_modify($conn,"uid=$user,ou=Users,".$this->ldapbasedn,$entry);
		}
		
		return $r;

	}
	
	public function userModifyPasswordLdap($conn, $user,$newPass){
		$r = FALSE;
		
		$password = "{SHA}".base64_encode(pack("H*",sha1($newPass)));
		$entry["userPassword"] = "$password";
		$r = ldap_modify($conn,"uid=$user,ou=Users,".$this->ldapbasedn,$entry);
		
		return $r;
		
	}
	
	public function validateUserLdap($conn,$user,$userDate,$userMail){
	
		 $r = array();
		 $is_r = FALSE;
		 
		 $filter= "(&(uid=$user)(initials=$userDate)(mail=$userMail))"; 
		 $attr = array("uid","initials","mail");
		 
		 $r = ldap_search($conn, "ou=Users,".$this->ldapbasedn, $filter, $attr); 
		 
		 $info = ldap_get_entries($conn, $r);
		 
		 if($info["count"] > "0"){
			$is_r = TRUE;
			return $is_r;	 
		 }
		 	
		 return $is_r;	
	}
}

?>

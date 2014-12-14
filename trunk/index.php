<?
require_once(dirname(__FILE__) . '/src/ldapManagerAPI.php');

	$host = "";
	$porta = "389";
	$basedn = "dc=<base>,dc=com,dc=br";
	$admin = "Manager";
	$senhaAdmin = "";
	$conexao = NULL;
	
	
	$ldapManager = new ldapManager($host,$porta,$basedn,$admin,$senhaAdmin);
	
	try{
		$conexao = $ldapManager->getConnectLdap();
	}catch (Exception $e) {
    	echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	//$listarUsuarios = $ldapManager->getUsers($conexao,"uid",array("uid"));
	
		
	/*$resultado = $ldapManager->userAddLdap($conexao, "MaxLane","12344321","11/06/2014","maxlane@gmail.com","1q2w3e");
	if($resultado)
		echo "usuario adcionado com sucesso";
	else
		echo "erro no add user";*/
		
		
	/*$resultado = $ldapManager->userModifyPasswordLdap($conexao, "0987654321", '123456');
	if($resultado)
		echo "Pass alterado com sucesso";
	else
		echo "Nao foi possivel alterar pass";*/

	$resultado = $ldapManager->validateUserLdap($conexao,"","", "");	
	if($resultado)
		echo "Existe usuario";
	else
		echo "Nao Existe usuario";

	$ldapManager->closeConnectLdap();
?>

<?php 

try {

	//Faz conexão com o banco de dados
	$conectar = new PDO("mysql:host=localhost;port=3306;dbname=pdo;", "root", "");

	

}catch(PDOException $e){

	//Erro na conexão
	echo "Falha ao conectar com o banco de dados: " . $e->getMessage();
}


?>
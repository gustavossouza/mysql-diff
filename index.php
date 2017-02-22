<?php
	include "class.diff.php";
	// Aqui é usado para iniciar conexao com o BD
	$diff = new banco_diff("localhost","root","","astoria");

	// Aqui é gerado as estruturas do BD para ser gravado/salvado!
	$diff->check_table();

	// Aqui é salvado em um arquivo " info_tmp.json " em JSON
	$diff->public_json();

	// Aqui é conexao em segundo BD para iniciar comparações
	$diff->db_diff("localhost","root","","astoria");


	/*
		Desenvolvido por AppTech BR.
		Código Livre, somente MYSQL
	*/

?>
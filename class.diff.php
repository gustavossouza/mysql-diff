<?php

class banco_diff{
	
	private $db,$host,$login,$senha,$bancodados;
	private $array,$array2;

	function __construct($host,$login,$senha,$bancodados){// Aqui inicia 1° conexao
		$this->host = $host;
		$this->login = $login;
		$this->senha = $senha;
		$this->bancodados = $bancodados;

		$this->array["nome_bd"] = $bancodados;

		$this->db = mysqli_connect($host,$login,$senha,$bancodados);
	}

	function check_table(){ // Aqui pega todas as informações da tabelas e salva em ARRAY
		$info_table = array();

		$sql = mysqli_query($this->db,"SELECT TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='{$this->bancodados}'");
		while($lista = mysqli_fetch_assoc($sql)){
			$nme_table = $lista['name'];
			$col = $this->check_column($nme_table);
			
			$json['table'] = $nme_table;
			$json['coluna']= $col;

			array_push($info_table,$json);
		}
		$this->array['info'] = $info_table;
	}

	function check_column($name_table){ // Aqui pega todas as informações de COLUNAS das tabelas e salva em ARRAY
		$coluna = array();
		$check_column = mysqli_query($this->db,"SHOW COLUMNS FROM {$name_table}");
			while($list_column = mysqli_fetch_assoc($check_column)){
				array_push($coluna,$list_column);
			}
			return $coluna;
	}

	function public_json(){ // Aqui salva estrutura do 1° Conexao BD para salvar
		$encode = json_encode($this->array);
		file_put_contents("info_tmp.json", $encode);
		print_r("Estrutura de banco dados gerados no arquivo info_tmp.json");


		mysqli_close($this->db);
	}

	/****/

	function db_diff($host,$login,$senha,$bancodados){
		/* Aqui pega as informações no arquivo " INFO_TMP.JSON " e faz 2° conexão para fazer comparações
		   E exibe valores errados ao original, aparece em JSON na tela mesmo!
		*/
		$this->array2["nome_bd"] = $bancodados;
		$erros = array();

		$leitura = file_get_contents("info_tmp.json");
		$json = json_decode($leitura,1);

		$bd = mysqli_connect($host,$login,$senha,$bancodados);

		$erros['tipo'] = "table";
		foreach ($json['info'] as $chave => $valores) {
			$check_table = mysqli_query($bd,"SELECT TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' AND TABLE_NAME='{$valores['table']}' AND TABLE_SCHEMA='{$this->bancodados}'");
			$lista_check = mysqli_fetch_assoc($check_table);
			$erros_js['original'] = $valores['table'];

			if(!$lista_check){
				$erros_js['info'] = "Nao existe essa tabela";
			}else{
				$erros_js['info'] = "Existe essa tabela";

				$sql_coluna = mysqli_query($bd,"SHOW COLUMNS FROM {$lista_check['name']}");
				$i=0;
				while($lista_coluna = mysqli_fetch_assoc($sql_coluna)){
					
					print_r("<pre>");
					foreach ($valores['coluna'] as $chave => $colunas) {
						if($chave==$i){
							if($lista_coluna['Field'] != $colunas['Field']){
								$erros_js['colunas'][] = "Fildes - {$colunas['Field']} : Informacoes não colide";
							}
							if($lista_coluna['Type'] != $colunas['Type']){
								$erros_js['colunas'][] = "Type - {$colunas['Type']} : Informacoes não colide";
							}
							if($lista_coluna['Null'] != $colunas['Null']){
								$erros_js['colunas'][] = "Null - {$colunas['Null']} : Informacoes não colide";
							}
							if($lista_coluna['Key'] != $colunas['Key']){
								$erros_js['colunas'][] = "Key - {$colunas['Key']} : Informacoes não colide";
							}
							if($lista_coluna['Default'] != $colunas['Default']){
								$erros_js['colunas'][] = "Default - {$colunas['Default']} : Informacoes não colide";
							}
							if($lista_coluna['Extra'] != $colunas['Extra']){
								$erros_js['colunas'][] = "Extra - {$colunas['Extra']} : Informacoes não colide";
							}
						}
					}
					$i=$i+1;
				}
			}

			array_push($erros,$erros_js);
			unset($erros_js);
		}

		print_r("<pre>");
		print_r($erros);
		


		mysqli_close($bd);
	}


}

?>
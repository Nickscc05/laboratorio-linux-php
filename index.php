<?php

//É o código PHP puro - o navegador vê somente o resultado que essas linhas produzem

$sistema = php_uname();                                            // Aqui é alocado as informações do sistema operacional 
$servidor = $_SERVER['SERVER_SOFTWARE'];                          //  Aqui fica o nome/versão do servidor web
$dataHora = date('d/m/Y H:i:s');                                 //   Informações sobre a data e hora do servidor

//Logo abaixo está o código HTML que irá aparecer na máquina 
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<title>Publicação PHP</title>
</head>
<body>
	<h1>Projeto Linux/PHP publicado com sucesso!</h1> 
	<p><strong>Ambiente:</strong> <?php echo $sistema; ?></p>
	<p><strong>Servidor web:</strong> <?php echo $servidor; ?></p>
	<p><strong>Data e Hora no servidor:</strong> <?php echo $dataHora; ?></p>
</body>
</html>

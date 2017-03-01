<?php session_start(); 
if(isset($_SESSION['user_login'])) {
	echo "<script> window.location.assign('index3.php')</script>";
	//header('Location:index.php');
	exit();
}
?>
<!DOCTYPE html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type" />
    <!-- <link rel="stylesheet" href="estilo.css" type="text/css" media="screen" /> -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="dist/sweetalert.js"></script>
    <link rel="stylesheet" href="dist/sweetalert.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Avalia SEWebS</title>
</head>

<body>

    <div class="container-fluid">
       <?php
      	 include 'header.php';
      	 include 'navbar.php';
       ?>
            
			<h3>Cadastro</h3>
			<?php
        include "conecta.php";
        
        
         function anti_injection($string) {
				// remove palavras que contenham sintaxe sql
				$string = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
				$string = trim($string);//limpa espaços vazio
				$string = strip_tags($string);//tira tags html e php
				$string = addslashes($string);//Adiciona barras invertidas a uma string
				return $string;
			}
			
//-------Verificar se o valor de validação é válido e pedir nova senha ou ativar o usuário se ele já tiver uma senha			
			if (isset($_GET['valid'])) {
				$valid = $_GET['valid'];
				
				$Sql = "SELECT * FROM `tbuser` WHERE `tbUserValid` = '".$valid."'";
				$rs = mysqli_query($conexao, $Sql) or die ("Erro ao buscar usuário");
				
				if (mysqli_num_rows($rs) == 0) {
					echo '<p>Esse link não é válido!</p>';		
				} else {
					$row = mysqli_fetch_assoc($rs);
					$user = $row['tbUserName'];
					$email = $row['tbUserEmail'];
					$user_id = $row['idtbUser'];
					$password = $row['tbUserPassword'];
					$user_level = $row['tbUserLevel'];
					
					if ($password == 0) {
						echo 'Cadastre uma nova senha:
						<form action="cadastrar.php" method="post" name="form1" class="form-group">
						<input name="txt_user_id" value="'.$user_id.'" size="12" type="text" hidden/>
						<p>Senha:</p> <p><input name="txt_password1" id="entravalor4" size="12" type="password" class="form-control" required /></p>
						<p>Repita a senha:</p> <p><input name="txt_password2" id="entravalor" size="12" maxlength="13" type="password" class="form-control" required /></p>
						<p><input value="Enviar" type="submit" name="submitUpdatePassword" class="btn btn-default" onclick="return validar()"></p>
						</form>';
					} else {
						$Sql = "UPDATE `tbuser` SET `tbUserValid` = '1' WHERE `tbuser`.`idtbUser` = ".$user_id;
						$rs = mysqli_query($conexao, $Sql) or die ("Erro ao atualizar usuário");
						$_SESSION['user_login'] = $user;
						$_SESSION['user_id'] = $user_id;
						$_SESSION['user_level'] = $user_level;
						echo "<script language='javascript' type='text/javascript'>
								swal({   title: '',   text: 'Usuário ativado!',    type: 'success'  },  function(){    window.location.href = 'index3.php';});
							</script>";
					}	
				}
			}	elseif (isset($_POST['submitUpdatePassword'])) {
				$user_id = $_POST['txt_user_id'];
				$password = md5($_POST['txt_password1']);
				$Sql = "UPDATE `tbuser` SET `tbUserPassword` = ".$password." WHERE `tbuser`.`idtbUser` = ".$user_id;
				$rs = mysqli_query($conexao, $Sql) or die ("Erro ao atualizar usuário");
				$_SESSION['user_login'] = $user;
				$_SESSION['user_id'] = $user_id;
				$_SESSION['user_level'] = $user_level;
				echo "<script language='javascript' type='text/javascript'>
						swal({   title: '',   text: 'Senha alterada com sucesso!',    type: 'success'  },  function(){    window.location.href = 'index3.php';});
					</script>";
			}	elseif (isset($_POST['submitNewUser'])) {
            $user = $_POST['txt_nome'];
            $email = $_POST['txt_email'];
            $password = md5($_POST['txt_password1']);
            $valid = md5(time());
            
				echo $email;            
            
            $Sql = "INSERT INTO `tbuser` (`idtbUser`, `tbUserName`, `tbUserEmail`, `tbUserPassword`, `tbUserLevel`, `tbUserValid`) VALUES (NULL, '" . $user . "', '" . $email . "', '" . $password . "', '1', '".$valid."')";
            
            echo $Sql;
            
            $rs = mysqli_query($conexao, $Sql) or die ("<script language='javascript' type='text/javascript'>
								swal({   title: '',   text: 'Já existe um usuário com esse email!',    type: 'error'  },  function(){    window.location.href = 'cadastrar.php';});
							</script>");

            if (mysqli_affected_rows($conexao)>0) {			
					$to = $email;
					$subject = "[AvaliaQASWebE] Usuário cadastrado";
					$txt = "<html><head><title>HTML email</title></head>
						<body>
						<h3>Olá ".$user."</h3>
						<p>Você foi cadastrado com sucesso nos sistema de avaliação AvaliaQASWebE. Para saber mais sobre o sistema de avaliação clique <a href='http://avaliasewebs.caed-lab.com/index.php' target='_blank'>aqui</a>.</p>
						<p>Para validar o seu cadastro clique no link abaixo:</p>
						<a href='http://avaliasewebs.caed-lab.com/cadastrar.php?valid=".$valid."' target='_blank'>http://avaliasewebs.caed-lab.com/cadastrar.php?valid=".$valid."</a>
						</body>
						</html>";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: <no-reply@caed-lab.com>' . "\r\n";
					
					mail($to,$subject,$txt,$headers);
					
                echo "<script language='javascript' type='text/javascript'>
								swal({   title: '',   text: 'Verifique a sua caixa de email para ativar a sua conta.',    type: 'success'  },  function(){    window.location.href = 'login.php';});
							</script>";
            } else {
                echo "<script language='javascript' type='text/javascript'> swal('Erro!'); window.location.href='index.html';</script>";
            }
        } else {
            echo '<form action="cadastrar.php" method="post" name="form1" class="form-group">';
            echo '<p>Nome Completo: </p> <p><input maxlength="60" name="txt_nome" id="entravalor2" size="50" class="form-control" required /></p>';
            echo '<p>Email para login:</p> <p><input name="txt_email" id="entravalor3" size="40" type="email" class="form-control" required /></p>';
            echo '<p>Senha:</p> <p><input name="txt_password1" id="entravalor4" size="12" type="password" class="form-control" required /></p>';
            echo '<p>Repita a senha:</p> <p><input name="txt_password2" id="entravalor" size="12" maxlength="13" type="password" class="form-control" required /></p>';
            //echo '<p><input value="Limpar" type="reset" class="btn btn-default">';
            echo '<p><input value="Enviar" type="submit" name="submitNewUser" class="btn btn-default" onclick="return validar()"></p>';
            echo '</form>';
        }
        ?>
        
        <script language="javascript" type="text/javascript">
            function validar() {
                var password1 = document.form1.txt_password1.value;
                var password2 = document.form1.txt_password2.value;

                if (password1 != password2) {
                    alert('Senhas diferentes');
                    form1.txt_password1.focus();
                    return false;
                }
            }
        </script>
        
            <?php
            include 'footer.php';
            ?>
    </div>

</body>

</html>

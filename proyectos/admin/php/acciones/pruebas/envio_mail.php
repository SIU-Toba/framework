<?php
require_once('nucleo/lib/correo.php');




	//try{

		//1: Obtengo datos
		//QUIEN
		$sql = "SELECT usuario, email FROM apex_usuario;";
		$usuarios = consultar_fuente($sql);
		//preaparar datos (eliminar usuario con mail = null)
		ei_arbol($usuarios);

		//QUE
		

		//2: Mandar mail


/*	}catch(excepcion_toba $e){
		echo "error";
	}*/









$mail = new correo();
###########
#  SET BODY  #
##########
$body = "Hola Chambon!";
$mail->set_txt_body($body);

################
# SET DE CABECERAS #
################
$hdrs['From'] = 'Central_de_pruebas@prueba.com';
$hdrs['Subject'] = 'Test mime mensaje';


$mail->set_cabeceras($hdrs);              
              
######################################
#	SET DE LAS DIRECCIONES DE EMAIL DE DESTINO	#
######################################
$mail->set_cuentas('esassone@sion.com, esassone@siu.edu.ar');



################
#  SE ENVIA EL EMAIL  #
################
$mail->enviar();


?>

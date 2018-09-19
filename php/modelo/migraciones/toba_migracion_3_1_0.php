<?php
class toba_migracion_3_1_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();		

		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN editor_config_file TEXT NULL;';
                                     
                                     $sql[] = 'ALTER TABLE apex_proyecto ALTER COLUMN estilo SET DATA TYPE TEXT;';
                                     $sql[] = 'ALTER TABLE apex_objeto_eventos ALTER COLUMN estilo SET DATA TYPE TEXT;';
                                     
                                     
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	
	function proyecto__copiar_imagen_calendario()
	{
		$nombre = 'calendario.gif';
		$dir_destino = $this->elemento->get_dir(). '/www/img/';

		$source = toba_dir(). '/php/modelo/template_proyecto/www/img/'. $nombre;		
		$destino_final = toba_manejador_archivos::path_a_plataforma($dir_destino.$nombre);
		if (! toba_manejador_archivos::existe_archivo_en_path($destino_final)) {
			copy($source, $destino_final);
		}
	}
	
	function instancia__migrar_preguntas()
	{
		$clave = $this->elemento->get_instalacion()->get_claves_encriptacion();			//Obtengo las claves con las que voy a encriptar		
		
		$sql = 'SELECT cod_pregunta_secreta, pregunta, respuesta FROM apex_usuario_pregunta_secreta;';
		$preguntas = $this->elemento->get_db()->consultar($sql);		
		if (! empty($preguntas)) {													//Si se recuperaron preguntas/respuestas secretas
			$cripter = toba_encriptador::instancia();
			$sqls = array();
			foreach($preguntas as $dato) {
				$id = $dato['cod_pregunta_secreta'];
				$preg = mcrypt_decrypt(MCRYPT_BLOWFISH, $clave['get'], base64_decode($dato['pregunta']), MCRYPT_MODE_CBC, substr($clave['db'], 0, 8));
				$resp = mcrypt_decrypt(MCRYPT_BLOWFISH, $clave['get'], base64_decode($dato['respuesta']), MCRYPT_MODE_CBC, substr($clave['db'], 0, 8));

				$preg_enc = $cripter->encriptar($preg, $clave['get']);
				$resp_enc = $cripter->encriptar($resp, $clave['get']);				
				$sqls[] = "UPDATE apex_usuario_pregunta_secreta SET pregunta = '$preg_enc', respuesta = '$resp_enc' WHERE cod_pregunta_secreta = '$id';";	
			}
			if (! empty($sqls)) {
				$this->elemento->get_db()->ejecutar($sqls);
			}
		}
	}
}
?>

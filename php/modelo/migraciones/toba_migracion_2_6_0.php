<?php
class toba_migracion_2_6_0 extends toba_migracion
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
		
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN placeholder  TEXT  NULL;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN placeholder  TEXT  NULL;';
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	 * Codifica las preguntas secretas en base_64 para que este a tono con la operacion de usuarios.
	 */
	function instancia__convertir_preguntas_secretas() 
	{
		$sql = 'SELECT cod_pregunta_secreta, pregunta, respuesta FROM apex_usuario_pregunta_secreta;';
		$preguntas = $this->elemento->get_db()->consultar($sql);
		if (! empty($preguntas)) {													//Si se recuperaron preguntas/respuestas secretas
			$sqls = array();
			foreach($preguntas as $dato) {
				$id = $dato['cod_pregunta_secreta'];
				$preg = base64_encode($dato['pregunta']);	
				$resp = base64_encode($dato['respuesta']);
			
				$sqls[] = "UPDATE apex_usuario_pregunta_secreta SET pregunta = '$preg', respuesta = '$resp' WHERE cod_pregunta_secreta = '$id';";	//Encripto y armo la SQL correspondiente
			}
			if (! empty($sqls)) {
				$this->elemento->get_db()->ejecutar($sqls);
			}
		}		

	}		
}
?>

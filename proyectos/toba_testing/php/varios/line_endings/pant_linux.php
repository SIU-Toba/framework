<?php
class pant_linux extends toba_ei_pantalla
{
	/**
		* Permite modificar la forma en que se grafica la pantalla, por defecto un componente sobre el otro
		*/
	function generar_layout()
	{
		$nombre = toba::proyecto()->get_path_php(). '/varios/line_endings/archivo_linux.php';

		//Verifico que no tiene el CHR 13 de movida
		assert(strrchr(file_get_contents($nombre), 13) === false);
		
		$archivo = new toba_archivo_php($nombre);
		$archivo->edicion_inicio();
		$codigo_actual = $archivo->contenido();

		assert(strrchr($codigo_actual, 13) === false);
		$metodo = new toba_codigo_metodo_php('extender_objeto_js');
		$metodo->set_contenido("echo \"alert('soy un alert nuevo');". "\n"."alert(4);\"");
		$nuevo_codigo = $archivo->codigo_agregar_metodo($codigo_actual, $metodo->get_codigo());
		$archivo->insertar(toba_archivo_php::codigo_sacar_tags_php($nuevo_codigo));

		//Verifico que el nuevo codigo que obtuve tampoco tiene CR antes de ser grabado
		assert(strrchr($archivo->contenido(), 13) === false);

		//Aca grabe el archivo
		$archivo->edicion_fin();
		assert(strrchr(file_get_contents($nombre), 13) === false);
		assert(strrchr(file_get_contents($nombre), PHP_EOL) !== false);

		$archivo->mostrar();
		echo 'El test parece que funco!, revirtiendo archivo....';
		$tsvn = new toba_svn();
		$tsvn->revert($nombre);
	}
}
?>
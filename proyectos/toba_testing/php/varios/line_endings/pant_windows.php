<?php
class pant_windows extends toba_ei_pantalla
{
	function generar_layout()
	{
		//El archivo original esta en formato Windows CR/LF
		$nombre = toba::proyecto()->get_path_php(). '/varios/line_endings/archivo_windows.php';		

		//Verifico que el archivo original tiene el CR
		assert(strrchr(file_get_contents($nombre), 13) !== false);
		
		$archivo = new toba_archivo_php($nombre);
		$archivo->edicion_inicio();
		$codigo_actual = $archivo->contenido();

		//Verifico que el codigo original no tiene CR al ser cargado en toba_archivo_php
		assert(strrchr($codigo_actual, 13)  === false);

		$metodo = new toba_codigo_metodo_php('extender_objeto_js');
		$metodo->set_contenido("alert('soy un alert nuevo'); \n alert(4);");
		$nuevo_codigo = $archivo->codigo_agregar_metodo($codigo_actual, $metodo->get_codigo());

		$archivo->insertar($nuevo_codigo);
		$archivo->insertar_al_final("function cargo_algo(\$id) \n { echo 'Ingrese \$id'; \n}");

		//Verifico que el nuevo codigo que obtuve tampoco tiene CR antes de ser grabado
		assert(strrchr($archivo->contenido(), 13) === false);

		//Aca grabe el archivo
		$archivo->edicion_fin();
		assert(strrchr(file_get_contents($nombre), 13) === false);
		assert(strrchr(file_get_contents($nombre), PHP_EOL) !== false);

		echo 'El test parece que funco!, revirtiendo archivo....';
		$tsvn = new toba_svn();
		$tsvn->revert($nombre);
	}
}

?>
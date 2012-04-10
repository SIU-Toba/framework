<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class toba_testing_pers_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); }
	}

	static $clases = array(
		'ci_simple' => 'ci_simple.php',
		'toba_testing_pers_ci' => 'extension_toba/componentes/toba_testing_pers_ci.php',
		'toba_testing_pers_cn' => 'extension_toba/componentes/toba_testing_pers_cn.php',
		'toba_testing_pers_datos_relacion' => 'extension_toba/componentes/toba_testing_pers_datos_relacion.php',
		'toba_testing_pers_datos_tabla' => 'extension_toba/componentes/toba_testing_pers_datos_tabla.php',
		'toba_testing_pers_ei_arbol' => 'extension_toba/componentes/toba_testing_pers_ei_arbol.php',
		'toba_testing_pers_ei_archivos' => 'extension_toba/componentes/toba_testing_pers_ei_archivos.php',
		'toba_testing_pers_ei_calendario' => 'extension_toba/componentes/toba_testing_pers_ei_calendario.php',
		'toba_testing_pers_ei_codigo' => 'extension_toba/componentes/toba_testing_pers_ei_codigo.php',
		'toba_testing_pers_ei_cuadro' => 'extension_toba/componentes/toba_testing_pers_ei_cuadro.php',
		'toba_testing_pers_ei_esquema' => 'extension_toba/componentes/toba_testing_pers_ei_esquema.php',
		'toba_testing_pers_ei_filtro' => 'extension_toba/componentes/toba_testing_pers_ei_filtro.php',
		'toba_testing_pers_ei_formulario' => 'extension_toba/componentes/toba_testing_pers_ei_formulario.php',
		'toba_testing_pers_ei_formulario_ml' => 'extension_toba/componentes/toba_testing_pers_ei_formulario_ml.php',
		'toba_testing_pers_ei_grafico' => 'extension_toba/componentes/toba_testing_pers_ei_grafico.php',
		'toba_testing_pers_ei_mapa' => 'extension_toba/componentes/toba_testing_pers_ei_mapa.php',
		'toba_testing_pers_servicio_web' => 'extension_toba/componentes/toba_testing_pers_servicio_web.php',
		'toba_testing_pers_autoload' => 'toba_testing_pers_autoload.php',
	);
}
?>
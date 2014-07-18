<?php

class toba_datos_editores
{
	static function get_pantallas_toba_ci()
	{
		return array (
  0 => 
  array (
    'identificador' => '0',
    'etiqueta' => 'Básicas',
    'imagen' => 'objetos/multi_etapa.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Dependencias',
    'imagen' => 'objetos/asociar_objeto.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Pantallas',
    'imagen' => 'objetos/pantalla.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'identificador' => '4',
    'etiqueta' => 'Layout',
    'imagen' => 'objetos/layout.png',
    'imagen_recurso_origen' => 'apex',
  ),
  4 => 
  array (
    'identificador' => '5',
    'etiqueta' => 'Layout Impr.',
    'imagen' => 'objetos/layout_impresion.png',
    'imagen_recurso_origen' => 'apex',
  ),
  5 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_cn()
	{
		return array (
  0 => 
  array (
    'identificador' => 'basicas',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/negocio.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'pant_dependencias',
    'etiqueta' => 'Composición',
    'imagen' => 'objetos/asociar_objeto.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => 'consumo',
    'etiqueta' => 'Consumo',
    'imagen' => 'objetos/relaciones.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_datos_relacion()
	{
		return array (
  0 => 
  array (
    'identificador' => 'p_prop_basicas',
    'etiqueta' => 'Propiedades basicas',
    'imagen' => 'objetos/datos_relacion.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'p_tablas',
    'etiqueta' => 'Tablas',
    'imagen' => 'objetos/datos_tabla.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => 'p_relaciones',
    'etiqueta' => 'Relaciones',
    'imagen' => 'objetos/relaciones.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_datos_tabla()
	{
		return array (
  0 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Propiedades basicas',
    'imagen' => 'objetos/datos_tabla.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Columnas',
    'imagen' => 'objetos/columna.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Carga externa',
    'imagen' => 'objetos/carga_externa.png',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'identificador' => '4',
    'etiqueta' => 'Valores unicos',
    'imagen' => 'calendario.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_arbol()
	{
		return array (
  0 => 
  array (
    'identificador' => 'basicas',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/arbol.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'p_eventos',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_archivos()
	{
		return array (
  0 => 
  array (
    'identificador' => '0',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/archivos.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_calendario()
	{
		return array (
  0 => 
  array (
    'identificador' => 'basicas',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/calendario.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_codigo()
	{
		return array (
  0 => 
  array (
    'identificador' => 'pant_inicial',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/code.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'pant_eventos',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_cuadro()
	{
		return array (
  0 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Básicas',
    'imagen' => 'objetos/cuadro_array.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Columnas',
    'imagen' => 'objetos/columna.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => 'pant_cortes',
    'etiqueta' => 'Cortes Control',
    'imagen' => 'objetos/fila.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_esquema()
	{
		return array (
  0 => 
  array (
    'identificador' => 'basicas',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/esquema.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'p_eventos',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_filtro()
	{
		return array (
  0 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Propiedades básicas',
    'imagen' => 'objetos/filtro.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Columnas a filtrar',
    'imagen' => 'objetos/efs.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_firma()
	{
		return array (
  0 => 
  array (
    'identificador' => 'pant_inicial',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'objetos/firma.png',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'pant_eventos',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_formulario()
	{
		return array (
  0 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Básicas',
    'imagen' => 'objetos/ut_formulario.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Elementos (efs)',
    'imagen' => 'objetos/efs.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => '4',
    'etiqueta' => 'Layout',
    'imagen' => 'objetos/layout.png',
    'imagen_recurso_origen' => 'apex',
  ),
  3 => 
  array (
    'identificador' => '5',
    'etiqueta' => 'Impresión',
    'imagen' => 'objetos/layout_impresion.png',
    'imagen_recurso_origen' => 'apex',
  ),
  4 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_formulario_ml()
	{
		return array (
  0 => 
  array (
    'identificador' => '1',
    'etiqueta' => 'Propiedades basicas',
    'imagen' => 'objetos/ut_formulario_ml.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => '2',
    'etiqueta' => 'Elementos (efs)',
    'imagen' => 'objetos/efs.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  2 => 
  array (
    'identificador' => '3',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_grafico()
	{
		return array (
  0 => 
  array (
    'identificador' => 'pant_inicial',
    'etiqueta' => 'Pantalla Inicial',
    'imagen' => 'objetos/grafico.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_ei_mapa()
	{
		return array (
  0 => 
  array (
    'identificador' => 'basicas',
    'etiqueta' => 'Basicas',
    'imagen' => 'objetos/met_estatico.gif',
    'imagen_recurso_origen' => 'apex',
  ),
  1 => 
  array (
    'identificador' => 'pant_eventos',
    'etiqueta' => 'Eventos',
    'imagen' => 'evento.png',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

	static function get_pantallas_toba_servicio_web()
	{
		return array (
  0 => 
  array (
    'identificador' => '0',
    'etiqueta' => 'Propiedades Básicas',
    'imagen' => 'solic_wddx.gif',
    'imagen_recurso_origen' => 'apex',
  ),
);
	}

}

?>
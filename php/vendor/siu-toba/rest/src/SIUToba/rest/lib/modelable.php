<?php

namespace SIUToba\rest\lib;

/**
 * Esta clase no es obligatoria para el uso de los modelos. Está para referencia mayormente.
 */
interface modelable
{
    /**
     * Retorna un arreglo de modelos.
     *
     * @return array
     */
    public static function _get_modelos();

//	El modelo se construye con los campos del lado izquiero.
//	Por defecto son de type=> string y se obtienen de la columna con el mismo nombre
//
//   Los campos especiales son
//      -- _mapeo: el valor del campo se toma de la columna _mapeo.
//      -- _compuesto: el valor del campo es un subarreglo que se calcula recursivamente con dicha especificacion.
//      -- _id: la fila que no se debe repetir (se usa al agrupar; filas con el mismo id, se agrupan segun las columas _agrupado.
//      -- _agrupado: si la columna tiene este atributo se agrupan los valores de la misma entre filas que compartan la columna _id.
//
//		Ejemplos
//
//	  		'Curso' => array(
//	 				'id_curso_externo'=> array('_mapeo' => 'curso'),
//	 				'nombre' => array(),
//	 				'estado' => array('enum' => array('A', 'B')),
//	 				'id_plataforma' => array('_mapeo' => 'sistema'),
//	 				'comisiones' => array('type'=> 'array', 'items'=> array('type'=> 'Comision')),
//	 		),
//
//			'Comision' => array(
//				"comision"          => array('type' => 'integer'),
//				"nombre",
//				"catedra"    => array('_mapeo' => "nombre_catedra"),
//
//				"modalidades"  => array('_mapeo' => "nombre_modalidad",
//				                        "type"   => "array", "items" => array("\$ref" => "string")
//				),
//				"turno"             => array('_compuesto' =>
//					                             array('turno'        => array(),
//					                                   "nombre" => array('_mapeo' => "nombre_turno"))
//				),
//				'ubicacion'         => array('_compuesto' =>
//					                             array('ubicacion'        => array(),
//					                                   'nombre_ubicacion' => array('_mapeo' => "nombre"))
//				),
//				'actividad'         => array('_compuesto' => array(
//					'codigo' => array('_mapeo' => "codigo_actividad"),
//					'nombre' => array('_mapeo' => "nombre_actividad"))
//				),
//
//				'periodo_lectivo'   => array('_compuesto' => array(
//					'periodo_lectivo',
//					'nombre' => array('_mapeo' => "nombre_periodo"),
//				)
//				),
//			),
//		);
//
//     'Agrupacion' => array(
//			'comision' ,
//			'horarios' => array('_agrupado_por' => 'comision', '_compuesto' =>
//								array('dia'    => array('_mapeo' => 'horario_dia'),
//									  'inicio' => array('_mapeo' => 'horario_inicio'),
//									  'fin'    => array('_mapeo' => 'horario_fin')
//								),
//							)
//			)
//      Esto mapea algo asi  [comision1; horario1], [comision1; horario2], [comision1; horario3]
//      a algo asi: [comision1; [horario1, horario2, horario3]]
//
//
//
}

<?php

namespace rest\lib;


class rest_hidratador {


	/**
	 * Dada la especificacion de campos, genera un arreglo multidemensial
	 * con los datos de a la fuente (un recordset).
	 *
	 * Formato:
	 * 'nombre_en_fuente'  -->se mapea a 'nombre_en_fuente' en la salida
	 * 'nombre_en_fuente' => 'nombre_en_objeto' -->
	 * 'nombre_sub_objeto' => array(
	 *      'nombre_en_fuente' => 'nombre_en_sub_objeto',
	 *      '...',
	 * ),
	 *
	 * @param $spec   -> especificacion de campos
	 * @param $fuente -> recordset con los datos
	 * @param array $merges -> array('id_fila' => curso,
	 *                      grupos => array(
	 *                          array('comisiones' => 'comision') //igual que $spec
	 *                      )
	 *                   )
	 * @return array especificacion hidratada con los datos
	 */
	public static function hidratar($spec, $fuente, $merges = array())
	{
		//recorro el rs. Si hay merges los aplico (pasa algunas columnas a un sub-arreglo, las uno por id)
		if (!empty($merges)) {
			$id_fila = $merges['id_fila'];
			$grupos = self::aplicar_merges($fuente, $merges);
		}

		$return = array();
		foreach ($fuente as $fila){
			if(!empty($merges) && isset($return[$fila[$id_fila]])) continue;

			$nueva_fila = self::aplicar_spec_fila($spec, $fila);
			if(!empty($merges)){
				$nueva_fila = array_merge($nueva_fila, $grupos[$fila[$id_fila]]);
				$return[$fila[$id_fila]] = $nueva_fila;
			}else {
				$return[] = $nueva_fila;
			}

		}
		if(!empty($merges)){
			return array_values($return);

		}
		return $return;
	}

	public static function hidratar_fila($spec, $fuente,  $merges = array())
	{
		$h = self::hidratar($spec, array($fuente), $merges);
		return $h[0];
	}

	/**
	 * @param $spec
	 * @param $fila
	 * @return array
	 */
	protected static function aplicar_spec_fila($spec, $fila)
	{
		$nueva_fila = array();
		foreach ($spec as $key => $campo) {
			if (is_array($campo)) { //es un objeto
				$nuevo_objeto = array();
				foreach ($campo as $campo_fuente => $campo_objeto) {
					$nuevo_objeto[$campo_objeto] = $fila[$campo_fuente];
				}
				$nueva_fila[$key] = $nuevo_objeto;
			} else {
				if (!is_numeric($key)) { //cambio el nombre
					$nueva_fila[$campo] = $fila[$key];
				} else {
					$nueva_fila[$campo] = $fila[$campo];
				}
			}
		}
		return $nueva_fila;
	}

	/**
	 * Transorma un rs a una estrucutra anidada (1 nivel maximo). Para esto toma el id de la fila que dice
	 * el $merges. En caso del LEFT JOINS ese subrecursos puede estar vacio, por lo que no se incluye. Para
	 * esto se testea la primer columna (poner el id del subrecurso primero)
	 * @param $fuente
	 * @param $merges
	 * @return mixed
	 */
	protected static function aplicar_merges($fuente, $merges)
	{/*
		Dado un rs, hidrata subrecursos primero, y luego los agrupa por el id de la fila
		Esto sirve para representar un uno-a-muchos en forma jerarquica
	*/
		$grupos = array();
		$id_fila = $merges['id_fila'];
		foreach ($merges['grupos'] as $nombre => $m) {
			$m[$id_fila] = $id_fila; //agrego id al grupo
			$filas = self::hidratar($m, $fuente);
			foreach ($filas as $fila){
				$id = $fila[$id_fila];
				unset($fila[$id_fila]);
				if(current($fila) == NULL ){
					$grupos[$id][$nombre] = array();
				}else{
					$grupos[$id][$nombre][] = $fila;
				}

			}
		}
		return $grupos;
	}
}
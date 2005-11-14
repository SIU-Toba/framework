<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_datos_tabla extends elemento_objeto
{
	
	function utilerias()
	{
		$iconos = array();
		$param_editores = array(apex_hilo_qs_zona=>$this->id_proyecto().apex_qs_separador.$this->id_objeto());
		if (isset($this->datos['apex_objeto_db_registros'][0]["ap_archivo"])) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("php.gif", false),
				'ayuda' => "Ver detalles de la extensión del Adm.Persistencia",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos/php", $param_editores,
																		false, false, null, true, "central")
			);
		}
		return array_merge($iconos, parent::utilerias());	
	}	
	
	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan los EFs del ei_formulario
	*/
	function exportar_datos_efs($incluir_pk=false)
	{
		$a=0;
		foreach($this->datos['apex_objeto_db_registros_col'] as $columna){
			if( (!$columna['pk']) || $incluir_pk){
				$datos[$a]['identificador'] = $columna['columna'];
				$datos[$a]['columnas'] = $columna['columna'];
				$datos[$a]['etiqueta'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				if(isset($columna['secuencia'])){
					$datos[$a]['elemento_formulario'] = 'ef_fijo';
				}else{
					if($columna['no_nulo_db']) $datos[$a]['obligatorio'] = 1;
					switch($columna['tipo']){
						case 'E':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero';
							break;
						case 'N':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero_moneda';
							break;
						case 'F':
							$datos[$a]['elemento_formulario'] = 'ef_editable_fecha';
							break;
						default:
							$datos[$a]['elemento_formulario'] = 'ef_editable';
					}
				}
				$datos[$a]['orden'] = $a;
				$a++;			
			}
		}
		return $datos;
	}

	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan las columnas del ei_cuadro
	*/
	function exportar_datos_columnas($incluir_pk=false)
	{
		$a=0;
		foreach($this->datos['apex_objeto_db_registros_col'] as $columna){
			if( ((!$columna['pk']) || $incluir_pk) && !(isset($columna['secuencia'])) ){
				$datos[$a]['clave'] = $columna['columna'];
				$datos[$a]['titulo'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				switch($columna['tipo']){
					case 'E':
						$datos[$a]['estilo'] = '0';//numero-1
						break;
					case 'N':
						$datos[$a]['estilo'] = '0';
						break;
					default:
						$datos[$a]['estilo'] = '4';	//texto-1
				}
				$datos[$a]['orden'] = $a;
				$a++;			
			}
		}
		return $datos;
	}
}
?>
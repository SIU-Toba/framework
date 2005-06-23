<?
//Generacion: 17-06-2005 12:51:57
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_mt.php');

class dbr_apex_objeto_mt_me_etapa_dep extends db_registros_mt
//db_registros especifico de la tabla 'apex_objeto_mt_me_etapa_dep'
{
	function __construct($id, $fuente, $tope_registros=null, $utilizar_transaccion=null, $memoria_autonoma=null)
	{
		$definicion['tabla'][0]='apex_objeto_dependencias';
		$definicion['tabla_alias'][0]='dep';		
		$definicion['apex_objeto_dependencias']['clave'][0]='proyecto';
		$definicion['apex_objeto_dependencias']['clave'][1]='objeto_consumidor';
		$definicion['apex_objeto_dependencias']['clave'][2]='identificador';
		$definicion['apex_objeto_dependencias']['no_nulo'][0]='proyecto';
		$definicion['apex_objeto_dependencias']['no_nulo'][1]='objeto_consumidor';
		$definicion['apex_objeto_dependencias']['no_nulo'][2]='objeto_proveedor';
		$definicion['apex_objeto_dependencias']['no_nulo'][3]='identificador';
		$definicion['apex_objeto_dependencias']['columna'][0]='objeto_proveedor';	
	
	
		$definicion['tabla'][1]='apex_objeto_mt_me_etapa_dep';
		$definicion['tabla_alias'][1]= 'etapa_dep';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][0]='objeto_mt_me_proyecto';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][1]='objeto_mt_me';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][2]='posicion';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][3]='proyecto';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][4]='objeto_consumidor';
		$definicion['apex_objeto_mt_me_etapa_dep']['clave'][5]='identificador';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][0]='objeto_mt_me_proyecto';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][1]='objeto_mt_me';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][2]='posicion';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][3]='proyecto';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][4]='objeto_consumidor';
		$definicion['apex_objeto_mt_me_etapa_dep']['no_nulo'][5]='identificador';
		
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][0]['pk'] = 'proyecto';
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][0]['fk'] = 'proyecto';
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][1]['pk'] = 'objeto_consumidor';
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][1]['fk'] = 'objeto_consumidor';
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][2]['pk'] = 'identificador';
		$definicion['relacion']['apex_objeto_mt_me_etapa_dep'][2]['fk'] = 'identificador';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "dep.proyecto = '{$id['proyecto']}'";
		$where[] = "dep.objeto_consumidor = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>
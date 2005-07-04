<?
//Generacion: 4-07-2005 00:43:11
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ut_formulario_ef extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ut_formulario_ef'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla']='apex_objeto_ut_formulario_ef';
		$definicion['clave'][0]='objeto_ut_formulario_proyecto';
		$definicion['clave'][1]='objeto_ut_formulario';
		$definicion['clave'][2]='identificador';
		$definicion['no_nulo'][0]='objeto_ut_formulario_proyecto';
		$definicion['no_nulo'][1]='objeto_ut_formulario';
		$definicion['no_nulo'][2]='identificador';
		$definicion['no_nulo'][3]='columnas';
		$definicion['no_nulo'][4]='elemento_formulario';
		$definicion['no_nulo'][5]='orden';
		$definicion['columna'][0]='columnas';
		$definicion['columna'][1]='clave_primaria';
		$definicion['columna'][2]='obligatorio';
		$definicion['columna'][3]='elemento_formulario';
		$definicion['columna'][4]='inicializacion';
		$definicion['columna'][5]='orden';
		$definicion['columna'][6]='etiqueta';
		$definicion['columna'][7]='descripcion';
		$definicion['columna'][8]='desactivado';
		$definicion['columna'][9]='no_sql';
		$definicion['columna'][10]='total';
		$definicion['columna'][11]='clave_primaria_padre';
		$definicion['columna'][12]='listar';
		$definicion['columna'][13]='lista_cabecera';
		$definicion['columna'][14]='lista_orden';
		$definicion['columna'][15]='lista_columna_estilo';
		$definicion['columna'][16]='lista_valor_sql';
		$definicion['columna'][17]='lista_valor_sql_formato';
		$definicion['columna'][18]='lista_valor_sql_esp';
		$definicion['columna'][19]='lista_ancho';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ut_formulario = '{$id['objeto']}'";
		$where[] = "objeto_ut_formulario_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>
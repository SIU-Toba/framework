<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		$operacion = toba_recurso::imagen_proyecto('tutorial/abms-operacion.png');
		$arbol = toba_recurso::imagen_proyecto('tutorial/abms-arbol.png');
		echo "
			<p>
				Ya se han presentado los componentes principales en forma individual, resta
				mostrar cómo integrarlos para formar una operación completa. El tipo de operación
				que se va a usar de ejemplo es de los llamados ABMs (Altas-Bajas y Modificaciones de una o varias
				entidades), comenzando por los llamados <strong>simples</strong>
			</p>
			<p>
				Un ABM simple contiene las operaciones de alta, baja y modificación de una única tabla.
				La idea es utilizar un <strong>cuadro</strong> para listar los datos existentes en la tabla y un
				<strong>formulario</strong> para poder agregar, modificar o eliminar los registros individualmente.
				Estos dos componentes se encontrarán en una <strong>pantalla</strong> de un <strong>ci</strong>. Finalmente
				para transaccionar con la base de datos se utilizará un <strong>datos_tabla</strong>.
			</p>
			<table>
				<tr>
					<td valign=top><img src='$arbol'><br>
						<span class='caption'>Arbol de componentes en el editor</span></td>
					<td> </td>
					<td><img src='$operacion'><br>
					<span class='caption'>Captura de la operación en ejecución</span></td>
				</tr>
			</table>
		";		
		$vinculo = toba::vinculador()->get_url(null, 2654, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ejecutar Operación</a></p>";		
	}
}

//--------------------------------------------------------------

class pant_definicion extends pant_tutorial 
{
	function generar_layout()
	{
		echo mostrar_video('abms');
	}
}

//--------------------------------------------------------------


class pant_ci extends pant_tutorial 
{
	function generar_layout()
	{
		$cod_carga_cuadro = 
'<?php
function conf__cuadro()
{
    $sql = "SELECT id, nombre, descripcion FROM ref_juegos";
    return toba::db()->consultar($sql);
}
?>
';
		$cod_seleccion = 
'<?php
function evt__cuadro__seleccion($seleccion)
{
	$this->dep("datos")->cargar($seleccion);
}
?>
';
		$cod_carga_form =
'<?php
function conf__formulario()
{
	if ($this->dep("datos")->esta_cargada()) {
		return $this->dep("datos")->get();	
	}
}
?>
';
		$cod_abm =
'<?php
function evt__formulario__alta($datos)
{
	$this->dep("datos")->nueva_fila($datos);
	$this->dep("datos")->sincronizar();
	$this->dep("datos")->resetear();		
}

function evt__formulario__modificacion($datos)
{
	$this->dep("datos")->set($datos);
	$this->dep("datos")->sincronizar();
	$this->dep("datos")->resetear();				
}

function evt__formulario__baja()
{
	$this->dep("datos")->eliminar_filas();
	$this->dep("datos")->sincronizar();
	$this->dep("datos")->resetear();		
}
?>
';

		echo "
			<p>
				Una vez definidos los componentes resta programar la lógica de la operación. En este caso
				la lógica es bien simple, sólo es necesario atender los eventos y configurar el cuadro y formulario.
				En los eventos se interactúa con el datos_tabla que es quien en definitiva hace las consultas y comandos SQL.
			</p>
			<p>
				
			</p>
			<h3>Manejo del Cuadro</h3>
			<ul>
				<li>Para cargar el cuadro con datos se hace una consulta directa a la base.
					".mostrar_php($cod_carga_cuadro)."
				</li>
				
				<li>Cuando del cuadro se selecciona un elemento, el datos_tabla se carga con ese elemento,
				marcando que a partir de aqui las operaciones de ABM se harán sobre este registro. En esta operación
				el registro cargado del datos_tabla funciona como un <strong>cursor</strong> que representa la fila actualmente seleccionada, 
				si no está cargado, no hay selección y viceversa.
					".mostrar_php($cod_seleccion)."
				</li>
			</ul>
			<h3>Manejo del Formulario</h3>
			<ul>
				<li>Cuando el datos_tabla esta cargado, es señal que del cuadro algo se selecciono, entonces
				se dispone a cargar con estos datos. S usa el método get() del datos_tabla porque se sabe de antemano
				que se va a retornar un único registro, si la cantidad puede ser mayor se necesita llamar al método get_filas()
					".mostrar_php($cod_carga_form)."
				<li>Cuando el usuario selecciona una acción sobre el registro cargado en el formulario,
					es necesario indicar la acción al datos_tabla, sincronizarlo con la base de datos
					(ejecutando los comandos SQL) y lo resetea para limpiar la selección:</li>
					".mostrar_php($cod_abm)."
			</ul>
		";
		$vinculo = toba::vinculador()->get_url(null, 2654, array(), array('celda_memoria'=>'ejemplo'));
		echo "<p style='font-size:150%;text-align:center;'>
				<a target='_blank' href='$vinculo'>Ejecutar Operación</a></p>";				
	}
}

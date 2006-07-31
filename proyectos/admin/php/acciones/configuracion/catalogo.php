<?
//--------------------------------------------------------------------------------------
//------------------------------------<  ELEMENTOS  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >Configuracion del proyecto</td>
</tr>
</table>
<script language='javascript'>
	editor='apex';
</script>
<table width="100%" class='lista-obj'>
<? 	
	$rs = array (
				  0 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '/admin/proyectos/propiedades',
				    'nombre' => 'Parametros Basicos',
				    'descripcion' => 'Editor de proyectos',
				    'imagen' => 'proyecto.gif',
				  ),
				  1 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '3287',
				    'nombre' => 'Param. Previsualizacion',
				    'descripcion' => NULL,
				    'imagen' => 'eventos_ruteo.gif',
				  ),
				  2 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '/admin/apex/elementos/pagina_tipo',
				    'nombre' => 'Tipo de PAGINA',
				    'descripcion' => NULL,
				    'imagen' => 'tipo_pagina.gif',
				  ),
				  3 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '/admin/apex/elementos/zona',
				    'nombre' => 'ZONA',
				    'descripcion' => NULL,
				    'imagen' => 'zona.gif',
				  ),
				  4 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '/admin/apex/elementos/error',
				    'nombre' => 'MENSAJES',
				    'descripcion' => 'Errores genericos del proyecto',
				    'imagen' => 'mensaje.gif',
				  ),
				  5 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '1000020',
				    'nombre' => 'Elementos de Formulario (efs)',
				    'descripcion' => NULL,
				    'imagen' => 'objetos/abms_ef.gif',
				  ) /*,
				  6 => 
				  array (
				    'proyecto' => 'admin',
				    'item' => '/admin/apex/elementos/vinculos_generales',
				    'nombre' => 'Vinculos GLOBALES',
				    'descripcion' => NULL,
				    'imagen' => 'vinculos.gif',
				  )*/
				);
	foreach( $rs as $registro ) {
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo toba::get_vinculador()->generar_solicitud($registro["proyecto"],$registro["item"]) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl($registro["imagen"]) ?>" alt="<? echo trim($registro["descripcion"])?>" border="0">
			</a>
	  	  </td>
          <td width="98%" class='lista-obj-dato1'>&nbsp;<? echo $registro["nombre"] ?></td>
        </tr>
<?
	}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  fuentes  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >FUENTES de DATOS</td>
 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/datos/fuente",null,false,false,null,true)?>" class="list-obj" target="<? echo  apex_frame_centro ?>">
	<? echo recurso::imagen_apl("fuente_nueva.gif",true) ?>
	</a>
</td>
</tr>
</table>
<script language='javascript'>
	editor='datos';
</script>
<? 	
	$sql = "	
			SELECT 	f.proyecto as					fuente_proyecto,
					f.fuente_datos as				fuente,
					f.base as						base,
					f.proyecto as					proyecto,
					f.host as						host,
					f.instancia_id as				instancia_id
			FROM 	apex_fuente_datos f
			WHERE   f.proyecto = '".editor::get_proyecto_cargado()."'			
			ORDER BY f.fuente_datos;";
	$rs = toba::get_db()->consultar($sql);
?>
<table width="100%" class='lista-obj'>
<?
	
	foreach( $rs as $registro ) {
?>
        <tr> 
          <td  class='cat-item-botones2'>
		 	<a href="<? echo toba::get_vinculador()->generar_solicitud('admin',"/admin/datos/fuente",array( apex_hilo_qs_zona => $registro["fuente_proyecto"] .apex_qs_separador. $registro["fuente"]))?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("fuente.gif",true,null,null,"host/dsn: " . $registro["host"]) ?>
			</a>
		  </td>
          <td  class='lista-obj-dato1' width="100%"><? echo $registro["fuente"] ?></td>
          <td  class='lista-obj-dato1'><? echo $registro["instancia_id"] ?></td>
        </tr>
<?
	}
?>
</table>
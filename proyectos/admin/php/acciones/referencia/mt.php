<?

	$clase =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto');
	$vinculo = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase );

	$clase_mt =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_mt');
	$vinculo_mt = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase_mt );

	$clase_mt_abms =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_mt_abms');
	$vinculo_mt_abms = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase_mt_abms );

	$clase_mt_mds =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_mt_mds');
	$vinculo_mt_mds = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase_mt_mds );
															
	$clase_ut =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_ut');
	$vinculo_ut = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase_ut );
															
	$clase_ut_formulario =  array(apex_hilo_qs_zona=> 'toba'.apex_qs_separador.'objeto_ut_formulario');
	$vinculo_ut_formulario = $this->vinculador->obtener_vinculo_a_item('admin',
															"/admin/apex/clase_autodoc",
															$clase_ut_formulario );
															
?>
<map name="mapa">
  <area shape="rect" coords="304,2,397,38"  href="<? echo $vinculo ?>"  alt="objeto">
  <area shape="rect" coords="113,76,228,114" href="<? echo $vinculo_mt ?>" alt="objeto_mt">
  <area shape="rect" coords="199,169,333,208" href="<? echo $vinculo_mt_abms ?>" alt="objeto_mt_abms">
  <area shape="rect" coords="2,170,133,210" href="<? echo $vinculo_mt_mds ?>" alt="objeto_mt_mds">
  <area shape="rect" coords="473,76,568,115" href="<? echo $vinculo_ut ?>" alt="objeto_ut">
  <area shape="rect" coords="376,172,512,208" href="<? echo $vinculo_ut_formulario ?>" alt="objeto_ut_formulario">
</map>
<?php
    echo ei_centrar(recurso::imagen_apl('diapositivas/mt-ut.png',true,null,null,null,"#mapa"));
?>
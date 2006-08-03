<? 
	$proyecto = toba::get_hilo()->obtener_proyecto_descripcion();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><? echo $proyecto ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<frameset rows="*" cols="380,*" frameborder="YES" border="1" bordercolor="#553DA1" framespacing="0" id='frameset_admin'>
  <frameset rows="59,*" frameborder="YES" border="1"  bordercolor="#553DA1" framespacing="0">
    <frame src="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),'/admin/menu_principal')?>" name="<? echo  apex_frame_control ?>" scrolling="NO">
    <frame src="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),'/admin/items/catalogo_unificado',null,false,false,null,true,'lateral')?>" name="<? echo  apex_frame_lista ?>" scrolling="auto">
  </frameset>
  <frame src="<? echo toba::get_vinculador()->generar_solicitud(editor::get_id(),'/inicio')?>" name="<? echo  apex_frame_centro ?>" scrolling="auto">
</frameset>

</html>

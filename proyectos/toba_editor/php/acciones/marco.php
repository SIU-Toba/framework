<?php
		$egg = date("d-m") == '28-12';
		if ($egg && ! isset($_SESSION['egg_28_diciembre'])) {
			echo "<style>body {background-color:black; color: white; font-familiy:arial; text-align:center}</style>";
			echo "<h1 style='text-align:center'>Toba</h1>";
			echo toba_recurso::imagen_proyecto('reflexion/pwned.jpg', true);
			echo "by <a href='#' onclick='getElementById(\"div\").style.display=\"\"'>chackal</a>";
			$img = toba_recurso::imagen_proyecto('reflexion/haha.jpg', true);
			echo "<div id='div' style='display:none'><h1>Que la inocencia te sea productiva!!</h1>$img</div>";
			$_SESSION['egg_28_diciembre'] = true;
			die;
		}		

	$proyecto = toba::proyecto()->get_parametro('descripcion');
	$ico = toba_recurso::imagen_proyecto('favicon.ico');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo $proyecto ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="icon" href="<?php echo $ico;?>" type="image/x-icon" /><link rel="shortcut icon" href="<?php echo $ico;?>" type="image/x-icon" />
</head>

<frameset rows="*" cols="380,*" frameborder="YES" border="1" bordercolor="#553DA1" framespacing="0" id='frameset_admin'>
  <frameset rows="59,*" frameborder="YES" border="1"  bordercolor="#553DA1" framespacing="0">
    <frame src="<?php echo toba::vinculador()->get_url(toba_editor::get_id(),1000241)?>" name="<?php echo  apex_frame_control ?>" scrolling="NO">
    <frame src="<?php echo toba::vinculador()->get_url(toba_editor::get_id(),1000239, array(), array('menu' => true, 'celda_memoria' => 'lateral')); ?>" name="<?php echo  apex_frame_lista ?>" scrolling="auto">
  </frameset>
  <frame src="<?php echo toba::vinculador()->get_url(toba_editor::get_id(),1000265)?>" name="<?php echo  apex_frame_centro ?>" scrolling="auto">
</frameset>

</html>

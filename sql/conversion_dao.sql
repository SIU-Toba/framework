UPDATE apex_objeto_ut_formulario_ef
SET inicializacion = inicializacion || '\nclave: id;\nvalor: nombre;\n'
WHERE elemento_formulario = 'ef_combo_dao';

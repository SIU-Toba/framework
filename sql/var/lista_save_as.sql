
INSERT INTO apex_objeto_lista (
  objeto_lista_proyecto   ,
  objeto_lista            ,
  titulo                  ,
  subtitulo               ,
  sql                     ,
  col_ver                 ,
  col_titulos             ,
  col_formato             ,
  ancho                   ,
  ordenar                 ,
  exportar                ,
  vinculo_clave           ,
  vinculo_id_get          ,
  objeto_abms_proyecto    ,
  objeto_abms             ,
  auto_vinculo            ,
  auto_vinculo_etiqueta   
)
SELECT
  'toba'   ,
  '221'            ,
  titulo                  ,
  subtitulo               ,
  sql                     ,
  col_ver                 ,
  col_titulos             ,
  col_formato             ,
  ancho                   ,
  ordenar                 ,
  exportar                ,
  vinculo_clave           ,
  vinculo_id_get          ,
  objeto_abms_proyecto    ,
  objeto_abms             ,
  auto_vinculo            ,
  auto_vinculo_etiqueta   
FROM apex_objeto_lista
WHERE    objeto_lista = 155 AND objeto_lista_proyecto = 'toba';

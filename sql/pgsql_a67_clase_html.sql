--**************************************************************************************************
--**************************************************************************************************
--******************************************   html   ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_html
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_html
--: dump_where: ( objeto_html_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_html_proyecto   		varchar(15)		NOT NULL,
   objeto_html            		int4			NOT NULL,
   html		               		varchar			NULL,
   CONSTRAINT  "apex_objeto_html_pk" PRIMARY KEY ("objeto_html_proyecto","objeto_html"),
   CONSTRAINT  "apex_objeto_html_fk_objeto"  FOREIGN KEY ("objeto_html_proyecto","objeto_html") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
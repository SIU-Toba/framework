CREATE OR REPLACE FUNCTION sp_old_pwd_copy()
  RETURNS trigger AS
$BODY$
				DECLARE
				BEGIN
					IF (TG_OP = 'INSERT') OR (TG_OP = 'DELETE') THEN
						RAISE EXCEPTION 'Error en la programación del trigger';
					END IF;

					IF (OLD.clave != NEW.clave) OR (OLD.autentificacion != NEW.autentificacion) THEN
						INSERT INTO apex_usuario_pwd_usados (usuario, clave, algoritmo) VALUES (OLD.usuario, OLD.clave, OLD.autentificacion);
					END IF;
					RETURN NULL;
				END;
			$BODY$
  LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER tusuario_pwd_pasados
  AFTER UPDATE
  ON apex_usuario
  FOR EACH ROW
  EXECUTE PROCEDURE sp_old_pwd_copy();

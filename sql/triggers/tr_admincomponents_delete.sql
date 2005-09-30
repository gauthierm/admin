CREATE OR REPLACE FUNCTION "public"."tr_admincomponents_delete" () RETURNS trigger AS ' 
    BEGIN
       
	   	delete from adminsubcomponents where component = OLD.id;
		delete from admincomponent_admingroup where component = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON admincomponents
    FOR EACH ROW EXECUTE PROCEDURE tr_admincomponents_delete();

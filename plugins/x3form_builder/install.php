<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// X3form_builder module installer

$version = '0.9';
$compatibility = '0.9.0 STABLE';
$mod_name = 'x3form_builder';
$required = array();
$sql0 = $sql1 = array();

// table
$sql0[] = 'CREATE TABLE IF NOT EXISTS x3_forms (
			id int(11) NOT NULL AUTO_INCREMENT,
			updated datetime NOT NULL,
			id_area int(11) NOT NULL,
			lang char(2) NOT NULL,
			name varchar(255) NOT NULL,
			title varchar(255) NOT NULL,
			description text NOT NULL,
			mailto varchar(255) NOT NULL,
			msg_ok varchar(255) NOT NULL,
			msg_failed varchar(255) NOT NULL,
			submit_button varchar(255) NOT NULL,
			reset_button varchar(255) NOT NULL,
			xlock tinyint(1) NOT NULL,
			xon tinyint(1) NOT NULL,
			PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8';

$sql0[] = 'CREATE TABLE IF NOT EXISTS x3_forms_fields (
			id int(11) NOT NULL AUTO_INCREMENT,
			updated datetime NOT NULL,
			id_area int(11) NOT NULL,
			lang char(2) NOT NULL,
			id_form int(11) NOT NULL,
			label varchar(255) NOT NULL,
			xtype varchar(32) NOT NULL,
			name varchar(255) NOT NULL,
			suggestion varchar(255) NOT NULL,
			value varchar(255) NOT NULL,
			rule varchar(255) NOT NULL,
			extra varchar(255) NOT NULL,
			xpos smallint(5) NOT NULL,
			xlock tinyint(1) NOT NULL,
			xon tinyint(1) NOT NULL,
			PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8';

$sql0[] = 'CREATE TABLE IF NOT EXISTS x3_forms_results (
			id int(11) NOT NULL AUTO_INCREMENT,
			updated datetime NOT NULL,
			id_area int(11) NOT NULL,
			lang char(2) NOT NULL,
			id_form int(11) NOT NULL,
			result text NOT NULL,
			xlock tinyint(1) NOT NULL,
			xon tinyint(1) NOT NULL,
			PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8';

$sql0[] = 'CREATE TABLE IF NOT EXISTS x3_forms_blacklist (
            id int(11) NOT NULL AUTO_INCREMENT,
            updated datetime NOT NULL,
            id_area int(11) NOT NULL,
            lang char(2) NOT NULL,
            name varchar(255) Not NULL,
            xlock tinyint(1) NOT NULL,
            xon tinyint(1) NOT NULL,
            PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8';

// PRIVTYPES
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_x3form_creation', '_X3FORM_CREATION', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_x3form_field_creation', '_X3FORM_FIELD_CREATION', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_x3form_blacklist_creation', '_X3FORM_BLACKLIST_CREATION', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x3_forms', 'X3_FORMS', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x3_forms_fields', 'X3_FORMS_FIELDS', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x3_forms_results', 'X3_FORMS_RESULTS', 1)";
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x3_forms_blacklist', 'X3_FORMS_BLACKLIST', 1)";
// GPRIVS
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_x3form_creation', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_x3form_field_creation', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_x3form_blacklist_creation', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x3_forms', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x3_forms_fields', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x3_forms_results', 4, 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x3_forms_blacklist', 4, 1)";

// DICTIONARY PRIVS
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', '_X3FORM_CREATION', 'Creazione nuovi form', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', '_X3FORM_FIELD_CREATION', 'Creazione nuovi campi di form', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', '_X3FORM_BLACKLIST_CREATION', 'Creazione nuovo elemento di blacklist', 1)";


$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X3_FORMS', 'Gestione form', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X3_FORMS_FIELDS', 'Gestione campi di form', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X3_FORMS_RESULTS', 'Gestione risultati form', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X3_FORMS_BLACKLIST', 'Gestione blacklist', 1)";
// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', '_X3FORM_CREATION', 'Form creation', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', '_X3FORM_FIELD_CREATION', 'Form field creation', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', '_X3FORM_BLACKLIST_CREATION', 'Blacklist item creation', 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X3_FORMS', 'Form manager', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X3_FORMS_FIELDS', 'Form field manager', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X3_FORMS_RESULTS', 'Form results manager', 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X3_FORMS_BLACKLIST', 'Blacklist manager', 1)";

// ADM
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'it', 1, 'base', 'base', 'x3_form_builder', 'modules', 'x3form-builder/mod', 'Gestione form', 'Gestione form', 'Gestione form', 2, 'A00310050000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'it', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/fields', 'Campi form', 'Campi form', 'Campi form', 3, 'A003100500000000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'it', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/results', 'Risultati form', 'Risultati form', 'Risultati form', 3, 'A003100500000000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'it', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/blacklist', 'Blacklist', 'Blacklist', 'Blacklist', 3, 'A003100500000000', 1, 1)";
// en
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'en', 1, 'base', 'base', 'x3_form_builder', 'modules', 'x3form-builder/mod', 'Form builder', 'Form builder', 'Form builder', 2, 'A00310050000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'en', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/fields', 'Form fields', 'Form fields', 'Form fields', 3, 'A003100500000000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'en', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/results', 'Form results', 'Form results', 'Form results', 3, 'A003100500000000', 1, 1)";
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'en', 1, 'base', 'base', 'x3_form_builder', 'x3form-builder/mod', 'x3form-builder/blacklist', 'Blacklist', 'Blacklist', 'Blacklist', 3, 'A003100500000000', 1, 1)";

// DICTIONARY

// it
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_MANAGE', 'Gestione form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FORMS', 'Form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_AREA', 'Area', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_LANG', 'Lingua', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TITLE', 'Titolo form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TITLE_SUGGESTION', 'Sar&agrave; visibile all\'utente', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FORM_NAME', 'Nome form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FORM_NAME_SUGGESTION', 'Solo per uso interno, ogni form deve avere un nome diverso', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_MAILTO', 'Email a cui inviare risultato form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_MSG_OK', 'Messaggio per gestione form senza errori', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_MSG_FAILED', 'Messaggio per gestione form fallita', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SUBMIT_BUTTON', 'Testo sul pulsante submit', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RESET_BUTTON', 'Testo sul pulsante reset', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_NEW_FORM', 'Nuovo form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_DUPLICATE_FORM', 'Duplica form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_EDIT_FORM', 'Modifica form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_DELETE_FORM', 'Elimina form', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FIELDS', 'Campi del form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_DETAIL_MSG', 'Drag and drop per cambiare l\'ordine degli elementi', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_NEW_FIELD', 'Nuovo campo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_EDIT_FIELD', 'Modifica campo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_DELETE_FIELD', 'Elimina campo', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_LABEL', 'Etichetta', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_XTYPE', 'Tipo di campo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HTML', 'Codice HTML', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HIDDEN', 'Campo nascosto', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TEXT', 'Campo di testo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TEXTAREA', 'Testo lungo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SELECT', 'Selezione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_CHECK', 'Casella di spunta', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RADIO', 'Pulsanti radio', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SINGLERADIO', 'Pulsante radio singolo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FILE', 'File', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FIELDSET', 'Fieldset', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_CAPTCHA', 'Captcha', 0, 1)";


$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HTML_SUGGESTION', 'Utile per inserire elementi in HTML nel form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HIDDEN_SUGGESTION', 'Pu&ograve; contenere un parametro informativo a piacere o un valore relativo alla pagina (id_area, url, lang)', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TEXT_SUGGESTION', 'Un campo di testo di lunghezza massima 255 caratteri', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TEXTAREA_SUGGESTION', 'Un campo di testo di lunghezza massima 8000 caratteri', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SELECT_SUGGESTION', 'Un elenco di opzioni tra cui scegliere un unico valore', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_CHECK_SUGGESTION', 'Una voce da spuntare o meno', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RADIO_SUGGESTION', 'Una voce da spuntare tra una serie', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SINGLERADIO_SUGGESTION', 'Un pulsante radio singolo da usare per costruire form complessi', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FILE_SUGGESTION', 'Un file da caricare', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FIELDSET_SUGGESTION', 'Un contenitore di elementi del form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RECAPTCHA_SUGGESTION', 'Pulsante Google CAPTCHA', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_NAME', 'Nome del campo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SUGGESTION', 'Suggerimento', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_VALUE', 'Valore di default', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RULE', 'Condizioni per il campo', 0, 1)";


$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RULE_NAME', 'Condizione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_FIELD_PARAM', 'Campo collegato', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_VALUE_PARAM', 'Parametro', 0, 1)";


$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_EXTRA', 'Opzioni extra', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_LABEL_SUGGESTION', 'Etichetta descrittiva del campo', 0, 1)";
//$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_XTYPE_SUGGESTION', 'Scegliere il tipo di campo pi&ugrave; adatto all\'informazione da raccogliere', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_NAME_SUGGESTION', 'Il nome del campo, ad uso interno, usare nomi brevi ed esplicativi', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_SUGGESTION_SUGGESTION', 'Informazione aggiuntiva per la corretta compilazione del form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_VALUE_SUGGESTION', 'Valore suggerito', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RULE_SUGGESTION', 'Regole che devono essere soddisfatte dal campo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_EXTRA_SUGGESTION', 'Per la formattazione del campo (esempio per valori numerici class=\"aright\")', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RESULTS', 'Risultati form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_EXPORT', 'Esporta risultati', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_DELETE_RESULTS', 'Elimina risultato form', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_MANAGE', 'Gestione blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEMS', 'Elementi blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEM', 'Elemento blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEM_MSG', 'inserire un elemento per riga per inserimenti multipli', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ADD', 'Aggiungi un elemento alla blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_NEW', 'Nuovo elemento blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_EDIT', 'Modifica elemento blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_DELETE', 'Elimina elemento blacklist', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_BLACKITEMS_ALREADY_EXISTS', 'Elementi che si cerca di inserire sono già presenti', 0, 1)";


// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_MANAGE', 'Form builder', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FORMS', 'Forms', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_AREA', 'Area', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_LANG', 'Language', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TITLE', 'Form title', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TITLE_SUGGESTION', 'Will be visible to the users', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FORM_NAME', 'Form name', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FORM_NAME_SUGGESTION', 'For internal use, each form must have a different name', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_MAILTO', 'Email address to send form result', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_MSG_OK', 'Message if all goes well', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_MSG_FAILED', 'Message if error occurs', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SUBMIT_BUTTON', 'Submit button text', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RESET_BUTTON', 'Reset button text', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_NEW_FORM', 'New form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_DUPLICATE_FORM', 'Duplicate form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_EDIT_FORM', 'Edit form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_DELETE_FORM', 'Delete form', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FIELDS', 'Form fields', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_DETAIL_MSG', 'Drag and drop to change the order', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_NEW_FIELD', 'New field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_EDIT_FIELD', 'Edit field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_DELETE_FIELD', 'Delete field', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_LABEL', 'Label', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_XTYPE', 'Field type', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HTML', 'HTML field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HIDDEN', 'Hidden field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TEXT', 'Text field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TEXTAREA', 'Text area', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SELECT', 'Select', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_CHECK', 'Checkbox', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RADIO', 'Radio button', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SINGLERADIO', 'Single radio button', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FILE', 'File', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FIELDSET', 'Fieldset', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_CAPTCHA', 'Captcha', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HTML_SUGGESTION', 'Useful to insert HTML item in the form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HIDDEN_SUGGESTION', 'May contain a parameter or a value information at will on the page (id_area, url, lang)', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TEXT_SUGGESTION', 'A text field of maximum length 255 characters', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TEXTAREA_SUGGESTION', 'A text field of maximum length 8000 characters', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SELECT_SUGGESTION', 'A list of options to choose a single value', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_CHECK_SUGGESTION', 'Voice to be ticked or not', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RADIO_SUGGESTION', 'A voice from peering through a series', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SINGLERADIO_SUGGESTION', 'A single radio button to build complex forms', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FILE_SUGGESTION', 'A file to upload', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FIELDSET_SUGGESTION', 'A field container', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RECAPTCHA_SUGGESTION', 'Google CAPTCHA button', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_NAME', 'Field name', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SUGGESTION', 'Suggestion', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_VALUE', 'Default value', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RULE', 'Conditions for the field', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RULE_NAME', 'Rule', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_FIELD_PARAM', 'Related field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_VALUE_PARAM', 'Parameter', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_EXTRA', 'Extra options', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_LABEL_SUGGESTION', 'Descriptive label of the field', 0, 1)";
//$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_XTYPE_SUGGESTION', 'Choose the type of field more suited to information gathering', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_NAME_SUGGESTION', 'The name of the field, for internal use, use short names and explanatory', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_SUGGESTION_SUGGESTION', 'Additional information for the correct application form', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_VALUE_SUGGESTION', 'Suggested value', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RULE_SUGGESTION', 'Rules that must be met by the field', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_EXTRA_SUGGESTION', 'Formatting of the field (eg for numerical values class=\"aright\")', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RESULTS', 'Form results', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_EXPORT', 'Export results', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_DELETE_RESULTS', 'Delete form result', 0, 1)";


$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_MANAGE', 'Blacklist manager', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEMS', 'Blacklist items', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEM', 'Blacklist item', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ITEM_MSG', 'enter one item per row for multiple entries', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_ADD', 'Add a new blacklist item', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_NEW', 'New blacklist item', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_EDIT', 'Edit blacklist item', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKLIST_DELETE', 'Delete blacklist item', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_BLACKITEMS_ALREADY_EXISTS', 'Items that you try to insert are already present', 0, 1)";

// helpers

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP', '<div id=\"help\">Scegliere il tipo di campo pi&ugrave; adatto all\'informazione da raccogliere per visualizzare l\'aiuto.</div>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_HTML', '<strong>Istruzioni per la compilazione del campo HTML</strong>:<br /><ul><li><strong>Etichetta</strong>: lasciare vuoto</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: lasciare vuoto</li><li><strong>Valore di default</strong>: testo che intendete visualizzare</li><li><strong>Condizioni per il campo</strong>: lasciare vuoto</li><li><strong>Opzioni Extra</strong>: lasciare vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_HIDDEN', '<strong>Istruzioni per la compilazione del CAMPO NASCOSTO</strong>:<br /><ul><li><strong>Etichetta</strong>: lasciare vuoto</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: lasciare vuoto</li><li><strong>Valore di default</strong>: valore da associare al campo nascosto</li><li><strong>Condizioni per il campo</strong>: lasciare vuoto</li><li><strong>Opzioni Extra</strong>: lasciare vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_TEXT', '<strong>Istruzioni per la compilazione del CAMPO TESTO</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: lasciate in bianco o inserite il valore atteso</li><li><strong>Condizioni per il campo</strong>: se lo ritenete opportuno inserite una combinazione dei temini che seguono separati da | (pipe)<ul><li>required: rende il campo obbligatorio, se lo usate inserite per primo</li><li>mail: verifica che sia inserito un indirizzo email valido</li><li>url: verifica che sia inserito un URL valido</li><li>phone: verifica che sia inserita una sequenza di numeri</li><li>minlength: (da usare cos&igrave; minlength-5) verifica che il testo inserito abbia una lunghezza minima pari al numero dopo il segno meno</li><li>maxlength: (da usare cos&igrave; maxlength-13) verifica che il testo inserito abbia una lunghezza massima pari al numero dopo il segno meno</li><li>alphanumeric: accetta solo lettere e numeri</li><li>integer: accetta solo numeri</li>date: accetta solo date valide</li><li>time: accetta solo orari nel formato ore:minuti</li><li>datetime: accetta solo data e ora nel formato aaaa-mm-gg hh:mm:ss</li><li>depends: (da usare cos&igrave; depends-nome_altro_campo) verifica che altro campo indicato non sia vuoto</li></ul></li><li><strong>Opzioni Extra</strong>: potete lasciare vuoto oppure <ul><li>impostare allineamento a destra cos&igrave;: class=\"aright\"</li><li>impostare campo largo cos&igrave;: class=\"large\"</li></ul></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_TEXTAREA', '<strong>Istruzioni per la compilazione del CAMPO AREA DI TESTO</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: lasciate in bianco o inserite il valore atteso</li><li><strong>Condizioni per il campo</strong>: usualmente per i campi AREA DI TESTO si imposta required se la compilazione del campo &egrave; obbligatoria, altrimenti si lascia vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_SELECT', '<strong>Istruzioni per la compilazione del CAMPO SELECT</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: inserite le opzioni della select separate da | (pipe).<br />Per lasciare la prima opzione vuota mettete un | all\'inizio della lista delle opzioni.</li><li><strong>Condizioni per il campo</strong>: di solito non è necessario, se volete rendere obbligatoria la selezione non offrite l\'opzione vuota.</li><li><strong>Opzioni Extra</strong>: per dare la possibilit&agrave, di selezionare pi&ugrave; di una opzione scrivete <strong>multiple</strong></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_CHECKBOX', '<strong>Istruzioni per la compilazione del CAMPO CHECKBOX</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: lasciare vuoto</li><li><strong>Condizioni per il campo</strong>: se volete rendere obbligatoria la selezione (esempio condizioni per la privacy) scrivete <strong>required</strong></li><li><strong>Opzioni Extra</strong>: lasciare vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_RADIO', '<strong>Istruzioni per la compilazione del CAMPO RADIO BUTTON</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: inserite le opzioni dei pulsanti radio separate da | (pipe)</li><li><strong>Condizioni per il campo</strong>: se volete rendere obbligatoria la selezione di una opzione scrivete <strong>required</strong></li><li><strong>Opzioni Extra</strong>: lasciare vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_SINGLERADIO', '<strong>Istruzioni per la compilazione del CAMPO SINGLE RADIO BUTTON</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: inserite il name (il valore comune a tutti i single radio da collegare tra loro)</li><li><strong>Condizioni per il campo</strong>: se volete rendere obbligatoria la selezione di una opzione scrivete <strong>required</strong></li><li><strong>Opzioni Extra</strong>: lasciare vuoto</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_FILE', '<strong>Istruzioni per la compilazione del CAMPO FILE</strong>:<br /><ul><li><strong>Etichetta</strong>: inserire breve descrizione del campo</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi</li><li><strong>Suggerimento</strong>: se lo ritenete necessario, inserite un suggerimento per l\'utente</li><li><strong>Valore di default</strong>: lasciate in bianco</li><li><strong>Condizioni per il campo</strong>: se volete rendere obbligatorio il caricamento di un file scrivete <strong>required</strong></li><li><strong>Opzioni Extra</strong>: lasciare vuoto </li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_FIELDSET', '<strong>Istruzioni per la compilazione del CAMPO FIELDSET</strong>:<br /><p>I campi FIELDSET vanno usati in coppia, il primo apre il contenitore e il secondo lo chiude.<br />Attraverso il foglio di stile &egrave; possibile impostare le regole relative alla visualizzazione dei diversi fieldset presenti nel form.</p><ul><li><strong>Etichetta</strong>: lasciare vuoto</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi.<br /><strong>NOTA BENE</strong>il nome campo deve essere lo stesso nei due campi filedset, quello di apertura e quello di chiusura</li><li><strong>Suggerimento</strong>: lasciare in bianco</li><li><strong>Valore di default</strong>: inserite <strong>open</strong>per il fieldset di apertura e <strong>close</strong> nel fieldset di chiusura</li><li><strong>Condizioni per il campo</strong>: lasciare in bianco</li><li><strong>Opzioni Extra</strong>: se volete potete inserire <strong>class=\"class_previsto_dal_css\"</strong></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_HELP_RECAPTCHA', '<strong>Instruzioni per la compilazione del CAMPO CAPTCHA</strong>:<br /><ul><li><strong>Etichetta</strong>: lasciare vuoto</li><li><strong>Nome campo</strong>: obbligatorio, usate un termine a piacere non usato per altri campi.</li><li><strong>Suggerimento</strong>: lasciare vuoto.</li></ul>', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_RELOAD', 'Ricarica', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_LATEST', 'Ultimi moduli inviati', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3form_builder', '_X3FB_TO_CHECK', 'Da controllare', 0, 1)";

// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP', '<div id=\"help\">Choose the type of field more suited to information gathering to see the help message.</div>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_HTML', '<strong>How to complete the HTML FIELD</strong>:<br /><ul><li><strong>Label</strong>: leave blank</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: leave blank</li><li><strong>Default value</strong>: text that you want to display</li><li><strong>Conditions for the field</strong>: leave blank</li><li><strong>Optional Extras</strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_HIDDEN', '<strong>Instructions to complete the HIDDEN FIELD</strong>:<br /><ul><li><strong>Label</strong>: leave blank</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: leave blank</li><li><strong>Default value</strong>: value to associate the hidden field</li><li><strong>Conditions for the field</strong>: leave blank </li><li><strong>Optional Extras</strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_TEXT', '<strong>How to complete the TEXT FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: leave blank or enter the expected value</li><li><strong>Conditions for the field</strong>: if you judge it appropriate to include a combination of term following separated by | (pipe) <ul><li>required: it makes the required field, if you use it put it as first</li><li>mail: verify that it is inserted a valid email address</li><li>url: check to be entered a valid URL</li><li>phone: make sure it is inserted into a sequence of numbers</li><li>minlength: (to be used as minlength-5) verifies that the text has added a minimum length equal to the number after the hyphen</li><li>maxlength: (to be used as maxlength-13) verifies that the inserted text has a maximum length equal to the number after the hyphen</li><li>alphanumeric: accepts only letters and numbers</li><li>integer: accept only numbers</li>date: accepts only valid dates</li><li>time: only accepts times in hours: minutes</li><li>datetime: only accepts date and time in the format yyyy-mm-dd hh:mm:ss</li><li>depends: (to be used as depends-other_filed_name) verify that other field is not empty</li></ul></li><li><strong>Optional Extras</strong>: you can leave it blank or set:<ul><li>right aligned like this: class=\"aright\"</li><li>set off the field like this: class=\"large\"</li></ul></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_TEXTAREA', '<strong>Instructions to complete the TEXTAREA FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you judge it necessary, enter a hint to the user</li><li><strong>Default value</strong>: leave blank or enter the expected value</li><li><strong>Conditions for the field</strong>: usually for fields TEXTAREA set <strong>required</strong> if you want the compilation of the field is mandatory, otherwise leave it blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_SELECT', '<strong>Instructions to complete the SELECT FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: enter the options in the select, separated by | (pipe).<br />to leave empty the first option you put | on top of the list of options.</li><li><strong>Conditions for the field </strong>: it is usually not necessary, if you want to make it obligatory not offer the empty option.</li><li><strong>Optional Extras</strong>: to give the possibility to select more than one option write <strong>multiple</strong></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_CHECKBOX', '<strong>Instructions to complete the CHECKBOX FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: leave blank</li><li><strong>Conditions for the field</strong>: if you want to make mandatory the selection (eg conditions for privacy) write <strong>required</strong></li><li><strong>Optional Extras </strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_RADIO', '<strong>Instructions to complete the RADIO BUTTON FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: enter the radio button options separated by | (pipe)</li><li><strong>Conditions for the field</strong>: if you want to make mandatory selection of an option type <strong>required</strong></li><li><strong>Optional Extras</strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_SINGLERADIO', '<strong>Instructions to complete the SINGLE RADIO BUTTON FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert a short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: enter the name (the value shared with other single radio buttons you want to connect)</li><li><strong>Conditions for the field</strong>: if you want to make mandatory selection of an option type <strong>required</strong></li><li><strong>Optional Extras</strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_FILE', '<strong>Instructions to complete the FILE FIELD</strong>:<br /><ul><li><strong>Label</strong>: insert short description of the field</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields</li><li><strong>Suggestion</strong>: if you feel necessary, enter a hint to the user</li><li><strong>Default value</strong>: leave blank</li><li><strong>Conditions for the field</strong>: if you want to make it compulsory for uploading a file type <strong>required</strong></li><li><strong>Optional Extras</strong>: leave blank</li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_FIELDSET', '<strong>Instructions to complete the FIELDSET FIELD</strong>:<p>FIELDSET fields are used in pairs, the first open the container and the second then closes.<br />Through the style sheet You can set rules concerning the display of different fieldset in the form.</p><ul><li><strong>Label</strong>: leave blank</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields<br /><strong>NOTE</strong> field name must be the same in the two fields filedset, the opening and closing</li><li><strong>Suggestion</strong>: leave blank</li><li><strong>Default value</strong>: Enter <strong>open</strong> for the opening fieldset and <strong>close</strong> for the closing fieldset</li><li><strong>Conditions for the field</strong>: leave blank</li><li><strong>Optional Extras</strong >: if you want you can insert <strong>class=\"class_as_in_css\"</strong></li></ul>', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_HELP_RECAPTCHA', '<strong>Instructions to complete the CAPTCHA FIELD</strong>:<br /><ul><li><strong>Label</strong>: leave empty</li><li><strong>Field Name</strong>: required, please do not use a term used for other fields.</li><li><strong>Suggestion</strong>: leave empty.</li></ul>', 0, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_RELOAD', 'Reload', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_LATEST', 'Latest forms submitted', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3form_builder', '_X3FB_TO_CHECK', 'To check', 0, 1)";


// msg
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'msg', '_X3FB_FORM_ALREADY_EXISTS', 'Esiste gi&agrave; un form con questo nome in questa area e questa lingua', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'msg', '_X3FB_FIELD_ALREADY_EXISTS', 'Esiste gi&agrave; un campo con lo stesso nome in questo form', 0, 1)";

// en

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'msg', '_X3FB_FORM_ALREADY_EXISTS', 'There is already a form with the name in this area and this language', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'msg', '_X3FB_FIELD_ALREADY_EXISTS', 'There is already a field with the same name in this form', 0, 1)";

// AREA DIPENDENT QUERIES


// dictionary
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x3form_builder', '_X3FB_RELOAD_CAPTCHA', 'Cambia l\'immagine se risulta illeggibile', 0, 1)";
// en
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x3form_builder', '_X3FB_RELOAD_CAPTCHA', 'Reload the image if it is not readable', 0, 1)";

// parameters
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'folder', 'Secret folder where store files', 'TEXT', '', 0, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'delete', 'Delete files after sending', '0|1', '0', 0, 0, 1)";

// MODULE
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, widget, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Form builder', 1, 1, 0, 1, 1, '$version', 0)";


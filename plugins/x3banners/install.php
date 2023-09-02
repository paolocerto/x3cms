<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */


// x3banners installer

// plugin version
$version = '0.1';

// compatibility
$compatibility = '0.9.0 STABLE';

// plugin name
$mod_name = 'x3banners';

// requirements
$required = array();

$sql0 = $sql1 = array();

// table
$sql0[] = 'CREATE TABLE IF NOT EXISTS x3_banners (
	id int(11) NOT NULL AUTO_INCREMENT,
	updated datetime NOT NULL,
	id_area int(11) NOT NULL,
	lang char(2) NOT NULL,
	name varchar(255) NOT NULL,
	title varchar(255) NOT NULL,
	description text NOT NULL,
	id_page int(11) NOT NULL,
	start_date datetime NOT NULL,
	end_date datetime NOT NULL,
	bg_color char(7) NOT NULL,
	fg_color char(7) NOT NULL,
	link_color char(7) NOT NULL,
	auto_hide smallint(4) NOT NULL,
	xlock tinyint(1) NOT NULL,
	xon tinyint(1) NOT NULL,
	PRIMARY KEY (id)) ENGINE=INNODB DEFAULT CHARSET=utf8';

// creation priv
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_x3banners_creation', '_X3BANNERS_CREATION', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_x3banners_creation', 4, 1)";

// administration priv
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x3_banners', 'X3_BANNERS', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x3_banners', 4, 1)";

// adm en
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'en', 1, 'base', 'base', 'x3_banners', 'modules', 'x3banners/mod', 'Banner manager', 'Banner manager', 'Banner manager', 2, 'A00310050000', 1, 1)";

// adm it
$sql0[] = "INSERT INTO pages (updated, lang, id_area, tpl, css, xid, xfrom, url, name, title, description, deep, ordinal, xlock, xon) VALUES (NOW(), 'it', 1, 'base', 'base', 'x3_banners', 'modules', 'x3banners/mod', 'Gestione banner', 'Gestione banner', 'Gestione banner', 2, 'A00310050000', 1, 1)";

// dictionary

// en

// admin
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X3_BANNERS', 'Manage Banners', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', '_X3BANNERS_CREATION', 'Create Banner', 0, 1)";

// it
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X3_BANNERS', 'Gestione banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', '_X3BANNERS_CREATION', 'Creazione banner', 0, 1)";

// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_MANAGE', 'Banner manager', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_ITEMS', 'Banners', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_ITEM', 'Banner message', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_EDIT', 'Edit banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_DELETE', 'Delete banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_DELETE_MSG', 'Deletion is irreversible', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_NEW', 'New banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_ADD', 'Add a new banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_ID_PAGE', 'Page', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_START_DATE', 'Start visualization', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_START_DATE_MSG', 'Date and time for the visualization window', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_END_DATE', 'End visualization', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_END_DATE_MSG', 'Date and time for the visualization window', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_SEARCH_MSG', 'search by text in title or description', 0, 1)";


// it
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_MANAGE', 'Gestione banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_ITEMS', 'Banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_ITEM', 'Messaggio', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_EDIT', 'Modifica banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_DELETE', 'Elimina banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_DELETE_MSG', 'Eliminazione è irreversibile', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_NEW', 'Nuovo banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_ADD', 'Aggiungi un nuovo banner', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_ID_PAGE', 'Pagina', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_START_DATE', 'Inizio visualizzazione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_START_DATE_MSG', 'Data e ora finestra di visualizzazione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_END_DATE', 'Fine visualizzazione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_END_DATE_MSG', 'Data e ora finestra di visualizzazione', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_SEARCH_MSG', 'cerca per testo in titolo o descrizione', 0, 1)";


// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_BG_COLOR', 'Background color', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_FG_COLOR', 'Foreground color', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_LINK_COLOR', 'Link color', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_AUTO_HIDE', 'Seconds before auto hide', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x3banners', '_X3BANNERS_AUTO_HIDE_MSG', 'set zero to disable auto hide', 0, 1)";

// it
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_BG_COLOR', 'Colore sfondo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_FG_COLOR', 'Colore testo', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_LINK_COLOR', 'Colore link', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_AUTO_HIDE', 'Secondi prima di chiusura', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x3banners', '_X3BANNERS_AUTO_HIDE_MSG', 'impostate zero per disabilitare chiusura', 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, mappable, widget, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Top banner manager', 0, 1, 0, 0, 0, 1, '$version', 0)";
<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


// x4cookie installer

// plugin version
$version = '0.3';

// compatibility
$compatibility = '0.9.0 STABLE';

// plugin name
$mod_name = 'x4cookie';

// requirements
$required = array();

$sql0 = $sql1 = array();

// administration priv
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_cookie', 'X4_COOKIE', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_cookie', 4, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_COOKIE', 'Gestione cookies', 0, 1)";

// dictionary

// en
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_CONFIG', 'Review your choices about cookies', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_SETUP', 'Cookies setup', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_MESSAGE', 'This site uses technical and third-party cookies.\nBy continuing browsing you implicitly accept the use of cookies.', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_OK', 'It\'s OK', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_MORE_INFO', 'More info', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_SETUP_MSG', 'Cookies are small text files that are stored on the user\'s device when browsing the internet.\nThis website uses:', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_TECHNICAL', 'Technical cookies', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_TECHNICAL_MSG', 'They are mandatory for the correct functioning of the site', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_THIRDY', 'Third party cookies', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_THIRDY_MSG', 'To allow the use of social networks and other multimedia applications.\nThose cookies could also be profiling cookies', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_PROFILE', 'Profiling cookies', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4cookie', '_X4COOKIE_PROFILE_MSG', 'To collect information (anonymously or otherwise) in order to create a user profile that allows us to offer a personalized experience: only show ads on products or articles that interest you or send targeted communications', 0, 1)";

// it
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_CONFIG', 'Rivedi le tue scelte sui cookie', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_SETUP', 'Impostazione cookie', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_MESSAGE', 'Questo sito fa uso di cookies tecnici e di terze parti.\nContinuando la navigazione si accetta implicitamente l\'uso dei cookie', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_OK', 'Va bene', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_MORE_INFO', 'Voglio saperne di più', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_SETUP_MSG', 'I cookie sono piccoli file di testo che vengono memorizzati sul dispositivo dell\'utente quando naviga su internet.\nQuesto sito fa uso di:', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_TECHNICAL', 'Cookie tecnici e cookie funzionali', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_TECHNICAL_MSG', 'Sono necessari per il corretto funzionamento del sito e usati analisi statistiche con dati anonimi e aggregati', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_THIRDY', 'Cookie di terze parti', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_THIRDY_MSG', 'Per consentire l’utilizzo dei social network e di altre applicazioni multimediali.\nQuesti cookie potrebbero anche essre cookie di profilazione', 0, 1)";

$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_PROFILE', 'Cookie di profilazione', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4cookie', '_X4COOKIE_PROFILE_MSG', 'Per raccogliere informazioni (in modo anonimo e non) in modo da creare un profilo dell\'utente che permetta di offrire una esperienza personalizzata: mostrare solo annunci su prodotti o articoli che vi interessano o inviare comunicazioni mirate', 0, 1)";


// param
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'url', 'Cookie info URL', 'TEXT', '', 0, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'third_party_cookies', 'Third party cookies used on the website', 'BOOLEAN', '1', 0, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'profiling_cookies', 'Profiling cookies used on the website', 'BOOLEAN', '0', 0, 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, mappable, widget, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Cookie', 1, 0, 0, 0, 0, 0, '$version', 0)";
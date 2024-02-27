--
-- Table structure for table `alang`
--

CREATE TABLE IF NOT EXISTS `alang` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `language` varchar(50) NOT NULL,
  `code` char(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `xdefault` tinyint(1) NOT NULL,
  `rtl` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `alang`
--

INSERT INTO `alang` (`updated`, `id_area`, `language`, `code`, `title`, `description`, `keywords`, `xdefault`, `rtl`, `xlock`, `xon`) VALUES
(NOW(), 1, 'english', 'en', 'X3 CMS Admin area', 'X3 CMS Administration area', 'x3 cms, x4 webapp, cblu.net', 1, 0, 0, 1),
(NOW(), 1, 'italiano', 'it', 'X3 CMS Area di amministrazione', 'X3 CMS Area di amministrazione', 'x3 cms, x4 webapp, cblu.net', 0, 0, 0, 1),
(NOW(), 2, 'english', 'en', 'Site title', 'Site description', 'some keywords', 1, 0, 0, 1),
(NOW(), 2, 'italiano', 'it', 'Titolo sito', 'X3 CMS - Descrizione sito', 'keywords', 0, 0, 0, 1),
(NOW(), 3, 'english', 'en', 'Private area title', 'Private area description', 'some keywords', 1, 0, 0, 1),
(NOW(), 3, 'italiano', 'it', 'Titolo area privata', 'X3 CMS - Descrizione area privata', 'keywords', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `aprivs`
--

CREATE TABLE IF NOT EXISTS `aprivs` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `area` varchar(128) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `aprivs`
--

INSERT INTO `aprivs` (`updated`, `id_user`, `id_area`, `area`, `xon`) VALUES
(NOW(), 1, 1, 'admin', 1),
(NOW(), 1, 3, 'private', 1),
(NOW(), 1, 2, 'public', 1);

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `lang` char(2) NOT NULL,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `folder` varchar(128) NOT NULL,
  `id_site` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `xdefault` tinyint(1) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `updated`, `lang`, `name`, `title`, `description`, `folder`, `id_theme`, `xdefault`, `private`,`xlock`, `xon`) VALUES
(1, NOW(), 'it', 'admin', 'Administration', 'Control panel', 'admin', 1, 1, 0, 1, 0, 1),
(2, NOW(), 'it', 'public', 'Public area', 'Web site', 'public', 1, 2, 1, 0, 0, 1),
(3, NOW(), 'it', 'private', 'Private area', 'Web private area', 'private', 1, 2, 0, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `bid` char(32) NOT NULL,
  `id_area` int(11) NOT NULL,
  `lang` char(2) NOT NULL,
  `code_context` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_page` int(11) NOT NULL,
  `id_editor` int(11) NOT NULL,
  `date_in` int(11) NOT NULL,
  `date_out` int(11) NOT NULL,
  `xkeys` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `tags` text NOT NULL,
  `content` mediumtext NOT NULL,
  `ftext` text NOT NULL,
  `js` text NOT NULL,
  `excerpt` tinyint(1) NOT NULL,
  `author` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `param` varchar(255) NOT NULL,


  `show_author` tinyint(1) NOT NULL,
  `show_date` tinyint(1) NOT NULL,
  `show_tags` tinyint(1) NOT NULL,
  `show_actions` tinyint(1) NOT NULL,
  `xschema` TEXT NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`updated`, `bid`, `id_area`, `lang`, `code_context`, `name`, `id_page`, `id_editor`, `date_in`, `date_out`, `content`, `module`, `param`, `xlock`, `xon`) VALUES
(NOW(), '3bdda9ed1e2a332550bf2783e4a3a70b', 2, 'it', 1, 'benvenuti', 1, 1, UNIX_TIMESTAMP(), 0, '<h1>Benvenuti nel CMS X3</h1><p>Se visualizzate questa pagina il vostro Content Management System X3 &egrave; stato correttamente installato e configurato.<br>Per accedere al pannello di amministrazione aggiungete <strong>/admin</strong> all\'URL nella barra dell\'indirizzo.<br>Buon divertimento</p><p><strong>X3 CMS</strong></p><h2 class="mt-6">Cosa fare ora</h2>', 'x4get_by_key', 'cosa_fare|no_tags', 0, 1),
(NOW(), '66d18c936a9cfe9d82417e9b3af69e3f', 2, 'it', 1, 'mappa sito', 4, 1, UNIX_TIMESTAMP(), 0, '<h1>Mappa del sito</h1>', '', '', 0, 1),
(NOW(), '01415ff7588434f6797bf8869d58b2d6', 2, 'it', 1, 'risultato ricerca', 5, 1, UNIX_TIMESTAMP(), 0, '<h1>Risultato della ricerca</h1>', '', '', 0, 1),
(NOW(), '096d1fda54d98e4dcd5924bf34e46b92', 2, 'it', 1, 'informazioni', 6, 1, UNIX_TIMESTAMP(), 0, '<h1>Informazioni</h1><p>X3 CMS è uno strumento semplice per grandi progetti: <strong>semplice è meglio</strong> è la regola principale del nostro lavoro.<br>Con questa nuova versione del CMS X3 abbiamo riscritto quasi tutto il codice per renderlo ancora più semplice e potente.</p><p>Altre informazioni e documentazione sul CMS X3 al sito <a href="http://www.x3cms.net" title="Sito del progetto X3 CMS">www.x3cms.net</a>.</p>', '', '', 0, 1),
(NOW(), '9c7c7a6472f843dea1c6148935c78c77', 2, 'it', 1, 'sito offline', 7, 1, UNIX_TIMESTAMP(), 0, '<h1>Sito in manutenzione</h1><p>Ci scusiamo per il disagio.</p>', '', '', 0, 1),

(NOW(), '9d2aa0933a39e7579dd68557dcb8324a', 2, 'en', 1, 'welcome', 8, 1, UNIX_TIMESTAMP(), 0, '<h1>Welcome to X3 CMS</h1><p>If you see this page your Content Management System X3 was correctly installed and configured.<br>To access to the control panel add <strong>/admin</strong> to the URL on the address bar.<br>Enjoy</p><p><strong>X3 CMS</strong></p><h2 class="mt-6">What to do now</h2>', 'x4get_by_key', 'what_to_do|no_tags', 0, 1),
(NOW(), '4fbb483bb2abca4cefe1818eddb3c6b5', 2, 'en', 1, 'site map', 11, 1, UNIX_TIMESTAMP(), 0, '<h1>Site map</h1>', '', '', 0, 1),
(NOW(), '682146f9fc27f463cd874d5bd14a618a', 2, 'en', 1, 'search result', 12, 1, UNIX_TIMESTAMP(), 0, '<h1>Search result</h1>', '', '', 0, 1),
(NOW(), 'c4c95c36570d5a8834be5e88e2f0f6b2', 2, 'en', 1, 'information', 13, 1, UNIX_TIMESTAMP(), 0, '<h1>Information</h1><p>X3 CMS aims to be a simple tool to build great projects: <strong>simple is better</strong> is the main rule of our work.<br>With this new release of X3 CMS we rewritten almost all the code to be even more simple and powerful.</p><p>More information and documentation at <a href="http://www.x3cms.net" title="Project X3 CMS site">www.x3cms.net</a>.</p>', '', '', 0, 1),
(NOW(), '45d00683ff3a196cb430483d8208688c', 2, 'en', 1, 'site off line', 14, 1, UNIX_TIMESTAMP(), 0, '<h1>Site maintenance</h1><p>We apologize for the inconvenience.</p>', '', '', 0, 1),

(NOW(), 'd266dc06172bf9e423bf83c6788483ea', 3, 'it', 1, 'benvenuti area privata', 15, 1, UNIX_TIMESTAMP(), 0, '<h1>Benvenuti nell\'area privata del CMS X3</h1><p>Se visualizzate questa pagina il vostro Content Management System X3 &egrave; stato correttamente installato e configurato.</p><p>Per accedere al pannello di amministrazione aggiungete <strong>/admin</strong> all\'URL nella barra dell\'indirizzo.</p><p>Buon divertimento</p><p><strong>X3 CMS</strong></p>', '', '', 0, 1),
(NOW(), '05141455c70bb27de5d8925cd7560ac7', 3, 'it', 1, 'mappa area', 18, 1, UNIX_TIMESTAMP(), 0, '<h1>Mappa dell\'area</h1>', '', '', 0, 1),
(NOW(), 'e79713eac44836bfafac19a296140538', 3, 'it', 1, 'risultato ricerca', 19, 1, UNIX_TIMESTAMP(), 0, '<h1>Risultato della ricerca</h1>', '', '', 0, 1),

(NOW(), '1cd2b77d8f2b17141e1a7d94be100df1', 3, 'en', 1, 'welcome reserved area', 22, 1, UNIX_TIMESTAMP(), 0, '<h1>Welcome to the private area of X3 CMS</h1><p>If you see this page your Content Management System X3 was correctly installed and configured.<br>To access to the control panel add <strong>/admin</strong>  to the URL on the address bar.<br>Enjoy</p><p><strong>X3 CMS</strong></p>', '', '', 0, 1),
(NOW(), '0640e3193041d7d58fae88d548e43b1a', 3, 'en', 1, 'area map', 25, 1, UNIX_TIMESTAMP(), 0, '<h1>Area map</h1>', '', '', 0, 1),
(NOW(), '14bdd61f75985a52949b033ea9957ae6', 3, 'en', 1, 'search result', 26, 1, UNIX_TIMESTAMP(), 0, '<h1>Search result</h1>', '', '', 0, 1);

INSERT INTO `articles` (`updated`, `bid`, `id_area`, `lang`, `code_context`, `name`, `id_page`, `id_editor`, `date_in`, `date_out`, `xkeys`, `category`, `tags`, `content`, `ftext`, `js`, `excerpt`, `author`, `module`, `param`, `show_author`, `show_date`, `show_tags`, `show_actions`, `xschema`, `xlock`, `xon`) VALUES
(NOW(),	'93a2ebdfb8c9f7a1d51adabcc87ca5c4',	2,	'en',	2,	'filemanager',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>File Manager</h3>\r\n<p>X3CMS use TinyMCE as WYSIWYG editor for contents but the File Manager is not included.<br><strong>Get the Api key for FLMNGR</strong><br>visit <a href=\"https://flmngr.com/\">https://flmngr.com/</a> and create a free account for the TinyMCE plugin. Configure the File Manager for your domain.<br>At the end when you will have your API key replace the default value in the /cms/config/config.php</p>\r\n<p>NOTE: your API key could not work immediately. You should wait a bit.</p>',	'filemanager File Manager\r\nX3CMS use TinyMCE as WYSIWYG editor for contents but the File Manager is not included.Get the Api key for FLMNGRvisit https://flmngr.com/ and create a free account for the TinyMCE plugin. Configure the File Manager for your domain.At the end when you will have your API key replace the default value in the /cms/config/config.php\r\nNOTE: your API key could not work immediately. You should wait a bit.',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'5e2a383f3f8177fc1823e2eabc932a8d',	2,	'en',	2,	'composer',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>Enable extra features</h3>\r\n<p>Some libraries are not included in X3 CMS so if you want send Emails or want the File manager integrated with the WYSIWYG editor you need them.<br>So do a <strong>Composer Install</strong></p>\r\n<p>NOTE: you should install Composer on your server</p>',	'composer Enable extra features\r\nSome libraries are not included in X3 CMS so if you want send Emails or want the File manager integrated with the WYSIWYG editor you need them.So do a Composer Install\r\nNOTE: you should install Composer on your server',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'957f7cf4bb76f900daa7326fe098257d',	2,	'en',	2,	'cache',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>More speed with cache</h3>\r\n<p>At the bottom of your fresh install you should see the Debug info about load time, database queries and included files.</p>\r\n<p>Even a small web site need to be fast today: try enabling CACHE in the site settings.<br>You will see a double Debug info, the cached and the last.</p>\r\n<p>CACHE cache page content if there is not NOT CACHEABLE contents like forms (to display form errors).</p>\r\n<p>Do you need more? Then you have to enable query CACHE, set APC = true in the cms/config/config.php file</p><p>NOTE: to reset the CACHE after a change goes in Settings -&gt; Site -&gt; Eraser icon</p>',	'cache More speed with cache\r\nAt the bottom of your fresh install you should see the Debug info about load time, database queries and included files.\r\nEven a small web site need to be fast today: try enabling CACHE in the site settings.You will see a double Debug info, the cached and the last.\r\nCACHE cache page content if there is not NOT CACHEABLE contents like forms (to display form errors).\r\nDo you need more? Then you have to enable APCu on the cms/config/config.php file',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'091b0fa0d220a3f818a04dca1496c540',	2,	'en',	2,	'themes',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>Graphic themes</h3>\r\n<p>Obtaining a pleasant, non-monotonous site without particular technical knowledge is the problem of those who rely on a CMS.</p>\r\n<p>X3 offers two ways:</p>\r\n<ul>\r\n<li><strong>Simple Editing</strong>: one page -&gt; one section (a box for articles) -&gt; one article (with eventually a plugin like in this page)</li>\r\n<li><strong>Advanced Editing</strong>: one page&nbsp; -&gt; one or more sections -&gt; one or more articles for each section</li>\r\n</ul>\r\n<p>With the first is all on your hands: you have to compose the article with a pleasant typography and/or with images.</p>\r\n<p>With the last you can set a collection of styles to use compose each article in your page with colors, background images, background colors, borders, rounded corners, padding and more.</p>',	'themes Graphic themes\r\nObtaining a pleasant, non-monotonous site without particular technical knowledge is the problem of those who rely on a CMS.\r\nX3 offers two ways:\r\n\r\nSimple Editing: one page -&gt; one section (a box for articles) -&gt; one article (with eventually a plugin like in this page)\r\nAdvanced Editing: one page&nbsp; -&gt; one or more sections -&gt; one or more articles for each section\r\n\r\nWith the first is all on your hands: you have to compose the article with a pleasant typography and/or with images.\r\nWith the last you can set a collection of styles to use compose each article in your page with colors, background images, background colors, borders, rounded corners, padding and more.',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'c0b2efb6806852b05955c10e2c538211',	2,	'en',	2,	'plugins',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>New plugins</h3>\r\n<p>This new release of X3 comes with interesting new plugins:</p>\r\n<ul>\r\n<li><strong>x3banners</strong> to show a banner on top of some pages with a specified time window</li>\r\n<li><strong>x3form_builder</strong> to build form to put inside articles</li>\r\n</ul>\r\n<p>Playing with these will make you discover the potential of X3 CMS</p>',	'plugins New plugins\r\nThis new release of X3 comes with interesting new plugins:\r\n\r\nx3banners to show a banner on top of some pages with a specified time window\r\nx3form_builder to build form to put inside articles\r\n\r\nPlaying with these will make you discover the potential of X3 CMS',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'd6632b3890ce7b8ba17d8bce2399ab2b',	2,	'en',	2,	'explore admin',	0,	1,	UNIX_TIMESTAMP(),	0,	'what_to_do',	'',	'',	'<h3>Explore the Control Panel</h3>\r\n<p>The new control panel is even more clean and intuitive.<br>All works as usual:&nbsp;</p>\r\n<ul>\r\n<li>the PLUS in the top right to create new items</li>\r\n<li>the PENCIL to edit</li>\r\n<li>the LIGHTBULB to turn on or off an item (almost all if off will be not visible on the public site)</li>\r\n<li>the BIN to delete</li>\r\n</ul>\r\n<p>Delete is irreversible. If not sure turn off the light.</p>\r\n<p>You as first user will be the superadmin, remember: with great power comes great responsibility.<br>Enjoy</p>',	'explore admin Explore the Control Panel\r\nThe new control panel is even more clean and intuitive.All works as usual:&nbsp;\r\n\r\nthe PLUS in the top right to create new items\r\nthe PENCIL to edit\r\nthe LIGHTBULB to turn on or off an item (almost all if off will be not visible on the public site)\r\nthe BIN to delete\r\n\r\nDelete is irreversible. If not sure turn off the light.\r\nYou as first user will be the superadmin, remember: with great power comes great responsibility.Enjoy',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1);

INSERT INTO `articles` (`updated`, `bid`, `id_area`, `lang`, `code_context`, `name`, `id_page`, `id_editor`, `date_in`, `date_out`, `xkeys`, `category`, `tags`, `content`, `ftext`, `js`, `excerpt`, `author`, `module`, `param`, `show_author`, `show_date`, `show_tags`, `show_actions`, `xschema`, `xlock`, `xon`) VALUES
(NOW(),	'a802772cbec1be1e3f1419c175ead90d',	2,	'it',	2,	'filemanager',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>File Manager</h3>\r\n<p>X3CMS usa TinyMCE come editor WYSIWYG. Il File Manager non &egrave; incluso.<br><strong>Ottieni la Api key per FLMNGR</strong><br>visita <a href=\"https://flmngr.com/\">https://flmngr.com/</a> e crea un account free per il TinyMCE plugin. Configura il File Manager per il tuo dominio.<br>Alla fine avrai la tua API key sostituisci il valore di default in /cms/config/config.php</p>\r\n<p>NOTA: la API key potrebbe non essere subito attiva. Nel caso attendi.</p>',	'filemanager File Manager\r\nX3CMS usa TinyMCE come editor WYSIWYG. Il File Manager non &egrave; incluso.Ottieni la Api key per FLMNGRvisita https://flmngr.com/ e crea un account free per il TinyMCE plugin. Configura il File Manager per il tuo dominio.Alla fine avrai la tua API key sostituisci il valore di default in /cms/config/config.php\r\nNOTA: la API key potrebbe non essere subito attiva. Nel caso attendi.',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'5e12ea9037795400c62ce006cf8d934c',	2,	'it',	2,	'composer',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>Abilita funzionalit&agrave; extra</h3>\r\n<p>Alcune librerie non sono incluse in X3 CMS, quindi se vuoi inviare email o vuoi poter usare il file manager integrato con l\'editor WYSIWYG devi installare queste librerie.<br>Da linea di comando vai nella cartella che contiene il CMS e esegui il comando <strong>Composer Install</strong></p>\r\n<p>NOTA: Composer deve essere installato sul tuo server</p>',	'composer Abilita funzionalit&agrave; extra\r\nAlcune librerie non sono incluse in X3 CMS, quindi se vuoi inviare email o vuoi poter usare il file manager integrato con l\'editor wYSIwYG devi installare queste libreire.Da linea di comando vai nella cartella che contiene il CMS e esegui il comando Composer Install\r\nNOTA: Composer deve essere installato sul tuo server',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'f51388315977897c144012339f8614f2',	2,	'it',	2,	'cache',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>Sempre pi&ugrave; veloce</h3>\r\n<p>In basso nella pagina alla fine della installazione dovreste vedere le informazioni di debug: tempo di caricamento, query al database, file coinvolti.</p>\r\n<p>Anche un piccolo sito deve essere veloce: provate a abilitare la CACHE nelle impostazioni del sito.<br>Dopo un paio di reload vedrete due righe di Debug, quella conservata nella cache e quella reale.</p>\r\n<p>La CACHE conserva i contenuti delle pagine tranne quelli non conservabili come i FORM (per visualizzare i messaggi di errore).</p>\r\n<p>Serve di pi&ugrave;? Potete abilitare la cache delle query impostando APC = true nel file cms/config/config.php file</p>\r\n<p>NOTA: per azzerare la cache dopo una modifica andate in Impostazioni -&gt; Sito -&gt; Icona gomma da cancellare</p>',	'cache Sempre pi&ugrave; veloce\r\nIn basso nella pagina alla fine della installazione dovreste vedere le informazioni di debug: tempo di caricamento, query al database, file coinvolti.\r\nAnche un piccolo sito deve essere veloce: provate a abilitare la CACHE nelle impostazioni del sito.Dopo un paio di reload vedrete due righe di Debug, quella conservata nella cache e quella reale.\r\nLa CACHE conserva i contenuti delle pagine tranne quelli non conservabilicome i FORM (per visualizzare i messaggi di errore).\r\nServe di pi&ugrave;? Potete abilitare la cache delle query impostando APC = true nel file cms/config/config.php file\r\nNOTA: per azzerare la cache dopo una modifica andate in Impostazioni -&gt; Sito -&gt; Icona gomma da cancellare',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'3bc2a89cfa5e040e14adfeae19aa5891',	2,	'it',	2,	'temi',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>Temi grafici</h3>\r\n<p>Ottenere un sito gradevole senza avere particolari conoscenze tecniche &egrave; il problema di chi si affida a un CMS.</p>\r\n<p>X3 offre due strade:</p>\r\n<ul>\r\n<li><strong>Simple Editing</strong>: una pagina -&gt; una sezione (un contenitore per articoli) -&gt; un articolo (con eventualmente un modulo come in questa pagina)</li>\r\n<li><strong>Advanced Editing</strong>: una pagina -&gt; una o pi&ugrave; sezioni -&gt; uno o pi&ugrave; articoli in ogni sezione</li>\r\n</ul>\r\n<p>Con la prima soluzione &egrave; tutto nelle vostre mani: siete voi a comporre l\'articolo con una formattazione gradevole e eventuali immagini.</p>\r\n<p>Con l\'altra soluzione potete impostare una collezione di stili da usare per strutturare la pagina differenziando le sezioni o anche i singoli articoli con: colori, sfondi, bordi, angoli arrotondati, padding e altro.</p>',	'temi Temi grafici\r\nOttenere un sito gradevole senza avere particolari conoscenze tecniche &egrave; il problema di chi si affida a un CMS.\r\nX3 offre due strade:\r\n\r\nSimple Editing: una paagina -&gt; una sezione (un contenitore per articoli) -&gt; un articolo (con eventualmente un modulo come in questa pagina)\r\nAdvanced Editing: una pagina -&gt; una o pi&ugrave; sezioni -&gt; uno o pi&ugrave; articoli in ogni sezione\r\n\r\nCon la prima soluzione &egrave; tutto nelle vostre mani: siete voi a comporre l\'articolo con una formattazione gradevole e eventuali immagini.\r\nCon l\'altra soluzione potete impostare una collezione di stili da usare per strutturare la pagina differenziando le sezioni o anche i singoli articoli con: colori, sfondi, bordi, angoli arrotondati, padding e altro.',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'95164450d481097e6ff1e0dcf52b4fe4',	2,	'it',	2,	'plugins',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>Nuovi moduli</h3>\r\n<p>questa nuova versione di X3 arriva con nuovi interessanti moduli:</p>\r\n<ul>\r\n<li><strong>x3banners</strong> mostra un banner in cima a determinate pagine con una specifica finestra temporale</li>\r\n<li><strong>x3form_builder</strong> costruisce form da inserire dentro agli articoli</li>\r\n</ul>\r\n<p>Giocando con questi moduli scoprirete il potenziale di X3 CMS</p>',	'plugins Nuovi moduli\r\nquesta nuova versione di X3 arriva con nuovi interessanti moduli:\r\n\r\nx3banners mostra un banner in cima a determinate pagine con una specifica finestra temporale\r\nx3form_builder costruisce form da inserire dentro agli articoli\r\n\r\nGiocando con questi moduli scoprirete il potenziale di X3 CMS',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1),
(NOW(),	'8f580c24fa4808221cde4d13b5bb049e',	2,	'it',	2,	'esplora admin',	0,	1,	UNIX_TIMESTAMP(),	0,	'cosa_fare',	'',	'',	'<h3>Esplora il pannello di controllo</h3>\r\n<p>Il nuovo pannello di controllo &egrave; ancora pi&ugrave; ordinato e intuitivo.<br>Tutto funziona come sempre:</p>\r\n<ul>\r\n<li>il PIU\' in alto a destra permette di creare nuovi elementi</li>\r\n<li>la MATITA permette di modificarli</li>\r\n<li>la LAMPADINA attiva o disattiva un elemento (elemento spento non si vede nel sito)</li>\r\n<li>il CESTINO permette di eliminare</li>\r\n</ul>\r\n<p>Eliminazione &egrave; irreversibile. Se non siete sicuri meglio spegnere la lampadina.</p>\r\n<p>Come primo utente voi siete un SUPERADMIN, ricordate: da un grande potere derivano grandi responsabilit&agrave;.<br>Buon divertimento</p>',	'esplora admin Esplora il pannello di controllo\r\nIl nuovo pannello di controllo &egrave; ancora pi&ugrave; ordinato e intuitivo.Tutto funziona come sempre:\r\n\r\nil PIU\' in alto a destra permette di creare nuovi elementi\r\nla MATITA permette di modificarli\r\nla LAMPADINA attiva o disattiva un elemento (elemento spento non si vede nel sito)\r\nil CESTINO permette di eliminare\r\n\r\nEliminazione &egrave; irreversibile. Se non siete sicuri meglio spegnere la lampadina.\r\nCome primo utente voi siete un SUPERADMIN, ricordate: da un grande potere derivano grandi responsabilit&agrave;.Buon divertimento',	'',	0,	'web@cblu.net',	'',	'',	0,	0,	0,	0,	'',	0,	1);

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int(10) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `lang` char(2) NOT NULL,
  `id_user` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `url` varchar(255) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `lang` char(2) NOT NULL,
  `tag` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `contexts`
--

CREATE TABLE IF NOT EXISTS `contexts` (
  `id` int(10) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `lang` char(2) NOT NULL,
  `xkey` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `code` tinyint(3) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contexts`
--

INSERT INTO `contexts` (`updated`, `id_area`, `lang`, `xkey`, `code`, `name`, `xlock`, `xon`) VALUES
(NOW(), 1, 'it', 'drafts', 0, 'bozze', 0, 1),
(NOW(), 1, 'it', 'pages', 1, 'pagine', 0, 1),
(NOW(), 1, 'it', 'multi', 2, 'multipagine', 0, 1),
(NOW(), 1, 'en', 'dratfs', 0, 'drafts', 0, 1),
(NOW(), 1, 'en', 'pages', 1, 'pages', 0, 1),
(NOW(), 1, 'en', 'multi', 2, 'multipages', 0, 1),
(NOW(), 2, 'it', 'drafts', 0, 'bozze', 0, 1),
(NOW(), 2, 'it', 'pages', 1, 'pagine', 0, 1),
(NOW(), 2, 'it', 'multi', 2, 'multipagine', 0, 1),
(NOW(), 2, 'en', 'drafts', 0, 'drafts', 0, 1),
(NOW(), 2, 'en', 'pages', 1, 'pages', 0, 1),
(NOW(), 2, 'en', 'multi', 2, 'multipages', 0, 1),
(NOW(), 3, 'it', 'drafts', 0, 'bozze', 0, 1),
(NOW(), 3, 'it', 'pages', 1, 'pagine', 0, 1),
(NOW(), 3, 'it', 'multi', 2, 'multipagine', 0, 1),
(NOW(), 3, 'en', 'drafts', 0, 'drafts', 0, 1),
(NOW(), 3, 'en', 'pages', 1, 'pages', 0, 1),
(NOW(), 3, 'en', 'multi', 2, 'multipages', 0, 1);

-- --------------------------------------------------------


--
-- Table structure for table `dictionary`
--

CREATE TABLE IF NOT EXISTS `dictionary` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `lang` char(2) NOT NULL,
  `area` varchar(16) NOT NULL,
  `what` varchar(64) NOT NULL,
  `xkey` varchar(255) NOT NULL,
  `xval` text NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ind` (`area`,`lang`,`xkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dictionary`
--

-- admin it

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'it', 'admin', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'it', 'admin', 'global', '_X3CMS_SLOGAN', 'La forza della semplicità', 0, 1),
(NOW(), 'it', 'admin', 'global', '_WARNING', 'Attenzione!', 0, 1),
(NOW(), 'it', 'admin', 'global', '_CONGRATULATIONS', 'Complimenti!', 0, 1),

(NOW(), 'it', 'admin', 'global', '_MAINTENANCE_MODE', 'Sito in manutenzione', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DEVEL_MODE', 'Sito in sviluppo', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DEBUG_MODE', 'Debug attivo', 0, 1),
(NOW(), 'it', 'admin', 'global', '_MSG_OK', 'Operazione completata', 0, 1),
(NOW(), 'it', 'admin', 'global', '_MSG_ERROR', 'Attenzione! Si &egrave; verificato un errore.', 0, 1),

(NOW(), 'it', 'admin', 'global', '_FAILED_X_TIMES', 'Il login &egrave; fallito XXX volte.', 0, 1),
(NOW(), 'it', 'admin', 'global', '_FAILED_TOO_TIMES', 'Il login &egrave; fallito troppe volte.<br />Il tuo utente &egrave; temporaneamente disabilitato.', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ADMIN_AREA', 'Amministrazione', 0, 1),
(NOW(), 'it', 'admin', 'global', '_HI', 'Ciao', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LAST_LOGIN', 'il tuo ultimo login &egrave; del', 0, 1),

(NOW(), 'it', 'admin', 'global', '_USER', 'Utente', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LOGOUT', 'Esci', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ON', 'On', 0, 1),
(NOW(), 'it', 'admin', 'global', '_OFF', 'Off', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DELETE', 'Elimina', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DELETE_BULK', 'Elimina selezionati', 0, 1),
(NOW(), 'it', 'admin', 'global', '_UPDATE_BULK', 'Aggiorna selezionati', 0, 1),
(NOW(), 'it', 'admin', 'global', '_STATUS', 'Stato attuale', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LOCKED', 'Bloccato', 0, 1),
(NOW(), 'it', 'admin', 'global', '_UNLOCKED', 'Sbloccato', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ACTIONS', 'Azioni', 0, 1),
(NOW(), 'it', 'admin', 'global', '_MOVE_UP', 'Sposta su', 0, 1),
(NOW(), 'it', 'admin', 'global', '_UP', 'Su', 0, 1),
(NOW(), 'it', 'admin', 'global', '_MOVE_DOWN', 'Sposta gi&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DOWN', 'Gi&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'global', '_EDIT', 'Modifica', 0, 1),
(NOW(), 'it', 'admin', 'global', '_DUPLICATE', 'Fai una copia', 0, 1),
(NOW(), 'it', 'admin', 'global', '_COPY_OF', 'copia di', 0, 1),
(NOW(), 'it', 'admin', 'global', '_GO_BACK', 'Torna indietro', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LANGUAGE', 'lingua', 0, 1),
(NOW(), 'it', 'admin', 'global', '_PAGE', 'pagina', 0, 1),
(NOW(), 'it', 'admin', 'global', '_AREA', 'Area', 0, 1),
(NOW(), 'it', 'admin', 'global', '_NO_ITEMS', 'Nessun elemento trovato', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LAST_UPGRADE', 'Ultimo aggiornamento', 0, 1),
(NOW(), 'it', 'admin', 'global', '_CLOSE', 'Chiudi', 0, 1),
(NOW(), 'it', 'admin', 'global', '_PERMISSIONS', 'Permessi', 0, 1),
(NOW(), 'it', 'admin', 'global', '_CONFIG', 'Configura', 0, 1),
(NOW(), 'it', 'admin', 'global', '_INSTALL', 'Installa', 0, 1),
(NOW(), 'it', 'admin', 'global', '_UNINSTALL', 'Disinstalla', 0, 1),
(NOW(), 'it', 'admin', 'global', '_FOUND', 'Trovato', 0, 1),
(NOW(), 'it', 'admin', 'global', '_PAGES', 'pagine', 0, 1),
(NOW(), 'it', 'admin', 'global', '_FIRST_PAGE', 'prima pagina', 0, 1),
(NOW(), 'it', 'admin', 'global', '_LAST_PAGE', 'ultima pagina', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ITEMS', 'elementi', 0, 1),
(NOW(), 'it', 'admin', 'global', '_IN', 'in', 0, 1),
(NOW(), 'it', 'admin', 'global', '_NEXT', 'pagina successiva', 0, 1),
(NOW(), 'it', 'admin', 'global', '_PREVIOUS', 'pagina precedente', 0, 1),
(NOW(), 'it', 'admin', 'global', '_SWITCH_LANGUAGE', 'Cambia lingua', 0, 1),
(NOW(), 'it', 'admin', 'global', '_SWITCH_AREA', 'Cambia area', 0, 1),
(NOW(), 'it', 'admin', 'global', '_FIND', 'Trova', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ORDERABLE_MSG', 'Potete ordinare gli elementi trascinandoli', 0, 1),
(NOW(), 'it', 'admin', 'global', '_UNSAVED_CHANGES', 'Ci sono modifiche non salvate', 0, 1),
(NOW(), 'it', 'admin', 'global', '_CANCEL', 'Annulla', 0, 1),
(NOW(),	'it', 'admin', 'global', '_GENERATE', 'Genera',	0, 1),
(NOW(),	'it', 'admin', 'global', '_SETTINGS', 'Impostazioni',	0, 1),
(NOW(), 'it', 'admin', 'global', '_PLUGINS', 'Moduli', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ALL_PLUGINS', 'Tutti i moduli', 0, 1),
(NOW(), 'it', 'admin', 'global', '_HIDDEN', 'Elemento nascosto', 0, 1),
(NOW(), 'it', 'admin', 'global', '_VISIBLE', 'Elemento visibile', 0, 1),
(NOW(), 'it', 'admin', 'global', '_ENTER_TO_FILTER', 'premi invio per filtrare', 0, 1),

(NOW(), 'it', 'admin', 'global', '_FILTER', 'Filtra', 0, 1),
(NOW(), 'it', 'admin', 'global', '_FILTER_MSG', 'Inserisci un testo per filtrare', 0, 1),

(NOW(), 'it', 'admin', 'global', '_SET_ALL', 'Imposta per tutti', 0, 1),
(NOW(), 'it', 'admin', 'global', '_SET_ALL_MSG', 'Azione sarà eseguita solo sugli elementi visibili', 0, 1),

(NOW(), 'it', 'admin', 'home', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'it', 'admin', 'home', '_PUBLIC_SIDE', 'Sito pubblico', 0, 1),
(NOW(), 'it', 'admin', 'home', '_LOGGED_AS', 'Loggato come', 0, 1),
(NOW(), 'it', 'admin', 'home', '_SETTINGS', 'Impostazioni', 0, 1),
(NOW(), 'it', 'admin', 'home', '_WIDGETS', 'Widgets', 0, 1),
(NOW(), 'it', 'admin', 'home', '_RELOAD', 'Aggiorna', 0, 1),
(NOW(), 'it', 'admin', 'home', '_BOOKMARKS', 'Segnalibri', 0, 1),
(NOW(), 'it', 'admin', 'home', '_PROFILE', 'Profilo', 0, 1),
(NOW(), 'it', 'admin', 'home', '_HELP_ON_LINE', 'Aiuto on line', 0, 1),
(NOW(), 'it', 'admin', 'home', '_ABOUT', 'Info', 0, 1),
(NOW(), 'it', 'admin', 'home', '_NOTICES_AND_UPDATES', 'Avvisi e aggiornamenti', 0, 1),
(NOW(), 'it', 'admin', 'home', '_UNABLE_TO_CONNECT', 'Non &egrave; possibile stabilire una connessione con il server remoto.', 0, 1),

(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_MANAGER', 'Gestione widget', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_ITEMS', 'Widget', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_ADD', 'Aggiungi un nuovo widget', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_NEW', 'Nuovo widget', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_NEW_MSG', 'Puoi caricare ogni widget solo una volta', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_DELETE', 'Elimina widget', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_WIDGETS_AVAILABLE', 'Widget disponibili', 0, 1),
(NOW(), 'it', 'admin', 'widgets', '_NO_WIDGETS_TO_SET', 'Non ci sono widget disponibili', 0, 1),

(NOW(), 'it', 'admin', 'files', '_CATEGORY', 'Categoria', 0, 1),
(NOW(), 'it', 'admin', 'files', '_ADD_FILE', 'Aggiungi un nuovo file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_NEW_FILE', 'Nuovo file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_UPLOAD_FILE', 'Upload file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_FILE_LIST', 'File', 0, 1),
(NOW(), 'it', 'admin', 'files', '_FILE', 'File', 0, 1),
(NOW(), 'it', 'admin', 'files', '_FILE_SIZES', 'dimensioni massime immagini/documenti', 0, 1),
(NOW(), 'it', 'admin', 'files', '_DELETE_FILE', 'Elimina file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_AREA_LIST', 'Elenco aree', 0, 1),
(NOW(), 'it', 'admin', 'files', '_REFRESH_FILES', 'Rigenera lista file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_SUBCATEGORY', 'Sottocategoria', 0, 1),
(NOW(), 'it', 'admin', 'files', '_FILE_TREE', 'File caricati', 0, 1),
(NOW(), 'it', 'admin', 'files', '_EDIT_FILE', 'Modifica file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_SWITCH_CATEGORY', 'Cambia categoria', 0, 1),
(NOW(), 'it', 'admin', 'files', '_SWITCH_SUBCATEGORY', 'Cambia sotto categoria', 0, 1),
(NOW(), 'it', 'admin', 'files', '_FILE_FILTER', 'filtra file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_SELECT_ALL', 'Seleziona tutto', 0, 1),
(NOW(), 'it', 'admin', 'files', '_UNCATEGORIZED', 'nessuna categoria', 0, 1),

(NOW(), 'it', 'admin', 'files', '_IMAGES', 'immagini', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_EDIT', 'Modifica immagine', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_XCOORD', 'X coord', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_YCOORD', 'Y coord', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_WIDTH', 'Larghezza', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_HEIGHT', 'Altezza', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_LOCK_RATIO', 'Blocca proporzioni', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_ROTATE', 'Ruota', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_CONTRAST', 'Contrasto', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_BRIGHTNESS', 'Luminosità', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_SATURATION', 'Saturazione', 0, 1),
(NOW(), 'it', 'admin', 'files', '_IMAGE_AS_NEW', 'Salva in nuova immagine', 0, 1),

(NOW(), 'it', 'admin', 'files', '_FFMPEG_NOT_FOUND', 'FFMPEG il comando necessario per eseguire l\'operazione non è disponibile sul server', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_SWF_MSG', 'La grafica vettoriale non può essere convertita in altri formati', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_EDIT', 'Modifica video', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_EDIT_MSG', '<p>Alcuni formati video non sono supportati da alcuni browser e/o piattaforme:</p><ul><li>Chromium (Google Chrome per Linux  richiede chromium-codecs-ffmpeg-extra) e Opera (solo dalla versione 25) offre supporto per il formato MP4</li><li>Internet Explorer non supporta i formati WEBM e OGV</li><li>Safari non supporta i formati WEBM e OGV</li></ul>', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_FORMAT', 'Formato video', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_FORMAT_MSG', 'la conversione di file di grandi dimensioni potrebbe rallentare il vostro sito web.', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_GET_IMAGE', 'Estrai immagine dal video', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_SEC', 'Frame da usare per generare l\'immagine', 0, 1),
(NOW(), 'it', 'admin', 'files', '_VIDEO_SEC_MSG', 'selezionato in automatico allo stop', 0, 1),
(NOW(), 'it', 'admin', 'files', '_MEDIA', 'media', 0, 1),
(NOW(), 'it', 'admin', 'files', '_DOCUMENTS', 'altri file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_TEMPLATES', 'template', 0, 1),
(NOW(), 'it', 'admin', 'files', '_TEMPLATE_EDIT', 'Modifica template', 0, 1),
(NOW(), 'it', 'admin', 'files', '_TEMPLATE_MSG', 'Un template non può contenere script', 0, 1),
(NOW(), 'it', 'admin', 'files', '_ALL_FILES', 'tutti i file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_SWITCH_TYPE', 'Cambia tipo di file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_DROP_MSG', 'Trascina e rilascia qui i file', 0, 1),
(NOW(), 'it', 'admin', 'files', '_TEXT_EDIT', 'Modifica file di testo', 0, 1),


(NOW(), 'it', 'admin', 'msg', '_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_NOT_PERMITTED', 'Non avete i permessi necessari per eseguire l\'operazione richiesta', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_NOT_WRITEABLE', 'Non avete i permessi di scrittura su questo', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_UPLOAD_ERROR', 'Si &egrave; verificato un errore durante l\'upload del file.', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_USER_ALREADY_EXISTS', 'Esiste gi&agrave; un utente con lo stesso nome', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_USER_CANNOT_BE_MODIFIED', 'Questo utente non può essere modificato', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_PAGE_ALREADY_EXISTS', 'Esiste gi&agrave;  una pagina con lo stesso nome', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_AREA_ALREADY_EXISTS', 'Esiste gi&agrave; un\'area con lo stesso nome', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_FILE_SIZE_IS_TOO_BIG', 'La dimensione, in Kilobyte, del file di cui si cerca di fare l\'upload sono superiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_IMAGE_SIZE_IS_TOO_BIG', 'Le dimensioni, in pixel, del file di cui si cerca di fare l\'upload sono superiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_IMAGE_SIZE_IS_TOO_SMALL', 'Le dimensioni, in pixel, del file di cui si cerca di fare l\'upload sono inferiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_XKEY_ALREADY_EXISTS', 'Esiste gi&agrave; una espressione con la stessa chiave', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_LANGUAGE_ALREADY_EXISTS', 'Questa lingua esiste gi&agrave;', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_CATEGORY_ALREADY_EXISTS', 'Questa categoria esiste gi&agrave;', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_CONTEXT_ALREADY_EXISTS', 'Questo contesto esiste gi&agrave;', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_BAD_MIMETYPE', 'Il formato di file che si cerca di caricare non &egrave; ammesso', 0, 1),

(NOW(), 'it', 'admin', 'msg', '_REQUIRED_PLUGIN', 'deve essere installato per installare questo modulo', 0, 1),
(NOW(), 'it', 'admin', 'msg', '_PLUGIN_NEEDED_BY', 'richiede questo modulo', 0, 1),

(NOW(), 'it', 'admin', 'form', '_SESSION_EXPIRED', 'La sessione per la compilazione di questo modulo è scaduta.<br>Ricaricate la pagina e riprovate.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_FORM_NOT_VALID', 'Uno o pi&ugrave; campi del form non sono compilati correttamente:', 0, 1),
(NOW(), 'it', 'admin', 'form', '_FORM_DUPLICATE', 'Questo form &egrave; gi&agrave; stato registrato.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_REQUIRED', '&egrave; un campo obbligatorio.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_REQUIREDIF', '&Egrave; obbligatorio se impostate "XXXRELATEDXXX" a "XXXVALUEXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_A_VALUE_OTHER_THAN', 'un valore di verso da ', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_VALUE', 'non &egrave; un valore ammesso.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_MAIL', 'non &egrave; un indirizzo email valido.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_URL', 'non &egrave; un URL valido.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_DEPENDS', 'dipende da un campo che non avete settato "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_CONTAINS', 'deve contenere almeno XXXVALUEXXX volte la stringa "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_IFEMPTY', '&Egrave; obbligatorio se lasciate "XXXRELATEDXXX" vuoto.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INARRAY', 'dipende da un valore che non avete selezionato.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NOTINARRAY', 'il valore selezionato "XXXVALUEXXX" non può essere contenuto in "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SELECTED', 'dovete selezionare un numero di elementi XXXRELATEDXXX XXXVALUEXXX.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TOO_SHORT', 'ha una lunghezza inferiore a quella richiesta [XXXRELATEDXXX].', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_EQUAL', 'non coincide con "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_DIFFERENT', 'deve essere diverso da "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_NUMERIC', 'deve essere un numero', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_A_DATE', 'deve essere una data nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TOO_LONG', 'ha una lunghezza superiore a quella richiesta "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'le dimensioni, in pixel, sono superiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'form', '_IMAGE_SIZE_IS_TOO_SMALL', 'le dimensioni, in pixel, sono inferiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'le dimensioni, in Kilobyte, sono superiori al consentito', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'deve contenere solo numeri', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_ONLY', 'deve contenere solo "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_ALPHABETIC', 'deve contenere solo lettere dell\'alfabeto', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_ALPHANUMERIC', 'deve contenere solo numeri e lettere', 0, 1),

(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_UPPERCASE', 'deve contenere almeno una lettera maiuscola', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_LOWERCASE', 'deve contenere almeno una lettera minuscola', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_NUMBER', 'deve contenere almeno un numero', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_CONTAIN_SYMBOL', 'deve contenere almeno un carattere non alphanumerico (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'deve contenere solo numeri, lettere e simboli', 0, 1),

(NOW(), 'it', 'admin', 'form', '_MUST_BE_A_TIME', 'deve essere un orario nel formato HH:MM', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_A_TIMER', 'deve essere un numero di ore e minuti nel formato H:MM', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_A_DATETIME', 'deve essere una data con orario nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_PIVA', 'deve essere un numero di partita iva valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_CF', 'deve essere un codice fiscale valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_FISCAL_ID', 'deve essere un identificativo fiscale valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_A_PERIODICAL', 'deve essere una stringa formata da un numero seguito da uno dei seguenti termini: year, month, week, day', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_AFTER', 'deve essere data successiva a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_AFTER_OR_EQUAL', 'deve essere una data successiva o al limite uguale a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_BEFORE', 'deve essere data precedente a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'deve essere una data precedente o al limite uguale a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_WRONG_LENGTH', 'numero di caratteri sbagliato (XXXRELATEDXXX)', 0, 1),
(NOW(), 'it', 'admin', 'form', '_GREATER_THAN', 'deve essere maggiore di "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_LOWER_THAN', 'deve essere minore di "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'admin', 'form', '_IS_NOT_A_VALID_COLOR', 'non &egrave; un colore valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_IBAN', 'il codice IBAN inserito non &egrave; valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_EAN', 'il codice EAN inserito non &egrave; valido', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INVALID_DIRECTORY', 'la cartella specificata non esiste', 0, 1),
(NOW(), 'it', 'admin', 'form', '_REQUIRED_PLUGIN', 'non &egrave; installato ed &egrave; necessario.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_ALREADY_INSTALLED', 'risulta gi&agrave; installato.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MISSING_PLUGIN_INSTALLER', 'installer del plugin non trovato.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_PLUGIN_NOT_INSTALLED', 'Si &egrave; verificato un errore durante l\'installazione del plugin', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INCOMPATIBLE_PLUGIN', 'Questo plugin non &egrave; compatible con questa versione di X3CMS', 0, 1),
(NOW(), 'it', 'admin', 'form', '_INCOMPATIBLE_AREA', 'Questo plugin &egrave; pu&ograve; essere installato solo in queste aree', 0, 1),
(NOW(), 'it', 'admin', 'form', '_PLUGIN_NOT_UNINSTALLED', 'Si &egrave; verificato un errore durante la disinstallazione del plugin', 0, 1),
(NOW(), 'it', 'admin', 'form', '_THEME_NOT_INSTALLED', 'Si &egrave; verificato un errore durante l\'installazione del tema', 0, 1),
(NOW(), 'it', 'admin', 'form', '_THEME_NOT_UNINSTALLED', 'Si &egrave; verificato un errore durante la disinstallazione del tema', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TEMPLATE_INSTALLER_NOT_FOUND', 'Installer del template non trovato', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TEMPLATE_NOT_INSTALLED', 'Si &egrave; verificato un errore durante l\'installazione del template', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TEMPLATE_NOT_UNINSTALLED', 'Si &egrave; verificato un errore durante la disinstallazione del template', 0, 1),
(NOW(), 'it', 'admin', 'form', '_DEFAULT_TEMPLATE_CANT_BE_UNINSTALLED', 'Il template di default non pu&ograve; essere disinstallato', 0, 1),
(NOW(), 'it', 'admin', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'it', 'admin', 'form', '_UPLOAD_PROGRESS', 'Avanzamento upload', 0, 1),
(NOW(), 'it', 'admin', 'form', '_CATEGORY', 'Categoria', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SUBCATEGORY', 'Sottocategoria', 0, 1),
(NOW(), 'it', 'admin', 'form', '_FILE', 'File', 0, 1),
(NOW(), 'it', 'admin', 'form', '_COMMENT', 'Didascalia', 0, 1),
(NOW(), 'it', 'admin', 'form', '_RESET', 'Annulla', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SUBMIT', 'Registra', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SEARCH', 'Cerca', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SEND', 'Invia', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NO', 'No', 0, 1),
(NOW(), 'it', 'admin', 'form', '_YES', 'Si', 0, 1),
(NOW(), 'it', 'admin', 'form', '_ASSIGN_PERMISSIONS', 'Assegna i permessi', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NONEP', 'Niente', 0, 1),
(NOW(), 'it', 'admin', 'form', '_READP', 'Lettura', 0, 1),
(NOW(), 'it', 'admin', 'form', '_WRITEP', 'Scrittura', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MANAGEP', 'Gestione', 0, 1),
(NOW(), 'it', 'admin', 'form', '_ADMINP', 'Amministrazione', 0, 1),
(NOW(), 'it', 'admin', 'form', '_ARE_YOU_SURE_DELETE', 'Sei sicuro di voler eliminare', 0, 1),
(NOW(), 'it', 'admin', 'form', '_MAIL', 'Email', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NAME', 'Nome', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TITLE', 'Titolo', 0, 1),
(NOW(), 'it', 'admin', 'form', '_DESCRIPTION', 'Descrizione', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NOTES', 'Note', 0, 1),
(NOW(), 'it', 'admin', 'form', '_ADDRESS', 'indirizzo', 0, 1),
(NOW(), 'it', 'admin', 'form', '_KEYS', 'Chiavi', 0, 1),
(NOW(), 'it', 'admin', 'form', '_NOT_IN_MAP', 'Non visualizzare nella mappa del sito', 0, 1),
(NOW(), 'it', 'admin', 'form', '_IMG', 'Immagine', 0, 1),
(NOW(), 'it', 'admin', 'form', '_DATA_IN', 'Data', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TAGS', 'Tags', 0, 1),
(NOW(), 'it', 'admin', 'form', '_AUTHOR', 'Autore', 0, 1),
(NOW(), 'it', 'admin', 'form', '_CONTENT', 'Contenuti', 0, 1),
(NOW(), 'it', 'admin', 'form', '_LEVEL_RULES', 'Lettura: pu&ograve; solo leggere, Scrittura: pu&ograve; leggere e scrivere, Gestione: pu&ograve; leggere, scrivere, attivare e disattivare, Amministrazione: come gestione e in pi&ugrave; bloccare e eliminare', 0, 1),
(NOW(), 'it', 'admin', 'form', '_CAPTCHA_ERROR', 'Il codice di controllo con corrisponde.', 0, 1),
(NOW(), 'it', 'admin', 'form', '_SELECT_COLOR', 'Scegli un colore', 0, 1),
(NOW(), 'it', 'admin', 'form', '_TO_CHANGE', 'Da modificare', 0, 1),

(NOW(), 'it', 'admin', 'sites', '_SETTINGS_MANAGER', 'Gestione impostazioni', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_SETTINGS_MANAGER_MSG', 'Qui potete configurare la struttura e il comportamento del vostro X3CMS', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_SITE_MANAGER', 'Gestione sito', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_ONLINE', 'On Line', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_OFFLINE', 'Off Line', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_KEYCODE', 'Codice licenza', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_DOMAIN', 'Dominio', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_DATABASE', 'Database', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_DATABASE_MSG', 'Per dominii alternativi', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_SITE_CONFIG', 'Configurazione sito', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_ADD_SITE', 'Aggiungi un nuovo sito', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_EDIT_SITE', 'Modifica sito', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_CLEAR_CACHE', 'Svuota la cache', 0, 1),
(NOW(), 'it', 'admin', 'sites', '_VERSION', 'versione X3CMS', 0, 1),


(NOW(), 'it', 'admin', 'info', '_SITE_INFO', 'Informazioni', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_SERVER', 'Server', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_KEY', 'Dato', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_VALUE', 'Valore', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_APACHE', 'Moduli Apache caricati', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_MYSQL', 'MySQL', 0, 1),
(NOW(), 'it', 'admin', 'info', '_INFO_PHP', 'Estensioni PHP caricate', 0, 1),


(NOW(), 'it', 'admin', 'help', '_HELP', 'Guida utente', 0, 1),
(NOW(), 'it', 'admin', 'help', '_HELP_MSG', 'Se il tuo CMS X3 prevede una guida personalizzata i link alle singole pagine della guida saranno visualizzati sotto questo testo.<br />In ogni caso puoi consultare la <b>Guida On Line</b>.', 0, 1),
(NOW(), 'it', 'admin', 'help', '_HELP_ON_LINE', 'Guida On Line', 0, 1),
(NOW(), 'it', 'admin', 'help', '_HELP_ON_SITE', 'Guida personalizzata', 0, 1),


(NOW(), 'it', 'admin', 'login', '_UNSUPPORTED_BROWSER', 'Questo browser non &egrave; supportato', 0, 1),
(NOW(), 'it', 'admin', 'login', '_SUPPORTED_BROWSER', 'Aggiorna il tuo browser alla versione pi&ugrave; recente e scegli uno dei browser supportati', 0, 1),
(NOW(), 'it', 'admin', 'login', '_LOGIN', 'Login', 0, 1),
(NOW(), 'it', 'admin', 'login', '_USERNAME', 'Username', 0, 1),
(NOW(), 'it', 'admin', 'login', '_MAIL', 'Il tuo indirizzo email', 0, 1),
(NOW(), 'it', 'admin', 'login', '_PASSWORD', 'Password', 0, 1),
(NOW(), 'it', 'admin', 'login', '_REMEMBER_ME', 'Ricordami su questo computer', 0, 1),
(NOW(), 'it', 'admin', 'login', '_CAPTCHA', 'Controllo antispam', 0, 1),
(NOW(), 'it', 'admin', 'login', '_RELOAD_CAPTCHA', 'Ricarica l\'immagine se risulta illegibile', 0, 1),
(NOW(), 'it', 'admin', 'login', '_CAPTCHA_MSG', 'Scrivi il testo sottostante', 0, 1),
(NOW(), 'it', 'admin', 'login', '_RESET_PWD_TITLE', 'Recupera la password', 0, 1),
(NOW(), 'it', 'admin', 'login', '_RESET_PWD', 'Hai dimenticato la password?', 0, 1),
(NOW(), 'it', 'admin', 'login', '_RESET_MSG', 'Una mail con le istruzioni per il recupero della password sar&agrave; inviata al vostro indirizzo email', 0, 1),


(NOW(), 'it', 'admin', 'pwd_recovery', '_RECOVERY_SUBJECT', 'Recupero password', 0, 1),
(NOW(), 'it', 'admin', 'pwd_recovery', '_RECOVERY_BODY_CONFIRM', 'Abbiamo ricevuto una richiesta di recupero della password associata a questo account.<br />Per confermare la richiesta segui questo link <a href="XXXLINKXXX" title="recupero password su XXXDOMAINXXX">XXXLINKXXX</a>', 0, 1),
(NOW(), 'it', 'admin', 'pwd_recovery', '_RECOVERY_BODY_RESET', 'Come richiesto la password &egrave; stata reimpostata<br />Ecco i vostri nuovi parametri di login: <li>Username: XXXUSERNAMEXXX</li> <li>Password: XXXPASSWORDXXX</li></ul>', 0, 1),
(NOW(), 'it', 'admin', 'pwd_recovery', '_RECOVERY_PWD_OK', 'Una email con le nuove credenziali di accesso &egrave; stata inviata al vostro indirizzo di posta elettronica.', 0, 1),
(NOW(), 'it', 'admin', 'pwd_recovery', '_RECOVERY_PWD_ERROR', 'L\'indirizzo e-mail che hai inserito non &egrave; presente nel database.<br />Controlla e riprova.<br />Grazie', 0, 1),


(NOW(), 'it', 'admin', 'lang', '_LANGUAGE', 'Lingua', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_LANG_AREAS_FOR', 'Aree della lingua', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_AREAS', 'Aree', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_AREA', 'Area', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_WORDS', 'Espressioni', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_TRANSLATION', 'Traduzioni', 0, 1),
(NOW(), 'it', 'admin', 'lang', '_SWITCH_LANGUAGE', 'Cambia lingua', 0, 1),

(NOW(), 'it', 'admin', 'languages', '_LANGUAGES_MANAGER', 'Gestione lingue', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_ADD_LANG', 'Aggiungi una lingua', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_EDIT_LANG', 'Modifica lingua', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_DELETE_LANG', 'Elimina lingua', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_SHOW_LANG_KEYS', 'Visualizza chiavi di questa lingua', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_NEW_LANG', 'Nuova lingua', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_CODE', 'Codice', 0, 1),
(NOW(), 'it', 'admin', 'languages', '_RTL_LANGUAGE', 'Lingua che va da destra a sinistra', 0, 1),


(NOW(), 'it', 'admin', 'menus', '_MENUS', 'Men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_MENU_LIST', 'Elenco men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_ADD_MENU', 'Aggiungi un men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_NEW_MENU', 'Nuovo men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_EDIT_MENU', 'Modifica un men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_DELETE_MENU', 'Elimina men&ugrave;', 0, 1),

(NOW(), 'it', 'admin', 'menus', '_BOOKMARKS', 'Segnalibro', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_BOOKMARKS_NAME', 'Nome segnalibro', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_BOOKMARKS_ADD', 'Aggiungi pagina corrente', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_BOOKMARKS_DELETE', 'Elimina segnalibro', 0, 1),
(NOW(), 'it', 'admin', 'menus', '_BOOKMARKS_ALREADY_EXISTS', 'Segnalibro già registrato', 0, 1),


(NOW(), 'it', 'admin', 'pages', '_PAGE_LIST', 'Pagine - elenco pagine area', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_ADD_PAGE', 'Aggiungi una nuova pagina', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_NEW_PAGE', 'Nuova pagina', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_MENU', 'Men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_SUBMENU', 'Sottomen&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_SUBPAGES', 'sottopagine', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_MENU_AND_ORDER', 'Posizione della pagina', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_SEO_TOOLS', 'Seo Tools', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_HISTORY', 'Storico', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_NO_SUBPAGES', 'Questa pagina non ha sottopagine', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_FROM_PAGE', 'Pagina di origine', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_TEMPLATE', 'Template', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_URL', 'URL pagina', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_DELETE_PAGE', 'Elimina pagina', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_INIZIALIZE_AREA', 'Inizializza area', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_SITE_MAP', 'Mappa del sito', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_FAKE_PAGE', 'Pagina finta', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_FAKE_PAGE_MSG', 'Solo voce di menù', 0, 1),

(NOW(), 'it', 'admin', 'pages', '_ROBOT', 'Regole per meta ROBOTS', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_ROBOT_MSG', 'se vuoto viene usato "index,follow"', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_REDIRECT_CODE', 'Codice redirect', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_REDIRECT', 'Redirect URL', 0, 1),
(NOW(), 'it', 'admin', 'pages', '_REDIRECT_MSG', 'inserire vecchio URL per esteso o relativo se nello stesso dominio', 0, 1),

(NOW(), 'it', 'admin', 'articles', '_ARTICLE_LIST', 'Elenco articoli', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLES', 'Articoli', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ADD_ARTICLE', 'Aggiungi un nuovo articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_NEW_ARTICLE', 'Nuovo articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_EDIT_ARTICLE', 'Modifica articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_DELETE_ARTICLE', 'Elimina articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLE_HISTORY', 'Storico articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_HISTORY', 'Storico', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_LATEST_ARTICLES', 'Ultimi articoli', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ALPHABETICAL_ORDER', 'Ordine alfabetico', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_KEY_ORDER', 'Ordinati per chiave', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT_ORDER', 'Ordinati per contesto', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CATEGORY_ORDER', 'Ordinati per categoria', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_AUTHOR_ORDER', 'Ordinati per autore', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_PAGE', 'Pagina', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_AUTHOR', 'Autore', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CATEGORY', 'Categoria', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT', 'Contesto', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SEARCH_BY_TEXT', 'Cerca per titolo o contenuti', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_KEY', 'Chiave', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_VIEW_ARTICLES', 'Vedi gli articoli', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_START_DATE', 'Data inizio', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_END_DATE', 'Data fine', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_NO_END_MSG', 'Vuoto per non fissare termine', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTENTS', 'Contenuti', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SCRIPT', 'Script', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SCRIPT_MSG', 'tutti gli script inclusi nei contenuti sono rimossi al fine di evitare inclusioni non desiderate. Eventuali script, compresi di tag di apertura e chiusura, vanno inseriti qui', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_MODULE', 'Modulo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_PARAM', 'Parametro', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLE_PARAM_SETTING', 'Configurazione parametro', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLE_PARAM_SETTING_NOT_REQUIRED', 'Questo modulo non richiede parametro', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLE_PARAM_DEFAULT_MSG', 'Seleziona una delle opzioni disponibili', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLE_PARAM_OPTIONS', 'Opzioni modulo', 0, 1),

(NOW(), 'it', 'admin', 'articles', '_CONTENT_EDITOR', 'Editor pagina', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT', 'Contesto', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SCHEMA', 'Schema articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_TIME_WINDOW', 'Finestra temporale', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ORGANIZATION', 'Catalogazione articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_PLUGIN', 'Plugin', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_OPTIONS', 'Opzioni articolo', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SHOW_AUTHOR', 'Visualizza autore', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SHOW_TAGS', 'Visualizza tags', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SHOW_DATE', 'Visualizza la data', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_SHOW_ACTIONS', 'Visualizza azioni', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT_DRAFTS', 'Bozze', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT_PAGES', 'Pagine', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_CONTEXT_MULTI', 'Multipagine', 0, 1),
(NOW(), 'it', 'admin', 'articles', '_ARTICLES_SEARCH_MSG', 'Cerca per titolo, contenuti e tag', 0, 1),

(NOW(), 'it', 'admin', 'categories', '_CATEGORY_LIST', 'Elenco categorie', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_CATEGORIES', 'Categorie', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_CATEGORY_TAG', 'Tag categoria', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_CATEGORY_TAG_MSG', 'usato internamente, utile per raggruppare categorie collegate a diverse finalità', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_ADD_CATEGORY', 'Aggiungi un nuova categoria', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_NEW_CATEGORY', 'Nuovo categoria', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_EDIT_CATEGORY', 'Modifica categoria', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_DELETE_CATEGORY', 'Elimina categoria', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_ALL_TAGS', 'Tutti i tag', 0, 1),
(NOW(), 'it', 'admin', 'categories', '_NO_CATEGORY_TAG', 'Tag vuoto', 0, 1),


(NOW(), 'it', 'admin', 'contexts', '_CONTEXT_LIST', 'Elenco contesti', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_CONTEXT_MSG', 'Contesti permettono di raggruppare articoli per gestirli e visualizzarli in modo coerente', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_CONTEXTS', 'Contesti', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_ADD_CONTEXT', 'Aggiungi un nuovo contesto', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_NEW_CONTEXT', 'Nuovo contesto', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_EDIT_CONTEXT', 'Modifica contesto', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_DELETE_CONTEXT', 'Elimina contesto', 0, 1),
(NOW(), 'it', 'admin', 'contexts', '_DEFAULT_CONTEXTS_MSG', 'Contesti di default non possono essere modificati', 0, 1),



(NOW(), 'it', 'admin', 'sections', '_SECTIONS', 'Sezioni', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTIONS_SORT_MSG', 'Solo le sezioni sotto la linea arancione sono ordinabili', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION', 'Sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_NEW', 'Nuova sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_EDIT', 'Modifica sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_EDIT_MSG', '<p>NOTA: la configurazione delle colonne si aggiorna solo dopo il salvataggio.</p>', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_DELETE', 'Elimina sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_SETTINGS', 'Configurazione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_SIZES_AND_COLORS', 'Sezione colori e dimensioni', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_COLUMNS_STYLE', 'Colonne colori e stili', 0, 1),

(NOW(), 'it', 'admin', 'sections', '_SECTION_BACKGROUND', 'Colore sfondo', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_IMG_H', 'Immagine sfondo orizzontale', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_IMG_V', 'Immagine sfondo verticale', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_FOREGROUND', 'Colore testo', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_COLUMNS', 'Numero di colonne', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_COL_SIZES', 'Suddivisione colonne', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_COL_SIZES_MSG', 'Per avere 2 colonne 2/3+1/3 inserire 3 per colonne e 2+1 in suddivisione colonne', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_STATUS', 'Stato sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_UNLOCKED', 'Sezione sbloccata: parametri personalizzabili', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_LOCKED', 'Sezione bloccata: il tema gestirà tutti i parametri', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_WIDTH', 'Larghezza sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_FULL_WIDTH', 'larghezza piena', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_FIXED_WIDTH', 'larghezza fissa da impostazioni del tema', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_HEIGHT', 'Altezza: libera o fullscreen', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_STYLE', 'Stile dal tema', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_STYLE_MSG', 'seleziona uno stile dal tema', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_CLASS', 'una o più classi CSS per la sezione', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTION_CLASS_MSG', 'inserire classi CSS del tema', 0, 1),


(NOW(), 'it', 'admin', 'sections', '_COMPOSE_EDITOR', 'Compositore pagina', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_ARTICLES_LIST', 'Articoli disponibili', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_ARTICLES_MSG', 'Trascina gli articoli nelle sezioni dei contenuti', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_SECTIONS_MSG', 'Trascina gli articoli per ordinarli o rimuoverli', 0, 1),
(NOW(), 'it', 'admin', 'sections', '_DROP_HERE', 'Rilascia qui', 0, 1),


(NOW(), 'it', 'admin', 'history', '_HISTORY_LIST', 'Pagine - storico pagina:', 0, 1),
(NOW(), 'it', 'admin', 'history', '_PREVIEW', 'Anteprima', 0, 1),
(NOW(), 'it', 'admin', 'history', '_EDIT_DATE', 'modifica data', 0, 1),
(NOW(), 'it', 'admin', 'history', '_UNDEFINED', 'indefinita', 0, 1),
(NOW(), 'it', 'admin', 'history', '_DELETE_VERSION', 'Elimina versione', 0, 1),
(NOW(), 'it', 'admin', 'history', '_SET_DATE', 'Date di visualizzazione', 0, 1),
(NOW(), 'it', 'admin', 'history', '_LEAVE_EMPTY_FOR_UNDEFINED', 'Lasciare il campo vuoto per non impostare un termine', 0, 1),


(NOW(), 'it', 'admin', 'areas', '_AREA_LIST', 'Elenco aree', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_ADD_AREA', 'Aggiungi un\'area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_NEW_AREA', 'Nuova area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_ENABLED_LANGUAGES', 'Lingue abilitate', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_DEFAULT_LANG', 'Lingua di default', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_DELETE_AREA', 'Elimina area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_AREA_LANG_LIST', 'Aree - elenco lingue per area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_RESET', 'Resetta', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_AREA_LANG_MAP', 'Mappa dell\'area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_EDIT_AREA', 'Modifica un\'area', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_SEO_DATA', 'Dati SEO', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_PRIVATE', 'Area privata', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_FOLDER', 'Cartella', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_DOMAIN', 'Dominio', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_DEFAULT_AREA', 'Default area per plugin', 0, 1),
(NOW(), 'it', 'admin', 'areas', '_DEFAULT_AREA_MSG', 'E\' utile tenere tutti i plugin collegati tra loro in una sola area', 0, 1),


(NOW(), 'it', 'admin', 'dictionary', '_KEY', 'Chiave', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_KEYS_LIST', 'Elenco chiavi', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_ADD_WORD', 'Aggiungi una espressione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_NEW_WORD', 'Nuova espressione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_EDIT_WORD', 'Modifica espressione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_DELETE_WORD', 'Elimina espressione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_SHOW_WORDS', 'Visualizza espressioni con questa chiave', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_WORD', 'Espressione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_IMPORT_INTO', 'Importa in', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_IMPORT_KEYS', 'Importa chiavi', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_SECTION', 'Sezione', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_SECTIONS_LIST', 'Elenco sezioni', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_WORDS_LIST', 'Elenco espressioni', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_DICTIONARY_SEARCH_MSG', 'Cerca per chiave in tutte le lingue', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_DICTIONARY_SEARCH_RESULT', 'Risultato della ricerca', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_DICTIONARY_DELETE_DUPLICATES', 'Elimina chiavi duplicate', 0, 1),
(NOW(), 'it', 'admin', 'dictionary', '_DICTIONARY_DELETE_DUPLICATES_MSG', 'tutte le chiavi duplicate all\'interno di ogni sezione', 0, 1),

(NOW(), 'it', 'admin', 'users', '_GROUP', 'Gruppo', 0, 1),
(NOW(), 'it', 'admin', 'users', '_USERS_LIST', 'Elenco utenti', 0, 1),
(NOW(), 'it', 'admin', 'users', '_USER_VIEW', 'Dettaglio utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_ADD_USER', 'Aggiungi un utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_EDIT_USER', 'Modifica un utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_EDIT_PROFILE', 'Modifica il tuo profilo', 0, 1),
(NOW(), 'it', 'admin', 'users', '_NEW_USER', 'Nuovo utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_DELETE_USER', 'Elimina utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_EMAIL', 'Email', 0, 1),
(NOW(), 'it', 'admin', 'users', '_PHONE', 'Telefono', 0, 1),
(NOW(), 'it', 'admin', 'users', '_REPEAT_PASSWORD', 'Ripeti password', 0, 1),
(NOW(), 'it', 'admin', 'users', '_LEVEL', 'Livello utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_USERNAME_RULE', 'Almeno 6 caratteri alfanumerici', 0, 1),
(NOW(), 'it', 'admin', 'users', '_PASSWORD_RULE', 'Almeno 6 caratteri alfanumerici e simboli', 0, 1),
(NOW(), 'it', 'admin', 'users', '_USER_DETAIL', 'Dettaglio utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_MAIL_USER', 'Scrivi una email', 0, 1),
(NOW(), 'it', 'admin', 'users', '_PASSWORD_CHANGE_MSG', 'Lasciare vuoto se non si intende modificare la password', 0, 1),
(NOW(), 'it', 'admin', 'users', '_HIDE_USER', 'Utente nascosto', 0, 1),
(NOW(), 'it', 'admin', 'users', '_SHOW_USER', 'Utente visibile', 0, 1),
(NOW(), 'it', 'admin', 'users', '_DOMAIN', 'Aree di pertinenza', 0, 1),
(NOW(), 'it', 'admin', 'users', '_EDIT_PRIV', 'Modifica i permessi dell\'utente', 0, 1),
(NOW(), 'it', 'admin', 'users', '_EDIT_DETAIL_PRIV', 'Modifica i permessi nel dettaglio', 0, 1),
(NOW(), 'it', 'admin', 'users', '_ALL_DETAIL_PRIV', 'Imposta lo stesso per tutti', 0, 1),
(NOW(), 'it', 'admin', 'users', '_GLOBAL_PRIVS', 'Permessi globali', 0, 1),
(NOW(), 'it', 'admin', 'users', '_RESET_PRIVS', 'Resetta i permessi', 0, 1),
(NOW(), 'it', 'admin', 'users', '_RESET_PRIVS_MSG', 'Elimina tutte le personalizzazioni e sincronizza con i permessi del gruppo', 0, 1),
(NOW(), 'it', 'admin', 'users', '_REFACTORY', 'Aggiorna i permessi', 0, 1),
(NOW(), 'it', 'admin', 'users', '_REFACTORY_MSG', 'Sincronizza con i permessi di gruppo, crea i permessi suoi nuovi oggetti e mantiene le personalizzazioni', 0, 1),
(NOW(), 'it', 'admin', 'users', '_TABLE', 'Tabella', 0, 1),


(NOW(), 'it', 'admin', 'profile', '_SUBJECT_PROFILE', 'Aggiornamento profilo su DOMAIN', 0, 1),
(NOW(), 'it', 'admin', 'profile', '_MSG_PROFILE', "Gentile Utente,\nconserva questa email come promemoria dell'account creato per te su DOMAIN \n\nDati per l'accesso \nUsername: USERNAME \nPassword: PASSWORD \n\nGrazie", 0, 1),


(NOW(), 'it', 'admin', 'modules', '_AREA_LIST', 'Elenco aree', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_MODULE_LIST', 'Elenco plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_MODULE', 'Plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_INSTALLED_PLUGINS', 'Plugin installati', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_INSTALLABLE_PLUGINS', 'Plugin installabili', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_MODULE', 'Plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_UNINSTALL_PLUGIN', 'Disinstalla plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_ARE_YOU_SURE_UNINSTALL', 'Sei sicuro di voler disinstallare il plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_MODULE_CONFIG', 'Configurazione plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_PARAM', 'Parametro plugin', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_MODULE_INSTRUCTIONS', 'Istruzioni per l\'uso del modulo', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_INSTRUCTIONS', 'Istruzioni', 0, 1),
(NOW(), 'it', 'admin', 'modules', '_PLUGGABLE', 'Modulo inseribile', 0, 1),

(NOW(), 'it', 'admin', 'groups', '_ADD_GROUP', 'Aggiungi un gruppo', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_EDIT_GROUP', 'Modifica un gruppo', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_NEW_GROUP', 'Nuovo gruppo', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_DELETE_GROUP', 'Elimina gruppo', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_GROUP_LIST', 'Elenco gruppi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_EDIT_GPRIV', 'Modifica i permessi del gruppo', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_GPRIV', 'Permessi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_GROUP_PERMISSION', 'Permessi del gruppo', 0, 1),

(NOW(), 'it', 'admin', 'groups', '_AREA_CREATION', 'Creazione aree', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_ARTICLE_CREATION', 'Creazione articoli', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_CATEGORY_CREATION', 'Creazione categorie', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_FILE_UPLOAD', 'Upload files', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_CONTEXT_CREATION', 'Creazione contesto', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_GROUP_CREATION', 'Creazione gruppi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_KEY_CREATION', 'Creazione chiavi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_KEY_IMPORT', 'Importazione chiavi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_LANGUAGE_CREATION', 'Nuove lingue', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_MENU_CREATION', 'Creazione men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_MODULE_INSTALL', 'Installazione moduli', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_MODULE_UNINSTALL', 'Disinstallazione moduli', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_PAGE_CREATION', 'Creazione pagine', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_SECTION_CREATION', 'Creazione sezioni', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_SITE_CREATION', 'Creazione sito', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_TEMPLATE_INSTALL', 'Installare template', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_THEME_INSTALL', 'Installare temi', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_USER_CREATION', 'Creazione utenti', 0, 1),
(NOW(), 'it', 'admin', 'groups', '_WORD_CREATION', 'Inserimento vocaboli', 0, 1),


(NOW(), 'it', 'admin', 'groups', 'AREAS', 'Gestione aree', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'ARTICLES', 'Gestione articoli', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'CATEGORIES', 'Gestione categorie', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'CONTENTS', 'Gestione contenuti', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'CONTEXTS', 'Gestione contenuti', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'DICTIONARY', 'Gestione dizionari', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'FILES', 'Gestione files', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'XGROUPS', 'Gestione gruppi', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'LANGUAGES', 'Gestione lingue', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'LOGS_DATA', 'Gestione logs', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'MENUS', 'Gestione men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'MODULES', 'Gestione moduli', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'PAGES', 'Gestione pagine', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'PRIVS', 'Gestione permessi', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'SECTIONS', 'Gestione sezioni', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'SITES', 'Gestione sito', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'TEMPLATES', 'Gestione template', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'THEMES', 'Gestione temi', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'USERS', 'Gestione utenti', 0, 1),
(NOW(), 'it', 'admin', 'groups', 'WIDGETS', 'Gestione widgets', 0, 1),


(NOW(), 'it', 'admin', 'themes', '_THEME_LIST', 'Elenco temi', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME', 'Tema', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_TEMPLATES', 'Template', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_MENUS', 'Men&ugrave;', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_MINIMIZE', 'Minifica CSS e JS', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_INSTALLED_THEMES', 'Temi installati', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_INSTALLABLE_THEMES', 'Temi installabili', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_UNINSTALL_THEME', 'Disinstalla tema', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_ARE_YOU_SURE_UNINSTALL', 'Sei sicuro di voler disinstallare il tema', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME_WHAT', 'Sezione o articolo', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME_EDIT', 'Modifica stili del tema', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME_EDIT_MSG', 'Queste opzioni sono usabili solo con l\'ADVANCED EDITING', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME_STYLE', 'Stile del tema', 0, 1),
(NOW(), 'it', 'admin', 'themes', '_THEME_STYLE_MSG', 'inserire classi presenti nel CSS del tema o nel framework CSS usato', 0, 1),


(NOW(), 'it', 'admin', 'templates', '_TEMPLATE_LIST', 'Elenco template', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_TEMPLATE', 'Template', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_INVALID_TEMPLATE', 'Il nome del Template non deve contenere spazi.', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_CSS', 'Foglio di stile associato', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_SECTIONS_SETTINGS', 'Impostazioni sezioni', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_INSTALL_TEMPLATE', 'Install template', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_INSTALLED_TEMPLATES', 'Template installati', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_INSTALLABLE_TEMPLATES', 'Template installabili', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_UNINSTALL_TEMPLATE', 'Disinstalla template', 0, 1),
(NOW(), 'it', 'admin', 'templates', '_ARE_YOU_SURE_UNINSTALL', 'Sei sicuro di voler disinstallare il template', 0, 1),

(NOW(), 'it', 'admin', 'time', '_TIME_DAYS_AGO', 'giorni fa', 0, 1),
(NOW(), 'it', 'admin', 'time', '_TIME_HOURS_AGO', 'ore fa', 0, 1),
(NOW(), 'it', 'admin', 'time', '_TIME_MINUTES_AGO', 'minuti fa', 0, 1),
(NOW(), 'it', 'admin', 'time', '_TIME_SECONDS_AGO', 'secondi fa', 0, 1),

(NOW(), 'it', 'admin', 'bulk', '_BULK_ACTION', 'Azione', 0, 1),
(NOW(), 'it', 'admin', 'bulk', '_BULK_COPY', 'Copia elementi selezionati', 0, 1),
(NOW(), 'it', 'admin', 'bulk', '_BULK_MOVE', 'Sposta elementi selezionati', 0, 1),
(NOW(), 'it', 'admin', 'bulk', '_BULK_DESTINATION', 'Destinazione', 0, 1),
(NOW(), 'it', 'admin', 'bulk', '_BULK_DELETE', 'Elimina elementi selezionati', 0, 1),
(NOW(), 'it', 'admin', 'bulk', '_BULK_BUTTON', 'Esegui', 0, 1);

-- admin en

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'en', 'admin', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'en', 'admin', 'global', '_X3CMS_SLOGAN', 'The power of simplicity', 0, 1),
(NOW(), 'en', 'admin', 'global', '_WARNING', 'Warning!', 0, 1),
(NOW(), 'en', 'admin', 'global', '_CONGRATULATIONS', 'Congratulations!', 0, 1),

(NOW(), 'en', 'admin', 'global', '_MAINTENANCE_MODE', 'Manintenance mode', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DEVEL_MODE', 'Development mode', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DEBUG_MODE', 'Debug on', 0, 1),
(NOW(), 'en', 'admin', 'global', '_MSG_OK', 'Operation completed', 0, 1),
(NOW(), 'en', 'admin', 'global', '_MSG_ERROR', 'Warning! An error occurred.', 0, 1),

(NOW(), 'en', 'admin', 'global', '_FAILED_X_TIMES', 'The login failed XXX times.', 0, 1),
(NOW(), 'en', 'admin', 'global', '_FAILED_TOO_TIMES', 'The login failed too times.<br />Your account was tempoarily disabled.', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ADMIN_AREA', 'Administration', 0, 1),
(NOW(), 'en', 'admin', 'global', '_HI', 'Hi', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LAST_LOGIN', 'last login', 0, 1),
(NOW(), 'en', 'admin', 'global', '_USER', 'User', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LOGOUT', 'Logout', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ON', 'On', 0, 1),
(NOW(), 'en', 'admin', 'global', '_OFF', 'Off', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DELETE', 'Delete', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DELETE_BULK', 'Delete selected', 0, 1),
(NOW(), 'en', 'admin', 'global', '_UPDATE_BULK', 'Update selected', 0, 1),
(NOW(), 'en', 'admin', 'global', '_STATUS', 'Status', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LOCKED', 'Locked', 0, 1),
(NOW(), 'en', 'admin', 'global', '_UNLOCKED', 'Unlocked', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ACTIONS', 'Actions', 0, 1),
(NOW(), 'en', 'admin', 'global', '_MOVE_UP', 'Move up', 0, 1),
(NOW(), 'en', 'admin', 'global', '_UP', 'Up', 0, 1),
(NOW(), 'en', 'admin', 'global', '_MOVE_DOWN', 'Move down', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DOWN', 'Down', 0, 1),
(NOW(), 'en', 'admin', 'global', '_EDIT', 'Edit', 0, 1),
(NOW(), 'en', 'admin', 'global', '_DUPLICATE', 'Duplicate', 0, 1),
(NOW(), 'en', 'admin', 'global', '_COPY_OF', 'copy of', 0, 1),
(NOW(), 'en', 'admin', 'global', '_GO_BACK', 'Go back', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LANGUAGE', 'language', 0, 1),
(NOW(), 'en', 'admin', 'global', '_PAGE', 'page', 0, 1),
(NOW(), 'en', 'admin', 'global', '_AREA', 'Area', 0, 1),
(NOW(), 'en', 'admin', 'global', '_NO_ITEMS', 'No items found', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LAST_UPGRADE', 'Last refresh', 0, 1),
(NOW(), 'en', 'admin', 'global', '_CLOSE', 'Close', 0, 1),
(NOW(), 'en', 'admin', 'global', '_PERMISSIONS', 'Permissions', 0, 1),
(NOW(), 'en', 'admin', 'global', '_CONFIG', 'Configure', 0, 1),
(NOW(), 'en', 'admin', 'global', '_INSTALL', 'Install', 0, 1),
(NOW(), 'en', 'admin', 'global', '_UNINSTALL', 'uninstall', 0, 1),
(NOW(), 'en', 'admin', 'global', '_FOUND', 'Found', 0, 1),
(NOW(), 'en', 'admin', 'global', '_PAGES', 'pages', 0, 1),
(NOW(), 'en', 'admin', 'global', '_FIRST_PAGE', 'first page', 0, 1),
(NOW(), 'en', 'admin', 'global', '_LAST_PAGE', 'last page', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ITEMS', 'items', 0, 1),
(NOW(), 'en', 'admin', 'global', '_IN', 'in', 0, 1),
(NOW(), 'en', 'admin', 'global', '_NEXT', 'next page', 0, 1),
(NOW(), 'en', 'admin', 'global', '_PREVIOUS', 'previous page', 0, 1),
(NOW(), 'en', 'admin', 'global', '_SWITCH_LANGUAGE', 'Switch language', 0, 1),
(NOW(), 'en', 'admin', 'global', '_SWITCH_AREA', 'Switch area', 0, 1),
(NOW(), 'en', 'admin', 'global', '_FIND', 'Find', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ORDERABLE_MSG', 'You can order items with drag and drop', 0, 1),
(NOW(), 'en', 'admin', 'global', '_UNSAVED_CHANGES', 'There are unsaved changes', 0, 1),
(NOW(), 'en', 'admin', 'global', '_CANCEL', 'Cancel', 0, 1),
(NOW(), 'en', 'admin', 'global', '_GENERATE', 'Generate', 0, 1),
(NOW(), 'en', 'admin', 'global', '_SETTINGS', 'Settings', 0, 1),
(NOW(), 'en', 'admin', 'global', '_PLUGINS', 'Plugins', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ALL_PLUGINS', 'All plugins', 0, 1),
(NOW(), 'en', 'admin', 'global', '_HIDDEN', 'Hidden element', 0, 1),
(NOW(), 'en', 'admin', 'global', '_VISIBLE', 'Visible element', 0, 1),
(NOW(), 'en', 'admin', 'global', '_ENTER_TO_FILTER', 'hit enter to filter', 0, 1),

(NOW(), 'en', 'admin', 'global', '_FILTER', 'Filter', 0, 1),
(NOW(), 'en', 'admin', 'global', '_FILTER_MSG', 'Insert a text to filter', 0, 1),

(NOW(), 'en', 'admin', 'global', '_SET_ALL', 'Set for all', 0, 1),
(NOW(), 'en', 'admin', 'global', '_SET_ALL_MSG', 'This action will be applied only to visible items', 0, 1),

(NOW(), 'en', 'admin', 'home', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'en', 'admin', 'home', '_PUBLIC_SIDE', 'Public side', 0, 1),
(NOW(), 'en', 'admin', 'home', '_LOGGED_AS', 'Logged as', 0, 1),
(NOW(), 'en', 'admin', 'home', '_SETTINGS', 'Settings', 0, 1),
(NOW(), 'en', 'admin', 'home', '_WIDGETS', 'Widgets', 0, 1),
(NOW(), 'en', 'admin', 'home', '_RELOAD', 'Reload', 0, 1),
(NOW(), 'en', 'admin', 'home', '_BOOKMARKS', 'Bookmarks', 0, 1),
(NOW(), 'en', 'admin', 'home', '_PROFILE', 'Profile', 0, 1),
(NOW(), 'en', 'admin', 'home', '_HELP_ON_LINE', 'Help on line', 0, 1),
(NOW(), 'en', 'admin', 'home', '_ABOUT', 'About', 0, 1),
(NOW(), 'en', 'admin', 'home', '_NOTICES_AND_UPDATES', 'Notices and updates', 0, 1),
(NOW(), 'en', 'admin', 'home', '_UNABLE_TO_CONNECT', 'Unable to connect to remote server.', 0, 1),

(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_MANAGER', 'Widgets manager', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_ITEMS', 'Widgets', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_ADD', 'Add a new widget', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_NEW', 'New widget', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_NEW_MSG', 'You can set a widget only one time', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_DELETE', 'Delete widget', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_WIDGETS_AVAILABLE', 'Available widgets', 0, 1),
(NOW(), 'en', 'admin', 'widgets', '_NO_WIDGETS_TO_SET', 'There are not widgets available', 0, 1),

(NOW(), 'en', 'admin', 'files', '_CATEGORY', 'Category', 0, 1),
(NOW(), 'en', 'admin', 'files', '_ADD_FILE', 'Add a new file', 0, 1),
(NOW(), 'en', 'admin', 'files', '_NEW_FILE', 'New file', 0, 1),
(NOW(), 'en', 'admin', 'files', '_UPLOAD_FILE', 'File upload', 0, 1),
(NOW(), 'en', 'admin', 'files', '_FILE_LIST', 'Files', 0, 1),
(NOW(), 'en', 'admin', 'files', '_FILE', 'File', 0, 1),
(NOW(), 'en', 'admin', 'files', '_FILE_SIZES', 'dimensions for images/documents', 0, 1),
(NOW(), 'en', 'admin', 'files', '_DELETE_FILE', 'Delete file', 0, 1),
(NOW(), 'en', 'admin', 'files', '_AREA_LIST', 'Areas list', 0, 1),
(NOW(), 'en', 'admin', 'files', '_REFRESH_FILES', 'Refresh files list', 0, 1),
(NOW(), 'en', 'admin', 'files', '_SUBCATEGORY', 'Subcategory', 0, 1),
(NOW(), 'en', 'admin', 'files', '_FILE_TREE', 'Files tree', 0, 1),
(NOW(), 'en', 'admin', 'files', '_EDIT_FILE', 'Edit file', 0, 1),
(NOW(), 'en', 'admin', 'files', '_SWITCH_CATEGORY', 'Switch category', 0, 1),
(NOW(), 'en', 'admin', 'files', '_SWITCH_SUBCATEGORY', 'Switch sub-category', 0, 1),
(NOW(), 'en', 'admin', 'files', '_FILE_FILTER', 'File filter', 0, 1),
(NOW(), 'en', 'admin', 'files', '_SELECT_ALL', 'Select all', 0, 1),
(NOW(), 'en', 'admin', 'files', '_UNCATEGORIZED', 'uncategorized', 0, 1),

(NOW(), 'en', 'admin', 'files', '_IMAGES', 'images', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_EDIT', 'Edit image', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_XCOORD', 'X coord', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_YCOORD', 'Y coord', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_WIDTH', 'Width', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_HEIGHT', 'Height', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_LOCK_RATIO', 'Lock ratio', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_ROTATE', 'Rotate', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_CONTRAST', 'Contrast', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_BRIGHTNESS', 'Brightness', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_SATURATION', 'Saturation', 0, 1),
(NOW(), 'en', 'admin', 'files', '_IMAGE_AS_NEW', 'Save as new', 0, 1),

(NOW(), 'en', 'admin', 'files', '_FFMPEG_NOT_FOUND', 'FFMPEG the command required for the operation is not available on the server', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_SWF_MSG', 'Vector graphic can\'t be converted in other formats', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_EDIT', 'Edit video', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_EDIT_MSG', '<p>Some video formats are not supported by some browser or platforms:</p><ul><li>Chromium (Google Chrome for Linux requires chromium-codecs-ffmpeg-extra) and Opera (only from release 25) support MP4</li><li>Internet Explorer do not support WEBM and OGV</li><li>Safari do not support WEBM and OGV</li></ul>', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_FORMAT', 'Video format', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_FORMAT_MSG', 'the conversion of big files could slow down your web site', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_GET_IMAGE', 'Extract an image from the video', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_SEC', 'Frame to use', 0, 1),
(NOW(), 'en', 'admin', 'files', '_VIDEO_SEC_MSG', 'automatically selected on stop', 0, 1),
(NOW(), 'en', 'admin', 'files', '_MEDIA', 'media', 0, 1),
(NOW(), 'en', 'admin', 'files', '_DOCUMENTS', 'generic files', 0, 1),
(NOW(), 'en', 'admin', 'files', '_TEMPLATES', 'templates', 0, 1),
(NOW(), 'en', 'admin', 'files', '_TEMPLATES_EDIT', 'Edit template', 0, 1),
(NOW(), 'en', 'admin', 'files', '_TEMPLATES_MSG', 'A template can\'t contain script', 0, 1),
(NOW(), 'en', 'admin', 'files', '_ALL_FILES', 'all files', 0, 1),
(NOW(), 'en', 'admin', 'files', '_SWITCH_TYPE', 'Switch file type', 0, 1),
(NOW(), 'en', 'admin', 'files', '_DROP_MSG', 'Please drop your files here', 0, 1),
(NOW(), 'en', 'admin', 'files', '_TEXT_EDIT', 'Edit text file', 0, 1),


(NOW(), 'en', 'admin', 'msg', '_PAGE_NOT_FOUND', 'Page not available.', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_NOT_PERMITTED', 'You don\'t have the right permission for this operation', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_NOT_WRITEABLE', 'You do not have write permissions on this', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_UPLOAD_ERROR', 'An error occurred during uploading.', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_USER_ALREADY_EXISTS', 'An user with the same name already exixts', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_USER_CANNOT_BE_MODIFIED', 'This user cannot be modified', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_PAGE_ALREADY_EXISTS', 'A page with the same URL already exixts', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_AREA_ALREADY_EXISTS', 'An area with the same name already exists', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_FILE_SIZE_IS_TOO_BIG', 'The size in kilobyte of uploading file is too big', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_IMAGE_SIZE_IS_TOO_BIG', 'The size in pixel of uploading file is too big', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_IMAGE_SIZE_IS_TOO_SMALL', 'The size in pixel of uploading file is too small', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_XKEY_ALREADY_EXISTS', 'A word with the same key already exixts', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_LANGUAGE_ALREADY_EXISTS', 'This language already exixts', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_CATEGORY_ALREADY_EXISTS', 'This category already exists', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_CONTEXT_ALREADY_EXISTS', 'This context already exists', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_BAD_MIMETYPE', 'The file format you are trying to upload is not allowed', 0, 1),

(NOW(), 'en', 'admin', 'msg', '_REQUIRED_PLUGIN', 'must be installed to install this plugin', 0, 1),
(NOW(), 'en', 'admin', 'msg', '_PLUGIN_NEEDED_BY', 'require this plugin', 0, 1),

(NOW(), 'en', 'admin', 'form', '_SESSION_EXPIRED', 'The session for filling out this form has expired.<br>Please reload the page and try again.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_FORM_NOT_VALID', 'One or more form\'s fields are wrong:', 0, 1),
(NOW(), 'en', 'admin', 'form', '_FORM_DUPLICATE', 'This form was already submitted.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_REQUIRED', 'is a required field.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_REQUIREDIF', 'is a requiref field if "XXXRELATEDXXX" is set to "XXXVALUEXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_A_VALUE_OTHER_THAN', 'a value other than ', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_VALUE', 'is an invalid value.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_MAIL', 'is not a valid email address.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_URL', 'is not a valid URL.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_DEPENDS', 'depends on an empty field "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_CONTAINS', 'must contain at least XXXVALUEXXX times the string "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_IFEMPTY', 'is mandatory if "XXXRELATEDXXX" is empty.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INARRAY', 'depends on a value not selected.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NOTINARRAY', 'the selected value "XXXVALUEXXX" can\'t be in "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TOO_SHORT', 'is too short, min length is [XXXRELATEDXXX].', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_EQUAL', 'is different from "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_DIFFERENT', 'must be different from "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_NUMERIC', 'must be numeric', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_A_DATE', 'expected XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TOO_LONG', 'is too long, max length is [XXXRELATEDXXX].', 0, 1),
(NOW(), 'en', 'admin', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'the size in pixel of uploading file is too big', 0, 1),
(NOW(), 'en', 'admin', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'the weight in kilobyte of uploading file is too big', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'can contain only numbers', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_ONLY', 'can contain only "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_ALPHABETIC', 'can contain only alphabetic chars', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_ALPHANUMERIC', 'can contains only alphanumeric', 0, 1),

(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_UPPERCASE', 'it must contain at least one upper case letter', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_LOWERCASE', 'it must contain at least one lower case letter', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_NUMBER', 'it must contain at least one number', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_CONTAIN_SYMBOL', 'it must contain at least one non-alphanumeric character (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'it must contain only numbers, letters and symbols', 0, 1),

(NOW(), 'en', 'admin', 'form', '_MUST_BE_A_TIME', 'must be a time HH:MM format', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_A_TIMER', 'must be a number of hours and minutes in H:MM format', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_A_DATETIME', 'must be a date time in XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_PIVA', 'must be an italian fiscal id', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_CF', 'must be an italian personal fiscal id', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_FISCAL_ID', 'must be a fiscal id', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_A_PERIODICAL', 'must be a string consisting of a number followed by one of the following: year, month, week, day, hour', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_AFTER', 'must be a later date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_AFTER_OR_EQUAL', 'must be a later date or at least equal than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_BEFORE', 'must be an earlier date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'must be an earlier or at less equal date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_WRONG_LENGTH', 'wrong length (XXXRELATEDXXX)', 0, 1),
(NOW(), 'en', 'admin', 'form', '_GREATER_THAN', 'have to be greater than "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'admin', 'form', '_LOWER_THAN', 'have to be lower than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_IS_NOT_A_VALID_COLOR', 'is not a valid color', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_IBAN', 'invalid IBAN code', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_EAN', 'invalid EAN code', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INVALID_DIRECTORY', 'the folder not exists', 0, 1),
(NOW(), 'en', 'admin', 'form', '_REQUIRED_PLUGIN', 'is required and not installed.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_ALREADY_INSTALLED', 'is already installed.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MISSING_PLUGIN_INSTALLER', 'missing plugin installer.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_PLUGIN_NOT_INSTALLED', 'An error occurred during plugin installing', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INCOMPATIBLE_PLUGIN', 'This plugin is not compatible with this version of X3CMS', 0, 1),
(NOW(), 'en', 'admin', 'form', '_INCOMPATIBLE_AREA', 'This plugin is installable only in these areas', 0, 1),
(NOW(), 'en', 'admin', 'form', '_PLUGIN_NOT_UNINSTALLED', 'An error occurred during plugin uninstalling', 0, 1),
(NOW(), 'en', 'admin', 'form', '_THEME_NOT_INSTALLED', 'An error occurred during theme installing', 0, 1),
(NOW(), 'en', 'admin', 'form', '_THEME_NOT_UNINSTALLED', 'An error occurred during theme uninstalling', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TEMPLATE_INSTALLER_NOT_FOUND', 'Template installer not found', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TEMPLATE_NOT_INSTALLED', 'An error occurred during template installing', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TEMPLATE_NOT_UNINSTALLED', 'An error occurred during template uninstalling', 0, 1),
(NOW(), 'en', 'admin', 'form', '_DEFAULT_TEMPLATE_CANT_BE_UNINSTALLED', 'The default template can\'t be uninstalled', 0, 1),
(NOW(), 'en', 'admin', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'en', 'admin', 'form', '_UPLOAD_PROGRESS', 'Upload progress', 0, 1),
(NOW(), 'en', 'admin', 'form', '_CATEGORY', 'Category', 0, 1),
(NOW(), 'en', 'admin', 'form', '_SUBCATEGORY', 'Subcategory', 0, 1),
(NOW(), 'en', 'admin', 'form', '_FILE', 'File', 0, 1),
(NOW(), 'en', 'admin', 'form', '_COMMENT', 'Caption', 0, 1),
(NOW(), 'en', 'admin', 'form', '_RESET', 'Reset', 0, 1),
(NOW(), 'en', 'admin', 'form', '_SUBMIT', 'Submit', 0, 1),
(NOW(), 'en', 'admin', 'form', '_SEARCH', 'Search', 0, 1),
(NOW(), 'en', 'admin', 'form', '_SEND', 'Send', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NO', 'No', 0, 1),
(NOW(), 'en', 'admin', 'form', '_YES', 'Yes', 0, 1),
(NOW(), 'en', 'admin', 'form', '_ASSIGN_PERMISSIONS', 'Set permissions', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NONEP', 'None', 0, 1),
(NOW(), 'en', 'admin', 'form', '_READP', 'Reader', 0, 1),
(NOW(), 'en', 'admin', 'form', '_WRITEP', 'Writer', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MANAGEP', 'Manager', 0, 1),
(NOW(), 'en', 'admin', 'form', '_ADMINP', 'Admnistrator', 0, 1),
(NOW(), 'en', 'admin', 'form', '_ARE_YOU_SURE_DELETE', 'Are you sure to delete', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NAME', 'Name', 0, 1),
(NOW(), 'en', 'admin', 'form', '_MAIL', 'Email', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TITLE', 'Title', 0, 1),
(NOW(), 'en', 'admin', 'form', '_DESCRIPTION', 'Description', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NOTES', 'Notes', 0, 1),
(NOW(), 'en', 'admin', 'form', '_ADDRESS', 'address', 0, 1),
(NOW(), 'en', 'admin', 'form', '_KEYS', 'Keys', 0, 1),
(NOW(), 'en', 'admin', 'form', '_NOT_IN_MAP', 'Don\'t show in site map', 0, 1),
(NOW(), 'en', 'admin', 'form', '_IMG', 'Image', 0, 1),
(NOW(), 'en', 'admin', 'form', '_DATA_IN', 'Data', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TAGS', 'Tags', 0, 1),
(NOW(), 'en', 'admin', 'form', '_AUTHOR', 'Author', 0, 1),
(NOW(), 'en', 'admin', 'form', '_CONTENT', 'Contents', 0, 1),
(NOW(), 'en', 'admin', 'form', '_LEVEL_RULES', 'Reader: can only read, Writer: can read and write, Manager: can read, write, enable and disable, Administrator: like manager plus lock and delete power', 0, 1),
(NOW(), 'en', 'admin', 'form', '_CAPTCHA_ERROR', 'Wrong captcha.', 0, 1),
(NOW(), 'en', 'admin', 'form', '_SELECT_COLOR', 'Pick a color', 0, 1),
(NOW(), 'en', 'admin', 'form', '_TO_CHANGE', 'To changes', 0, 1),

(NOW(), 'en', 'admin', 'sites', '_SETTINGS_MANAGER', 'Settings manager', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_SETTINGS_MANAGER_MSG', 'Here you can configure the structure and the behaviour of your X3CMS', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_SITE_MANAGER', 'Site manager', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_ONLINE', 'On Line', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_OFFLINE', 'Off Line', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_KEYCODE', 'License code', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_DOMAIN', 'Domain', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_DATABASE', 'Database', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_DATABASE_MSG', 'For alternative domains', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_SITE_CONFIG', 'Site config', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_ADD_SITE', 'Add a new site', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_EDIT_SITE', 'Edit site', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_CLEAR_CACHE', 'Clear your cache', 0, 1),
(NOW(), 'en', 'admin', 'sites', '_VERSION', 'X3CMS version', 0, 1),

(NOW(), 'en', 'admin', 'info', '_SITE_INFO', 'Informations', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_SERVER', 'Server', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_KEY', 'Data', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_VALUE', 'Value', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_APACHE', 'Loaded Apache modules', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_MYSQL', 'MySQL', 0, 1),
(NOW(), 'en', 'admin', 'info', '_INFO_PHP', 'Loaded PHP exstension', 0, 1),


(NOW(), 'en', 'admin', 'help', '_HELP', 'User Guide', 0, 1),
(NOW(), 'en', 'admin', 'help', '_HELP_MSG', 'If your CMS X3 provides a personalized guide the links to individual pages of the guide will be displayed below this text.<br />In any case, you can consult the <b>Help On Line</b>.', 0, 1),
(NOW(), 'en', 'admin', 'help', '_HELP_ON_LINE', 'Help On Line', 0, 1),
(NOW(), 'en', 'admin', 'help', '_HELP_ON_SITE', 'Help on Site', 0, 1),


(NOW(), 'en', 'admin', 'login', '_UNSUPPORTED_BROWSER', 'This browser is not supported', 0, 1),
(NOW(), 'en', 'admin', 'login', '_SUPPORTED_BROWSER', 'Update your browser to the latest version and choose one of the supported browsers', 0, 1),
(NOW(), 'en', 'admin', 'login', '_LOGIN', 'Login', 0, 1),
(NOW(), 'en', 'admin', 'login', '_USERNAME', 'Username', 0, 1),
(NOW(), 'en', 'admin', 'login', '_MAIL', 'Your email address', 0, 1),
(NOW(), 'en', 'admin', 'login', '_PASSWORD', 'Password', 0, 1),
(NOW(), 'en', 'admin', 'login', '_REMEMBER_ME', 'Remember me on this computer', 0, 1),
(NOW(), 'en', 'admin', 'login', '_CAPTCHA', 'Antispam control', 0, 1),
(NOW(), 'en', 'admin', 'login', '_RELOAD_CAPTCHA', 'Reload the image if it is unreadable', 0, 1),
(NOW(), 'en', 'admin', 'login', '_CAPTCHA_MSG', 'Write the text below', 0, 1),
(NOW(), 'en', 'admin', 'login', '_RESET_PWD_TITLE', 'Password recovery', 0, 1),
(NOW(), 'en', 'admin', 'login', '_RESET_PWD', 'Forgot your password?', 0, 1),
(NOW(), 'en', 'admin', 'login', '_RESET_MSG', 'An email with instructions on retrieving your password will be sent to you', 0, 1),


(NOW(), 'en', 'admin', 'pwd_recovery', '_RECOVERY_SUBJECT', 'Password recovery', 0, 1),
(NOW(), 'en', 'admin', 'pwd_recovery', '_RECOVERY_BODY_CONFIRM', 'We received a request to recover the password associated with this account.<br />To confirm your request follow this link <a href="XXXLINKXXX" title="reset password on XXXDOMAINXXX">XXXLINKXXX</a>', 0, 1),
(NOW(), 'en', 'admin', 'pwd_recovery', '_RECOVERY_BODY_RESET', 'As you requested your password has been reset.<br />Here are your new login parameters: <li>Username: XXXUSERNAMEXXX</li> <li>Password: XXXPASSWORDXXX</li></ul>', 0, 1),
(NOW(), 'en', 'admin', 'pwd_recovery', '_RECOVERY_PWD_OK', 'An email with your new login credentials have been sent to your e-mail.', 0, 1),
(NOW(), 'en', 'admin', 'pwd_recovery', '_RECOVERY_PWD_ERROR', 'The email address you entered is not in the database.<br />Check and try again.<br />Thank you', 0, 1),


(NOW(), 'en', 'admin', 'lang', '_LANGUAGE', 'Language', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_LANG_AREAS_FOR', 'Language areas', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_AREAS', 'Areas', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_AREA', 'Area', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_WORDS', 'Words', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_TRANSLATION', 'Translations', 0, 1),
(NOW(), 'en', 'admin', 'lang', '_SWITCH_LANGUAGE', 'Switch language', 0, 1),

(NOW(), 'en', 'admin', 'languages', '_LANGUAGES_MANAGER', 'Languages manager', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_ADD_LANG', 'Add a language', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_EDIT_LANG', 'Edit language', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_DELETE_LANG', 'Delete language', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_SHOW_LANG_KEYS', 'Show language keys', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_NEW_LANG', 'New language', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_CODE', 'Code', 0, 1),
(NOW(), 'en', 'admin', 'languages', '_RTL_LANGUAGE', 'Right to Left language', 0, 1),


(NOW(), 'en', 'admin', 'menus', '_MENUS', 'Men&ugrave;', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_MENU_LIST', 'Menu list', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_ADD_MENU', 'Add a menu', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_NEW_MENU', 'New menu', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_EDIT_MENU', 'Edit menu', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_DELETE_MENU', 'Delete menu', 0, 1),


(NOW(), 'en', 'admin', 'menus', '_BOOKMARKS', 'Bookmarks', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_BOOKMARKS_NAME', 'Bookmark name', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_BOOKMARKS_ADD', 'Add the current page', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_BOOKMARKS_DELETE', 'Delete bookmark', 0, 1),
(NOW(), 'en', 'admin', 'menus', '_BOOKMARKS_ALREADY_EXISTS', 'Bookmark already in', 0, 1),


(NOW(), 'en', 'admin', 'pages', '_PAGE_LIST', 'Pages list', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_ADD_PAGE', 'Add a new page', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_NEW_PAGE', 'New page', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_MENU', 'Menu', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_SUBMENU', 'Submenu', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_SUBPAGES', 'subpages', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_MENU_AND_ORDER', 'Page position', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_SEO_TOOLS', 'SEO Tools', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_HISTORY', 'History', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_NO_SUBPAGES', 'This page don\'t has subpages', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_FROM_PAGE', 'Parent page', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_TEMPLATE', 'Template', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_URL', 'page URL', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_DELETE_PAGE', 'Delete page', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_INIZIALIZE_AREA', 'Initialize area', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_SITE_MAP', 'Site map', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_FAKE_PAGE', 'Fake page', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_FAKE_PAGE_MSG', 'Only a menu item', 0, 1),

(NOW(), 'en', 'admin', 'pages', '_ROBOT', 'Rule for meta ROBOTS', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_ROBOT_MSG', 'if empty will be used "index,follow"', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_REDIRECT_CODE', 'Redirect code', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_REDIRECT', 'Redirect URL', 0, 1),
(NOW(), 'en', 'admin', 'pages', '_REDIRECT_MSG', 'insert the old URL the full version or the relative version if in the same domain', 0, 1),

(NOW(), 'en', 'admin', 'articles', '_ARTICLE_LIST', 'Articles list', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLES', 'Articles', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ADD_ARTICLE', 'Add a new article', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_NEW_ARTICLE', 'New article', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_EDIT_ARTICLE', 'Edit article', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_DELETE_ARTICLE', 'Delete article', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLE_HISTORY', 'Article history', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_HISTORY', 'History', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_LATEST_ARTICLES', 'Latest articles', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ALPHABETICAL_ORDER', 'Alphabetical order', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_KEY_ORDER', 'Ordered by key', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT_ORDER', 'Ordered by context', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CATEGORY_ORDER', 'Ordered by category', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_AUTHOR_ORDER', 'Ordered by author', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_PAGE', 'Page', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_AUTHOR', 'Author', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CATEGORY', 'Category', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT', 'Context', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SEARCH_BY_TEXT', 'Search by title or content', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_KEY', 'Key', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_VIEW_ARTICLES', 'View articles', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_START_DATE', 'Start date', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_END_DATE', 'End date', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_NO_END_MSG', 'Blank for no end', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTENTS', 'Contents', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SCRIPT', 'Script', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SCRIPT_MSG', 'all the scripts included in the contents will be removed in order to avoid unwanted inclusions. Any scripts, including the opening and closing tags, must be inserted here', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_MODULE', 'Module', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_PARAM', 'Parameter', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLE_PARAM_SETTING', 'Parameter setting', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLE_PARAM_SETTING_NOT_REQUIRED', 'No parameter is required for this plugin', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLE_PARAM_DEFAULT_MSG', 'Select an option', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLE_PARAM_OPTIONS', 'Plugin options', 0, 1),

(NOW(), 'en', 'admin', 'articles', '_CONTENT_EDITOR', 'Page editor', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT', 'Context', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SCHEMA', 'Article schema', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_TIME_WINDOW', 'Time window', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ORGANIZATION', 'Cataloging of the article', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_PLUGIN', 'Plugin', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_OPTIONS', 'Article options', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SHOW_AUTHOR', 'Show author', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SHOW_TAGS', 'Show tags', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SHOW_DATE', 'Show date', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_SHOW_ACTIONS', 'Show actions', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT_DRAFTS', 'Drafts', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT_PAGES', 'Pages', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_CONTEXT_MULTI', 'Multipages', 0, 1),
(NOW(), 'en', 'admin', 'articles', '_ARTICLES_SEARCH_MSG', 'Search by title, content and tags', 0, 1),


(NOW(), 'en', 'admin', 'categories', '_CATEGORY_LIST', 'Category list', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_CATEGORIES', 'Categories', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_CATEGORY_TAG', 'Category tag', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_CATEGORY_TAG_MSG', 'only for internal use, useful to group categories for different things', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_ADD_CATEGORY', 'Add a new category', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_NEW_CATEGORY', 'New category', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_EDIT_CATEGORY', 'Edit category', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_DELETE_CATEGORY', 'Delete category', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_ALL_TAGS', 'All tags', 0, 1),
(NOW(), 'en', 'admin', 'categories', '_NO_CATEGORY_TAG', 'No tag', 0, 1),


(NOW(), 'en', 'admin', 'contexts', '_CONTEXT_LIST', 'Context list', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_CONTEXT_MSG', 'With contexts you can group articles to simplify the management and the organization of your contents', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_CONTEXTS', 'Contexts', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_ADD_CONTEXT', 'Add a new context', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_NEW_CONTEXT', 'New context', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_EDIT_CONTEXT', 'Edit context', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_DELETE_CONTEXT', 'Delete context', 0, 1),
(NOW(), 'en', 'admin', 'contexts', '_DEFAULT_CONTEXTS_MSG', 'Default contexts are not modifiable', 0, 1),


(NOW(), 'en', 'admin', 'sections', '_SECTIONS', 'Sections', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTIONS_SORT_MSG', 'Only sections below the orange line are sortable', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION', 'Section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_NEW', 'New section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_EDIT', 'Edit section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_EDIT_MSG', 'NOTE: columns settings will update after saving', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_DELETE', 'Delete section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_SETTINGS', 'Configuration', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_SIZES_AND_COLORS', 'Section sizes and colors', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_COLUMNS_STYLE', 'Columns colors and styles', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_BACKGROUND', 'Background color', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_IMG_H', 'Backgrond image horizontal', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_IMG_V', 'Background image vertical', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_FOREGROUND', 'Foreground color', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_COLUMNS', 'Number of columns', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_COL_SIZES', 'Column subdivision', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_COL_SIZES_MSG', 'TO get 2 columns 2/3+1/3 set 3 columns and 2+1 as subdivision', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_STATUS', 'Section status', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_UNLOCKED', 'Unlocked section: you can set parameters', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_LOCKED', 'Locked section: the theme will handle all parameter of this section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_WIDTH', 'Section width', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_FULL_WIDTH', 'full width', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_FIXED_WIDTH', 'fixed width from theme settings', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_HEIGHT', 'Height: free or fullscreen', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_STYLE', 'Theme style', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_STYLE_MSG', 'select one style from the theme', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_CLASS', 'CSS classe for section', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTION_CLASS_MSG', 'insert one or more CSS classes', 0, 1),

(NOW(), 'en', 'admin', 'sections', '_COMPOSE_EDITOR', 'Page composer', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_ARTICLES_LIST', 'Available articles', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_ARTICLES_MSG', 'Drag the items in the areas of content', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_SECTIONS_MSG', 'Drag the items to sort or remove', 0, 1),
(NOW(), 'en', 'admin', 'sections', '_DROP_HERE', 'Drop here', 0, 1),


(NOW(), 'en', 'admin', 'history', '_HISTORY_LIST', 'Pages - history:', 0, 1),
(NOW(), 'en', 'admin', 'history', '_PREVIEW', 'Preview', 0, 1),
(NOW(), 'en', 'admin', 'history', '_EDIT_DATE', 'Edit date', 0, 1),
(NOW(), 'en', 'admin', 'history', '_UNDEFINED', 'undefined', 0, 1),
(NOW(), 'en', 'admin', 'history', '_DELETE_VERSION', 'Delete version', 0, 1),
(NOW(), 'en', 'admin', 'history', '_SET_DATE', 'Visualization date', 0, 1),
(NOW(), 'en', 'admin', 'history', '_LEAVE_EMPTY_FOR_UNDEFINED', 'Leave blank to set undefined ', 0, 1),


(NOW(), 'en', 'admin', 'areas', '_AREA_LIST', 'Areas list', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_ADD_AREA', 'Add an area', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_NEW_AREA', 'New area', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_ENABLED_LANGUAGES', 'Enabled languages', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_DEFAULT_LANG', 'Default language', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_DELETE_AREA', 'Delete area', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_AREA_LANG_LIST', 'Areas - languages list', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_AREA_LANG_MAP', 'Area map', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_EDIT_AREA', 'Edit area', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_SEO_DATA', 'SEO data', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_PRIVATE', 'Private area', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_FOLDER', 'Folder', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_DOMAIN', 'Domain', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_DEFAULT_AREA', 'Default area for plugins', 0, 1),
(NOW(), 'en', 'admin', 'areas', '_DEFAULT_AREA_MSG', 'Is useful to keep all plugins linked each other in one area', 0, 1),

(NOW(), 'en', 'admin', 'dictionary', '_KEY', 'Key', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_KEYS_LIST', 'Keys list', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_ADD_WORD', 'Add a word', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_NEW_WORD', 'New word', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_EDIT_WORD', 'Edit word', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_DELETE_WORD', 'Delete word', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_SHOW_WORDS', 'Show words', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_WORD', 'Word', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_IMPORT_INTO', 'Import in', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_IMPORT_KEYS', 'Import keys', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_SECTION', 'Section', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_SECTIONS_LIST', 'Sections list', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_WORDS_LIST', 'Words list', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_DICTIONARY_SEARCH_MSG', 'Search by key in any language', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_DICTIONARY_SEARCH_RESULT', 'Results of the search', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_DICTIONARY_DELETE_DUPLICATES', 'Delete duplicates', 0, 1),
(NOW(), 'en', 'admin', 'dictionary', '_DICTIONARY_DELETE_DUPLICATES_MSG', 'all duplicates inside each section', 0, 1),

(NOW(), 'en', 'admin', 'users', '_GROUP', 'Group', 0, 1),
(NOW(), 'en', 'admin', 'users', '_USERS_LIST', 'Users list', 0, 1),
(NOW(), 'en', 'admin', 'users', '_USER_VIEW', 'User detail', 0, 1),
(NOW(), 'en', 'admin', 'users', '_ADD_USER', 'Add an user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_NEW_USER', 'New user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_EDIT_USER', 'Edit user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_EDIT_PROFILE', 'Edit your profile', 0, 1),
(NOW(), 'en', 'admin', 'users', '_DELETE_USER', 'Delete user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_EMAIL', 'Email', 0, 1),
(NOW(), 'en', 'admin', 'users', '_PHONE', 'Phone', 0, 1),
(NOW(), 'en', 'admin', 'users', '_REPEAT_PASSWORD', 'Repeat password', 0, 1),
(NOW(), 'en', 'admin', 'users', '_LEVEL', 'User level', 0, 1),
(NOW(), 'en', 'admin', 'users', '_USERNAME_RULE', 'At least 6 alphanumeric chars', 0, 1),
(NOW(), 'en', 'admin', 'users', '_PASSWORD_RULE', 'At least 6 alphanumeric plus symbols chars', 0, 1),
(NOW(), 'en', 'admin', 'users', '_USER_DETAIL', 'User detail', 0, 1),
(NOW(), 'en', 'admin', 'users', '_MAIL_USER', 'Send an email to user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_PASSWORD_CHANGE_MSG', 'Leave blank so as not to change', 0, 1),
(NOW(), 'en', 'admin', 'users', '_HIDE_USER', 'Hidden user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_SHOW_USER', 'Visible user', 0, 1),
(NOW(), 'en', 'admin', 'users', '_DOMAIN', 'Enabled areas', 0, 1),
(NOW(), 'en', 'admin', 'users', '_EDIT_PRIV', 'Edit permissions', 0, 1),
(NOW(), 'en', 'admin', 'users', '_EDIT_DETAIL_PRIV', 'Edit detailed permissions', 0, 1),
(NOW(), 'en', 'admin', 'users', '_ALL_DETAIL_PRIV', 'Set the same for all', 0, 1),
(NOW(), 'en', 'admin', 'users', '_GLOBAL_PRIVS', 'Global permissions', 0, 1),
(NOW(), 'en', 'admin', 'users', '_RESET_PRIVS', 'Reset permissions', 0, 1),
(NOW(), 'en', 'admin', 'users', '_RESET_PRIVS_MSG', 'Delete all settings and sync privs with group privs', 0, 1),
(NOW(), 'en', 'admin', 'users', '_REFACTORY', 'Refactory permissions', 0, 1),
(NOW(), 'en', 'admin', 'users', '_REFACTORY_MSG', 'Sync with group privs, create privs on new objects and keep settings', 0, 1),
(NOW(), 'en', 'admin', 'users', '_TABLE', 'Table', 0, 1),


(NOW(), 'en', 'admin', 'profile', '_SUBJECT_PROFILE', 'Updated profile on DOMAIN', 0, 1),
(NOW(), 'en', 'admin', 'profile', '_MSG_PROFILE', "Dear user,\nkeep this email as a memo of your account on DOMAIN \n\nLogin data \nUsername: USERNAME \nPassword: PASSWORD \n\nThanks", 0, 1),


(NOW(), 'en', 'admin', 'modules', '_AREA_LIST', 'Areas list', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_MODULE_LIST', 'Plugins list', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_MODULE', 'Plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_INSTALLED_PLUGINS', 'Installed plugins', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_INSTALLABLE_PLUGINS', 'Installable plugins', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_MODULE', 'Plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_UNINSTALL_PLUGIN', 'Uninstall plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_ARE_YOU_SURE_UNINSTALL', 'Are you sure to uninstall the plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_MODULE_CONFIG', 'Configure plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_PARAM', 'Parameter plugin', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_MODULE_INSTRUCTIONS', 'Plugin instructions', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_INSTRUCTIONS', 'Istructions', 0, 1),
(NOW(), 'en', 'admin', 'modules', '_PLUGGABLE', 'Pluggable plugin', 0, 1),

(NOW(), 'en', 'admin', 'groups', '_ADD_GROUP', 'Add a group', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_EDIT_GROUP', 'Edit group', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_NEW_GROUP', 'New group', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_DELETE_GROUP', 'Delete group', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_GROUP_LIST', 'Groups list', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_EDIT_GPRIV', 'Edit group\'s permissions', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_GPRIV', 'Permissions', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_GROUP_PERMISSION', 'Group permissions', 0, 1),


(NOW(), 'en', 'admin', 'groups', '_AREA_CREATION', 'Area creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_ARTICLE_CREATION', 'Article creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_CATEGORY_CREATION', 'Category creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_CONTEXT_CREATION', 'Context creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_FILE_UPLOAD', 'Upload files', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_GROUP_CREATION', 'Group creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_KEY_CREATION', 'Key creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_KEY_IMPORT', 'Key importation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_LANGUAGE_CREATION', 'New languages', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_MENU_CREATION', 'Men&ugrave; creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_MODULE_INSTALL', 'Plugin installation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_MODULE_UNINSTALL', 'Plugin uninstall', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_PAGE_CREATION', 'Page creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_SECTION_CREATION', 'Section creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_SITE_CREATION', 'Site creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_TEMPLATE_INSTALL', 'Template installation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_THEME_INSTALL', 'Theme installation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_USER_CREATION', 'User creation', 0, 1),
(NOW(), 'en', 'admin', 'groups', '_WORD_CREATION', 'Word insert', 0, 1),


(NOW(), 'en', 'admin', 'groups', 'AREAS', 'Areas management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'ARTICLES', 'Articles management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'CATEGORIES', 'Categories management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'CONTENTS', 'Contents management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'CONTEXTS', 'Contexts management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'DICTIONARY', 'Dictionary management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'FILES', 'Files management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'XGROUPS', 'Groups management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'LANGUAGES', 'Language management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'LOGS_DATA', 'Logs management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'MENUS', 'Menus management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'MODULES', 'Plugins management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'PAGES', 'Pages management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'PRIVS', 'Permissions management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'SECTIONS', 'Sections management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'SITES', 'Site management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'TEMPLATES', 'Template management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'THEMES', 'Themes management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'USERS', 'Users management', 0, 1),
(NOW(), 'en', 'admin', 'groups', 'WIDGETS', 'Widgets management', 0, 1),


(NOW(), 'en', 'admin', 'themes', '_THEME_LIST', 'Themes list', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME', 'Theme', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_TEMPLATES', 'Template', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_MENUS', 'Men&ugrave;', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_MINIMIZE', 'CSS and  JS Minifier', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_INSTALLED_THEMES', 'Installed themes', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_INSTALLABLE_THEMES', 'Installable themes', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_UNINSTALL_THEME', 'Uninstall theme', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_ARE_YOU_SURE_UNINSTALL', 'Are you sure to uninstall the theme', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME_WHAT', 'Section or article', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME_EDIT', 'Edit theme styles', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME_EDIT_MSG', 'Those options are available only with ADVANCED EDITING', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME_STYLE', 'Style', 0, 1),
(NOW(), 'en', 'admin', 'themes', '_THEME_STYLE_MSG', 'insert classes from theme CSS or from CSS framework', 0, 1),


(NOW(), 'en', 'admin', 'templates', '_TEMPLATE_LIST', 'Templates list', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_TEMPLATE', 'Template', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_INVALID_TEMPLATE', 'The Template name can\'t contains space.', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_CSS', 'Related style sheet', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_SECTIONS_SETTINGS', 'Sections settings', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_INSTALL_TEMPLATE', 'Install template', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_INSTALLED_TEMPLATES', 'Installed templates', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_INSTALLABLE_TEMPLATES', 'Installable templates', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_UNINSTALL_TEMPLATE', 'Uninstall template', 0, 1),
(NOW(), 'en', 'admin', 'templates', '_ARE_YOU_SURE_UNINSTALL', 'Are you ute to uninstall the template', 0, 1),

(NOW(), 'en', 'admin', 'time', '_TIME_DAYS_AGO', 'days ago', 0, 1),
(NOW(), 'en', 'admin', 'time', '_TIME_HOURS_AGO', 'hours ago', 0, 1),
(NOW(), 'en', 'admin', 'time', '_TIME_MINUTES_AGO', 'minutes ago', 0, 1),
(NOW(), 'en', 'admin', 'time', '_TIME_SECONDS_AGO', 'seconds ago', 0, 1),

(NOW(), 'en', 'admin', 'bulk', '_BULK_ACTION', 'Action', 0, 1),
(NOW(), 'en', 'admin', 'bulk', '_BULK_COPY', 'Copy selected', 0, 1),
(NOW(), 'en', 'admin', 'bulk', '_BULK_MOVE', 'Move selected', 0, 1),
(NOW(), 'en', 'admin', 'bulk', '_BULK_DESTINATION', 'Destination', 0, 1),
(NOW(), 'en', 'admin', 'bulk', '_BULK_DELETE', 'Delete selected', 0, 1),
(NOW(), 'en', 'admin', 'bulk', '_BULK_BUTTON', 'Execute', 0, 1);


-- public it

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'it', 'public', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'it', 'public', 'global', '_X3CMS_SLOGAN', 'La forza della semplicità', 0, 1),
(NOW(), 'it', 'public', 'global', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'it', 'public', 'global', '_WARNING', 'Attenzione!', 0, 1),
(NOW(), 'it', 'public', 'global', '_CONGRATULATIONS', 'Complimenti!', 0, 1),

(NOW(), 'it', 'public', 'global', '_MSG_OK', 'Operazione completata', 0, 1),
(NOW(), 'it', 'public', 'global', '_MSG_ERROR', 'Si &egrave; verificato un errore.', 0, 1),

(NOW(), 'it', 'public', 'global', '_GLOBAL_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'public', 'global', '_PARENT_LEVEL', 'Livello superiore', 0, 1),
(NOW(), 'it', 'public', 'global', '_TAGS', 'Tags', 0, 1),
(NOW(), 'it', 'public', 'global', '_TAG', 'Tag', 0, 1),
(NOW(), 'it', 'public', 'global', '_FOUND', 'Trovato', 0, 1),
(NOW(), 'it', 'public', 'global', '_PAGES', 'pagine', 0, 1),
(NOW(), 'it', 'public', 'global', '_PAGE', 'pagina', 0, 1),
(NOW(), 'it', 'public', 'global', '_NEXT', 'pagina successiva', 0, 1),
(NOW(), 'it', 'public', 'global', '_PREVIOUS', 'pagina precedente', 0, 1),
(NOW(), 'it', 'public', 'global', '_FIRST_PAGE', 'prima pagina', 0, 1),
(NOW(), 'it', 'public', 'global', '_LAST_PAGE', 'ultima pagina', 0, 1),
(NOW(), 'it', 'public', 'global', '_ITEMS', 'elementi', 0, 1),
(NOW(), 'it', 'public', 'global', '_NO_ITEMS', 'Nessun elemento trovato', 0, 1),
(NOW(), 'it', 'public', 'global', '_IN', 'in', 0, 1),
(NOW(), 'it', 'public', 'global', '_CLOSE', 'Chiudi', 0, 1),
(NOW(), 'it', 'public', 'global', '_CANCEL', 'Annulla', 0, 1),

(NOW(), 'it', 'public', 'global', '_PHONE', 'Telefono', 0, 1),
(NOW(), 'it', 'public', 'global', '_MOBILE', 'Cellulare', 0, 1),
(NOW(), 'it', 'public', 'global', '_FAX', 'Fax', 0, 1),
(NOW(), 'it', 'public', 'global', '_EMAIL', 'Email', 0, 1),

(NOW(), 'it', 'public', 'global', '_ADMIN', 'Amministrazione', 0, 1),
(NOW(), 'it', 'public', 'global', '_GOTO_ADMIN', 'Vai al pannello di amministrazione', 0, 1),
(NOW(), 'it', 'public', 'global', '_EDIT', 'Modifica', 0, 1),
(NOW(), 'it', 'public', 'global', '_EDIT_ARTICLE', 'Modifica articolo', 0, 1),
(NOW(), 'it', 'public', 'global', '_PRINTFRIENDLY', 'Stampa una versione ottimizzata di questa pagina o genera un PDF', 0, 1),

(NOW(), 'it', 'public', 'global', '_X3CMS_PWA_INSTALL', 'Installa', 0, 1),
(NOW(), 'it', 'public', 'global', '_X3CMS_PWA_OFFLINE_MSG', 'Sei Offline', 0, 1),

(NOW(), 'it', 'public', 'msg', '_UNKNOW_ERROR', 'Si &egrave; verificato un errore.', 0, 1),
(NOW(), 'it', 'public', 'msg', '_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'public', 'msg', '_OFFLINE', 'Sito in manutenzione<br />Ci scusiamo per il disagio.', 0, 1),
(NOW(), 'it', 'public', 'msg', '_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'public', 'msg', '_UPLOAD_ERROR', 'Si &egrave; verificato un errore durante l\'upload del file.', 0, 1),
(NOW(), 'it', 'public', 'msg', '_FILE_SIZE_IS_TOO_BIG', 'La dimensione, in Kilobyte, del file di cui si cerca di fare l\'upload sono superiori al consentito', 0, 1),
(NOW(), 'it', 'public', 'msg', '_IMAGE_SIZE_IS_TOO_BIG', 'Le dimensioni, in pixel, del file di cui si cerca di fare l\'upload sono superiori al consentito', 0, 1),
(NOW(), 'it', 'public', 'msg', '_IMAGE_SIZE_IS_TOO_SMALL', 'Le dimensioni, in pixel, del file di cui si cerca di fare l\'upload sono inferiori al consentito', 0, 1),
(NOW(), 'it', 'public', 'msg', '_BAD_MIMETYPE', 'Il formato di file che si cerca di caricare non &egrave; ammesso', 0, 1),


(NOW(), 'it', 'public', 'search', '_SEARCH_RESULT', 'Risultato della ricerca', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_OF', 'Ricerca di', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_MSG_SEARCH_EMPTY', 'Impossibile eseguire la ricerca.<br />Verificate e riprovate.<br />Grazie.', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_ZERO_RESULT', 'La ricerca non ha prodotto nessun risultato.', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_FOUND', 'Sono stati trovati', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_ITEMS', 'elementi', 0, 1),
(NOW(), 'it', 'public', 'search', '_SEARCH_PAGES', 'Trovato nelle pagine:', 0, 1),

(NOW(), 'it', 'public', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'it', 'public', 'form', '_FORM_NOT_VALID', 'Uno o pi&ugrave; campi del form non sono compilati correttamente:', 0, 1),
(NOW(), 'it', 'public', 'form', '_FORM_DUPLICATE', 'Questo form &egrave; gi&agrave; stato registrato.', 0, 1),

(NOW(), 'it', 'public', 'form', '_SESSION_EXPIRED', 'La sessione per la compilazione di questo modulo è scaduta.<br>Ricaricate la pagina e riprovate.', 0, 1),
(NOW(), 'it', 'public', 'form', '_REQUIRED', '&egrave; un campo obbligatorio.', 0, 1),
(NOW(), 'it', 'public', 'form', '_REQUIREDIF', '&egrave; obbligatorio se impostate "XXXRELATEDXXX" a "XXXVALUEXXX".', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_VALUE', 'non &egrave; un valore ammesso.', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_MAIL', 'non &egrave; un indirizzo email valido.', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_URL', 'non &egrave; un URL valido.', 0, 1),
(NOW(), 'it', 'public', 'form', '_INARRAY', 'dipende da un valore che non avete selezionato.', 0, 1),
(NOW(), 'it', 'public', 'form', '_NOTINARRAY', 'il valore selezionato "XXXVALUEXXX" non può essere contenuto in "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_DEPENDS', 'dipende da un campo che non avete settato "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'public', 'form', '_CONTAINS', 'deve contenere almeno XXXVALUEXXX volte la stringa "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'public', 'form', '_IFEMPTY', '&egrave; obbligatorio se lasciate "XXXRELATEDXXX" vuoto.', 0, 1),

(NOW(), 'it', 'public', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'le dimensioni, in pixel, sono superiori al consentito', 0, 1),
(NOW(), 'it', 'public', 'form', '_IMAGE_SIZE_IS_TOO_SMALL', 'le dimensioni, in pixel, sono inferiori al consentito', 0, 1),
(NOW(), 'it', 'public', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'le dimensioni, in Kilobyte, sono superiori al consentito', 0, 1),

(NOW(), 'it', 'public', 'form', '_WRONG_LENGTH', 'numero di caratteri sbagliato (XXXRELATEDXXX)', 0, 1),
(NOW(), 'it', 'public', 'form', '_TOO_SHORT', 'ha una lunghezza inferiore a quella richiesta [XXXRELATEDXXX].', 0, 1),
(NOW(), 'it', 'public', 'form', '_TOO_LONG', 'ha una lunghezza superiore a quella richiesta [XXXRELATEDXXX].', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_EQUAL', 'non coincide con "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_DIFFERENT', 'deve essere diverso da "XXXRELATEDXXX".', 0, 1),

(NOW(), 'it', 'public', 'form', '_GREATER_THAN', 'deve essere maggiore di "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'public', 'form', '_LOWER_THAN', 'deve essere minore di "XXXRELATEDXXX"', 0, 1),

(NOW(), 'it', 'public', 'form', '_MUST_BE_NUMERIC', 'deve essere un numero', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'deve contenere solo numeri', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_A_DATE', 'deve essere una data nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'public', 'form', '_CAPTCHA_ERROR', 'Il codice di controllo con corrisponde.', 0, 1),

(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_ONLY', 'deve contenere solo "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_ALPHABETIC', 'deve contenere solo lettere dell\'alfabeto', 0, 1),

(NOW(), 'it', 'public', 'form', '_MUST_BE_ALPHANUMERIC', 'deve contenere solo numeri e lettere', 0, 1),

(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_UPPERCASE', 'deve contenere almeno una lettera maiuscola', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_LOWERCASE', 'deve contenere almeno una lettera minuscola', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_NUMBER', 'deve contenere almeno un numero', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_CONTAIN_SYMBOL', 'deve contenere almeno un carattere non alphanumerico (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'deve contenere solo numeri, lettere e simboli', 0, 1),

(NOW(), 'it', 'public', 'form', '_MUST_BE_A_TIME', 'deve essere un orario nel formato HH:MM', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_A_TIMER', 'deve essere un numero di ore e minuti nel formato H:MM', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_A_DATETIME', 'deve essere una data con orario nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_PIVA', 'deve essere un numero di partita iva valido', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_CF', 'deve essere un codice fiscale valido', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_FISCAL_ID', 'deve essere un identificativo fiscale valido', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_A_PERIODICAL', 'deve essere una stringa formata da un numero seguito da uno dei seguenti termini: year, month, week, day, hour', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_AFTER', 'deve essere data successiva a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_AFTER_OR_EQUAL', 'deve essere una data successiva o al limite uguale a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_BEFORE', 'deve essere data precedente a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'deve essere una data precedente o al limite uguale a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_IBAN', 'il codice IBAN inserito non &egrave; valido', 0, 1),
(NOW(), 'it', 'public', 'form', '_INVALID_DIRECTORY', 'la cartella specificata non esiste', 0, 1),

(NOW(), 'it', 'public', 'form', '_CAPTCHA', 'Controllo antispam', 0, 1),
(NOW(), 'it', 'public', 'form', '_RELOAD_CAPTCHA', 'Cambia l\'immagine se risulta illegibile', 0, 1),
(NOW(), 'it', 'public', 'form', '_CASE_SENSITIVE', 'Scrivi il testo sovrastante', 0, 1),

(NOW(), 'it', 'public', 'form', '_RESET', 'Annulla', 0, 1),
(NOW(), 'it', 'public', 'form', '_SUBMIT', 'Registra', 0, 1),
(NOW(), 'it', 'public', 'form', '_SEARCH', 'Cerca', 0, 1),
(NOW(), 'it', 'public', 'form', '_NO', 'No', 0, 1),
(NOW(), 'it', 'public', 'form', '_YES', 'Si', 0, 1),

(NOW(), 'it', 'public', 'calendar', '_JANUARY', 'Gennaio', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_FEBRUARY', 'Febbraio', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_MARCH', 'Marzo', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_APRIL', 'Aprile', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_MAY', 'Maggio', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_JUNE', 'Giugno', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_JULY', 'Luglio', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_AUGUST', 'Agosto', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_SEPTEMBER', 'Settembre', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_OCTOBER', 'Ottobre', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_NOVEMBER', 'Novembre', 0, 1),
(NOW(), 'it', 'public', 'calendar', '_DECEMBER', 'Dicembre', 0, 1),

(NOW(), 'it', 'public', 'cal', '_JAN', 'Gen', 0, 1),
(NOW(), 'it', 'public', 'cal', '_FEB', 'Feb', 0, 1),
(NOW(), 'it', 'public', 'cal', '_MAR', 'Mar', 0, 1),
(NOW(), 'it', 'public', 'cal', '_APR', 'Apr', 0, 1),
(NOW(), 'it', 'public', 'cal', '_MAY', 'Mag', 0, 1),
(NOW(), 'it', 'public', 'cal', '_JUN', 'Giu', 0, 1),
(NOW(), 'it', 'public', 'cal', '_JUL', 'Lug', 0, 1),
(NOW(), 'it', 'public', 'cal', '_AUG', 'Ago', 0, 1),
(NOW(), 'it', 'public', 'cal', '_SEP', 'Set', 0, 1),
(NOW(), 'it', 'public', 'cal', '_OCT', 'Ott', 0, 1),
(NOW(), 'it', 'public', 'cal', '_NOV', 'Nov', 0, 1),
(NOW(), 'it', 'public', 'cal', '_DEC', 'Dic', 0, 1),

(NOW(), 'it', 'public', 'week_long', '_MONDAY', 'Lunedì', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_TUESDAY', 'Martedì', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_WEDNESDAY', 'Mercoledì', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_THURSDAY', 'Giovedì', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_FRIDAY', 'Venerdì', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_SATURDAY', 'Sabato', 0, 1),
(NOW(), 'it', 'public', 'week_long', '_SUNDAY', 'Domenica', 0, 1),


(NOW(), 'it', 'public', 'week', '_MON', 'Lun', 0, 1),
(NOW(), 'it', 'public', 'week', '_TUE', 'Mar', 0, 1),
(NOW(), 'it', 'public', 'week', '_WED', 'Mer', 0, 1),
(NOW(), 'it', 'public', 'week', '_THU', 'Gio', 0, 1),
(NOW(), 'it', 'public', 'week', '_FRI', 'Ven', 0, 1),
(NOW(), 'it', 'public', 'week', '_SAT', 'Sab', 0, 1),
(NOW(), 'it', 'public', 'week', '_SUN', 'Dom', 0, 1),

(NOW(), 'it', 'public', 'time', '_TIME_DAYS_AGO', 'giorni fa', 0, 1),
(NOW(), 'it', 'public', 'time', '_TIME_HOURS_AGO', 'ore fa', 0, 1),
(NOW(), 'it', 'public', 'time', '_TIME_MINUTES_AGO', 'minuti fa', 0, 1),
(NOW(), 'it', 'public', 'time', '_TIME_SECONDS_AGO', 'secondi fa', 0, 1);

-- public en

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'en', 'public', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'en', 'public', 'global', '_X3CMS_SLOGAN', 'The power of simplicity', 0, 1),
(NOW(), 'en', 'public', 'global', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'en', 'public', 'global', '_WARNING', 'Warning!!', 0, 1),
(NOW(), 'en', 'public', 'global', '_CONGRATULATIONS', 'Congratulations!', 0, 1),

(NOW(), 'en', 'public', 'global', '_MSG_OK', 'Operation completed', 0, 1),
(NOW(), 'en', 'public', 'global', '_MSG_ERROR', 'An error occurred.', 0, 1),

(NOW(), 'en', 'public', 'global', '_GLOBAL_PAGE_NOT_FOUND', 'This page is not available', 0, 1),
(NOW(), 'en', 'public', 'global', '_PARENT_LEVEL', 'Upper level', 0, 1),
(NOW(), 'en', 'public', 'global', '_TAGS', 'Tags', 0, 1),
(NOW(), 'en', 'public', 'global', '_TAG', 'Tag', 0, 1),
(NOW(), 'en', 'public', 'global', '_FOUND', 'Found', 0, 1),
(NOW(), 'en', 'public', 'global', '_PAGES', 'pages', 0, 1),
(NOW(), 'en', 'public', 'global', '_PAGE', 'page', 0, 1),
(NOW(), 'en', 'public', 'global', '_NEXT', 'next page', 0, 1),
(NOW(), 'en', 'public', 'global', '_PREVIOUS', 'previous page', 0, 1),
(NOW(), 'en', 'public', 'global', '_FIRST_PAGE', 'first page', 0, 1),
(NOW(), 'en', 'public', 'global', '_LAST_PAGE', 'last page', 0, 1),
(NOW(), 'en', 'public', 'global', '_ITEMS', 'items', 0, 1),
(NOW(), 'en', 'public', 'global', '_NO_ITEMS', 'No items found', 0, 1),
(NOW(), 'en', 'public', 'global', '_IN', 'in', 0, 1),
(NOW(), 'en', 'public', 'global', '_CLOSE', 'Close', 0, 1),
(NOW(), 'en', 'public', 'global', '_CANCEL', 'Cancle', 0, 1),

(NOW(), 'en', 'public', 'global', '_PHONE', 'Phone', 0, 1),
(NOW(), 'en', 'public', 'global', '_MOBILE', 'Mobile', 0, 1),
(NOW(), 'en', 'public', 'global', '_FAX', 'Fax', 0, 1),
(NOW(), 'en', 'public', 'global', '_EMAIL', 'Email', 0, 1),

(NOW(), 'en', 'public', 'global', '_ADMIN', 'Admin', 0, 1),
(NOW(), 'en', 'public', 'global', '_GOTO_ADMIN', 'Go to admin panel', 0, 1),
(NOW(), 'en', 'public', 'global', '_EDIT', 'Edit', 0, 1),
(NOW(), 'en', 'public', 'global', '_EDIT_ARTICLE', 'Edit article', 0, 1),
(NOW(), 'en', 'public', 'global', '_PRINTFRIENDLY', 'Print an optimized version of this web page or generate PDF', 0, 1),

(NOW(), 'en', 'public', 'global', '_X3CMS_PWA_INSTALL', 'Install', 0, 1),
(NOW(), 'en', 'public', 'global', '_X3CMS_PWA_OFFLINE_MSG', 'You are Offline', 0, 1),

(NOW(), 'en', 'public', 'msg', '_UNKNOW_ERROR', 'An error occurred', 0, 1),
(NOW(), 'en', 'public', 'msg', '_PAGE_NOT_FOUND', 'This page is not available', 0, 1),
(NOW(), 'en', 'public', 'msg', '_OFFLINE', 'Maintenance in progress<br />Sorry', 0, 1),
(NOW(), 'en', 'public', 'msg', '_UPLOAD_ERROR', 'An error occurred during uploading.', 0, 1),
(NOW(), 'en', 'public', 'msg', '_FILE_SIZE_IS_TOO_BIG', 'The size in kilobyte of uploading file is too big', 0, 1),
(NOW(), 'en', 'public', 'msg', '_IMAGE_SIZE_IS_TOO_BIG', 'The size in pixel of uploading file is too big', 0, 1),
(NOW(), 'en', 'public', 'msg', '_IMAGE_SIZE_IS_TOO_SMALL', 'The size in pixel of uploading file is too small', 0, 1),
(NOW(), 'en', 'public', 'msg', '_BAD_MIMETYPE', 'The file format you are trying to upload is not allowed', 0, 1),


(NOW(), 'en', 'public', 'search', '_SEARCH_RESULT', 'Search result', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_OF', 'Search of', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_MSG_SEARCH_EMPTY', 'Search not possible<br />Check and try again.<br />Please', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_ZERO_RESULT', 'No items found', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_FOUND', 'Found', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_ITEMS', 'items', 0, 1),
(NOW(), 'en', 'public', 'search', '_SEARCH_PAGES', 'Found in pages:', 0, 1),

(NOW(), 'en', 'public', 'form', '_SESSION_EXPIRED', 'The session for filling out this form has expired.<br>Please reload the page and try again.', 0, 1),
(NOW(), 'en', 'public', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'en', 'public', 'form', '_FORM_NOT_VALID', 'One or more fields are wrong:', 0, 1),
(NOW(), 'en', 'public', 'form', '_FORM_DUPLICATE', 'This form was already submitted.', 0, 1),

(NOW(), 'en', 'public', 'form', '_REQUIRED', 'is required.', 0, 1),
(NOW(), 'en', 'public', 'form', '_REQUIREDIF', 'is a requiref field if "XXXRELATEDXXX" is set to "XXXVALUEXXX".', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_VALUE', 'is an invalid value.', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_MAIL', 'invalid email address.', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_URL', 'invalid URL.', 0, 1),
(NOW(), 'en', 'public', 'form', '_INARRAY', 'depends on a value not selected.', 0, 1),
(NOW(), 'en', 'public', 'form', '_NOTINARRAY', 'the selected value "XXXVALUEXXX" can\'t be in "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_DEPENDS', 'depends on an empty field "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'public', 'form', '_CONTAINS', 'must contain at least XXXVALUEXXX times the string "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'public', 'form', '_IFEMPTY', 'is mandatory if "XXXRELATEDXXX" is empty.', 0, 1),

(NOW(), 'en', 'public', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'the size in pixel of uploading file is too big', 0, 1),
(NOW(), 'en', 'public', 'form', '_IMAGE_SIZE_IS_TOO_SMALL', 'the size in pixel of uploading file is too small', 0, 1),
(NOW(), 'en', 'public', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'the weight in kilobyte of uploading file is too big', 0, 1),

(NOW(), 'en', 'public', 'form', '_WRONG_LENGTH', 'wrong length (XXXRELATEDXXX)', 0, 1),
(NOW(), 'en', 'public', 'form', '_TOO_SHORT', 'is too short, min length is XXXRELATEDXXX.', 0, 1),
(NOW(), 'en', 'public', 'form', '_TOO_LONG', 'is too long, max length is XXXRELATEDXXX.', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_EQUAL', 'is different from "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_DIFFERENT', 'must be different from "XXXRELATEDXXX".', 0, 1),

(NOW(), 'en', 'public', 'form', '_GREATER_THAN', 'have to be greater than "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'public', 'form', '_LOWER_THAN', 'have to be lower than "XXXRELATEDXXX".', 0, 1),

(NOW(), 'en', 'public', 'form', '_MUST_BE_NUMERIC', 'must be numeric', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'can contain only numbers', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_A_DATE', 'expected XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'public', 'form', '_CAPTCHA_ERROR', 'captcha is wrong.', 0, 1),

(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_ONLY', 'can contain only "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_ALPHABETIC', 'can contain only alphabetic chars', 0, 1),

(NOW(), 'en', 'public', 'form', '_MUST_BE_ALPHANUMERIC', 'can contains only alphanumeric', 0, 1),

(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_UPPERCASE', 'it must contain at least one upper case letter', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_LOWERCASE', 'it must contain at least one lower case letter', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_NUMBER', 'it must contain at least one number', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_CONTAIN_SYMBOL', 'it must contain at least one non-alphanumeric character (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'it must contain only numbers, letters and symbols', 0, 1),

(NOW(), 'en', 'public', 'form', '_MUST_BE_A_TIME', 'must be a time HH:MM format', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_A_TIMER', 'must be a number of hours and minutes in H:MM format', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_A_DATETIME', 'must be a date time in XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_PIVA', 'must be an italian fiscal id', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_CF', 'must be an italian personal fiscal id', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_FISCAL_ID', 'must be a fiscal id', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_A_PERIODICAL', 'must be a string consisting of a number followed by one of the following: year, month, week, day, hour', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_AFTER', 'must be a later date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_AFTER_OR_EQUAL', 'must be a later date or at least equal than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_BEFORE', 'must be an earlier date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'must be an earlier or at less equal date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_IBAN', 'invalid IBAN code', 0, 1),
(NOW(), 'en', 'public', 'form', '_INVALID_DIRECTORY', 'the folder not exists', 0, 1),

(NOW(), 'en', 'public', 'form', '_CAPTCHA', 'Antispam control', 0, 1),
(NOW(), 'en', 'public', 'form', '_RELOAD_CAPTCHA', 'Change the image if it is unreadable', 0, 1),
(NOW(), 'en', 'public', 'form', '_CASE_SENSITIVE', 'Write the text above', 0, 1),

(NOW(), 'en', 'public', 'form', '_RESET', 'Reset', 0, 1),
(NOW(), 'en', 'public', 'form', '_SUBMIT', 'Submit', 0, 1),
(NOW(), 'en', 'public', 'form', '_SEARCH', 'Cerca', 0, 1),
(NOW(), 'en', 'public', 'form', '_NO', 'No', 0, 1),
(NOW(), 'en', 'public', 'form', '_YES', 'Yes', 0, 1),

(NOW(), 'en', 'public', 'calendar', '_JANUARY', 'January', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_FEBRUARY', 'February', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_MARCH', 'March', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_APRIL', 'April', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_MAY', 'May', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_JUNE', 'June', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_JULY', 'July', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_AUGUST', 'August', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_SEPTEMBER', 'September', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_OCTOBER', 'October', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_NOVEMBER', 'November', 0, 1),
(NOW(), 'en', 'public', 'calendar', '_DECEMBER', 'December', 0, 1),

(NOW(), 'en', 'public', 'cal', '_JAN', 'Jan', 0, 1),
(NOW(), 'en', 'public', 'cal', '_FEB', 'Feb', 0, 1),
(NOW(), 'en', 'public', 'cal', '_MAR', 'Mar', 0, 1),
(NOW(), 'en', 'public', 'cal', '_APR', 'Apr', 0, 1),
(NOW(), 'en', 'public', 'cal', '_MAY', 'May', 0, 1),
(NOW(), 'en', 'public', 'cal', '_JUN', 'Jun', 0, 1),
(NOW(), 'en', 'public', 'cal', '_JUL', 'Jul', 0, 1),
(NOW(), 'en', 'public', 'cal', '_AUG', 'Aug', 0, 1),
(NOW(), 'en', 'public', 'cal', '_SEP', 'Sep', 0, 1),
(NOW(), 'en', 'public', 'cal', '_OCT', 'Oct', 0, 1),
(NOW(), 'en', 'public', 'cal', '_NOV', 'Nov', 0, 1),
(NOW(), 'en', 'public', 'cal', '_DEC', 'Dec', 0, 1),

(NOW(), 'en', 'public', 'week_long', '_MONDAY', 'Monday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_TUESDAY', 'Tuesday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_WEDNESDAY', 'Wednesday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_THURSDAY', 'Thursday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_FRIDAY', 'Friday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_SATURDAY', 'Saturday', 0, 1),
(NOW(), 'en', 'public', 'week_long', '_SUNDAY', 'Sunday', 0, 1),


(NOW(), 'en', 'public', 'week', '_MON', 'Mon', 0, 1),
(NOW(), 'en', 'public', 'week', '_TUE', 'Tue', 0, 1),
(NOW(), 'en', 'public', 'week', '_WED', 'Wed', 0, 1),
(NOW(), 'en', 'public', 'week', '_THU', 'Thu', 0, 1),
(NOW(), 'en', 'public', 'week', '_FRI', 'Fri', 0, 1),
(NOW(), 'en', 'public', 'week', '_SAT', 'Sat', 0, 1),
(NOW(), 'en', 'public', 'week', '_SUN', 'Sun', 0, 1),

(NOW(), 'en', 'public', 'time', '_TIME_DAYS_AGO', 'days ago', 0, 1),
(NOW(), 'en', 'public', 'time', '_TIME_HOURS_AGO', 'hours ago', 0, 1),
(NOW(), 'en', 'public', 'time', '_TIME_MINUTES_AGO', 'minutes ago', 0, 1),
(NOW(), 'en', 'public', 'time', '_TIME_SECONDS_AGO', 'seconds ago', 0, 1);


-- private it

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'it', 'private', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'it', 'private', 'global', '_X3CMS_SLOGAN', 'La forza della semplicità', 0, 1),
(NOW(), 'it', 'private', 'global', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'it', 'private', 'global', '_WARNING', 'Attenzione!', 0, 1),
(NOW(), 'it', 'private', 'global', '_CONGRATULATIONS', 'Complimenti!', 0, 1),

(NOW(), 'it', 'private', 'global', '_MSG_OK', 'Operazione completata', 0, 1),
(NOW(), 'it', 'private', 'global', '_MSG_ERROR', 'Si &egrave; verificato un errore.', 0, 1),

(NOW(), 'it', 'private', 'global', '_GLOBAL_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'private', 'global', '_TAGS', 'Tags', 0, 1),
(NOW(), 'it', 'private', 'global', '_TAG', 'Tag', 0, 1),
(NOW(), 'it', 'private', 'global', '_FOUND', 'Trovato', 0, 1),
(NOW(), 'it', 'private', 'global', '_PAGES', 'pagine', 0, 1),
(NOW(), 'it', 'private', 'global', '_PAGE', 'pagina', 0, 1),
(NOW(), 'it', 'private', 'global', '_NEXT', 'pagina successiva', 0, 1),
(NOW(), 'it', 'private', 'global', '_PREVIOUS', 'pagina precedente', 0, 1),
(NOW(), 'it', 'private', 'global', '_FIRST_PAGE', 'prima pagina', 0, 1),
(NOW(), 'it', 'private', 'global', '_LAST_PAGE', 'ultima pagina', 0, 1),
(NOW(), 'it', 'private', 'global', '_ITEMS', 'elementi', 0, 1),
(NOW(), 'it', 'private', 'global', '_IN', 'in', 0, 1),
(NOW(), 'it', 'private', 'global', '_USER', 'Utente', 0, 1),
(NOW(), 'it', 'private', 'global', '_LAST_LOGIN', 'Ultimo accesso', 0, 1),

(NOW(), 'it', 'private', 'global', '_NO_ITEMS', 'Nessun elemento trovato', 0, 1),
(NOW(), 'it', 'private', 'global', '_CLOSE', 'Chiudi', 0, 1),
(NOW(), 'it', 'private', 'global', '_ACTIONS', 'Azioni', 0, 1),
(NOW(), 'it', 'private', 'global', '_EDIT', 'Modifica', 0, 1),
(NOW(), 'it', 'private', 'global', '_ON', 'On', 0, 1),
(NOW(), 'it', 'private', 'global', '_OFF', 'Off', 0, 1),
(NOW(), 'it', 'private', 'global', '_STATUS', 'Stato attuale', 0, 1),
(NOW(), 'it', 'private', 'global', '_DELETE', 'Elimina', 0, 1),

(NOW(), 'it', 'private', 'global', '_ADMIN', 'Amministrazione', 0, 1),
(NOW(), 'it', 'private', 'global', '_GOTO_ADMIN', 'Vai al pannello di amministrazione', 0, 1),
(NOW(), 'it', 'private', 'global', '_EDIT', 'Modifica', 0, 1),
(NOW(), 'it', 'private', 'global', '_EDIT_ARTICLE', 'Modifica articolo', 0, 1),
(NOW(), 'it', 'private', 'global', '_PRINTFRIENDLY', 'Stampa una versione ottimizzata di questa pagina o genera un PDF', 0, 1),

(NOW(), 'it', 'private', 'msg', '_UNKNOW_ERROR', 'Si &egrave; verificato un errore.', 0, 1),
(NOW(), 'it', 'private', 'msg', '_PAGE_NOT_FOUND', 'La pagina richiesta non &egrave; disponibile.', 0, 1),
(NOW(), 'it', 'private', 'msg', '_OFFLINE', 'Sito in manutenzione<br />Ci scusiamo per il disagio.', 0, 1),
(NOW(), 'it', 'private', 'msg', '_NOT_PERMITTED', 'Non avete i permessi necessari per eseguire l\'operazione richiesta', 0, 1),
(NOW(), 'it', 'private', 'msg', '_NOT_EXECUTABLE', 'Non potete eseguire questa operazione', 0, 1),

(NOW(), 'it', 'private', 'search', '_SEARCH_RESULT', 'Risultato della ricerca', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_OF', 'Ricerca di', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_MSG_SEARCH_EMPTY', 'Impossibile eseguire la ricerca.<br />Verificate e riprovate.<br />Grazie.', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_ZERO_RESULT', 'La ricerca non ha prodotto nessun risultato.', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_FOUND', 'Sono stati trovati', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_ITEMS', 'elementi', 0, 1),
(NOW(), 'it', 'private', 'search', '_SEARCH_PAGES', 'Trovato nelle pagine:', 0, 1),


(NOW(), 'it', 'private', 'login', '_LOGIN', 'Login', 0, 1),
(NOW(), 'it', 'private', 'login', '_USERNAME', 'Username', 0, 1),
(NOW(), 'it', 'private', 'login', '_PASSWORD', 'Password', 0, 1),
(NOW(), 'it', 'private', 'login', '_REMEMBER_ME', 'Ricordami su questo computer', 0, 1),

(NOW(), 'it', 'private', 'form', '_SESSION_EXPIRED', 'La sessione per la compilazione di questo modulo è scaduta.<br>Ricaricate la pagina e riprovate.', 0, 1),
(NOW(), 'it', 'private', 'form', '_FORM_NOT_VALID', 'Uno o pi&ugrave; campi del form non sono compilati correttamente:', 0, 1),
(NOW(), 'it', 'private', 'form', '_FORM_DUPLICATE', 'Questo form &egrave; gi&agrave; stato registrato.', 0, 1),
(NOW(), 'it', 'private', 'form', '_REQUIRED', '&egrave; un campo obbligatorio.', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_VALUE', 'non &egrave; un valore ammesso.', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_MAIL', 'non &egrave; un indirizzo email valido.', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_URL', 'non &egrave; un URL valido.', 0, 1),
(NOW(), 'it', 'private', 'form', '_DEPENDS', 'dipende da un campo che non avete settato.', 0, 1),
(NOW(), 'it', 'private', 'form', '_IFEMPTY', '&Egrave; obbligatorio se lasciate "XXXRELATEDXXX" vuoto.', 0, 1),
(NOW(), 'it', 'private', 'form', '_TOO_SHORT', 'ha una lunghezza inferiore a quella richiesta [XXXRELATEDXXX].', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_EQUAL', 'non coincide con "XXXRELATEDXXX".', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_NUMERIC', 'deve essere un numero', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_A_DATE', 'deve essere una data nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'private', 'form', '_TOO_LONG', 'ha una lunghezza superiore a quella richiesta [XXXRELATEDXXX].', 0, 1),
(NOW(), 'it', 'private', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'le dimensioni, in pixel, sono superiori al consentito', 0, 1),
(NOW(), 'it', 'private', 'form', '_IMAGE_SIZE_IS_TOO_SMALL', 'le dimensioni, in pixel, sono inferiori al consentito', 0, 1),
(NOW(), 'it', 'private', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'le dimensioni, in Kilobyte, sono superiori al consentito', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'deve contenere solo numeri', 0, 1),

(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_ONLY', 'deve contenere solo "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_ALPHABETIC', 'deve contenere solo lettere dell\'alfabeto', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_ALPHANUMERIC', 'deve contenere solo numeri e lettere', 0, 1),

(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_UPPERCASE', 'deve contenere almeno una lettera maiuscola', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_LOWERCASE', 'deve contenere almeno una lettera minuscola', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_NUMBER', 'deve contenere almeno un numero', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_CONTAIN_SYMBOL', 'deve contenere almeno un carattere non alphanumerico (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'deve contenere solo numeri, lettere e simboli', 0, 1),

(NOW(), 'it', 'private', 'form', '_MUST_BE_A_TIME', 'deve essere un orario nel formato HH:MM', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_A_TIMER', 'deve essere un numero di ore e minuti nel formato H:MM', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_A_DATETIME', 'deve essere una data con orario nel formato XXXVALUEXXX', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_PIVA', 'deve essere un numero di partita iva valido', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_CF', 'deve essere un codice fiscale valido', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_FISCAL_ID', 'deve essere un identificativo fiscale valido', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_A_PERIODICAL', 'deve essere una stringa formata da un numero seguito da uno dei seguenti termini: year, month, week, day, hour', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_AFTER', 'deve essere data successiva a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_BEFORE', 'deve essere data precedente a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'private', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'deve essere una data precedente o al limite uguale a "XXXRELATEDXXX"', 0, 1),
(NOW(), 'it', 'private', 'form', '_INVALID_IBAN', 'il codice IBAN inserito non &egrave; valido', 0, 1),

(NOW(), 'it', 'private', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'it', 'private', 'form', '_UPLOAD_PROGRESS', 'Avanzamento upload', 0, 1),
(NOW(), 'it', 'private', 'form', '_CATEGORY', 'Categoria', 0, 1),
(NOW(), 'it', 'private', 'form', '_SUBCATEGORY', 'Sottocategoria', 0, 1),
(NOW(), 'it', 'private', 'form', '_FILE', 'File', 0, 1),
(NOW(), 'it', 'private', 'form', '_COMMENT', 'Didascalia', 0, 1),
(NOW(), 'it', 'private', 'form', '_RESET', 'Annulla', 0, 1),
(NOW(), 'it', 'private', 'form', '_SUBMIT', 'Registra', 0, 1),
(NOW(), 'it', 'private', 'form', '_SEARCH', 'Cerca', 0, 1),
(NOW(), 'it', 'private', 'form', '_NO', 'No', 0, 1),
(NOW(), 'it', 'private', 'form', '_YES', 'Si', 0, 1),
(NOW(), 'it', 'private', 'form', '_ASSIGN_PERMISSIONS', 'Assegna i permessi', 0, 1),
(NOW(), 'it', 'private', 'form', '_NONEP', 'Niente', 0, 1),
(NOW(), 'it', 'private', 'form', '_READP', 'Lettura', 0, 1),
(NOW(), 'it', 'private', 'form', '_WRITEP', 'Scrittura', 0, 1),
(NOW(), 'it', 'private', 'form', '_MANAGEP', 'Gestione', 0, 1),
(NOW(), 'it', 'private', 'form', '_PRIVATEP', 'Amministrazione', 0, 1),
(NOW(), 'it', 'private', 'form', '_ARE_YOU_SURE_DELETE', 'Sei sicuro di voler eliminare', 0, 1),
(NOW(), 'it', 'private', 'form', '_NAME', 'Nome', 0, 1),
(NOW(), 'it', 'private', 'form', '_TITLE', 'Titolo', 0, 1),
(NOW(), 'it', 'private', 'form', '_DESCRIPTION', 'Descrizione', 0, 1),
(NOW(), 'it', 'private', 'form', '_KEYS', 'Chiavi', 0, 1),
(NOW(), 'it', 'private', 'form', '_IMG', 'Immagine', 0, 1),
(NOW(), 'it', 'private', 'form', '_DATA_IN', 'Data', 0, 1),
(NOW(), 'it', 'private', 'form', '_TAGS', 'Tags', 0, 1),
(NOW(), 'it', 'private', 'form', '_AUTHOR', 'Autore', 0, 1),
(NOW(), 'it', 'private', 'form', '_CONTENT', 'Contenuti', 0, 1),
(NOW(), 'it', 'private', 'form', '_LINK', 'Link', 0, 1),
(NOW(), 'it', 'private', 'form', '_COMPANY', 'Societa', 0, 1),

(NOW(), 'it', 'private', 'form', '_EMAIL', 'Email', 0, 1),
(NOW(), 'it', 'private', 'form', '_PHONE', 'Telefono', 0, 1),
(NOW(), 'it', 'private', 'form', '_REPEAT_PASSWORD', 'Ripeti password', 0, 1),
(NOW(), 'it', 'private', 'form', '_USERNAME_RULE', 'Almeno 6 caratteri alfanumerici', 0, 1),
(NOW(), 'it', 'private', 'form', '_PASSWORD_RULE', 'Almeno 6 caratteri alfanumerici e simboli', 0, 1),
(NOW(), 'it', 'private', 'form', '_PASSWORD_CHANGE_MSG', 'Lasciare vuoto se non si intende modificare la password', 0, 1),

(NOW(), 'it', 'private', 'form', '_CAPTCHA', 'Controllo antispam', 0, 1),
(NOW(), 'it', 'private', 'form', '_RELOAD_CAPTCHA', 'Cambia l\'immagine se risulta illegibile', 0, 1),
(NOW(), 'it', 'private', 'form', '_CASE_SENSITIVE', 'Scrivi il testo sovrastante', 0, 1),
(NOW(), 'it', 'private', 'form', '_CAPTCHA_ERROR', 'Il codice di controllo con corrisponde.', 0, 1);

-- private en

INSERT INTO `dictionary` (`updated`, `lang`, `area`, `what`, `xkey`, `xval`, `xlock`, `xon`) VALUES
(NOW(), 'en', 'private', 'global', '_X3CMS', 'X3CMS', 0, 1),
(NOW(), 'en', 'private', 'global', '_X3CMS_SLOGAN', 'The power of simplicity', 0, 1),
(NOW(), 'en', 'private', 'global', '_HOME_PAGE', 'Home page', 0, 1),
(NOW(), 'en', 'private', 'global', '_WARNING', 'Warning!', 0, 1),
(NOW(), 'en', 'private', 'global', '_CONGRATULATIONS', 'Congratulations!', 0, 1),

(NOW(), 'en', 'private', 'global', '_MSG_OK', 'Operation completed', 0, 1),
(NOW(), 'en', 'private', 'global', '_MSG_ERROR', 'An error occurred.', 0, 1),

(NOW(), 'en', 'private', 'global', '_GLOBAL_PAGE_NOT_FOUND', 'This page is not available.', 0, 1),
(NOW(), 'en', 'private', 'global', '_TAGS', 'Tags', 0, 1),
(NOW(), 'en', 'private', 'global', '_TAG', 'Tag', 0, 1),
(NOW(), 'en', 'private', 'global', '_FOUND', 'Found', 0, 1),
(NOW(), 'en', 'private', 'global', '_PAGES', 'pages', 0, 1),
(NOW(), 'en', 'private', 'global', '_PAGE', 'page', 0, 1),
(NOW(), 'en', 'private', 'global', '_NEXT', 'next page', 0, 1),
(NOW(), 'en', 'private', 'global', '_PREVIOUS', 'previous page', 0, 1),
(NOW(), 'en', 'private', 'global', '_FIRST_PAGE', 'first page', 0, 1),
(NOW(), 'en', 'private', 'global', '_LAST_PAGE', 'last page', 0, 1),
(NOW(), 'en', 'private', 'global', '_ITEMS', 'items', 0, 1),
(NOW(), 'en', 'private', 'global', '_IN', 'in', 0, 1),
(NOW(), 'en', 'private', 'global', '_USER', 'User', 0, 1),
(NOW(), 'en', 'private', 'global', '_LAST_LOGIN', 'Last login', 0, 1),

(NOW(), 'en', 'private', 'global', '_NO_ITEMS', 'No item found', 0, 1),
(NOW(), 'en', 'private', 'global', '_CLOSE', 'Close', 0, 1),
(NOW(), 'en', 'private', 'global', '_ACTIONS', 'Actions', 0, 1),
(NOW(), 'en', 'private', 'global', '_EDIT', 'Edit', 0, 1),
(NOW(), 'en', 'private', 'global', '_ON', 'On', 0, 1),
(NOW(), 'en', 'private', 'global', '_OFF', 'Off', 0, 1),
(NOW(), 'en', 'private', 'global', '_STATUS', 'Status', 0, 1),
(NOW(), 'en', 'private', 'global', '_DELETE', 'Delete', 0, 1),

(NOW(), 'en', 'private', 'global', '_ADMIN', 'Admin', 0, 1),
(NOW(), 'en', 'private', 'global', '_GOTO_ADMIN', 'Go to admin panel', 0, 1),
(NOW(), 'en', 'private', 'global', '_EDIT', 'Edit', 0, 1),
(NOW(), 'en', 'private', 'global', '_EDIT_ARTICLE', 'Edit article', 0, 1),
(NOW(), 'en', 'private', 'global', '_PRINTFRIENDLY', 'Print an optimized version of this web page or generate PDF', 0, 1),

(NOW(), 'en', 'private', 'msg', '_UNKNOW_ERROR', 'An error occurred.', 0, 1),
(NOW(), 'en', 'private', 'msg', '_PAGE_NOT_FOUND', 'This page is not available.', 0, 1),
(NOW(), 'en', 'private', 'msg', '_OFFLINE', 'Maintenance in progress<br />Sorry.', 0, 1),
(NOW(), 'en', 'private', 'msg', '_NOT_PERMITTED', 'You don\'t have the right permission for this operation', 0, 1),
(NOW(), 'en', 'private', 'msg', '_NOT_EXECUTABLE', 'You can\'t execute this operation', 0, 1),

(NOW(), 'en', 'private', 'search', '_SEARCH_RESULT', 'Search result', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_OF', 'Search of', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_MSG_SEARCH_EMPTY', 'Search not possible<br />Check and try again.<br />Please', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_ZERO_RESULT', 'No items found.', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_FOUND', 'Found', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_ITEMS', 'items', 0, 1),
(NOW(), 'en', 'private', 'search', '_SEARCH_PAGES', 'Found in pages:', 0, 1),


(NOW(), 'en', 'private', 'login', '_LOGIN', 'Login', 0, 1),
(NOW(), 'en', 'private', 'login', '_USERNAME', 'Username', 0, 1),
(NOW(), 'en', 'private', 'login', '_PASSWORD', 'Password', 0, 1),
(NOW(), 'en', 'private', 'login', '_REMEMBER_ME', 'Remember me on this computer', 0, 1),

(NOW(), 'en', 'private', 'form', '_SESSION_EXPIRED', 'The session for filling out this form has expired.<br>Please reload the page and try again.', 0, 1),
(NOW(), 'en', 'private', 'form', '_FORM_NOT_VALID', 'One or more fields are wrong:', 0, 1),
(NOW(), 'en', 'private', 'form', '_FORM_DUPLICATE', 'This form was already submitted.', 0, 1),
(NOW(), 'en', 'private', 'form', '_REQUIRED', 'is required.', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_VALUE', 'is an invalid value.', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_MAIL', 'invalid email address.', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_URL', 'invalid URL.', 0, 1),
(NOW(), 'en', 'private', 'form', '_DEPENDS', 'depends on an empty field.', 0, 1),
(NOW(), 'en', 'private', 'form', '_IFEMPTY', 'is mandatory if "XXXRELATEDXXX" is empty.', 0, 1),
(NOW(), 'en', 'private', 'form', '_TOO_SHORT', 'is too short.', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_EQUAL', 'is different from "XXXRELATEDXXX".', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_NUMERIC', 'must be numeric', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_A_DATE', 'expected XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'private', 'form', '_TOO_LONG', 'is too long.', 0, 1),
(NOW(), 'en', 'private', 'form', '_IMAGE_SIZE_IS_TOO_BIG', 'the size in pixel of uploading file is too big', 0, 1),
(NOW(), 'en', 'private', 'form', '_IMAGE_SIZE_IS_TOO_SMALL', 'the size in pixel of uploading file is too small', 0, 1),
(NOW(), 'en', 'private', 'form', '_FILE_WEIGHT_IS_TOO_BIG', 'the weight in kilobyte of uploading file is too big', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_ONLY_NUMBERS', 'can contain only numbers', 0, 1),

(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_ONLY', 'can contain only "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_ALPHABETIC', 'can contain only alphabetic chars', 0, 1),

(NOW(), 'en', 'private', 'form', '_MUST_BE_ALPHANUMERIC', 'can contains only alphanumeric', 0, 1),

(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_UPPERCASE', 'it must contain at least one upper case letter', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_LOWERCASE', 'it must contain at least one lower case letter', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_NUMBER', 'it must contain at least one number', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_CONTAIN_SYMBOL', 'it must contain at least one non-alphanumeric character (!"#$%&()*+,\-./:;<=>?@[]^_{|}~)', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_ALPHANUMERIC_PLUS_SYMBOLS', 'it must contain only numbers, letters and symbols', 0, 1),

(NOW(), 'en', 'private', 'form', '_MUST_BE_A_TIME', 'must be a time HH:MM format', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_A_TIMER', 'must be a number of hours and minutes in H:MM format', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_A_DATETIME', 'must be a date time in XXXVALUEXXX format', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_PIVA', 'must be an italian fiscal id', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_CF', 'must be an italian personal fiscal id', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_FISCAL_ID', 'must be a fiscal id', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_A_PERIODICAL', 'must be a string consisting of a number followed by one of the following: year, month, week, day, hour', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_AFTER', 'must be a later date ', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_BEFORE', 'must be an earlier date', 0, 1),
(NOW(), 'en', 'private', 'form', '_MUST_BE_BEFORE_OR_EQUAL', 'must be an earlier or at less equal date than "XXXRELATEDXXX"', 0, 1),
(NOW(), 'en', 'private', 'form', '_INVALID_IBAN', 'invalid IBAN code', 0, 1),

(NOW(), 'en', 'private', 'form', '_CHECKED', 'checked="checked"', 0, 1),
(NOW(), 'en', 'private', 'form', '_CATEGORY', 'Category', 0, 1),
(NOW(), 'en', 'private', 'form', '_SUBCATEGORY', 'Subcategory', 0, 1),
(NOW(), 'en', 'private', 'form', '_FILE', 'File', 0, 1),
(NOW(), 'en', 'private', 'form', '_COMMENT', 'Caption', 0, 1),
(NOW(), 'en', 'private', 'form', '_RESET', 'Reset', 0, 1),
(NOW(), 'en', 'private', 'form', '_SUBMIT', 'Submit', 0, 1),
(NOW(), 'en', 'private', 'form', '_SEARCH', 'Cerca', 0, 1),
(NOW(), 'en', 'private', 'form', '_NO', 'No', 0, 1),
(NOW(), 'en', 'private', 'form', '_YES', 'Yes', 0, 1),
(NOW(), 'en', 'private', 'form', '_ASSIGN_PERMISSIONS', 'Set permissions', 0, 1),
(NOW(), 'en', 'private', 'form', '_NONE', 'None', 0, 1),
(NOW(), 'en', 'private', 'form', '_READER', 'Reader', 0, 1),
(NOW(), 'en', 'private', 'form', '_WRITER', 'Writer', 0, 1),
(NOW(), 'en', 'private', 'form', '_MANAGER', 'Manager', 0, 1),
(NOW(), 'en', 'private', 'form', '_ADMINISTRATOR', 'Administrator', 0, 1),
(NOW(), 'en', 'private', 'form', '_ARE_YOU_SURE_DELETE', 'Are you sure to delete', 0, 1),
(NOW(), 'en', 'private', 'form', '_NAME', 'Name', 0, 1),
(NOW(), 'en', 'private', 'form', '_TITLE', 'Title', 0, 1),
(NOW(), 'en', 'private', 'form', '_DESCRIPTION', 'Description', 0, 1),
(NOW(), 'en', 'private', 'form', '_KEYS', 'Keys', 0, 1),
(NOW(), 'en', 'private', 'form', '_IMG', 'Image', 0, 1),
(NOW(), 'en', 'private', 'form', '_DATA_IN', 'Date', 0, 1),
(NOW(), 'en', 'private', 'form', '_TAGS', 'Tags', 0, 1),
(NOW(), 'en', 'private', 'form', '_AUTHOR', 'Author', 0, 1),
(NOW(), 'en', 'private', 'form', '_CONTENT', 'Contents', 0, 1),
(NOW(), 'en', 'private', 'form', '_LINK', 'Link', 0, 1),
(NOW(), 'en', 'private', 'form', '_COMPANY', 'Company', 0, 1),

(NOW(), 'en', 'private', 'form', '_EMAIL', 'Email', 0, 1),
(NOW(), 'en', 'private', 'form', '_PHONE', 'Phone', 0, 1),
(NOW(), 'en', 'private', 'form', '_REPEAT_PASSWORD', 'Repeat password', 0, 1),
(NOW(), 'en', 'private', 'form', '_USERNAME_RULE', 'At least 6 alphanumeric chars', 0, 1),
(NOW(), 'en', 'private', 'form', '_PASSWORD_RULE', 'At least 6 alphanumeric plus symbols chars', 0, 1),
(NOW(), 'en', 'private', 'form', '_PASSWORD_CHANGE_MSG', 'Leave blank for no change', 0, 1),

(NOW(), 'en', 'private', 'form', '_CAPTCHA', 'Antispam control', 0, 1),
(NOW(), 'en', 'private', 'form', '_RELOAD_CAPTCHA', 'Change the image if it is unreadable', 0, 1),
(NOW(), 'en', 'private', 'form', '_CASE_SENSITIVE', 'Write the text above', 0, 1),
(NOW(), 'en', 'private', 'form', '_CAPTCHA_ERROR', 'captcha is wrong.', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `xtype` tinyint(1) NOT NULL,
  `category` varchar(128) NOT NULL,
  `subcategory` varchar(128) NOT NULL,
  `name` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Table structure for table `gprivs`
--

CREATE TABLE IF NOT EXISTS `gprivs` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_group` int(11) NOT NULL,
  `what` varchar(64) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gprivs`
--

INSERT INTO `gprivs` (`updated`, `id_group`, `what`, `level`, `xon`) VALUES
(NOW(), 1, 'areas', 4, 1),
(NOW(), 1, 'articles', 4, 1),
(NOW(), 1, 'categories', 4, 1),
(NOW(), 1, 'contexts', 4, 1),
(NOW(), 1, 'dictionary', 4, 1),
(NOW(), 1, 'files', 4, 1),
(NOW(), 1, 'xgroups', 4, 1),
(NOW(), 1, 'languages', 4, 1),
(NOW(), 1, 'logs', 4, 1),
(NOW(), 1, 'menus', 4, 1),
(NOW(), 1, 'modules', 4, 1),
(NOW(), 1, 'pages', 4, 1),
(NOW(), 1, 'privs', 4, 1),
(NOW(), 1, 'sections', 4, 1),
(NOW(), 1, 'sites', 4, 1),
(NOW(), 1, 'templates', 4, 1),
(NOW(), 1, 'themes', 4, 1),
(NOW(), 1, 'users', 4, 1),
(NOW(), 1, 'widgets', 4, 1),

(NOW(), 1, '_area_creation', 4, 1),
(NOW(), 1, '_article_creation', 4, 1),
(NOW(), 1, '_category_creation', 4, 1),
(NOW(), 1, '_context_creation', 4, 1),
(NOW(), 1, '_file_upload', 4, 1),
(NOW(), 1, '_group_creation', 4, 1),
(NOW(), 1, '_language_creation', 4, 1),
(NOW(), 1, '_key_creation', 4, 1),
(NOW(), 1, '_key_import', 4, 1),
(NOW(), 1, '_menu_creation', 4, 1),
(NOW(), 1, '_module_install', 4, 1),
(NOW(), 1, '_module_uninstall', 4, 1),
(NOW(), 1, '_page_creation', 4, 1),
(NOW(), 1, '_section_creation', 4, 1),
(NOW(), 1, '_site_creation', 4, 1),
(NOW(), 1, '_template_install', 4, 1),
(NOW(), 1, '_theme_install', 4, 1),
(NOW(), 1, '_user_creation', 4, 1),
(NOW(), 1, '_word_creation', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `xgroups`
--

CREATE TABLE IF NOT EXISTS `xgroups` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `name` varchar(128) NOT NULL,
  `id_area` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `xgroups` (`id`, `updated`, `name`, `id_area`, `description`, `xlock`, `xon`) VALUES
(1, NOW(), 'admin', 1, 'Administrators group', 0, 1),
(2, NOW(), 'editor', 1, 'Editors group', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `language` varchar(32) NOT NULL,
  `code` char(2) NOT NULL,
  `rtl` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`updated`, `language`, `code`, `rtl`, `xlock`, `xon`) VALUES
(NOW(), 'italiano', 'it', 0, 0, 1),
(NOW(), 'english', 'en', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `name`, `description`, `xon`) VALUES
(1, 'Reader', 'only read', 1),
(2, 'Writer', 'read and write', 1),
(3, 'Manager', 'read, write and enable/disable', 1),
(4, 'Administrator', 'read, write, enable/disable, lock/unlock and delete', 1),
(5, 'God', 'everything and more', 1);

-- --------------------------------------------------------

--
-- Table structure for table `debug`
--

CREATE TABLE IF NOT EXISTS `debug` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `who` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `what` varchar(64) NOT NULL,
  `id_what`	int(11) NOT NULL,
  `memo` text NOT NULL,
  `extra` varchar(255) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `who` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `what` varchar(64) NOT NULL,
  `id_what`	int(11) NOT NULL,
  `memo` text NOT NULL,
  `extra` varchar(255) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE IF NOT EXISTS `matches` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `xfrom` varchar(255) NOT NULL,
  `id_from` int(11) NOT NULL,
  `xto` varchar(255) NOT NULL,
  `id_to` int(11) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_theme` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`updated`, `id_theme`, `name`, `description`, `xlock`, `xon`) VALUES
(NOW(), 1, 'user_menu', 'User menu', 1, 1),
(NOW(), 1, 'admin_global', 'Global menu', 1, 0),
(NOW(), 1, 'sidebar', 'Left menu', 0, 1),
(NOW(), 2, 'menu_top', 'Top menu', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `configurable` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `searchable` tinyint(1) NOT NULL,
  `mappable` tinyint(1) NOT NULL,
  `widget` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `pluggable` tinyint(1) NOT NULL,
  `version` varchar(16) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `lang` char(2) NOT NULL,
  `id_area` int(11) NOT NULL,
  `tpl` varchar(50) NOT NULL,
  `css` varchar(50) NOT NULL,
  `xfrom` varchar(64) NOT NULL,
  `xid` varchar(32) NOT NULL,
  `url` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xkeys` text NOT NULL,
  `robot` varchar(128) NOT NULL,
  `redirect_code` smallint(4) NOT NULL,
  `redirect` varchar(255) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `xpos` smallint(2) NOT NULL,
  `deep` smallint(2) NOT NULL,
  `ordinal` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `fake` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `arealang` (`id_area`,`lang`,`xfrom`),
  KEY `fromdeep` (`xfrom`,`deep`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--


-- default pages public it

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `xlock`, `xon`) VALUES
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'home', 'Home', 'Home', 'Home page', '', 0, 0, 0, 'A', 0, 0, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'x3admin', 'Editor', 'Editor', 'Editor', '', 0, 1, 1, 'A0000001', 1, 1, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'msg', 'Comunicazione', 'Comunicazione', 'Messaggio all&#039;utente', '', 0, 2, 1, 'A0000002', 1, 1, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'map', 'Mappa del sito', 'Mappa del sito', 'Mappa del sito', '', 0, 3, 1, 'A0000003', 0, 1, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'search', 'Risultati della ricerca', 'Risultati della ricerca', 'Risultati della ricerca', '', 0, 4, 1, 'A0000004', 1, 1, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'info', 'Informazioni', 'Informazioni', 'Informazioni', '', 4, 5, 1, 'A0041001', 0, 0, 1),
(NOW(), 'it', 2, 'base', 'base', 'home', 'base', 'offline', 'Sito in manutenzione', 'Sito in manutenzione', 'Sito in manutenzione', '', 0, 6, 1, 'A0000006', 1, 0, 1);


-- default pages public en

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `xlock`, `xon`) VALUES
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'home', 'Home', 'Home', 'Home page', '', 0, 0, 0, 'A', 0, 0, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'x3admin', 'Editor', 'Editor', 'Editor', '', 0, 1, 1, 'A0000001', 1, 1, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'msg', 'Warning', 'Warning', 'Message', '', 0, 2, 1, 'A0000002', 1, 1, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'map', 'Site map', 'Site map', 'Site map', '', 0, 3, 1, 'A0000003', 0, 1, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'search', 'Search results', 'Search results', 'Search results', '', 0, 4, 1, 'A0000004', 1, 1, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'info', 'Informations', 'Informations', 'Informations', '', 4, 5, 1, 'A0041001', 0, 0, 1),
(NOW(), 'en', 2, 'base', 'base', 'home', 'base', 'offline', 'Site maintenance', 'Site maintenance', 'Site maintenance', '', 0, 6, 1, 'A0000006', 1, 0, 1);;


-- default pages private it

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `xlock`, `xon`) VALUES
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'home', 'Home page', 'Home page', 'Home page private', '', 0, 0, 0, 'A', 0, 0, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'x3admin', 'Editor', 'Editor', 'Editor', '', 0, 1, 1, 'A0000001', 1, 1, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'msg', 'Comunicazione', 'Comunicazione', 'Messaggio all''utente', '', 0, 2, 1, 'A0000002', 1, 1, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'map', 'Mappa del sito', 'Mappa del sito', 'Mappa del sito', '', 0, 3, 1, 'A0000003', 0, 1, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'search', 'Risultati della ricerca', 'Risultati della ricerca', 'Risultati della ricerca', '', 0, 4, 1, 'A0000004', 1, 1, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'logout', 'Esci', 'Esci', 'Esci dall\'area riservata', '', 0, 5, 1, 'A0000005', 0, 1, 1),
(NOW(), 'it', 3, 'base', 'base', 'home', 'base', 'login', 'Login', 'Login', 'Accesso autenticato', '', 0, 6, 1, 'A0000006', 0, 0, 1);


-- default pages private en

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `xlock`, `xon`) VALUES
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'home', 'Home page', 'Home page', 'Home page private', '', 0, 0, 0, 'A', 0, 0, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'x3admin', 'Editor', 'Editor', 'Editor', '', 0, 1, 1, 'A0000001', 1, 1, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'msg', 'Warning', 'Warning', 'Warning', '', 0, 2, 1, 'A0000002', 1, 1, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'map', 'Site map', 'Site map', 'Site map', '', 0, 3, 1, 'A0000003', 0, 1, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'search', 'Search results', 'Search results', 'Search results', '', 0, 4, 1, 'A0000004', 1, 1, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'logout', 'Logout', 'Logout', 'Logout', '', 0, 5, 1, 'A0000005', 0, 1, 1),
(NOW(), 'en', 3, 'base', 'base', 'home', 'base', 'login', 'Login', 'Login', 'Authenticated access', '', 0, 6, 1, 'A0000006', 0, 0, 1);


-- admin pages it

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `robot`, `redirect_code`, `redirect`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `fake`, `xlock`, `xon`) VALUES
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'home',	'Home',	'Home',	'Home page',	'Home page',	'',	0,	'',	0,	0,	0,	'A',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'msg',	'Comunicazione',	'Comunicazione',	'Messaggio all\'utente',	'',	'',	0,	'',	0,	1,	1,	'A0000001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'search',	'Risultati della ricerca',	'Risultati della ricerca',	'Risultati della ricerca',	'',	'',	0,	'',	0,	2,	1,	'A0000002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'login','x3ui',	'home',	'base',	'login',	'Login',	'Login utente',	'Login utente',	'',	'',	0,	'',	0,	3,	1,	'A0000003',	0,	0,	1,	1),
(NOW(),	'it',	1,	'login','x3ui',	'home',	'base',	'login/recovery',	'Recupero password',	'Recupero password',	'Recupero password',	'',	'',	0,	'',	0,	4,	1,	'A0000004',	0,	0,	1,	1),

(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'widgets',	'widgets',	'Widget',	'Gestione widget',	'Gestione widget',	'',	'',	0,	'',	1,	1,	1,	'A0011001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'help',	'help',	'Guida in linea',	'Guida in linea',	'Guida in linea',	'',	'',	0,	'',	1,	2,	1,	'A0011002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'profile',	'Profilo',	'Profilo utente',	'Profilo utente',	'',	'',	0,	'',	1,	3,	1,	'A0011003',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'info',	'info',	'Info',	'Informazioni sul CMS X3',	'',	'',	0,	'',	1,	4,	1,	'A0011004',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	'base',	'login/logout',	'Esci',	'Esci',	'Chiudi sessione',	'',	'',	0,	'',	1,	5,	1,	'A0011005',	0,	0,	1,	1),

(NOW(),	'it',	1,	'base',	'x3ui',	'home',	    'sites',	'sites',	'Impostazioni',	'Gestione impostazioni',	'Gestione impostazioni',	'',	'',	0,	'',	3,	1,	1,	'A0031001',	0,	1,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'sites',	'sites/index',	'Sito',	'Gestione sito',	'Gestione sito',	'',	'',	0,	'',	3,	1,	2,	'A00310011001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'pages',	'areas',	'Aree',	'Gestione aree',	'Gestione aree',	'',	'',	0,	'',	3,	2,	2,	'A00310011002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'languages',	'languages',	'Lingue',	'Gestione lingue',	'Gestione lingue',	'',	'',	0,	'',	3,	3,	2,	'A00310011003',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'languages',	'languages',	'dictionary/keys',	'Gestione chiavi',	'Gestione chiavi',	'Gestione chiavi',	'',	'',	0,	'',	0,	1,	3,	'A003100110030001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'dictionary/keys',	'languages',	'dictionary/words',	'Gestione espressioni',	'Gestione espressioni',	'Gestione espressioni',	'',	'',	0,	'',	0,	1,	3,	'A0031001100300010001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'users',	'users',	'Utenti',	'Gestione utenti',	'Gestione utenti',	'',	'',	0,	'',	3,	4,	2,	'A00310011004',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'users',	'users',	'users/detail',	'Dettaglio utente',	'Dettaglio utente',	'Dettaglio utente',	'',	'',	0,	'',	0,	1,	3,	'A003100110040001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'users',	'users/detail',	'users/permissions',	'Permessi utente',	'Permessi utente',	'Permessi utente',	'',	'',	0,	'',	0,	1,	4,	'A003100110040001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'pages',	'themes',	'Temi',	'Gestione temi',	'Gestione temi',	'',	'',	0,	'',	3,	5,	2,	'A00310011005',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'themes',	'pages',	'templates/index',	'Template',	'Gestione template',	'Gestione template',	'',	'',	0,	'',	0,	1,	3,	'A003100110050001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'themes',	'pages',	'menus/index',	'Men&ugrave;',	'Gestione men&ugrave;',	'Gestione men&ugrave;',	'',	'',	0,	'',	0,	2,	3,	'A003100110050002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'pages',	'contexts',	'Contesti',	'Contesti articoli',	'Contesti articoli',	'',	'',	0,	'',	3,	6,	2,	'A00310011006',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'sites',	'pages',	'categories',	'Categorie',	'Categorie articoli',	'Categorie articoli',	'',	'',	0,	'',	3,	7,	2,	'A00310011007',	0,	0,	1,	1),

(NOW(),	'it',	1,	'base',	'x3ui',	'home',	    'pages',	'pages',	'Pagine',	'Gestione pagine',	'Gestione pagine',	'',	'',	0,	'',	3,	2,	1,	'A0031002',	0,	0,	0,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'pages',	'pages',	'areas/map',	'Mappa area',	'Mappa area',	'Mappa area',	'',	'',	0,	'',	0,	1,	1,	'A00310020000001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'pages',	'pages',	'sections/compose',	'Disposizione articoli',	'Disposizione articoli',	'Disposizione articoli',	'',	'',	0,	'',	0,	2,	1,	'A00310020000002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'pages',	'pages',	'sections',	'Sezioni',	'Gestione sezioni',	'Gestione sezioni',	'',	'',	0,	'',	2,	1,	1,	'A00310020021001',	0,	0,	0,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	    'pages',	'articles',	'Articoli',	'Gestione articoli',	'Gestione articoli',	'',	'',	0,	'',	3,	3,	1,	'A0031003',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'articles',	'pages',	'articles/history',	'Storico articolo',	'Storico articolo',	'Storico articolo',	'',	'',	0,	'',	0,	1,	2,	'A00310030001',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'articles',	'pages',	'articles/edit',	'Editor articolo',	'Editor articolo',	'Editor articolo',	'',	'',	0,	'',	0,	2,	2,	'A00310030002',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	    'files',	'files',	'Files',	'Gestione files',	'Gestione files',	'',	'',	0,	'',	3,	4,	1,	'A0031004',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'files',	'files',	'files/editor',	'Editor file',	'Editor file',	'Editor file',	'',	'',	0,	'',	0,	3,	2,	'A00310040003',	0,	0,	1,	1),
(NOW(),	'it',	1,	'base',	'x3ui',	'home',	    'modules',	'modules',	'Moduli',	'Gestione moduli',	'Gestione moduli',	'',	'',	0,	'',	3,	5,	1,	'A0031005',	0,	0,	1,	1);


-- admin pages en

INSERT INTO `pages` (`updated`, `lang`, `id_area`, `tpl`, `css`, `xfrom`, `xid`, `url`, `name`, `title`, `description`, `xkeys`, `robot`, `redirect_code`, `redirect`, `id_menu`, `xpos`, `deep`, `ordinal`, `hidden`, `fake`, `xlock`, `xon`) VALUES
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'home',	'Home',	'Home',	'Home page',	'',	'',	0,	'',	0,	0,	0,	'A',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'msg',	'Message',	'Message',	'Message',	'',	'',	0,	'',	0,	1,	1,	'A0000001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'search',	'Search result',	'Search result',	'Search result',	'',	'',	0,	'',	0,	2,	1,	'A0000002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'login',	'x3ui',	'home',	'base',	'login',	'Login',	'User login',	'User login',	'',	'',	0,	'',	0,	3,	1,	'A0000003',	0,	0,	1,	1),
(NOW(),	'en',	1,	'login',	'x3ui',	'home',	'base',	'login/recovery',	'Recovery password',	'Recovery password',	'Recovery password',	'',	'',	0,	'',	0,	4,	1,	'A0000004',	0,	0,	1,	1),

(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'widgets',	'widgets',	'Widgets',	'Widgets manager',	'Widgets manager',	'',	'',	0,	'',	1,	1,	1,	'A0011001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'help',	'help',	'Help',	'Help on line',	'Help on line',	'',	'',	0,	'',	1,	2,	1,	'A0011002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'profile',	'Profile',	'User profile',	'User profile',	'',	'',	0,	'',	1,	3,	1,	'A0011003',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'info',	'info',	'Info',	'Info about X3 CMS',	'',	'',	0,	'',	1,	4,	1,	'A0011004',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'base',	'login/logout',	'Logout',	'Logout',	'Logout',	'',	'',	0,	'',	1,	5,	1,	'A0011005',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	'settings',	'sites',	'Settings',	'Settings',	'Settings',	'',	'',	0,	'',	3,	1,	1,	'A0031001',	0,	1,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'sites',	'sites/index',	'Site',	'Site manager',	'Site manager',	'',	'',	0,	'',	3,	1,	2,	'A00310011001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'pages',	'areas',	'Areas',	'Areas manager',	'Areas manager',	'',	'',	0,	'',	3,	2,	2,	'A00310011002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'languages',	'languages',	'Languages',	'Languages manager',	'Languages manager',	'',	'',	0,	'',	3,	3,	2,	'A00310011003',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'languages',	'languages',	'dictionary/keys',	'Keys',	'Keys manager',	'Keys manager',	'',	'',	0,	'',	0,	1,	3,	'A003100110030001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'dictionary/keys',	'languages',	'dictionary/words',	'Words manager',	'Words manager',	'Words manager',	'',	'',	0,	'',	0,	1,	3,	'A0031001100300010001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'users',	'users',	'Users',	'Users manager',	'Users manager',	'',	'',	0,	'',	3,	4,	2,	'A00310011004',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'users',	'users',	'users/detail',	'User detail',	'User detail',	'User detail',	'',	'',	0,	'',	0,	1,	3,	'A003100110040001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'users',	'users/detail',	'users/permissions',	'User permissions',	'User User permissions',	'User permissions',	'',	'',	0,	'',	0,	1,	4,	'A003100110040001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'pages',	'themes',	'Themes',	'Themes manager',	'Themes manager',	'',	'',	0,	'',	3,	5,	2,	'A00310011005',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'themes',	'pages',	'templates/index',	'Templates',	'Templates manager',	'Templates manager',	'',	'',	0,	'',	0,	1,	3,	'A003100110050001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'themes',	'pages',	'menus/index',	'Menus',	'Menus manager',	'Menus manager',	'',	'',	0,	'',	0,	2,	3,	'A003100110050002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'pages',	'contexts',	'Contexts',	'Context of articles',	'Context of articles',	'',	'',	0,	'',	3,	6,	2,	'A00310011006',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'sites',	'pages',	'categories',	'Categories',	'Categories of articles',	'Categories of articles',	'',	'',	0,	'',	3,	7,	2,	'A00310011007',	0,	0,	1,	1),

(NOW(),	'en',	1,	'base',	'x3ui',	'home',	    'pages',	'pages',	'Pages',	'Pages manager',	'Pages manager',	'',	'',	0,	'',	3,	2,	1,	'A0031002',	0,	0,	0,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'pages',	'pages',	'areas/map',	'Area map',	'Area map',	'Area map',	'',	'',	0,	'',	0,	1,	3,	'A00310020001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'pages',	'pages',	'sections/compose',	'Articles disposition',	'Articles disposition',	'Articles disposition',	'',	'',	0,	'',	0,	2,	3,	'A00310020002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'pages',	'pages',	'sections',	'Sections',	'Sections manager',	'Sections manager',	'',	'',	0,	'',	2,	1,	3,	'A00310021001',	0,	0,	0,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	    'pages',	'articles',	'Articles',	'Articles manager',	'Articles manager',	'',	'',	0,	'',	3,	3,	1,	'A0031003',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'articles',	'pages',	'articles/history',	'Article history',	'Article history',	'Article history',	'',	'',	0,	'',	0,	1,	2,	'A00310030001',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'articles',	'pages',	'articles/edit',	'Article editor',	'Article editor',	'Article editor',	'',	'',	0,	'',	0,	2,	2,	'A00310030002',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	    'files',	'files',	'Files',	'Files manager',	'Files manager',	'',	'',	0,	'',	3,	4,	1,	'A0031004',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'files',	'files',	'files/editor',	'File editor',	'File editor',	'File editor',	'',	'',	0,	'',	0,	3,	2,	'A00310040003',	0,	0,	1,	1),
(NOW(),	'en',	1,	'base',	'x3ui',	'home',	    'modules',	'modules',	'Plugins',	'Plugins manager',	'Plugins manager',	'',	'',	0,	'',	3,	5,	1,	'A0031005',	0,	0,	1,	1);

-- --------------------------------------------------------

--
-- Table structure for table `param`
--

CREATE TABLE IF NOT EXISTS `param` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `xrif` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xtype` varchar(128) NOT NULL,
  `xvalue` text NOT NULL,
  `required` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `param`
--

INSERT INTO `param` (`updated`, `id_area`, `xrif`, `name`, `description`, `xtype`, `xvalue`, `required`, `xlock`, `xon`) VALUES
(NOW(), 1, 'site', 'notices', 'Enable/disable notices', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'online', 'Enable/disable online editing', '0|1', '0', 1, 0, 1),
(NOW(), 1, 'site', 'inline', 'Enable/disable inline editing', '0|1', '0', 1, 0, 1),
(NOW(), 1, 'site', 'debug', 'Enable/disable debug', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'devel', 'Enable/disable development state', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'logs', 'Enable/disable logs', '0|1', '0', 1, 0, 1),
(NOW(), 1, 'site', 'multilanguage', 'Enable/disable multilingual site', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'multiarea', 'Enable/disable multiarea site', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'cache', 'Enable/disable page caching', '0|1', '0', 1, 0, 1),
(NOW(), 1, 'site', 'cache_time', 'Caching time (seconds)', 'INTEGER', '12000', 1, 0, 1),
(NOW(), 1, 'site', 'advanced_editing', 'Enable/disable Advanced Editing', '0|1', '0', 1, 0, 1),
(NOW(), 1, 'site', 'autorefresh', 'Enable/disable autorefresh articles', '0|1', '1', 1, 0, 1),
(NOW(), 1, 'site', 'date_format', 'Date format', 'TEXT', 'F, d Y', 1, 0, 1),
(NOW(), 1, 'site', 'pp', 'Items per page', 'INTEGER', '20', 1, 0, 1),
(NOW(), 1, 'site', 'max_doc', 'Max uploading documents size in KB', 'INTEGER', '1024', 1, 0, 1),
(NOW(), 1, 'site', 'max_img', 'Max uploading images size in KB', 'INTEGER', '1024', 1, 0, 1),
(NOW(), 1, 'site', 'max_w', 'Max uploading image width in px', 'INTEGER', '1920', 1, 0, 1),
(NOW(), 1, 'site', 'max_h', 'Max uploading image height in px', 'INTEGER', '1080', 1, 0, 1),
(NOW(), 1, 'site', 'mail', 'Default web site email', 'EMAIL', 'ZZZAMAIL', 1, 0, 1);


-- --------------------------------------------------------

--
-- Table structure for table `privs`
--

CREATE TABLE IF NOT EXISTS `privs` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_who` int(11) NOT NULL,
  `what` varchar(32) NOT NULL,
  `id_what` int(11) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `priv` (`id_who`,`what`,`id_what`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `privtypes`
--

CREATE TABLE IF NOT EXISTS `privtypes` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `xrif` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privtypes`
--

INSERT INTO `privtypes` (`updated`, `xrif`, `name`, `description`, `xon`) VALUES

(NOW(), 1, '_area_creation', '_AREA_CREATION', 1),
(NOW(), 1, '_article_creation', '_ARTICLE_CREATION', 1),
(NOW(), 1, '_category_creation', '_CATEGORY_CREATION', 1),
(NOW(), 1, '_context_creation', '_CONTEXT_CREATION', 1),
(NOW(), 1, '_file_upload', '_FILE_UPLOAD', 1),
(NOW(), 1, '_group_creation', '_GROUP_CREATION', 1),
(NOW(), 1, '_key_creation', '_KEY_CREATION', 1),
(NOW(), 1, '_key_import', '_KEY_IMPORT', 1),
(NOW(), 1, '_language_creation', '_LANGUAGE_CREATION', 1),
(NOW(), 1, '_menu_creation', '_MENU_CREATION', 1),
(NOW(), 1, '_module_install', '_MODULE_INSTALL', 1),
(NOW(), 1, '_page_creation', '_PAGE_CREATION', 1),
(NOW(), 1, '_section_creation', '_SECTION_CREATION', 1),
(NOW(), 1, '_site_creation', '_SITE_CREATION', 1),
(NOW(), 1, '_template_install', '_TEMPLATE_INSTALL', 1),
(NOW(), 1, '_theme_install', '_THEME_INSTALL', 1),
(NOW(), 1, '_user_creation', '_USER_CREATION', 1),
(NOW(), 1, '_word_creation', '_WORD_CREATION', 1),

(NOW(), 1, 'areas', 'AREAS', 1),
(NOW(), 1, 'articles', 'ARTICLES', 1),
(NOW(), 1, 'categories', 'CATEGORIES', 1),
(NOW(), 1, 'contexts', 'CONTEXTS', 1),
(NOW(), 1, 'dictionary', 'DICTIONARY', 1),
(NOW(), 1, 'files', 'FILES', 1),
(NOW(), 1, 'xgroups', 'XGROUPS', 1),
(NOW(), 1, 'languages', 'LANGUAGES', 1),
(NOW(), 1, 'logs', 'LOGS_DATA', 1),
(NOW(), 1, 'menus', 'MENUS', 1),
(NOW(), 1, 'modules', 'MODULES', 1),
(NOW(), 1, 'pages', 'PAGES', 1),
(NOW(), 1, 'privs', 'PRIVS', 1),
(NOW(), 1, 'sections', 'SECTIONS', 1),
(NOW(), 1, 'sites', 'SITES', 1),
(NOW(), 1, 'templates', 'TEMPLATES', 1),
(NOW(), 1, 'themes', 'THEMES', 1),
(NOW(), 1, 'users', 'USERS', 1),
(NOW(), 1, 'widgets', 'WIDGETS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_page` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `progressive` smallint(2) NOT NULL,
  `settings` text NOT NULL,
  `articles` text NOT NULL,
  `show_author` tinyint(1) NOT NULL,
  `show_date` tinyint(1) NOT NULL,
  `comments` tinyint(1) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `updated`, `id_area`, `id_page`, `progressive`, `settings`, `articles`, `show_author`, `show_date`, `comments`, `xlock`, `xon`) VALUES
(1, NOW(), 2, 1, 1, '', '0665f9f126a93750d68c49acde91c0ac', 0, 0, 0, 0, 1),
(2, NOW(), 2, 5, 1, '', '0665f9f126a93750d68c49acde91c0ac', 0, 0, 0, 0, 1),
(3, NOW(), 3, 6, 1, '', '0665f9f126a93750d68c49acde91c0ac', 0, 0, 0, 0, 1),
(4, NOW(), 3, 10, 1, '', '0665f9f126a93750d68c49acde91c0ac', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `xcode` char(32) NOT NULL,
  `domain` varchar(128) NOT NULL,
  `xdatabase` varchar(32) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  `version` varchar(16) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`updated`, `xcode`, `domain`, `version`, `xon`) VALUES
(NOW(), '', 'ZZZDOMAIN', '0.9.99 STABLE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `name` varchar(128) NOT NULL,
  `css` varchar(128) NOT NULL,
  `js` varchar(64) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  `sections` int(4) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `updated`, `name`, `css`, `js`, `id_theme`, `description`, `settings`, `sections`, `xlock`, `xon`) VALUES
(1, NOW(), 'base', 'x3ui', 'x3ui', 1, 'Default Admin template', '{"s1":{"bgcolor":"default","fgcolor":"default","columns":"1","width":"100"},"sn":{"bgcolor":"#ffffff","fgcolor":"#444444","columns":"4","width":"100"}}', 1, 0, 1),
(2, NOW(), 'base', 'base', 'script', 2, 'Default page template', '{"s1":{"bgcolor":"default","fgcolor":"default","columns":"1","col_sizes":"1", "width":"container mx-auto","class":""},"sn":{"bgcolor":"#ffffff","fgcolor":"#444444","columns":"4","col_sizes":"1+1+1+1","width":"fullwidth","class":""}}',1, 0, 1);
-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `version` varchar(16) NOT NULL,
  `styles` text NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `updated`, `name`, `description`, `styles`, `version`, `xlock`, `xon`) VALUES
(1, NOW(), 'admin', 'Admin - default Admin theme', '', '3', 0, 1),
(2, NOW(), 'default', 'Default - default site theme', '[{"what":"section","style":"bg-fucsia","description":"background fuchsia"},{"what":"section","style":"x3bg-orange","description":"background orange"},{"what":"section","style":"bg-petroleum","description":"background petroleum"},{"what":"section","style":"x3bg-violet","description":"background violet"},{"what":"section","style":"bg-military","description":"background military"},{"what":"section","style":"fucsium","description":"gradient fuchsia -> petroleum"},{"what":"section","style":"orangium","description":"gradient orange -> petroleum"},{"what":"section","style":"fucsange","description":"gradient fuchsia -> orange"},{"what":"article","style":"overlay1 rounded-2xl p-8","description":"ultra lightgray transparent rounded"},{"what":"article","style":"overlay2 rounded-2xl p-8","description":"lightgray transparent rounded"},{"what":"article","style":"overlay3 rounded-2xl p-8","description":"gray transparent rounded"},{"what":"article","style":"border-2 border-neutral-200 rounded-2xl p-8","description":"lightgray bordered rounded"},{"what":"article","style":"border-2 border-neutral-200 p-8","description":"lightgray bordered not-rounded"},{"what":"article","style":"bg-fucsia rounded-2xl p-8","description":"background fuchsia rounded"},{"what":"article","style":"x3bg-orange rounded-2xl p-8","description":"background orange rounded"},{"what":"article","style":"bg-petroleum rounded-2xl p-8","description":"background petroleum rounded"},{"what":"article","style":"x3bg-violet rounded-2xl p-8","description":"background violet rounded"},{"what":"article","style":"bg-military rounded-2xl p-8","description":"background military rounded"},{"what":"article","style":"bg-military p-8","description":"background military squared"}]', '1.4', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `uprivs`
--

CREATE TABLE IF NOT EXISTS `uprivs` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `privtype` varchar(128) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_group` int(11) NOT NULL,
  `lang` char(2) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `mail` varchar(48) NOT NULL,
  `phone` varchar(48) NOT NULL,
  `last_in` datetime NOT NULL,
  `level` smallint(6) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `hashkey` varchar(32) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `updated`, `id_group`, `lang`, `username`, `password`, `description`, `mail`, `phone`, `last_in`, `level`, `hidden`, `hashkey`, `xlock`, `xon`) VALUES
(1, NOW(), 1, 'en', 'ZZZAUSER', 'ZZZAPASS', 'default Administrator', 'ZZZAMAIL', '', NOW(), 4, 1, '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL auto_increment,
  `updated` datetime NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `xpos` smallint(6) NOT NULL,
  `xlock` tinyint(1) NOT NULL,
  `xon` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Install X4 Flags
--

INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_flags', 'X4_FLAGS', 1);
INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_flags', 4, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_FLAGS', 'Manage X4Flags plugin', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_FLAGS', 'Gestione modulo X4Flags', 0, 1);
INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), 2, 'x4flags', 'flags', 'Flags or text', '0|1', '1', 0, 0, 1);
INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), 2, 'x4flags', 'short_text', 'Short text', '0|1', '0', 0, 0, 1);
INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), 2, 'x4flags', 'Language switcher', 1, 0, 0, 0, 1, 1);

--
-- Install x4get_by_key
--

INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_get_by_key', 'X4_GET_BY_KEY', 1);
INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_get_by_key', 4, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_GET_BY_KEY', 'Raggruppa articoli per chiave', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_GET_BY_KEY', 'Group articles by key', 0, 1)
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_CONFIGURATOR_MSG', 'Seleziona una chiave per filtrare gli articoli', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_OPTION', 'Chiave articoli', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_TAG_OPTION', 'Visualizza Tag', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_CONFIGURATOR_MSG', 'Select an article key to filter articles', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_OPTION', 'Key', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_TAG_OPTION', 'Show tags', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'public', 'x4get_by_key', '_X4GET_BY_KEY_FILTER', 'filtra per Tag', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'public', 'x4get_by_key', '_X4GET_BY_KEY_UNFILTER', 'Rimuovi filtro', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'public', 'x4get_by_key', '_X4GET_BY_KEY_FILTER', 'filter by Tag', 0, 1);
INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'public', 'x4get_by_key', '_X4GET_BY_KEY_UNFILTER', 'remove filter', 0, 1);
INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), 2, 'x4get_by_key', 'Articles by key', 0, 0, 0, 1, '0.9', 1);

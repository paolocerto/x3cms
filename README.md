
X3CMS README.md

---

X3 CMS
Your Next Content Management System: X3 CMS is a CMS PHP based, is easy to use, 
W3C compliant, accessible, support themes and plugins. Since 0.4 version the 
engine is a Php MVC Framework derived from Kohana (http://kohanaphp.com).

---

VERSION 0.5.3 STABLE (October 2015)

Which news?
	- completely rewritten the admin theme now cleaner and responsive
	- completely rewritten the default theme with Bootstrap
	- a lot of bug fixes and small improvements
	- moved to GitHub
	
---

LICENSE

X3 CMS is distributed under three different licenses:

1) the AGPL3 licence 
X3 CMS is free to use, but only if a link to the X3 CMS Legal Notices is kept visible in the template for the site. 

2) Linkware license
Same as AGPL, but instead of keeping a link to the X3 CMS Legal Notices, you must place a static, visible and 
readable link to www.x3cms.net with the text or an image stating "Powered by X3 CMS" on every generated page.

3) Commercial license
This license allow you to remove the "Powered by X3 CMS" and/or "X3 CMS Legal Notices" links at one specific 
domain from each generated page.
This licence NOT protect your modifications against the copyleft requirements in AGPL 3.

The AGPL licence is an "as is" licence with no warranties whatsoever.

More informations at http://www.x3cms.net/en/public/download_x3cms

---

REQUIREMENTS

- apache2 with htaccess enabled, mod_rewrite, headers, filter
- mysql5 or newer
- php 5.3 or newer with PDO mysql driver, GD, mcrypt, Curl

---

FEATURES

With 0.5 version many things are changed.
Some features was added and some are pending.

- accessible
- xHTML1.1/HTML5 compliant
- CSS 3.0 compliant
- multi language support
- multi area support
- themeable
	- each area can have a different theme
	- each theme can have many templates
	- each template can have many menus and sections
	- easy API to use into template
- internal search engine
- automatic breadcrumb
- SEO optmized
	- Friendly URLs
	- you can edit site title and site description
	- you can edit page URL, page title, page name (used into menus and site map), page decription and page keywords
	- automatic sitemap.xml file
- pluggable
	- x4site_map for automatic site map construction
	- x3flags for switch from a language to another
- friendly administration
- multi user supported
- fine grain permission management
- simple editing with WYSIWYG editor
- advanced editing with multiple articles and modules on each page
- content versioning
- centralized file management
	- image resize
	- video conversion
- uncentralized file management with Responsive Filemanager for TinyMCE
- caching of static pages or only for heaviest queries
- RESTful ready
- MongoDB support

---

HOWTO INSTALL

1 - copy files into your web space
2 - create a mysql database with charset utf8
3 - set set write permission on:
	- .htaccess
	- robots.txt
	- cms/config/config.php
	- cms/files folder and all its contents
4 - open a browser and go to http://your_web_space and follow the instructions
5 - enjoy with X3 CMS

---

CREDITS

X3 CMS includes several projects

- X4WebApp: a very lightweight Php MVC Framework derived from Kohana (http://kohanaphp.com)
- Mootools: a compact, modular, Object-Oriented JavaScript framework designed for JavaScript developer (http://mootools.net)
- Tiny MCE: a platform independent web based Javascript HTML WYSIWYG editor (http://tinymce.moxiecode.com)
- SwiftMailer: a flexible and elegant object-oriented approach to sending emails with a multitude of features (http://swiftmailer.org/)
- PHP Simple HTML DOM Parser: A HTML DOM parser written in PHP5+ let you manipulate HTML in a very easy way! (http://simplehtmldom.sourceforge.net/)
- jQuery: a new kind of JavaScript Library (http://jquery,com)
- OpenInviter: a free import contacts (addressbook) script from email providers	(http://openinviter.com)
- Restler: A RESTful API server framework that is written in PHP that aids your mobile/web/desktop applications (http://luracast.com/products/restler/)
- JShrink is a Javascript Minifier built in PHP (http://www.tedivm.com)
- Responsive FileManager is a free open-source file manager for Tiny MCE (http://www.responsivefilemanager.com)
- Lasso.js and Lasso.crop.js wrote by Nathan White, a crop script with Mootools (http://www.nwhite.net)
- GetID3 a PHP script that extracts useful information from MP3s & other multimedia file formats (http://www.getid3.org)
- Bootstrap the most popular HTML, CSS, and JS framework for developing responsive, mobile first projects on the web (http://getbootstrap.com/)
- Font Awesome The iconic font and CSS toolkit (https://fortawesome.github.io/Font-Awesome/)
- mPDF a PHP class which generates PDF files from UTF-8 encoded HTML (http://www.mpdf1.com/mpdf/index.php)
- spreadsheet-reader a PHP spreadsheet reader that differs from others in that the main goal for it was efficient (https://github.com/nuovo/spreadsheet-reader)

Plugins can include some other projects.

---

DOWNLOAD

https://github.com/paolocerto/x3cms

Paolo Certo

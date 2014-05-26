For english speaking ppl: currently 'en' translation is not available, just russian.

SETUP
======

Unpack files on server (currently tested on apache).
Make `public` folder as server root.
Ensure that mod_rewrite is on and .htaccess files processed

Dependencies:
-------------
composer install
bower install

Tinymce
--------
Tinymce bundle does not compiled by default.
Go to public/vendor/tinymce and run:
npm i -g jake
jake

(unix) Configure server-writable dirs:
--------------------------------------
config/localhost/database.db (sqlite database)
public/uploads/*
public/assets/*
cache/*
tmp/*

(unix/sqlite)
-------------
Ensure all folders in path to database file are writable+executable by web server,
otherwise you'll get ```unable to open database file``` error.
{server-root}/config/whatever/database.db

Fresh Install
-------------
In console run command:

`app.bat core:install`

This command create database tables (see engine.cfg),
default site and superuser account.


That's all.


Optional steps
==============

Build Editor minified js-bundle for faster acp loading
-------------------------------------------------------
r.js -o build.js

Append config/engine.cfg with:

render_config_vars = "..., lib_editor"

[lib_editor]
optimized = yes

This will create `assets/editor/main.js` file.


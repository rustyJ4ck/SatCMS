INSTALL:
---------

unpack files on server (currently tested on apache)
make `public` folder as server root
ensure that mod_rewrite is on and .htaccess files processed

dependencies:
-------------
composer install
bower install

build tinymce
-------------
go to public/vendor/tinymce and run:
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
otherwise you'll "get unable to open database file" error.
{server-root}/config/whatever/database.db

That's all.




Build Editor all-in-one bundle (optional)
-----------------------------------------
r.js -o build.js


Append config/engine.cfg with:

render_config_vars = "..., lib_editor"

[lib_editor]
optimized = yes



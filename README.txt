COMPOST INSTALLATION INSTRUCTIONS
=================================

To install, follow these steps:

1. Extract the zip file to a web-accessible directory.

2 a. If you want to use the txt database (the default):
	Make sure the following directories and files are writable by the server:
	/database/compost

--OR--

2 b. If you want to use an sql database (for advanced users):
	look in database/sql/empty_database.sql for the default sql file and run it on your database
	set up your database connection information in /config/database.php

3. Make sure that the following directories are writable by the server:
	/images/clients
	/images/comps

4. Navigate to your new Compost install and follow the install process.
	
That's it! Visit http://compo.st for more information about how to use Compost.

Note: For Compost to work, you need PHP 5+ and Apache's mod_rewrite module enabled.
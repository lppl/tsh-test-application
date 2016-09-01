TSH Developer Test Project Specification
========================================

We've grabbed a SQL dump from data.gov.uk which contains a list of all the payments made by Chichester District Council to suppliers in October 2010 with a value of £500 or higher. The task is to create a simple listing of the data that can be used to invesigate it. 

We've split it into 3 distinct chunks. We'd like to be able to see each chunk as a seperate stage so when you've finished, please submit three zips / archives named Phase-1.zip, Phase-2.zip and Phase-3.zip containing the project as it was when you finished each chunk. 

We'd like you to keep that basic structure that we've outlined but feel free to create folders to house javascript / css / whatever as you like. Please keep your classes in lib/Local (using whatever file layout you like).

Phase 1
=======
In the specification/designs folder you'll find two files - Listing.psd and Listing.png. We'd like you to cut up this design into valid html 5. We're happy for the design to gracefully degrade in older browsers (eg: don't worry about curved corners in IE6!!). The font we've used is called 'Source Sans Pro' and should be available quite freely on the web. Here's the page about it on Adobe.com - http://blogs.adobe.com/typblography/2012/08/source-sans-pro.html.

Phase 2
=======
Create a suitable MySQL database from the structure and data contained in specification/data/payments.sql. Using this data, write some PHP that can pull it into the page and show it in the listing. You'll also need to paginate the data - the design suggests five rows per page. There's no requirement to do any sorting of the data (eg: by clicking on headings) so don't worry about that. We've provided you with a couple of classes to do some of the heavy lifting - a simple DB class (lib/TSH/Db.php) and a simple data model class (lib/TSH/Model.php). As mentioned above, please keep the classes that you write in lib/Local. We haven't supplied an autoloader - feel free to implement one if you like (we don't mind if you don't!).

Phase 3
=======
Convert the listing you created in Phase 2 to use AJAX to pull in the paginated data. It would be super nice if we could click on a row and have a modal pop up with that record in it. We haven't provided a design for the modal - we'll leave that to your creative side! We've provided you (in the javascript folder) with jQuery 1.8 minified and the SimpleModal jQuery plugin. We'd like you to stick to jQuery as the base library, but if you prefer a different plugin than SimpleModal that's fine!

When you've finished the project, please send it back to us at {WSTAW_TU_LINKA}. Please tell us roughly how long it took you to do it and enclose any notes you feel we'll need to get it all running locally.

Good luck!

The TSH Team



Test Project Layout
===================

This is a very simple test project stub framework that provides some rudimentary tools for building a small web 
application. The layout is as follows:

lib/
	
	TSH.php				- Include file for the TSH supplied helper classes
	TSH/
		Model.php		- A simple database model base class ready for you to subclass
		Db.php			- A database access layer, used by TSH_Model
		Exception.php 	- A generic exception class

	Local/				- The folder where your classes should go - the layout is up to you

config/
	database.php		- Database configuration file - please update with your settings as required

specification/
	
	data/				- Database dumps that you can use for the project
	design/				- Page designs that you will need to implement

javascript 				
	
	jquery.min.js 		- Version 1.8 of the jQuery javascript library
	jquery.simplemodal.min.js 	- Version 1.4.2 of the SimpleModel jQuery plugin

images
	
	tile.png			- A tileable png of the design background - we're trying to be nice!

README.txt				- This file

index.php				- The 'front controller' - your starting point


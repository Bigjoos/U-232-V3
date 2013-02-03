/// U-232 V3
/// All Credit goes to the original code creators, especially to any author for the mods i selected for this. 
/// The original coders of torrentbits and especially to CoLdFuSiOn for carrying on the legacy with Tbdev.
/// The coders of gazelle for the class.cache, sctbdev for various replacement code.
/// All other mods and snippets for this version from CoLdFuSiOn, putyn, pdq, djGrrr, Retro, elephant, ezero, Alex2005, system, sir_Snugglebunny, laffin, Wilba, Traffic, dokty, djlee, neptune, scars , Raw, soft, jaits, Melvinmeow, Roguesurfer, stoner  Theres to many to mention here but the upmost respect and credit to you all.
/// Credit's to pdq/putyn for improvements in key areas on the code. Your input has been first class.
/// Credit's to Kidvision for designs used in the v0+v1+v2 Installer projects.
/// Credits to Roguesurfer for all v3 design - Your a credit to this team. 
/// Huge thanks to pdq for so much input and improved code and guidance with memcache.

Set Up Instructions - Please take note :

Before you begin installation it is very important that your server is configured correctly and has all the required dependencies the source code requires, ensure your error reporting is enabled on the server and your logging the errors not displaying them, any error on install is a failure to adhere to setup instructions, if you experience a failure then a properly configured server will report that issue, no excuses required.

First create a dir one up from root so it resides beside it not inside it named bucket then inside that folder another named avatar, if you use your own names for those folders then edit bitbucket.php and img.php defines at top of the files, add htaccess and index.html files into both newly created dirs accordinally, make sure those folders are accessible by chmod, extract pic.rar - javairc.rar and GeoIp.rar and ensure there not inside an extra folder from being archived then upload the code to your server and chmod - cache and all nested files and folders - dir_list - uploads - uploadsubs - imdb imdb/cache imdb/images - include - include/backup - include/settings settings.txt  install/config.sample.php install/ann_config.sample.php - logs - torrents.
Create a new database user and password via phpmyadmin - Point to http://yoursite.com/install/index.php - fill in all the required data - log in. Create a new user on entry named System ensure its userid2 so you dont need to alter the autoshout function on include/user_functions.php. Sysop entry to allowed arrays is auto, so is staff, make sure member is offline when promoting.

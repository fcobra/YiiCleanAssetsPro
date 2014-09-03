YiiCleanAssetsPro
===========

Command line Yii Script to clean assets with exclude mode dirs, files and subdirs

YiiCleanAssetsPro assets is console yii script to clean assets or runtime directory.
You can use this script to clean `/assets` folder full or path of it. You can use it
from command line with dialog or with cron actions in `auto` mode. 

##Usage

1. Place YiiCleanAssetsProCommand to `application.commands` folder
2. Place yiic.php script in on folder with `assets`.
3. Run `yiic help YiiCleanAssetsProCommand` to see how to use this script.

##Command line arguments
**--auto** - makes script to run without questions and dialogs (good for cron);

**--dir=/path/to/dir** - specifies path to directory. If blank - system default path will be applied;

**--exclude=/_thumbnails/catalog/,/_thumbnails/catalogstructure/,.gitignore etc. For ignore folders, 
	files and subfolders.

##Useful info:
[How to Run Yiic directly from your app without a shell](http://www.yiiframework.com/wiki/226/run-yiic-directly-from-your-app-without-a-shell/ "How to Run Yiic directly from your app without a shell") by [jacmoe](http://www.yiiframework.com/user/7189/ "jacmoe")

##Do not use if you are not sure!
Command List
============

Commands starts with underscore as the first character. 

to be implemented. 

Standard Command
----------------

Commands that are available in normal mode.  


*   _view, default:  
    shows contents in template. php code will be executed.
*   _raw:  
    shows contents as text without template. 
*   _src:  
    shows the source code of the file. 

Edti Command
------------

Commands that are available after login to _dev mode. 
These commands are valid only for file mode (not in application). 

*   _edit:  
    edit file source code. saves as _dev.file_name.ext. 
*   _put:  
    saves the contents to _dev.file_name.ext. 
*   _del:  
    deletes the _dev.file_name.ext. 
*   _pub:  
    publishes the file. rename _dev.file_name.ext to file_name.ext. 
    The original file is backup to _Backup folder. 
*   _purge:  
    deletes the file_name.ext. 
    The file is backup to _Backup folder. 

File Back Up
------------

When a file is published (_pub) or purged (_purge), 
the original file is backed up to _Backup folder. 
The file name will be:

　   _Backup/file_name-YYYYmmddHHiiss.ext  

Commands for Backup are:

*   _bkView:\<backup_name\>  
    view backed-up files. 
*   _bkDiff:\<backup_name\>  
    show diff between backup and current file. to-be-implemented.
*   _bkDDiff:\<backup_name1\>:\<backup_name2\>  
    diff between backup files... not sure if this will be implemented...


There is no way to check the back up file currently. 
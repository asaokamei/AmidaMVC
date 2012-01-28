AmidaMVC, a PHP Micro Framework
===============================

Yet another MVC micro framework for PHP, based on amida-style dispatch chain.  
I developed this framework for studying and some demo purpose. 

Still under development; please add "I hope" or "plan to" at the end of each 
paragraph. Thanks. 

LICENSE
-------

The MIT License (MIT)

demo
----

download from github, place it anywhere under document root of the web server 
with PHP 5.3 and above, and hit the url http://your_server/some/path/AmidaMVC/. 


AmidaMVC should be working... 

Features
--------

What's so special about AmidaMVC Framework?  


Some features I like are:

*   small:  
    the core of the framwork is consisted of only 3 files, with less 
    than 500 of lines of code. I think it is small enough to understand 
    how framework works. 

*   Simple:  
    I think it is simple [Citation Needed].  

*   various view mode:  
    *   view php file as source code, 
    *   view markdown file automatically converted to html, 
    *   show text file after nl2br...
    interestingly, php code won't run as php, 
    but php code in md and text file works as php.

Amida-Chain
-----------

It started as a simple dispatcher code to study the Chain of Responsibility 
pattern. Amida-style chain is nothing but to dispatching the same method 
of series of classes. Chain can change the method name to dispatch at any 
time of the chain.


I found Amida-Chain maybe ideal for web framework [Citation Needed] since 
it cna handle the normal case (when successfully returned a page) from other 
abnormal case (when not finding a page, auth failed, etc.) [Citation Needed].


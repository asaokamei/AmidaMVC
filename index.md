AmidaMVC
========

Yet another MVC micro framework for PHP, based on amida-style dispatch chain.  
I developed this framework for studying and some demo purpose.

*   [README.md](README.md)
*   [MIT License](LICENSE.txt)
*   Source Code at gitHub:    
    [(https://github.com/asaokamei/AmidaMVC)](https://github.com/asaokamei/AmidaMVC)

File Structure and View Source Code
-----------------------------------

*   [src/AmidaMVC](src/AmidaMVC/README.md):  
    source code of AmidaMVC Framework.

*   [vendor](vendor/README.md):  
    includes external library used in the Framework.
    Currently, PHPMarkdown is used.

*   tests:  
    going to contain some tests. almost empty now. 

*   _Config:   
    configuration for viewin this source as a demo site.

Example and Demo
----------------

This git entry itself is a demo site.  
download the source code from the [github](https://github.com/asaokamei/AmidaMVC) 
and deploy it anywhere under root document of Apache with PHP >= 5.3.0. Then, access the url. 

some other interesting demo: 

*   [sample HTML file](example.html)

*   <?php echo ( $_site['mode'] == '_dev' ) ? 'Login (already logged in...)' : '[Login for Developer\'s Mode](_dev/)'; ?>  
    Shows Debug Info. 

*   <?php echo ( $_site['mode'] == '_dev' ) ? '[Logout](_logout/)' : 'Logout (not logged in...)'; ?>

Amida-Chain
-----------

Amida chain dispatches the same method of classes in its chain. 
As for the web framework, the classes are called components. 


If everything is OK, the allOK method of each of the components are called. 
if page is not found in the Router (which searches for a file from request), 
the Router changes the default to err404 method, and Amida-chain keeps 
calling the err404 method, instead of allOK. 


class  | all OK  | page not found | login  |
-------|---------|----------------|--------|
Config |  allOK  |                |        |
Auth   |  allOK  |                | login  |
Router |  allOK  |    err404      |        |
Loader |  allOK  |                |        |
Render |  allOK  |    err404      | login  |


As such, I hope this style of chain will make it easier to develop a 
web framework. 

Usage
-----

Seriously, how I can write a usage for a framework?
Except to provide some demo and ask to look at the code...



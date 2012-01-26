AmidaMVC
========

Yet another MVC micro framework for PHP, based on amida-style dispatch chain.  
I developed this framework for studying and some demo purpose.

*   [MIT License](LICENSE.txt)
*   [README.md](README.md)
*   [Source Code at gitHub](https://github.com/asaokamei/AmidaMVC)
*   [sample HTML file](example.html)

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


[Show Debug Info](_dev/)
AmidaMVC/Component
==================

Components are classes that are plugged into the Amida-Chain.

files
-----

*   [Config.php](Config.php/_src):  
    creates configuration... still under development.

*   [Debug.php](Debug.php/_src):  
    include this module to use Debug class in models.
    activates itself if \_dev is in path_info.

*   [Router.php](Router.php/_src):  
    finds file to load based on Routes, or scanning for matching folder/files.

*   [Loader.php](Loader.php/_src):  
    loads file according to the file types.
    * \_App.php: application file which loads Model/View to Controller.
    * .php: executes php file.
    * .html: reads html as contents.
    * .md, .markdown: reads as markdown and converts to html.

*   [Render.php](Render.php/_src):  
    Renders using dumb PHP based template.

*   [SiteObj.php](SiteObj.php/_src):    
    data passed through the component chains.
    contains all the necessary information to build html page.


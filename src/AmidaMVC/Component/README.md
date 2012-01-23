#AmidaMVC/Component

Components
    :plugin for AmidaMVC.


*   Debug:

    include this module to use Debug class in models.
    activates itself if \_dev is in path_info.

*   Router:

    finds file to load based on Routes, or scanning for matching folder/files.

*   Loader

    loads file according to the file types.

    * \_App.php: application file which loads Model/View to Controller.
    * .php: executes php file.
    * .html: reads html as contents.
    * .md, .markdown: reads as markdown and converts to html.

*   Render

    Renders using dumb PHP based template.

*   SiteObj

    data passed through the component chains.
    contains all the necessary information to build html page.


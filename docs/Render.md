Rendering Contents
==================

This document is mostly about Render class which converts various content types 
to HTML and renders contents inside template. 

Rendering Mode
--------------

Default (_view):
: default mode to view contents.   
  md, text are **converted to HTML** based on content_type, except html is used as is.   
: shows html, md, text **contents in template**.  
  others types (css, images) are emitted as is. 

_src:
: shows source of the file.  
  all text content always are source code, **highlighted**, and **shown in template**.  
: Loader must read file content, i.e. not executed as PHP.  
  any text contents (html, md, text, css, etc.) can be viewed; others are emitted as is. 

_raw:
: raw output of html, md, text, are shown as is. 
  which are **not shown in template**.  
  the http's content_type will be text/plain. 

contentObj in SiteObj
---------------------

contentObj carries contents of the viewed page, with other information. 

contents:
: content of the page.  
  may be html, markdown, text, css, or some image. 

content_type:
: type of content, such as html, md, text, css, etc. 
  this is not http header's content_type. this is almost the same as file extension. 

file_name:
: file name of the page's file (basename only). 
  if content_type is not set, file_name is used to determine the type of content. 

debug:
: debug information from Debugger, if any.  
  not sure if this info should be in contentObj... 


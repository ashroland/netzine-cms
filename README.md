# netzine-cms
### an ultra-lightweight static cms for netzines

#### [tl;dr, want a live demo](https://ashro.land/netzine-cms)



# philosophy
traditional zine-making is democratized book-binding with 
commonly available office supplies. netzine-cms aims to be the printer paper 
and staples of online zine publishing. it joins several different tools 
together as design decisions to make publishing as simple as dropping 
files on a server.

#### what it is
a lightweight framework which provides pagination for many different 
filetypes. an organizational system. a form of collage. sensible design 
decisions. accessible. 

#### what it isn't 
netzine-cms assumes you already know how to author a webpage so it 
provides no tools for generating semantic markdown. bloated. 

#### accessibility and durability
whenever possible, netzine-cms makes an effort to output semantically-valid,
user-parseable and machine-traversable HTML. it is designed to work well with
screenreaders. it is built to be very static for archive projects 
like [the internet archive](https://archive.org/web/).


# usage

requisites: standard web server (think apache or nginx), relatively 
modern PHP

clone this repository into /var/www/html or similar.

add files to ./pages. netzine-cms sorts pages in descending 
alphabetical order, so 2018-08-22.html will appear after 
2013-03-22.html.


### filetypes supported

netzine-cms currently has handlers written for common web formats (.html, .php), most major image formats (.jpg, .gif, .png, and others), as well as most common programming formats (.js, .py, .rb, ...).

to see how netzine-cms handles different filetypes, [check out the demo](https://ashro.land/netzine-cms).



### planned

* assume subdirectories in ./pages are chapters and provide chapternav
* module for common audio formats
* more rigorous screenreader testing
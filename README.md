# netzine-cms 
### an ultra-lightweight static cms for netzines
---

### philosophy
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

### filetypes supported

#### code snippets
files ending in .py and .sh are rendered as inline code snippets.

#### web pages
files ending in .html and .php are rendered in full-width iframe.

#### text snippets
files ending in .txt are rendered as a column of 
whitespace-adherent in-line text.


### usage

requisites: standard web server (think apache or nginx), relatively 
modern PHP

clone this repository into /var/www/html or similar.

add files to ./pages. netzine-cms sorts pages in descending 
alphabetical order, so 2018-08-22.html will appear after 
2013-03-22.html.

### planned

- forward / back pagenav buttons
- back-end tool for publishing / unpublishing files in ./pages
	- userauth

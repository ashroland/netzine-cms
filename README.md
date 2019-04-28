# netzine-cms 
### an ultra-lightweight static cms for netzines
---

### philosophy
traditional zine-making is democratized book-binding with 
commonly available office supplies. 
netzine-cms aims to be as little as possible while also providing the tools 
necessary to author a zine online. no databases, no plug-ins, no serious 
dependency chain. it is an organizational engine as 
well as a method of collage.

### usage

requisites: web server, relatively modern PHP

clone this repository into /var/www/html or similar.

add files to ./pages. netzine-cms sorts pages in descending 
alphabetical order, so 2018-08-22.html will appear after 
2013-03-22.html.

### planned

- forward / back pagenav buttons
- back-end tool for publishing / unpublishing files in ./pages
	- userauth

# Tweelinks

A small API for displaying one's tweets that contains links. I use it to keep track of interesting links for my technological watch, since I tweet a lot of links that I find useful, interesting or worth reading.

A demo is available at [http://tweelinks.keiruaprod.fr/](http://tweelinks.keiruaprod.fr/)

A lot of things remain to do in order to make this API a real API (there's waaaaay to much coupling right now, but this was supposed to be a quick&dirty code at first, not something I meant to release).
Stuff that need to be decoupled:
- the coupling with the OAuth API
- the coupling with PDO
- the coupling with the display 
But right now, it fits what I need, and I probably won't go further.

## How does it works


It keeps a list of former tweets that contain links, and store them in a database.
The way I use it, when someone gets on the page, it tries to fetch new tweets and store them in the database.
Then, the cached tweets are displayed. Yup, many things could be improved (better caching, fetching less data from the db or pagination), but my database of links is pretty small

## Prerequisite

The current version is based on MySQL, and requires PDO and curl to be enabled. I've developped it under php 5.3.1, even though I did not take advantage of it as much as I could have (hello, psr0). 

## Installation

### Create a twitter application

It sucks a bit, but since APIv1.1 you need to authenticate through OAuth, and to have credentials for that. It's not that hard.

### Create a table tweelink in your database

    CREATE TABLE IF NOT EXISTS `tweelink` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tw_idstr` varchar(50) NOT NULL,
      `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `timestamp` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1695 ;

### Create a config.php file, based on config.php.dist.
You will need to provide the twitter's application info, to provide you mysql configuration info, and to provide an username

### That's it !
Now you can run index. It might take a while on the first execution because of all the curl's, but you see your tweets with links.
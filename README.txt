Labrador Dataset Browser
-----------------------------

Labrador is a web based tool which helps uers to find and download data which
has been processed in house and request new publicly available datasets.

You can find full instructions for using Labrador in the manual, which is in
the documentation folder of the distribution: documentation/labrador_manual.md

Labrador is written in PHP and Javascript with a mysql database backend to keep
track of the sample metadata. No data is stored inside Labrador, instead you 
simply point it to a directory on a server and it will read the appropriate data
out of there.

To get started with Labrador all you need is a computer on which you can run:

 - A webserver (the instructions assume apache, but any server could be used)

 - A mysql database server (this can be on a different machine if you prefer)

 - PHP

All of the instructions for setting up a new instance of Labrador can be found
in the manual. You can also see a screencast walkthrough video of installing
Apache, PHP, MySQL and Labrador on a blank server here: http://youtu.be/w1n4SuRtM3U

If you have any problems with Labrador you can report them in our bug tracking
system at:

http://www.bioinformatics.babraham.ac.uk/bugzilla/

..or you can send them directly to phil.ewels@babraham.ac.uk

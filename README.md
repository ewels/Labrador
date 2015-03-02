# Labrador Dataset Browser

Labrador is a web based tool which helps users to find and download in-house data.

Labrador is primarily designed to be used with publicly available datasets -
users can request new datasets with integrated SRA/ENA/DDBJ tools and enter
required metadata. Core bioinformaticians will be notified by e-mail and can
quickly and easily download and process the data.

Labrador is written to be publicly visible by all internal users, so future
researchers can view existing datasets and download without having to request
data from the core facility.

You can find full instructions for using Labrador in the
[manual](documentation/labrador_manual.md), which can be found in
the documentation folder of the distribution.


## Usage
I have recorded a video tutorial showing how to use Labrador for end-users:
[Introduction to Labrador](http://www.youtube.com/watch?v=m03HTQtSGFg).

I've also don a video tutorial for administrative usage (aimed at core bioinformaticians):
[Labrador Administration Tutorial](http://www.youtube.com/watch?v=eK58RMMc9Gg).


## Requirements

To get started with Labrador, you need a computer / server with:

* A webserver (the [installation instructions](documentation/labrador_manual.md#2-installation)
 assume apache, but any server could be used)
* A MySQL database server (this can be on a different machine if you prefer)
* PHP

You can find installation instructions in the
[documentation](documentation/labrador_manual.md#2-installation), plus
a screencast walkthrough video of installing Apache, PHP, MySQL and
Labrador on a blank server on [YouTube](http://www.youtube.com/watch?v=EE70lpp5Fwc).


## Release Notes

#### Labrador v0.2 - 2014-01-22
* New Features
  * Custom accession numbers
  * Labrador now checks for available updates
  * Enter an e-mail address in the search bar to show projects associated with that user
* Updates
  * Number of e-mails sent when adding projects and datasets cut down if you're an admin
  * Documentation - described how to set up Sendmail for the PHP `mail()` function
* Bug fixes
  * You can now add new papers whilst editing an existing project
  * Number of new datasets in e-mail notifications is now correct
  * Retrieving an accession when editing a project no longer warns you that it exists
  * Minor formatting fixes


#### Labrador v0.1 - 2013-11-28

This is the first public release of Labrador, although the system has been
in use at the Babraham Institute for around 6 months, so the code has
received some stress testing.


## Credits

Labrador was written by Phil Ewels (@ewels) whilst working at the
[Babraham Institute](http://www.babraham.ac.uk/) in Cambridge, UK. He now
maintains it from his new position at the
[Science for Life Laboratory](http://www.scilifelab.se/) in Stockholm, Sweden.


## Licence
Labrador is released with a GPL v3 licence. Labrador is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. For more information, see the [licence](LICENCE.TXT) that comes bundled with Labrador.

# Labrador Version History

## Labrador v0.3 (16 March 2021)

Labrador v0.3 includes a range of updates that have mostly been contributed by @darogan and @StevenWingett who have been using Labrador at other centres beyond the fair shores of the Babraham Institute.

* Updated documentation on how to run Labrador using a webserver inside a Docker container
* Improved `sendmail` setup - Updated instructions to use a new config file to setup `sendmail`.
* Added Docker container instructions - Updated the documentation to provide instructions on how to run Labrador inside a Docker container.
* Fix invalid filenames before writing SRA links file
* Fix the data dir permissions problem
* Make dataset lookups work for GEO accessions again
* Make new project directories get `777` permissions
* Symlink based failure fixes
* Conditional statements wrapped around `df` check and logo, plus removed custom images from img
* Simplified and tidied up the use of config variables for data and server locations
* Add a conditional for testing the storage situation on local server
* Implemented samtools path check, replace `zcat` with `gunzip -c` for OSX compatibility
* Fixed minor variable formatting in `header.php`
* Added a samtools path variable for setting in the config if required
* Remove hard coded links to be more generic for main Labrador repo
* Added PHPExcel and ParseDown submodules
* Added support for markdown reports
* PHP7 Compatability updates to split function in `files.php`, plus minor formatting
* Added PDF viewer to reports
* Base version customised for generic NGS projects
* Cleaned up datasets page a bit.
* Added last modified dates to homepage and datasets pages.
* Fixed datatables error on homepage.
* Got SRA links modal to work with selected table rows.
* Added option to save file list to server.
* Removed links to 'processing' page. Made javascript SRA links download. Added datatables.
* Updated `mysql_` to `mysqli_`

## Labrador v0.2 (22 January 2014)


### New Features

- Custom accession numbers
- Labrador now checks for available updates
- Enter an e-mail address in the search bar to show projects associated 
with that user Updates
- Number of e-mails sent when adding projects and datasets cut down if 
you're an admin
- Documentation - described how to set up Sendmail for the PHP mail() 
function

### Bug fixes

- You can now add new papers whilst editing an existing project
- Number of new datasets in e-mail notifications is now correct
- Retrieving an accession when editing a project no longer warns you that 
it exists
- Minor formatting fixes

<!---
# Labrador Sequence Management System Documentation
Note - this documentation can be read in a web browser in your installation of Labrador or at the Babraham Bioinformatics website:

http://<your labrador installation>/documentation/
http://www.bioinformatics.babraham.ac.uk/projects/labrador/documentation/

-->

# 1 Introduction

Labrador is a web based tool to manage projects and automate the processing of publicly available datasets.

Researchers can use it to search through previously processed data, find how it was analysed, read processing reports and download the relevant files to their computers. If a required dataset isn't yet available, they can request it by creating a new project - the information about the required data sets is then passed on to adminsitrators (typically a bioinformatics team), who can process it. The status of projects is tracked, and everything is kept together in a logical place.

Administrators can delegate the process of choosing the required datasets to researchers. Labrador automatically retrieves public data accession numbers and can write bash scripts to download and process data. This helps to standardise in-house processing and streamline pipelines. Researchers interested in a project are notified when new datasets are added or processing is complete.

# 2 Installation

To see a walk-through of how to install Apache, PHP, MySQL and Labrador on a blank server, please see the following video tutorial: <http://youtu.be/EE70lpp5Fwc>

## 2.1 Pre-requisites

### 2.1.1 Hardware

The hardware requirements for Labrador are very minimal.  No intensive processing is done within the system so a basic server class, or even desktop machine should easily be able to run the system.  In order to store the data you will need to have a large robust storage system, but this need not be present on the local machine as long as it can be made visible to the machine running Labrador (eg via NFS, Samba etc).  Since Labrador can read and transmit very large data files it would be very beneficial to have a fast (preferably gigabit) network connection to the machine running Labrador.

### 2.1.2 Software

Labrador itself is written in PHP and uses a MySQL database to store the data.  The user interface is provided through a web server, and default configuration files are provided for the apache web server, although other servers should work equally well.

Users do not need any software to use Labrador other than a web browser and an installation of Java for the bulk file download tool.

## 2.2 Configuration

Installation of Labrador is as simple as downloading the installation bundle from the project web site and then extracting it into the location you wish to install the program.  On a unix system you would do this using:

```bash
tar -xzvf labrador_vXX.tar.gz
```

On a windows system you would use a decompression utility such as 7zip or WinZip to unpack the files from the installation bundle

There are a number of different configuration steps you need to take before launching system for the first time.

### 2.2.1 Server Setup

To run Labrador you need a working web server. It is recommended that Apache is installed, along with PHP and MySQL.

### 2.2.2 Database Creation

Before starting the system you need to create the database which is going to be used by Labrador.  The Labrador installation contains an SQL file which will set up and configure a database. The file is in the `conf` folder of the installation. If you are using a MySQL install on the same machine as your webserver and you're happy to use the default configuration, you can install the database using:

```bash
mysql -u root -p < labrador_database.sql
```

This script will create a database and a user, both called `labrador`. It will then populate the database with the empty table structure needed to run Labrador.

You will be prompted for the MySQL root password and the database will be created.  You can substitute the root account for any other MySQL account with sufficient privileges to create a new database and add user permissions to it. Warning - running this file with an existing Labrador database will overwrite all previous data.

If you want to put the database on a different machine to the webserver, or you want to change the username used to connect to the database then you will need to edit the SQL file and change the `GRANT` statements at the bottom to use the username and machine you would prefer to use.

For example you could change the statement:

```sql
GRANT ALL PRIVILEGES ON labrador.* TO 'labrador'@'localhost';
```

to

```sql
GRANT ALL PRIVILEGES ON labrador.* TO 'mylocaluser'@'somemachine.example.com';
```

### 2.2.3 Webserver Configuration

Our recommended configuration is to install Labrador in a directory outside your document root and then adjust your webserver configuration to allow it to find the installation and map it to a URL.  We have included a default apache configuration file under `conf/labrador_apache.conf` which you can either copy into your apache configuration directory (normally something like `/etc/httpd/conf.d/`) or which you can copy into your main `httpd.conf` file.

The example configuration will allow you to access Labrador at a URL of `/labrador/` under your top level domain (eg `http://yourserver/labrador/`).  You will need to edit the configuration file to change the file paths shown to reflect the directory in which you have installed Labrador.

Once you have added the configuration file you will need to restart your web server for the changes to take effect.

### 2.2.4 Sendmail Setup

If you set up Labrador and find that you aren't receiving any e-mails, it could be that the PHP `mail()` function isn't happy. This could be due to a missing copy of sendmail, [which is required](http://www.php.net/manual/en/mail.requirements.php).

Sendmail can be installed as follows:

```bash
yum install sendmail sendmail-cf
cd /etc/mail/
nano sendmail.mc

## update the SMARTHOST line: remove dnl at start of line and enter your smtp address

./make
service postfix stop
service sendmail start
```

Please note that this could be server specific and changing these settings can affect the performance of other services running on the same machine..

### 2.2.5 Data Folders

If you want Labrador to be able to locate and serve data you need to make these available on the system on which Labrador is installed.  Labrador uses a directory for each project, named after that project identifier.

eg You would have a run data folder with a structure like:

```txt
..Somewhere
           \
           Labrador
                   \
                   \ Project_One_2011
                   \ Project_Two_2013
```

### 2.2.6 Labrador Configuration File

In order to connect to your database and find your data Labrador needs to know some information about your setup.  All of the pieces of information the system needs are configured in a file called `labrador_config.php` which is in the conf directory of your Labrador installation.  A template configuration file called `labrador_config.php.example` is provided, and you should copy this to a file called `labrador_config.php` in the same directory and then edit this to include the correct information for your site. Hopefully all the pieces of information in there are self-explanatory, and you need to ensure that they reflect your local environment

Once you have all of these elements in place you should be able to go to `http://yourserver/labrador/` and start using the system.

## 2.3 Docker Installation Walkthrough

An alternative and simple way to get Labrador working on your system is to run the LAMP (Linux/Apache/MySQL/PHP) setup inside a [Docker](https://www.docker.com/) container.  While the LAMP setup is contained within the container, the actual website files, MySQL database and datasets are kept on your host machine.  To use this installation, follow the steps below.  You will need Git and Docker installed on your machine for this to work.

> NOTE: Depending on your installation, you may need to run the following `docker` commands with superuser (`sudo`) privileges.
> Just prefix `sudo` to each command. For example, `sudo docker ps -a`.

1. Create the directories on your host machine where the MySQL and the data files should be hosted.

2. Clone Labrador from GitHub:

    ```bash
    git clone https://github.com/ewels/Labrador.git
    ```

    (These instructions were tested with git commit [`233758f`](https://github.com/ewels/Labrador/commit/233758f9a2c2f132cf7640189bc155fd7b452a8b)).

    Alternatively, download the latest release from GitHub and unzip/untar.

3.  In order to connect to your database and find your data Labrador needs to know some information about your setup.  All of the pieces of information the system needs are configured in a file called `labrador_config.php` which is in the conf directory of your Labrador installation.  A template configuration file called `labrador_config.php.example` is provided, and you should copy this to a file called `labrador_config.php` in the same directory and then edit this to include the correct information for your site. Hopefully all the pieces of information in there are self-explanatory, and you need to ensure that they reflect your local environment (i.e. within the container).

4. Configuring the email may depend on your requirements, host machine and local IT infrastructure.  However the configuration file `sendmail_config.sh.example` (also in the conf directory) should work for most systems.  Open this file in a text editor and edit as required and save as `sendmail_config.sh`.  The instructions in the file will tell you what needs editing.

5. Create the Docker container, using the command below (remember to replace the MySQL, Data and Labrador folder paths to match that on your host system):

    ```bash
    docker create \
      --name labrador_website \
      -t \
      -p "8000:80" -p "13306:3306" \
      -v [Path to Labrador website files folder]:/app \
      -v [Path to MySQL folder]:/var/lib/mysql \
      -v [Path to Data folder]:/mnt \
      mattrayner/lamp:latest-1404-php5
    ```

    This creates a container named `labrador_website`, based an a minimal Ubuntu OS running PHP5.  The command also exposes ports on the container useful for data exchange between the container and the host machine.

6. Check the container was created:

    ```bash
    docker ps -a
    ```

7. Start the container:

    ```bash
    docker start labrador_website
    ```

8. Check the container is running:

    ```bash
    docker ps
    ```

9.  If you need to create a new MySQl database, then do so with the command below.  Note: do NOT do this if you wish to use an existing Labrador database, as this will overwrite your data.
    
    ```bash
    mysql -u root -p < labrador_database.sql
    ```

10. You can enter the running container (opening a bash shell) with the command:

    ```bash
    docker exec -it labrador_website /bin/bash
    ```

    You can navigate around the container now, or install software.  For example, to install the simple text editor nano:

    ```bash
    apt-get install nano
    ```

11. Still within the container, install and configure sendmail:
    ```bash
    bash /app/conf/sendmail_config.sh
    ```

    This should send a test message to the admin email address to verify that everything is working.

    If required, please read [sendmail](https://www.proofpoint.com/us/products/email-protection/open-source-email-solution) documentation for further assistance.

12. To leave the container, type:

    ```bash
    exit
    ```

13. Should you need to stop the webserver for some reason (e.g. adjusting configuration), enter:

    ```bash
    docker stop labrador_website
    ```

    And if you wish to delete the container, you can with the command:

    ```bash
    docker rm labrador_website
    ```

# 3 Normal Usage

Once set up, Labrador can be viewed and used by anyone without any authentication for basic use. Below, the basic functionality is described for a user who is not logged in. Then the additional features granted to authenticated users (normal and then administrators) is described after that.

You can see a video tutorial of how to use Labrador as an end-user here: <http://www.youtube.com/watch?v=m03HTQtSGFg>

## 3.1 Searching projects

Often you want to find a specific project within Labrador that you know about. There is a search bar at the top of every page - entering a search term and submitting the form will take you to a results page with tabs for projects, publications and datasets.

As you start typing into the search bar, Labrador tries to match project identifiers. Any matching identifiers will be shown as a drop down list - these can be selected with the keyboard or the mouse, clicking or pressing enter will take you directly to that project.

## 3.2 Browsing projects

The homepage of Labrador is designed for browsing projects. The main panel lists all projects known to Labrador, colour coded by their status. Clicking the different statuses in the key will filter the table by status. The left panel contains a number of filters, enabling you to quickly filter by a text string, project name (first letter), species and data type. Clicking anywhere on a project will go to that project.

## 3.3 Viewing projects

Once you've selected a project, you'll be taken to its page. Project pages always have a side navigation allowing you to view different aspects of the project.

All project pages share a header. This shows the project identifier and any project accession numbers. Under this the project status is shown, the names of people linked to the project, the bioinformatician assigned to process the data and the location of the project directory on the server.

### 3.3.1 Project Details

The Project Details page outlines the overarching meta data related to the project. Any publications relating to the project are shown, along with a project title and description. A full list of contacts is shown as well as a history of logged events showing any activity related to the project.

### 3.3.2 Datasets

The Datasets page shows all datasets associated with a project. A dataset describes data from a single experiment. Name, species, cell type, data type and accession codes are shown. You can click column headers to sort the table by that column and use the text box at the top to filter by text string. Clicking an accession number will open the relevant database page in a new window.

### 3.3.3 Processing

The processing page shows logs of the processing commands that have been run on each dataset, if they were generated using Labrador. This gives a way to see how data was processed, even if it was done some time ago.

### 3.3.4 Reports

Labrador can be configured to show processing reports within the browser, making it quick and easy to get an overview of the quality of a dataset and how well the processsing has worked. Recognised report files will be shown in dropdown boxes in the top right. Selecting an option will refresh the page.

### 3.3.5 Files

The Files page shows all files found within the project folder on the server. You can sort the table by it's headers - name, file size, genome and filename. You can filter the fiels by dataset, by free text string and by type (aligned, raw etc. Uses common file extensions).

Clicking a filename will download that file to your computer. If you want to download multiple files you can select their rows and click the download button at the bottom of the page. This launches the downloads in a Java applet which maintains the directory structure and runs the downloads in series.

# 4 Use by registered users

To do some functions in Labrador you have to be logged in. This is so that any bioinformaticians processing the data for you know who to contact about any questions.

## 4.1 Creating an account

You can register to use Labrador by from the login page by clicking `Log In / Register` at the top of any page. This will show a modal window with two tabs - click `Register` and fill in the associated form.  Upon submission the system will send out an email containing a link which will activate the account and allow you to log in. If your group is not present in the drop down list, an administrator can add it in the Labrador config file.

## 4.2 Logging in

Once registered you can log in by clicking the same `Log In / Register` link in the top right of any page. You will be returned to the same page upon logging in, but please note that you'll lose any data that you've entered into the page and not saved. If you've forgotten your password you can reset it by clicking the `click here` link on the log in form.

## 4.3 Requesting a new project

If you've searched Labrador for a particular dataset and not been able to find it, you can request it be processed by your bioinformatics team by clicking `Create New Project` at the top of any page.

Most repositories of publicly available next-generation sequencing (GEO, SRA, ENA, DDJB) have a similar structure - a single publication usually has a single project accession. Contained within this are multiple datasets, which correspond to different experiments. Labrador mirrors this structure.

The best place to start when requesting a new project is from such a public repository. Entering a project accession and clicking the magnifying glass will look the accession up from the relevant repository and automatically fill in as much of the form as possible. For GEO and SRA accessions, this typically means the title, description, publication and project identifier.

You can enter as many accession numbers as you like. In fact, the more you add, the better. Having all relevant accession numbers is important when adding datasets (see below).

The only item that is mandatory is the project identifier. This needs to be unique within Labrador and will correspond to a directory name on the server. Entering an identifier that already exists will produce an error.

### 4.3.1 Adding new datasets

Once you have created a new project, you need to add the datasets that you are interested in. Clicking `Add datasets` after creating the project will take you to this page.

Labrador can make your life easier by retrieving all of the datasets associated with a project accession entered under Project Details. These appear as buttons at the top of the page. Clicking a button will query that repository for all datasets associated and enter them into the table below. You may need to try searching several different accessions before finding all of the datasets that you need.

Before adding datasets, make sure that you delete any that you are not interested in (more datasets means more delay in processing). Also make sure that all of the fields are filled in. When you're done, click Save. The bioinformaticians will be e-mailed to tell them that the datasets have been added and need to be processed.

### 4.3.2 Requesting new datasets for an old project

Sometimes, it may be that a project that you're interested in has already been added and processed. However, the specific datasets that you want may have been omitted. In this case, you can still request new datasets just as you would for a new project. Go to the project page and click `Datasets` on the left hand side. You should see a button on the top right saying `Add Datasets` (only visible whilst logged in). The bioinformaticians running Labrador will get an e-mail notifying them of the request.

### 4.3.3 Adding yourself as a contact for a project

When you create or add datasets to a project, you will automatically be assigned as a contact. This means that you will receive e-mail notifications when the project status changes (for example, from currently being processed to complete). You can add or remove yourself from any project by clicking `Add / Remove as Contact` in the top right of the project page.

# 5 Use by Administrators

Administrators can do everything a normal user can do, so only information about additional options will be shown in this section.

You can see a video tutorial of how to use Labrador as an administrator here: <http://www.youtube.com/watch?v=eK58RMMc9Gg>

## 5.1 Creating a new account

Administrators can create user accounts like any normal user. What makes them an administrator is having their e-mail address listed at the top of the Labrador `labrador_config.php` file.

## 5.2 Administering projects

When creating or editing a project, administers have some additional fields visible.

Under the Project Contacts heading, the contacts for the project can be set - these people will receive e-mails when the project is updated. The project can also be assigned to an administrator - clicking a name will fill in their e-mail address. The project status can also be changed here. This is important to keep track of ongoing work with projects. A change in project status will cause all project contacts to be e-mailed.

The other administrator-only feature on this page is the `Delete Project` button. This is to be used with caution - it will permanently delete records of all processing, datasets, publications and the project from the Labrador dataset. There is no way to undo this action. Labrador will not delete any data on the server, so any underlying files will be unaffected.

### 5.2.1 Administering datasets

Administrators are able to edit the details of any datasets for all projects. Regular users are only able to edit datasets from projects that they are listed as a contact for.

### 5.2.1 Administering processing

Administrators are able to delete processing records for any project. Creating new processing scripts is dealt with below.

# 6 Creating Processing Scripts

One of the key benefits of Labrador is its ability to create custom bash scripts to automate common analysis pipelines. Notably, Labrador knows the accession codes for each dataset and can generate the download links required and provide sensible filenames. In our experience, this can save a great deal of manual work and cuts down on human errors.

## 6.1 New Processing Scripts

Once project and dataset details have been added to Labrador, new processing scripts can be added by going to the `Processing` tab and clicking the `Create New Processing Script` in the top right. This feature is only available to administrators. Clicking this button will take you to a new page.

### 6.1.1 Step 1 - Choose Datasets

First - select the datasets that you would like to generate the processing script for. This is typically all of the datasets of a certain data type. By default all datasets are already selected, in green.

### 6.1.2 Step 2 - Choose Processing

This is the crucial panel for creating your processing script. It is split into two sections - configuration options at the top and processing steps below. All of this can be customised in the Labrador config file. See below for details.

It may be that you have multiple servers upon which you can run your processing. If so, it's likely that these servers require differing commands for the same tasks (genome paths, job management and so on). You can choose which server you want to run your processing with here, and the script will be customised for that choice. For some processing you may need to specify a reference Genome. This box may be greyed out if not required.

All pipelines use a series of steps, done in order for each accession in each dataset. The `Add Processing Step` and `Delete Processing Step` buttons do just that - add and delete steps. `Shortcuts` are links provide quick ways to select a common pipeline of multiple steps.

Each step has a drop-down box where you can select a processing step template. Clicking one of these options will auto-fill the text box to the right with a template. As this needs to be specific for each accession and dataset, variables in squiggly brackets are used - for example, `{{SRA}}` will be replaced with the SRA accession. These templates can be manually edited if you would like to change a setting.

### 6.1.3 Step 3 - Preview Bash Script

Once your datasets, configuration and processing steps are set up, your bash script should be ready. If there are any problems (for example, no Genome is set when one is required), an error message will be shown in the grey box. If everything is working as it should, you should be shown the contents of the bash script that has been written.

### 6.1.4 Step 4 - Save Script

If the preview looks good, you can get Labrador to save your bash script for you. If the project folder does not yet exist, it will be created. If the bash script already exists, it will be overwritten. You can change the desired filename, then click `Save Bash Script`.

### 6.1.5 Step 5 - Running the Script

Outside of Labrador, you can now go to the directory with the new bash script in using your favourite command line tool and run the script. Typically you might do this by executing `bash filename.sh`. If you would like the process to run in the backround you can use `nohup bash filename.sh > log.out`

## 6.2 Use with Cluster Flow

Based on the success of our use of Labrador's pipelines at the Babraham Institute, we went on to create a new piece of software called Cluster Flow. This is designed specifically to run on a distributed GRIDEngine cluster environment, and is capable of quite a bit more than pipeline's simple bash scripts. Cluster Flow is available on the [Babraham Bioinformatics projects page](http://www.bioinformatics.babraham.ac.uk/projects/).

Labrador works very well with Cluster Flow (or indeed, any other package that requires only download links). Selecting `Cluster Flow Download` in the Step 1 drop-down box will create a simple file with a list of download links followed by a nicer replacement filename (tab delimted).

## 6.3 Editing and Creating new Processing Pipelines

Virtually everything in the `Create Processing Script` page can be customised through the Labrador configuration file. Detailed instructions can be found within this file as comments.

# 7 Final Comments and Getting Help

If you have any feedback or suggestions for Labrador, or find any bugs, please get in touch at [phil.ewels@babraham.ac.uk](mailto:phil.ewels@babraham.ac.uk)

The Labrador Project page with the download of the latest version can be found at <http://www.bioinformatics.babraham.ac.uk/projects/index.html>

Labrador was written by [Phil Ewels](http://phil.ewels.co.uk/) whilst working as a postdoctoral research scientist for [Wolf Reik](http://www.babraham.ac.uk/our-research/epigenetics/reik/), at the [Babraham Institute](http://www.babraham.ac.uk/), Cambridge, UK. It was first released in January 2014.

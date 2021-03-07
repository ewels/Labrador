#!/bin/bash

######################################
# sendmail config
#
# This file is for configuring the Labrador
# email using the software sendmail.
#
# Edit the lines of below code needing ATTENTION
# as described.  You need to provide your:
# 1) admin email address (currently topdog@labrador.ac.uk)
# 2) webserver name (currently mail.labrador.internal)
# 3) admin username (currently topdog)
# 
# When the configuration takes place, reply Yes [Y] to
# on-screen prompts.
######################################

apt-get install -q -y ssmtp mailutils

# Root is the person who gets all mail for userids < 1000

################################################################
# ATTENTION: Change to your admin email address here
echo "root=topdog@labrador.ac.uk" >> /etc/ssmtp/ssmtp.conf      
################################################################

################################################################
# ATTENTION: Change to your mail server name here (remember to keep ':587')
echo "mailhub=mail.labrador.internal:587" >> /etc/ssmtp/ssmtp.conf   
################################################################

# Alternatively use address/password
#RUN echo "AuthUser=your@gmail.com" >> /etc/ssmtp/ssmtp.conf
#RUN echo "AuthPass=yourGmailPass" >> /etc/ssmtp/ssmtp.conf

echo "UseTLS=YES" >> /etc/ssmtp/ssmtp.conf
echo "UseSTARTTLS=YES" >> /etc/ssmtp/ssmtp.conf

# Set up php sendmail config
mkdir /usr/local/etc/php
mkdir /usr/local/etc/php/conf.d
echo "sendmail_path=sendmail -i -t" >> /usr/local/etc/php/conf.d/php-sendmail.ini
echo "FromLineOverride=YES" >> /etc/ssmtp/ssmtp.conf

################################################################
# ATTENTION: Change to your username:admin_email:mailserver
echo "topdog:topdog@labrador.ac.uk:mail.lmb.internal" >> /etc/ssmtp/revaliases 
################################################################

################################################################
# ATTENTION: Change to your username
useradd -ms /bin/bash top.dog
################################################################

sendmailconfig
service sendmail start

################################################################
# ATTENTION: Change to the admin email address
echo "Subject:Test mail 4\n\n Labrador test email\n" | sendmail topdog@labrador.ac.uk
################################################################

<?php
// $Id: createxml.php,v 1.1 2005/01/24 08:20:30 mhaller Exp $
//
// JDownload
// sample php script to dynamically generate XML Data file
// Copyright (c) 2005-2006 Mike Haller Systemservice and smartwerkz.com
//
// http://jdownload.jupload.biz
//

// Make it pretty in the browser
header('Content-Type: text/xml');

// Create an empty XML Document with UTF-8 encoding
$dom = new DOMDocument('1.0', 'UTF-8');

// Create an empty root element
$rootelement_download= $dom->createElement('download');

// Create a folder element and give him a name
$samplefolder1 = $dom->createElement('folder');
$samplefolder1->setAttribute('name','SampleFolder1');

// Create a subfolder element and give him a name
$subfolder1 = $dom->createElement('folder');
$subfolder1->setAttribute('name','SubFolder1');

// Create three files, and give them names
// One file will be auto-uncompressed after being downloaded
$uncompressfile = $dom->createElement('file');
$uncompressfile->setAttribute('name','sampleUncompress.zip');
$uncompressfile->setAttribute('uncompress','true');
$file1 = $dom->createElement('file');
$file1->setAttribute('name','sampleFile1.zip');
$file1->setAttribute('id','1');
$file2 = $dom->createElement('file');
$file2->setAttribute('name','sampleFile2.zip');
$file2->setAttribute('id','2');

// The files need URLs where we can download them
// we just use the same URL for all three files for
// demonstration purposes.
$url1 = $dom->createElement('url');
$url2 = $dom->createElement('url');
$url3 = $dom->createElement('url');

$url1->appendChild($dom->createTextNode('sample.zip'));
$url2->appendChild($dom->createTextNode('sample.zip'));
$url3->appendChild($dom->createTextNode('sample.zip'));

// add the URLs to the files
$file1->appendChild($url1);
$file2->appendChild($url2);
$uncompressfile->appendChild($url3);

// add the files to the folders
$subfolder1->appendChild($file1);
$subfolder1->appendChild($file2);

$samplefolder1->appendChild($subfolder1);
$samplefolder1->appendChild($uncompressfile);

// add the folders to the root element
$rootelement_download->appendChild($samplefolder1);

// add the root element to the DOM (Document Object Model)
$dom->appendChild($rootelement_download);

// just print out the file. this will be later read by JDownload
$dom->normalize(); 
print $dom->saveXML();

?>
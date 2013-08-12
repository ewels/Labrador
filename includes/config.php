<?php

/*
* config.php
* ---------
* Some configuration and customisation options.
*
*/

$labrador_url = 'http://bilin1/labrador_dev/';
$data_root = '/data/pipeline/new_public/TIDIED/';

$project_groupAssignQuickFill = array(
	'Wolf Reik' => 'Reik',
	'Peter Fraser' => 'Fraser',
	'Anne Corcoran' => 'Corcoran',
	'Jon Houseley' => 'Houseley',
	'Gavin Kelsey' => 'Kelsey',
	'Sarah Elderkin' => 'Elderkin'
);

$project_assignQuickFill = array(
	'simon.andrews@babraham.ac.uk' => 'Simon',
	'felix.krueger@babraham.ac.uk' => 'Felix',
	'steven.wingett@babraham.ac.uk' => 'Steven',
	'laura.biggins@babraham.ac.uk' => 'Laura',
	'anne.segonds-pichon@babraham.ac.uk' => 'Anne',
	'phil.ewels@babraham.ac.uk' => 'Phil'
);


$raw_filename_filters = array(
	'.fastq',
	'.fastq.gz',
	'.fq',
	'.fq.gz',
	'.sra'
);

$aligned_filename_filters = array(
	'.bam',
	'.sam',
	'.bowtie',
	'.bowtie.gz',
	'.txt.gz',
	'.smk',
	'.smk.gz'
);

$reports_filename_filters = array(
	'.log',
	'.nohup',
	'.out',
	'.alignment_overview.png',
	'bismark_PE_report.txt',
	'.M-bias.txt',
	'.M-bias_R1.png',
	'.M-bias_R2.png',
	'.bam_splitting_report.txt',
	'.deduplication_report.txt',
	'_fastqc.zip',
	'trimming_report.txt',
	'_screen.png',
	'_screen.txt'
);

$download_instructions = "Aligned files contain genome co-ordinates and can be imported directly into SeqMonk.
Raw files contain the original sequence read data.";


//////////////////////
// CUSTOM REPORT TYPES
//////////////////////

$report_types = array(
	'fastqc' => 'FastQC Reports',
	'fastq_screen' => 'FastQ Screen',
	'alignment_overview' => 'Bismark Alignment Overview Plots',
	'm_bias' => 'Bismark M-Bias Reports',
	'ditag_classification' => 'HiCUP Di-Tag Analysis',
	'cis-trans' => 'HiCUP <em>cis</em>/<em>trans</em> Analysis'
);

function report_match ($file, $type) {
	switch($type) {
	
		case 'fastqc':
			return basename($file) == 'fastqc_report.html';
			
		case 'fastq_screen':
			return substr($file, -11) ==  '_screen.png';
			
		case 'alignment_overview':
			return substr($file, -23) == '.alignment_overview.png';
			
		case 'm_bias':
			return stripos(basename($file), 'M-bias') && substr($file, -4) == '.png';
			
		case 'ditag_classification':
			return substr($file, -29) == 'pair_ditag_classification.png';
			
		case 'cis-trans':
			return substr($file, -18) == '.sam_cis-trans.png';
			
		default:
			return false;
	}
}

function report_naming ($path, $type) {
	switch($type) {
	
		case 'fastqc':
			return substr(basename(dirname($path)), 0, -7);
			
		case 'fastq_screen':
			return substr(basename($path),0, -11);
			
		case 'alignment_overview':
			return substr(basename($path),0, -23);
			
		case 'm_bias':
			return substr(basename($path),0, -4);
			
		case 'ditag_classification':
			return substr(basename($path),0, -30);
			
		case 'cis-trans':
			return substr(basename($path),0, -18);
			
		default:
			return false;
	}
}





?>
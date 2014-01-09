<?php

##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################

/*
* labrador_config.php
* ---------
* Configuration and customisation options.
*
*/


/////////////
/// SETUP
/////////////

//// The base URL at which a browser can load Labrador
$labrador_url = 'http://bilin1/labrador/';

//// The directory path at which data within Labrador should be accessed
$data_root = '/data/pipeline/pubcache/TIDIED/';

//// Support e-mail address. Will be notified when new unassigned projects are added.
$support_email = 'babraham.bioinformatics@babraham.ac.uk';

//// Title and subtitle (used on homepage)
$homepage_title = 'Labrador Dataset Browser';
$homepage_subtitle = 'A database of datasets processed by the BI Bioinformatics group.';

//// MySQL database details
// Typical permissions required with database and user called labrador:
// GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER, LOCK TABLES ON `labrador`.* TO 'labrador'@'localhost'
$db_database = 'labrador';	// database name
$db_user = 'labrador';		// database username
$db_password = false;		// database password
$db_host = 'localhost';		// database host (default: localhost)

//// List of administrators
// Keys should be e-mail addresses (used for notifications)
// Values should be Names (used for quick insertion links on project page)
// eg. 'user@email.com' => 'Name',
$administrators = array(
	'simon.andrews@babraham.ac.uk' => 'Simon',
	'felix.krueger@babraham.ac.uk' => 'Felix',
	'steven.wingett@babraham.ac.uk' => 'Steven',
	'laura.biggins@babraham.ac.uk' => 'Laura',
	'anne.segonds-pichon@babraham.ac.uk' => 'Anne',
	'phil.ewels@babraham.ac.uk' => 'Phil'
);

//// Groups (used for user registration)
// Keys should be full length names
// Values should be shortened versions (no spaces)
$groups = array(
	'Wolf Reik' => 'Reik',
	'Peter Fraser' => 'Fraser',
	'Anne Corcoran' => 'Corcoran',
	'Jon Houseley' => 'Houseley',
	'Gavin Kelsey' => 'Kelsey',
	'Sarah Elderkin' => 'Elderkin',
	'Peter Rugg-Gunn' => 'RuggGunn',
	'Bioinformatics' => 'Bioinformatics'
);






//////////////////////
// DOWNLOAD SETTINGS
//////////////////////

//// Files instruction text (shown in filter box on Files page)
$download_instructions = "Aligned files contain genome co-ordinates and can be imported directly into SeqMonk.
Raw files contain the original sequence read data.";

//// Filename filters for 'Projects' filter button
// Simple array of extensions. Should match end of file name. eg. '.bam', '.fastq.gz'
$project_filename_filters = array(
	'.smk',
	'.smk.gz'
);

//// Filename filters for 'Aligned' filter button
$aligned_filename_filters = array(
	'.bam',
	'.sam',
	'.bam.gz',
	'.sam.gz',
	'.bowtie',
	'.bowtie.gz',
	'.txt.gz'
);

//// Filename filters for 'Raw' filter button
$raw_filename_filters = array(
	'.fastq',
	'.fastq.gz',
	'.fq',
	'.fq.gz',
	'.sra'
);

//// Filename filters for 'Reports' filter button
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





/////////////////////////
// PROCESSING PIPELINES
/////////////////////////

//// Servers available for creating processing scripts
// Key: simple text key used to identify server below
// Value: associative array. Needs key value pairs for the full name of 
//  the server and whether queueing is enabled. Setting queueing to true
//  enables some basic support for a GRIDEngine queueing system.
// Example: 'server1' => array('name' => 'Server No. 1', 'queueing' => 'false'),
$processing_servers = array(
	'rocks1' => array('name' => 'The Cluster', 'queueing' => 'true'),
	'bilin1' => array('name' => 'Bilin 1', 'queueing' => 'false')
);


//// Processing Steps
// Contains arrays to group drop-down into sections. Keys are name of each dropdown optgroup.
// Each array contains keys which are a unique identifier for the
// processing step. Then an array with a name, a processing unit and whether the step
// requires a genome.
// Example:
/*
$processing_steps = array(
	'Pre-processing' => array(
		'get_sra' => array(
			'name' => 'Download SRA',
			'unit' => 'accession_sra',
			'requires_genome' => 'false'
		), 
		'sra_dump' => array(
			'name' => 'SRA to FastQ',
			'unit' => 'accession_sra',
			'requires_genome' => 'false'
		) 
	),
	'Alignment' => array(
		'bowtie' => array(
			'name' => 'Bowtie',
			'unit' => 'accession_sra',
			'requires_genome' => 'true'
		) 
	)
);
*/

$processing_steps = array(
	'Pre-processing' => array(
		'cf_download' => array( 'name' => 'Cluster Flow Download', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'get_sra' => array( 'name' => 'Download SRA', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'sra_dump' => array( 'name' => 'SRA to FastQ', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'fastqc' => array( 'name' => 'FastQC', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'fastq_screen' => array( 'name' => 'Fastq Screen', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'trim_galore_se' => array( 'name' => 'Trim Galore, Single End', 'unit' => 'accession_sra', 'requires_genome' => 'false' ),
		'trim_galore_pe' => array( 'name' => 'Trim Galore, Paired End', 'unit' => 'accession_sra', 'requires_genome' => 'false' )
	),
	'Alignment' => array(
		'bowtie1_se' => array( 'name' => 'Bowtie 1 SE', 'unit' => 'accession_sra', 'requires_genome' => 'true' ), 
		'bowtie1_pe' => array( 'name' => 'Bowtie 1 PE', 'unit' => 'accession_sra', 'requires_genome' => 'true' ), 
		'bowtie2' => array( 'name' => 'Bowtie 2', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'tophat_se' => array( 'name' => 'Tophat SE', 'unit' => 'accession_sra', 'requires_genome' => 'true' ), 
		'tophat_pe' => array( 'name' => 'Tophat PE', 'unit' => 'accession_sra', 'requires_genome' => 'true' ), 
		'bismark_se' => array( 'name' => 'Bismark, Single End', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'bismark_pe' => array( 'name' => 'Bismark, Paired End', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'bismark_pbat' => array( 'name' => 'Bismark, PBAT', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
	),
	'Post-Processing' => array(
		'email_assigned' => array( 'name' => 'Send Assigned E-mail', 'unit' => 'project', 'requires_genome' => 'true' )
	)
);

//// Processing commands
// Template commands to use for each step on each server.
// Keys correspond to processing step keys defined above.
// Values are an array with keys corresponding to server ids.
// These values contain the commands. Squiggly brackets should
// be used to code for dynamic values.
// Example:
/*
$processing_codes = array(
	'get_sra' => array(
		'server1' => 'wget -nv {{sra_url_wget}}'
	)
);
*/
$processing_codes = array(
	'cf_download' => array(
		'rocks1' => "{{sra_url}}\t{{fn}}.sra",
		'bilin1' => "{{sra_url}}\t{{fn}}.sra"
	),
	'get_sra' => array(
		'rocks1' => 'echo "wget -nv {{sra_url_wget}}" | qsub -V -cwd -pe orte 1 -l vf=1G -o {{fn}}_download.out -j y -m as -M {{assigned_email}} -N download_{{fn}}',
		'bilin1' => 'wget -nv {{sra_url_wget}}'
	),
	'sra_dump' => array(
		'rocks1' => 'echo "fastq-dump --split-files ./{{fn}}.sra" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_fqdump.out -j y -m as -M {{assigned_email}} -N dump_{{fn}} -hold_jid download_{{fn}}',
		'bilin1' => 'fastq-dump --split-files ./{{fn}}.sra'
	),
	'fastqc' => array(
		'rocks1' => 'echo "fastqc  {{fn}}_1.fastq" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_1_fastqc.out -j y -m as -M {{assigned_email}} -N fastqc_{{fn}}_1 -hold_jid dump_{{fn}}',
		'bilin1' => 'fastqc {{fn}}_1.fastq'
	),
	'fastq_screen' => array(
		'rocks1' => 'echo "fastq_screen --subset 100000 {{fn}}_1.fastq" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_1_fqscreen.out -j y -m as -M {{assigned_email}} -N screen_{{fn}}_1 -hold_jid dump_{{fn}}',
		'bilin1' => 'fastq_screen --subset 100000 {{fn}}_1.fastq'
	),
	'trim_galore_se' => array(
		'rocks1' => 'echo "trim_galore --fastqc {{fn}}_1.fastq" | qsub -V -cwd -pe orte 2 -l vf=4G -o {{fn}}_trimming.out -j y -m as -M {{assigned_email}} -N trim_{{fn}} -hold_jid dump_{{fn}}',
		'bilin1' => 'trim_galore --fastqc {{fn}}_1.fastq'
	),
	'trim_galore_pe' => array(
		'rocks1' => 'echo "trim_galore --paired --trim1 --fastqc {{fn}}_1.fastq {{fn}}_2.fastq" | qsub -V -cwd -pe orte 2 -l vf=4G -o {{fn}}_trimming.out -j y -m as -M {{assigned_email}} -N trim_{{fn}} -hold_jid dump_{{fn}}',
		'bilin1' => 'trim_galore --paired --trim1 --fastqc {{fn}}_1.fastq {{fn}}_2.fastq'
	),
	'bowtie1_se' => array(
		'rocks1' => 'echo "bowtie -q -t -p 8 -m 1 --best --strata --chunkmbs 2048 -S {{genome_path}} {{fn}}_1_trimmed.fq | samtools view -bS - > {{fn}}_1.bam" | qsub -V -cwd -l vf=4G -pe orte 8 -o {{fn}}_alignment.out -j y -m as -M {{assigned_email}} -N bowtie_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'bowtie -q -m 1 -p 4 --best --strata --chunkmbs 512 -S {{genome_path}} {{fn}}_1_trimmed.fq | samtools view -bS - > {{fn}}_1.bam'
	),
	'bowtie1_pe' => array(
		'rocks1' => 'echo "bowtie -q -t -p 8 -m 1 --chunkmbs 2048 -S {{genome_path}} -1 {{fn}}_1_trimmed.fq -2 {{fn}}_2_trimmed.fq | samtools view -bS - > {{fn}}.bam" | qsub -V -cwd -l vf=4G -pe orte 8 -o {{fn}}_alignment.out -j y -m as -M {{assigned_email}} -N bowtie_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'bowtie -q -m 1 -p 4 --chunkmbs 512 -S {{genome_path}} -1 {{fn}}_1_val_1.fq -2 {{fn}}_2__val_2.fq | samtools view -bS - > {{fn}}.bam'
	),
	'bowtie2' => array(
		'rocks1' => '',
		'bilin1' => ''
	),
	'tophat_se' => array(
		'rocks1' => 'echo "tophat -g 1 -p 4 -o {{fn}}_tophat -G {{genome_path}}.cleaned.gtf {{genome_path}} {{fn}}_1_trimmed.fq" | qsub -V -cwd -l vf=4G -pe orte 4 -o {{fn}}_tophat.out -j y -m as -M {{assigned_email}} -N tophat_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'tophat -g 1 -p 4 --segment-length 42 -o {{fn}}_tophat -G {{genome_path}}.cleaned.gtf {{genome_path}} {{fn}}_1_trimmed.fq'
	),
	'tophat_pe' => array(
		'rocks1' => 'echo "tophat -g 1 -p 4 -o {{fn}}_tophat -G {{genome_path}}.cleaned.gtf {{genome_path}} {{fn}}_1_val_1.fq {{fn}}_2_val_2.fq" | qsub -V -cwd -l vf=4G -pe orte 4 -o {{fn}}_tophat.out -j y -m as -M {{assigned_email}} -N tophat_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'tophat -g 1 -p 4 --segment-length 42 -o {{fn}}_tophat -G {{genome_path}}.cleaned.gtf {{genome_path}} {{fn}}_1_val_1.fq {{fn}}_2_val_2.fq'
	),
	'bismark_se' => array(
		'rocks1' => 'echo "bismark --bam {{genome_path}} {{fn}}_1_trimmed.fq" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'bismark --bam {{genome_path}} {{fn}}_1_trimmed.fq'
	),
	'bismark_pe' => array(
		'rocks1' => 'echo "bismark --bam {{genome_path}} -1 {{fn}}_1_val_1.fq -2 {{fn}}_2_val_2.fq" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'bismark --bam {{genome_path}} -1 {{fn}}_1_val_1.fq -2 {{fn}}_2_val_2.fq'
	),
	'bismark_pbat' => array(
		'rocks1' => 'echo "bismark --pbat –bam {{genome_path}} {{fn}}_1_trimmed.fq" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}',
		'bilin1' => 'bismark --pbat –bam {{genome_path}} {{fn}}_1_trimmed.fq'
	),
	'email_assigned' => array(
		'rocks1' => 'echo "{{project}} Processing Completed at {{time}}" | qsub -V -cwd -pe orte 1 -l vf=1G -o {{project}}_email.out -j y -N email_{{project}} -m eas -M {{assigned_email}} {{hold_prev}}',
		'bilin1' => 'echo "{{project}} Processing Completed at {{time}}" | mail -s "{{project}} Processing Complete" {{assigned_email}}'
	)
);


//// GRIDEngine queue system module load commands
// Commands used to load modules if using a queueing server
// Should be a 2D array. Keys correspond to server keys above.
// Values correspond to an array with keys as processing step
// keys and values as command to load.
// Written to the javascript in the base of processing.php
// Example for server with no queueing: 'server1' => array() 
// Example for server with queueing: 'server2' => array('step_id' => 'module load step_example') 
$processing_modules = array(
	'rocks1' => array(
		'sra_dump' => 'module load sratoolkit',
		'fastqc' => 'module load fastqc',
		'fastq_screen' => 'module load fastq_screen',
		'trim_galore' => 'module load trim_galore',
		'bowtie1_se' => 'module load bowtie',
		'bowtie1_pe' => 'module load bowtie',
		'bowtie2' => 'module load bowtie2',
		'tophat_se' => 'module load tophat',
		'tophat_pe' => 'module load tophat',
		'bismark_se' => 'module load bismark',
		'bismark_pe' => 'module load bismark',
		'bismark_pbat' => 'module load bismark',
	),
	'bilin1' => array()
);

//// Names and locations of Reference Genomes
// Keys: Name of genome
// Values: array. Keys correspond to server id. Values correspond to path.
// Example: 'Human - GRCh37' => array( 'server1' => '/root/Genomes/Human/GRCh37/Homo_sapiens.GRCh37')
$genomes = array (
	'Mouse - NCBIM37' => array(
		'rocks1' => '/bi/scratch/Genomes/Mouse/NCBIM37/Mus_musculus.NCBIM37',
		'bilin1' => '/data/public/Genomes/Mouse/NCBIM37/Mus_musculus.NCBIM37'
	),
	'Mouse - GRCm38' => array(
		'rocks1' => '/bi/scratch/Genomes/Mouse/GRCm38/Mus_musculus.GRCm38',
		'bilin1' => '/data/public/Genomes/Mouse/GRCm38/Mus_musculus.GRCm38'
	),
	'Human - GRCh37' => array(
		'rocks1' => '/bi/scratch/Genomes/Human/GRCh37/Homo_sapiens.GRCh37',
		'bilin1' => '/data/public/Genomes/Human/GRCh37/Homo_sapiens.GRCh37'
	)
);

//// Processing pipeline shortcuts
// Shortcuts that can be used from the Choose Processing panel
// Keys are unique pipeline identifiers.
// Values are an array with a full name (used for link) and steps - an
// array of Step identifiers
// Example:
/*
$processing_pipelines = array(
	'sra_to_bowtie' => array(
		'name' => 'SRA &raquo; Bowtie',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore', 'bowtie'),
	)
);
*/
$processing_pipelines = array(
	'sra_to_bowtie1_se' => array(
		'name' => 'SRA &raquo; Bowtie 1 SE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_se', 'bowtie1_se'),
	),
	'sra_to_bowtie1_pe' => array(
		'name' => 'SRA &raquo; Bowtie 1 PE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_pe', 'bowtie1_pe'),
	),
	'sra_to_tophat_se' => array(
		'name' => 'SRA &raquo; Tophat SE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_se', 'tophat_se'),
	),
	'sra_to_tophat_pe' => array(
		'name' => 'SRA &raquo; Tophat PE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_pe', 'tophat_pe'),
	),
	'sra_to_bismark_se' => array(
		'name' => 'SRA &raquo; Bismark SE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_se', 'bismark_se'),
	),
	'sra_to_bismark_pe' => array(
		'name' => 'SRA &raquo; Bismark PE',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_pe', 'bismark_pe')
	)
);




//////////////////////
// CUSTOM REPORT TYPES
//////////////////////

//// Project reports
// Identifiers and names of reports which relate to the entire project
// Key: unique identifier
// Value: full name of report type
$project_report_types = array(
	'bowtie_report' => 'Bowtie Overview',
	'tophat_report' => 'Tophat Overview'
);

//// Dataset reports
// Identifiers and names of reports which relate to each dataset
// Key: unique identifier
// Value: full name of report type
$dataset_report_types = array(
	
	'bowtie' => 'Bowtie Reports',
	'tophat' => 'Tophat Reports',
	'bismark_report' => 'Bismark Reports',
	'bismark_alignment_overview' => 'Bismark Alignment Overview Plots',
	'bismark_m_bias' => 'Bismark M-Bias Reports',
	'hicup_ditag_classification' => 'HiCUP Di-Tag Analysis',
	'hicup_cis-trans' => 'HiCUP <em>cis</em>/<em>trans</em> Analysis',
	
	'fastqc' => 'FastQC Reports',
	'fastq_screen' => 'FastQ Screen',
	'trim_galore' => 'Trim Galore',
);

//// Report filename match function
// PHP function to match each report type to a filename
// Takes two variables as input - $file (filename) and $type (true or
//   false, depending on whether the filename matched or not)
function report_match ($file, $type) {
	switch($type) {
		
		case 'bowtie':
			return (stripos(basename($file), 'bowtie') || stripos(basename($file), 'alignment')) && (substr($file, -4) ==  '.out' || substr($file, -4) ==  '.log');
		
		case 'bowtie_report':
			return substr($file, -18) ==  'bowtie_report.html';
			
		case 'tophat_report':
			return substr($file, -18) ==  'tophat_report.html';
		
		case 'tophat':
			return substr($file, -17) ==  'align_summary.txt';
		
		case 'bismark_report':
			return substr($file, -13) ==  'E_report.html';
		
		case 'bismark_alignment_overview':
			return substr($file, -23) == '.alignment_overview.png';
			
		case 'bismark_m_bias':
			return stripos(basename($file), 'M-bias') && substr($file, -4) == '.png';
			
		case 'hicup_ditag_classification':
			return substr($file, -29) == 'pair_ditag_classification.png';
			
		case 'hicup_cis-trans':
			return substr($file, -18) == '.sam_cis-trans.png';
			
		case 'fastqc':
			return basename($file) == 'fastqc_report.html';
			
		case 'fastq_screen':
			return substr($file, -11) ==  '_screen.png';
			
		case 'trim_galore':
			return substr($file, -20) ==  '_trimming_report.txt';
			
		default:
			return false;
	}
}

//// Report filename cleaning function
// PHP function to clean up filenames of matched reports
// Typically uses substr to cut off unneccessary filename
//  suffixes such as align_summary.txt
function report_naming ($path, $type) {
	switch($type) {
			
		case 'bowtie':
			return substr(basename($path),0,-4);
			
		case 'bowtie_report':
			return substr(basename($path),0,-5);
			
		case 'tophat_report':
			return substr(basename($path),0,-5);
			
		case 'tophat':
			return basename($path);

		case 'bismark_report':
			return substr(basename($path),0,-5);
		
		case 'bismark_alignment_overview':
			return substr(basename($path),0, -23);
			
		case 'bismark_m_bias':
			return substr(basename($path),0, -4);
			
		case 'hicup_ditag_classification':
			return substr(basename($path),0, -30);
			
		case 'hicup_cis-trans':
			return substr(basename($path),0, -18);
		
		case 'fastqc':
			return substr(basename(dirname($path)), 0, -7);
			
		case 'fastq_screen':
			return substr(basename($path),0, -11);
			
		case 'trim_galore':
			return substr(basename($path),0, -20);
			
		default:
			return false;
	}
}





?>

<?php

/*
* config.php
* ---------
* Some configuration and customisation options.
*
*/

$labrador_url = 'http://bilin1/labrador/';
$data_root = '/data/pipeline/new_public/TIDIED/';
$support_email = 'babraham.bioinformatics@babraham.ac.uk';


$db_database = 'labrador';
$db_user = 'labrador';
$db_password = false;
$db_host = 'localhost';


$administrators = array(
	'simon.andrews@babraham.ac.uk' => 'Simon',
	'felix.krueger@babraham.ac.uk' => 'Felix',
	'steven.wingett@babraham.ac.uk' => 'Steven',
	'laura.biggins@babraham.ac.uk' => 'Laura',
	'anne.segonds-pichon@babraham.ac.uk' => 'Anne',
	'phil.ewels@babraham.ac.uk' => 'Phil'
);

$groups = array(
	'Wolf Reik' => 'Reik',
	'Peter Fraser' => 'Fraser',
	'Anne Corcoran' => 'Corcoran',
	'Jon Houseley' => 'Houseley',
	'Gavin Kelsey' => 'Kelsey',
	'Sarah Elderkin' => 'Elderkin',
	'Bioinformatics' => 'Bioinformatics'
);


$homepage_title = 'Labrador Dataset Browser';
$homepage_subtitle = 'A database of datasets processed by the BI Bioinformatics group.';



//////////////////////
// DOWNLOADS SETTINGS
//////////////////////

$project_filename_filters = array(
	'.smk',
	'.smk.gz'
);

$aligned_filename_filters = array(
	'.bam',
	'.sam',
	'.bowtie',
	'.bowtie.gz',
	'.txt.gz'
);

$raw_filename_filters = array(
	'.fastq',
	'.fastq.gz',
	'.fq',
	'.fq.gz',
	'.sra'
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
// PROCESSING PIPELINES
//////////////////////

$processing_servers = array(
	'rocks1' => array('name' => 'The Cluster', 'queueing' => 'true'),
	'bilin1' => array('name' => 'Bilin 1', 'queueing' => 'true')
);

// Written to the javascript in the base of processing.php
$processing_modules = array(
	'rocks1' => array(
		'sra_dump' => 'module load sratoolkit',
		'fastqc' => 'module load fastqc',
		'fastq_screen' => 'module load fastq_screen',
		'trim_galore' => 'module load trim_galore',
		'bowtie1' => 'module load bowtie',
		'bowtie2' => 'module load bowtie2',
		'bismark_se' => 'module load bismark',
		'bismark_pe' => 'module load bismark',
		'bismark_pbat' => 'module load bismark',
	),
	'bilin1' => array()
);

$genomes = array (
	'Mouse - NCBIM37' => array(
		'rocks1' => '/bi/scratch/Genomes/Mouse/NCBIM37/Mus_musculus.NCBIM37',
		'bilin1' => '/data/public/Genomes/Mouse/NCBIM37/Mus_musculus.NCBIM37'
	),
	'Human - GRCh37' => array(
		'rocks1' => '/bi/scratch/Genomes/Human/GRCh37/Homo_sapiens.GRCh37',
		'bilin1' => '/data/public/Genomes/Human/GRCh37/Homo_sapiens.GRCh37'
	)
);

$processing_pipelines = array(
	'sra_to_bowtie1' => array(
		'name' => 'SRA &raquo; Bowtie 1',
		'steps' => array('get_sra', 'sra_dump', 'fastqc', 'fastq_screen', 'trim_galore_se', 'bowtie1'),
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

$processing_steps = array(
	'Pre-processing' => array(
		'get_sra' => array( 'name' => 'Download SRA', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'sra_dump' => array( 'name' => 'SRA to FastQ', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'fastqc' => array( 'name' => 'FastQC', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'fastq_screen' => array( 'name' => 'Fastq Screen', 'unit' => 'accession_sra', 'requires_genome' => 'false' ), 
		'trim_galore_se' => array( 'name' => 'Trim Galore, Single End', 'unit' => 'accession_sra', 'requires_genome' => 'false' ),
		'trim_galore_pe' => array( 'name' => 'Trim Galore, Paired End', 'unit' => 'accession_sra', 'requires_genome' => 'false' )
	),
	'Alignment' => array(
		'bowtie1' => array( 'name' => 'Bowtie 1', 'unit' => 'accession_sra', 'requires_genome' => 'true' ), 
		'bowtie2' => array( 'name' => 'Bowtie 2', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'bismark_se' => array( 'name' => 'Bismark, Single End', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'bismark_pe' => array( 'name' => 'Bismark, Paired End', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
		'bismark_pbat' => array( 'name' => 'Bismark, PBAT', 'unit' => 'accession_sra', 'requires_genome' => 'true' ),
	),
	'Post-Processing' => array(
		'email_contact' => array( 'name' => 'Send Contact E-mail', 'unit' => 'project' ),
		'email_assigned' => array( 'name' => 'Send Assigned E-mail', 'unit' => 'project' )
	)
);

// Remember to add trailing newline characters
$processing_codes = array(
	'get_sra' => array(
		'rocks1' => 'echo "wget -nv {{sra_url}}" | qsub -V -cwd -pe orte 1 -l vf=1G -o {{fn}}_download.out -j y -m as -M {{assigned_email}} -N download_{{fn}}'."\n",
		'bilin1' => 'wget -nv {{sra_url}}'."\n"
	),
	'sra_dump' => array(
		'rocks1' => 'echo "fastq-dump --split-files ./{{fn}}.sra" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_fqdump.out -j y -m as -M {{assigned_email}} -N dump_{{fn}} -hold_jid download_{{fn}}'."\n",
		'bilin1' => 'fastq-dump --split-files ./{{fn}}.sra'."\n"
	),
	'fastqc' => array(
		'rocks1' => 'echo "fastqc  {{fn}}_1.fastq" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_1_fastqc.out -j y -m as -M {{assigned_email}} -N fastqc_{{fn}}_1 -hold_jid dump_{{fn}}'."\n",
		'bilin1' => 'fastqc {{fn}}_1.fastq'."\n"
	),
	'fastq_screen' => array(
		'rocks1' => 'echo "fastq_screen --subset 100000 {{fn}}_1.fastq" | qsub -V -cwd -pe orte 1 -l vf=4G -o {{fn}}_1_fqscreen.out -j y -m as -M {{assigned_email}} -N screen_{{fn}}_1 -hold_jid dump_{{fn}}'."\n",
		'bilin1' => 'fastq_screen --subset 100000 {{fn}}_1.fastq'."\n"
	),
	'trim_galore_se' => array(
		'rocks1' => 'echo "trim_galore --fastqc --gzip {{fn}}.fastq" | qsub -V -cwd -pe orte 2 -l vf=4G -o {{fn}}_trimming.out -j y -m as -M {{assigned_email}} -N trim_{{fn}} -hold_jid dump_{{fn}}'."\n",
		'bilin1' => 'trim_galore --fastqc --gzip {{fn}}.fastq'
	),
	'trim_galore_pe' => array(
		'rocks1' => 'echo "trim_galore --paired --trim1 --fastqc --gzip {{fn}}_1.fastq {{fn}}_2.fastq" | qsub -V -cwd -pe orte 2 -l vf=4G -o {{fn}}_trimming.out -j y -m as -M {{assigned_email}} -N trim_{{fn}} -hold_jid dump_{{fn}}'."\n",
		'bilin1' => 'trim_galore --paired --trim1 --fastqc --gzip {{fn}}_1.fastq {{fn}}_2.fastq'
	),
	'bowtie1' => array(
		'rocks1' => 'echo "bowtie -q -t -p 8 -m 1 --best --strata --chunkmbs 2048 {{genome_path}} {{fn}}_1.fastq {{fn}}.bowtie" | qsub -V -cwd -l vf=4G -pe orte 8 -o {{fn}}_alignment.out -j y -m as -M {{assigned_email}} -N bowtie_{{fn}} -hold_jid dump_{{fn}}'."\n",
		'bilin1' => 'bowtie -q -m 1 -p 4 --best --strata --chunkmbs 512 {{genome_path}} {{fn}}_1.fastq {{fn}}.bowtie'."\n"
	),
	'bowtie2' => array(
		'rocks1' => '',
		'bilin1' => ''
	),
	'bismark_se' => array(
		'rocks1' => 'echo "bismark --bam {{genome_path}} {{fn}}.fq.gz" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}'."\n",
		'bilin1' => 'bismark --bam {{genome_path}} {{fn}}.fq.gz'."\n"
	),
	'bismark_pe' => array(
		'rocks1' => 'echo "bismark --bam {{genome_path}} -1 {{fn}}_1.fq.gz -2 {{fn}}_2.fq.gz" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}'."\n",
		'bilin1' => 'bismark --bam {{genome_path}} -1 {{fn}}_1.fq.gz -2 {{fn}}_2.fq.gz'."\n"
	),
	'bismark_pbat' => array(
		'rocks1' => 'echo "bismark --pbat –bam {{genome_path}} {{fn}}.fq.gz" | qsub -V -cwd -l vf=12G -pe orte 6 -o {{fn}}_bismark_run.out -j y -m as -M {{assigned_email}} -N bismark_{{fn}} -hold_jid trim_{{fn}}'."\n",
		'bilin1' => 'bismark --pbat –bam {{genome_path}} {{fn}}.fq.gz'."\n"
	),
	'email_contact' => array(
		'rocks1' => 'echo "{{project}} Processing Completed at {{time}}" | qsub -V -cwd -pe orte 1 -l vf=1G -o {{project}}_email.out -j y -N email_{{project}} -m eas -M {{contact_email}} {{hold_prev}}'."\n",
		'bilin1' => 'echo "{{project}} Processing Completed at {{time}}" | mail -s "{{project}} Processing Complete" {{contact_email}}'."\n"
	),
	'email_assigned' => array(
		'rocks1' => 'echo "{{project}} Processing Completed at {{time}}" | qsub -V -cwd -pe orte 1 -l vf=1G -o {{project}}_email.out -j y -N email_{{project}} -m eas -M {{assigned_email}} {{hold_prev}}'."\n",
		'bilin1' => 'echo "{{project}} Processing Completed at {{time}}" | mail -s "{{project}} Processing Complete" {{assigned_email}}'."\n"
	)
);





//////////////////////
// CUSTOM REPORT TYPES
//////////////////////

$project_report_types = array(
	'bowtie_report' => 'Bowtie Overview'
);

$dataset_report_types = array(
	'fastqc' => 'FastQC Reports',
	'fastq_screen' => 'FastQ Screen',
	'bowtie' => 'Bowtie Reports',
	
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
		
		case 'bowtie':
			return (stripos(basename($file), 'bowtie') || stripos(basename($file), 'alignment')) && (substr($file, -4) ==  '.out' || substr($file, -4) ==  '.log');
		
		case 'bowtie_report':
			return substr($file, -18) ==  'bowtie_report.html';

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
			
		case 'bowtie':
			return substr(basename($path),0,-4);
			
		case 'bowtie_report':
			return substr(basename($path),0,-5);

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
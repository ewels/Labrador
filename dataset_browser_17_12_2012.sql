-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 17, 2012 at 02:45 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dataset_browser`
--

-- --------------------------------------------------------

--
-- Table structure for table `datasets`
--

CREATE TABLE `datasets` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `paper_id` int(12) NOT NULL,
  `name` text NOT NULL,
  `species` varchar(250) NOT NULL,
  `cell_type` varchar(250) NOT NULL,
  `data_type` varchar(250) NOT NULL,
  `geo_accession` varchar(50) DEFAULT NULL,
  `sra_accession` varchar(50) DEFAULT NULL,
  `notes` text NOT NULL,
  `last_modified` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `geo_accession` (`geo_accession`),
  UNIQUE KEY `sra_accession` (`sra_accession`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `datasets`
--

INSERT INTO `datasets` VALUES(1, 42, 'Chip-Seq input control', 'Mus musculus', 'Rag1 -/- pro-B cells', 'ChIP-Seq', 'GSM1002561', 'SRR567651', 'This is a test note...', 1355253093);
INSERT INTO `datasets` VALUES(2, 42, 'YY1 Chip-Seq', 'Mus musculus', 'Rag1 -/- pro-B cells', 'ChIP-Seq', 'GSM1002560', 'SRR567650', '', 1355253093);
INSERT INTO `datasets` VALUES(17, 17, 'PolII_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721286', 'SRR202852', '', 1355415938);
INSERT INTO `datasets` VALUES(18, 17, 'PolII_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721287', 'SRR202855', '', 1355415938);
INSERT INTO `datasets` VALUES(19, 17, 'H3K4me1_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721288', 'SRR202858', '', 1355415938);
INSERT INTO `datasets` VALUES(20, 17, 'H3K4me1_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721289', 'SRR202861', '', 1355415938);
INSERT INTO `datasets` VALUES(21, 17, 'H3K4me2_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721290', 'SRR202863', '', 1355415938);
INSERT INTO `datasets` VALUES(22, 17, 'H3K4me2_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721291', 'SRR202865', '', 1355415938);
INSERT INTO `datasets` VALUES(23, 17, 'H3K4me3_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721292', 'SRR202869', '', 1355415938);
INSERT INTO `datasets` VALUES(24, 17, 'H3K4me3_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721293', 'SRR202873', '', 1355415938);
INSERT INTO `datasets` VALUES(25, 17, 'H3K27me3_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721294', 'SRR202876', '', 1355415938);
INSERT INTO `datasets` VALUES(26, 17, 'H3K27me3_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721295', 'SRR202879', '', 1355415938);
INSERT INTO `datasets` VALUES(27, 17, 'H3K36me3_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721296', 'SRR202882', '', 1355415938);
INSERT INTO `datasets` VALUES(28, 17, 'H3K36me3_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721297', 'SRR202885', '', 1355415938);
INSERT INTO `datasets` VALUES(29, 17, 'H3K9Ac_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721300', 'SRR202894', '', 1355415938);
INSERT INTO `datasets` VALUES(30, 17, 'H3K9Ac_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721301', 'SRR202897', '', 1355415938);
INSERT INTO `datasets` VALUES(31, 17, 'H3K18Ac_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721302', 'SRR202900', '', 1355415938);
INSERT INTO `datasets` VALUES(32, 17, 'H3K18Ac_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721303', 'SRR202903', '', 1355415938);
INSERT INTO `datasets` VALUES(33, 17, 'H4K12Ac_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721304', 'SRR202907', '', 1355415938);
INSERT INTO `datasets` VALUES(34, 17, 'H4K12Ac_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721305', 'SRR202911', '', 1355415938);
INSERT INTO `datasets` VALUES(35, 17, 'Sonicated_input_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721306', 'SRR202915', '', 1355415938);
INSERT INTO `datasets` VALUES(36, 17, 'Sonicated_input_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721307', 'SRR202919', '', 1355415938);
INSERT INTO `datasets` VALUES(37, 17, 'MNAse_digested_input_MB', 'Mus musculus', 'C2C12 myoblasts', 'ChIP-Seq', 'GSM721308', 'SRR202925', '', 1355415938);
INSERT INTO `datasets` VALUES(38, 17, 'MNAse_digested_input_MT', 'Mus musculus', 'C2C12 myotubes', 'ChIP-Seq', 'GSM721309', 'SRR202931', '', 1355415938);

-- --------------------------------------------------------

--
-- Table structure for table `files_aligned`
--

CREATE TABLE `files_aligned` (
  `id` int(12) NOT NULL,
  `dataset_id` int(12) NOT NULL,
  `filename` text NOT NULL,
  `genome` varchar(250) NOT NULL,
  `parameters` text NOT NULL,
  `num_reads` int(18) NOT NULL,
  `modified` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files_aligned`
--


-- --------------------------------------------------------

--
-- Table structure for table `files_derived`
--

CREATE TABLE `files_derived` (
  `id` int(12) NOT NULL,
  `dataset_id` int(12) NOT NULL,
  `type` varchar(250) NOT NULL,
  `notes` text NOT NULL,
  `modified` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files_derived`
--


-- --------------------------------------------------------

--
-- Table structure for table `files_raw`
--

CREATE TABLE `files_raw` (
  `id` int(12) NOT NULL,
  `dataset_id` int(12) NOT NULL,
  `filename` text NOT NULL,
  `read_length` int(9) NOT NULL,
  `num_reads` int(18) NOT NULL,
  `modified` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files_raw`
--


-- --------------------------------------------------------

--
-- Table structure for table `papers`
--

CREATE TABLE `papers` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `first_author` varchar(250) NOT NULL,
  `year` int(4) NOT NULL,
  `paper_title` text NOT NULL,
  `authors` text NOT NULL,
  `PMID` varchar(20) DEFAULT NULL,
  `DOI` varchar(100) NOT NULL,
  `geo_accession` varchar(50) DEFAULT NULL,
  `sra_accession` varchar(50) NOT NULL,
  `notes` text NOT NULL,
  `requested_by` varchar(250) NOT NULL,
  `processed_by` varchar(250) NOT NULL,
  `last_modified` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PMID` (`PMID`),
  UNIQUE KEY `geo_accession` (`geo_accession`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `papers`
--

INSERT INTO `papers` VALUES(1, 'Meissner', 2008, 'Genome-scale DNA methylation maps of pluripotent and differentiated cells', 'Meissner A, Mikkelsen TS, Gu H, Wernig M, Hanna J, Sivachenko A, Zhang X, Bernstein BE, Nusbaum C, Jaffe DB, Gnirke A, Jaenisch R, Lander ES.', '18600261', '10.1038/nature07107', 'GSE11034', '', '', '', '', 1354555461);
INSERT INTO `papers` VALUES(2, 'Aravin', 2008, 'A piRNA pathway primed by individual transposons is linked to de novo DNA methylation in mice.', 'Aravin AA, Sachidanandam R, Bourc''his D, Schaefer C, Pezic D, Toth KF, Bestor T, Hannon GJ.', '18922463', '10.1016/j.molcel.2008.09.003', 'GSE12757', '', '', '', '', 1354801277);
INSERT INTO `papers` VALUES(3, 'Marson', 2008, 'Connecting microRNA genes to the core transcriptional regulatory circuitry of embryonic stem cells.', 'Marson A, Levine SS, Cole MF, Frampton GM, Brambrink T, Johnstone S, Guenther MG, Johnston WK, Wernig M, Newman J, Calabrese JM, Dennis LM, Volkert TL, Gupta S, Love J, Hannett N, Sharp PA, Bartel DP, Jaenisch R, Young RA.', '18692474', '10.1016/j.cell.2008.07.020', 'GSE11724', '', '', '', '', 1354801456);
INSERT INTO `papers` VALUES(4, 'Aiba', 2009, 'Defining developmental potency and cell lineage trajectories by expression profiling of differentiating mouse embryonic stem cells.', 'Aiba K, Nedorezov T, Piao Y, Nishiyama A, Matoba R, Sharova LV, Sharov AA, Yamanaka S, Niwa H, Ko MS.', '19112179', '10.1093/dnares/dsn035', 'GSE11523', '', '', '', '', 1354801543);
INSERT INTO `papers` VALUES(5, 'Cheng', 2009, 'Erythroid GATA1 function revealed by genome-wide analysis of transcription factor occupancy, histone modifications, and mRNA expression.', 'Cheng Y, Wu W, Kumar SA, Yu D, Deng W, Tripic T, King DC, Chen KB, Zhang Y, Drautz D, Giardine B, Schuster SC, Miller W, Chiaromonte F, Zhang Y, Blobel GA, Weiss MJ, Hardison RC.', '19887574', '10.1101/gr.098921.109', 'GSE18164', '', '', '', '', 1354801653);
INSERT INTO `papers` VALUES(6, 'Song', 2011, 'Selective chemical labeling reveals the genome-wide distribution of 5-hydroxymethylcytosine.', 'Song CX, Szulwach KE, Fu Y, Dai Q, Yi C, Li X, Li Y, Chen CH, Zhang W, Jian X, Wang J, Zhang L, Looney TJ, Zhang B, Godley LA, Hicks LM, Lahn BT, Jin P, He C.', '21151123', '10.1038/nbt.1732', 'GSE25398', '', '', '', '', 1354801704);
INSERT INTO `papers` VALUES(7, 'Lister', 2008, 'Highly integrated single-base resolution maps of the epigenome in Arabidopsis.', 'Lister R, O\\''Malley RC, Tonti-Filippini J, Gregory BD, Berry CC, Millar AH, Ecker JR.', '18423832', '10.1016/j.cell.2008.03.029', 'GSE10877', '', '', '', '', 1354801832);
INSERT INTO `papers` VALUES(8, 'Wilson', 2012, 'Resources for the MeDUSA (Methylated DNA Utility for Sequence Analysis) MeDIP-seq computational analysis pipeline for the identification of differentially methylated regions, and associated methylome data from 18 wild-type and mutant mouse ES, NP and MEF cells', 'Wilson, G; Dharmi, P; Saito, Y; CortÃ¡zar, D; Kunz, C; SchÃ¤r, P; Beck, S', '', '10.5524/100035', 'GSE27468', '', 'http://gigadb.org/mouse-methylomes/', '', '', 1354802311);
INSERT INTO `papers` VALUES(10, 'Wei', 2009, 'Global mapping of H3K4me3 and H3K27me3 reveals specificity and plasticity in lineage fate determination of differentiating CD4+ T cells.', 'Wei G, Wei L, Zhu J, Zang C, Hu-Li J, Yao Z, Cui K, Kanno Y, Roh TY, Watford WT, Schones DE, Peng W, Sun HW, Paul WE, O\\''Shea JJ, Zhao K.', '19144320', '10.1016/j.immuni.2008.12.009', 'GSE14254', '', '', '', '', 1354803021);
INSERT INTO `papers` VALUES(11, 'Guttman', 2010, 'Ab initio reconstruction of cell type-specific transcriptomes in mouse reveals the conserved multi-exonic structure of lincRNAs.', 'Guttman M, Garber M, Levin JZ, Donaghey J, Robinson J, Adiconis X, Fan L, Koziol MJ, Gnirke A, Nusbaum C, Rinn JL, Lander ES, Regev A.', '20436462', '10.1038/nbt.1633', 'GSE20851', '', '', '', '', 1354803419);
INSERT INTO `papers` VALUES(12, 'Heinz', 2010, 'Simple combinations of lineage-determining transcription factors prime cis-regulatory elements required for macrophage and B cell identities.', 'Heinz S, Benner C, Spann N, Bertolino E, Lin YC, Laslo P, Cheng JX, Murre C, Singh H, Glass CK.', '20513432', '10.1016/j.molcel.2010.05.004', 'GSE21512', '', '', '', '', 1354803800);
INSERT INTO `papers` VALUES(13, 'Kuchen', 2010, 'Regulation of microRNA expression and abundance during lymphopoiesis.', 'Kuchen S, Resch W, Yamane A, Kuo N, Li Z, Chakraborty T, Wei L, Laurence A, Yasuda T, Peng S, Hu-Li J, Lu K, Dubois W, Kitamura Y, Charles N, Sun HW, Muljo S, Schwartzberg PL, Paul WE, O\\''Shea J, Rajewsky K, Casellas R.', '20605486', '10.1016/j.immuni.2010.05.009', 'GSE21630', '', '', '', '', 1354804040);
INSERT INTO `papers` VALUES(14, 'Tavares', 2012, 'RYBP-PRC1 complexes mediate H2A ubiquitylation at polycomb target sites independently of PRC2 and H3K27me3.', 'Tavares L, Dimitrova E, Oxley D, Webster J, Poot R, Demmers J, Bezstarosti K, Taylor S, Ura H, Koide H, Wutz A, Vidal M, Elderkin S, Brockdorff N.', '22325148', '10.1016/j.cell.2011.12.029', 'GSE23716', '', '', '', '', 1354804559);
INSERT INTO `papers` VALUES(15, 'De', 2011, 'Dynamic BRG1 recruitment during T helper differentiation and activation reveals distal regulatory elements.', 'De S, Wurster AL, Precht P, Wood WH 3rd, Becker KG, Pazin MJ.', '21262765', '10.1128/MCB.00920-10', 'GSE23719', '', '', '', '', 1354804798);
INSERT INTO `papers` VALUES(16, 'Marks', 2012, 'The transcriptional and epigenomic foundations of ground state pluripotency.', 'Marks H, Kalkan T, Menafra R, Denissov S, Jones K, Hofemeister H, Nichols J, Kranz A, Stewart AF, Smith A, Stunnenberg HG.', '22541430', '10.1016/j.cell.2012.03.026', 'GSE23943', '', '', '', '', 1354805379);
INSERT INTO `papers` VALUES(17, 'Asp', 2011, 'Genome-wide remodeling of the epigenetic landscape during myogenic differentiation.', 'Asp P, Blum R, Vethantham V, Parisi F, Micsinai M, Cheng J, Bowman C, Kluger Y, Dynlacht BD.', '21551099', '10.1073/pnas.1102223108', 'GSE25308', '', '', '', '', 1354872143);
INSERT INTO `papers` VALUES(18, 'Deaton', 2011, 'Cell type-specific DNA methylation at intragenic CpG islands in the immune system.', 'Deaton AM, Webb S, Kerr AR, Illingworth RS, Guy J, Andrews R, Bird A.', '21628449', '10.1101/gr.118703.110', 'GSE25688', '', '', '', '', 1354872327);
INSERT INTO `papers` VALUES(19, 'Liao', 2011, 'Modulation of cytokine receptors by IL-2 broadly regulates differentiation into helper T cell lineages.', 'Liao W, Lin JX, Wang L, Li P, Leonard WJ.', '21516110', '10.1038/ni.2030', 'GSE27158', '', '', '', '', 1354873312);
INSERT INTO `papers` VALUES(20, 'Quenneville', 2011, 'In embryonic stem cells, ZFP57/KAP1 recognize a methylated hexanucleotide to affect chromatin and DNA methylation of imprinting control regions.', 'Quenneville S, Verde G, Corsinotti A, Kapopoulou A, Jakobsson J, Offner S, Baglivo I, Pedone PV, Grimaldi G, Riccio A, Trono D.', '22055183', '10.1016/j.molcel.2011.08.032', 'GSE31183', '', '', '', '', 1354873699);
INSERT INTO `papers` VALUES(21, 'Zhang', 2012, 'Dynamic transformations of genome-wide epigenetic marking and transcriptional control establish T cell identity.', 'Zhang JA, Mortazavi A, Williams BA, Wold BJ, Rothenberg EV.', '22500808', '10.1016/j.cell.2012.01.056', 'GSE31233', '', '', '', '', 1354873926);
INSERT INTO `papers` VALUES(22, 'Drinnenberg', 2011, 'Compatibility with killer explains the rise of RNAi-deficient fungi.', 'Drinnenberg IA, Fink GR, Bartel DP.', '21921191', '10.1126/science.1209575', 'GSE31300', '', '', '', '', 1354874204);
INSERT INTO `papers` VALUES(23, 'Xie', 2012, 'Base-resolution analyses of sequence and parent-of-origin dependent DNA methylation in the mouse genome.', 'Xie W, Barr CL, Kim A, Yue F, Lee AY, Eubanks J, Dempster EL, Ren B.', '22341451', '10.1016/j.cell.2011.12.035', 'GSE33722', '', '', '', '', 1354874615);
INSERT INTO `papers` VALUES(24, 'Wang', 2012, '\\"Calling cards\\" for DNA-binding proteins in mammalian cells.', 'Wang H, Mayhew D, Chen X, Johnston M, Mitra RD.', '22214611', '10.1534/genetics.111.137315', 'GSE34791', '', '', '', '', 1354874698);
INSERT INTO `papers` VALUES(25, 'Smith', 2012, 'A unique regulatory phase of DNA methylation in the early mammalian embryo.', 'Smith ZD, Chan MM, Mikkelsen TS, Gu H, Gnirke A, Regev A, Meissner A.', '22456710', '10.1038/nature10960', 'GSE34864', '', '', '', '', 1354876521);
INSERT INTO `papers` VALUES(26, 'Auerbach', 2009, 'Mapping accessible chromatin regions using Sono-Seq.', 'Auerbach RK, Euskirchen G, Rozowsky J, Lamarre-Vincent N, Moqtaderi Z, LefranÃ§ois P, Struhl K, Gerstein M, Snyder M.', '19706456', '10.1073/pnas.0905443106', 'GSE12781', '', '', '', '', 1354876989);
INSERT INTO `papers` VALUES(27, 'Encode', 2012, 'An integrated encyclopedia of DNA elements in the human genome.', 'ENCODE Project Consortium, Dunham I, Kundaje A, Aldred SF, Collins PJ, Davis CA, Doyle F, Epstein CB, Frietze S, Harrow J, Kaul R, Khatun J, Lajoie BR, Landt SG, Lee BK, Pauli F, Rosenbloom KR, Sabo P, Safi A, Sanyal A, Shoresh N, Simon JM, Song L, Trinklein ND, Altshuler RC, Birney E, Brown JB, Cheng C, Djebali S, Dong X, Dunham I, Ernst J, Furey TS, Gerstein M, Giardine B, Greven M, Hardison RC, Harris RS, Herrero J, Hoffman MM, Iyer S, Kelllis M, Khatun J, Kheradpour P, Kundaje A, Lassman T, Li Q, Lin X, Marinov GK, Merkel A, Mortazavi A, Parker SC, Reddy TE, Rozowsky J, Schlesinger F, Thurman RE, Wang J, Ward LD, Whitfield TW, Wilder SP, Wu W, Xi HS, Yip KY, Zhuang J, Bernstein BE, Birney E, Dunham I, Green ED, Gunter C, Snyder M, Pazin MJ, Lowdon RF, Dillon LA, Adams LB, Kelly CJ, Zhang J, Wexler JR, Green ED, Good PJ, Feingold EA, Bernstein BE, Birney E, Crawford GE, Dekker J, Elinitski L, Farnham PJ, Gerstein M, Giddings MC, Gingeras TR, Green ED, GuigÃ³ R, Hardison RC, Hubbard TJ, Kellis M, Kent WJ, Lieb JD, Margulies EH, Myers RM, Snyder M, Starnatoyannopoulos JA, Tennebaum SA, Weng Z, White KP, Wold B, Khatun J, Yu Y, Wrobel J, Risk BA, Gunawardena HP, Kuiper HC, Maier CW, Xie L, Chen X, Giddings MC, Bernstein BE, Epstein CB, Shoresh N, Ernst J, Kheradpour P, Mikkelsen TS, Gillespie S, Goren A, Ram O, Zhang X, Wang L, Issner R, Coyne MJ, Durham T, Ku M, Truong T, Ward LD, Altshuler RC, Eaton ML, Kellis M, Djebali S, Davis CA, Merkel A, Dobin A, Lassmann T, Mortazavi A, Tanzer A, Lagarde J, Lin W, Schlesinger F, Xue C, Marinov GK, Khatun J, Williams BA, Zaleski C, Rozowsky J, RÃ¶der M, Kokocinski F, Abdelhamid RF, Alioto T, Antoshechkin I, Baer MT, Batut P, Bell I, Bell K, Chakrabortty S, Chen X, Chrast J, Curado J, Derrien T, Drenkow J, Dumais E, Dumais J, Duttagupta R, Fastuca M, Fejes-Toth K, Ferreira P, Foissac S, Fullwood MJ, Gao H, Gonzalez D, Gordon A, Gunawardena HP, Howald C, Jha S, Johnson R, Kapranov P, King B, Kingswood C, Li G, Luo OJ, Park E, Preall JB, Presaud K, Ribeca P, Risk BA, Robyr D, Ruan X, Sammeth M, Sandu KS, Schaeffer L, See LH, Shahab A, Skancke J, Suzuki AM, Takahashi H, Tilgner H, Trout D, Walters N, Wang H, Wrobel J, Yu Y, Hayashizaki Y, Harrow J, Gerstein M, Hubbard TJ, Reymond A, Antonarakis SE, Hannon GJ, Giddings MC, Ruan Y, Wold B, Carninci P, GuigÃ³ R, Gingeras TR, Rosenbloom KR, Sloan CA, Learned K, Malladi VS, Wong MC, Barber GP, Cline MS, Dreszer TR, Heitner SG, Karolchik D, Kent WJ, Kirkup VM, Meyer LR, Long JC, Maddren M, Raney BJ, Furey TS, Song L, Grasfeder LL, Giresi PG, Lee BK, Battenhouse A, Sheffield NC, Simon JM, Showers KA, Safi A, London D, Bhinge AA, Shestak C, Schaner MR, Kim SK, Zhang ZZ, Mieczkowski PA, Mieczkowska JO, Liu Z, McDaniell RM, Ni Y, Rashid NU, Kim MJ, Adar S, Zhang Z, Wang T, Winter D, Keefe D, Birney E, Iyer VR, Lieb JD, Crawford GE, Li G, Sandhu KS, Zheng M, Wang P, Luo OJ, Shahab A, Fullwood MJ, Ruan X, Ruan Y, Myers RM, Pauli F, Williams BA, Gertz J, Marinov GK, Reddy TE, Vielmetter J, Partridge EC, Trout D, Varley KE, Gasper C, Bansal A, Pepke S, Jain P, Amrhein H, Bowling KM, Anaya M, Cross MK, King B, Muratet MA, Antoshechkin I, Newberry KM, McCue K, Nesmith AS, Fisher-Aylor KI, Pusey B, DeSalvo G, Parker SL, Balasubramanian S, Davis NS, Meadows SK, Eggleston T, Gunter C, Newberry JS, Levy SE, Absher DM, Mortazavi A, Wong WH, Wold B, Blow MJ, Visel A, Pennachio LA, Elnitski L, Margulies EH, Parker SC, Petrykowska HM, Abyzov A, Aken B, Barrell D, Barson G, Berry A, Bignell A, Boychenko V, Bussotti G, Chrast J, Davidson C, Derrien T, Despacio-Reyes G, Diekhans M, Ezkurdia I, Frankish A, Gilbert J, Gonzalez JM, Griffiths E, Harte R, Hendrix DA, Howald C, Hunt T, Jungreis I, Kay M, Khurana E, Kokocinski F, Leng J, Lin MF, Loveland J, Lu Z, Manthravadi D, Mariotti M, Mudge J, Mukherjee G, Notredame C, Pei B, Rodriguez JM, Saunders G, Sboner A, Searle S, Sisu C, Snow C, Steward C, Tanzer A, Tapanan E, Tress ML, van Baren MJ, Walters N, Washieti S, Wilming L, Zadissa A, Zhengdong Z, Brent M, Haussler D, Kellis M, Valencia A, Gerstein M, Raymond A, GuigÃ³ R, Harrow J, Hubbard TJ, Landt SG, Frietze S, Abyzov A, Addleman N, Alexander RP, Auerbach RK, Balasubramanian S, Bettinger K, Bhardwaj N, Boyle AP, Cao AR, Cayting P, Charos A, Cheng Y, Cheng C, Eastman C, Euskirchen G, Fleming JD, Grubert F, Habegger L, Hariharan M, Harmanci A, Iyenger S, Jin VX, Karczewski KJ, Kasowski M, Lacroute P, Lam H, Larnarre-Vincent N, Leng J, Lian J, Lindahl-Allen M, Min R, Miotto B, Monahan H, Moqtaderi Z, Mu XJ, O\\''Geen H, Ouyang Z, Patacsil D, Pei B, Raha D, Ramirez L, Reed B, Rozowsky J, Sboner A, Shi M, Sisu C, Slifer T, Witt H, Wu L, Xu X, Yan KK, Yang X, Yip KY, Zhang Z, Struhl K, Weissman SM, Gerstein M, Farnham PJ, Snyder M, Tenebaum SA, Penalva LO, Doyle F, Karmakar S, Landt SG, Bhanvadia RR, Choudhury A, Domanus M, Ma L, Moran J, Patacsil D, Slifer T, Victorsen A, Yang X, Snyder M, White KP, Auer T, Centarin L, Eichenlaub M, Gruhl F, Heerman S, Hoeckendorf B, Inoue D, Kellner T, Kirchmaier S, Mueller C, Reinhardt R, Schertel L, Schneider S, Sinn R, Wittbrodt B, Wittbrodt J, Weng Z, Whitfield TW, Wang J, Collins PJ, Aldred SF, Trinklein ND, Partridge EC, Myers RM, Dekker J, Jain G, Lajoie BR, Sanyal A, Balasundaram G, Bates DL, Byron R, Canfield TK, Diegel MJ, Dunn D, Ebersol AK, Ebersol AK, Frum T, Garg K, Gist E, Hansen RS, Boatman L, Haugen E, Humbert R, Jain G, Johnson AK, Johnson EM, Kutyavin TM, Lajoie BR, Lee K, Lotakis D, Maurano MT, Neph SJ, Neri FV, Nguyen ED, Qu H, Reynolds AP, Roach V, Rynes E, Sabo P, Sanchez ME, Sandstrom RS, Sanyal A, Shafer AO, Stergachis AB, Thomas S, Thurman RE, Vernot B, Vierstra J, Vong S, Wang H, Weaver MA, Yan Y, Zhang M, Akey JA, Bender M, Dorschner MO, Groudine M, MacCoss MJ, Navas P, Stamatoyannopoulos G, Kaul R, Dekker J, Stamatoyannopoulos JA, Dunham I, Beal K, Brazma A, Flicek P, Herrero J, Johnson N, Keefe D, Lukk M, Luscombe NM, Sobral D, Vaquerizas JM, Wilder SP, Batzoglou S, Sidow A, Hussami N, Kyriazopoulou-Panagiotopoulou S, Libbrecht MW, Schaub MA, Kundaje A, Hardison RC, Miller W, Giardine B, Harris RS, Wu W, Bickel PJ, Banfai B, Boley NP, Brown JB, Huang H, Li Q, Li JJ, Noble WS, Bilmes JA, Buske OJ, Hoffman MM, Sahu AO, Kharchenko PV, Park PJ, Baker D, Taylor J, Weng Z, Iyer S, Dong X, Greven M, Lin X, Wang J, Xi HS, Zhuang J, Gerstein M, Alexander RP, Balasubramanian S, Cheng C, Harmanci A, Lochovsky L, Min R, Mu XJ, Rozowsky J, Yan KK, Yip KY, Birney E.', '22955616', '10.1038/nature11247', 'GSE13008', '', '', '', '', 1354877524);
INSERT INTO `papers` VALUES(28, 'Macfarlan', 2012, 'Embryonic stem cell potency fluctuates with endogenous retrovirus activity.', 'Macfarlan TS, Gifford WD, Driscoll S, Lettieri K, Rowe HM, Bonanomi D, Firth A, Singer O, Trono D, Pfaff SL.', '22722858', '10.1038/nature11244', 'GSE33923', '', '', '', '', 1354879947);
INSERT INTO `papers` VALUES(29, 'DeVeale', 2012, 'Critical evaluation of imprinted gene expression by RNA-Seq: a new perspective.', 'DeVeale B, van der Kooy D, Babak T.', '22479196', '10.1371/journal.pgen.1002600', 'GSE27016', '', '', '', '', 1354880886);
INSERT INTO `papers` VALUES(30, 'Gertz', 2012, 'Transposase mediated construction of RNA-seq libraries.', 'Gertz J, Varley KE, Davis NS, Baas BJ, Goryshin IY, Vaidyanathan R, Kuersten S, Myers RM.', '22128135', '10.1101/gr.127373.111', 'GSE32307', '', '', '', '', 1354882089);
INSERT INTO `papers` VALUES(31, 'Pavri', 2010, 'Activation-induced cytidine deaminase targets DNA at sites of RNA polymerase II stalling by interaction with Spt5.', 'Pavri R, Gazumyan A, Jankovic M, Di Virgilio M, Klein I, Ansarah-Sobrinho C, Resch W, Yamane A, Reina San-Martin B, Barreto V, Nieland TJ, Root DE, Casellas R, Nussenzweig MC.', '20887897', '10.1016/j.cell.2010.09.017', 'GSE24178', '', '', '', '', 1354882345);
INSERT INTO `papers` VALUES(32, 'Tallack', 2010, 'A global role for KLF1 in erythropoiesis revealed by ChIP-seq in primary erythroid cells.', 'Tallack MR, Whitington T, Yuen WS, Wainwright EN, Keys JR, Gardiner BB, Nourbakhsh E, Cloonan N, Grimmond SM, Bailey TL, Perkins AC.', '20508144', '10.1101/gr.106575.110', 'GSE20478', '', '', '', '', 1354887678);
INSERT INTO `papers` VALUES(33, 'Kobayashi', 2012, 'Contribution of intragenic DNA methylation in mouse gametic DNA methylomes to establish oocyte-specific heritable marks.', 'Kobayashi H, Sakurai T, Imai M, Takahashi N, Fukuda A, Yayoi O, Sato S, Nakabayashi K, Hata K, Sotomaru Y, Suzuki Y, Kono T.', '22242016', '10.1371/journal.pgen.1002440', NULL, '', '', '', '', 1354889242);
INSERT INTO `papers` VALUES(34, 'Mikkelsen', 2007, 'Genome-wide maps of chromatin state in pluripotent and lineage-committed cells.', 'Mikkelsen TS, Ku M, Jaffe DB, Issac B, Lieberman E, Giannoukos G, Alvarez P, Brockman W, Kim TK, Koche RP, Lee W, Mendenhall E, O\\''Donovan A, Presser A, Russ C, Xie X, Meissner A, Wernig M, Jaenisch R, Nusbaum C, Lander ES, Bernstein BE.', '17603471', '10.1038/nature06008', 'GSE12241', '', '', 'julian.peat@babraham.ac.uk', 'phil.ewels@babraham.ac.uk', 1354891688);
INSERT INTO `papers` VALUES(36, 'Ramskold', 2012, 'Full-length mRNA-Seq from single-cell levels of RNA and individual circulating tumor cells.', 'RamskÃ¶ld D, Luo S, Wang YC, Li R, Deng Q, Faridani OR, Daniels GA, Khrebtukova I, Loring JF, Laurent LC, Schroth GP, Sandberg R.', '22820318', '10.1038/nbt.2282', 'GSE38495', '', '', '', '', 1354892206);
INSERT INTO `papers` VALUES(37, 'Lienert', 2011, 'Genomic prevalence of heterochromatic H3K9me2 and transcription do not discriminate pluripotent from terminally differentiated cells.', 'Lienert F, Mohn F, Tiwari VK, Baubec T, Roloff TC, Gaidatzis D, Stadler MB, SchÃ¼beler D.', '21655081', '10.1371/journal.pgen.1002090', 'GSE27843', '', '', '', '', 1354894566);
INSERT INTO `papers` VALUES(38, 'Kowalczyk', 2012, 'Intragenic enhancers act as alternative promoters.', 'Kowalczyk MS, Hughes JR, Garrick D, Lynch MD, Sharpe JA, Sloane-Stanley JA, McGowan SJ, De Gobbi M, Hosseini M, Vernimmen D, Brown JM, Gray NE, Collavin L, Gibbons RJ, Flint J, Taylor S, Buckle VJ, Milne TA, Wood WG, Higgs DR.', '22264824', '10.1016/j.molcel.2011.12.021', 'GSE27921', '', 'For Peter Fraser', '', '', 1354895058);
INSERT INTO `papers` VALUES(39, 'Kalhor', 2011, 'Genome architectures revealed by tethered chromosome conformation capture and population-based modeling.', 'Kalhor R, Tjong H, Jayathilaka N, Alber F, Chen L.', '22198700', '10.1038/nbt.2057', NULL, '', '', '', '', 1354895446);
INSERT INTO `papers` VALUES(40, 'Williams', 2011, 'TET1 and hydroxymethylcytosine in transcription and DNA methylation fidelity.', 'Williams K, Christensen J, Pedersen MT, Johansen JV, Cloos PA, Rappsilber J, Helin K.', '21490601', '10.1038/nature10066', 'GSE24843', '', '', '', '', 1354895830);
INSERT INTO `papers` VALUES(42, 'Verma-Gaur', 2012, 'Noncoding transcription within the Igh distal VH region at PAIR elements affects the 3D structure of the Igh locus in pro-B cells.', 'Verma-Gaur J, Torkamani A, Schaffer L, Head SR, Schork NJ, Feeney AJ', '23027941', '10.1073/pnas.1208398109', 'GSE40822', '', '', 'mike.stubbington@babraham.ac.uk', 'phil.ewels@babraham.ac.uk', 1355237227);

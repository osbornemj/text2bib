<?php

namespace Database\Seeders;

use DB;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Example;
use App\Models\ExampleField;

class ExampleSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            [
                'source' => 'Lizzeri A. and N. Persico (2002), \textquotedblleft The Drawbacks of Electoral Competition\textquotedblright , \textit{Journal of the European Economic Association}, forthcoming.% First item',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Lizzeri, A. and N. Persico',
                    'title' => 'The Drawbacks of Electoral Competition',
                    'journal' => 'Journal of the European Economic Association',
                    'year' => '2002',
                    'note' => 'forthcoming',
                ]
            ],
            [
                'source' => '1. Hillisch A, Pineda LF, Hilgenfeld R. Utility of homology models in the drug discovery process. Drug Discov Today. 2004;9:659-669.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Hillisch, A. and Pineda, L. F. and Hilgenfeld, R.',
                    'title' => 'Utility of homology models in the drug discovery process',
                    'journal' => 'Drug Discov Today',
                    'year' => '2004',
                    'volume' => '9',
                    'pages' => '659-669'
                ]
            ],
            [
                'source' => '[13 Ramsook, Caleen B., Cho Tan, Melissa C. Garcia, Raymond Fung, Gregory Soybelman, Ryan Henry, Anna Litewka et al. "Yeast cell adhesion molecules have functional amyloid-forming sequences." Eukaryotic cell 9, no. 3 (2010): 393-404.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ramsook, Caleen B. and Cho Tan and Melissa C. Garcia and Raymond Fung and Gregory Soybelman and Ryan Henry and Anna Litewka and others',
                    'title' => 'Yeast cell adhesion molecules have functional amyloid-forming sequences',
                    'journal' => 'Eukaryotic cell',
                    'year' => '2010',
                    'volume' => '9',
                    'number' => '3',
                    'pages' => '393-404',
                ]
            ],
            [
                'source' => 'Tipparaju, Suresh K et al.. "Identification and development of novel inhibitors of Toxoplasma gondii enoyl reductase." Journal of medicinal chemistry 53.17 (2010): 6287-6300.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Tipparaju, Suresh K. and others',
                    'title' => 'Identification and development of novel inhibitors of Toxoplasma gondii enoyl reductase',
                    'journal' => 'Journal of medicinal chemistry',
                    'year' => '2010',
                    'volume' => '53',
                    'number' => '17',
                    'pages' => '6287-6300',
                ]
            ],
            [
                'source' => 'Do, Thai Q., Safiehkhatoon Moshkani, Patricia Castillo, Suda Anunta, Adelina Pogosyan, Annie Cheung, Beth Marbois et al. "Lipids including cholesteryl linoleate and cholesteryl arachidonate contribute to the inherent antibacterial activity of human nasal fluid." The Journal of Immunology 181, no. 6 (2008): 4177-4187.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Do, Thai Q. and Safiehkhatoon Moshkani and Patricia Castillo and Suda Anunta and Adelina Pogosyan and Annie Cheung and Beth Marbois and others',
                    'title' => 'Lipids including cholesteryl linoleate and cholesteryl arachidonate contribute to the inherent antibacterial activity of human nasal fluid',
                    'journal' => 'The Journal of Immunology',
                    'year' => '2008',
                    'volume' => '181',
                    'number' => '6',
                    'pages' => '4177-4187',
                ]
            ],
            [
                'source' => 'de la Monte, Suzanne M. "Quantitation of cerebral atrophy in preclinical and end-stage alzheimer\'s disease." Annals of neurology 25, no. 5 (1989): 450-459.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'de la Monte, Suzanne M.',
                    'title' => 'Quantitation of cerebral atrophy in preclinical and end-stage alzheimer\'s disease',
                    'journal' => 'Annals of neurology',
                    'year' => '1989',
                    'volume' => '25',
                    'number' => '5',
                    'pages' => '450-459',
                ]
            ],
            [
                'source' => 'Helle, S., V. Lummaa, and J. Jokela. 2004. Accelerated immunosenescence in preindustrial twin mothers. Proceedings of the National Academy of Science of U.S.A. 101:12391-12396.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Helle, S. and V. Lummaa and J. Jokela',
                    'title' => 'Accelerated immunosenescence in preindustrial twin mothers',
                    'journal' => 'Proceedings of the National Academy of Science of U. S. A.',
                    'year' => '2004',
                    'volume' => '101',
                    'pages' => '12391-12396',
                ]
            ],
            [
                'source' => 'Rowcliffe, J.M., de Merode, E. and Cowlishaw, G. 2004. Do wildlife laws work? Species protection and the application of a prey choice model to poaching decisions. Proceedings of the Royal Society B: Biological Sciences 271: 2631-6.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Rowcliffe, J. M. and de Merode, E. and Cowlishaw, G.',
                    'title' => 'Do wildlife laws work? Species protection and the application of a prey choice model to poaching decisions',
                    'journal' => 'Proceedings of the Royal Society B: Biological Sciences',
                    'year' => '2004',
                    'volume' => '271',
                    'pages' => '2631-6',
                ]
            ],
            [
                'source' => 'M. Safari and C. Delacourt ``Aging of a commercial graphite/LiFePO4 cell\'\' J. Electroch. Soc. Vol. 158, Issue 10, pp. A1123-A1135. 2011.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. Safari and C. Delacourt',
                    'title' => 'Aging of a commercial graphite/LiFePO4 cell',
                    'journal' => 'J. Electroch. Soc.',
                    'year' => '2011',
                    'volume' => '158',
                    'number' => '10',
                    'pages' => 'A1123-A1135',
                ]
            ],
            [
                'source' => 'J. Wang, P. Liu, J. Hicks-Garner, E. Sherman, S. Soukiazian, M. Verbrugge, H. Tataria, J. Musser and P. Finamor. ``Cycle-life model for graphite-LiFePO$_4$ cells\'\' J. Power Sources, Vol. 196, Issue 8, pp 3942-3948 April 2011.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Wang and P. Liu and J. Hicks-Garner and E. Sherman and S. Soukiazian and M. Verbrugge and H. Tataria and J. Musser and P. Finamor',
                    'title' => 'Cycle-life model for graphite-LiFePO$_4$ cells',
                    'journal' => 'J. Power Sources',
                    'year' => '2011',
                    'month' => 'April',
                    'volume' => '196',
                    'number' => '8',
                    'pages' => '3942-3948',
                ]
            ],
            [
                'source' => 'W. Waag, C. Fleischer, D. U. Sauer, ``Critical review of the methods for monitoring of lithium-ion batteries in electric and hybrid vehicles\'\', Journal of Power Sources, vol. 258, no. 15, pp. 321-339. 2014.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'W. Waag and C. Fleischer and D. U. Sauer',
                    'title' => 'Critical review of the methods for monitoring of lithium-ion batteries in electric and hybrid vehicles',
                    'journal' => 'Journal of Power Sources',
                    'year' => '2014',
                    'volume' => '258',
                    'number' => '15',
                    'pages' => '321-339',
                ]
            ],
            [
                'source' => 'M. Gholizadeh, F.R. Salmasi, ``Estimation of State of Charge, Unknown Nonlinearities, and State of Health of a Lithium-Ion Battery Based on a Comprehensive Unobservable Model\'\' IEEE Trans. on Industrial Electronics, vol. 61, no. 3, pp. 1335-1344 , Mar. 2014.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. Gholizadeh and F. R. Salmasi',
                    'title' => 'Estimation of State of Charge, Unknown Nonlinearities, and State of Health of a Lithium-Ion Battery Based on a Comprehensive Unobservable Model',
                    'journal' => 'IEEE Trans. on Industrial Electronics',
                    'year' => '2014',
                    'month' => 'March',
                    'volume' => '61',
                    'number' => '3',
                    'pages' => '1335-1344',
                ]
            ],
            [
                'source' => 'J. Li, J.K. Barillas, C. Guenther and M.A. Danzer. ``A comparative study of state of charge estimation algorithms for LiFePO4 batteries used in electric vehicles\'\' J. Power Sources, Vol. 230, pp. 244-250. May 2013.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Li and J. K. Barillas and C. Guenther and M. A. Danzer',
                    'title' => 'A comparative study of state of charge estimation algorithms for LiFePO4 batteries used in electric vehicles',
                    'journal' => 'J. Power Sources',
                    'year' => '2013',
                    'month' => 'May',
                    'volume' => '230',
                    'pages' => '244-250',
                ]
            ],
            [
                'source' => 'L.R. Chen, S.L. Wu, D.T. Shieh and T.R. Chen ``Sinusoidal-Ripple-Current Charging Strategy and Optimal Charging Frequency Study for Li-Ion Batteries\'\' IEEE Trans. Ind. Electron. Vol. 60, no 1, pp. 88-97. Jan. 2013.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'L. R. Chen and S. L. Wu and D. T. Shieh and T. R. Chen',
                    'title' => 'Sinusoidal-Ripple-Current Charging Strategy and Optimal Charging Frequency Study for Li-Ion Batteries',
                    'journal' => 'IEEE Trans. Ind. Electron.',
                    'year' => '2013',
                    'month' => 'January',
                    'volume' => '60',
                    'number' => '1',
                    'pages' => '88-97',
                ]
            ],
            [
                'source' => 'S. Haghbin, S. Lundmark, M. Alakula and O. Carlson. ``Grid-Connected Integrated Battery Chargers in Vehicle Applications: Review and New Solution\'\' IEEE Trans. Ind. Electron. Vol. 60 , no 2; pp. 459 -- 473. 2013. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'S. Haghbin and S. Lundmark and M. Alakula and O. Carlson',
                    'title' => 'Grid-Connected Integrated Battery Chargers in Vehicle Applications: Review and New Solution',
                    'journal' => 'IEEE Trans. Ind. Electron.',
                    'year' => '2013',
                    'volume' => '60',
                    'number' => '2',
                    'pages' => '459-473',
                ]
            ],
            [
                'source' => 'M. Doyle and J. Newman. ``The use of mathematical modeling in the design of lithium/polymer battery systems\'\'. Electrochimica Acta, Vol. 40, Issues 13-14, pp. 2191-2196. Oct. 1995.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. Doyle and J. Newman',
                    'title' => 'The use of mathematical modeling in the design of lithium/polymer battery systems',
                    'journal' => 'Electrochimica Acta',
                    'year' => '1995',
                    'month' => 'October',
                    'volume' => '40',
                    'number' => '13-14',
                    'pages' => '2191-2196',
                ]
            ],
            [
                'source' => 'K. E. Thomas, J. Newman and R.M. Darling. ``Mathematical Modeling of Lithium Batteries\'\' in Advances in Lithium-Ion Batteries. W. van Schalkwijk and B. Scrosati Eds. New York, Kluwer Academic/Plenum Publishers, pp. 345-392. 2002.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'K. E. Thomas and J. Newman and R. M. Darling',
                    'title' => 'Mathematical Modeling of Lithium Batteries',
                    'booktitle' => 'Advances in Lithium-Ion Batteries',
                    'year' => '2002',
                    'address' => 'New York',
                    'publisher' => 'Kluwer Academic/Plenum Publishers',
                    'editor' => 'W. van Schalkwijk and B. Scrosati',
                    'pages' => '345-392',
                ]
            ],
            [
                'source' => 'Itishree Mohanty, Prasun Das, Debashish Bhattacharjee, Shubhabrata Datta (2016), In Search of the Attributes Responsible for Sliver Formation in Cold Rolled Steel Sheets, Journal of The Institution of Engineers (India): Series D, Springer India, Pages 1-12.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Itishree Mohanty and Prasun Das and Debashish Bhattacharjee and Shubhabrata Datta',
                    'title' => 'In Search of the Attributes Responsible for Sliver Formation in Cold Rolled Steel Sheets',
                    'journal' => 'Journal of The Institution of Engineers (India): Series D, Springer India',
                    'year' => '2016',
                    'pages' => '1-12',
                ]
            ],
            [
                'source' => 'Bouskill, N. J., Wood, T. E.; Baran, R.; Ye, Z.; Bowen, B. P.; Lim, H. C.; Zhou, J.; Van Nostrand, J. D.; Nico, P.; Northen, T. R.; Silver, W. L.; Brodie, E. L., Belowground Response to Drought in a Tropical Forest Soil. I. Changes in Microbial Functional Potential and Metabolism. Frontiers in Microbiology 2016, 7',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bouskill, N. J. and Wood, T. E. and Baran, R. and Ye, Z. and Bowen, B. P. and Lim, H. C. and Zhou, J. and Van Nostrand, J. D. and Nico, P. and Northen, T. R. and Silver, W. L. and Brodie, E. L.',
                    'title' => 'Belowground Response to Drought in a Tropical Forest Soil. I. Changes in Microbial Functional Potential and Metabolism',
                    'journal' => 'Frontiers in Microbiology',
                    'year' => '2016',
                    'volume' => '7',
                ]
            ],
            [
                'source' => 'Pardo, Thiago, António Branco, Aldebaro Klautau, Renata Vieira and Vera Strube de Lima (eds.), 2010, Computational Processing of the Portuguese Language, Springer, Berlin.',
                'type' => 'book',
                'bibtex' => [
                    'editor' => 'Pardo, Thiago and Ant{\\\'o}nio Branco and Aldebaro Klautau and Renata Vieira and Vera Strube de Lima',
                    'title' => 'Computational Processing of the Portuguese Language',
                    'year' => '2010',
                    'address' => 'Berlin',
                    'publisher' => 'Springer',
                ]
            ],
            [
                'source' => 'Ault, Bradley A., and Lisa C. Nevett. "Summing Up: Whither the Archaeology of the Greek Household?" In Ancient Greek Houses and Households: Chronological, Regional, and Social Diversity, 160-75. Philadelphia: University of Pennsylvania Press, 2005.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Ault, Bradley A. and Lisa C. Nevett',
                    'title' => 'Summing Up: Whither the Archaeology of the Greek Household?',
                    'booktitle' => 'Ancient Greek Houses and Households: Chronological, Regional, and Social Diversity',
                    'year' => '2005',
                    'publisher' => 'University of Pennsylvania Press',
                    'address' => 'Philadelphia',
                    'pages' => '160-75',
                ]
            ],
            [
                'source' => 'Van de Hulst, H.C., 1981. Light Scattering by small particles, Dover Publications, New York.',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Van de Hulst, H. C.',
                    'title' => 'Light Scattering by small particles',
                    'year' => '1981',
                    'address' => 'New York',
                    'publisher' => 'Dover Publications',
                ]
            ],
            [
                'source' => 'Darby, S. (2001). Making it obvious: designing feedback into energy consumption. Energy Efficiency in Household Appliances and Lighting (pp. 685-696). Berlin, Springer-Verlag.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Darby, S.',
                    'title' => 'Making it obvious: designing feedback into energy consumption',
                    'booktitle' => 'Energy Efficiency in Household Appliances and Lighting',
                    'year' => '2001',
                    'publisher' => 'Springer-Verlag',
                    'address' => 'Berlin',
                    'pages' => '685-696',
                ]
            ],
            [
                'source' => 'Exner, J. E., Jr., Smith, A. B., Sr., Xavier Y. Biden, Jr., and X. Y. Jones, Jr. (1993). The Rorschach: A comprehensive system, Vol. 1. (3rd ed.), New York: John Wiley and Sons.',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Exner, Jr., J. E. and Smith, Sr., A. B. and Biden, Jr., Xavier Y. and Jones, Jr., X. Y.',
                    'title' => 'The Rorschach: A comprehensive system',
                    'edition' => '3rd',
                    'volume' => '1',
                    'year' => '1993',
                    'address' => 'New York',
                    'publisher' => 'John Wiley and Sons',
                ]
            ],
            [
                'source' => '\\"{U}nver, M.U. (2001) "Backward Unraveling over Time: The Evolution of Strategic Behavior in the Entry-Level British Medical Labor Markets." \\emph{Journal of Economic Dynamics and Control} 25: 1039-1080',
                'type' => 'article',
                'bibtex' => [
                    'author' => '\\"{U}nver, M. U.',
                    'title' => 'Backward Unraveling over Time: The Evolution of Strategic Behavior in the Entry-Level British Medical Labor Markets',
                    'journal' => 'Journal of Economic Dynamics and Control',
                    'year' => '2001',
                    'volume' => '25',
                    'pages' => '1039-1080',
                ]
            ],
            [
                'source' => 'He, H. A., Greenberg, S., & Huang, E. M. (2010). One size does not fit all: applying the transtheoretical model to energy feedback technology design. In Proceedings of the 28th international conference on Human factors in computing systems (pp. 927-936). Atlanta, Georgia, USA: ACM.',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'He, H. A. and Greenberg, S. and Huang, E. M.',
                    'title' => 'One size does not fit all: applying the transtheoretical model to energy feedback technology design',
                    'booktitle' => 'Proceedings of the 28th international conference on Human factors in computing systems',
                    'year' => '2010',
                    'publisher' => 'ACM',
                    'address' => 'Atlanta, Georgia, USA',
                    'pages' => '927-936',
                ]
            ],
            [
                'source' => 'CLIFF, Gary X., R.P. VAN DER ELST, GOVENDER AB, SMITH X. Y., Teng A., Ulster, Z., Thomas K. WITTHUKN AND E. M. BULLEN 1996. First estimates of mortality and population size of white sharks on the South African coast. In Great white sharks: the biology of Carcharodon carcharias, Klimley, A.P. and D.G. Ainley. (Eds), Academic Press, San Diego: 393-400.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Cliff, Gary X. and R. P. Van Der Elst and Govender, A. B. and Smith, X. Y. and Teng, A. and Ulster, Z. and Thomas K. Witthukn and E. M. Bullen',
                    'title' => 'First estimates of mortality and population size of white sharks on the South African coast',
                    'booktitle' => 'Great white sharks: the biology of Carcharodon carcharias',
                    'year' => '1996',
                    'publisher' => 'Academic Press',
                    'address' => 'San Diego',
                    'editor' => 'Klimley, A. P. and D. G. Ainley',
                    'pages' => '393-400',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' {\\sl Advances in Applied Microeconomics, v. 6}, M. R. Baye (ed.). Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 6',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' {\\sl Advances in Applied Microeconomics, v. 7}, ed. M. R. Baye. Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 7',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' {\\sl Advances in Applied Microeconomics, v. 8}, M. R. Baye. Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 8',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' in M. R. Baye (ed.), {\\sl Advances in Applied Microeconomics, v. 9}. Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 9',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' in ed. M. R. Baye, {\\sl Advances in Applied Microeconomics, v. 10}. Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 10',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => '{\\sc Harstad, R.M., M.H. Rothkopf, and K. Waehrer}~(1996), ``Efficiency in Auctions when Bidders have Private Information about Competitors,\'\' in M. R. Baye, {\\sl Advances in Applied Microeconomics, v. 11}. Greenwich, CT: JAI Press, pp. 1-13.',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Harstad, R. M. and M. H. Rothkopf and K. Waehrer',
                    'title' => 'Efficiency in Auctions when Bidders have Private Information about Competitors',
                    'booktitle' => 'Advances in Applied Microeconomics, v. 11',
                    'year' => '1996',
                    'publisher' => 'JAI Press',
                    'address' => 'Greenwich, CT',
                    'editor' => 'M. R. Baye',
                    'pages' => '1-13',
                ]
            ],
            [
                'source' => 'Ackermann M, Stearns SC, Jenal U. 2003. Senescence in a bacterium with asymmetric division. \\textit{Science}. 300:1920',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ackermann, M. and Stearns, S. C. and Jenal, U.',
                    'title' => 'Senescence in a bacterium with asymmetric division',
                    'journal' => 'Science',
                    'year' => '2003',
                    'volume' => '300',
                    'pages' => '1920',
                ]
            ],
            [
                'source' => 'Gatersleben, B., Lars Steg, & Vlek, C. (2002). Measurement and determinants of environmentally significant consumer behavior. Environment and Behavior, 34(3), 335.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Gatersleben, B. and Lars Steg and Vlek, C.',
                    'title' => 'Measurement and determinants of environmentally significant consumer behavior',
                    'journal' => 'Environment and Behavior',
                    'year' => '2002',
                    'volume' => '34',
                    'number' => '3',
                    'pages' => '335',
                ]
            ],
            [
                'source' => 'Bartumeus F, Fern\\\'andez P, da Luz, MGE, Catalan J, Sol\\\'e RV, Levin SA (2008) Superdiffusion and encounter rates in diluted, low dimensional worlds. Eur Phys J Spec Topics 157:157--66',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bartumeus, F. and Fern\\\'andez, P. and da Luz, M. G. E. and Catalan, J. and Sol\\\'e, R. V. and Levin, S. A.',
                    'title' => 'Superdiffusion and encounter rates in diluted, low dimensional worlds',
                    'journal' => 'Eur Phys J Spec Topics',
                    'year' => '2008',
                    'volume' => '157',
                    'pages' => '157-66',
                ]
            ],
            [
                'source' => 'von Hofsten, C., and R\\\"onnqvist, L. (1993). The structuring of neonatal arm movements. Child development, 64(4), 1046-57.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'von Hofsten, C. and R\\\"onnqvist, L.',
                    'title' => 'The structuring of neonatal arm movements',
                    'journal' => 'Child development',
                    'year' => '1993',
                    'volume' => '64',
                    'number' => '4',
                    'pages' => '1046-57',
                ]
            ],
            [
                'source' => 'Roberts, John H. and James M. Lattin (1997). Consideration: Review of Research and Prospects for future Insights. Journal of Marketing Research, 34 (August), 406.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Roberts, John H. and James M. Lattin',
                    'title' => 'Consideration: Review of Research and Prospects for future Insights',
                    'journal' => 'Journal of Marketing Research',
                    'year' => '1997',
                    'month' => 'August',
                    'volume' => '34',
                    'pages' => '406',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, and H. Uzawa (1961), "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, and H. Uzawa [1961], ``Constraint qualifications in maximization problems,\'\' {\\it Naval Research Logistics Quarterly}, 8(2), 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'number' => '2',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, \\& H. Uzawa. Constraint qualifications in maximization problems. \\textit{Naval Research Logistics Quarterly}, 8(2): 175--191. 1961.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'number' => '2',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, \\& H. Uzawa. Constraint qualifications in maximization problems. Naval Research Logistics Quarterly, 8(2): 175--191. 1961.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'number' => '2',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, & H. Uzawa. Constraint qualifications in maximization problems, Naval Research Logistics Quarterly, 8(2): 175--191, 1961.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'number' => '2',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., L. Hurwicz, and H. Uzawa. ``Constraint qualifications in maximization problems,\'\' \\emph{Naval Research Logistics Quarterly}, 8 (2): 175-191, 1961.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'number' => '2',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => '{\\sc Arrow, K. J., Hurwicz, L. and H. Uzawa}. ``Constraint qualifications in maximization problems,\'\' {\\em Naval Research Logistics Quarterly}, {\\bf 8} (1961), pp. 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., Hurwicz, L. and H. Uzawa (1961), Constraint qualifications in maximization problems. \\textit{Naval Research Logistics Quarterly}, \\textbf{8}, 175 - 191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and H. Uzawa',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, K. J., Hurwicz, L., and Uzawa, H. \\textquotedblleft Constraint qualifications in maximization problems,\\textquotedblright\\ \\textit{Naval Research Logistics Quarterly} \\textbf{8} (1961), 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and Uzawa, H.',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow, KJ, Hurwicz, L, and Uzawa, H. Constraint qualifications in maximization problems. \\textit{Naval Research Logistics Quarterly}, 8 (1961), 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and Uzawa, H.',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow KJ, Hurwicz L, and Uzawa H. Constraint qualifications in maximization problems. Naval Research Logistics Quarterly, 8 (1961), 175-191.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and Uzawa, H.',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175-191',
                ]
            ],
            [
                'source' => 'Arrow KJ, Hurwicz L, and Uzawa H. Constraint qualifications in maximization problems. Naval Research Logistics Quarterly, 8, 1961, 175.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, K. J. and Hurwicz, L. and Uzawa, H.',
                    'title' => 'Constraint qualifications in maximization problems',
                    'journal' => 'Naval Research Logistics Quarterly',
                    'year' => '1961',
                    'volume' => '8',
                    'pages' => '175',
                ]
            ],
            [
                'source' => 'Andr\\\'{e} B. Chadwick, P. Oblo{\\v z}insk{\\\' y}, M. Herman, N. M. Greene, R. D. McKnight, D. L. Smith, P. G. Young, R. E. MacFarlane, G. M. Hale, S. C. Frankle, A. C. Kahler, T. Kawano, R. C. Little, D. G. Madland, P. Moller, R. D. Mosteller, P. R. Page, P. Talou, H. Trellue, M. C. White, W. B. Wilson, R. Arcilla, C. L. Dunford, S. F. Mughabghab, B. Pritychenko, D. Rochman, A. A. Sonzogni, C. R. Lubitz, T. H. Trumbull, J. P. Weinman, D. A. Br, D. E. Cullen, D. P. Heinrichs, D. P. McNabb, H. Derrien, M. E. Dunn, N. M. Larson, L. C. Leal, A. D. Carlson, R. C. Block, J. B. Briggs, E. T. Cheng, H. C. Huria, M. L. Zerkle, K. S. Kozier, A. Courcelle, V. Pronyaev, and S. C. van der Marck, ``ENDF/B-VII.0: Next Generation Evaluated Nuclear Data Library for Nuclear Science and Technology," Nuclear Data Sheets {\\bf 107}, 2931 (2006).',
                'type' => 'article', '2023-08-01 23:39:03', '2023-08-03 00:09:27',
                'bibtex' => [
                    'author' => 'Andr\\\'{e} B. Chadwick and P. Oblo{\\v z}insk{\\\'y} and M. Herman and N. M. Greene and R. D. McKnight and D. L. Smith and P. G. Young and R. E. MacFarlane and G. M. Hale and S. C. Frankle and A. C. Kahler and T. Kawano and R. C. Little and D. G. Madland and P. Moller and R. D. Mosteller and P. R. Page and P. Talou and H. Trellue and M. C. White and W. B. Wilson and R. Arcilla and C. L. Dunford and S. F. Mughabghab and B. Pritychenko and D. Rochman and A. A. Sonzogni and C. R. Lubitz and T. H. Trumbull and J. P. Weinman and D. A. Br and D. E. Cullen and D. P. Heinrichs and D. P. McNabb and H. Derrien and M. E. Dunn and N. M. Larson and L. C. Leal and A. D. Carlson and R. C. Block and J. B. Briggs and E. T. Cheng and H. C. Huria and M. L. Zerkle and K. S. Kozier and A. Courcelle and V. Pronyaev and S. C. van der Marck',
                    'title' => 'ENDF/B-VII.0: Next Generation Evaluated Nuclear Data Library for Nuclear Science and Technology',
                    'journal' => 'Nuclear Data Sheets',
                    'year' => '2006',
                    'volume' => '107',
                    'pages' => '2931',
                ]
            ],
            [
                'source' => 'Cusihuamán G., Antonio. (1976) Gramática quechua: Cuzco/Collao. Lima: Ministerio de Educación.',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Cusihuam{\\\'a}n G., Antonio',
                    'title' => 'Gram{\\\'a}tica quechua: Cuzco/Collao',
                    'year' => '1976',
                    'address' => 'Lima',
                    'publisher' => 'Ministerio de Educaci{\\\'o}n',
                ]
            ],
            [
                'source' => 'Michael, Lev David. (2008) Nanti evidential practice: Language, knowledge, and social action in an Amazonian society. Austin: University of Texas at Austin, PhD thesis.',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Michael, Lev David',
                    'title' => 'Nanti evidential practice: Language, knowledge, and social action in an Amazonian society',
                    'school' => 'University of Texas at Austin',
                    'year' => '2008',
                    'note' => 'Austin',
                ]
            ],
            [
                'source' => 'Leister, H.-J., Peri\\\'{c}, M. (1994): Vectorized strongly implicit solving procedure for seven-diagonal coefficient matrix. Int.\\ J.\\ Numer.\\ Meth.\\ Heat Fluid Flow, {\\bf 4}, 159—172',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Leister, H.-J. and Peri\\\'{c}, M.',
                    'title' => 'Vectorized strongly implicit solving procedure for seven-diagonal coefficient matrix',
                    'journal' => 'Int. J. Numer. Meth. Heat Fluid Flow',
                    'year' => '1994',
                    'volume' => '4',
                    'pages' => '159-172',
                ]
            ],
            [
                'source' => 'Barbe, Walter Burke; Swassing, Raymond H.; Milone, Michael N. (1979). Teaching through modality strengths: concepts practices. Columbus, Ohio: Zaner-Bloser. ISBN 0883091003. OCLC 5990906.',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Barbe, Walter Burke and Swassing, Raymond H. and Milone, Michael N.',
                    'title' => 'Teaching through modality strengths: concepts practices',
                    'year' => '1979',
                    'address' => 'Columbus, Ohio',
                    'publisher' => 'Zaner-Bloser',
                    'isbn' => '0883091003',
                    'oclc' => '5990906',
                ]
            ],
            [
                'source' => 'Echoxiii. (2013). How to Make a Sound Map: Cartographic, Compositional, Performative. Acoustic Ecology @ The University of Hull, Scarborough Campus. Retrieved from https://acousticecologyuoh.wordpress.com/2013/12/04/how-to-make-a-sound-map/, 29 May 2018',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Echoxiii',
                    'title' => 'How to Make a Sound Map: Cartographic, Compositional, Performative',
                    'note' => 'Acoustic Ecology @ The University of Hull, Scarborough Campus',
                    'year' => '2013',
                    'url' => 'https://acousticecologyuoh.wordpress.com/2013/12/04/how-to-make-a-sound-map/',
                    'urldate' => '29 May 2018',
                ]
            ],
            [
                'source' => 'VGStorm. (2016). Adventure at C. http://www.vgstorm.com/aac/about.php, accessed 2 Sept 2016',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'VGStorm',
                    'title' => 'Adventure at C',
                    'year' => '2016',
                    'url' => 'http://www.vgstorm.com/aac/about.php',
                    'urldate' => '2 Sept 2016',
                ]
            ],
            [
                'source' => 'Techopedia. (2018). What is a Side Scroller? https://www.techopedia.com/definition/27153/side-scroller (viewed 2018/4/2)',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Techopedia',
                    'title' => 'What is a Side Scroller?',
                    'year' => '2018',
                    'url' => 'https://www.techopedia.com/definition/27153/side-scroller',
                    'urldate' => '2018/4/2',
                ]
            ],
            [
                'source' => 'audiogames.net. (2017). AudioGames, your resource for audiogames, games for the blind, games for the visually impaired! http://audiogames.net/, retrieved Sept 2, 2018',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'audiogames.net',
                    'title' => 'AudioGames, your resource for audiogames, games for the blind, games for the visually impaired!',
                    'year' => '2017',
                    'url' => 'http://audiogames.net/',
                    'urldate' => 'Sept 2, 2018',
                ]
            ],
            [
                'source' => 'E.A. Pronin, A.F. Starace, and L.-Y. Peng, ``Perturbation-theory analysis of ionization by a chirped few-cycle attosecond pulse,\'\' Phys. Rev. A \\textbf{84}, 013417 (2011).',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'E. A. Pronin and A. F. Starace and L.-Y. Peng',
                    'title' => 'Perturbation-theory analysis of ionization by a chirped few-cycle attosecond pulse',
                    'journal' => 'Phys. Rev. A',
                    'year' => '2011',
                    'volume' => '84',
                    'pages' => '013417',
                ]
            ],
            [
                'source' => 'D. Zille, D. Adolph, M. Moller, A.M. Sayler, and G.G. Paulus, ``Chirp and carrier-envelope-phase effects in the multiphoton regime: measurements and analytical modeling o strong-field ionization of sodium,\'\' New J. Phys. \\textbf{20}, 063018 (2018).',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'D. Zille and D. Adolph and M. Moller and A. M. Sayler and G. G. Paulus',
                    'title' => 'Chirp and carrier-envelope-phase effects in the multiphoton regime: measurements and analytical modeling o strong-field ionization of sodium',
                    'journal' => 'New J. Phys.',
                    'year' => '2018',
                    'volume' => '20',
                    'pages' => '063018',
                ]
            ],
            [
                'source' => '\\bibitem{geant3} J. Allison et al., \\textit{Recent developments in Geant4}, Nuclear Instruments and Methods in Physics Research Section A: Accelerators, Spectrometers, Detectors and Associated Equipment, vol. 835, pp. 186–225, 2016. https://www.sciencedirect.com/science/article/pii/S0168900216306957 [Cited on page 111.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Allison and others',
                    'title' => 'Recent developments in Geant4',
                    'journal' => 'Nuclear Instruments and Methods in Physics Research Section A: Accelerators, Spectrometers, Detectors and Associated Equipment',
                    'volume' => '835',
                    'year' => '2016',
                    'pages' => '186-225',
                    'url' => 'https://www.sciencedirect.com/science/article/pii/S0168900216306957',
                ]
            ],
            [
                'source' => 'Ahmed, S., Hasan, B., Jrad, F., & Dlask, P. (2016). Analyzing the change orders impact on building projects. Journal of engineering and applied sciences, 11(7), 1532–1537.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ahmed, S. and Hasan, B. and Jrad, F. and Dlask, P.',
                    'title' => 'Analyzing the change orders impact on building projects',
                    'journal' => 'Journal of engineering and applied sciences',
                    'year' => '2016',
                    'volume' => '11',
                    'number' => '7',
                    'pages' => '1532-1537',
                ]
            ],
            [
                'source' => 'Aichouni, M., Ait Messaoudene, N., Al-Ghonamy, A., & Touahmia, M. (2014). An empirical study of quality management systems in the Saudi construction industry. International Journal of Construction Management, 14(3), 181-190.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Aichouni, M. and Ait Messaoudene, N. and Al-Ghonamy, A. and Touahmia, M.',
                    'title' => 'An empirical study of quality management systems in the Saudi construction industry',
                    'journal' => 'International Journal of Construction Management',
                    'year' => '2014',
                    'volume' => '14',
                    'number' => '3',
                    'pages' => '181-190',
                ]
            ],
            [
                'source' => 'Catry, B., et al., Reflection paper on MRSA in food-producing and companion animals: epidemiology and control options for human and animal health. Epidemiol Infect, 2010. 138(5): p. 626-44.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Catry, B. and others',
                    'title' => 'Reflection paper on MRSA in food-producing and companion animals: epidemiology and control options for human and animal health',
                    'journal' => 'Epidemiol Infect',
                    'year' => '2010',
                    'volume' => '138',
                    'number' => '5',
                    'pages' => '626-44',
                ]
            ],
            [
                'source' => '\\bibitem{} Brandenburger, A. and E. Dekel (1987). \\textquotedblleft Rationalizability and Correlated Equilibria,\\textquotedblright\\ \\textit{Econometrica} \\textbf{55}, 1391-1402.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Brandenburger, A. and E. Dekel',
                    'year' => '1987',
                    'title' => 'Rationalizability and Correlated Equilibria',
                    'journal' => 'Econometrica',
                    'pages' => '1391-1402',
                    'volume' => '55'
                ]
            ],
            [
                'source' => '\bibitem {MPR} Milgrom, P. (1989), ``Auctions and Bidding: A Primer,\'\' {\it Journal of Economic Perspectives}, 3, 3-22.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Milgrom, P.',
                    'title' => 'Auctions and Bidding: A Primer',
                    'journal' => 'Journal of Economic Perspectives',
                    'volume' => '3',
                    'pages' => '3-22',
                    'year' => '1989'
                ]
            ],
            [
                'source' => 'Glejser, H., \& Heyndels, B. Efficiency and inefficiency in the ranking in competitions: The case of the Queen Elisabeth Music Contest. \textit{Journal of Cultural Economics}, 25 (2001), 109--129. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2001',
                    'pages' => '109-129',
                    'title' => 'Efficiency and inefficiency in the ranking in competitions: The case of the Queen {E}lisabeth Music Contest',
                    'author' => 'Glejser, H. and Heyndels, B.',
                    'volume' => '25',
                    'journal' => 'Journal of Cultural Economics',
                ],
                'use' => 'latex',
            ],    
            [
                'source' => 'Mertens, J.-F., S. Sorin and S. Zamir (1994). Repeated Games: Part A Background Material, CORE Discussion Paper \#9420. ',
                'type' => 'techreport',
                'bibtex' => [
                    'type' => 'Discussion Paper',
                    'year' => '1994',
                    'title' => 'Repeated Games: Part A Background Material',
                    'author' => 'Mertens, J.-F. and S. Sorin and S. Zamir',
                    'number' => '9420',
                    'institution' => 'CORE',
                    ]
            ],
            [
                'source' => 'Kasper Nielsen. Institutional Investors and the Market for Corporate Equity. Working paper 33, University of Copenhagen, September 2003. ',
                'type' => 'techreport',
                'bibtex' => [
                    'type' => 'Working paper',
                    'year' => '2003',
                    'month' => 'September',
                    'title' => 'Institutional Investors and the Market for Corporate Equity',
                    'author' => 'Kasper Nielsen',
                    'number' => '33',
                    'institution' => 'University of Copenhagen',
                    ]
            ],  
            [
                'source' => 'Rubinstein, A., Tversky, A., \& Heller, D. (1996). Naive Strategies in Zero-sum Games. \textit{Understanding Strategic Interaction -- Essays in Honor of Reinhard Selten}, W.Guth et al. (editors), Springer-Verlag, 394-402.',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1996',
                    'pages' => '394-402',
                    'title' => 'Naive Strategies in Zero-sum Games',
                    'author' => 'Rubinstein, A. and Tversky, A. and Heller, D.',
                    'editor' => 'W. Guth and others',
                    'booktitle' => 'Understanding Strategic Interaction -- Essays in Honor of Reinhard Selten',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],
            [
                'source' => '[13] Laffont, Jean-Jacques, Eric Maskin, and Jean-Charles Rochet, ``Optimal Nonlinear Pricing with Two-Dimensional Characteristics,\'\' in T. Groves, R. Radner and S. Reiter, (eds.), Information, Incentives and Economic Mechanism (Minneapolis: University of Minnesota Press, 1987). ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'Optimal Nonlinear Pricing with Two-Dimensional Characteristics',
                    'author' => 'Laffont, Jean-Jacques and Eric Maskin and Jean-Charles Rochet',
                    'editor' => 'T. Groves and R. Radner and S. Reiter',
                    'address' => 'Minneapolis',
                    'booktitle' => 'Information, Incentives and Economic Mechanism',
                    'publisher' => 'University of Minnesota Press',
                    ]
            ],
            [
                'source' => '\bibitem{} Dekel, E., D. Fudenberg, and D. K. Levine (2004) . "Learning to Play Bayesian Games," \textit{Games and Economic Behavior}, \textbf{46}, 282-303. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dekel, E. and D. Fudenberg and D. K. Levine',
                    'year' => '2004',
                    'title' => 'Learning to Play {B}ayesian Games',
                    'journal' => 'Games and Economic Behavior',
                    'volume' => '46',
                    'pages' => '282-303',
                ],
                'use' => 'latex',
            ],
            [
                'source' => 'Bruine de Bruin, W., \& Keren, G. (2003). ``Save the last dance for me: Unwanted order effects in jury evaluations.\'\' \textit{Manuscript under review}. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'year' => '2003',
                    'title' => 'Save the last dance for me: Unwanted order effects in jury evaluations',
                    'author' => 'Bruine de Bruin, W. and Keren, G.',
                    'note' => 'Manuscript under review',
                    ]
            ],
            [
                'source' => '\bibitem{dennis-strickland} Patrick Dennis and Deon Strickland. Who Blinks in Volatile Markets, Individuals or Institutions? \ \emph{Journal of Finance} 57(5): 1923-1950. 2002. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2002',
                    'pages' => '1923-1950',
                    'title' => 'Who Blinks in Volatile Markets, Individuals or Institutions?',
                    'author' => 'Patrick Dennis and Deon Strickland',
                    'number' => '5',
                    'volume' => '57',
                    'journal' => 'Journal of Finance',
                    ]
            ], 
            [
                'source' => '\bibitem{glaser-weber} Markus Glaser and Martin Weber. Overconfindence and trading volume. CEPR Discussion Paper No. 3941, 2003.',
                'type' => 'techreport',
                'bibtex' => [
                    'type' => 'Discussion Paper',
                    'year' => '2003',
                    'title' => 'Overconfindence and trading volume',
                    'author' => 'Markus Glaser and Martin Weber',
                    'institution' => 'CEPR',
                    'number' => '3941',
                    ]
            ],
            [
                'source' => '\bibitem{gumbel} Alexander G\"{u}mbel. Trading on short-term information. Forthcoming, \emph{Journal of Institutional and Theoretical Economics}. 2004.',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'Forthcoming',
                    'year' => '2004',
                    'title' => 'Trading on short-term information',
                    'author' => 'Alexander G\"{u}mbel',
                    'journal' => 'Journal of Institutional and Theoretical Economics',
                    ]
            ],
            [
                'source' => '\bibitem{vayanos} Dimitri Vayanos. Flight to Quality, Flight to Liquidity, and the Pricing of Risk. Working paper, MIT, 2003.',
                'type' => 'unpublished',
                'bibtex' => [
                    'note' => 'Working paper, MIT',
                    'year' => '2003',
                    'title' => 'Flight to Quality, Flight to Liquidity, and the Pricing of Risk',
                    'author' => 'Dimitri Vayanos',
                    ]
            ],
            [
                // Note: no space at end of authors
                'source' => '\noindent {\sc Joseph Greenberg, Benyamin Shitovitz \& A. Wieczorek},``Existence of Equilibria in Atomless Production Economies with Price Dependent Preferences,\'\' {\em Journal of Mathematical Economics} {\bf 6} (1979), 31-41 . ',
                'type' => 'article',
                'bibtex' => [
                    'volume' => '6',
                    'pages' => '31-41',
                    'year' => '1979',
                    'journal' => 'Journal of Mathematical Economics',
                    'title' => 'Existence of Equilibria in Atomless Production Economies with Price Dependent Preferences',
                    'author' => 'Joseph Greenberg and Benyamin Shitovitz and A. Wieczorek',
                    ]
            ],
            [
                'source' => '{\sc Bikhchandani, S., S. Chatterji, R. Lavi, A. Mu\'alem,  N. Nisan, and A. Sen}~(2006), ``Weak Monotonicity Characterizes Dominant Strategy Implementation,\'\' forthcoming {\it Econometrica}. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'forthcoming',
                    'year' => '2006',
                    'title' => 'Weak Monotonicity Characterizes Dominant Strategy Implementation',
                    'author' => 'Bikhchandani, S. and S. Chatterji and R. Lavi and A. Mu\'alem and N. Nisan and A. Sen',
                    'journal' => 'Econometrica',
                    ]
            ],
            [
                'source' => '{\sc Dasgupta, P. and E. Maskin~(2000)}, ``Efficient Auctions,\'\' {\it Quarterly Journal of Economics}, 115, 341-388. ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Quarterly Journal of Economics',
                    'year' => '2000',
                    'volume' => '115',
                    'pages' => '341-388',
                    'title' => 'Efficient Auctions',
                    'author' => 'Dasgupta, P. and E. Maskin',
                    ]
            ],
            [
                'source' => '\bibitem{} \\\'{A}brah\\\'{a}m \\\'{A}. and E. Carceles-Poveda (2006), \textquotedblleft Risk Sharing under Limited Commitment\textquotedblright , mimeo., University of Rochester and SUNY, Stony Brook. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'year' => '2006',
                    'title' => 'Risk Sharing under Limited Commitment',
                    'author' => '\\\'{A}brah\\\'{a}m, \\\'{A}. and E. Carceles-Poveda',
                    'note' => 'mimeo., University of Rochester and SUNY, Stony Brook',
                    ]
            ],
            [
                'source' => '\bibitem{} Kehoe, P. and F. Perri (2002b), \textquotedblleft Competitive Equilibria with Limited Enforcement\textquotedblright , \textit{NBER Working Paper 9077}. ',
                'type' => 'techreport',
                'bibtex' => [
                    'type' => 'Working Paper',
                    'year' => '2002',
                    'title' => 'Competitive Equilibria with Limited Enforcement',
                    'author' => 'Kehoe, P. and F. Perri',
                    'number' => '9077',
                    'institution' => 'NBER',
                    ]
            ],
            [
                'source' => 'Glazer, Jacob and Ariel Rubinstein (2001), Debates and Decisions, On a Rationale of Argumentation Rules, \textit{Games and Economic Behavior}, 36, 158-173',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2001',
                    'pages' => '158-173',
                    'title' => 'Debates and Decisions, On a Rationale of Argumentation Rules',
                    'author' => 'Glazer, Jacob and Ariel Rubinstein',
                    'volume' => '36',
                    'journal' => 'Games and Economic Behavior',
                    ]
            ],
            [
                // Note: no space before year
                'source' => '\bibitem{} Bai, Y. and J. Zhang(2005), \textquotedblleft Explaining the Cross-Section Feldstein-Horioka Puzzle\textquotedblright , Unpublished Manuscript, University of Minnesota. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'note' => 'Unpublished Manuscript, University of Minnesota',
                    'year' => '2005',
                    'title' => 'Explaining the Cross-Section Feldstein-Horioka Puzzle',
                    'author' => 'Bai, Y. and J. Zhang',
                    ]
            ],
            [
                'source' => 'Savage (1954) \textit{The Foundations of Statistics}, Wiley, New York.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1954',
                    'title' => 'The Foundations of Statistics',
                    'author' => 'Savage',
                    'address' => 'New York',
                    'publisher' => 'Wiley',
                    ]
            ],
            [
                'source' => '\bibitem{duff1} Darrell Duffie and Wayne Shafer, Equilibrium in Incomplete Markets: I. {\em Journal of Mathematical Economics} 14(1985), 285-300. ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Journal of Mathematical Economics',
                    'volume' => '14',
                    'pages' => '285-300',
                    'year' => '1985',
                    'title' => 'Equilibrium in Incomplete Markets: I',
                    'author' => 'Darrell Duffie and Wayne Shafer',
                    ]
            ],
            [
                'source' => '\bibitem{Magill Shafer} Michael Magill and Wayne Shafer, Incomplete Markets, pages 1523-1614 in {\em Handbook of Mathematical Economics,  vol. IV}, North-Holland, Amsterdam, 1991.',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1991',
                    'pages' => '1523-1614',
                    'title' => 'Incomplete Markets',
                    'author' => 'Michael Magill and Wayne Shafer',
                    'booktitle' => 'Handbook of Mathematical Economics, vol. IV',
                    'publisher' => 'North-Holland',
                    'address' => 'Amsterdam'
                    ]
            ],
            [
                'source' => '\bibitem{Monteiro} Paulo Klinger Monteiro, A New Proof of the Existence of Equilibrium in Incomplete Market Economies, {\em Journal of Mathematical Economics} 26(1996), 85-101. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1996',
                    'pages' => '85-101',
                    'title' => 'A New Proof of the Existence of Equilibrium in Incomplete Market Economies',
                    'author' => 'Paulo Klinger Monteiro',
                    'volume' => '26',
                    'journal' => 'Journal of Mathematical Economics',
                    ]
            ],                                                                                               
            [
                'source' => '\bibitem{Raimondo Algebraic Geometry} Roberto C. Raimondo, Hart Effect and Equilibrium in Incomplete Markets I, Research Paper Number 876, Department of Economics, The University of Melbourne, Australia, March 2003. ',
                'type' => 'techreport',
                'bibtex' => [
                    'type' => 'Research Paper',
                    'year' => '2003',
                    'month' => 'March',
                    'title' => 'Hart Effect and Equilibrium in Incomplete Markets I',
                    'author' => 'Roberto C. Raimondo',
                    'number' => '876',
                    'institution' => 'Department of Economics, The University of Melbourne, Australia',
                    ]
            ],
            [
                'source' => '\bibitem{Raimondo Discrete Time} Roberto C. Raimondo, ``Incomplete Markets with a Continuum of States,\'\' Department of Economics, The University of Melbourne, November 2002. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'year' => '2002',
                    'month' => 'November',
                    'title' => 'Incomplete Markets with a Continuum of States',
                    'author' => 'Roberto C. Raimondo',
                    'note' => 'Department of Economics, The University of Melbourne',
                    ]
            ],
            [
                'source' => 'Y. Lv, Y. Duan, W. Kang, Z. Li, and F. Wang, “Traffic flow prediction with big data: A deep learning approach,” IEEE Trans. Intel. Transp. Syst., vol. 16, no. 2, pp. 865–873, Apr. 2015.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2015',
                    'month' => 'April',
                    'pages' => '865-873',
                    'title' => 'Traffic flow prediction with big data: A deep learning approach',
                    'author' => 'Y. Lv and Y. Duan and W. Kang and Z. Li and F. Wang',
                    'volume' => '16',
                    'number' => '2',
                    'journal' => 'IEEE Trans. Intel. Transp. Syst.',

                ]
            ],
            [
                'source' => 'Chakraborty, A. and R. Harbaugh [2005]: ``Comparative cheap talk,\'\' Journal of Economic Theory, forthcoming.',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'forthcoming',
                    'year' => '2005',
                    'title' => 'Comparative cheap talk',
                    'author' => 'Chakraborty, A. and R. Harbaugh',
                    'journal' => 'Journal of Economic Theory',
                    ]
            ],
            [
                'source' => '[19] Page Jr., H. Frank and Paulo K. Monteiro. ``Three Principles of Competitive Nonlinear Pricing,\'\' \textit{Journal of Mathematical Economics}, 2003, 39, pp 63-109.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2003',
                    'pages' => '63-109',
                    'title' => 'Three Principles of Competitive Nonlinear Pricing',
                    'author' => 'Page Jr., H. Frank and Paulo K. Monteiro',
                    'volume' => '39',
                    'journal' => 'Journal of Mathematical Economics',
                    ]
            ],
            [
                'source' => '[11] Johnson, Justin and David Myatt. ``Multiproduct Cournot Oligopoly,\'\' forthcoming at \textit{Rand Journal of Economics}, 2005.',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'forthcoming',
                    'year' => '2005',
                    'title' => 'Multiproduct {C}ournot Oligopoly',
                    'author' => 'Johnson, Justin and David Myatt',
                    'journal' => 'Rand Journal of Economics',
                ],
                    'use' => 'latex',
            ],
            [
                'source' => '[14] Martimort, David and Lars Stole. ``Communication Spaces, Equilibria Sets and the Revelation Principle Under Common Agency,\'\' 1997, Chicago GSB Working Paper. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'year' => '1997',
                    'title' => 'Communication Spaces, Equilibria Sets and the Revelation Principle Under Common Agency',
                    'author' => 'Martimort, David and Lars Stole',
                    'note' => 'Chicago GSB Working Paper',
                    ]
            ],
            [
                'source' => 'Fernández, R. and J. Galí, (1999), ``To each according to...? Markets, tournaments and the matching problem with borrowing constraints\'\', \textit{Review of Economic Studies}, 66, 799-824. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1999',
                    'pages' => '799-824',
                    'title' => 'To each according to ... ? Markets, tournaments and the matching problem with borrowing constraints',
                    'author' => 'Fern{\\\'a}ndez, R. and J. Gal{\\\'\i}',
                    'volume' => '66',
                    'journal' => 'Review of Economic Studies',
                    ]
            ],
            [
                // Note error: year is duplicated
                'source' => '[19] Slovic, Paul. 1966. ``Risk-Taking in Children: Age and Sex Differences.\'\' 1966 \textit{Child Development} 37:169-176. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1966',
                    'pages' => '169-176',
                    'title' => 'Risk-Taking in Children: Age and Sex Differences',
                    'author' => 'Slovic, Paul',
                    'volume' => '37',
                    'journal' => 'Child Development',
                    ]
            ],
            [
                'source' => '[13] Harry Holzer and David Neumark. 2000. ``Assessing Affirmative Action.\'\' \textit{Journal of Economic Literature} XXXVIII:483-569. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2000',
                    'volume' => 'XXXVIII',
                    'pages' => '483-569',
                    'title' => 'Assessing Affirmative Action',
                    'author' => 'Harry Holzer and David Neumark',
                    'journal' => 'Journal of Economic Literature',
                    ]
            ],
            [
                'source' => '[8] Eckel, Catherine C. and Philip J. Grossman. 2005a. ``Sex and Risk: Experimental Evidence.\'\' (Forthcoming in) \textit{Handbook of Experimental Economics Results}. Amsterdam: Elsevier Science (North-Holland).',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2005',
                    'title' => 'Sex and Risk: Experimental Evidence',
                    'author' => 'Eckel, Catherine C. and Philip J. Grossman',
                    'booktitle' => 'Handbook of Experimental Economics Results',
                    'address' => 'Amsterdam',
                    'publisher' => 'Elsevier Science (North-Holland)',
                    'note' => 'Forthcoming',
                    ]
            ],
            [
                'source' => '\bibitem{K2-50} Atiyah, M. F. (1969). The signature of fibre-bundles. In D. C. Spencer \& S. Iyanaga (Eds.), Global Analysis. Papers in honor of K. Kodaira, pp. 73-84. Princeton Univ. Press.',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1969',
                    'pages' => '73-84',
                    'title' => 'The signature of fibre-bundles',
                    'author' => 'Atiyah, M. F.',
                    'editor' => 'D. C. Spencer and S. Iyanaga',
                    'booktitle' => 'Global Analysis. Papers in honor of K. Kodaira',
                    'publisher' => 'Princeton Univ. Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-54} Auckly, D. R. (1995). Surgery numbers of 3-manifolds: a hyperbolic example. In W. H. Kazez (Ed.), Geometric Topology, Proc. of the 1993 Georgia International Topology Conference. International Press.',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1995',
                    'title' => 'Surgery numbers of 3-manifolds: a hyperbolic example',
                    'author' => 'Auckly, D. R.',
                    'editor' => 'W. H. Kazez',
                    'booktitle' => 'Geometric Topology, Proc. of the 1993 Georgia International Topology Conference',
                    'publisher' => 'International Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-63} Bar-Natan, D. (1995b). Vassiliev homotopy string link invariants. To appear in J. Knot Theory Ramifications.  ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'To appear',
                    'year' => '1995',
                    'title' => 'Vassiliev homotopy string link invariants',
                    'author' => 'Bar-Natan, D.',
                    'journal' => 'J. Knot Theory Ramifications',
                    ]
            ],
            [
                'source' => '\bibitem{K2-69} Bass, H. \& Morgan, J. W. (1984a). The Smith Conjecture, Volume 112 of Pure and Applied Mathematics. Academic Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1984',
                    'title' => 'The Smith Conjecture',
                    'author' => 'Bass, H. and Morgan, J. W.',
                    'volume' => '112',
                    'series' => 'Pure and Applied Mathematics',
                    'publisher' => 'Academic Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-70} Bauer, S. (1988). The homotopy type of a 4-manifold with finite fundamental group. In T. tom Dieck (Ed.), Algebraic Topology and Transformation Groups, Volume 1361 of Lecture Notes in Math., pp. 1-6. Springer-Verlag.',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1988',
                    'pages' => '1-6',
                    'title' => 'The homotopy type of a 4-manifold with finite fundamental group',
                    'author' => 'Bauer, S.',
                    'editor' => 'T. tom Dieck',
                    'volume' => '1361',
                    'series' => 'Lecture Notes in Math.',
                    'publisher' => 'Springer-Verlag',
                    'booktitle' => 'Algebraic Topology and Transformation Groups',
                    ]
            ],
            [
                'source' => '\bibitem{K2-75} Baumslag, G., Dyer, E., \& Miller, III, C. F. (1983). On the integral homology of finitely presented groups. Topology Vol. 22, 27-46.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1983',
                    'pages' => '27-46',
                    'title' => 'On the integral homology of finitely presented groups',
                    'author' => 'Baumslag, G. and Dyer, E. and Miller, III, C. F.',
                    'volume' => '22',
                    'journal' => 'Topology',
                    ]
            ],
            [
                'source' => '\bibitem{K2-82} Besse, A. L. (1987). Einstein Manifolds, Volume 10 of Ergeb. Math. Grenzgeb. Springer-Verlag.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'Einstein Manifolds',
                    'author' => 'Besse, A. L.',
                    'series' => 'Ergeb. Math. Grenzgeb',
                    'volume' => '10',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],
            [
                'source' => '\bibitem{K2-82} Besse, A. L. (1987). Einstein Manifolds, Volume 10 of Ergeb. Math. Grenzgeb. Berlin: Springer-Verlag.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'Einstein Manifolds',
                    'author' => 'Besse, A. L.',
                    'series' => 'Ergeb. Math. Grenzgeb',
                    'volume' => '10',
                    'address' => 'Berlin',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],
            [
                'source' => '\bibitem{K2-82} Besse, A. L. (1987). Einstein Manifolds, Volume 10 of Ergeb. Math. Grenzgeb. Springer-Verlag, Berlin.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'Einstein Manifolds',
                    'author' => 'Besse, A. L.',
                    'series' => 'Ergeb. Math. Grenzgeb',
                    'volume' => '10',
                    'address' => 'Berlin',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],                                                                                                                              [
                'source' => '\bibitem{K2-82} Besse, A. L. (1987). Einstein Manifolds, Volume 10 of Ergeb. Math. Grenzgeb, Springer-Verlag, Berlin.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'Einstein Manifolds',
                    'author' => 'Besse, A. L.',
                    'series' => 'Ergeb. Math. Grenzgeb',
                    'volume' => '10',
                    'address' => 'Berlin',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],
            [
                'source' => '\bibitem{K2-90} Birman, J. S. (1974). Braids, Links, and Mapping Class Groups, Volume 82 of Ann. of Math. Stud. Princeton Univ. Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1974',
                    'title' => 'Braids, Links, and Mapping Class Groups',
                    'author' => 'Birman, J. S.',
                    'series' => 'Ann. of Math. Stud',
                    'volume' => '82',
                    'publisher' => 'Princeton Univ. Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-90} Birman, J. S. (1974). Braids, Links, and Mapping Class Groups, Volume 82 of Ann. of Math. Stud. Princeton: Princeton Univ. Press.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1974',
                    'title' => 'Braids, Links, and Mapping Class Groups',
                    'author' => 'Birman, J. S.',
                    'series' => 'Ann. of Math. Stud',
                    'volume' => '82',
                    'address' => 'Princeton',
                    'publisher' => 'Princeton Univ. Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-90} Birman, J. S. (1974). Braids, Links, and Mapping Class Groups, Volume 82 of Ann. of Math. Stud. Princeton Univ. Press, Princeton.',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1974',
                    'title' => 'Braids, Links, and Mapping Class Groups',
                    'author' => 'Birman, J. S.',
                    'series' => 'Ann. of Math. Stud',
                    'volume' => '82',
                    'address' => 'Princeton',
                    'publisher' => 'Princeton Univ. Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-92} Birman, J. S., Gonzalez-Acu\~{n}a, E., \& Montesinos, J. M. (1976). Heegaard splittings of prime 3-manifolds are not unique. Michigan Math. J. Vol. 23, 97-103.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1976',
                    'pages' => '97-103',
                    'title' => 'Heegaard splittings of prime 3-manifolds are not unique',
                    'author' => 'Birman, J. S. and Gonzalez-Acu\~{n}a, E. and Montesinos, J. M.',
                    'volume' => '23',
                    'journal' => 'Michigan Math. J.',
                    ]
            ],
            [
                'source' => '\bibitem{K2-99} Bi\v{z}aca, \v{Z}. (1994). A handle decomposition of an exotic $\mathbb{R}^{4}$. J. Differential Geom. Vol. 39, 491-508.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1994',
                    'pages' => '491-508',
                    'title' => 'A handle decomposition of an exotic $\mathbb{R}^{4}$',
                    'author' => 'Bi\v{z}aca, \v{Z}.',
                    'volume' => '39',
                    'journal' => 'J. Differential Geom.',
                    ]
            ],
            [
                'source' => '\bibitem{K2-109} Bleiler, S. A. \& Scharlemann, M. G. (1988). A projective plane in $\mathbb{R}^{4}$ with three critical points is standard. Strongly invertible knots have property P. Topology Vol. 27, 519-540.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1988',
                    'pages' => '519-540',
                    'title' => 'A projective plane in $\mathbb{R}^{4}$ with three critical points is standard. Strongly invertible knots have property P',
                    'author' => 'Bleiler, S. A. and Scharlemann, M. G.',
                    'volume' => '27',
                    'journal' => 'Topology',
                    ]
            ],
            [
                'source' => '\bibitem{K2-110} Bo\\\'echat, J. \& Haefliger, A. (1970). Plongements diff\\\'erentiables des vari\\\'et\\\'es orient\\\'ees de dimension 4 dans $\mathbb{R}^{7}$. In A. Haefliger \& R. Narasimhan (Eds.), Essays on Topology and Related Topics. Memoires d\\\'edi\\\'es \`a Georges de Rham. Springer-Verlag.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1970',
                    'title' => 'Plongements diff\\\'erentiables des vari\\\'et\\\'es orient\\\'ees de dimension 4 dans $\mathbb{R}^{7}$',
                    'author' => 'Bo\\\'echat, J. and Haefliger, A.',
                    'editor' => 'A. Haefliger and R. Narasimhan',
                    'booktitle' => 'Essays on Topology and Related Topics. Memoires d\\\'edi\\\'es \`a Georges de Rham',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],
            [
                'source' => 'Šváb, L., Gross, J., & Langová, J. (1972). Stuttering and social isolation. The Journal of Nervous and Mental Disease, 155, 1–5. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1972',
                    'pages' => '1-5',
                    'title' => 'Stuttering and social isolation',
                    'author' => '\v{S}v{\\\'a}b, L. and Gross, J. and Langov{\\\'a}, J.',
                    'volume' => '155',
                    'journal' => 'The Journal of Nervous and Mental Disease',
                    ]
            ],
            [
                'source' => 'American Speech-Language-Hearing Association (1999). Terminology pertaining to fluency and fluency disorders: Guidelines. ASHA, 41(Suppl. 19), 29–36.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1999',
                    'pages' => '29-36',
                    'title' => 'Terminology pertaining to fluency and fluency disorders: Guidelines',
                    'author' => 'American Speech-Language-Hearing Association',
                    'volume' => '41(Suppl. 19)',
                    'journal' => 'ASHA',
                    ]
            ],
            [
                'source' => 'Prabhat, P., Rombouts, E., & Borry, P. (2022). The disabling nature of hope in discovering a biological explanation of stuttering. Journal of Fluency Disorders, 72, Article 105906. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2022',
                    'title' => 'The disabling nature of hope in discovering a biological explanation of stuttering',
                    'author' => 'Prabhat, P. and Rombouts, E. and Borry, P.',
                    'volume' => '72',
                    'journal' => 'Journal of Fluency Disorders',
                    'note' => 'Article 105906',
                    ]
            ],
            [
                'source' => 'Tichenor, S., & Yaruss, J. S. (2018). A phenomenological analysis of the experience of stuttering. American Journal of Speech-Language Pathology, 27(3S), 1180–1194. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2018',
                    'pages' => '1180-1194',
                    'title' => 'A phenomenological analysis of the experience of stuttering',
                    'author' => 'Tichenor, S. and Yaruss, J. S.',
                    'number' => '3S',
                    'volume' => '27',
                    'journal' => 'American Journal of Speech-Language Pathology',
                    ]
            ],
            [
                'source' => 'Perkins, W. H. (1983). The problem of definition: Commentary on “stuttering.” Journal of Speech and Hearing Disorders, 48, 246–249. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1983',
                    'title' => 'The problem of definition: Commentary on ``stuttering.\'\'',
                    'author' => 'Perkins, W. H.',
                    'journal' => 'Journal of Speech and Hearing Disorders',
                    'volume' => '48',
                    'pages' => '246-249',
                    ]
            ],
            [
                'source' => 'Bloodstein, O. (1987). A handbook on stuttering (4th ed.). Chicago, IL: National Easter Seal Society. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'A handbook on stuttering',
                    'edition' => '4th',
                    'author' => 'Bloodstein, O.',
                    'address' => 'Chicago, IL',
                    'publisher' => 'National Easter Seal Society',
                    ]
            ],
            [
                'source' => 'Bloodstein, O. (1987). A handbook on stuttering, 4th ed. Chicago, IL: National Easter Seal Society. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1987',
                    'title' => 'A handbook on stuttering',
                    'edition' => '4th',
                    'author' => 'Bloodstein, O.',
                    'address' => 'Chicago, IL',
                    'publisher' => 'National Easter Seal Society',
                    ]
            ],
            [
                'source' => 'Ingham, R. J. (1990). Commentary on Perkins (1990) and Moore and Perkins (1990): On the valid role of reliability in identifying "what is stuttering?" Journal of Speech and Hearing Disorders, 55, 394–397. ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Journal of Speech and Hearing Disorders',
                    'volume' => '55',
                    'pages' => '394-397',
                    'year' => '1990',
                    'title' => 'Commentary on Perkins (1990) and Moore and Perkins (1990): On the valid role of reliability in identifying ``what is stuttering?\'\'',
                    'author' => 'Ingham, R. J.',
                    ]
            ],
            [
                'source' => 'World Health Organization. (1977). Manual of the international statistical classification of diseases, injuries, and causes of death (Vol. 1). Geneva: World Health Organization. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1977',
                    'volume' => '1',
                    'title' => 'Manual of the international statistical classification of diseases, injuries, and causes of death',
                    'author' => 'World Health Organization',
                    'publisher' => 'World Health Organization',
                    'address' => 'Geneva',
                    ]
            ],
            [
                'source' => 'World Health Organization. (2010). Stuttering (stammering). In International statistical classification of diseases and related health problems (10th Rev. ed.). Retrieved from http://apps.who.int/classifications/icd10/browse/2010/en#/F98.5',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2010',
                    'title' => 'Stuttering (stammering)',
                    'author' => 'World Health Organization',
                    'booktitle' => 'International statistical classification of diseases and related health problems (10th Rev. ed.)',
                    'url' => 'http://apps.who.int/classifications/icd10/browse/2010/en#/F98.5',
                    ]
            ],
            [
                'source' => ' Almudhi, A., Zafar, H., Anwer, S., & Alghadir, A. (2019). Effect of different body postures on the severity of stuttering in young adults with developmental stuttering. BioMed Research International, 2019, 1817906. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2019',
                    'title' => 'Effect of different body postures on the severity of stuttering in young adults with developmental stuttering',
                    'author' => 'Almudhi, A. and Zafar, H. and Anwer, S. and Alghadir, A.',
                    'note' => 'Article 1817906',
                    'volume' => '2019',
                    'journal' => 'BioMed Research International',
                    ]
            ],
            [
                'source' => ' Kikuchi, Y., Umezaki, T., Adachi, K., Sawatsubashi, M., Taura, M., Tsuchihashi, N., Yamaguchi, Y., Murakami, D., & Nakagawa. T. (2022). Employment quotas for adults who stutter: A preliminary study. International Archives of Communication Disorder, 4(1), Article 020. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2022',
                    'title' => 'Employment quotas for adults who stutter: A preliminary study',
                    'author' => 'Kikuchi, Y. and Umezaki, T. and Adachi, K. and Sawatsubashi, M. and Taura, M. and Tsuchihashi, N. and Yamaguchi, Y. and Murakami, D. and Nakagawa, T.',
                    'volume' => '4',
                    'number' => '1',
                    'journal' => 'International Archives of Communication Disorder',
                    'note' => 'Article 020',
                    ]
            ],
            [
                'source' => ' Sønsterud, H., Howells, K., & Ward, D. (2022). Covert and overt stuttering: concepts and comparative findings. Journal of Communication Disorders, Article 106246. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2022',
                    'title' => 'Covert and overt stuttering: concepts and comparative findings',
                    'author' => 'S{\o}nsterud, H. and Howells, K. and Ward, D.',
                    'note' => 'Article 106246',
                    'journal' => 'Journal of Communication Disorders',
                    ]
            ],
            [
                'source' => ' Treon, M., Dempster, L., & Blaesing, K. (2006). MMPI-2/A assessed personality differences in people who do, and do not, stutter. Social Behavior and Personality: An International Journal, 34, 271–294. ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Social Behavior and Personality: An International Journal',
                    'volume' => '34',
                    'pages' => '271-294',
                    'year' => '2006',
                    'title' => 'MMPI-2/A assessed personality differences in people who do, and do not, stutter',
                    'author' => 'Treon, M. and Dempster, L. and Blaesing, K.',
                    ]
            ],
            [
                'source' => ' St. Louis, K. O. (2020). Comparing and predicting public attitudes toward stuttering, obesity, and mental illness. American Journal of Speech-Language Pathology, 29, 2023–2038.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2020',
                    'pages' => '2023-2038',
                    'title' => 'Comparing and predicting public attitudes toward stuttering, obesity, and mental illness',
                    'author' => 'St. Louis, K. O.',
                    'volume' => '29',
                    'journal' => 'American Journal of Speech-Language Pathology',
                    ]
            ],
            [
                'source' => 'Tramontana, F., Gardini, L., Dieci, R. and Westerhoff, F., 2009. Global bifurcations in a three-dimensional financial model of bull and bear interactions. In: Gian Italo Bischi, Carl Chiarella and Laura Gardini, eds. Nonlinear Dynamics in Economics, Finance and the Social Sciences. Springer-Verlag, Heidelberg, pp. 333-352. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2009',
                    'pages' => '333-352',
                    'title' => 'Global bifurcations in a three-dimensional financial model of bull and bear interactions',
                    'author' => 'Tramontana, F. and Gardini, L. and Dieci, R. and Westerhoff, F.',
                    'editor' => 'Gian Italo Bischi and Carl Chiarella and Laura Gardini',
                    'booktitle' => 'Nonlinear Dynamics in Economics, Finance and the Social Sciences',
                    'publisher' => 'Springer-Verlag',
                    'address' => 'Heidelberg',
                    ]
            ],
            [
                'source' => 'Werner F. M. DeBondt and Richard H. Thaler. Financial decision making in markets and firms: A Behavioral perspective. In R. A. Jarrow, V. Maksimovic, W. T. Ziemba (eds.) \emph{Handbook in Operations Research and Management Science, Volume 9, Finance}. Elsevier, 1995. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1995',
                    'title' => 'Financial decision making in markets and firms: A Behavioral perspective',
                    'author' => 'Werner F. M. DeBondt and Richard H. Thaler',
                    'editor' => 'R. A. Jarrow and V. Maksimovic and W. T. Ziemba',
                    'booktitle' => 'Handbook in Operations Research and Management Science, Volume 9, Finance',
                    'publisher' => 'Elsevier',
                    ]
            ],
            [
                'source' => 'Benz, Anton, Gerhard Jaeger, and Robert van Rooij. (eds.) (2005),\ \textit{Game Theory and Pragmatics}, by Palgrave MacMillan. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2005',
                    'title' => 'Game Theory and Pragmatics',
                    'editor' => 'Benz, Anton and Gerhard Jaeger and Robert van Rooij',
                    'publisher' => 'Palgrave MacMillan',
                    ]
            ],
            [
                'source' => '\bibitem{} Bergemann, D. and S. Morris (2001). \textquotedblleft Robust Mechanism Design,\textquotedblright\ http://www.princeton.edu/\symbol{126}smorris/pdfs/robustmechanism2001.pdf. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'year' => '2001',
                    'title' => 'Robust Mechanism Design',
                    'author' => 'Bergemann, D. and S. Morris',
                    'url' => 'http://www.princeton.edu/\symbol{126}smorris/pdfs/robustmechanism2001.pdf',
                    ]
            ],
            [
                'source' => '\bibitem{ } Kagel, J. (1995), ``Auctions:  A Survey of Experimental Research," in {\it The Handbook of Experimental Economics}, J.H. Kagel and A.E. Roth Eds., Princeton University Press, New Jersey. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1995',
                    'title' => 'Auctions: A Survey of Experimental Research',
                    'author' => 'Kagel, J.',
                    'editor' => 'J. H. Kagel and A. E. Roth',
                    'address' => 'New Jersey',
                    'booktitle' => 'The Handbook of Experimental Economics',
                    'publisher' => 'Princeton University Press',
                    ]
            ],
            [
                'source' => '\noindent {\sc Mark Feldman \& Christian Gilles,} ``An Expository Note on Individual Risk  without Aggregate Uncertainty,\'\'  {\em Journal of Economic Theory} {\bf 35} (1985), 26-32. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1985',
                    'pages' => '26-32',
                    'title' => 'An Expository Note on Individual Risk without Aggregate Uncertainty',
                    'author' => 'Mark Feldman and Christian Gilles',
                    'volume' => '35',
                    'journal' => 'Journal of Economic Theory',
                    ]
            ],
            [
                'source' => 'Lipman, Barton L. and Duane J.Seppi (1995), Robust Inference in Communication Games with Partial Provability, \textit{Journal of Economic Theory, 66, 370-405.} ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1995',
                    'title' => 'Robust Inference in Communication Games with Partial Provability',
                    'author' => 'Lipman, Barton L. and Duane J. Seppi',
                    'journal' => 'Journal of Economic Theory',
                    'volume' => '66',
                    'pages' => '370-405',
                    ]
            ],
            [
                'source' => ' \bibitem{Conway} John B. Conway, {\em A Course in Functional Analysis}, Second Edition, volume 96 in {\em Graduate Texts in Mathematics}, Springer-Verlag, New York, 1990. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1990',
                    'title' => 'A Course in Functional Analysis',
                    'author' => 'John B. Conway',
                    'series' => 'Graduate Texts in Mathematics',
                    'volume' => '96',
                    'address' => 'New York',
                    'edition' => 'Second',
                    'publisher' => 'Springer-Verlag',
                    ]
            ],[
                'source' => 'R.F Wilson and J.R Cloutier. ``Optimal eigenstructure achievement with robustness guarantees,\'\'  in Proc. Amer. Control Conf., San Diego, CA, May 1990 ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1990',
                    'month' => 'May',
                    'title' => 'Optimal eigenstructure achievement with robustness guarantees',
                    'author' => 'R. F. Wilson and J. R. Cloutier',
                    'booktitle' => 'Proc. Amer. Control Conf., San Diego, CA, May 1990',
                    ]
            ],
            [
                'source' => 'R.F Wilson and J.R Cloutier. ``Generalized and robust eigenstructure assignment,\'\' in Proc.AIAA Missile Sci. Conf., Monterey, CA, Dec. 1990. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1990',
                    'month' => 'December',
                    'title' => 'Generalized and robust eigenstructure assignment',
                    'author' => 'R. F. Wilson and J. R. Cloutier',
                    'booktitle' => 'Proc. AIAA Missile Sci. Conf., Monterey, CA, Dec. 1990',
                    ]
            ],
            [
                'source' => 'A.N. Andry, E.Y. Sharpiro, and J.C. Chung. ``Eigenstructure assignment for linear systems,\'\' IEEE Trans.Aero.Elec.Syst., vol. AES-19, pp.711-729, Sept,1983. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1983',
                    'month' => 'September',
                    'pages' => '711-729',
                    'title' => 'Eigenstructure assignment for linear systems',
                    'author' => 'A. N. Andry and E. Y. Sharpiro and J. C. Chung',
                    'volume' => 'AES-19',
                    'journal' => 'IEEE Trans. Aero. Elec. Syst.',
                    ]
            ],
            [
                'source' => 'R.K. Cavin and S.P. Bhattacharyya. ``Robust and well-conditioned eigenstructure assignment via sylvester\'s equation .\'\' J.Opt. Cont., Appl.Meth., vol.4 no.3, pp.205-212,1983. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1983',
                    'pages' => '205-212',
                    'title' => 'Robust and well-conditioned eigenstructure assignment via sylvester\'s equation',
                    'author' => 'R. K. Cavin and S. P. Bhattacharyya',
                    'number' => '3',
                    'volume' => '4',
                    'journal' => 'J. Opt. Cont., Appl. Meth.',
                    ]
            ],
            [
                'source' => 'M.M. Fahmy and J. O\'Reilly. ``Eigenstructure assignment in linear multivariable systems-A parametric solution,\'\'in Proc. 21st  IEEE Conf. Decision and Control, Orlando, FL. Pp.1308-1311,1982. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1982',
                    'pages' => '1308-1311',
                    'title' => 'Eigenstructure assignment in linear multivariable systems-A parametric solution',
                    'author' => 'M. M. Fahmy and J. O\'Reilly',
                    'booktitle' => 'Proc. 21st IEEE Conf. Decision and Control, Orlando, FL',
                    ]
            ],
            [
                'source' => 'K.E. Simonyi and N.K. Loh . ``Robust constrained eigensystem assignment,\'\' in Proc. Amer. Cont. Conf., Pittsburgh, PA, June 1989. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1989',
                    'month' => 'June',
                    'title' => 'Robust constrained eigensystem assignment',
                    'author' => 'K. E. Simonyi and N. K. Loh',
                    'booktitle' => 'Proc. Amer. Cont. Conf., Pittsburgh, PA, June 1989',
                    ]
            ],
            [
                'source' => '\bibitem[Gustafson(2010)]{6} R. J. Gustafson, B. C. White, M. J. Fidler, A. C. Muscatello, Demonstrating the Solar Carbothermal Reduction of Lunar Regolith to Produce Oxygen, 48th AIAA Aerospace Sciences Meeting Including the New Horizons Forum and Aerospace Exposition (2010) 4-12.',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '2010',
                    'pages' => '4-12',
                    'title' => 'Demonstrating the Solar Carbothermal Reduction of Lunar Regolith to Produce Oxygen',
                    'author' => 'R. J. Gustafson and B. C. White and M. J. Fidler and A. C. Muscatello',
                    'booktitle' => '48th AIAA Aerospace Sciences Meeting Including the New Horizons Forum and Aerospace Exposition',
                ]
            ],
            [
                'source' => 'K.M. Sobel and W.Yu. ``Flight control application of eigenstructure assignment with optimization of robustness to structure state space uncertainty,\'\' in Proc. 28th IEEE Conf. Decision and Control, Tampa,FL, pp. 1705-1707, 1989. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1989',
                    'pages' => '1705-1707',
                    'title' => 'Flight control application of eigenstructure assignment with optimization of robustness to structure state space uncertainty',
                    'author' => 'K. M. Sobel and W. Yu',
                    'booktitle' => 'Proc. 28th IEEE Conf. Decision and Control, Tampa, FL',
                    ]
            ],
            [
                'source' => 'S.Garg. ``Robust eigenspace assignment using singular value sensitivies,\'\' ,\'\' J. Guid. Cont. Dyn., vol. 14 pp. 416-424, Mar.-Apr. 1991. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1991',
                    'month' => 'March-April',
                    'pages' => '416-424',
                    'title' => 'Robust eigenspace assignment using singular value sensitivies',
                    'author' => 'S. Garg',
                    'volume' => '14',
                    'journal' => 'J. Guid. Cont. Dyn.',
                    ]
            ],
            [
                'source' => 'E.Soroka and U.Shaked. ``On the robustness of LQ regulators ,\'\'IEEE Trans. Auto. Cont., vol. AC-29 pp.664-665, Jul 1984. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1984',
                    'month' => 'July',
                    'pages' => '664-665',
                    'title' => 'On the robustness of LQ regulators',
                    'author' => 'E. Soroka and U. Shaked',
                    'volume' => 'AC-29',
                    'journal' => 'IEEE Trans. Auto. Cont.',
                    ]
            ],
            [
                'source' => 'B.R. Barmish. ``Necessary and  sufficient conditions for quadratic stabilizability of uncertain linear systems,\'\' J. Optim. Theory Appl. 46 (1985) 399. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1985',
                    'title' => 'Necessary and sufficient conditions for quadratic stabilizability of uncertain linear systems',
                    'author' => 'B. R. Barmish',
                    'volume' => '46',
                    'pages' => '399',
                    'journal' => 'J. Optim. Theory Appl.',
                    ]
            ],
            [
                'source' => 'Barmish, B.R. and Wei, K.H.  ``Simultaneous Stabilizability of Single Input-Single Output Systems,\'\' Proceedings of 7th Int. Symp. On Math Theory of Networks and Systems, Stockholm, Sweden, 1985. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1985',
                    'title' => 'Simultaneous Stabilizability of Single Input-Single Output Systems',
                    'author' => 'Barmish, B. R. and Wei, K. H.',
                    'booktitle' => 'Proceedings of 7th Int. Symp. On Math Theory of Networks and Systems, Stockholm, Sweden, 1985',
                    ]
            ],
            [
                'source' => 'Soh, Y.C. and Evans, R.J. \'\'Robust Multivariable Regulator Design- General Case & Special Cases,\'\' Proc. of 1985 Conference on Decision & Control, Dec. 1985, pp. 1323-1332. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1985',
                    'month' => 'December',
                    'pages' => '1323-1332',
                    'title' => 'Robust Multivariable Regulator Design- General Case & Special Cases',
                    'booktitle' => 'Proc. of 1985 Conference on Decision & Control',
                    'author' => 'Soh, Y. C. and Evans, R. J.',
                    ]
            ],
            [
                'source' => 'M. Vidysagar, Nonlinear Systems Analysis (Prentice-Hall, Engelwood Cliffs, NJ, 1978). ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1978',
                    'title' => 'Nonlinear Systems Analysis',
                    'author' => 'M. Vidysagar',
                    'address' => 'Engelwood Cliffs, NJ',
                    'publisher' => 'Prentice-Hall',
                    ]
            ],
            [
                'source' => 'D.H. Jacobson, Extensions of Linear-Quadratic Control, Optimization and Matrix Theory (Academic Press, New York, 1977). ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1977',
                    'title' => 'Extensions of Linear-Quadratic Control, Optimization and Matrix Theory',
                    'author' => 'D. H. Jacobson',
                    'address' => 'New York',
                    'publisher' => 'Academic Press',
                    ]
            ],
            [
                'source' => 'J.C. Geromel, G. Garcia, and J. Bernussou. ``H^2 robust control with pole placement,\'\' in Proc. 12th World I.F.A.C. Congress, Sydney, Australia, 1993. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1993',
                    'title' => 'H^2 robust control with pole placement',
                    'author' => 'J. C. Geromel and G. Garcia and J. Bernussou',
                    'booktitle' => 'Proc. 12th World I. F. A. C. Congress, Sydney, Australia, 1993',
                    ]
            ],
            [
                'source' => 'W.M. Haddad, D.S. Bernstein. ``Controller design with regional pole constraints,\'\' IEEE Trans. Automat. Contr., vol. 37, no. 1, 1992. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1992',
                    'number' => '1',
                    'title' => 'Controller design with regional pole constraints',
                    'author' => 'W. M. Haddad and D. S. Bernstein',
                    'volume' => '37',
                    'journal' => 'IEEE Trans. Automat. Contr.',
                    ]
            ],[
                'source' => 'M. Vidyasagar, Nonlinear Systems Analysis. Englewood Cliffs, NJ  Prentice-Hall, 1978. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1978',
                    'title' => 'Nonlinear Systems Analysis',
                    'author' => 'M. Vidyasagar',
                    'address' => 'Englewood Cliffs, NJ',
                    'publisher' => 'Prentice-Hall',
                    ]
            ],
            [
                'source' => 'R.H. Martin, Jr., Nonlinear Operators and Differential Equations in Banach Space. New York  Wiley, 1976. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1976',
                    'address' => 'New York',
                    'publisher' =>  'Wiley',
                    'author' => 'Martin, Jr., R. H.',
                    'title' => 'Nonlinear Operators and Differential Equations in {B}anach Space',
                ],
                'use' => 'latex',
            ],
            [
                'source' => 'Giavoni, A., & Tamayo, Á. (2003). Spatials analysis: Concept, Method and Applicability/Análise Espacial: Conceito, Método e Aplicabilidade. Psicologia: Reflexão e Critica, 16(2), 303-307.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2003',
                    'pages' => '303-307',
                    'title' => 'Spatials analysis: Concept, Method and Applicability/An{\\\'a}lise Espacial: Conceito, M{\\\'e}todo e Aplicabilidade',
                    'author' => 'Giavoni, A. and Tamayo, {\\\'A}.',
                    'number' => '2',
                    'volume' => '16',
                    'journal' => 'Psicologia: Reflex{\\=a}o e Critica',
                    ]
            ],
            [
                'source' => 'Couldry, Nick. "On the Actual Street." In The Media and the Tourist Imagination: Converging Cultures, edited by David Crouch, Rhona Jackson, and Felix Thompson. London: Routledge, 2005. 60-75. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2005',
                    'pages' => '60-75',
                    'title' => 'On the Actual Street',
                    'author' => 'Couldry, Nick',
                    'editor' => 'David Crouch and Rhona Jackson and Felix Thompson',
                    'address' => 'London',
                    'booktitle' => 'The Media and the Tourist Imagination: Converging Cultures',
                    'publisher' => 'Routledge',
                    ]
            ],
            [
                'source' => 'Fukunishi Suzuki, Midori. "Women and Television: Portrayal of Women in Mass Media." In Japanese Women: New Feminist Perspectives on the Past, Present, and Future, edited by Kumiko Fujimura-Fanselow and Atsuko Kameda. New York: Feminist Press at the City University of New York, 1995. 75-92. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1995',
                    'pages' => '75-92',
                    'title' => 'Women and Television: Portrayal of Women in Mass Media',
                    'author' => 'Fukunishi Suzuki, Midori',
                    'editor' => 'Kumiko Fujimura-Fanselow and Atsuko Kameda',
                    'address' => 'New York',
                    'booktitle' => 'Japanese Women: New Feminist Perspectives on the Past, Present, and Future',
                    'publisher' => 'Feminist Press at the City University of New York',
                    ]
            ],
            [
                'source' => 'Hills, Matt. "Media Academics as Media Audiences." In Fandom: Identities and Communities in a Mediated World, edited by Jonathan Gray, Cornel Sandvoss, and C. Lee Harrington. New York: New York University Press, 2007. 33-47. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2007',
                    'pages' => '33-47',
                    'title' => 'Media Academics as Media Audiences',
                    'author' => 'Hills, Matt',
                    'address' => 'New York',
                    'booktitle' => 'Fandom: Identities and Communities in a Mediated World',
                    'editor' => 'Jonathan Gray and Cornel Sandvoss and C. Lee Harrington',
                    'publisher' => 'New York University Press',
                    ]
            ],
            [
                'source' => 'Hills, Matt. "Virtually out There: Strategies, Tactics, and Affective Spaces in On-line Fandom." Edited by Sally Munt. In Technospaces: inside the New Media. London: Continuum, 2001. 147-60.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2001',
                    'pages' => '147-60',
                    'title' => 'Virtually out There: Strategies, Tactics, and Affective Spaces in On-line Fandom',
                    'author' => 'Hills, Matt',
                    'editor' => 'Sally Munt',
                    'address' => 'London',
                    'booktitle' => 'Technospaces: inside the New Media',
                    'publisher' => 'Continuum',
                    ]
            ],
            [
                'source' => 'Sleep, N. H., 2012 Site Resonance from Strong Ground Motions at Lucerne, California, during the 1992 Landers Mainshock. Bulletin of the Seismological Society of America, Vol. 102, No. 4,  in press,  doi: 10.1785/0120110267.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2012',
                    'title' => 'Site Resonance from Strong Ground Motions at Lucerne, California, during the 1992 Landers Mainshock',
                    'author' => 'Sleep, N. H.',
                    'number' => '4',
                    'volume' => '102',
                    'journal' => 'Bulletin of the Seismological Society of America',
                    'note' => 'in press',
                    'doi' => '10.1785/0120110267',
                    ]
            ],
            [
                'source' => 'Sleep, N. H., 2012 Maintenance of permeable habitable  subsurface environments by earthquakes and tidal stresses, International Journal of Astrobiology, in press, doi:10.1017/S1473550412000122. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1017/S1473550412000122',
                    'year' => '2012',
                    'title' => 'Maintenance of permeable habitable subsurface environments by earthquakes and tidal stresses',
                    'author' => 'Sleep, N. H.',
                    'journal' => 'International Journal of Astrobiology',
                    'note' => 'in press',
                    ]
            ],
            [
                'source' => 'Sleep, N. H., 2012 Site Resonance from Strong Ground Motions at Lucerne, California, during the 1992 Landers Mainshock, Proceedings of the National Academy of Sciences of the United States of America, Vol. 109, Issue 1, pp. 59-62, Q12001, DOI: 10.1073/pnas.1118675109.  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1073/pnas.1118675109',
                    'year' => '2012',
                    'pages' => '59-62',
                    'title' => 'Site Resonance from Strong Ground Motions at Lucerne, California, during the 1992 Landers Mainshock',
                    'author' => 'Sleep, N. H.',
                    'number' => '1',
                    'volume' => '109',
                    'journal' => 'Proceedings of the National Academy of Sciences of the United States of America',
                    ]
            ],
            [
                'source' => '\bibitem{ColtonKress_InverseAcoustic} D. Colton, R. Kress {\em Inverse acoustic and electromagnetic scattering theory, second edition} Applied Mathematical Sciences vol 93. Springer. (1997)  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1997',
                    'title' => 'Inverse acoustic and electromagnetic scattering theory',
                    'edition' => 'second',
                    'series' => 'Applied Mathematical Sciences vol 93',
                    'author' => 'D. Colton and R. Kress',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => '1961 Arrow, K. J., L. Hurwicz, and H. Uzawa, "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191. ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Naval Research Logistics Quarterly',
                    'volume' => '8',
                    'pages' => '175-191',
                    'year' => '1961',
                    'title' => 'Constraint qualifications in maximization problems',
                    'author' => 'Arrow, K. J. and L. Hurwicz and H. Uzawa',
                    ]
            ],
            [
                'source' => '2013 *+ | Denolle, M., E. M. Dunham, G. A. Prieto, and G. C. Beroza, Ground motion prediction of realistic earthquake sources using the ambient seismic field, J. Geophys. Res., (in press). ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'in press',
                    'year' => '2013',
                    'title' => 'Ground motion prediction of realistic earthquake sources using the ambient seismic field',
                    'author' => 'Denolle, M. and E. M. Dunham and G. A. Prieto and G. C. Beroza',
                    'journal' => 'J. Geophys. Res.',
                    ]
            ],
            [
                'source' => '2013 *+ | Denolle, M., E. M. Dunham, G. A. Prieto, and G. C. Beroza, Ground motion prediction of realistic earthquake sources using the ambient seismic field, <em>J. Geophys. Res., </em>(in press). ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'in press',
                    'year' => '2013',
                    'title' => 'Ground motion prediction of realistic earthquake sources using the ambient seismic field',
                    'author' => 'Denolle, M. and E. M. Dunham and G. A. Prieto and G. C. Beroza',
                    'journal' => 'J. Geophys. Res.',
                    ]
            ],
            [
                'source' => '2008 *+ | Ma, S., and G. C. Beroza, Rupture dynamics on a bi-material interface for dipping faults, Bull. Seismol. Soc. Am., 98, p. 1642-1658; DOI: 10.1785/0120070201.  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1785/0120070201',
                    'year' => '2008',
                    'pages' => '1642-1658',
                    'title' => 'Rupture dynamics on a bi-material interface for dipping faults',
                    'author' => 'Ma, S. and G. C. Beroza',
                    'volume' => '98',
                    'journal' => 'Bull. Seismol. Soc. Am.',
                    ]
            ],
            [
                'source' => '2007 * | Mooney, W. D., G. C. Beroza, and R. Kind, Fault Zones from Top to Bottom: A Geophysical Perspective, in Tectonic Faults: Agents of Change on a Dynamic Earth, Mark R. Handy, Greg Hirth, and Niels Hovius ed., Berlin, Germany, ISBN-10:0-262-08362-0, 9-46. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2007',
                    'pages' => '9-46',
                    'isbn' => '0-262-08362-0',
                    'title' => 'Fault Zones from Top to Bottom: A Geophysical Perspective',
                    'author' => 'Mooney, W. D. and G. C. Beroza and R. Kind',
                    'editor' => 'Mark R. Handy and Greg Hirth and Niels Hovius',
                    'address' => 'Berlin, Germany',
                    'booktitle' => 'Tectonic Faults: Agents of Change on a Dynamic Earth',
                    ]
            ],
            [
                'source' => '2007 *+ | Ide, S., G. C. Beroza, D. R. Shelly, and T. Uchide, A scaling law for slow earthquakes, Nature, 447, 76-79, doi:10.1038/nature05780. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1038/nature05780',
                    'year' => '2007',
                    'pages' => '76-79',
                    'title' => 'A scaling law for slow earthquakes',
                    'author' => 'Ide, S. and G. C. Beroza and D. R. Shelly and T. Uchide',
                    'volume' => '447',
                    'journal' => 'Nature',
                    ]
            ], [
                'source' => '2007 Beroza, G. C., A man of magnitude: review of Richter\'s Scale: Measure of an Earthquake, Measure of a Man, by S. Hough, <em>Nature</em>, 445, 599; doi:10.1038/445599a. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1038/445599a',
                    'year' => '2007',
                    'pages' => '599',
                    'title' => 'A man of magnitude: review of Richter\'s Scale: Measure of an Earthquake, Measure of a Man, by S. Hough',
                    'author' => 'Beroza, G. C.',
                    'volume' => '445',
                    'journal' => 'Nature',
                    ]
            ],
            [
                'source' => '2009 *+ | Prieto, G. A., J. F. Lawrence, and G. C. Beroza, Anelastic Earth structure from the coherency of the Ambient seismic field, \textit{J. Geophys. Res.}, 114, B07202, doi:10.1029/2008JB006067 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1029/2008JB006067',
                    'year' => '2009',
                    'title' => 'Anelastic Earth structure from the coherency of the Ambient seismic field',
                    'author' => 'Prieto, G. A. and J. F. Lawrence and G. C. Beroza',
                    'volume' => '114',
                    'pages' => 'B07202',
                    'journal' => 'J. Geophys. Res.',
                    ]
            ],
            [
                'source' => 'Fischer, O., 2008. On analogy as the motivation for grammaticalization. Studies in Language,32(2), pp. 336-382. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2008',
                    'pages' => '336-382',
                    'title' => 'On analogy as the motivation for grammaticalization',
                    'author' => 'Fischer, O.',
                    'number' => '2',
                    'volume' => '32',
                    'journal' => 'Studies in Language',
                    ]
            ],
            [
                'source' => 'Hoffmann, S., 2005. Grammaticalization and English complex prepositions: A corpus-based study. London and New York: Routledge. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2005',
                    'title' => 'Grammaticalization and English complex prepositions: A corpus-based study',
                    'author' => 'Hoffmann, S.',
                    'address' => 'London and New York',
                    'publisher' => 'Routledge',
                    ]
            ],
            [
                'source' => 'Trask, R.L.,1996. Historical Linguistics. London: Arnold Van Bergem. ',
                'type' => 'book',
                'bibtex' => [
                    'address' => 'London',
                    'publisher' => 'Arnold Van Bergem',
                    'year' => '1996',
                    'title' => 'Historical Linguistics',
                    'author' => 'Trask, R. L.',
                    ]
            ],
            [
                'source' => 'Gomez, Luis O., and Hiram W. Woodward, Jr. (1981) Barabudur: History and Significance of a Buddhist Monument. Berkeley Buddhist Studies Series. Berkeley: Asian Humanities Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1981',
                    'title' => 'Barabudur: History and Significance of a Buddhist Monument',
                    'author' => 'Gomez, Luis O. and Woodward, Jr., Hiram W.',
                    'series' => 'Berkeley Buddhist Studies Series',
                    'address' => 'Berkeley',
                    'publisher' => 'Asian Humanities Press',
                    ]
            ],
            [
                'source' => 'Higham, Charles (1989) The Archaeology of Mainland Southeast Asia from 10,000 B.C. to the Fall of Angkor. Cambridge: Cambridge University Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1989',
                    'title' => 'The Archaeology of Mainland Southeast Asia from 10,000 B. C. to the Fall of Angkor',
                    'author' => 'Higham, Charles',
                    'address' => 'Cambridge',
                    'publisher' => 'Cambridge University Press',
                    ]
            ],
            [
                'source' => 'Sanderson, Alexis (2003-2004) "The Śaiva Religion among the Khmers." Bulletin de l\'École française d\'Extrême-Orient 90-91: 349-462. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2003-2004',
                    'pages' => '349-462',
                    'title' => 'The Śaiva Religion among the Khmers',
                    'author' => 'Sanderson, Alexis',
                    'volume' => '90-91',
                    'journal' => 'Bulletin de l\'{\\\'E}cole fran\c{c}aise d\'Extr{\^e}me-Orient',
                    ]
            ],
            [
                'source' => 'Wolters, O. W. (1999) History, culture, and region in Southeast Asian perspectives, rev. ed. Ithaca, N.Y.: Southeast Asia Program Publications, Cornell University. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1999',
                    'title' => 'History, culture, and region in Southeast Asian perspectives',
                    'author' => 'Wolters, O. W.',
                    'address' => 'Ithaca, N. Y.',
                    'edition' => 'rev',
                    'publisher' => 'Southeast Asia Program Publications, Cornell University',
                    ]
            ],
            [
                'source' => 'Boon, James A. (1990) Affinities and Extremes: Crisscrossing the Bittersweet Ethnology of East Indies History, Hindu-Balinese Culture, and Indo-European Allure. Chicago: University of Chicago Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1990',
                    'title' => 'Affinities and Extremes: Crisscrossing the Bittersweet Ethnology of East Indies History, Hindu-Balinese Culture, and Indo-European Allure',
                    'author' => 'Boon, James A.',
                    'address' => 'Chicago',
                    'publisher' => 'University of Chicago Press',
                    ]
            ],
            
            [
                'source' => 'Lubin, Timothy. 2005. “The Transmission, Patronage, and Prestige of Brahmanical Piety from the Mauryas to the Guptas.” In Federico Squarcini, ed., Boundaries, Dynamics and Construction of Traditions in South Asia, Firenze: Firenze University Press, 77-103.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2005',
                    'pages' => '77-103',
                    'title' => 'The Transmission, Patronage, and Prestige of Brahmanical Piety from the Mauryas to the Guptas',
                    'author' => 'Lubin, Timothy',
                    'booktitle' => 'Boundaries, Dynamics and Construction of Traditions in South Asia',
                    'address' => 'Firenze',
                    'editor' => 'Federico Squarcini',
                    'publisher' => 'Firenze University Press',
                    ]
            ],
            [
                'source' => 'Mendelsohn, Oliver. 1993. "The transformation of authority in rural India.” Modern Asian Studies 27: 805-42.',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1993',
                    'title' => 'The transformation of authority in rural India',
                    'journal' => 'Modern Asian Studies',
                    'volume' => '27',
                    'pages' => '805-42',
                    'author' => 'Mendelsohn, Oliver',
                    ]
            ],
            [
                'source' => 'Pollock, Sheldon. 2006. The Language of the Gods in the World of Men: Sanskrit, Culture, and Power in Premodern India. Berkeley: U. of California Press. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2006',
                    'title' => 'The Language of the Gods in the World of Men: Sanskrit, Culture, and Power in Premodern India',
                    'author' => 'Pollock, Sheldon',
                    'address' => 'Berkeley',
                    'publisher' => 'U. of California Press',
                    ]
            ],
            [
                'source' => 'Srivastava, S. K. 1963. “The Process of Desanskritization in Village India.” In Bala Ratnam, ed., Anthropology on the March. Madras, 266-270. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1963',
                    'pages' => '266-270',
                    'title' => 'The Process of Desanskritization in Village India',
                    'author' => 'Srivastava, S. K.',
                    'editor' => 'Bala Ratnam',
                    'booktitle' => 'Anthropology on the March',
                    'address' => 'Madras',
                    ]
            ],
            [
                'source' => 'Witzel, Michael. 1997. “Early Sanskritization: Origins and Development of the Kuru State.” In Recht, Staat und Verwaltung im klassischen Indien / The State, the Law, and Administration in Classical India, ed. by Bernhard Kölver, with E. Müller-Luckner. Munich: R. Oldenbourg Verlag, 29-52. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1997',
                    'pages' => '29-52',
                    'title' => 'Early Sanskritization: Origins and Development of the Kuru State',
                    'author' => 'Witzel, Michael',
                    'editor' => 'Bernhard K{\"o}lver and E. M{\"u}ller-Luckner',
                    'address' => 'Munich',
                    'booktitle' => 'Recht, Staat und Verwaltung im klassischen Indien / The State, the Law, and Administration in Classical India',
                    'publisher' => 'R. Oldenbourg Verlag',
                    ]
            ],
            [
                'source' => 'J. Conway and N. Sloane, Sphere Packings, Lattices and Groups, Springer, Berlin, 1993. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1993',
                    'title' => 'Sphere Packings, Lattices and Groups',
                    'author' => 'J. Conway and N. Sloane',
                    'address' => 'Berlin',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => 'G. Crippen, Distance geometry for realistic molecular conformations, in Distance Geometry: Theory, Methods, and Applications, A. Mucherino, C. Lavor, L. Liberti, and N. Maculan, eds., Springer, New York, 2013, pp. 315--328. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2013',
                    'pages' => '315-328',
                    'title' => 'Distance geometry for realistic molecular conformations',
                    'author' => 'G. Crippen',
                    'editor' => 'A. Mucherino and C. Lavor and L. Liberti and N. Maculan',
                    'address' => 'New York',
                    'booktitle' => 'Distance Geometry: Theory, Methods, and Applications',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => 'R. Diestel, Graph Theory, Springer, New York, 2005. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2005',
                    'title' => 'Graph Theory',
                    'author' => 'R. Diestel',
                    'publisher' => 'Springer',
                    'address' => 'New York',
                    ]
            ],
            [
                'source' => 'P. Krishnaiah and L. Kanal, eds., Theory of Multidimensional Scaling, Vol. 2, North-Holland, 1982. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '1982',
                    'title' => 'Theory of Multidimensional Scaling',
                    'editor' => 'P. Krishnaiah and L. Kanal',
                    'volume' => '2',
                    'publisher' => 'North-Holland',
                    ]
            ],
            [
                'source' => 'M. Laurent, Matrix completion problems, in Encyclopedia of Optimization, 2nd ed., C. Floudas and P. Pardalos, eds., Springer, New York, 2009, pp. 1967--1975. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2009',
                    'pages' => '1967-1975',
                    'title' => 'Matrix completion problems',
                    'author' => 'M. Laurent',
                    'editor' => 'C. Floudas and P. Pardalos',
                    'address' => 'New York',
                    'booktitle' => 'Encyclopedia of Optimization, 2nd ed.',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => 'A. Mucherino, C. Lavor, L. Liberti, and N. Maculan, eds., Distance Geometry: Theory, Methods, and Applications, Springer, New York, 2013. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2013',
                    'title' => 'Distance Geometry: Theory, Methods, and Applications',
                    'editor' => 'A. Mucherino and C. Lavor and L. Liberti and N. Maculan',
                    'address' => 'New York',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => 'J. Sylvester, Chemistry and algebra, Nature, 17 (1877), pp. 284--284.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1877',
                    'pages' => '284-284',
                    'title' => 'Chemistry and algebra',
                    'author' => 'J. Sylvester',
                    'volume' => '17',
                    'journal' => 'Nature',
                    ]
            ],
            [
                'source' => '\bibitem{Zi07} H.~Zimmer. \newblock {PDE}-based image compression using corner information. \newblock Master\'s thesis, Dept. of Computer Science, Saarland University, Saarbr\"ucken, Germany, 2007. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'year' => '2007',
                    'title' => '{PDE}-based image compression using corner information',
                    'author' => 'H. Zimmer',
                    'school' => 'Dept. of Computer Science, Saarland University, Saarbr\"ucken, Germany',
                    ]
            ],
            [
                'source' => '\bibitem{import} García, P. [2005]\'Caretta caretta (Tortuga boba) en las playas de Matalascañas y Castilla\'. {\it Boletín de la Asociación Herpetológica Española} ,  16 (1-2):28 ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2005',
                    'pages' => '28',
                    'title' => 'Caretta caretta (Tortuga boba) en las playas de Matalasca{\~n}as y Castilla',
                    'author' => 'Garc{\\\'\i}a, P.',
                    'volume' => '16',
                    'number' => '1-2',
                    'journal' => 'Bolet{\\\'\i}n de la Asociaci{\\\'o}n Herpetol{\\\'o}gica Espa{\~n}ola',
                    ]
            ],
            [
                'source' => '\bibitem{import} Barnestein, J. A. M. & González De La Vega, J.P.. [2007]\'Depredación de culebra de herradura, Hemorrhois hippocrepis , sobre sapillo pintojo ibérico, Discoglossus galganoi y sapillo pintojo meridional Discoglossus jeanneae \'. {\it Boletín de la Asociación Herpetológica Española} ,  18: 82-83 ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2007',
                    'pages' => '82-83',
                    'title' => 'Depredaci{\\\'o}n de culebra de herradura, Hemorrhois hippocrepis, sobre sapillo pintojo ib{\\\'e}rico, Discoglossus galganoi y sapillo pintojo meridional Discoglossus jeanneae',
                    'author' => 'Barnestein, J. A. M. and Gonz{\\\'a}lez De La Vega, J. P.',
                    'volume' => '18',
                    'journal' => 'Bolet{\\\'\i}n de la Asociaci{\\\'o}n Herpetol{\\\'o}gica Espa{\~n}ola',
                    ]
            ],
            [
                'source' => '\bibitem{import} González De La Vega, J.P.. & Toscano Díaz-Galiano, P.. [2015]\'Un caso de depredación sobre gallipato (Pleurodeles waltl) por parte de urraca (Pica pica) en Sierra Morena, Córdoba (Andalucía, Sur de España)\'. {\it Butlletí de la Societat Catalana d\'Herpetologia} , 22: 34-36 ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2015',
                    'pages' => '34-36',
                    'title' => 'Un caso de depredaci{\\\'o}n sobre gallipato (Pleurodeles waltl) por parte de urraca (Pica pica) en Sierra Morena, C{\\\'o}rdoba (Andaluc{\\\'\i}a, Sur de Espa{\~n}a)',
                    'author' => 'Gonz{\\\'a}lez De La Vega, J. P. and Toscano D{\\\'\i}az-Galiano, P.',
                    'volume' => '22',
                    'journal' => 'Butllet{\\\'\i} de la Societat Catalana d\'Herpetologia',
                    ]
            ],
            [
                'source' => '\bibitem{import} González De La Vega, J.P., Reposo-González, J. M. & Fernández-Carrasco, J.A. [1994]\'Primera cita de Hyla arborea (L.) en la provincia de Córdoba\'. {\it Actas del III Congreso Luso-Español, VII Congreso Español de Herpetología, Badajoz} ,  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '1994',
                    'title' => 'Primera cita de Hyla arborea (L.) en la provincia de C{\\\'o}rdoba',
                    'author' => 'Gonz{\\\'a}lez De La Vega, J. P. and Reposo-Gonz{\\\'a}lez, J. M. and Fern{\\\'a}ndez-Carrasco, J. A.',
                    'booktitle' => 'Actas del III Congreso Luso-Espa{\~n}ol, VII Congreso Espa{\~n}ol de Herpetolog{\\\'\i}a, Badajoz',
                    ]
            ],
            [
                'source' => 'Jullien, Bruno, 2012, "B2B Two-sided Platforms." In Oxford Handbook of Digital Economics (M. Peitz and J. Waldfogel eds.). Oxford University Press, New York. ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '2012',
                    'title' => 'B2B Two-sided Platforms',
                    'author' => 'Jullien, Bruno',
                    'editor' => 'M. Peitz and J. Waldfogel',
                    'address' => 'New York',
                    'booktitle' => 'Oxford Handbook of Digital Economics',
                    'publisher' => 'Oxford University Press',
                    ]
            ],
            [
                'source' => '\bibitem{PMP-PUB-2957}Baker, E. A. G., J. L. Wegrzyn, U. U. Sezen, T. Falk, P. E. Maloney, D. R. Vogler, C. Jensen, J. Mitton, J. Wright, B. Knaus, H. Rai, R. Cronn, D. Gonzalez-Ibeas, H. A. Vasquez-Gross, R. A. Famula, J.-J. Liu, L. M. Kueppers, and D. B. Neale. Comparative transcriptomics among four white pine species. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'title' => 'Comparative transcriptomics among four white pine species',
                    'author' => 'Baker, E. A. G. and J. L. Wegrzyn and U. U. Sezen and T. Falk and P. E. Maloney and D. R. Vogler and C. Jensen and J. Mitton and J. Wright and B. Knaus and H. Rai and R. Cronn and D. Gonzalez-Ibeas and H. A. Vasquez-Gross and R. A. Famula and J.-J. Liu and L. M. Kueppers and D. B. Neale',
                    ]
            ],
            [
                'source' => 'Pardo, Thiago, António Branco, Aldebaro Klautau, Renata Vieira and Vera Strube de Lima (eds.), 2010, Computational Processing of the Portuguese Language, Springer, Berlin. ',
                'type' => 'book',
                'bibtex' => [
                    'year' => '2010',
                    'title' => 'Computational Processing of the Portuguese Language',
                    'editor' => 'Pardo, Thiago and Ant{\\\'o}nio Branco and Aldebaro Klautau and Renata Vieira and Vera Strube de Lima',
                    'address' => 'Berlin',
                    'publisher' => 'Springer',
                    ]
            ],
            [
                'source' => ' \bibitem[{Adams et~al.(2014)Adams, Cherchye, {De Rock}, and   Verriest}]{Adams2014} Adams Abi, Cherchye Laurens, Bram De Rock, Verriest Ewout, 2014. Consume now or   later? time inconsistency, collective choice and revealed preference.   American Economic Review 104, 4147--4183. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2014',
                    'pages' => '4147-4183',
                    'title' => 'Consume now or later? time inconsistency, collective choice and revealed preference',
                    'author' => 'Adams Abi and Cherchye Laurens and Bram De Rock and Verriest Ewout',
                    'volume' => '104',
                    'journal' => 'American Economic Review',
                    ]
            ],
            [
                'source' => 'Woodruff, A. R. \emph{et al.} State-dependent function of neocortical chandelier cells. \emph{J. Neurosci.} \textbf{31,} 17872--17886 (2011). ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2011',
                    'pages' => '17872-17886',
                    'title' => 'State-dependent function of neocortical chandelier cells',
                    'author' => 'Woodruff, A. R. and others',
                    'volume' => '31',
                    'journal' => 'J. Neurosci.',
                    ]
            ],
            [
                'source' => '\bibitem{2002_hromkovic} J. Hromkovi{\v c}, S. Seibert, J. Karhum{\" a}ki, H. Klauck, and G. Schnitger, ``Communication Complexity Method for Measuring Nondeterminism in Finite Automata.\'\' \textit{Inform. Comput.} 172(2), pp. 202--217, (2002). ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2002',
                    'pages' => '202-217',
                    'title' => 'Communication Complexity Method for Measuring Nondeterminism in Finite Automata',
                    'author' => 'J. Hromkovi{\v c} and S. Seibert and J. Karhum{\" a}ki and H. Klauck and G. Schnitger',
                    'number' => '2',
                    'volume' => '172',
                    'journal' => 'Inform. Comput.',
                    ]
            ],
            [
                'source' => '\bibitem{K2-108} Bleiler, S. A. \& Scharlemann, M. G. (1986). Tangles, property $P$ and a problem of J. Martin, Math. Ann. Vol. 273, 215-225.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1986',
                    'pages' => '215-225',
                    'title' => 'Tangles, property $P$ and a problem of J. Martin',
                    'author' => 'Bleiler, S. A. and Scharlemann, M. G.',
                    'volume' => '273',
                    'journal' => 'Math. Ann.',
                    ]
            ],
            [
                'source' => '\bibitem{K2-172} Canary, R. D. (1994). Covering theorems for hyperbolic 3-manifolds. In K. Johannson (Ed.), Low-Dimensional Topology, Knoxville, Vol. 1992, pp. 21-30. International Press.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'year' => '1994',
                    'pages' => '21-30',
                    'title' => 'Covering theorems for hyperbolic 3-manifolds',
                    'author' => 'Canary, R. D.',
                    'editor' => 'K. Johannson',
                    'volume' => '1992',
                    'booktitle' => 'Low-Dimensional Topology, Knoxville',
                    'publisher' => 'International Press',
                    ]
            ],
            [
                'source' => '\bibitem{K2-11} Ahlfors, L. (1966). Fundamental polyhedrons and limit sets of Kleinian groups. Proc. Nat. Acad. Sci. U.S.A. Vol. 55, 251-254.  ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1966',
                    'pages' => '251-254',
                    'title' => 'Fundamental polyhedrons and limit sets of {K}leinian groups',
                    'author' => 'Ahlfors, L.',
                    'volume' => '55',
                    'journal' => 'Proc. Nat. Acad. Sci. U. S. A.',
                ],
                'use' => 'latex',
            ],
            [
                'source' => 'A. V. Knyazev, {\em Toward the optimal preconditioned eigensolver: Locally optimal block preconditioned conjugated gradient method}, SIAM J. Sci. Comput.,{\bf 23} (2001), pp. 517-541. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '2001',
                    'pages' => '517-541',
                    'volume' => '23',
                    'title' => 'Toward the optimal preconditioned eigensolver: Locally optimal block preconditioned conjugated gradient method',
                    'author' => 'A. V. Knyazev',
                    'journal' => 'SIAM J. Sci. Comput.',
                    ]
            ],
            [
                'source' => 'G. L. G. Sleijpen and H.A. van der Vorst, {\em A Jacobi-Davidson iteration method for linear eigenvalue problems}, SIAM J. Matrix Anal. Appl.,{\bf 17}(1996), pp. 401-425. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1996',
                    'pages' => '401-425',
                    'volume' => '17',
                    'title' => 'A Jacobi-Davidson iteration method for linear eigenvalue problems',
                    'author' => 'G. L. G. Sleijpen and H. A. van der Vorst',
                    'journal' => 'SIAM J. Matrix Anal. Appl.',
                    ]
            ],
            [
                'source' => 'Aerospace.org. 2023. Brief history of GPS. El Segundo, Ca: Aerospace Corporation. Accessed 11 March 2024 at https://aerospace.org/article/brief-history-gps ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://aerospace.org/article/brief-history-gps',
                    'urldate' => '11 March 2024',
                    'author' => 'Aerospace.org',
                    'year' => '2023',
                    'title' => 'Brief history of GPS',
                    'note' => 'El Segundo, Ca: Aerospace Corporation',
                    ]
            ],
            [
                'source' => 'Allen, D.E., Singh, B.P. and Dalal, R.C. 2011. Soil health indicators under climate change: a review of current knowledge. p. 25-45. In: Soil Health and Climate Change (B. Singh, A. Cowie, K. Chan, eds). Berlin: Springer. https://doi.org/10.1007/978-3-642-20256-8_2  ',
                'type' => 'incollection',
                'bibtex' => [
                    'doi' => '10.1007/978-3-642-20256-8_2',
                    'author' => 'Allen, D. E. and Singh, B. P. and Dalal, R. C.',
                    'year' => '2011',
                    'title' => 'Soil health indicators under climate change: a review of current knowledge',
                    'pages' => '25-45',
                    'publisher' => 'Springer',
                    'address' => 'Berlin',
                    'booktitle' => 'Soil Health and Climate Change',
                    'editor' => 'B. Singh and A. Cowie and K. Chan',
                ],
                'use' => 'zotero-word',
            ],
            [
                'source' => 'Barrile, V., Simonetti, S., Citroni, R., Fotia, A., and Bilotta, G. 2022. Experimenting agriculture 4.0 with sensors: A data fusion approach between remote sensing, UAVs and self-driving tractors. Sensors 22(20): 7910. https://doi.org/10.3390/s22207910  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.3390/s22207910',
                    'author' => 'Barrile, V. and Simonetti, S. and Citroni, R. and Fotia, A. and Bilotta, G.',
                    'year' => '2022',
                    'title' => 'Experimenting agriculture 4.0 with sensors: A data fusion approach between remote sensing, UAVs and self-driving tractors',
                    'journal' => 'Sensors',
                    'number' => '20',
                    'volume' => '22',
                    'pages' => '7910',
                    ]
            ],
            [
                'source' => 'Otto Nathan, Heinz Norden, [ed.]. Einstein on Peace.Schocken Books, New York, 1960. ',
                'type' => 'book',
                'bibtex' => [
                    'editor' => 'Otto Nathan and Heinz Norden',
                    'title' => 'Einstein on Peace',
                    'year' => '1960',
                    'address' => 'New York',
                    'publisher' => 'Schocken Books',
                    ]
            ],
            [
                'source' => '\bibitem{} Ahmed, Fahad. 2022. ``Syrian refugee children in Turkey and coronavirus disease 2019: A close-up view.’’ \textit{Journal of Global Health} 12. \url{https://www.ncbi.nlm.nih.gov/pmc/articles/PMC8889378/}. ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://www.ncbi.nlm.nih.gov/pmc/articles/PMC8889378/',
                    'author' => 'Ahmed, Fahad',
                    'year' => '2022',
                    'title' => 'Syrian refugee children in Turkey and coronavirus disease 2019: A close-up view',
                    'journal' => 'Journal of Global Health',
                    'volume' => '12',
                    ]
            ],
            [
                'source' => 'Kajuth, F., & Schmidt, T. (2011). Seasonality in house prices. SSRN Working Paper 2785400 ',
                'type' => 'techreport',
                'bibtex' => [
                    'author' => 'Kajuth, F. and Schmidt, T.',
                    'title' => 'Seasonality in house prices',
                    'year' => '2011',
                    'number' => '2785400',
                    'institution' => 'SSRN',
                    'type' => 'Working Paper',
                    ]
            ],
            [
                'source' => 'Keiser J, Maltese MF, Erlanger TE, Bos R, Tanner M, et al. (2005) Effect of irrigated rice agriculture on Japanese encephalitis, including challenges and opportunities for integrated vector management. Acta Trop 95: 40–57 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Keiser, J. and Maltese, M. F. and Erlanger, T. E. and Bos, R. and Tanner, M. and others',
                    'title' => 'Effect of irrigated rice agriculture on Japanese encephalitis, including challenges and opportunities for integrated vector management',
                    'year' => '2005',
                    'journal' => 'Acta Trop',
                    'volume' => '95',
                    'pages' => '40-57',
                    ]
            ],
            [
                'source' => 'Singh AK, Kharya P, Agarwal V, Singh S, Singh NP, Jain PK, et al. Japanese encephalitis in Uttar Pradesh, India: A situational analysis. J Family Med Prim Care 2020;9:3716-21.). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Singh, A. K. and Kharya, P. and Agarwal, V. and Singh, S. and Singh, N. P. and Jain, P. K. and others',
                    'title' => 'Japanese encephalitis in Uttar Pradesh, India: A situational analysis',
                    'year' => '2020',
                    'journal' => 'J Family Med Prim Care',
                    'pages' => '3716-21',
                    'volume' => '9',
                    ]
            ],
            [
                'source' => 'Akomea-Frimpong, I., Tenakwah, E.S., Tenakwah, E.J. and Amponsah, M. (2022) Corporate governance and performance of pension funds in Ghana: A mixed-method study. International Journal of Financial Studies, 10(3), p.52. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Akomea-Frimpong, I. and Tenakwah, E. S. and Tenakwah, E. J. and Amponsah, M.',
                    'title' => 'Corporate governance and performance of pension funds in Ghana: A mixed-method study',
                    'journal' => 'International Journal of Financial Studies',
                    'year' => '2022',
                    'volume' => '10',
                    'number' => '3',
                    'pages' => '52',
                    ]
            ],
            [
                'source' => 'Dhirani, Lubna Luxmi, Noorain Mukhtiar, Bhawani Shankar Chowdhry, y Thomas Newe. 2023. «Ethical Dilemmas and Privacy Issues in Emerging Technologies: A Review.» Sensors 23, 1151. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dhirani, Lubna Luxmi and Noorain Mukhtiar and Bhawani Shankar Chowdhry and Thomas Newe',
                    'title' => 'Ethical Dilemmas and Privacy Issues in Emerging Technologies: A Review',
                    'journal' => 'Sensors',
                    'year' => '2023',
                    'volume' => '23',
                    'pages' => '1151',
                    ]
            ],
            [
                'source' => 'Friedman, Batya, Peter Kahn, y Alan Borning. 2006. «Value Sensitive Design and Information Systems.» En Human-Computer Interaction in Management Information Systems: Foundations, P. Zhang y D. Galletta (Eds.), 1-27. New York: M.E. Sharpe, Inc:. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Friedman, Batya and Peter Kahn and Alan Borning',
                    'title' => 'Value Sensitive Design and Information Systems',
                    'year' => '2006',
                    'pages' => '1-27',
                    'booktitle' => 'Human-Computer Interaction in Management Information Systems: Foundations',
                    'editor' => 'P. Zhang and D. Galletta',
                    'publisher' => 'M. E. Sharpe, Inc',
                    'address' => 'New York',
                    ]
            ],
            [
                'source' => 'Kruger, J., & Dunning, D. (2009). Unskilled and unaware of it: How difficulties in recognizing one\'s own incompetence lead to inflated self-assessments. In Advances in Experimental Social Psychology (Vol. 41, pp. 1–70). Academic Press. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Kruger, J. and Dunning, D.',
                    'year' => '2009',
                    'title' => 'Unskilled and unaware of it: How difficulties in recognizing one\'s own incompetence lead to inflated self-assessments',
                    'pages' => '1-70',
                    'volume' => '41',
                    'publisher' => 'Academic Press',
                    'booktitle' => 'Advances in Experimental Social Psychology',
                    ]
            ],
            [
                'source' => 'Breeding LC, Dixon DL. A bonded provisional fixed prosthesis to be worn after implant surgery. J Prosthet Dent 1995;74(1):114-16. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Breeding, L. C. and Dixon, D. L.',
                    'title' => 'A bonded provisional fixed prosthesis to be worn after implant surgery',
                    'year' => '1995',
                    'journal' => 'J Prosthet Dent',
                    'volume' => '74',
                    'number' => '1',
                    'pages' => '114-16',
                    ]
            ],
            [
                'source' => 'Berglin GM. A technique for fabricating a fixed provisional prosthesis on osseointegrated fixtures. J Prosthet Dent 1989;61(3):347-8. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Berglin, G. M.',
                    'title' => 'A technique for fabricating a fixed provisional prosthesis on osseointegrated fixtures',
                    'year' => '1989',
                    'journal' => 'J Prosthet Dent',
                    'volume' => '61',
                    'number' => '3',
                    'pages' => '347-8',
                    ]
            ],
            [
                'source' => 'Amazon. (n.d.). How Amazon uses CRM to improve customer service. Retrieved from https://www.salesforce.com/in/crm/what-is-crm/ ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Amazon',
                    'title' => 'How Amazon uses CRM to improve customer service',
                    'year' => 'n.d.',
                    'url' => 'https://www.salesforce.com/in/crm/what-is-crm/',
                    ]
            ],
            [
                'source' => 'Chen, Q., M. Tyrer, C. D. Hills et al. 2010. Immobilisation of Heavy Metal in Cement-Based Solidification/Stabilisation: A Review. Cement & Concrete Research 40, no. 5: 787–94. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Chen, Q. and M. Tyrer and C. D. Hills and others',
                    'title' => 'Immobilisation of Heavy Metal in Cement-Based Solidification/Stabilisation: A Review',
                    'year' => '2010',
                    'journal' => 'Cement & Concrete Research',
                    'volume' => '40',
                    'number' => '5',
                    'pages' => '787-94',
                    ]
            ],
            [
                'source' => 'Chen, S. J., Z. S. Xu, A. A. Khoreshok, H. B. Shao, and F. Feng. 2023. Surface Subsidence Laws of Footwall Coal Seam Mining of Normal Fault Under Different Overburden Strata. Journal of Shandong University of Science & Technology (Natural Science) 42, no. 01: 38–48. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Chen, S. J. and Z. S. Xu and A. A. Khoreshok and H. B. Shao and F. Feng',
                    'year' => '2023',
                    'title' => 'Surface Subsidence Laws of Footwall Coal Seam Mining of Normal Fault Under Different Overburden Strata',
                    'journal' => 'Journal of Shandong University of Science & Technology (Natural Science)',
                    'pages' => '38-48',
                    'volume' => '42',
                    'number' => '01',
                    ]
            ],
            [
                'source' => '\bibitem{fonik4} C. Webber, H. Patel, A. Cunningham, A. Fox, J. Vousden, A. Castles and L. Shapiro, “An experimental comparison of additional training in phoneme awareness, letter-sound knowledge and decoding for struggling beginner readers,”\emph{ British Journal of Educational Psychology,} vol. 94, pp. 282-305, Nov. 2023, doi: https://doi.org/10.1111/bjep.12641. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1111/bjep.12641',
                    'author' => 'C. Webber and H. Patel and A. Cunningham and A. Fox and J. Vousden and A. Castles and L. Shapiro',
                    'title' => 'An experimental comparison of additional training in phoneme awareness, letter-sound knowledge and decoding for struggling beginner readers',
                    'year' => '2023',
                    'month' => 'November',
                    'journal' => 'British Journal of Educational Psychology',
                    'pages' => '282-305',
                    'volume' => '94',
                    ]
            ],
            [
                'source' => '[8] X. Zuo et al., “Satellite constellation reconfiguration using surrogate-based optimization,” Journal of Aerospace Engineering, vol. 35, no. 4, 2022. doi:10.1061/(asce)as.1943-5525.0001438  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'X. Zuo and others',
                    'title' => 'Satellite constellation reconfiguration using surrogate-based optimization',
                    'journal' => 'Journal of Aerospace Engineering',
                    'year' => '2022',
                    'volume' => '35',
                    'number' => '4',
                    'doi' => '10.1061/(asce)as.1943-5525.0001438',
                    ]
            ],
            [
                'source' => '[1] K. Howard and A. Ah, "Large Constellations of Satellites: Mitigating Environmental and Other Effects", Government Accountability Office, Washington, DC, USA, GAO Report No. GAO-22-105166, 2022. ',
                'type' => 'techreport',
                'bibtex' => [
                    'author' => 'K. Howard and A. Ah',
                    'title' => 'Large Constellations of Satellites: Mitigating Environmental and Other Effects',
                    'year' => '2022',
                    'number' => 'GAO-22-105166',
                    'institution' => 'Government Accountability Office, Washington, DC, USA, GAO',
                    'type' => 'Report',
                    ]
            ],
            [
                'source' => '\bibitem{AM} Arteaga, J. R. B., and   Malakhaltsev, M. A. {\it A remark on Ricci flow on left invariant metrics.} \href{https://arxiv.org/abs/math/0507473}{	arXiv:math/0507473}. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'arXiv',
                    'eprint' => 'math/0507473',
                    'url' => 'https://arxiv.org/abs/math/0507473',
                    'author' => 'Arteaga, J. R. B. and Malakhaltsev, M. A.',
                    'title' => 'A remark on Ricci flow on left invariant metrics',
                    ]
            ],
            [
                'source' => '\bibitem{DelBarco} Del Barco, V., and San Martin, L. A. B. {\it De Rham 2-Cohomology of Real Flag Manifolds.} Symmetry, Integrability and Geometry: Methods and Applications (SIGMA) 15(051) (2019)',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Del Barco, V. and San Martin, L. A. B.',
                    'title' => 'De Rham 2-Cohomology of Real Flag Manifolds',
                    'year' => '2019',
                    'journal' => 'Symmetry, Integrability and Geometry: Methods and Applications (SIGMA)',
                    'volume' => '15',
                    'number' => '051',
                    ]
            ],
            [
                'source' => 'Alkhawaldeh, F. (2022). False textual information detection, a deep learning approach (Doctoral dissertation, University of York).  ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Alkhawaldeh, F.',
                    'year' => '2022',
                    'title' => 'False textual information detection, a deep learning approach',
                    'school' => 'University of York',
                    ]
            ],
            [
                'source' => '[7] Roussille, Hector, Önder Gürcan e Fabien Michel: Agr4bs: A generic multi-agent organizational model for blockchain systems. Big Data and Cognitive Computing, 6(1):1, 2021. 2 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Roussille, Hector and {\"O}nder G{\"u}rcan and Fabien Michel',
                    'title' => 'Agr4bs: A generic multi-agent organizational model for blockchain systems',
                    'year' => '2021',
                    'journal' => 'Big Data and Cognitive Computing',
                    'pages' => '1',
                    'volume' => '6',
                    'number' => '1',
                    ]
            ],
            [
                'source' => ' [41] Almeida, Patrícia Albieri de, Gisela Lobo BP Tartuce e Marina Muniz Rossa Nunes: Quais as razões para a baixa atratividade da docência por alunos do ensino médio? Psicologia Ensino & Formação, 5(2):103–121, 2014. 43 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Almeida, Patr{\\\'\i}cia Albieri de and Gisela Lobo B. P. Tartuce and Marina Muniz Rossa Nunes',
                    'title' => 'Quais as raz{\=o}es para a baixa atratividade da doc{\^e}ncia por alunos do ensino m{\\\'e}dio?',
                    'journal' => 'Psicologia Ensino & Forma\c{c}{\=a}o',
                    'volume' => '5',
                    'number' => '2',
                    'pages' => '103-121',
                    'year' => '2014',
                    ]
            ],
            [
                'source' => 'P. Palensky, D. Dietrich. "Demand Side Management: Demand Response, Intelligent Energy Systems, and Smart Loads". IEEE TRANSACTIONS ON INDUSTRIAL INFORMATICS, VOL. 7, NO. 3, AUGUST 2011 381. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'P. Palensky and D. Dietrich',
                    'title' => 'Demand Side Management: Demand Response, Intelligent Energy Systems, and Smart Loads',
                    'year' => '2011',
                    'journal' => 'IEEE TRANSACTIONS ON INDUSTRIAL INFORMATICS',
                    'volume' => '7',
                    'number' => '3',
                    'month' => 'August',
                    'pages' => '381',
                    ]
            ],
            [
                'source' => 'Abuljadayel, F., & Omar, A. A. (2022, December 12). Saudi Arabia Says $50 Billion Investments Agreed With China. Retrieved from Bloomberg.com Website: https://www.bloomberg.com/news/articles/2022-12-11/saudi-arabia-says-50-billion-investments-agreed-at-china-summit?leadSource=uverify',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.bloomberg.com/news/articles/2022-12-11/saudi-arabia-says-50-billion-investments-agreed-at-china-summit?leadSource=uverify',
                    'author' => 'Abuljadayel, F. and Omar, A. A.',
                    'title' => 'Saudi Arabia Says $50 Billion Investments Agreed With {C}hina',
                    'year' => '2022',
                    'month' => 'December',
                    'urldate' => '2022, December 12',
                    'date' => '2022-12-12',
                    'note' => 'Retrieved from Bloomberg.com Website',
                    ]
            ],
            [
                'source' => 'Acar, G., Eubank, C., Englehardt, S., Juarez, M., Narayanan, A., & Diaz, C. (2014, November). The web never forgets: Persistent tracking mechanisms in the wild. In Proceedings of the 2014 ACM SIGSAC Conference on Computer and Communications Security (pp. 674- 689). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Acar, G. and Eubank, C. and Englehardt, S. and Juarez, M. and Narayanan, A. and Diaz, C.',
                    'title' => 'The web never forgets: Persistent tracking mechanisms in the wild',
                    'year' => '2014',
                    'month' => 'November',
                    'pages' => '674-689',
                    'booktitle' => 'Proceedings of the 2014 ACM SIGSAC Conference on Computer and Communications Security',
                    ]
            ],
            [
                'source' => 'Ackerman, M.S. & Davis, D.T. (n.d.). Privacy and security issues in e-commerce. https://web.eecs.umich.edu/~ackerm/pub/03e05/EC-privacy.ackerman.pdf. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'Ackerman, M. S. and Davis, D. T.',
                    'year' => 'n.d.',
                    'title' => 'Privacy and security issues in e-commerce',
                    'url' => 'https://web.eecs.umich.edu/~ackerm/pub/03e05/EC-privacy.ackerman.pdf',
                    ]
            ],
            [
                'source' => 'Shvartzshnaider, Y., Balashankar, A., Patidar, V., Wies, T., & Subramanian, L. (2020). Beyond the text: Analysis of privacy statements through syntactic and semantic role labeling. arXiv preprint arXiv:2010.00678. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'arXiv',
                    'eprint' => '2010.00678',
                    'author' => 'Shvartzshnaider, Y. and Balashankar, A. and Patidar, V. and Wies, T. and Subramanian, L.',
                    'year' => '2020',
                    'title' => 'Beyond the text: Analysis of privacy statements through syntactic and semantic role labeling',
                    'note' => 'arXiv preprint',
                    ]
            ],
            [
                'source' => 'Honda, K., and S. Sekito. "Two Kinds of Martensite." Nature 121 (1928): 744. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Honda, K. and S. Sekito',
                    'title' => 'Two Kinds of Martensite',
                    'journal' => 'Nature',
                    'year' => '1928',
                    'volume' => '121',
                    'pages' => '744',
                    ]
            ],
            [
                'source' => 'Msolli, S., M. Bettaieb, and F. Abed-Meraim. "Modelling of Void Coalescence Initiation and Its Impact on the Prediction of Material Failure." AIP Conference Proceedings (2016). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Msolli, S. and M. Bettaieb and F. Abed-Meraim',
                    'title' => 'Modelling of Void Coalescence Initiation and Its Impact on the Prediction of Material Failure',
                    'year' => '2016',
                    'booktitle' => 'AIP Conference Proceedings',
                    ]
            ],
            [
                'source' => 'Kristian Østergaard: Die Antithese Physis/Nomos als rhetorische Problemstellung im Dialog Gorgias. In: Classica et Mediaevalia 50, 1999, 81–96. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kristian {\O}stergaard',
                    'title' => 'Die Antithese Physis/Nomos als rhetorische Problemstellung im Dialog Gorgias',
                    'year' => '1999',
                    'journal' => 'Classica et Mediaevalia',
                    'pages' => '81-96',
                    'volume' => '50',
                    ]
            ],
            [
                'source' => 'Casella, A., & Mortari, V. (1950/2007). La técnica de la orquesta contemporánea. Buenos Aires: Ricordi. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Casella, A. and Mortari, V.',
                    'title' => 'La t{\\\'e}cnica de la orquesta contempor{\\\'a}nea',
                    'address' => 'Buenos Aires',
                    'publisher' => 'Ricordi',
                    'year' => '1950/2007',
                    ]
            ],
            [
                'source' => 'Wang, Y., Skerry-Ryan, R. J., Stanton, D., Wu, Y., Weiss, R. J., Jaitly, N., & Bengio, S. (2017). Tacotron: Towards end-to-end speech synthesis. arXiv preprint arXiv:1703.10135. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'arXiv',
                    'eprint' => '1703.10135',
                    'author' => 'Wang, Y. and Skerry-Ryan, R. J. and Stanton, D. and Wu, Y. and Weiss, R. J. and Jaitly, N. and Bengio, S.',
                    'year' => '2017',
                    'title' => 'Tacotron: Towards end-to-end speech synthesis',
                    'note' => 'arXiv preprint',
                    ]
            ],
            [
                'source' => 'van den Oord, A., Dieleman, S., Zen, H., Simonyan, K., Vinyals, O., Graves, A., & Kavukcuoglu, K. (2016). Wavenet: A generative model for raw audio. In 9th ISCA Speech Synthesis Workshop (pp. 125–125). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'van den Oord, A. and Dieleman, S. and Zen, H. and Simonyan, K. and Vinyals, O. and Graves, A. and Kavukcuoglu, K.',
                    'year' => '2016',
                    'title' => 'Wavenet: A generative model for raw audio',
                    'pages' => '125-125',
                    'booktitle' => '9th ISCA Speech Synthesis Workshop',
                    ]
            ],
            [
                'source' => 'Beckers, G. J., Suthers, R. A., & Ten Cate, C. (2003). Pure-tone birdsong by resonance filtering of harmonic overtones. Proceedings of the National Academy of Sciences, 100(12), 7372-7376. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Beckers, G. J. and Suthers, R. A. and Ten Cate, C.',
                    'title' => 'Pure-tone birdsong by resonance filtering of harmonic overtones',
                    'year' => '2003',
                    'journal' => 'Proceedings of the National Academy of Sciences',
                    'volume' => '100',
                    'number' => '12',
                    'pages' => '7372-7376',
                    ]
            ],
            [
                'source' => 'Billot, B., Greve, D. N., Puonti, O., Thielscher, A., Van Leemput, K., Fischl, B., . . . Iglesias, J. E. (2021). SynthSeg: Domain Randomisation for Segmentation of Brain MRI Scans of any Contrast and Resolution. arXiv:2108.09559. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'arXiv',
                    'eprint' => '2108.09559',
                    'author' => 'Billot, B. and Greve, D. N. and Puonti, O. and Thielscher, A. and Van Leemput, K. and Fischl, B. and others and Iglesias, J. E.',
                    'title' => 'SynthSeg: Domain Randomisation for Segmentation of Brain MRI Scans of any Contrast and Resolution',
                    'year' => '2021',
                    ]
            ],
            [
                'source' => '[1] Network Rail, “How Network Rail Buys Utlities,” [Online]. Available: https://safety.networkrail.co.uk/wp-content/uploads/2017/03/How-Network-Rail-buys-energy-v2-2017-03.docx. [Accessed 13 12 2023]. ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://safety.networkrail.co.uk/wp-content/uploads/2017/03/How-Network-Rail-buys-energy-v2-2017-03.docx',
                    'author' => 'Network Rail',
                    'title' => 'How Network Rail Buys Utlities',
                    'urldate' => '13 12 2023',
                    ]
            ],
            [   
                'source' => '\bibitem{mni1}Mazziotta, J., Toga, A., Evans, A., Fox, P., Lancaster, J., Zilles, K., ...\& Mazoyer, B. (2001). A four-dimensional probabilistic atlas of the human brain. \textit{Journal of the American Medical Informatics Association}, 8(5), 401-430. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Mazziotta, J. and Toga, A. and Evans, A. and Fox, P. and Lancaster, J. and Zilles, K. and others and Mazoyer, B.',
                    'title' => 'A four-dimensional probabilistic atlas of the human brain',
                    'year' => '2001',
                    'journal' => 'Journal of the American Medical Informatics Association',
                    'pages' => '401-430',
                    'volume' => '8',
                    'number' => '5',
                    ]
            ],
            [
                'source' => 'Dunaway S, Rothaus A, Zhang Y, Luisa Kadekaro A, Andl T, Andl CD. Divide and conquer: two stem cell populations in squamous epithelia, reserves and the active duty forces. Int J Oral Sci. 2019;11: 26. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dunaway, S. and Rothaus, A. and Zhang, Y. and Luisa Kadekaro, A. and Andl, T. and Andl, C. D.',
                    'title' => 'Divide and conquer: two stem cell populations in squamous epithelia, reserves and the active duty forces',
                    'journal' => 'Int J Oral Sci',
                    'year' => '2019',
                    'volume' => '11',
                    'pages' => '26',
                    ]
            ],
            [
                'source' => 'Alcolea MP, Jones PH. Cell competition: winning out by losing notch. Cell Cycle. 2015;14: 9–17. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Alcolea, M. P. and Jones, P. H.',
                    'title' => 'Cell competition: winning out by losing notch',
                    'journal' => 'Cell Cycle',
                    'year' => '2015',
                    'volume' => '14',
                    'pages' => '9-17',
                    ]
            ],
            [
                'source' => 'Andreatta M, Carmona SJ. UCell: Robust and scalable single-cell gene signature scoring. Comput Struct Biotechnol J. 2021;19: 3796–3798. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Andreatta, M. and Carmona, S. J.',
                    'title' => 'UCell: Robust and scalable single-cell gene signature scoring',
                    'year' => '2021',
                    'journal' => 'Comput Struct Biotechnol J',
                    'volume' => '19',
                    'pages' => '3796-3798',
                    ]
            ],
            [
                'source' => '45	Yoon, H. M., Lee, E. J., & Lim, K. H. (2018). Study on benzo (a) pyran content and its transfer ratio in extracts of medicinal herbs. Korean Chem. Eng. Res, 56, 832-840. . ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yoon, H. M. and Lee, E. J. and Lim, K. H.',
                    'year' => '2018',
                    'title' => 'Study on benzo (a) pyran content and its transfer ratio in extracts of medicinal herbs',
                    'journal' => 'Korean Chem. Eng. Res',
                    'volume' => '56',
                    'pages' => '832-840',
                    ]
            ],
            [
                'source' => '41	Wang, Y., Gou, Y., Zhang, L., Li, C., Wang, Z., Liu, Y., ... & Ma, S. (2022). Levels and health risk of pesticide residues in Chinese herbal medicines. Frontiers in pharmacology, 12, 3941.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Wang, Y. and Gou, Y. and Zhang, L. and Li, C. and Wang, Z. and Liu, Y. and others and Ma, S.',
                    'year' => '2022',
                    'title' => 'Levels and health risk of pesticide residues in Chinese herbal medicines',
                    'journal' => 'Frontiers in pharmacology',
                    'volume' => '12',
                    'pages' => '3941',
                    ]
            ],
            [
                'source' => '3.	Stavros V Konstantinides, Guy Meyer, Cecilia Becattini et al. 2019 ESC Guidelines for the diagnosis and management of acute pulmonary embolism developed in collaboration with the European Respiratory Society. Eur Heart J. 2020;41(4):543–603 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Stavros V. Konstantinides and Guy Meyer and Cecilia Becattini and others',
                    'title' => '2019 ESC Guidelines for the diagnosis and management of acute pulmonary embolism developed in collaboration with the European Respiratory Society',
                    'year' => '2020',
                    'journal' => 'Eur Heart J',
                    'volume' => '41',
                    'number' => '4',
                    'pages' => '543-603',
                    ]
            ],
            [
                'source' => '8.	Goldhaber SZ, Come PC, Lee RT, Braunwald E, Parker JA, Haire WD, Feldstein ML, Miller M, Toltzis R, Smith JL, Taveira da Silva AM, Mogtader A, McDonough TJ. Alteplase versus heparin in acute pulmonary embolism: randomised trial assessing right-ventricular function and pulmonary perfusion. Lancet 1993;341:507_511 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Goldhaber, S. Z. and Come, P. C. and Lee, R. T. and Braunwald, E. and Parker, J. A. and Haire, W. D. and Feldstein, M. L. and Miller, M. and Toltzis, R. and Smith, J. L. and Taveira da Silva, A. M. and Mogtader, A. and McDonough, T. J.',
                    'title' => 'Alteplase versus heparin in acute pulmonary embolism: randomised trial assessing right-ventricular function and pulmonary perfusion',
                    'year' => '1993',
                    'journal' => 'Lancet',
                    'pages' => '507-511',
                    'volume' => '341',
                    ]
            ],
            [
                'source' => 'Kumar, N., Gurumurthy, R. K., Prakash, P. G., Kurian, S. M., Wentland, C., Brinkmann, V., Mollenkopf, H.-J., Krammer, T., Toussaint, C., Saliba, A.-E., Biebl, M., Juergensen, C., Wiedenmann, B., Meyer, T. F., & Chumduri, C. (2021). Spatial organisation and homeostasis of epithelial lineages at the gastroesophageal junction is regulated by the divergent Wnt mucosal microenvironment. In bioRxiv (p. 2021.08.05.455222). https://doi.org/10.1101/2021.08.05.455222 ',
                'type' => 'unpublished',
                'bibtex' => [
                    'doi' => '10.1101/2021.08.05.455222',
                    'author' => 'Kumar, N. and Gurumurthy, R. K. and Prakash, P. G. and Kurian, S. M. and Wentland, C. and Brinkmann, V. and Mollenkopf, H.-J. and Krammer, T. and Toussaint, C. and Saliba, A.-E. and Biebl, M. and Juergensen, C. and Wiedenmann, B. and Meyer, T. F. and Chumduri, C.',
                    'year' => '2021',
                    'title' => 'Spatial organisation and homeostasis of epithelial lineages at the gastroesophageal junction is regulated by the divergent Wnt mucosal microenvironment',
                    'archiveprefix' => 'bioRxiv',
                    'eprint' => 'p. 2021.08.05.455222',
                    ]
            ],
            [
                'source' => 'Aichelin. (n.d.). LFP vs NMC batteries: Unveiling the differences for a sustainable future. Retrieved from Aichelin AT Industrial furnace solutions: https://www.aichelin.at/en/products/topics/lfp-vs-nmc-battery ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Aichelin',
                    'title' => 'LFP vs NMC batteries: Unveiling the differences for a sustainable future',
                    'year' => 'n.d.',
                    'url' => 'https://www.aichelin.at/en/products/topics/lfp-vs-nmc-battery',
                    'note' => 'Retrieved from Aichelin AT Industrial furnace solutions',
                    ]
            ],
            [
                'source' => 'Brakels, R. (2017). P-Type And N-Type Solar Cells’ Excellent Electron Adventure. Retrieved from Solar Quotes Blog: https://www.solarquotes.com.au/blog/p-type-and-n-type-solar-cells-excellent-electron-adventure/ ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Brakels, R.',
                    'title' => 'P-Type And N-Type Solar Cells\' Excellent Electron Adventure',
                    'year' => '2017',
                    'url' => 'https://www.solarquotes.com.au/blog/p-type-and-n-type-solar-cells-excellent-electron-adventure/',
                    'note' => 'Retrieved from Solar Quotes Blog',
                    ]
            ],
            [
                'source' => 'Pickerel, K. (2018, July 2). The difference between n-type and p-type solar cells. Retrieved from Solar Power World: https://www.solarpowerworldonline.com/2018/07/the-difference-between-n-type-and-p-type-solar-cells/ ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Pickerel, K.',
                    'title' => 'The difference between n-type and p-type solar cells',
                    'year' => '2018',
                    'month' => 'July',
                    'url' => 'https://www.solarpowerworldonline.com/2018/07/the-difference-between-n-type-and-p-type-solar-cells/',
                    'urldate' => '2018, July 2',
                    'date' => '2018-07-02',
                    'note' => 'Retrieved from Solar Power World',
                    ]
            ],
            [
                'source' => 'Reynolds, M. (2022, January 4). Gravity Could Solve Clean Energy’s One Major Drawback. Retrieved from https://www.wired.com/story/energy-vault-gravity-storage/ ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Reynolds, M.',
                    'title' => 'Gravity Could Solve Clean Energy\'s One Major Drawback',
                    'year' => '2022',
                    'month' => 'January',
                    'url' => 'https://www.wired.com/story/energy-vault-gravity-storage/',
                    'urldate' => '2022, January 4',
                    'date' => '2022-01-04'
                    ]
            ],
            [
                'source' => 'Gracyk, T., Rhythm and Noise: An Aesthetics of Rock, Durham: Duke University Press, 1996. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Gracyk, T.',
                    'title' => 'Rhythm and Noise: An Aesthetics of Rock',
                    'year' => '1996',
                    'address' => 'Durham',
                    'publisher' => 'Duke University Press',
                    ]
            ],
            [
                'source' => 'Karki, M. (2022) Deepfake and real images, Kaggle. Available at: https://www.kaggle.com/datasets/manjilkarki/deepfake-and-real-images (Accessed: 9 July 2023).  ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.kaggle.com/datasets/manjilkarki/deepfake-and-real-images',
                    'author' => 'Karki, M.',
                    'year' => '2022',
                    'title' => 'Deepfake and real images, Kaggle',
                    'urldate' => '9 July 2023',
                    ]
            ],
            [
                'source' => 'Bishop-Taylor, R., Nanson, R., Sagar, S., Lymburner, L. (2021). Mapping Australia\'s dynamic coastline at mean sea level using three decades of Landsat imagery. *Remote Sensing of Environment*, 267, 112734. https://doi.org/10.1016/j.rse.2021.112734  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.rse.2021.112734',
                    'author' => 'Bishop-Taylor, R. and Nanson, R. and Sagar, S. and Lymburner, L.',
                    'year' => '2021',
                    'title' => 'Mapping Australia\'s dynamic coastline at mean sea level using three decades of Landsat imagery',
                    'journal' => 'Remote Sensing of Environment',
                    'volume' => '267',
                    'pages' => '112734',
                    ]
            ],
            [
                'source' => 'Anilan, T., Satilmis, U., Kankal, M., & Yuksek, O. (2016). Application of Artificial Neural Networks and regression analysis to L-moments based regional frequency analysis in the Eastern Black Sea Basin, Turkey. KSCE Journal of Civil Engineering, 20, 2082-2092. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Anilan, T. and Satilmis, U. and Kankal, M. and Yuksek, O.',
                    'year' => '2016',
                    'title' => 'Application of Artificial Neural Networks and regression analysis to L-moments based regional frequency analysis in the Eastern Black Sea Basin, Turkey',
                    'journal' => 'KSCE Journal of Civil Engineering',
                    'volume' => '20',
                    'pages' => '2082-2092',
                    ]
            ],
            [
                'source' => 'Barnard, P. L., Erikson, L. H., Foxgrover, A. C., Hart, J. A. F., Limber, P., O’Neill, A. C., ... & Jones, J. M. (2019). Dynamic flood modeling essential to assess the coastal impacts of climate change. Scientific reports, 9(1), 4309. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Barnard, P. L. and Erikson, L. H. and Foxgrover, A. C. and Hart, J. A. F. and Limber, P. and O\'Neill, A. C. and others and Jones, J. M.',
                    'year' => '2019',
                    'title' => 'Dynamic flood modeling essential to assess the coastal impacts of climate change',
                    'journal' => 'Scientific reports',
                    'volume' => '9',
                    'number' => '1',
                    'pages' => '4309',
                    ]
            ],
            [
                'source' => 'Cai, W., Santoso, A., Collins, M., Dewitte, B., Karamperidou, C., Kug, J. S., ... & Zhong, W. (2021). Changing El Niño–Southern oscillation in a warming climate. Nature Reviews Earth & Environment, 2(9), 628-644. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Cai, W. and Santoso, A. and Collins, M. and Dewitte, B. and Karamperidou, C. and Kug, J. S. and others and Zhong, W.',
                    'year' => '2021',
                    'title' => 'Changing El Ni{\~n}o--Southern oscillation in a warming climate',
                    'journal' => 'Nature Reviews Earth & Environment',
                    'volume' => '2',
                    'number' => '9',
                    'pages' => '628-644',
                    ]
            ],
            [
                'source' => '\bibitem{Nash1962} J. Nash, Le problème de Cauchy pour les équations différentielles d’un fluide 	général, Bull. Soc. Math. France 90 (1962) 487–497. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Nash',
                    'title' => 'Le probl{\`e}me de Cauchy pour les {\\\'e}quations diff{\\\'e}rentielles d\'un fluide g{\\\'e}n{\\\'e}ral',
                    'year' => '1962',
                    'journal' => 'Bull. Soc. Math. France',
                    'volume' => '90',
                    'pages' => '487-497',
                    ]
            ],
            [
                'source' => '\bibitem{1}  Vandromme P, Schmitt FG, Souissi S, Buskey EJ, Strickler JR,  Wu C-H, Hwang JS. 2010  Symbolic analysis of plankton swimming trajectories: case study of {\em Strobilidium} sp (Protista) helical walking under various food conditions.  {\em Zool. Stud.} {\bf 49}, 289-303.  (https://archimer.ifremer.fr/doc/00070/18171/) ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://archimer.ifremer.fr/doc/00070/18171/',
                    'author' => 'Vandromme, P. and Schmitt, F. G. and Souissi, S. and Buskey, E. J. and Strickler, J. R. and Wu, C.-H. and Hwang, J. S.',
                    'year' => '2010',
                    'title' => 'Symbolic analysis of plankton swimming trajectories: case study of {\em Strobilidium} sp (Protista) helical walking under various food conditions',
                    'journal' => 'Zool. Stud.',
                    'pages' => '289-303',
                    'volume' => '49',
                    ]
            ],
            [
                'source' => '\bibitem{2}  Almeida PJ, Vieira MV, Kajin M, Forero-Medina G,  Cerqueira R.  2010  Indices of movement behaviour: conceptual background,  effects of scale and location errors. {\em Zoologia} (Curitiba) {\bf 27}, 674–680.  (doi.org/10.1590/S1984-46702010000500002) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Almeida, P. J. and Vieira, M. V. and Kajin, M. and Forero-Medina, G. and Cerqueira, R.',
                    'year' => '2010',
                    'title' => 'Indices of movement behaviour: conceptual background, effects of scale and location errors',
                    'journal' => 'Zoologia',
                    'pages' => '674-680',
                    'volume' => '27',
                    'doi' => '10.1590/S1984-46702010000500002',
                    ]
            ],
            [
                'source' => '\bibitem{rasher-2016} Lasley-Rasher RS, Nagel K, Angra A, Yen J.  2016 Intoxicated copepods: ingesting toxic phytoplankton leads to risky behaviour. {\em Proc. R. Soc. B} {\bf 283}, 20160176. (https://doi.org/10.1098/rspb.2016.0176) ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1098/rspb.2016.0176',
                    'author' => 'Lasley-Rasher, R. S. and Nagel, K. and Angra, A. and Yen, J.',
                    'year' => '2016',
                    'title' => 'Intoxicated copepods: ingesting toxic phytoplankton leads to risky behaviour',
                    'journal' => 'Proc. R. Soc. B',
                    'volume' => '283',
                    'note' => 'Article 20160176',
                    ]
            ],
            [
                'source' => '\bibitem{S27}  Schmitt FG, Seuront L, Hwang JS, Souissi S, Tseng LC. 2006  Scaling of swimming sequences in copepod behavior:  data analysis and simulation.  {\em Physica A} {\bf 364}, 287-296.  (doi.org/10.1016/j.physa.2005.09.04) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Schmitt, F. G. and Seuront, L. and Hwang, J. S. and Souissi, S. and Tseng, L. C.',
                    'year' => '2006',
                    'title' => 'Scaling of swimming sequences in copepod behavior: data analysis and simulation',
                    'journal' => 'Physica A',
                    'pages' => '287-296',
                    'volume' => '364',
                    'doi' => '10.1016/j.physa.2005.09.04',
                    ]
            ],
            [
                'source' => '\bibitem{S36}  Anderson TW, Goodman LA.  1957  Statistical inference about Markov chains.  {\em Ann. Math. Statist.} {\bf 28}, 89-110.  (http://www.jstor.org/stable/2237025) ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'http://www.jstor.org/stable/2237025',
                    'author' => 'Anderson, T. W. and Goodman, L. A.',
                    'year' => '1957',
                    'title' => 'Statistical inference about {M}arkov chains',
                    'journal' => 'Ann. Math. Statist.',
                    'pages' => '89-110',
                    'volume' => '28',
                ],
                'use' => 'latex',
            ],
            [
                'source' => 'Baade, A., Peng, P., and Harwath, D. Mae-ast: Masked autoencoding audio spectrogram transformer. arXiv, abs/2203.16691, 2022. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'Baade, A. and Peng, P. and Harwath, D.',
                    'title' => 'Mae-ast: Masked autoencoding audio spectrogram transformer',
                    'archiveprefix' => 'arXiv',
                    'eprint' => 'abs/2203.16691',
                    'year' => '2022',
                    ]
            ],
            [
                'source' => 'Ba, J. L., Kiros, J. R., and Hinton, G. E. Layer normalization. arXiv, abs/1607.06450, 2016. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'Ba, J. L. and Kiros, J. R. and Hinton, G. E.',
                    'title' => 'Layer normalization',
                    'year' => '2016',
                    'archiveprefix' => 'arXiv',
                    'eprint' => 'abs/1607.06450',
                    ]
            ],
            [
                'source' => '\item Bather, J.\ (1995), \textquotedblleft Response Adaptive Allocation and Selection Bias,\textquotedblright\ in:{\it \ Adaptive Designs, }eds.\ N. Flournoy and W.F. Rosenberger, Hayward, CA: Institute of Mathematical Statistics, pp.\ 23-35. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Bather, J.',
                    'year' => '1995',
                    'title' => 'Response Adaptive Allocation and Selection Bias',
                    'pages' => '23-35',
                    'booktitle' => 'Adaptive Designs',
                    'editor' => 'N. Flournoy and W. F. Rosenberger',
                    'publisher' => 'Institute of Mathematical Statistics',
                    'address' => 'Hayward, CA',
                    ]
            ],
            [
                'source' => '\item Bischoff, W. (2010), \textquotedblleft An Improvement in the Lack-of-Fit Optimality of the (Absolutely) Continuous Uniform Design in Respect of Exact Designs,\textquotedblright\ in\ {\it mODa 9 - Advances in Model-Oriented Design and Analysis}, eds. Giovagnoli, G., Atkinson, A. and Torsney, B. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Bischoff, W.',
                    'year' => '2010',
                    'title' => 'An Improvement in the Lack-of-Fit Optimality of the (Absolutely) Continuous Uniform Design in Respect of Exact Designs',
                    'booktitle' => 'mODa 9 - Advances in Model-Oriented Design and Analysis',
                    'editor' => 'Giovagnoli, G. and Atkinson, A. and Torsney, B.',
                    ]
            ],
            [
                'source' => '\item Herzberg, A. M., Prescott, P. and Akhtar, M. (1987); Equi-information robust designs: Which designs are possible?,"{\it The Canadian Journal of Statistics}, 15, 71-76. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Herzberg, A. M. and Prescott, P. and Akhtar, M.',
                    'year' => '1987',
                    'title' => 'Equi-information robust designs: Which designs are possible?',
                    'journal' => 'The Canadian Journal of Statistics',
                    'volume' => '15',
                    'pages' => '71-76',
                    ]
            ],
            [
                'source' => 'Afaneh, M. (2020, Avril 6). Bluetooth Addresses & Privacy in Bluetooth Low Energy. Consulté le Mars 21, 2023, sur NovelBits: https://novelbits.io/bluetooth-address-privacy-ble/ ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://novelbits.io/bluetooth-address-privacy-ble/',
                    'author' => 'Afaneh, M.',
                    'title' => 'Bluetooth Addresses & Privacy in Bluetooth Low Energy',
                    'year' => '2020',
                    'month' => 'avril',
                    'urldate' => 'Mars 21, 2023',
                    'date' => '2020-04-06',
                    'note' => 'Consulté le Mars 21, 2023, sur NovelBits',
                ],
                'language' => 'fr',
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'BlueZ Project. (2022, Novembre 14). BlueZ. Consulté le Mars 27, 2023, sur BlueZ: http://www.bluez.org/ ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'http://www.bluez.org/',
                    'author' => 'BlueZ Project',
                    'title' => 'BlueZ',
                    'year' => '2022',
                    'month' => 'novembre',
                    'urldate' => 'Mars 27, 2023',
                    'date' => '2022-11-14',
                    'note' => 'Consulté le Mars 27, 2023, sur BlueZ',
                ],
                'language' => 'fr',
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Osborn, Z. (2020, Juin 3). peer_to_peer_ble. Récupéré sur Github: https://github.com/keinix/peer_to_peer_ble ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://github.com/keinix/peer_to_peer_ble',
                    'urldate' => '2020, Juin 3',
                    'date' => '2020-06-03',
                    'author' => 'Osborn, Z.',
                    'title' => 'peer_to_peer_ble',
                    'year' => '2020',
                    'month' => 'juin',
                    'note' => 'Récupéré sur Github',
                ],
                'language' => 'fr',
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => '[11] D. E. Edmunds and W. D. Evans, “Preliminaries,” in Fractional Sobolev Spaces and Inequalities, Cambridge: Cambridge University Press, 2022, pp. 1–17 ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'D. E. Edmunds and W. D. Evans',
                    'title' => 'Preliminaries',
                    'year' => '2022',
                    'pages' => '1-17',
                    'address' => 'Cambridge',
                    'publisher' => 'Cambridge University Press',
                    'booktitle' => 'Fractional Sobolev Spaces and Inequalities',
                    ]
            ],
            [
                'source' => 'McKinsey, “What is Central Bank Digital Currency (CBDC)?”, 2023, https://www.mckinsey.com/featured-insights/mckinsey-explainers/what-is-central-bank-digital-currency-cbdc  ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'McKinsey',
                    'title' => 'What is Central Bank Digital Currency (CBDC)?',
                    'year' => '2023',
                    'url' => 'https://www.mckinsey.com/featured-insights/mckinsey-explainers/what-is-central-bank-digital-currency-cbdc',
                    ]
            ],
            [
                'source' => 'Oliver Wyman, “Four Visions for the Future of Digital Money”, 2023, https://www.oliverwymanforum.com/future-of-money/2023/may/four-visions-for-digital-money.html  ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Oliver Wyman',
                    'title' => 'Four Visions for the Future of Digital Money',
                    'year' => '2023',
                    'url' => 'https://www.oliverwymanforum.com/future-of-money/2023/may/four-visions-for-digital-money.html',
                    ]
            ],
            [
                'source' => 'Akdağ, Ahmet (2016), “Telhisü’l Miftâh’ın Beyân Bölümünün Mütercimi Bilinmeyen Bir Tercümesi”, Uluslararası Türkçe Edebiyat  Kültür Eğitim  Dergisi, V(3), s. 1243-1266. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Akdağ, Ahmet',
                    'year' => '2016',
                    'title' => 'Telhisü\'l Miftâh\'ın Beyân Bölümünün Mütercimi Bilinmeyen Bir Tercümesi',
                    'journal' => 'Uluslararası Türkçe Edebiyat Kültür Eğitim Dergisi',
                    'pages' => '1243-1266',
                    'volume' => 'V',
                    'number' => '3',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Center for Disease Control and Prevention. History of Ebola Disease Outbreaks. Cases and Outbreaks of Ebola Disease by Year. Estados Unidos: CDC, 2023. Disponível em: https://www.cdc.gov/vhf/ebola/history/chronology.html?CDC_AA_refVal=https',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Center for Disease Control and Prevention',
                    'title' => 'History of Ebola Disease Outbreaks. Cases and Outbreaks of Ebola Disease by Year',
                    'year' => '2023',
                    'url' => 'https://www.cdc.gov/vhf/ebola/history/chronology.html?CDC_AA_refVal=https',
                    'note' => 'Estados Unidos: CDC',
                ],
                'language' => 'pt',
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'FRIEDEN, Thomas R. et al. Ebola 2014—new challenges, new global response and responsibility. New England Journal of Medicine, v. 371, n. 13, p. 1177-1180, 2014. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Frieden, Thomas R. and others',
                    'title' => 'Ebola 2014---new challenges, new global response and responsibility',
                    'journal' => 'New England Journal of Medicine',
                    'year' => '2014',
                    'volume' => '371',
                    'number' => '13',
                    'pages' => '1177-1180',
                    ]
            ],
            [
                'source' => 'Center for Disease Control and Prevention. Marburg (Marburg Virus Disease). Estados Unidos: CDC, 2023. Disponível em: https://www.cdc.gov/vhf/marburg/index.html. Acesso em: 15 mar. 2023. ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Center for Disease Control and Prevention',
                    'title' => 'Marburg (Marburg Virus Disease)',
                    'year' => '2023',
                    'url' => 'https://www.cdc.gov/vhf/marburg/index.html',
                    'urldate' => '15 mar. 2023',
                    'note' => 'Estados Unidos: CDC',
                ],
                'language' => 'pt',
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Md. Rezaul Bashar , Mirza A.F.M. Rashidul Hasan , Md. Altab Hossain and Dipankar Das , 2004. Handwritten Bangla Numerical Digit Recognition using Histogram Technique. Asian Journal of Information Technology, 3: 611-615. URL: https://medwelljournals.com/abstract/?doi=ajit.2004.611.615  ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://medwelljournals.com/abstract/?doi=ajit.2004.611.615',
                    'author' => 'Md. Rezaul Bashar and Mirza A. F. M. Rashidul Hasan and Md. Altab Hossain and Dipankar Das',
                    'year' => '2004',
                    'title' => 'Handwritten Bangla Numerical Digit Recognition using Histogram Technique',
                    'journal' => 'Asian Journal of Information Technology',
                    'pages' => '611-615',
                    'volume' => '3',
                    ]
            ], 
            [
                'source' => 'Wang, Panqu & Chen, Pengfei & Yuan, Ye & Liu, Ding & Huang, Zehua & Hou, Xiaodi & Cottrell, Garrison. (2017). Understanding Convolution for Semantic Segmentation. https://doi.org/10.48550/arXiv.1702.08502 ',
                'type' => 'unpublished',
                'bibtex' => [
                    'doi' => '10.48550/arXiv.1702.08502',
                    'author' => 'Wang, Panqu and Chen, Pengfei and Yuan, Ye and Liu, Ding and Huang, Zehua and Hou, Xiaodi and Cottrell, Garrison',
                    'year' => '2017',
                    'title' => 'Understanding Convolution for Semantic Segmentation',
                    ]
            ],
            [
                'source' => 'Go-Ahead Group. Who We Are. [online] Available at: [https://www.go-ahead.com/who-we-are]. ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.go-ahead.com/who-we-are',
                    'author' => 'Go-Ahead Group',
                    'title' => 'Who We Are',
                    ]
            ],
            [
                'source' => 'Kjaer, A. M. (2018, December 27). State capture. Retrieved November 24, 2023, from Encyclopedia Britannica: https://www.britannica.com/topic/state-capture ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.britannica.com/topic/state-capture',
                    'month' => 'December',
                    'author' => 'Kjaer, A. M.',
                    'year' => '2018',
                    'title' => 'State capture',
                    'urldate' => 'November 24, 2023',
                    'date' => '2018-12-27',
                    'note' => 'Retrieved November 24, 2023, from Encyclopedia Britannica',
                    ]
            ],
            [
                'source' => '\bibitem{ref7} He, K., Sun, J., & Tang, X. (2010). Single image haze removal using dark channel prior. In IEEE transactions on pattern analysis and machine intelligence, 33(12), 2341-2353. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'He, K. and Sun, J. and Tang, X.',
                    'title' => 'Single image haze removal using dark channel prior',
                    'journal' => 'IEEE transactions on pattern analysis and machine intelligence',
                    'year' => '2010',
                    'volume' => '33',
                    'number' => '12',
                    'pages' => '2341-2353',
                    ]
            ],
            [
                'source' => '\bibitem{ref16} Hitam, M. S., Awalludin, E. A., Yussof, W. N. J. H. W., & Bachok, Z. (2013, January). Mixture contrast limited adaptive histogram equalization for underwater image enhancement. In 2013 International conference on computer applications technology (ICCAT) (pp. 1-5). IEEE. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Hitam, M. S. and Awalludin, E. A. and Yussof, W. N. J. H. W. and Bachok, Z.',
                    'title' => 'Mixture contrast limited adaptive histogram equalization for underwater image enhancement',
                    'year' => '2013',
                    'month' => 'January',
                    'pages' => '1-5',
                    'booktitle' => '2013 International conference on computer applications technology (ICCAT)',
                    'publisher' => 'IEEE',
                    ]
            ],
            [
                'source' => 'HOSTER HA, ZANES RP Jr, VON HAAM E. Studies in Hodgkin\'s syndrome; the association of viral hepatitis and Hodgkin\'s disease; a preliminary report. Cancer Res. 1949 Aug;9(8):473-80. PMID: 18134519. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Hoster, H. A. and Zanes, Jr, R. P. and Von Haam, E.',
                    'title' => 'Studies in Hodgkin\'s syndrome; the association of viral hepatitis and Hodgkin\'s disease; a preliminary report',
                    'year' => '1949',
                    'month' => 'August',
                    'journal' => 'Cancer Res.',
                    'pages' => '473-80',
                    'volume' => '9',
                    'number' => '8',
                    'note' => 'PMID: 18134519',
                    ]
            ],
            [
                'source' => 'GEORGIADES J, ZIELINSKI T, CICHOLSKA A et al. Research on the oncolytic effect of APC viruses in cancer of the cervix uteri; preliminary report. Biul Inst Med Morsk Gdansk. 1959;10:49-57. PMID: 13827367. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Georgiades, J. and Zielinski, T. and Cicholska, A. and others',
                    'title' => 'Research on the oncolytic effect of APC viruses in cancer of the cervix uteri; preliminary report',
                    'year' => '1959',
                    'journal' => 'Biul Inst Med Morsk Gdansk',
                    'pages' => '49-57',
                    'volume' => '10',
                    'note' => 'PMID: 13827367',
                    ]
            ],
            [
                'source' => 'Albetis, J., Duthoit, S., Guttler, F., Jacquin, A., Goulard, M., Poilvé, H., ... & Dedieu, G. (2017). Detection of Flavescence dorée grapevine disease using unmanned aerial vehicle (UAV) multispectral imagery. Remote Sensing, 9(4), 308. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Albetis, J. and Duthoit, S. and Guttler, F. and Jacquin, A. and Goulard, M. and Poilv{\\\'e}, H. and others and Dedieu, G.',
                    'year' => '2017',
                    'title' => 'Detection of Flavescence dor{\\\'e}e grapevine disease using unmanned aerial vehicle (UAV) multispectral imagery',
                    'journal' => 'Remote Sensing',
                    'volume' => '9',
                    'number' => '4',
                    'pages' => '308',
                    ]
            ],
            [
                'source' => 'Potena, C., Nardi, D., & Pretto, A. (2017). Fast and accurate crop and weed identification with summarized train sets for precision agriculture. In Intelligent Autonomous Systems 14: Proceedings of the 14th International Conference IAS-14 14 (pp. 105-121). Springer International Publishing. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Potena, C. and Nardi, D. and Pretto, A.',
                    'year' => '2017',
                    'title' => 'Fast and accurate crop and weed identification with summarized train sets for precision agriculture',
                    'pages' => '105-121',
                    'publisher' => 'Springer International Publishing',
                    'booktitle' => 'Intelligent Autonomous Systems 14: Proceedings of the 14th International Conference IAS-14 14',
                    ]
            ],
            [
                'source' => 'Ababneh S. Y., & Gurcan M. N., “An efficient graph-cut segmentation for knee bone osteoarthritis medical images”. 2010 IEEE International Conference on Electro/Information Technology. ©2010. [doi:10.1109/eit.2010.5612191]  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'year' => '2010',
                    'title' => 'An efficient graph-cut segmentation for knee bone osteoarthritis medical images',
                    'author' => 'Ababneh, S. Y. and Gurcan, M. N.',
                    'booktitle' => '2010 IEEE International Conference on Electro/Information Technology',
                    'doi' => '10.1109/eit.2010.5612191',
                    ]
            ],
            [
                'source' => 'Fisch   C. "William Withering: an account of foxglove and some of its medical uses 1785–1985", J Am Coll Cardiol. 1985:5(5):1A–2A. https://doi.org/10.1016/S0735-1097(85)80456-3 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/S0735-1097(85)80456-3',
                    'author' => 'Fisch, C.',
                    'title' => 'William Withering: an account of foxglove and some of its medical uses 1785--1985',
                    'year' => '1985',
                    'journal' => 'J Am Coll Cardiol',
                    'volume' => '5',
                    'number' => '5',
                    'pages' => '1A-2A',
                    ]
            ],
            [
                'source' => '[30]	K. C. Song, S. M. Lee, T. S. Park, and B. S. Lee, “Preparation of colloidal silver nanoparticles by chemical reduction method,” Korean Journal of Chemical Engineering, vol. 26, no. 1, pp. 153–155, Jan. 2009, doi: 10.1007/s11814-009-0024-y. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s11814-009-0024-y',
                    'author' => 'K. C. Song and S. M. Lee and T. S. Park and B. S. Lee',
                    'title' => 'Preparation of colloidal silver nanoparticles by chemical reduction method',
                    'journal' => 'Korean Journal of Chemical Engineering',
                    'year' => '2009',
                    'month' => 'January',
                    'volume' => '26',
                    'number' => '1',
                    'pages' => '153-155',
                    ]
            ],
            [
                'source' => '[58]	K. R. Raghupathi, R. T. Koodali, and A. C. Manna, “Size-dependent bacterial growth inhibition and mechanism of antibacterial activity of zinc oxide nanoparticles,” Langmuir, vol. 27, no. 7, pp. 4020–4028, Apr. 2011, doi: 10.1021/la104825u. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1021/la104825u',
                    'author' => 'K. R. Raghupathi and R. T. Koodali and A. C. Manna',
                    'title' => 'Size-dependent bacterial growth inhibition and mechanism of antibacterial activity of zinc oxide nanoparticles',
                    'journal' => 'Langmuir',
                    'year' => '2011',
                    'month' => 'April',
                    'volume' => '27',
                    'number' => '7',
                    'pages' => '4020-4028',
                    ]
            ],
            [
                'source' => 'Théry, C., Witwer, K. W., Aikawa, E., Alcaraz, M. J., Anderson, J. D., Andriantsitohaina, R., Antoniou, A., Arab, T., Archer, F., Atkin-Smith, G. K., Ayre, D. C., Bach, J. M., Bachurski, D., Baharvand, H., Balaj, L., Baldacchino, S., Bauer, N. N., Baxter, A. A., Bebawy, M., … Zuba-Surma, E. K. (2018). Minimal information for studies of extracellular vesicles 2018 (MISEV2018): a position statement of the International Society for Extracellular Vesicles and update of the MISEV2014 guidelines. Journal of Extracellular Vesicles, 7(1). https://doi.org/10.1080/20013078.2018.1535750 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1080/20013078.2018.1535750',
                    'author' => 'Th{\\\'e}ry, C. and Witwer, K. W. and Aikawa, E. and Alcaraz, M. J. and Anderson, J. D. and Andriantsitohaina, R. and Antoniou, A. and Arab, T. and Archer, F. and Atkin-Smith, G. K. and Ayre, D. C. and Bach, J. M. and Bachurski, D. and Baharvand, H. and Balaj, L. and Baldacchino, S. and Bauer, N. N. and Baxter, A. A. and Bebawy, M. and others and Zuba-Surma, E. K.',
                    'year' => '2018',
                    'title' => 'Minimal information for studies of extracellular vesicles 2018 (MISEV2018): a position statement of the International Society for Extracellular Vesicles and update of the MISEV2014 guidelines',
                    'journal' => 'Journal of Extracellular Vesicles',
                    'number' => '1',
                    'volume' => '7',
                    ]
            ],
            [
                'source' => 'Zhang, J., Li, S., Li, L., Li, M., Guo, C., Yao, J., & Mi, S. (2015). Exosome and exosomal microRNA: Trafficking, sorting, and function. In Genomics, Proteomics and Bioinformatics (Vol. 13, Issue 1, pp. 17–24). Beijing Genomics Institute. https://doi.org/10.1016/j.gpb.2015.02.001 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.gpb.2015.02.001',
                    'author' => 'Zhang, J. and Li, S. and Li, L. and Li, M. and Guo, C. and Yao, J. and Mi, S.',
                    'year' => '2015',
                    'title' => 'Exosome and exosomal microRNA: Trafficking, sorting, and function',
                    'journal' => 'Genomics, Proteomics and Bioinformatics',
                    'volume' => '13',
                    'number' => '1',
                    'pages' => '17-24',
                    ]
            ],
            [
                'source' => '\bibitem{ref12}	Yueqian Li and Masoud Salehi, “An efficient decoding algorithm for concatenated RS-convolutional codes,” 2009 43rd Annual Conference on Information Sciences and Systems, pp. 411–413, Mar. 2009, doi: 10.1109/CISS.2009.5054755. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/CISS.2009.5054755',
                    'author' => 'Yueqian Li and Masoud Salehi',
                    'title' => 'An efficient decoding algorithm for concatenated RS-convolutional codes',
                    'year' => '2009',
                    'month' => 'March',
                    'pages' => '411-413',
                    'booktitle' => '2009 43rd Annual Conference on Information Sciences and Systems',
                    ]
            ],
            [
                'source' => 'Abenavoli, L., Larussa, T., Corea, A., Procopio, A., Boccuto, L., Dallio, M., . . . Luzza, F. (3 de Feb. de 2021). Polyphenols and Non-Alcoholic Fatty Liver Disease. Nutrients. doi:10.3390/nu13020494. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.3390/nu13020494',
                    'year' => '2021',
                    'month' => 'febrero',
                    'date' => '2021-02-03',
                    'title' => 'Polyphenols and Non-Alcoholic Fatty Liver Disease',
                    'journal' => 'Nutrients',
                    'author' => 'Abenavoli, L. and Larussa, T. and Corea, A. and Procopio, A. and Boccuto, L. and Dallio, M. and others and Luzza, F.',
                ],
                'language' =>'es',
            ],
            [
                'source' => 'Aguiar, J. E., & Miwa, M. (2009). O vinho e sua história - Do Império Romano, pela Idade Média, até os nossos dias, o vinho acompanhou os principais momentos da história da humanidade e se transformou. Obtido de https://revistaadega.uol.com.br/artigo/o-vinho-e-sua-historia_1064.html ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://revistaadega.uol.com.br/artigo/o-vinho-e-sua-historia_1064.html',
                    'year' => '2009',
                    'title' => 'O vinho e sua história - Do Império Romano, pela Idade Média, até os nossos dias, o vinho acompanhou os principais momentos da história da humanidade e se transformou',
                    'author' => 'Aguiar, J. E. and Miwa, M.',
                ],
                'char_encoding' => 'utf8leave',
                'language' => 'pt',
            ],
            [
                'source' => 'M.V. Moreno-Arribas & Polo, M. (12 de 01 de 2007). Winemaking biochemistry and microbiology: current knowledge and future trends. Food Science and Nutrition , 45(4), pp. 265-286. doi:https://doi.org/10.1080/10408690490478118 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1080/10408690490478118',
                    'pages' => '265-286',
                    'title' => 'Winemaking biochemistry and microbiology: current knowledge and future trends',
                    'author' => 'M. V. Moreno-Arribas and Polo, M.',
                    'year' => '2007',
                    'date' => '2007-01-12',
                    'month' => '01',
                    'number' => '4',
                    'volume' => '45',
                    'journal' => 'Food Science and Nutrition',
                    ]
            ],
            [
                'source' => '\bibitem[Bekta\c{s} {\em et~al.}, 2022]{bektas22} Bekta\c{s} AB {\em et~al}. Fast and interpretable genomic data analysis using multiple approximate kernel learning. {\em Bioinformatics} 2022;\textbf{38}, i77--i83. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bekta\c{s}, A. B. and others',
                    'title' => 'Fast and interpretable genomic data analysis using multiple approximate kernel learning',
                    'year' => '2022',
                    'journal' => 'Bioinformatics',
                    'volume' => '38',
                    'pages' => 'i77-i83',
                    ]
            ],
            [
                'source' => '[68]	M.G. Gubler, A.J. Kovacs, La structure du polyethylene considere comme un melange de n-paraffines, Journal of Polymer Science XXXIV (1959) 551–568. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. G. Gubler and A. J. Kovacs',
                    'title' => 'La structure du polyethylene considere comme un melange de n-paraffines',
                    'year' => '1959',
                    'journal' => 'Journal of Polymer Science',
                    'volume' => 'XXXIV',
                    'pages' => '551-568',
                    ]
            ],
            [
                'source' => ' \bibitem{daily17} J. Daily, J. Peterson, Predictive Maintenance: How Big Data Analysis Can Improve Maintenance. In: Richter, K., Walther, J. (eds) Supply Chain Integration Challenges in Commercial Aerospace. Springer, Cham. 2017 doi:/10.1007/978-3-319-46155-7\_18 ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'J. Daily and J. Peterson',
                    'title' => 'Predictive Maintenance: How Big Data Analysis Can Improve Maintenance',
                    'year' => '2017',
                    'doi' => '10.1007/978-3-319-46155-7\_18',
                    'editor' => 'Richter, K. and Walther, J.',
                    'address' => 'Cham',
                    'publisher' => 'Springer',
                    'booktitle' => 'Supply Chain Integration Challenges in Commercial Aerospace',
                    ]
            ],
            [
                'source' => 'Berthrong, John H. (1994). All Under Heaven: Transforming Paradigms in Confucian-Christian Dialogue. Albany: State University of New York Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Berthrong, John H.',
                    'year' => '1994',
                    'title' => 'All Under Heaven: Transforming Paradigms in Confucian-Christian Dialogue',
                    'publisher' => 'State University of New York Press',
                    'address' => 'Albany',
                    ]
            ],
            [
                'source' => '_______. Transformations of the Confucian Way. Boulder, CO: Westview Press, 1998. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Berthrong, John H.',
                    'title' => 'Transformations of the Confucian Way',
                    'year' => '1998',
                    'address' => 'Boulder, CO',
                    'publisher' => 'Westview Press',
                    ]
            ],
            [
                'source' => '[22] Fei Hua, Yanhao Chen, Yuwei Jin, Chi Zhang, Ari Hayes, Youtao Zhang, and Eddy Z. Zhang. 2021. AutoBraid: A Framework for Enabling Efficient Surface Code Communication in Quantum Computing. In MICRO-54: 54th Annual IEEE/ACM International Symposium on Microarchitecture (MICRO \'21). ACM, New York, NY, USA, 925–936. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Fei Hua and Yanhao Chen and Yuwei Jin and Chi Zhang and Ari Hayes and Youtao Zhang and Eddy Z. Zhang',
                    'title' => 'AutoBraid: A Framework for Enabling Efficient Surface Code Communication in Quantum Computing',
                    'year' => '2021',
                    'booktitle' => 'MICRO-54: 54th Annual IEEE/ACM International Symposium on Microarchitecture (MICRO \'21)',
                    'pages' => '925-936',
                    'address' => 'New York, NY, USA',
                    'publisher' => 'ACM',
                    ]
            ],
            [
                'source' => '\bibitem[Garlan and Shaw 1993]{GARLAN_SHAW:1993}Garlan, D. \& Shaw, M.: ``An Introduction to Software Architecture\'\'; {\em Advances In Software Engineering And Knowledge Engineering}. pp. 1-39 (1993), doi: 10.1142/9789812798039\_0001 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1142/9789812798039\_0001',
                    'author' => 'Garlan, D. and Shaw, M.',
                    'title' => 'An Introduction to Software Architecture',
                    'year' => '1993',
                    'journal' => 'Advances In Software Engineering And Knowledge Engineering',
                    'pages' => '1-39',
                    ]
            ],
            [
                'source' => '\bibitem[Hasselbring 2018]{HASSELBRING:2018}Hasselbring, W.: ``Software Architecture: Past, Present, Future\'\'; {\em The Essence Of Software Engineering}. pp. 169-184 (2018), doi: 10.1007/978-3-319-73897-0\_10 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/978-3-319-73897-0\_10',
                    'author' => 'Hasselbring, W.',
                    'title' => 'Software Architecture: Past, Present, Future',
                    'year' => '2018',
                    'journal' => 'The Essence Of Software Engineering',
                    'pages' => '169-184',
                    ]
            ],
            [
                'source' => ' Dratch, Rabbi Mark. 2003. ‘Forgiving the Unforgivable? Jewish Insights into Repentance and Forgiveness’. Journal of Religion & Abuse 4 (4): 7–24. https://doi.org/10.1300/J154v04n04_02. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dratch, Rabbi Mark',
                    'title' => 'Forgiving the Unforgivable? Jewish Insights into Repentance and Forgiveness',
                    'journal' => 'Journal of Religion & Abuse',
                    'year' => '2003',
                    'volume' => '4',
                    'number' => '4',
                    'pages' => '7-24',
                    'doi' => '10.1300/J154v04n04\_02',
                    ]
            ],
            [
                'source' => ' Amati, Ghila. 2023. ‘Discovering the Depths Within: Kook’s Zionism and the Philosophy of Life of Henri Bergson.’ Religions 14 (2). https://doi.org/10.3390/rel14020261. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Amati, Ghila',
                    'title' => 'Discovering the Depths Within: Kook\'s Zionism and the Philosophy of Life of Henri Bergson',
                    'journal' => 'Religions',
                    'year' => '2023',
                    'volume' => '14',
                    'number' => '2',
                    'doi' => '10.3390/rel14020261',
                    ]
            ],
            [
                'source' => ' Amati, Ghila. Forthcoming. ‘Freedom, Creativity, the Self, and God: Between Rabbi Kook and Bergson’s Lebensphilosophie.’ Harvard Theological Review. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Amati, Ghila',
                    'title' => 'Freedom, Creativity, the Self, and God: Between Rabbi Kook and Bergson\'s Lebensphilosophie',
                    'journal' => 'Harvard Theological Review',
                    'year' => 'Forthcoming',
                    ]
            ],
            [
                'source' => ' Beer, Moshe. 2011. ‘Al Maaseu Kapparah Shel Baale Teshuvah Besifrut Chazal’. In Sages of the Mishnah and the Talmud: Teachings, Activities and Leadership. Ramat Gan: Bar-Ilan University Press, 216–239. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Beer, Moshe',
                    'title' => 'Al Maaseu Kapparah Shel Baale Teshuvah Besifrut Chazal',
                    'year' => '2011',
                    'pages' => '216-239',
                    'address' => 'Ramat Gan',
                    'publisher' => 'Bar-Ilan University Press',
                    'booktitle' => 'Sages of the Mishnah and the Talmud: Teachings, Activities and Leadership',
                    ]
            ],
            [
                'source' => ' Gilbert, Maurice. 2002. ‘God, Sin and Mercy: Sirach 15:11–18:14’ In Ben Sira’s God: Proceedings of the International Ben Sira Conference: Durham – Ushaw College 2001, edited by R. Egger Wenzel, 118–135. Berlin: de Gruyter. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Gilbert, Maurice',
                    'title' => 'God, Sin and Mercy: Sirach 15:11--18:14',
                    'year' => '2002',
                    'pages' => '118-135',
                    'editor' => 'R. Egger Wenzel',
                    'address' => 'Berlin',
                    'publisher' => 'de Gruyter',
                    'booktitle' => 'Ben Sira\'s God: Proceedings of the International Ben Sira Conference: Durham -- Ushaw College 2001',
                    ]
            ],
            [
                'source' => ' Ish Shalom, Benjamin. 1993. Rabbi Avraham Itzhak Ha-Cohen Kook: Between Rationalism and Mysticism. Translated by Ora Wiskind-Elper. Albany: State University of New York Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ish Shalom, Benjamin',
                    'title' => 'Rabbi Avraham Itzhak Ha-Cohen Kook: Between Rationalism and Mysticism',
                    'year' => '1993',
                    'note' => 'Translated by Ora Wiskind-Elper.',
                    'address' => 'Albany',
                    'publisher' => 'State University of New York Press',
                    ]
            ],
            [
                'source' => ' Nachman of Breslov. 1995. Likutey Moharan. Translated by Ozer Bergman and Moshe Mykoff. Jerusalem/New York: Breslov Research Institue. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Nachman of Breslov',
                    'title' => 'Likutey Moharan',
                    'year' => '1995',
                    'note' => 'Translated by Ozer Bergman and Moshe Mykoff.',
                    'address' => 'Jerusalem/New York',
                    'publisher' => 'Breslov Research Institue',
                    ]
            ],
            [
                'source' => ' Petuchowski, Jakob J. 1968. ‘The Concept of “Teshuvah” in the Bible and the Talmud’. Judaism 17 (2): 175. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Petuchowski, Jakob J.',
                    'title' => 'The Concept of ``Teshuvah\'\' in the Bible and the Talmud',
                    'journal' => 'Judaism',
                    'year' => '1968',
                    'volume' => '17',
                    'number' => '2',
                    'pages' => '175',
                    ]
            ],
            [
                'source' => ' Strauss, Leo. 2013. Leo Strauss on Maimonides: The Complete Writings. Edited by Kenneth Green Hart. Chicago: University of Chicago Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Strauss, Leo',
                    'title' => 'Leo Strauss on Maimonides: The Complete Writings',
                    'year' => '2013',
                    'note' => 'Edited by Kenneth Green Hart.',
                    'address' => 'Chicago',
                    'publisher' => 'University of Chicago Press',
                    ]
            ],
            [
                'source' => '[11] 	YardLink, “What Is BIM and Why Is It Important in Construction?,” 12 October 2021. [Online]. Available: https://yardlink.com/blog/what-is-bim-and-why-is-it-important-in-construction. [Accessed 12 December 2023].  ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://yardlink.com/blog/what-is-bim-and-why-is-it-important-in-construction',
                    'urldate' => '12 December 2023',
                    'year' => '2021',
                    'month' => 'October',
                    'date' => '2021-10-12',
                    'author' => 'YardLink',
                    'title' => 'What Is BIM and Why Is It Important in Construction?',
                    ]
            ],
            [
                'source' => '\bibitem{ROGERS20141421}Rogers, D. Leaking Water Networks: An Economic and Environmental Disaster. {\em Procedia Engineering}. \textbf{70} pp. 1421-1429 (2014)',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Rogers, D.',
                    'title' => 'Leaking Water Networks: An Economic and Environmental Disaster',
                    'year' => '2014',
                    'journal' => 'Procedia Engineering',
                    'volume' => '70',
                    'pages' => '1421-1429',
                    ]
            ],
            [
                'source' => '\bibitem{Robus_Leak}Quiñones-Grueiro, M., Ares Milián, M., Sánchez Rivero, M., Silva Neto, A. \& Llanes-Santiago, O. Robust leak localization in water distribution networks using computational intelligence. {\em Neurocomputing}. \textbf{438} pp. 195-208 (2021)',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Qui{\~n}ones-Grueiro, M. and Ares Mili{\\\'a}n, M. and S{\\\'a}nchez Rivero, M. and Silva Neto, A. and Llanes-Santiago, O.',
                    'title' => 'Robust leak localization in water distribution networks using computational intelligence',
                    'year' => '2021',
                    'journal' => 'Neurocomputing',
                    'volume' => '438',
                    'pages' => '195-208',
                    ]
            ],
            [
                'source' => 'van der Geest, L.G.M.; Lemmens, V.E.P.P.; de Hingh, I.H.J.T.; van Laarhoven, C.J.H.M.; Bollen, T.L.; Nio, C.Y.; van Eijck, C.H.J.; Busch, O.R.C.; Besselink, M.G.; Dutch Pancreatic Cancer Group Nationwide Outcomes in Patients Undergoing Surgical Exploration without Resection for Pancreatic Cancer. Br. J. Surg. 2017, 104, 1568–1577, doi:10.1002/bjs.10602. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1002/bjs.10602',
                    'author' => 'van der Geest, L. G. M. and Lemmens, V. E. P. P. and de Hingh, I. H. J. T. and van Laarhoven, C. J. H. M. and Bollen, T. L. and Nio, C. Y. and van Eijck, C. H. J. and Busch, O. R. C. and Besselink, M. G.',
                    'title' => 'Dutch Pancreatic Cancer Group Nationwide Outcomes in Patients Undergoing Surgical Exploration without Resection for Pancreatic Cancer',
                    'year' => '2017',
                    'journal' => 'Br. J. Surg',
                    'volume' => '104',
                    'pages' => '1568-1577',
                    ]
            ],
            [
                'source' => 'Tempero, M.A.; Malafa, M.P.; Al-Hawary, M.; Behrman, S.W.; Benson, A.B.; Cardin, D.B.; Chiorean, E.G.; Chung, V.; Czito, B.; Del Chiaro, M.; et al. Pancreatic Adenocarcinoma, Version 2.2021, NCCN Clinical Practice Guidelines in Oncology. J. Natl. Compr. Canc. Netw. 2021, 19, 439–457, doi:10.6004/jnccn.2021.0017. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.6004/jnccn.2021.0017',
                    'author' => 'Tempero, M. A. and Malafa, M. P. and Al-Hawary, M. and Behrman, S. W. and Benson, A. B. and Cardin, D. B. and Chiorean, E. G. and Chung, V. and Czito, B. and Del Chiaro, M. and others',
                    'title' => 'Pancreatic Adenocarcinoma, Version 2.2021, NCCN Clinical Practice Guidelines in Oncology',
                    'year' => '2021',
                    'journal' => 'J. Natl. Compr. Canc. Netw',
                    'volume' => '19',
                    'pages' => '439-457',
                    ]
            ],
            [
                'source' => 'Perdana, F. R., Wahyu, H., & Daryanto. 2015. Perbandingan Metode Double Exponential Smoothing Dengan Triple Exponential Smoothing Pada Peramalan Penjualan Rokok. Jember: Universitas Muhammaadiyah Jember. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Perdana, F. R. and Wahyu, H. and Daryanto',
                    'title' => 'Perbandingan Metode Double Exponential Smoothing Dengan Triple Exponential Smoothing Pada Peramalan Penjualan Rokok',
                    'year' => '2015',
                    'address' => 'Jember',
                    'publisher' => 'Universitas Muhammaadiyah Jember',
                    ]
            ],
            [
                'source' => '23.	Piri, R., et al., "Global" cardiac atherosclerotic burden assessed by artificial intelligence-based versus manual segmentation in (18)F-sodium fluoride PET/CT scans: Head-to-head comparison. J Nucl Cardiol, 2022. 29(5): p. 2531-2539. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Piri, R. and others',
                    'title' => '``Global\'\' cardiac atherosclerotic burden assessed by artificial intelligence-based versus manual segmentation in (18)F-sodium fluoride PET/CT scans: Head-to-head comparison',
                    'year' => '2022',
                    'journal' => 'J Nucl Cardiol',
                    'pages' => '2531-2539',
                    'volume' => '29',
                    'number' => '5',
                    ]
            ],
            [
                'source' => '\bibitem{b34}Peduzzi, P., Concato, J., Kemper, E., Holford, T.R., Feinstein, A. R. (1996). A simulation study of the number of events per variable in logistic regression analysis. Journal of Clinical Epidemiology. Dec;49(12):1373-9. doi: 10.1016/s0895-4356(96)00236-3. PMID: 8970487. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/s0895-4356(96)00236-3',
                    'note' => 'PMID: 8970487',
                    'author' => 'Peduzzi, P. and Concato, J. and Kemper, E. and Holford, T. R. and Feinstein, A. R.',
                    'year' => '1996',
                    'title' => 'A simulation study of the number of events per variable in logistic regression analysis',
                    'journal' => 'Journal of Clinical Epidemiology.',
                    'pages' => '12',
                    'month' => 'December',
                    'volume' => '49',
                    'number' => '12',
                    'pages' => '1373-9'
                    ]
            ],
            [
                'source' => 'Yan, B.; Luh, P.B.; Warner, G.; Zhang, P. Operation and Design Optimization of Microgrids with Renewables. IEEE Trans. Autom. Sci. Eng. 2017, 14, 573–585. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yan, B. and Luh, P. B. and Warner, G. and Zhang, P.',
                    'title' => 'Operation and Design Optimization of Microgrids with Renewables',
                    'year' => '2017',
                    'journal' => 'IEEE Trans. Autom. Sci. Eng',
                    'volume' => '14',
                    'pages' => '573-585',
                    ]
            ],
            [
                'source' => 'Erenoğlu, A.K., Şengör, İ., Erdinç, O., Taşcıkaraoğlu, A. and Catalão, J.P., 2022. Optimal energy management system for microgrids considering energy storage, demand response and renewable power generation. International Journal of Electrical Power & Energy Systems, 136, p.107714. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Erenoğlu, A. K. and Şengör, İ. and Erdinç, O. and Taşcıkaraoğlu, A. and Catalão, J. P.',
                    'title' => 'Optimal energy management system for microgrids considering energy storage, demand response and renewable power generation',
                    'year' => '2022',
                    'journal' => 'International Journal of Electrical Power & Energy Systems',
                    'volume' => '136',
                    'pages' => '107714',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Alok Kumar Shukla, & Tripathi, D. (2019). Identification of potential biomarkers on microarray data using distributed gene selection approach. Mathematical Biosciences, 315, 108230–108230. https://doi.org/10.1016/j.mbs.2019.108230 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.mbs.2019.108230',
                    'author' => 'Alok Kumar Shukla and Tripathi, D.',
                    'year' => '2019',
                    'title' => 'Identification of potential biomarkers on microarray data using distributed gene selection approach',
                    'journal' => 'Mathematical Biosciences',
                    'pages' => '108230-108230',
                    'volume' => '315',
                    ]
            ],
            [
                'source' => 'Burch JQ, and Campbell GB (1963) A new genus for a deep-water Californian naticid. Proceedings of the Malacological Society of London 35((5)), 221-223.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Burch, J. Q. and Campbell, G. B.',
                    'year' => '1963',
                    'title' => 'A new genus for a deep-water Californian naticid',
                    'volume' => '35',
                    'number' => '5',
                    'pages' => '221-223',
                    'journal' => 'Proceedings of the Malacological Society of London',
                    ]
            ],
            [
                'source' => ' Ben Shlomo, Yoseph. 1990. Poetry of Being. Translated by Shmuel Himelstein. Tel Aviv: MOD Books. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ben Shlomo, Yoseph',
                    'title' => 'Poetry of Being',
                    'year' => '1990',
                    'note' => 'Translated by Shmuel Himelstein.',
                    'address' => 'Tel Aviv',
                    'publisher' => 'MOD Books',
                    ]
            ],
            [
                'source' => ' Gruenwald, Itamar. 1991. ‘The Concept of Teshuvah in the Teachings of Maimonides and Rav Kook’. In The World of Rav Kook’s Thought, edited by Benjamin Ish Shalom and Shalom Rosenberg, translated by Shalom Carmy. Jerusalem: Avi Chai, 282–304.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Gruenwald, Itamar',
                    'title' => 'The Concept of Teshuvah in the Teachings of Maimonides and Rav Kook',
                    'year' => '1991',
                    'pages' => '282-304',
                    'note' => 'Translated by Shalom Carmy.',
                    'editor' => 'Benjamin Ish Shalom and Shalom Rosenberg',
                    'address' => 'Jerusalem',
                    'publisher' => 'Avi Chai',
                    'booktitle' => 'The World of Rav Kook\'s Thought',
                    ]
            ],
            [
                'source' => ' Maimonides, Moses. 2012. Moses Maimonides on Teshuvah: The Ways of Repentance. A New Translation and Commentary. Translated by Henry Abramson. 2nd ed. Middletown, DE: Smashwords. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Maimonides, Moses',
                    'year' => '2012',
                    'edition' => '2nd',
                    'title' => 'Moses Maimonides on Teshuvah: The Ways of Repentance. A New Translation and Commentary',
                    'note' => 'Translated by Henry Abramson.',
                    'publisher' => 'Smashwords',
                    'address' => 'Middletown, DE',
                    ]
            ],
            [
                'source' => 'Lehmberg, T. & Wörner, K. (2008). Annotation standards. In A. Lüdeling & M. Kytö (Eds.), Corpus linguistics – An international handbook (volume 1) (pp. 484-501). Walter de Gruyter. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Lehmberg, T. and W{\"o}rner, K.',
                    'year' => '2008',
                    'title' => 'Annotation standards',
                    'pages' => '484-501',
                    'editor' => 'A. L{\"u}deling and M. Kyt{\"o}',
                    'booktitle' => 'Corpus linguistics -- An international handbook (volume 1)',
                    'publisher' => 'Walter de Gruyter',
                    ]
            ],
            [
                'source' => 'Schoepf, I. C., Esteban-Cantos, A., Thorball, C. W., Rodés, B., Reiss, P., Rodríguez-Centeno, J., ... & Tarr, P. E. (2023). Epigenetic ageing accelerates before antiretroviral therapy and decelerates after viral suppression in people with HIV in Switzerland: a longitudinal study over 17 years. The Lancet Healthy Longevity, 4(5), e211-e218. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Schoepf, I. C. and Esteban-Cantos, A. and Thorball, C. W. and Rod{\\\'e}s, B. and Reiss, P. and Rodr{\\\'\i}guez-Centeno, J. and others and Tarr, P. E.',
                    'title' => 'Epigenetic ageing accelerates before antiretroviral therapy and decelerates after viral suppression in people with HIV in Switzerland: a longitudinal study over 17 years',
                    'journal' => 'The Lancet Healthy Longevity',
                    'year' => '2023',
                    'volume' => '4',
                    'number' => '5',
                    'pages' => 'e211-e218',
                    ]
            ],
            [
                'source' => 'Gehle, S. C., Kleissler, D., Heiling, H., Deal, A., Xu, Z., Ayer Miller, V. L., ... & Smitherman, A. B. (2023). Accelerated epigenetic aging and myopenia in young adult cancer survivors. Cancer Medicine. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Gehle, S. C. and Kleissler, D. and Heiling, H. and Deal, A. and Xu, Z. and Ayer Miller, V. L. and others and Smitherman, A. B.',
                    'title' => 'Accelerated epigenetic aging and myopenia in young adult cancer survivors',
                    'journal' => 'Cancer Medicine',
                    'year' => '2023',
                    ]
            ],
            [
                'source' => 'Burnard, L. (2004). Developing linguistic corpora: a guide to good practice. Metadata for corpus work. https://users.ox.ac.uk/~martinw/dlc/chapter3.htm (last accessed, 7 March 2024) ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Burnard, L.',
                    'title' => 'Developing linguistic corpora: a guide to good practice. Metadata for corpus work',
                    'year' => '2004',
                    'url' => 'https://users.ox.ac.uk/~martinw/dlc/chapter3.htm',
                    'urldate' => '7 March 2024',
                    ]
            ],
            [
                'source' => 'Carlsen, C. (2012). Proficiency level—A fuzzy variable in computer learner corpora. Applied Linguistics, 33(2), 161‑183. https://doi.org/10.1093/applin/amr047 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Carlsen, C.',
                    'title' => 'Proficiency level---A fuzzy variable in computer learner corpora',
                    'journal' => 'Applied Linguistics',
                    'year' => '2012',
                    'volume' => '33',
                    'number' => '2',
                    'pages' => '161-183',
                    'doi' => '10.1093/applin/amr047',
                    ]
            ],
            [
                'source' => 'Ortega, L. (2019). SLA and the study of equitable multilingualism. The Modern Language Journal, 103, 23‑38. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ortega, L.',
                    'title' => 'SLA and the study of equitable multilingualism',
                    'journal' => 'The Modern Language Journal',
                    'year' => '2019',
                    'volume' => '103',
                    'pages' => '23-38',
                    ]
            ],
            [
                'source' => '\bibitem{feature_2} Nuñez, M. 2019. “Exploring Materials Band Structure Space with Unsupervised Machine Learning.” Computational Materials Science 158 (February): 117–23. https://doi.org/10.1016/j.commatsci.2018.11.002. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.commatsci.2018.11.002',
                    'author' => 'Nu{\~n}ez, M.',
                    'year' => '2019',
                    'title' => 'Exploring Materials Band Structure Space with Unsupervised Machine Learning',
                    'journal' => 'Computational Materials Science',
                    'month' => 'February',
                    'volume' => '158',
                    'pages' => '117-23',
                    ]
            ],
            [
                'source' => 'Chalofsky, N. (2007). The seminal foundation of the discipline of HRD: people, learning, and organizations. Human Resource Development Quarterly, 18(3). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Chalofsky, N.',
                    'title' => 'The seminal foundation of the discipline of HRD: people, learning, and organizations',
                    'journal' => 'Human Resource Development Quarterly',
                    'year' => '2007',
                    'volume' => '18',
                    'number' => '3',
                    ]
            ], [
                'source' => 'Boero, P., Garuti, R., Lemut, E. y Mariotti, A. (1996). Challenging the traditional school approach to theorems: A hypothesis about the cognitive unity of theorems. En L. Puig y A. Gutiérrez (Eds.), Proceedings of the 20th PME international conference (pp. 113–120).  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Boero, P. and Garuti, R. and Lemut, E. and Mariotti, A.',
                    'title' => 'Challenging the traditional school approach to theorems: A hypothesis about the cognitive unity of theorems',
                    'year' => '1996',
                    'pages' => '113-120',
                    'editor' => 'L. Puig and A. Gutiérrez',
                    'booktitle' => 'Proceedings of the 20th PME international conference',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Lemmetty, S., 1999. Review of speech synthesis technology (Master\'s thesis). HELSINKI UNIVERSITY OF TECHNOLOGY ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Lemmetty, S.',
                    'year' => '1999',
                    'title' => 'Review of speech synthesis technology',
                    'school' => 'HELSINKI UNIVERSITY OF TECHNOLOGY',
                    ]
            ],
            [
                'source' => 'Duarte, B (2010). Cuestiones didácticas a propósito de la enseñanza de la fundamentación en matemática. [Tesis doctoral]. Universidad de San Andrés.  ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Duarte, B.',
                    'year' => '2010',
                    'title' => 'Cuestiones didácticas a propósito de la enseñanza de la fundamentación en matemática',
                    'school' => 'Universidad de San Andrés',
                ],
                'language' => 'es',
                'char_encoding' => 'utf8leave'
            ], 
            [
                'source' => 'Brown Jr, William O. (2001). Faculty participation in university governance and the effects on university performance. Journal of Economic Behavior & Organization, 44, (2), 129-143 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Brown Jr, William O.',
                    'year' => '2001',
                    'title' => 'Faculty participation in university governance and the effects on university performance',
                    'journal' => 'Journal of Economic Behavior & Organization',
                    'pages' => '129-143',
                    'volume' => '44',
                    'number' => '2',
                    ]
            ],
            [
                'source' => 'Abid, M., 2017. Does economic, financial and institutional developments matter for environmental quality? A comparative analysis of EU and MEA countries. J. Environ. Manage. 188, 183–194. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abid, M.',
                    'year' => '2017',
                    'title' => 'Does economic, financial and institutional developments matter for environmental quality? A comparative analysis of EU and MEA countries',
                    'journal' => 'J. Environ. Manage.',
                    'volume' => '188',
                    'pages' => '183-194',
                    ]
            ],
            [
                'source' => 'Acheampong, A.O., Amponsah, M., Boateng, E., 2020. Does financial development mitigate carbon emissions? Evidence from heterogeneous financial economies. Energy Econ. 88, 104768. ',
                'type' => 'article',
                'bibtex' => [
                    'pages' => '104768',
                    'author' => 'Acheampong, A. O. and Amponsah, M. and Boateng, E.',
                    'year' => '2020',
                    'title' => 'Does financial development mitigate carbon emissions? Evidence from heterogeneous financial economies',
                    'journal' => 'Energy Econ.',
                    'volume' => '88',
                    ]
            ],
            [
                'source' => 'Bion, R. A. H., Borovsky, A., & Fernald, A. (2013). Fast mapping, slow learning: Disambiguation of novel word–object mappings in relation to vocabulary learning at 18, 24, and 30 months. Cognition, 126(1), 39–53. https://doi.org/10.1016/j.cognition.2012.08.008 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bion, R. A. H. and Borovsky, A. and Fernald, A.',
                    'title' => 'Fast mapping, slow learning: Disambiguation of novel word--object mappings in relation to vocabulary learning at 18, 24, and 30 months',
                    'journal' => 'Cognition',
                    'year' => '2013',
                    'volume' => '126',
                    'number' => '1',
                    'pages' => '39-53',
                    'doi' => '10.1016/j.cognition.2012.08.008',
                    ]
            ],
            [
                'source' => 'DeLoache, J. S. (1984). What’s This? Maternal Questions in Joint Picture Book Reading with Toddlers. https://eric.ed.gov/?id=ED251176 ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'DeLoache, J. S.',
                    'title' => 'What\'s This? Maternal Questions in Joint Picture Book Reading with Toddlers',
                    'year' => '1984',
                    'url' => 'https://eric.ed.gov/?id=ED251176',
                    ]
            ],
            [
                'source' => 'Horst, J. S., Parsons, K. L., & Bryan, N. M. (2011). Get the Story Straight: Contextual Repetition Promotes Word Learning from Storybooks. Frontiers in Psychology, 2. https://www.frontiersin.org/journals/psychology/articles/10.3389/fpsyg.2011.00017 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Horst, J. S. and Parsons, K. L. and Bryan, N. M.',
                    'title' => 'Get the Story Straight: Contextual Repetition Promotes Word Learning from Storybooks',
                    'journal' => 'Frontiers in Psychology',
                    'year' => '2011',
                    'volume' => '2',
                    'url' => 'https://www.frontiersin.org/journals/psychology/articles/10.3389/fpsyg.2011.00017',
                    ]
            ],
            [
                'source' => 'Ahenkan, A., & Boon, E. (2011). Non-timber forest products (NTFPs): Clearing the confusion in semantics. Journal of Human Ecology, 33(1), 1-9. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ahenkan, A. and Boon, E.',
                    'year' => '2011',
                    'title' => 'Non-timber forest products (NTFPs): Clearing the confusion in semantics',
                    'journal' => 'Journal of Human Ecology',
                    'volume' => '33',
                    'number' => '1',
                    'pages' => '1-9',
                    ]
            ],
            [
                'source' => 'Fleischman, F. D. (2014). Why do foresters plant trees? Testing theories of bureaucratic decision-making in central India. World Development, 62, 62-74. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Fleischman, F. D.',
                    'year' => '2014',
                    'title' => 'Why do foresters plant trees? Testing theories of bureaucratic decision-making in central India',
                    'journal' => 'World Development',
                    'volume' => '62',
                    'pages' => '62-74',
                    ]
            ],
            [
                'source' => 'Adams, J. L. (1993). Flying Buttresses, Entropy, and O-Rings: The World of an Engineer. Harvard University Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Adams, J. L.',
                    'year' => '1993',
                    'title' => 'Flying Buttresses, Entropy, and O-Rings: The World of an Engineer',
                    'publisher' => 'Harvard University Press',
                    ]
            ],
            [
                'source' => 'M. Campbell, A. J. Hoane Jr, and F.-h. Hsu. Deep blue. Artificial intelligence, 134(1-2):57–83, 2002.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. Campbell and Hoane Jr, A. J. and F.-h. Hsu',
                    'title' => 'Deep blue',
                    'journal' => 'Artificial intelligence',
                    'year' => '2002',
                    'volume' => '134',
                    'number' => '1-2',
                    'pages' => '57-83',
                    ]
            ],
            [
                'source' => ' Defensie, „F-16,” 2023 januari 18. Available: https://www.defensie.nl/onderwerpen/materieel/vliegtuigen-en-helikopters/f-16. ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.defensie.nl/onderwerpen/materieel/vliegtuigen-en-helikopters/f-16',
                    'author' => 'Defensie',
                    'title' => 'F-16',
                    'year' => '2023',
                    'month' => 'januari',
                    'date' => '2023-01-18',
                    'urldate' => '2023 januari 18',
                ],
                'language' => 'nl',
            ],
            [
                'source' => 'E. v. d. Boom, „Lockheed F-16,” vliegles.nl, 18 juli 2022. Available: https://www.vliegles.nl/vliegtuig/lockheedf16#:~:text=Hoeveeln',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.vliegles.nl/vliegtuig/lockheedf16#:~:text=Hoeveeln',
                    'author' => 'E. v. d. Boom',
                    'title' => 'Lockheed F-16',
                    'urldate' => '18 juli 2022',
                    'year' => '2022',
                    'month' => 'juli',
                    'date' => '2022-07-18',
                    'note' => 'vliegles.nl',
                ],
                'language' => 'nl',
            ],
            [
                'source' => 'Getachew, D., Getachew, T., Debella, A., Eyeberu, A., Atnafe, G., & Assefa, N. (2022). Magnitude and determinants of knowledge towards pregnancy danger signs among pregnant women attending antenatal care at Chiro town health institutions, Ethiopia. SAGE Open Medicine, 10, 20503121221075124. https://doi.org/10.1177/20503121221075125 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Getachew, D. and Getachew, T. and Debella, A. and Eyeberu, A. and Atnafe, G. and Assefa, N.',
                    'title' => 'Magnitude and determinants of knowledge towards pregnancy danger signs among pregnant women attending antenatal care at Chiro town health institutions, Ethiopia',
                    'journal' => 'SAGE Open Medicine',
                    'year' => '2022',
                    'volume' => '10',
                    'note' => 'Article 20503121221075124',
                    'doi' => '10.1177/20503121221075125',
                    ]
            ],
            [
                'source' => 'Fekene, D. B., Woldeyes, B. S., Erena, M. M., & Demisse, G. A. (2020). Knowledge, uptake of preconception care and associated factors among reproductive age group women in West Shewa zone, Ethiopia, 2018. BMC Women’s Health, 20(1), 30. https://doi.org/10.1186/s12905-020-00900-2 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Fekene, D. B. and Woldeyes, B. S. and Erena, M. M. and Demisse, G. A.',
                    'title' => 'Knowledge, uptake of preconception care and associated factors among reproductive age group women in West Shewa zone, Ethiopia, 2018',
                    'journal' => 'BMC Women\'s Health',
                    'year' => '2020',
                    'volume' => '20',
                    'number' => '1',
                    'pages' => '30',
                    'doi' => '10.1186/s12905-020-00900-2',
                    ]
            ],
            [
                'source' => 'Anderson, Stanford. 2001. "The Profession and Discipline of Architecture: Practice and Education." In The Discipline of Architecture, by Andrzej Piotrowski and Julia Williams Robinson, 292-305. Minneapolis; London: University of Minnesota Press. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Anderson, Stanford',
                    'year' => '2001',
                    'title' => 'The Profession and Discipline of Architecture: Practice and Education',
                    'pages' => '292-305',
                    'booktitle' => 'The Discipline of Architecture',
                    'publisher' => 'University of Minnesota Press',
                    'address' => 'Minneapolis; London',
                    'editor' => 'Andrzej Piotrowski and Julia Williams Robinson',
                    ]
            ],
            [
                'source' => 'Abidemi, A.K. and Abiodun, A.A. (2023). Exponentially generated modified Chen distribution with applications to lifetime dataset. J. of Probability and Statistics, Article ID \ 4458562, 25 pages. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abidemi, A. K. and Abiodun, A. A.',
                    'title' => 'Exponentially generated modified Chen distribution with applications to lifetime dataset',
                    'journal' => 'J. of Probability and Statistics',
                    'year' => '2023',
                    'note' => 'Article ID 4458562, 25 pages',
                    ]
            ],
            [
                'source' => 'Abiodun, A.A. and Ishaq, A.I. (2022). On Maxwell-Lomax distribution: properties and applications. Arab J. of Basic and Applied Sciences, 29:1, 221-232. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abiodun, A. A. and Ishaq, A. I.',
                    'title' => 'On Maxwell-Lomax distribution: properties and applications',
                    'journal' => 'Arab J. of Basic and Applied Sciences',
                    'year' => '2022',
                    'volume' => '29',
                    'number' => '1',
                    'pages' => '221-232',
                    ]
            ],
            [
                'source' => 'Abonongo, A.I.L. and Abonongo, J. (2023). Exponentiated generalized Weibull exponential distribution: properties, estimation and applications. Computational J. of Mathematical and Statistical Sciences, 3(1), 57-84. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abonongo, A. I. L. and Abonongo, J.',
                    'title' => 'Exponentiated generalized Weibull exponential distribution: properties, estimation and applications',
                    'journal' => 'Computational J. of Mathematical and Statistical Sciences',
                    'year' => '2023',
                    'volume' => '3',
                    'number' => '1',
                    'pages' => '57-84',
                    ]
            ],
            [
                'source' => 'Alqawba, M., Altayab, Y., Zaidi, S.M., and Afify, A.Z. (2023). The extended Kumaraswamy generated family: properties, inference and applications in applied fields. EJASA, 16(3), 740-763. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Alqawba, M. and Altayab, Y. and Zaidi, S. M. and Afify, A. Z.',
                    'title' => 'The extended Kumaraswamy generated family: properties, inference and applications in applied fields',
                    'journal' => 'EJASA',
                    'year' => '2023',
                    'volume' => '16',
                    'number' => '3',
                    'pages' => '740-763',
                    ]
            ],
            [
                'source' => 'Zeenalabiden, N.A. and Saracoglu, B. (2023). A new family of distributions: exponential power-X family of distributions and its some properties. Iraqi J. of Statistical Sciences, 20(3), 235-248.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Zeenalabiden, N. A. and Saracoglu, B.',
                    'year' => '2023',
                    'title' => 'A new family of distributions: exponential power-X family of distributions and its some properties',
                    'journal' => 'Iraqi J. of Statistical Sciences',
                    'volume' => '20',
                    'number' => '3',
                    'pages' => '235-248',
                    ]
            ],
            [
                'source' => 'Ainsworth, L., & Viegut, D. (2006). Common formative assessments: How to Connect Standards-Based Instruction and Assessment. Thousand Oaks, CA: Corwin Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ainsworth, L. and Viegut, D.',
                    'year' => '2006',
                    'title' => 'Common formative assessments: How to Connect Standards-Based Instruction and Assessment',
                    'address' => 'Thousand Oaks, CA',
                    'publisher' => 'Corwin Press',
                    ]
            ],
            [
                'source' => 'Angoff, W. W. (1971). Scales, norms, and equivalent scores. In R. L Thorndike (Ed.). Educational Measurement 2nd Ed. Washington, D.C.: American Council on Education. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Angoff, W. W.',
                    'year' => '1971',
                    'title' => 'Scales, norms, and equivalent scores',
                    'editor' => 'R. L. Thorndike',
                    'booktitle' => 'Educational Measurement 2nd Ed.',
                    'publisher' => 'American Council on Education',
                    'address' => 'Washington, D.C.',
                    ]
            ],
            [
                'source' => '[8]	Chatterjee S, Kumari S, Rath S, Das S. Chapter 1 - Prospects and scope of microbial bioremediation for the restoration of the contaminated sites. In: Das S, Dash HR, editors. Microbial Biodegradation and Bioremediation (Second Edition), Elsevier; 2022, p. 3–31. https://doi.org/https://doi.org/10.1016/B978-0-323-85455-9.00011-4. ',
                'type' => 'incollection',
                'bibtex' => [
                    'doi' => '10.1016/B978-0-323-85455-9.00011-4',
                    'author' => 'Chatterjee, S. and Kumari, S. and Rath, S. and Das, S.',
                    'title' => 'Prospects and scope of microbial bioremediation for the restoration of the contaminated sites',
                    'year' => '2022',
                    'pages' => '3-31',
                    'editor' => 'Das, S. and Dash, H. R.',
                    'publisher' => 'Elsevier',
                    'booktitle' => 'Microbial Biodegradation and Bioremediation (Second Edition)',
                    'chapter' => '1',
                    ]
            ],
            [
                'source' => 'Anak Kemarau, R. & Eboy, O.V. (2022) Statistical Modeling of Impacts El Niño Southern Oscillations (ENSO) on Land Surface Temperature in Small Medium Size City: Case Study Kuching Sarawak. Journal of Sustainable Natural Resources. 3 (1). doi:10.30880/jsunr.2022.03.01.002. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.30880/jsunr.2022.03.01.002',
                    'author' => 'Anak Kemarau, R. and Eboy, O. V.',
                    'title' => 'Statistical Modeling of Impacts El Niño Southern Oscillations (ENSO) on Land Surface Temperature in Small Medium Size City: Case Study Kuching Sarawak',
                    'year' => '2022',
                    'journal' => 'Journal of Sustainable Natural Resources.',
                    'volume' => '3',
                    'number' => '1',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Balas, D.B., Tiwari, M.K., Trivedi, M. & Patel, G.R. (2023) Impact of Land Surface Temperature (LST) and Ground Air Temperature (Tair) on Land Use and Land Cover (LULC): An Investigative Study. International Journal of Environment and Climate Change. https://api.semanticscholar.org/CorpusID:261847328. ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://api.semanticscholar.org/CorpusID:261847328',
                    'author' => 'Balas, D. B. and Tiwari, M. K. and Trivedi, M. and Patel, G. R.',
                    'year' => '2023',
                    'title' => 'Impact of Land Surface Temperature (LST) and Ground Air Temperature (Tair) on Land Use and Land Cover (LULC): An Investigative Study',
                    'journal' => 'International Journal of Environment and Climate Change',
                    ]
            ],
            [
                'source' => 'Dikbaş, F. (2017) A novel two‐dimensional correlation coefficient for assessing associations in time series data. International Journal of Climatology. 37. https://api.semanticscholar.org/CorpusID:126412072. ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://api.semanticscholar.org/CorpusID:126412072',
                    'author' => 'Dikbaş, F.',
                    'year' => '2017',
                    'title' => 'A novel two-dimensional correlation coefficient for assessing associations in time series data',
                    'journal' => 'International Journal of Climatology.',
                    'volume' => '37',
                ],
                'char_encoding' => 'utf8leave',
            ], 
            [
                'source' => 'França, F.M., Ferreira, J., Vaz-de-Mello, F.Z., Maia, L.F., Berenguer, E., Ferraz Palmeira, A., Fadini, R., Louzada, J., Braga, R., Hugo Oliveira, V. & Barlow, J. (2020) El Niño impacts on human-modified tropical forests: Consequences for dung beetle diversity and associated ecological processes. Biotropica. 52 (2). doi:10.1111/btp.12756. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1111/btp.12756',
                    'author' => 'França, F. M. and Ferreira, J. and Vaz-de-Mello, F. Z. and Maia, L. F. and Berenguer, E. and Ferraz Palmeira, A. and Fadini, R. and Louzada, J. and Braga, R. and Hugo Oliveira, V. and Barlow, J.',
                    'title' => 'El Niño impacts on human-modified tropical forests: Consequences for dung beetle diversity and associated ecological processes',
                    'year' => '2020',
                    'journal' => 'Biotropica.',
                    'volume' => '52',
                    'number' => '2',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Acuña, A. M., Caso, L., Aliphat, M. M., and Vergara, C. H. (2011). Edible insects as part of the traditional food system of the Popoloca town of Los Reyes Metzontla, Mexico. Journal of Ethnobiology, 31(1), 150-169. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Acuña, A. M. and Caso, L. and Aliphat, M. M. and Vergara, C. H.',
                    'year' => '2011',
                    'title' => 'Edible insects as part of the traditional food system of the Popoloca town of Los Reyes Metzontla, Mexico',
                    'journal' => 'Journal of Ethnobiology',
                    'volume' => '31',
                    'number' => '1',
                    'pages' => '150-169',
                ],
                'char_encoding' => 'utf8leave',
            ],
            [
                'source' => 'Marcantonio ER, Ngo LH, O\'Connor M, Jones RN, Crane PK, Metzger ED, Inouye SK. 3D-CAM: derivation and validation of a 3-minute diagnostic interview for CAM-defined delirium: a cross-sectional diagnostic test study. Ann Intern Med. 2014 Oct 21;161(8):554-61.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Marcantonio, E. R. and Ngo, L. H. and O\'Connor, M. and Jones, R. N. and Crane, P. K. and Metzger, E. D. and Inouye, S. K.',
                    'title' => '3D-CAM: derivation and validation of a 3-minute diagnostic interview for CAM-defined delirium: a cross-sectional diagnostic test study',
                    'year' => '2014',
                    'month' => 'October',
                    'date' => '2014-10-21',
                    'pages' => '554-61',
                    'journal' => 'Ann Intern Med.',
                    'volume' => '161',
                    'number' => '8',
                    ]
            ],
            [
                'source' => 'Berkowitz, Jacob F., Lindsey Green, Christine M. VanZomeren, and John R. White. 2016. Evaluating Soil Properties and Potential Nitrate Removal in Wetlands Created Using an Engineering With Nature Based Dredged Material Placement Technique. Ecological Engineering 97: 381-88. http://dx.doi.org/10.1016/j.ecoleng.2016.10.022. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.ecoleng.2016.10.022',
                    'author' => 'Berkowitz, Jacob F. and Lindsey Green and Christine M. VanZomeren and John R. White',
                    'title' => 'Evaluating Soil Properties and Potential Nitrate Removal in Wetlands Created Using an Engineering With Nature Based Dredged Material Placement Technique',
                    'year' => '2016',
                    'journal' => 'Ecological Engineering',
                    'pages' => '381-88',
                    'volume' => '97',
                    ]
            ],
            [
                'source' => '\bibitem{Crowderetal} Harlan P.~Crowder, Ron S.~Dembo und John M.~Mulvey: \newblock Reporting computational experiments in Mathematical Programming. \newblock {\em Mathematical Programming}, {\bf 15}:316--329, 1978. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Harlan P. Crowder and Ron S. Dembo and John M. Mulvey',
                    'title' => 'Reporting computational experiments in Mathematical Programming',
                    'journal' => 'Mathematical Programming',
                    'year' => '1978',
                    'volume' => '15',
                    'pages' => '316-329',
                    ]
            ],
            [
                'source' => 'Anderson, E., Oliver, R.L., 1987. Perspectives on Behavior-BasedVersus Outcome-Based Salesforce Control Systems. J. Market. 51, 76–88. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Anderson, E. and Oliver, R. L.',
                    'title' => 'Perspectives on Behavior-BasedVersus Outcome-Based Salesforce Control Systems',
                    'journal' => 'J. Market.',
                    'year' => '1987',
                    'volume' => '51',
                    'pages' => '76-88',
                    ]
            ],
            [
                'source' => '5.	Billingham, R. E., Brent, L. & Medawar, P. B. ‘Activity Acquired Tolerance’ of Foreign Cells. Nature 172, 603–606 (1953). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Billingham, R. E. and Brent, L. and Medawar, P. B.',
                    'title' => '`Activity Acquired Tolerance\' of Foreign Cells',
                    'year' => '1953',
                    'journal' => 'Nature',
                    'volume' => '172',
                    'pages' => '603-606',
                    ]
            ],
            [
                'source' => ' \bibitem{MM} Machina, M. (1982),  ```Expected Utility\' Analysis Without the Independence Axiom,\'\'  {\it Econometrica}, 50, 277-324. ',
                'type' => 'article',
                'bibtex' => [
                    'year' => '1982',
                    'pages' => '277-324',
                    'title' => '`Expected Utility\' Analysis Without the Independence Axiom',
                    'author' => 'Machina, M.',
                    'volume' => '50',
                    'journal' => 'Econometrica',
                    ]
            ],
            [
                'source' => '• Johnson, M., & Crews, T. (2023). Enhancement of MR fluids through nanoparticle integration. Journal of Material Science and Engineering, 67(8), 1129-1143. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Johnson, M. and Crews, T.',
                    'year' => '2023',
                    'title' => 'Enhancement of MR fluids through nanoparticle integration',
                    'journal' => 'Journal of Material Science and Engineering',
                    'volume' => '67',
                    'number' => '8',
                    'pages' => '1129-1143',
                    ]
            ],
            [
                'source' => 'Akhilesh, K. V. (2014) Fishery and biology of deep-sea chondrichthyans off the southwest coast of India. Phd Thesis. Cochin University of Science & Technology. http://dyuthi.cusat.ac.in/purl/4951 ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'school' => 'Cochin University of Science & Technology',
                    'url' => 'http://dyuthi.cusat.ac.in/purl/4951',
                    'author' => 'Akhilesh, K. V.',
                    'year' => '2014',
                    'title' => 'Fishery and biology of deep-sea chondrichthyans off the southwest coast of India',
                    ]
            ],
            [
                'source' => '	\bibitem{Jimenez20} {Jimenez J.C., de la Cruz H., De Maio P., Efficient computation of phi-functions in exponential integrators, J. Comput. Appl. Math., 374 (2020) 112758.} ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jimenez, J. C. and de la Cruz, H. and De Maio, P.',
                    'title' => 'Efficient computation of phi-functions in exponential integrators',
                    'journal' => 'J. Comput. Appl. Math.',
                    'year' => '2020',
                    'volume' => '374',
                    'pages' => '112758',
                    ]
            ],
            [
                'source' => '	\bibitem{Jimenez06 SIAM} {Jimenez J.C., Pedroso L., Carbonell F., 		Hernadez V., Local linearization method for numerical integration of delay differential equations, SIAM J. Numer. Analysis, 44 (2006) 2584-2609.} ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jimenez, J. C. and Pedroso, L. and Carbonell, F. and Hernadez, V.',
                    'title' => 'Local linearization method for numerical integration of delay differential equations',
                    'journal' => 'SIAM J. Numer. Analysis',
                    'year' => '2006',
                    'volume' => '44',
                    'pages' => '2584-2609',
                    ]
            ],
            [
                'source' => 'LIU H L，Ma J，ZHANG G X. Review of studies on deep learning-based content recommendation algorithms［J］. Computer Engineering，2021，47（7）：1-12. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Liu, H. L. and Ma, J. and Zhang, G. X.',
                    'title' => 'Review of studies on deep learning-based content recommendation algorithms',
                    'journal' => 'Computer Engineering',
                    'pages' => '1-12',
                    'volume' => '47',
                    'year' => '2021',
                    'number' => '7',
                    ]
            ],
            [
                'source' => '\bibitem{PantaleoneAJP2002} J. Pantaleone, Synchronization of metronomes, Am. J. Phys. {\bf70}, 992 (2002). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Pantaleone',
                    'title' => 'Synchronization of metronomes',
                    'year' => '2002',
                    'journal' => 'Am. J. Phys.',
                    'volume' => '70',
                    'pages' => '992',
                    ]
            ],
            [
                'source' => '\bibitem{RauPR1963} J. Rau, Relaxation Phenomena in Spin and Harmonic Oscillator Systems, Phys. Rev. {\bf129}, 1880 (1963). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Rau',
                    'title' => 'Relaxation Phenomena in Spin and Harmonic Oscillator Systems',
                    'year' => '1963',
                    'journal' => 'Phys. Rev.',
                    'volume' => '129',
                    'pages' => '1880',
                    ]
            ],
            [
                'source' => '\bibitem{CattaneoPRL2021} M. Cattaneo, G. De Chiara, S. Maniscalco, R. Zambrini, and G. L. Giorgi, Collision Models Can Efficiently Simulate Any Multipartite Markovian Quantum Dynamics, Phys. Rev. Lett. {\bf126}, 130403 (2021). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. Cattaneo and G. De Chiara and S. Maniscalco and R. Zambrini and G. L. Giorgi',
                    'title' => 'Collision Models Can Efficiently Simulate Any Multipartite {M}arkovian Quantum Dynamics',
                    'year' => '2021',
                    'journal' => 'Phys. Rev. Lett.',
                    'volume' => '126',
                    'pages' => '130403',
                ],
                'use' => 'latex',
            ],
            [
                'source' => ' \bibitem{LiPRA2023} W. Li, J. Cheng, W. -J. Gong, and J. Li, Nonlinear self-sustaining dynamics in cavity magnomechanics, Phys. Rev. A {\bf108}, 033518 (2023). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'W. Li and J. Cheng and W. -J. Gong and J. Li',
                    'title' => 'Nonlinear self-sustaining dynamics in cavity magnomechanics',
                    'year' => '2023',
                    'journal' => 'Phys. Rev. A',
                    'volume' => '108',
                    'pages' => '033518',
                    ]
            ],
            [
                'source' => '\bibitem {b22} K. Plis, R. Bunescu and C. Marling, "A machine learning approach to predicting blood glucose levels for diabetes management," in AAAI-14: 2014 Association for the Advancement of Artificial Intelligence Workshop, Ohio, 2014.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'K. Plis and R. Bunescu and C. Marling',
                    'title' => 'A machine learning approach to predicting blood glucose levels for diabetes management',
                    'booktitle' => 'AAAI-14: 2014 Association for the Advancement of Artificial Intelligence Workshop, Ohio',
                    'year' => '2014',
                    ]
            ],
            [
                // conversion 2061, 525-2059-source.txt
                'source' => 'Young, L., & Dungan, J. (2012). Where in the brain is morality? Everywhere and maybe nowhere. Social Neuroscience, 7(February), 1-10. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Young, L. and Dungan, J.',
                    'year' => '2012',
                    'title' => 'Where in the brain is morality? Everywhere and maybe nowhere',
                    'journal' => 'Social Neuroscience',
                    'month' => 'February',
                    'volume' => '7',
                    'pages' => '1-10',
                    ]
            ],
            [
                'source' => 'Yoo, H., Feng, X., & Day, R. (2013). Adolescents? empathy and prosocial behavior in the family context: a longitudinal study. Journal of Youth and Adolescence, 42(12), 1858-72. doi:10.1007/s10964-012-9900-6 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s10964-012-9900-6',
                    'author' => 'Yoo, H. and Feng, X. and Day, R.',
                    'year' => '2013',
                    'title' => 'Adolescents? empathy and prosocial behavior in the family context: a longitudinal study',
                    'journal' => 'Journal of Youth and Adolescence',
                    'volume' => '42',
                    'number' => '12',
                    'pages' => '1858-72',
                    ]
            ],
            [
                'source' => 'BOAG Paul. Why whitespace matters [online]. Dostupné z: https://boagworld.com/design/why-whitespace-matters/ ',
                'type' => 'online',
                'bibtex' => [
                    'note' => 'Dostupn{\\\'e} z',
                    'url' => 'https://boagworld.com/design/why-whitespace-matters/',
                    'author' => 'Boag, Paul',
                    'title' => 'Why whitespace matters',
                    ]
            ],
            [
                'source' => '\bibitem{Katanaev}  	Katanaev, M.O.,  Volovich, I.V.   	{ Theory of defects in solids and three-dimensional gravity}.  	{\it Annals of Physics} 216, 1-28 (1992). 	\href{https://doi.org/10.1016/0003-4916(52)90040-7}{\tt 10.1016/0003-4916(52)90040-7}. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/0003-4916(52)90040-7',
                    'author' => 'Katanaev, M. O. and Volovich, I. V.',
                    'title' => 'Theory of defects in solids and three-dimensional gravity',
                    'year' => '1992',
                    'journal' => 'Annals of Physics',
                    'pages' => '1-28',
                    'volume' => '216',
                    ]
            ],
            // title stopped early
            [
                'source' => '104.	Kanaujia, R., Biswal, M., Angrup, A., & Ray, P. (2022). Diagnostic accuracy of the metagenomic next-generation sequencing (mNGS) for detection of bacterial meningoencephalitis: a systematic review and meta-analysis. European Journal of Clinical Microbiology & Infectious Diseases, 41(6), 881–891. https://doi.org/10.1007/s10096-022-04445-0 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s10096-022-04445-0',
                    'author' => 'Kanaujia, R. and Biswal, M. and Angrup, A. and Ray, P.',
                    'year' => '2022',
                    'title' => 'Diagnostic accuracy of the metagenomic next-generation sequencing (mNGS) for detection of bacterial meningoencephalitis: a systematic review and meta-analysis',
                    'journal' => 'European Journal of Clinical Microbiology & Infectious Diseases',
                    'pages' => '881-891',
                    'volume' => '41',
                    'number' => '6',
                    ]
            ],
            // title stopped early
            [
                'source' => '107.	Ben Amor, K., Breeuwer, P., Verbaarschot, P., Rombouts, F. ., Akkermans, A. D. ., de Vos, W. ., & Abee, T. (2002). Multiparametric Flow Cytometry and Cell Sorting for the Assessment of Viable, Injured, and Dead Bifidobacterium Cells during Bile Salt Stress. Applied and Environmental Microbiology, 68(11), 5209–5216. https://doi.org/10.1128/AEM.68.11.5209-5216.2002 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1128/AEM.68.11.5209-5216.2002',
                    'author' => 'Ben Amor, K. and Breeuwer, P. and Verbaarschot, P. and Rombouts, F. and Akkermans, A. D. and de Vos, W. and Abee, T.',
                    'year' => '2002',
                    'title' => 'Multiparametric Flow Cytometry and Cell Sorting for the Assessment of Viable, Injured, and Dead Bifidobacterium Cells during Bile Salt Stress',
                    'journal' => 'Applied and Environmental Microbiology',
                    'pages' => '5209-5216',
                    'volume' => '68',
                    'number' => '11',
                    ]
            ],
            // title ended early (end at colon only if next word is journal word??)
            [
                'source' => 'Palazzo, S., & Levey, J. (2024). Shaping the Future of Nursing Education: Next Generation NCLEX (NGN) Question Writing and the Power of Psychometrics. Nursing Education Perspectives, 45(2). doi:doi.10.1097/01.NEP.0000000000001234 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Palazzo, S. and Levey, J.',
                    'title' => 'Shaping the Future of Nursing Education: Next Generation NCLEX (NGN) Question Writing and the Power of Psychometrics',
                    'journal' => 'Nursing Education Perspectives',
                    'year' => '2024',
                    'volume' => '45',
                    'number' => '2',
                    'doi' => '10.1097/01.NEP.0000000000001234',
                    ]
            ],
            // title ended early
            [
                'source' => 'Smith, C. R., Palazzo, S. J., Grubb, P. L., & Gillespie, G. L. (2020). Standing up against workplace bullying: Recommendations from newly licensed nurses. Journal of Nursing Education and Practice, 10(7). doi:10.5430/jnep.v10n7p35 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Smith, C. R. and Palazzo, S. J. and Grubb, P. L. and Gillespie, G. L.',
                    'title' => 'Standing up against workplace bullying: Recommendations from newly licensed nurses',
                    'journal' => 'Journal of Nursing Education and Practice',
                    'year' => '2020',
                    'volume' => '10',
                    'number' => '7',
                    'doi' => '10.5430/jnep.v10n7p35',
                    ]
            ],
            // journal name ended early
            [
                'source' => '[3] D. S. Weile, G. Pisharody, N. W. Chen, B. Shanker, and E. Michielssen, "A novel scheme for the solution of the time-domain integral equations of electromagnetics," IEEE Trans. Antennas Propag., vol. 52, no. 1, pp. 283-295, Jan. 2004. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'D. S. Weile and G. Pisharody and N. W. Chen and B. Shanker and E. Michielssen',
                    'title' => 'A novel scheme for the solution of the time-domain integral equations of electromagnetics',
                    'year' => '2004',
                    'month' => 'January',
                    'journal' => 'IEEE Trans. Antennas Propag.',
                    'pages' => '283-295',
                    'volume' => '52',
                    'number' => '1',
                    ]
            ],
            // title ended early
            [
                'source' => '\bibitem{DeSantola 2017} DeSantola, A., \& Gulati, R. (2017). Scaling: Organizing and in entrepreneurial ventures. \textit{Academy of Management Annals}, 11(2), 640--668. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'DeSantola, A. and Gulati, R.',
                    'year' => '2017',
                    'title' => 'Scaling: Organizing and in entrepreneurial ventures',
                    'journal' => 'Academy of Management Annals',
                    'pages' => '640-668',
                    'volume' => '11',
                    'number' => '2',
                    ]
            ],
            // title ended early
            [
                'source' => '\bibitem{DeSantola 2022} DeSantola, A., Gulati, R., \& Zhelyazkov, P. I. (2022). External interfaces or internal processes? Market positioning and divergent professionalization paths in young ventures. \textit{Organization Science}, 16(4). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'DeSantola, A. and Gulati, R. and Zhelyazkov, P. I.',
                    'year' => '2022',
                    'title' => 'External interfaces or internal processes? Market positioning and divergent professionalization paths in young ventures',
                    'journal' => 'Organization Science',
                    'volume' => '16',
                    'number' => '4',
                    ]
            ],
            // title ended early
            [
                'source' => '\bibitem{Fini 2011} Fini, R., Grimaldi, R., Santoni, S., \& Sobrero, M. (2011). Complements or substitutes? The role of universities and local context in supporting the creation of academic spin-offs. Research Policy, 40(8), 1113-1127. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Fini, R. and Grimaldi, R. and Santoni, S. and Sobrero, M.',
                    'title' => 'Complements or substitutes? The role of universities and local context in supporting the creation of academic spin-offs',
                    'journal' => 'Research Policy',
                    'year' => '2011',
                    'volume' => '40',
                    'number' => '8',
                    'pages' => '1113-1127',
                    ]
            ],
            // Journal name included in title
            [
                'source' => 'Kaninjing, E., Dickey, S., & Ouma, C. (2022). Communication of Family Health History Among College Students and Their Families. International Journal of Higher Education, 11, 16. Retrieved from https://doi.org/10.5430/ijhe.v11n5p153 doi:10.5430/ijhe.v11n5p153 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kaninjing, E. and Dickey, S. and Ouma, C.',
                    'title' => 'Communication of Family Health History Among College Students and Their Families',
                    'journal' => 'International Journal of Higher Education',
                    'year' => '2022',
                    'volume' => '11',
                    'pages' => '16',
                    'doi' => '10.5430/ijhe.v11n5p153',
                    'url' => 'https://doi.org/10.5430/ijhe.v11n5p153',
                    ]
            ],
            // doi not correctly identified, because of nonstandard URL
            [
                'source' => 'Millender, E., Dickey, S. L., Ouma, C., Bruneau, D., & Wisdom-Chambers, K. (2022). Addressing Disparities by Evaluating Depression and Prostate Screenings in a Community Health Clinic. Journal of Community Health, 39(1), 31. Retrieved from https://doi-org.proxy.lib.fsu.edu/10.1080/07370016.2022.2028063 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Millender, E. and Dickey, S. L. and Ouma, C. and Bruneau, D. and Wisdom-Chambers, K.',
                    'title' => 'Addressing Disparities by Evaluating Depression and Prostate Screenings in a Community Health Clinic',
                    'journal' => 'Journal of Community Health',
                    'year' => '2022',
                    'volume' => '39',
                    'number' => '1',
                    'pages' => '31',
                    'doi' => '10.1080/07370016.2022.2028063',
                    ]
            ],
            [
                'source' => 'GROOTSCHOLTEN, T.I.M.; STEINBUSCH, K.J.J.; HAMELERS, H.V.M.; BUISMAN, C.J.N. (2013) High rate heptanoate production from propionate and ethanol using chain elongation. Bioresource Technology, v. 136, p. 715-718. https://doi.org/10.1016/j.biortech. 2013.02.085 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Grootscholten, T. I. M. and Steinbusch, K. J. J. and Hamelers, H. V. M. and Buisman, C. J. N.',
                    'title' => 'High rate heptanoate production from propionate and ethanol using chain elongation',
                    'journal' => 'Bioresource Technology',
                    'year' => '2013',
                    'volume' => '136',
                    'pages' => '715-718',
                    'doi' => '10.1016/j.biortech',
                    ]
            ],
            // words "Masters thesis" included in school field
            [
                'source' => 'Atherton, C. (2017). An Investigation of Heterogeneity and the Impact of Acidic Regions on Bulk Effluent from a Deconstructed Low Sulfide Waste-Rock Pile. Masters Thesis, University of Waterloo. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Atherton, C.',
                    'year' => '2017',
                    'title' => 'An Investigation of Heterogeneity and the Impact of Acidic Regions on Bulk Effluent from a Deconstructed Low Sulfide Waste-Rock Pile',
                    'school' => 'University of Waterloo',
                    ]
            ], 
            // title ended early, after spp.
            [
                'source' => 'Ziba MW, Bowa B, Romantini R, Di Marzio V, Marfoglia C, Antoci S, Muuka G, Scacchia M, Mattioli M, Pomilio F (2020). Occurrence and antimicrobial resistance of Salmonella spp. in broiler chicken neck skin from slaughterhouses in Zambia. Journal of Veterinary Medicine and Animal Health  12(2):85-90.    24                      ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ziba, M. W. and Bowa, B. and Romantini, R. and Di Marzio, V. and Marfoglia, C. and Antoci, S. and Muuka, G. and Scacchia, M. and Mattioli, M. and Pomilio, F.',
                    'year' => '2020',
                    'title' => 'Occurrence and antimicrobial resistance of Salmonella spp. in broiler chicken neck skin from slaughterhouses in Zambia',
                    'journal' => 'Journal of Veterinary Medicine and Animal Health',
                    'volume' => '12',
                    'number' => '2',
                    'pages' => '85-90',
                    ]
            ],
            // school not properly identified
            [
                'source' => 'Shilpakar, A., 2009. Phytochemical screening and analysis of antibacterial and antioxidant activity of Ficus auriculata, Lour. stem bark (Doctoral dissertation, Pokhara University).',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Shilpakar, A.',
                    'year' => '2009',
                    'title' => 'Phytochemical screening and analysis of antibacterial and antioxidant activity of Ficus auriculata, Lour. stem bark',
                    'school' => 'Pokhara University',
                    ]
            ],
            // extra chars after volume
            [
                'source' => 'Mouho, D.G., Oliveira, A.P., Kodjo, C.G., Valentão, P., Gil-Izquierdo, A., Andrade, P.B., Ouattara, Z.A., Bekro, Y.A. and Ferreres, F., 2018. Chemical findings and in vitro biological studies to uphold the use of Ficus exasperata Vahl leaf and stem bark. Food and Chemical Toxicology, 112, pp.134-144.‎ ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Mouho, D. G. and Oliveira, A. P. and Kodjo, C. G. and Valentão, P. and Gil-Izquierdo, A. and Andrade, P. B. and Ouattara, Z. A. and Bekro, Y. A. and Ferreres, F.',
                    'year' => '2018',
                    'title' => 'Chemical findings and in vitro biological studies to uphold the use of Ficus exasperata Vahl leaf and stem bark',
                    'journal' => 'Food and Chemical Toxicology',
                    'pages' => '134-144',
                    'volume' => '112',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // volume issue format: 1/2
            [
                'source' => 'S. Yoon and A. Jameson. (1988) Lower-upper symmetric-Gauss-Seidel method for the Euler and Navier-Stokes equations, AIAA Journal, Vol. 26/9, pp. 1025-1026. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'S. Yoon and A. Jameson',
                    'year' => '1988',
                    'title' => 'Lower-upper symmetric-Gauss-Seidel method for the {E}uler and Navier-Stokes equations',
                    'journal' => 'AIAA Journal',
                    'pages' => '1025-1026',
                    'volume' => '26',
                    'number' => '9',
                ],
                'use' => 'latex',
            ],
            // Portuguese date format
            [
                'source' => 'Agencia de Sostenibilidad Energética. Ponle energia a tu Pyme. 2023. https://www.agenciase.org/energia-a-tu-pyme/ (acesso em 07 de 03 de 2023). ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.agenciase.org/energia-a-tu-pyme/',
                    'author' => 'Agencia de Sostenibilidad Energética',
                    'year' => '2023',
                    'urldate' => '07 de 03 de 2023',
                    'title' => 'Ponle energia a tu Pyme',
                ],
                'language' => 'pt',
                'char_encoding' => 'utf8leave',
            ],
            // for book (as opposed to incollection), city at end of title should not be part of title
            [
                'source' => 'Eliade, Mircea. 1964. Traité d’Histoire des Religions, Paris: Payot. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Eliade, Mircea',
                    'title' => 'Traité d\'Histoire des Religions',
                    'year' => '1964',
                    'address' => 'Paris',
                    'publisher' => 'Payot',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // secondary date in brackets
            [
                'source' => 'Abdel Razek, Ali. 2012 [1925]. Islam and the Foundations of Political Power. Translated by Maryam Loutfi. Edited by Abdou Filali-Ansary. Edinburgh: Edinburgh University Press. ',
                'type' => 'book',
                'bibtex' => [
                    'note' => 'Translated by Maryam Loutfi. Edited by Abdou Filali-Ansary.',
                    'author' => 'Abdel Razek, Ali',
                    'year' => '2012 [1925]',
                    'title' => 'Islam and the Foundations of Political Power',
                    'publisher' => 'Edinburgh University Press',
                    'address' => 'Edinburgh',
                    ]
            ],
            // Journal included in title
            [
                'source' => 'Baroni M., Bernardini S., Ferraresi A. & Zanchetta E. (2009), The WaCky wide web: a collection of very large linguistically processed webcrawled corpora, Language Resources and Evaluation, 43, pp. 209–226. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Baroni, M. and Bernardini, S. and Ferraresi, A. and Zanchetta, E.',
                    'year' => '2009',
                    'title' => 'The WaCky wide web: a collection of very large linguistically processed webcrawled corpora',
                    'journal' => 'Language Resources and Evaluation',
                    'volume' => '43',
                    'pages' => '209-226',
                    ]
            ],
            // Journal included in title, No. symbol
            [
                'source' => 'Atkins S., Clear J., Ostler N. (1992), Corpus Design Criteria, Literary and Linguistic Computing, Vol. 7, № 1, pp.1–16. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Atkins, S. and Clear, J. and Ostler, N.',
                    'year' => '1992',
                    'title' => 'Corpus Design Criteria',
                    'journal' => 'Literary and Linguistic Computing',
                    'pages' => '1-16',
                    'volume' => '7',
                    'number' =>	'1',
                    ]
            ],
            // journal included in title
            [
                'source' => 'Benjamin R. G. (2012), Reconstructing readability: recent developments and recommendations in the analysis of text difficulty, Educational Psychology Review, 24(1), pp. 63–88. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Benjamin, R. G.',
                    'year' => '2012',
                    'title' => 'Reconstructing readability: recent developments and recommendations in the analysis of text difficulty',
                    'journal' => 'Educational Psychology Review',
                    'volume' => '24',
                    'number' => '1',
                    'pages' => '63-88',
                    ]
            ],
            // slash between issue numbers
            [
                'source' => 'Kim, Jaegwon. 1999. ‘Making Sense of Emergence’, Philosophical Studies 95, no.1/2: 3–36.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kim, Jaegwon',
                    'title' => 'Making Sense of Emergence',
                    'journal' => 'Philosophical Studies',
                    'year' => '1999',
                    'volume' => '95',
                    'number' => '1/2',
                    'pages' => '3-36',
                    ]
            ],
            // Journal included in title
            [
                'source' => 'Stone, Jerome A. 2011. Is a “Christian Naturalism” Possible?: Exploring the Boundaries of a Tradition. American Journal of Theology and Philosophy 32, no. 3: 205–220.    ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Stone, Jerome A.',
                    'title' => 'Is a ``Christian Naturalism\'\' Possible?: Exploring the Boundaries of a Tradition',
                    'journal' => 'American Journal of Theology and Philosophy',
                    'year' => '2011',
                    'volume' => '32',
                    'number' => '3',
                    'pages' => '205-220',
                    ]
            ],
                        // journal appended to end of title
            [
                'source' => 'ANTONIO JOSÉ SÁNCHEZ-GUARNIDO et al. Analysis of the Consequences of the COVID-19 Pandemic on People with Severe Mental Disorders. International Journal of Environmental Research and Public Health, v. 18, n. 16, p. 8549–8549, 13 ago. 2021.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Antonio José Sánchez-Guarnido and others',
                    'title' => 'Analysis of the Consequences of the COVID-19 Pandemic on People with Severe Mental Disorders',
                    'year' => '2021',
                    'month' => 'agosto',
                    'date' => '2021-08-13',
                    'journal' => 'International Journal of Environmental Research and Public Health',
                    'volume' => '18',
                    'number' => '16',
                    'pages' => '8549-8549',
                ],
                'char_encoding' => 'utf8leave',
                'language' => 'pt',
            ],
            // y-m-d date
            [
                'source' => 'Nordås, H., E. Pinali and M. Geloso Grosso (2006-05-30), “Logistics and Time as a Trade Barrier”, OECD Trade Policy Papers, No. 35, OECD Publishing, Paris. http://dx.doi.org/10.1787/664220308873.  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1787/664220308873',
                    'author' => 'Nordås, H. and E. Pinali and M. Geloso Grosso',
                    'year' => '2006',
                    'month' => '05',
                    'date' => '2006-05-30',
                    'title' => 'Logistics and Time as a Trade Barrier',
                    'journal' => 'OECD Trade Policy Papers, No.',
                    'volume' => '35, OECD Publishing, Paris',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // uses braces as quotation marks
            [
                'source' => '\bibitem{EM2} Edison M. N. Guzmán, Abramo Hefez, {On value sets of fractional ideals} J. Commut. Algebra 14(3): 339-349 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Edison M. N. Guzmán and Abramo Hefez',
                    'title' => 'On value sets of fractional ideals',
                    'journal' => 'J. Commut. Algebra',
                    'volume' => '14',
                    'number' => '3',
                    'pages' => '339-349',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // question marks as quotes
            [
                'source' => '[7]	N. S. Abdul Mubarak, N. N. Bahrudin, A. H. Jawad, B. Hameed, and S. Sabar, ?Microwave Enhanced Synthesis of Sulfonated Chitosan-Montmorillonite for Effective Removal of Methylene Blue,? J. Polym. Environ., vol. 29, 2021, doi: 10.1007/s10924-021-02172-9. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s10924-021-02172-9',
                    'author' => 'N. S. Abdul Mubarak and N. N. Bahrudin and A. H. Jawad and B. Hameed and S. Sabar',
                    'title' => 'Microwave Enhanced Synthesis of Sulfonated Chitosan-Montmorillonite for Effective Removal of Methylene Blue',
                    'year' => '2021',
                    'journal' => 'J. Polym. Environ.',
                    'volume' => '29',
                    ]
            ],
            // spaces after "vol" and "no"
            [
                'source' => 'KABAKOFF, R. P.; CHAZDON, R. L. Effects of Canopy Species Dominance on Understorey Light Availability in Low-Elevation Secondary Forest Stands in Costa Rica . Journal of Tropical Ecology , Vol . 12 , No . 6 , 1996 . ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kabakoff, R. P. and Chazdon, R. L.',
                    'title' => 'Effects of Canopy Species Dominance on Understorey Light Availability in Low-Elevation Secondary Forest Stands in Costa Rica',
                    'year' => '1996',
                    'journal' => 'Journal of Tropical Ecology',
                    'volume' => '12',
                    'number' => '6',
                    ]
            ],
            // Page number starting with letter not detected
            [
                'source' => 'Philip, N., Madore, M., & Kozel, F. (2021). Transcranial Magnetic Stimulation in Veterans-Real World Efficacy and the Foundation for Mechanistic Insights. Biological Psychiatry, 89(9), S76. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Philip, N. and Madore, M. and Kozel, F.',
                    'title' => 'Transcranial Magnetic Stimulation in Veterans-Real World Efficacy and the Foundation for Mechanistic Insights',
                    'journal' => 'Biological Psychiatry',
                    'year' => '2021',
                    'volume' => '89',
                    'number' => '9',
                    'pages' => 'S76',
                    ]
            ],
            // p. 12 should not be classified as volume.  Start of title included in authors.
            [
                'source' => 'FRAZER, G.; CANHAM, C.; LERTZMAN, K. Gap Light Analyzer (GLA), Version 2.0: Imaging software to extract canopy structure and gap light transmission indices from truecolour fisheye photographs, users manual and program documentation. Program, p. 36, 1999. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Frazer, G. and Canham, C. and Lertzman, K.',
                    'title' => 'Gap Light Analyzer (GLA), Version 2.0: Imaging software to extract canopy structure and gap light transmission indices from truecolour fisheye photographs, users manual and program documentation',
                    'year' => '1999',
                    'journal' => 'Program',
                    'pages' => '36',
                    ]
            ],
            // uppercase name interpreted as many initials
            [
                'source' => 'HOODA, SHALINI & JOOD, SUDESH. (2017). Effect of soaking and germination on nutrient and antinutrient contents of fenugreek (Trigonella foenum graecum L.). Journal of Food Biochemistry. 27. 165 - 176. 10.1111/j.1745-4514.2003.tb00274.x. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Hooda, Shalini and Jood, Sudesh',
                    'year' => '2017',
                    'title' => 'Effect of soaking and germination on nutrient and antinutrient contents of fenugreek (Trigonella foenum graecum L.)',
                    'journal' => 'Journal of Food Biochemistry.',
                    'pages' => '165-176',
                    'volume' => '27',
                    'doi' => '10.1111/j.1745-4514.2003.tb00274.x',
                    ]
            ],
            // Starts with {\it; separator //.  (Original had no title; I added a dummy one.)
            [
                'source' => '\bibitem{Tluczykont} {\it Tluczykont M, Hampf D, Horns D, Spitschan D, Kuzmichev L, Prosin V, Spiering C and Wischnewski R} // No title // Astroparticle Physics 2014 {\bf 56}, 42–53 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Tluczykont, M. and Hampf, D. and Horns, D. and Spitschan, D. and Kuzmichev, L. and Prosin, V. and Spiering, C. and Wischnewski, R.',
                    'title' => 'No title',
                    'journal' => 'Astroparticle Physics',
                    'year' => '2014',
                    'volume' => '56',
                    'pages' => '42-53',
                    ]
            ],
            // author is name of organization
            [
                'source' => 'National Center for Biotechnology Information (2024). PubChem Compound Summary for CID 62648, Ammonium Persulfate. Retrieved May 10, 2024 from https://pubchem.ncbi.nlm.nih.gov/compound/Ammonium-Persulfate. ',
                'type' => 'online',
                'bibtex' => [
                    'title' => 'PubChem Compound Summary for CID 62648, Ammonium Persulfate',
                    'url' => 'https://pubchem.ncbi.nlm.nih.gov/compound/Ammonium-Persulfate',
                    'urldate' => 'May 10, 2024',
                    'author' => 'National Center for Biotechnology Information',
                    'year' => '2024',
                    ]
            ], 
            // authors not detected (others in conversion 3448: see file 1408-3448).
            [
                'source' => '1: Ferris JK, Lo BP, Barisano G. Modulation of the Association Between Corticospinal Tract Damage and Outcome After Stroke by White Matter Hyperintensities. Neurology. 2024 May 28;102(10):e209387. doi: 10.1212/WNL.0000000000209387. Epub 2024 May 3. PMID: 38701386. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'PMID: 38701386. Epub 2024 May 3.',
                    'doi' => '10.1212/WNL.0000000000209387',
                    'author' => 'Ferris, J. K. and Lo, B. P. and Barisano, G.',
                    'title' => 'Modulation of the Association Between Corticospinal Tract Damage and Outcome After Stroke by White Matter Hyperintensities',
                    'year' => '2024',
                    'month' => 'May',
                    'date' => '2024-05-28',
                    'journal' => 'Neurology.',
                    'volume' => '102',
                    'number' => '10',
                    'pages' => 'e209387',
                    ]
            ],
            // Still correct after adding Proc. IEEE to proceedings exceptions?
            [
                'source' => '\bibitem{deeprl19} K. Li,W.Ni, E. Tovar, and A. Jamalipour, “Deep q-learning-based resource management in UAV-assisted wireless powered IoT networks,” in Proc. IEEE Int. Conf. Commun., Dublin, Ireland, 2020, pp. 1–6. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'K. Li and W. Ni and E. Tovar and A. Jamalipour',
                    'title' => 'Deep q-learning-based resource management in UAV-assisted wireless powered IoT networks',
                    'year' => '2020',
                    'pages' => '1-6',
                    'booktitle' => 'Proc. IEEE Int. Conf. Commun., Dublin, Ireland, 2020',
                    ]
            ],
            // booktitle ended early; wrong year
            [
                'source' => '\bibitem{l1pruning}X. Liu, W. Xia and Z. Fan, "A Deep Neural Network Pruning Method Based on Gradient L1-norm," 2020 IEEE 6th International Conference on Computer and Communications (ICCC), Chengdu, China, 2020, pp. 2070-2074, doi: 10.1109/ICCC51575.2020.9345039. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/ICCC51575.2020.9345039',
                    'author' => 'X. Liu and W. Xia and Z. Fan',
                    'title' => 'A Deep Neural Network Pruning Method Based on Gradient L1-norm',
                    'pages' => '2070-2074',
                    'year' => '2020',
                    'booktitle' => '2020 IEEE 6th International Conference on Computer and Communications (ICCC), Chengdu, China',
                    ]
            ],
            //
            [
                'source' => '\bibitem{scheduling2}Yang, T., Chai, R., \& Zhang, L. (2020, May). Latency optimization-based joint task offloading and scheduling for multi-user MEC system. In 2020 29th Wireless and Optical Communications Conference (WOCC) (pp. 1-6). IEEE. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'month' => 'May',
                    'author' => 'Yang, T. and Chai, R. and Zhang, L.',
                    'year' => '2020',
                    'title' => 'Latency optimization-based joint task offloading and scheduling for multi-user MEC system',
                    'booktitle' => '2020 29th Wireless and Optical Communications Conference (WOCC)',
                    'pages' => '1-6',
                    'publisher' => 'IEEE',
                    ]
            ],
            // booktitle ended early; wrong type
            [
                'source' => '\bibitem{batch2}B. Liu, W. Shen, P. Li and X. Zhu, "Accelerate Mini-batch Machine Learning Training With Dynamic Batch Size Fitting," 2019 International Joint Conference on Neural Networks (IJCNN), Budapest, Hungary, 2019, pp. 1-8, doi: 10.1109/IJCNN.2019.8851944. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/IJCNN.2019.8851944',
                    'author' => 'B. Liu and W. Shen and P. Li and X. Zhu',
                    'title' => 'Accelerate Mini-batch Machine Learning Training With Dynamic Batch Size Fitting',
                    'year' => '2019',
                    'pages' => '1-8',
                    'booktitle' => '2019 International Joint Conference on Neural Networks (IJCNN), Budapest, Hungary',
                    ]
            ],
            // booktitle ended early
            [
                'source' => '\bibitem{allocation}B. Zhang and C. Wang, "Deep Reinforcement Learning-based Predictive Maintenance Task Offloading and Resource Allocation," 2023 IEEE 23rd International Conference on Communication Technology (ICCT), Wuxi, China, 2023, pp. 659-664, doi: 10.1109/ICCT59356.2023.10419793. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/ICCT59356.2023.10419793',
                    'author' => 'B. Zhang and C. Wang',
                    'title' => 'Deep Reinforcement Learning-based Predictive Maintenance Task Offloading and Resource Allocation',
                    'year' => '2023',
                    'pages' => '659-664',
                    'booktitle' => '2023 IEEE 23rd International Conference on Communication Technology (ICCT), Wuxi, China',
                    ]
            ],
            // New York included in title
            [
                'source' => 'Le Cam, L. (1986), Asymptotic Methods in Statistical Decision Theory, New York: Springer. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Le Cam, L.',
                    'year' => '1986',
                    'title' => 'Asymptotic Methods in Statistical Decision Theory',
                    'address' => 'New York',
                    'publisher' => 'Springer',
                    ]
            ],
            // title terminated too early, before India
            [
                'source' => 'Kulshrestha S., Awasthi A., Dabral S. K. (2013).Assessment of heavy metals in the industrial effluents, tube-wells and municipal supplied water of Dehradun, India. J Environ Sci Eng,:55(3):290-300. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kulshrestha, S. and Awasthi, A. and Dabral, S. K.',
                    'year' => '2013',
                    'title' => 'Assessment of heavy metals in the industrial effluents, tube-wells and municipal supplied water of Dehradun, India',
                    'journal' => 'J Environ Sci Eng',
                    'pages' => '290-300',
                    'volume' => '55',
                    'number' => '3',
                    ]
            ],
            // booktitle not detected 
            [
                'source' => 'Rubega, M., Pascucci, D., Queralt, J. R., Van Mierlo, P., Hagmann, P., Plomp, G. and Michel, C. M. (2019), Time-varying effective eeg source connectivity: The optimization of model parameters, in ‘2019 41st Annual International Conference of the IEEE Engineering in Medicine and Biology Society (EMBC)’, IEEE, pp. 6438–6441. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Rubega, M. and Pascucci, D. and Queralt, J. R. and Van Mierlo, P. and Hagmann, P. and Plomp, G. and Michel, C. M.',
                    'year' => '2019',
                    'title' => 'Time-varying effective eeg source connectivity: The optimization of model parameters',
                    'booktitle' => '2019 41st Annual International Conference of the IEEE Engineering in Medicine and Biology Society (EMBC)',
                    'publisher' => 'IEEE',
                    'pages' => '6438-6441',
                    ]
            ],
            // Another proceedings exception
            [
                'source' => '\bibitem {1} C. E. Shannon, “Communication in the presence of noise,” Proceedings of the IRE, vol. 37, Issue no. 1, pp. 10–21, 1949. doi:10.1109/JRPROC.1949.232969 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1109/JRPROC.1949.232969',
                    'author' => 'C. E. Shannon',
                    'title' => 'Communication in the presence of noise',
                    'year' => '1949',
                    'pages' => '10-21',
                    'volume' => '37',
                    'number' => '1',
                    'journal' => 'Proceedings of the IRE',
                    ]
            ],
            [
                'source' => '    6 Braasch, M.S.,“Isolation of GPS multipath and receiver tracking errors,” Journal of the Institute Navigation, vol. 41, pp. 415–434, 1995.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Braasch, M. S.',
                    'title' => 'Isolation of GPS multipath and receiver tracking errors',
                    'year' => '1995',
                    'journal' => 'Journal of the Institute Navigation',
                    'volume' => '41',
                    'pages' => '415-434',
                    ]
            ],
            [
                'source' => 'Treasure J, Duarte TA, Schmidt U. Eating disorders. Lancet 2020;395:899-911.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Treasure, J. and Duarte, T. A. and Schmidt, U.',
                    'title' => 'Eating disorders',
                    'year' => '2020',
                    'journal' => 'Lancet',
                    'volume' => '395',
                    'pages' => '899-911',
                    ]
            ],
            // No space between , and "
            [
                'source' => '\bibitem {14} Naushad Ansari,Anubha Gupta ,”Image Reconstruction using Matched Wavelet Estimated from Data Sensed Compressively using partial canonical Identity matrix” IEEE Transactions on Image Processing Vol26 No.8 ,2017 pp.3680-3695. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Naushad Ansari and Anubha Gupta',
                    'title' => 'Image Reconstruction using Matched Wavelet Estimated from Data Sensed Compressively using partial canonical Identity matrix',
                    'year' => '2017',
                    'journal' => 'IEEE Transactions on Image Processing',
                    'volume' => '26',
                    'number' => '8',
                    'pages' => '3680-3695',
                    ]
            ],
            //"India" was detected as first word of journal name
            [
                'source' => 'Muthu, C., Ayyanar, M., Raja, N., & Ignacimuthu, S. (2006). Medicinal plants used by traditional healers in Kancheepuram District of Tamil Nadu, India. Journal of Ethnobiology and ethnomedicine, 2(1), 1-10. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Muthu, C. and Ayyanar, M. and Raja, N. and Ignacimuthu, S.',
                    'year' => '2006',
                    'title' => 'Medicinal plants used by traditional healers in Kancheepuram District of Tamil Nadu, India',
                    'journal' => 'Journal of Ethnobiology and ethnomedicine',
                    'volume' => '2',
                    'number' => '1',
                    'pages' => '1-10',
                    ]
            ],
            // Not recognized as phdthesis
            [
                'source' => 'Khanal, Y. (2008). Valuation of carbon sequestration and water supply services in community forests of Palpa district, Nepal (Doctoral dissertation, Tribhuvan University,)., Kathmanndu: Tribhuvan University. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Khanal, Y.',
                    'year' => '2008',
                    'title' => 'Valuation of carbon sequestration and water supply services in community forests of Palpa district, Nepal',
                    'school' => 'Tribhuvan University',
                    'note' => 'Kathmanndu: Tribhuvan University',
                    ]
            ],
            // \enquote used for quoted string
            [
                'source' => '	\bibitem{ref:Atabaki16} 	M.~M. Atabaki, N. Yazdian, J. Ma, R. Kovacevic. 	\enquote{High power laser welding of think steel plates in a horizontal butt joint configuration}. 	\newblock In: {\em Optics \& Laser Technology}, vol. 83, pp. 1--12.  	\newblock 2016. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'M. M. Atabaki and N. Yazdian and J. Ma and R. Kovacevic',
                    'title' => 'High power laser welding of think steel plates in a horizontal butt joint configuration',
                    'year' => '2016',
                    'journal' => 'Optics \& Laser Technology',
                    'volume' => '83',
                    'pages' => '1-12',
                    ]
            ],
            // Issue abbreviated as iss:
            [
                'source' => 'Yeo, R., (2002) "From individual to team learning: practical perspectives on the learning organisation", Team Performance Management, Vol. 8 Iss: 7/8, pp.157 – 170. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yeo, R.',
                    'year' => '2002',
                    'title' => 'From individual to team learning: practical perspectives on the learning organisation',
                    'journal' => 'Team Performance Management',
                    'pages' => '157-170',
                    'volume' => '8',
                    'number' => '7/8',
                    ]
            ],
            // Authors stopped too early
            [
                'source' => 'Allison, M., Kaye, J. (2005). Strategic Planning for Nonprofit Organizations. ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'Allison, M. and Kaye, J.',
                    'title' => 'Strategic Planning for Nonprofit Organizations',
                    'year' => '2005',
                    ]
            ],
            // No space between , and "
            [
                'source' => '\bibitem {14} Naushad Ansari,Anubha Gupta ,”Image Reconstruction using Matched Wavelet Estimated from Data Sensed Compressively using partial canonical Identity matrix” IEEE Transactions on Image Processing Vol26 No.8 ,2017 pp.3680-3695. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Naushad Ansari and Anubha Gupta',
                    'title' => 'Image Reconstruction using Matched Wavelet Estimated from Data Sensed Compressively using partial canonical Identity matrix',
                    'year' => '2017',
                    'journal' => 'IEEE Transactions on Image Processing',
                    'volume' => '26',
                    'number' => '8',
                    'pages' => '3680-3695',
                    ]
            ],
            // Title ended early; date at end of booktitle
            [
                'source' => '[10] D.-A. Clevert, T. Unterthiner, and S. Hochreiter. Fast and accurate deep network learning by Exponential Linear Units (ELUs). In 4th International Conference on Learning Representations (ICLR), May 24, 2016.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'D.-A. Clevert and T. Unterthiner and S. Hochreiter',
                    'title' => 'Fast and accurate deep network learning by Exponential Linear Units (ELUs)',
                    'year' => '2016',
                    'booktitle' => '4th International Conference on Learning Representations (ICLR), May 24, 2016',
                    ]
            ],
            // Date at end of booktitle
            [
                'source' => '[16] S. Huang et al. DSANet: Dual self-attention network for multivariate time series forecasting. In 28th ACM International Conference on Information and Knowledge Management (CIKM), November 3-7, 2019.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'S. Huang and others',
                    'title' => 'DSANet: Dual self-attention network for multivariate time series forecasting',
                    'year' => '2019',
                    'booktitle' => '28th ACM International Conference on Information and Knowledge Management (CIKM), November 3-7, 2019',
                    ]
            ],
            // Date at end of booktitle
            [
                'source' => '[21] D. P. Kingma and J. Ba. Adam: A method for stochastic optimization. In 3rd International Conference on Learning Representations (ICLR) May 7-9, 2015.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'D. P. Kingma and J. Ba',
                    'title' => 'Adam: A method for stochastic optimization',
                    'year' => '2015',
                    'booktitle' => '3rd International Conference on Learning Representations (ICLR) May 7-9, 2015',
                    ]
            ],
            // Date at end of booktitle
            [
                'source' => '[48] Y. Wu et al. Deep learning for epidemiological predictions. In 41st International ACM SIGIR Conference on Research & Development in Information Retrieval (SIGIR), July 8-12, 2018.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Y. Wu and others',
                    'title' => 'Deep learning for epidemiological predictions',
                    'year' => '2018',
                    'booktitle' => '41st International ACM SIGIR Conference on Research & Development in Information Retrieval (SIGIR), July 8-12, 2018',
                    ]
            ],
            // date at end of booktitle
            [
                'source' => '\bibitem {18} Xuan, Y.; Yang, C. "2sER-VGSR-Net: A Two-Stage Enhancement Reconstruction Based On Video Group Sparse Representation Network For Compressed Video Sensing" In Proceedings of the 2020 IEEE International Conference on Multimedia and Expo (ICME), London, UK, 6–10 July 2020; pp. 1–6. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Xuan, Y. and Yang, C.',
                    'title' => '2sER-VGSR-Net: A Two-Stage Enhancement Reconstruction Based On Video Group Sparse Representation Network For Compressed Video Sensing',
                    'year' => '2020',
                    'pages' => '1-6',
                    'booktitle' => 'Proceedings of the 2020 IEEE International Conference on Multimedia and Expo (ICME), London, UK, 6--10 July 2020',
                    ]
            ],
            // date at end of booktitle
            [
                'source' => '\bibitem {19} Mousavi, A.; Patel, A.B.; Baraniuk, R.G. A deep learning approach to structured signal recovery. In Proceedings of the 2015 53rd Annual Allerton Conference on Communication, Control, and Computing (Allerton), Monticello, IL, USA, 29 September–2 October 2015; pp. 1336–1343. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Mousavi, A. and Patel, A. B. and Baraniuk, R. G.',
                    'title' => 'A deep learning approach to structured signal recovery',
                    'year' => '2015',
                    'pages' => '1336-1343',
                    'booktitle' => 'Proceedings of the 2015 53rd Annual Allerton Conference on Communication, Control, and Computing (Allerton), Monticello, IL, USA, 29 September--2 October 2015',
                    ]
            ],
            // date at end of booktitle
            [
                'source' => '\bibitem {20} Kulkarni, K.; Lohit, S.; Turaga, P.; Kerviche, R.; Ashok, A. ReconNet: Non-iterative reconstruction of images from compressively sensed measurements. In Proceedings of the IEEE Conference on Computer Vision and Pattern Recognition (CVPR), Las Vegas,NV, USA, 27–30 June 2016; pp. 449–458. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Kulkarni, K. and Lohit, S. and Turaga, P. and Kerviche, R. and Ashok, A.',
                    'title' => 'ReconNet: Non-iterative reconstruction of images from compressively sensed measurements',
                    'year' => '2016',
                    'booktitle' => 'Proceedings of the IEEE Conference on Computer Vision and Pattern Recognition (CVPR), Las Vegas, NV, USA, 27--30 June 2016',
                    'pages' => '449-458',
                    ]
            ],
            // year at end of booktitle
            [
                'source' => '\bibitem{30}Yulun Zhang, Yapeng Tian, Yu Kong, Bineng Zhong, Yun Fu; "Residual dense network for image super-resolution", in Proceedings of the IEEE Conference on Computer Vision and Pattern Recognition (CVPR), 2018, pp. 2472-2481 ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Yulun Zhang and Yapeng Tian and Yu Kong and Bineng Zhong and Yun Fu',
                    'title' => 'Residual dense network for image super-resolution',
                    'booktitle' => 'Proceedings of the IEEE Conference on Computer Vision and Pattern Recognition (CVPR), 2018',
                    'year' => '2018',
                    'pages' => '2472-2481',
                    ]
            ],
            // inproceedings correctly parsed
            [
                'source' => '\bibitem{WsaaA} Bell A, Solano-Kamaiko I, Nov O, Stoyanovich J (2022) It\'s Just Not That Simple: An Empirical Study of the Accuracy-Explainability Trade-off in Machine Learning for Public Policy. In: Proceedings of the 2022 ACM Conference on Fairness, Accountability, and Transparency. Association for Computing Machinery, New York, NY, USA, pp 248–266 ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Bell, A. and Solano-Kamaiko, I. and Nov, O. and Stoyanovich, J.',
                    'year' => '2022',
                    'title' => 'It\'s Just Not That Simple: An Empirical Study of the Accuracy-Explainability Trade-off in Machine Learning for Public Policy',
                    'pages' => '248-266',
                    'publisher' => 'Association for Computing Machinery',
                    'address' => 'New York, NY, USA',
                    'booktitle' => 'Proceedings of the 2022 ACM Conference on Fairness, Accountability, and Transparency',
                    ]
            ],
            // journal included in title
            [
                'source' => 'Bonadonna P, Zanotti R, Müller U. Mastocytosis and insect venom allergy. Curr Opin Allergy Clin Immunol. 2010;10:347–353. doi: 10.1097/ACI.0b013e32833b280c ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1097/ACI.0b013e32833b280c',
                    'author' => 'Bonadonna, P. and Zanotti, R. and Müller, U.',
                    'year' => '2010',
                    'volume' => '10',
                    'pages' => '347-353',
                    'title' => 'Mastocytosis and insect venom allergy',
                    'journal' => 'Curr Opin Allergy Clin Immunol',
                    ]
            ],
            // Authors ended early
            [
                'source' => 'Türkmen İ, Bingöl AF, Tortum A, Demirboğa R, Gül R. Properties of pumice aggregate concretes at elevated temperatures and comparison with ANN models. Fire Mater 2017;41:142–153. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'T{\"u}rkmen, {\.I}. and Bing{\"o}l, A. F. and Tortum, A. and Demirboğa, R. and G{\"u}l, R.',
                    'title' => 'Properties of pumice aggregate concretes at elevated temperatures and comparison with ANN models',
                    'year' => '2017',
                    'journal' => 'Fire Mater',
                    'volume' => '41',
                    'pages' => '142-153',
                    ]
            ],
            // day range picked up as pages
            [
                'source' => 'Phan LT, Carino NJ. Fire performance of high strength concrete: Research needs. Proceedings of ASCE/SEI Structures Congress, May 8-10, Philadelphia, PA, 2000. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Phan, L. T. and Carino, N. J.',
                    'title' => 'Fire performance of high strength concrete: Research needs',
                    'year' => '2000',
                    'booktitle' => 'Proceedings of ASCE/SEI Structures Congress, May 8-10, Philadelphia, PA',
                    ]
            ],
            // et. al.
            [
                'source' => 'Bauermeister, J., Dominguez Islas, C., Jiao, Y., Tingler, R., Brown, E., Zemanek, J., Giguere, R., Balan, I., Johnson, S., Macagna, N., Lucas, J., Rose, M., Jacobson, C., Collins, C., Livant, E., Singh, D., Ho, K., Hoesley, C., Liu, A., et. al. (2023). Safety, acceptability and adherence of three rectal microbicide placebo formulations among young sexual and gender minorities who engage in receptive anal intercourse (MTN-035). AIDS and Behavior, 18(4), e0284339. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bauermeister, J. and Dominguez Islas, C. and Jiao, Y. and Tingler, R. and Brown, E. and Zemanek, J. and Giguere, R. and Balan, I. and Johnson, S. and Macagna, N. and Lucas, J. and Rose, M. and Jacobson, C. and Collins, C. and Livant, E. and Singh, D. and Ho, K. and Hoesley, C. and Liu, A. and others',
                    'title' => 'Safety, acceptability and adherence of three rectal microbicide placebo formulations among young sexual and gender minorities who engage in receptive anal intercourse (MTN-035)',
                    'journal' => 'AIDS and Behavior',
                    'year' => '2023',
                    'volume' => '18',
                    'number' => '4',
                    'pages' => 'e0284339',
                    ]
            ],
            // title ended in middle of U.S.
            [
                'source' => 'Gonzales, L. D., Hall, K., Benton, A., Kanhai, D., & Núñez, A. M. (2021, March 11). Comfort over Change: a Case Study of Diversity and Inclusivity Efforts in U.S. Higher Education. Innovative Higher Education, 46(4), 445-460. https://doi.org/10.1007/s10755-020-09541-7 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s10755-020-09541-7',
                    'month' => 'March',
                    'author' => 'Gonzales, L. D. and Hall, K. and Benton, A. and Kanhai, D. and Núñez, A. M.',
                    'year' => '2021',
                    'title' => 'Comfort over Change: a Case Study of Diversity and Inclusivity Efforts in U. S. Higher Education',
                    'journal' => 'Innovative Higher Education',
                    'volume' => '46',
                    'number' => '4',
                    'pages' => '445-460',
                    'date' => '2021-03-11',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // title ended in middle of U.S.
            [
                'source' => 'Hodrick, R. J., and E. C. Prescott. 1997. Postwar U.S. business cycles: An empirical investigation. Journal of Money, Credit, and Banking 29: 1–16.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Hodrick, R. J. and E. C. Prescott',
                    'year' => '1997',
                    'title' => 'Postwar U. S. business cycles: An empirical investigation',
                    'journal' => 'Journal of Money, Credit, and Banking',
                    'volume' => '29',
                    'pages' => '1-16',
                    ]
            ],
  			// Last initial included in title (because accented?)
			[
                'source' => '\bibitem{SAINZPARDODIAZ2023142}Sáinz-Pardo Díaz, J. \& López García, Á. Study of the performance and scalability of federated learning for medical imaging with intermittent clients. {\em Neurocomputing}. \textbf{518} pp. 142-154 (2023), https://www.sciencedirect.com/science/article/pii/S0925231222013844 ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://www.sciencedirect.com/science/article/pii/S0925231222013844',
                    'author' => 'Sáinz-Pardo Díaz, J. and López García, Á.',
                    'title' => 'Study of the performance and scalability of federated learning for medical imaging with intermittent clients',
                    'year' => '2023',
                    'journal' => 'Neurocomputing',
                    'volume' => '518',
                    'pages' => '142-154',
                    ],
					'char_encoding' => 'utf8leave'
            ],
			// classified as unpublished
			[
                'source' => '\bibitem{Nagaosa2013} N. Nagaosa and Y. Tokura, Topological properties and dynamics of magnetic skyrmions, Nature Nanotechnology \textbf{8}, 899-911 (2013). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'N. Nagaosa and Y. Tokura',
                    'title' => 'Topological properties and dynamics of magnetic skyrmions',
                    'journal' => 'Nature Nanotechnology',
                    'year' => '2013',
                    'volume' => '8',
                    'pages' => '899-911',
                    ]
            ],
			// classified as unpublished
			 [
                'source' => '\bibitem{Bogdanov-Nature2006} U. K. Rößler, N. Bogdanov, and C. Pfleiderer, Spontaneous skyrmion ground states in magnetic metals, Nature \textbf{442}, 797-801 (2006). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'U. K. R{\"o}{\ss}ler and N. Bogdanov and C. Pfleiderer',
                    'title' => 'Spontaneous skyrmion ground states in magnetic metals',
                    'journal' => 'Nature',
                    'year' => '2006',
                    'volume' => '442',
                    'pages' => '797-801',
                    ]
            ],
   			// author's name interpreted as initials
			[
                'source' => 'KUM, C. W.; SATO, T.; GUO, J.; LIU, K.; BUTLER, D. 2018. A novel media properties-based material removal rate model for magnetic field-assisted finishing. International Journal of Mechanical Sciences, v. 141, p. 189–197. . ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kum, C. W. and Sato, T. and Guo, J. and Liu, K. and Butler, D.',
                    'year' => '2018',
                    'title' => 'A novel media properties-based material removal rate model for magnetic field-assisted finishing',
                    'journal' => 'International Journal of Mechanical Sciences',
                    'volume' => '141',
                    'pages' => '189-197',
                    ]
            ],
			// should recognize '?' as page separator?
			[
                'source' => '	Le AD, Zhou B, Shiu HR, Lee CI, Chang WC. Numerical simulation and experimental validation of liquid water behaviors in a proton exchange membrane fuel cell cathode with serpentine channels. J Power Sources 2010;195:7302?15. doi:10.1016/j.jpowsour.2010.05.045. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.jpowsour.2010.05.045',
                    'author' => 'Le, A. D. and Zhou, B. and Shiu, H. R. and Lee, C. I. and Chang, W. C.',
                    'title' => 'Numerical simulation and experimental validation of liquid water behaviors in a proton exchange membrane fuel cell cathode with serpentine channels',
                    'year' => '2010',
                    'journal' => 'J Power Sources',
                    'pages' => '7302-15',
                    'volume' => '195',
                    ]
            ],
			// issue number has slash
			[
                'source' => 'Bailey, Don Clifford. 1960 ‘Early Japanese Lexicography’. Monumenta Nipponica, Vol. 16, No. 1/2 (April-July), pp. 1-52. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bailey, Don Clifford',
                    'title' => 'Early Japanese Lexicography',
                    'journal' => 'Monumenta Nipponica',
                    'year' => '1960',
                    'volume' => '16',
                    'number' => '1/2 (April-July)',
                    'pages' => '1-52',
                    ]
            ],
   			// Many proceedings, including the following, in 1463-3663-source.txt
			// year should not be included at end of booktitle
			[
                'source' => 'Michael Braun, Anja Mainz, Ronee Chadowitz, Bastian Pfleging, and Florian Alt. At your service: Designing voice assistant personalities to improve automotive user interfaces. In Proceedings of the 2019 CHI Conference on Human Factors in Computing Systems , CHI ’19, page 1–11, 2019. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Michael Braun and Anja Mainz and Ronee Chadowitz and Bastian Pfleging and Florian Alt',
                    'title' => 'At your service: Designing voice assistant personalities to improve automotive user interfaces',
                    'year' => '2019',
                    'pages' => '1-11',
                    'booktitle' => 'Proceedings of the 2019 CHI Conference on Human Factors in Computing Systems, CHI \'19',
                    ]
            ], 
			// year should not be included at end of booktitle
			[
                'source' => 'Seungbeom Choi, Sunho Lee, Yeonjae Kim, Jongse Park, Youngjin Kwon, and Jaehyuk Huh.  Serving heterogeneous machine learning models on Multi-GPU servers with Spatio-Temporal sharing. In Proceedings of 2022 USENIX Annual Technical Conference (USENIX ATC 22) , pages 199–216, 2022. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Seungbeom Choi and Sunho Lee and Yeonjae Kim and Jongse Park and Youngjin Kwon and Jaehyuk Huh',
                    'title' => 'Serving heterogeneous machine learning models on Multi-GPU servers with Spatio-Temporal sharing',
                    'year' => '2022',
                    'pages' => '199-216',
                    'booktitle' => 'Proceedings of 2022 USENIX Annual Technical Conference (USENIX ATC 22)',
                    ]
            ],
			// year not included at end of booktitle --- which is correct.
			[
                'source' => 'Jianfeng Gu, Yichao Zhu, Puxuan Wang, Mohak Chadha, and Michael Gerndt. Fast-gshare: Enabling efficient spatio-temporal gpu sharing in serverless computing for deep learning inference. In Proceedings of the 52nd International Conference on Parallel Processing , ICPP ’23, page 635–644, 2023. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Jianfeng Gu and Yichao Zhu and Puxuan Wang and Mohak Chadha and Michael Gerndt',
                    'title' => 'Fast-gshare: Enabling efficient spatio-temporal gpu sharing in serverless computing for deep learning inference',
                    'year' => '2023',
                    'pages' => '635-644',
                    'booktitle' => 'Proceedings of the 52nd International Conference on Parallel Processing, ICPP \'23',
                    ]
            ],
			// year included at end of booktitle --- correct?
			[
                'source' => 'Baolin  Li,  Siddharth  Samsi,  Vijay  Gadepally,  and  Devesh  Tiwari. Clover: Toward sustainable ai with carbon-aware machine learning inference service.  In Proceedings of the International Conference for High Performance Computing, Networking, Storage and Analysis , SC ’23, 2023. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Baolin Li and Siddharth Samsi and Vijay Gadepally and Devesh Tiwari',
                    'title' => 'Clover: Toward sustainable ai with carbon-aware machine learning inference service',
                    'year' => '2023',
                    'booktitle' => 'Proceedings of the International Conference for High Performance Computing, Networking, Storage and Analysis, SC \'23, 2023',
                    ]
            ],
			// year not included at end of booktitle --- which is correct.
			[
                'source' => 'Suyi Li, Luping Wang, Wei Wang, Yinghao Yu, and Bo Li.  George: Learning to place long-lived containers in large clusters with operation constraints. In Proceedings of the ACM Symposium on Cloud Computing , SoCC ’21, page 258–272, 2021. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Suyi Li and Luping Wang and Wei Wang and Yinghao Yu and Bo Li',
                    'title' => 'George: Learning to place long-lived containers in large clusters with operation constraints',
                    'year' => '2021',
                    'pages' => '258-272',
                    'booktitle' => 'Proceedings of the ACM Symposium on Cloud Computing, SoCC \'21',
                    ]
            ],
			// two commas between booktitle and city
			[
                'source' => 'Wencong Xiao, Romil Bhardwaj, Ramachandran Ramjee, Muthian Sivathanu, Nipun Kwatra, Zhenhua Han, Pratyush Patel, Xuan Peng, Hanyu Zhao, Quanlu Zhang, Fan Yang, and Lidong Zhou. Gandiva: Introspective cluster scheduling for deep learning. In Proceedings of 13th USENIX Symposium on Operating Systems Design and Implementation (OSDI 18) , pages 595–610, Carlsbad, CA, 2018. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Wencong Xiao and Romil Bhardwaj and Ramachandran Ramjee and Muthian Sivathanu and Nipun Kwatra and Zhenhua Han and Pratyush Patel and Xuan Peng and Hanyu Zhao and Quanlu Zhang and Fan Yang and Lidong Zhou',
                    'title' => 'Gandiva: Introspective cluster scheduling for deep learning',
                    'year' => '2018',
                    'pages' => '595-610',
                    'booktitle' => 'Proceedings of 13th USENIX Symposium on Operating Systems Design and Implementation (OSDI 18), Carlsbad, CA',
                    ]
            ],
			// extra punctuation in booktitle
			[
                'source' => 'Lekchiri, A. (1996). Soil testing and leaf analysis in Morocco. Proc. 8th Int. Soc. Citriculture, (pp. 1286-1292). Sun City, South Africa. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Lekchiri, A.',
                    'year' => '1996',
                    'title' => 'Soil testing and leaf analysis in Morocco',
                    'pages' => '1286-1292',
                    'booktitle' => 'Proc. 8th Int. Soc. Citriculture, Sun City, South Africa',
                    ]
            ],
			// missing closing parenthesis at end of booktitle
			[
                'source' => 'Chengliang Zhang, Minchen Yu, Wei Wang, and Feng Yan. MArk: Exploiting cloud services for Cost-Effective, SLO-Aware machine learning inference serving. In 2019 USENIX Annual Technical Conference (USENIX ATC 19) , pages 1049–1062, 2019. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Chengliang Zhang and Minchen Yu and Wei Wang and Feng Yan',
                    'title' => 'MArk: Exploiting cloud services for Cost-Effective, SLO-Aware machine learning inference serving',
                    'year' => '2019',
                    'pages' => '1049-1062',
                    'booktitle' => '2019 USENIX Annual Technical Conference (USENIX ATC 19)',
                    ]
            ],
   			// conference date identified as page range
			[
                'source' => ' S. Garzia, M.A. Scarpolini, K. Capellini, V. Positano, F. Cademartiri, S. Celi. "Deep learning thoracic aorta segmentation for feature extraction and hemodynamic analysis from 3D PC-MRI". 28th Congress of the European Society of Biomechanics, July 9-12, 2023, Maastricht, the Netherlands. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'S. Garzia and M. A. Scarpolini and K. Capellini and V. Positano and F. Cademartiri and S. Celi',
                    'title' => 'Deep learning thoracic aorta segmentation for feature extraction and hemodynamic analysis from 3D PC-MRI',
                    'year' => '2023',
                    'booktitle' => '28th Congress of the European Society of Biomechanics, July 9-12, 2023, Maastricht, the Netherlands',
                    ]
            ],
			// conference date identified as page range
			[
                'source' => 'K. Capellini, E. Gasparotti, E. Vignali,B. M. Fanni, U. Cella, E. Costa, M.E. Biancolini, S. Celi. "An image-based CFD and RBF mesh morphing approach: an alternative for standard FSI technique". 26th Congress of the European Society of Biomechanics, July 11-14, 2021, Milan, Italy. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'K. Capellini and E. Gasparotti and E. Vignali and B. M. Fanni and U. Cella and E. Costa and M. E. Biancolini and S. Celi',
                    'title' => 'An image-based CFD and RBF mesh morphing approach: an alternative for standard FSI technique',
                    'year' => '2021',
                    'booktitle' => '26th Congress of the European Society of Biomechanics, July 11-14, 2021, Milan, Italy',
                    ]
            ],
			// booktitle not correctly interpreted
			[
                'source' => 'J. Singh, K. Capellini, B.M. Fanni, A. Mariotti, M.V. Salvetti, S. Celi. "Numerical simulations to predict the onset of atherosclerotic plaques in carotid arteries".WECM\'23 - 2nd Workshop on Experimental and Computational Mechanics, September 20-22, 2023, Pisa, Italy. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'J. Singh and K. Capellini and B. M. Fanni and A. Mariotti and M. V. Salvetti and S. Celi',
                    'title' => 'Numerical simulations to predict the onset of atherosclerotic plaques in carotid arteries',
                    'year' => '2023',
                    'booktitle' => 'WECM\'23 - 2nd Workshop on Experimental and Computational Mechanics, September 20-22, 2023, Pisa, Italy',
                    ]
            ],
			// booktitle not correctly identified
			[
                'source' => 'M.A. Scarpolini, M. Mazzoli, S. Garzia, A. Clemente, A. Monteleone,K. Capellini, S. Celi. "Deploying digital twins of the cardiovascular system in clinics: a deep learning-based automatized framework".XI Annual Meeting of the Italian Chapter of the European Society of Biomechanics, October 6-7, 2022, Massa, Italy. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'M. A. Scarpolini and M. Mazzoli and S. Garzia and A. Clemente and A. Monteleone and K. Capellini and S. Celi',
                    'title' => 'Deploying digital twins of the cardiovascular system in clinics: a deep learning-based automatized framework',
                    'year' => '2022',
                    'booktitle' => 'XI Annual Meeting of the Italian Chapter of the European Society of Biomechanics, October 6-7, 2022, Massa, Italy',
                    ]
            ],
			// booktitle problem
			[
                'source' => 'B.M. Fanni, E. Gasparotti, K. Capellini, C. Capelli, E. Vignali, V. Positano, S. Celi. "A novel image-based formulation for enhanced patient-specific in silico simulations of cardiovascular interventions". International CAE conference and exhibition. October 28-29, 2019, Vicenza, Italy. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'B. M. Fanni and E. Gasparotti and K. Capellini and C. Capelli and E. Vignali and V. Positano and S. Celi',
                    'title' => 'A novel image-based formulation for enhanced patient-specific in silico simulations of cardiovascular interventions',
                    'year' => '2019',
                    'booktitle' => 'International CAE conference and exhibition. October 28-29, 2019, Vicenza, Italy',
                    ]
            ],
			// address repeated at end of editor name
            [
                'source' => 'Beghi, Clemente. 2011. “The Dissemination of Esoteric Scriptures in Eighth Century Japan.” In Esoteric Buddhism and the Tantras in East Asia, 661-682. Edited by Charles Orzech, Leiden: Brill. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Beghi, Clemente',
                    'title' => 'The Dissemination of Esoteric Scriptures in Eighth Century Japan',
                    'year' => '2011',
                    'pages' => '661-682',
                    'editor' => 'Charles Orzech',
                    'address' => 'Leiden',
                    'publisher' => 'Brill',
                    'booktitle' => 'Esoteric Buddhism and the Tantras in East Asia',
                    ]
            ],
			// address repeated at end of editor name
			[
                'source' => 'Gardiner, David L. 1999. “Japan’s First Shingon Ceremony.” In Religions of Japan in Practice. Edited by George J. Tanabe, Jr. Princeton, NJ: Princeton University Press. 153-158. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Gardiner, David L.',
                    'title' => 'Japan\'s First Shingon Ceremony',
                    'year' => '1999',
                    'pages' => '153-158',
                    'editor' => 'Tanabe, Jr., George J.',
                    'address' => 'Princeton, NJ',
                    'publisher' => 'Princeton University Press',
                    'booktitle' => 'Religions of Japan in Practice',
                    ]
            ],
			// date after booktitle
			[
                'source' => 'Zhang, B., Liang, P., Zhou, X., Ahmad, A. and Waseem, M. (2023), “Practices and Challenges of Using GitHub Copilot: An Empirical Study”, in Proceedings of the 35th International Conference on Software Engineering and Knowledge Engineering, July 1-10, 2023, KSI Research Inc, pp. 124–129. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Zhang, B. and Liang, P. and Zhou, X. and Ahmad, A. and Waseem, M.',
                    'title' => 'Practices and Challenges of Using GitHub Copilot: An Empirical Study',
                    'year' => '2023',
                    'pages' => '124-129',
                    'booktitle' => 'Proceedings of the 35th International Conference on Software Engineering and Knowledge Engineering, July 1-10, 2023, KSI Research Inc',
                    ]
            ],
			// urldate not correctly detected
			[
                'source' => 'Thèse.fr. (2016). Système hybride d\'adaptation dans les systèmes de recommandation. Récupéré le 24 avril 2024, de https://theses.fr/2016SACLC050 ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://theses.fr/2016SACLC050',
                    'author' => 'Thèse.fr',
                    'year' => '2016',
                    'title' => 'Système hybride d\'adaptation dans les systèmes de recommandation',
					'urldate' => '24 avril 2024',
                ],
                'language' => 'fr',
                'char_encoding' => 'utf8leave',
            ],
			// date not detected correctly
            [
                'source' => 'Negre, E. (2018, 20 Septembre). Les systèmes de recommandation : une catégorisation. Récupéré le 24 avril 2024, de https://interstices.info/les-systemes-de-recommandation-categorisation/ ',
                'type' => 'online',
                'bibtex' => [
                    'title' => 'Les systèmes de recommandation : une catégorisation',
                    'url' => 'https://interstices.info/les-systemes-de-recommandation-categorisation/',
                    'author' => 'Negre, E.',
                    'year' => '2018',
                    'month' => 'septembre',
                    'date' => '2018-09-20',
                    'urldate' => '24 avril 2024',
                    ],
					'language' => 'fr',
                    'char_encoding' => 'utf8leave',
            ],
			// year included in publisher name
			[
                'source' => 'Placher, William C. 1997. ‘Postliberal Theology’, in The Modern Theologians: An Introduction to Christian Theology in the Twentieth Century. Edited by David F. Ford. Malden, MA: Blackwell, 1997, 343–356.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Placher, William C.',
                    'title' => 'Postliberal Theology',
                    'year' => '1997',
                    'pages' => '343-356',
                    'editor' => 'David F. Ford',
                    'address' => 'Malden, MA',
                    'publisher' => 'Blackwell',
                    'booktitle' => 'The Modern Theologians: An Introduction to Christian Theology in the Twentieth Century',
                    ]
            ],
			// book wrongly classified as online            
			[
                'source' => 'James, Vanus and Lloyd Taylor. 2021. Competing for Development: Perspectives on Self-Sustaining Growth for Caribbean Economies. Port of Spain: Tapia House Group. Available at https://competingfordevelopment.com.  ',
                'type' => 'book',
                'bibtex' => [
                    'address' => 'Port of Spain',
					'publisher' => 'Tapia House Group',
                    'url' => 'https://competingfordevelopment.com',
                    'author' => 'James, Vanus and Lloyd Taylor',
                    'year' => '2021',
                    'title' => 'Competing for Development: Perspectives on Self-Sustaining Growth for Caribbean Economies',
                    ]
            ],
			// book wrongly classified as online
			[
                'source' => 'Clark, J. B. (1899). The Distribution of Wealth: A Theory of Wages, Interest, and Profits. New York: MacMillan (1908 Edition). Available at https://oll.libertyfund.org/title/clark-the-distribution-of-wealth-a-theory-of-wages-interest-and-profits.  ',
                'type' => 'book',
                'bibtex' => [
                    'address' => 'New York',
					'publisher' => 'MacMillan',
					'edition' => '1908',
                    'url' => 'https://oll.libertyfund.org/title/clark-the-distribution-of-wealth-a-theory-of-wages-interest-and-profits',
                    'author' => 'Clark, J. B.',
                    'year' => '1899',
                    'title' => 'The Distribution of Wealth: A Theory of Wages, Interest, and Profits',
                    ]
            ],
   			// repetition of doi; failure to get volume etc.
			[
                'source' => '[6]	S. Firdous, G. A. Wagai, and K. Sharma, "A survey on diabetes risk prediction using machine learning approaches," J. Family Med. Prim. Care, vol. 11, no. 11, pp. 6929–6934, Nov. 2022. doi: 10.4103/jfmpc.jfmpc_502_22. [Online]. Available: https://doi.org/10.4103/jfmpc.jfmpc_502_22. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.4103/jfmpc.jfmpc\_502\_22',
                    'url' => 'https://doi.org/10.4103/jfmpc.jfmpc_502_22',
                    'author' => 'S. Firdous and G. A. Wagai and K. Sharma',
                    'title' => 'A survey on diabetes risk prediction using machine learning approaches',
					'journal' => 'J. Family Med. Prim. Care',
					'volume' => '11',
					'number' => '11',
					'pages' => '6929-6934',
                    'year' => '2022',
                    'month' => 'November',
                    ]
            ],
			// volume - number format
			[
                'source' => '[140]    A.P. Adedigba and A.R. Zubair, “Performance Comparison of Blood Glucose Controllers for Diabetic Patients”, International Journal of Computer Applications, Volume 184 – No.8, April 2022 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'A. P. Adedigba and A. R. Zubair',
                    'title' => 'Performance Comparison of Blood Glucose Controllers for Diabetic Patients',
                    'year' => '2022',
                    'month' => 'April',
                    'journal' => 'International Journal of Computer Applications',
                    'volume' => '184',
					'number' => '8',
                    ]
            ],
			// Not detected as PhD thesis
			[
                'source' => '[9] Ben Mhenni, A. A. (2007). Influence de l\'état de surface et du serrage sur les outils assemblés par frettage [Thèse de doctorat, École Polytechnique de Montréal]. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Ben Mhenni, A. A.',
                    'title' => 'Influence de l\'état de surface et du serrage sur les outils assemblés par frettage',
                    'year' => '2007',
                    'school' => 'École Polytechnique de Montréal',
                    ],
					'language' => 'fr',
                    'char_encoding' => 'utf8leave',
            ],
			// wrongly classified as inproceedings
			[
                'source' => 'Ford, I. J., Statistical Mechanics of Nucleation: A Review. Proc. Inst. Mech. Eng. C 2004, 218, 883–899. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ford, I. J.',
                    'title' => 'Statistical Mechanics of Nucleation: A Review',
                    'journal' => 'Proc. Inst. Mech. Eng. C',
                    'year' => '2004',
                    'volume' => '218',
                    'pages' => '883-899',
                    ]
            ],
			// title included in author string
			[
                'source' => 'Clausen, C. H.; Jensen, J.; Castillo, J.; Dimaki, M.; Svendsen, W. E., Qualitative Mapping of Structurally Different Dipeptide Nanotubes. Nano Lett. 2008, 8, 4066–4069. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Clausen, C. H. and Jensen, J. and Castillo, J. and Dimaki, M. and Svendsen, W. E.',
					'title' => 'Qualitative Mapping of Structurally Different Dipeptide Nanotubes',
                    'journal' => 'Nano Lett',
                    'year' => '2008',
                    'volume' => '8',
                    'pages' => '4066-4069',
                    ]
            ],
			// J included at end of title
			[
                'source' => 'Jacobs, W. M.; Reinhardt, A.; Frenkel, D., Communication: Theoretical Prediction of Free-Energy Landscapes for Complex Self-Assembly. J. Chem. Phys. 2015, 142, 021101. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jacobs, W. M. and Reinhardt, A. and Frenkel, D.',
                    'title' => 'Communication: Theoretical Prediction of Free-Energy Landscapes for Complex Self-Assembly',
                    'journal' => 'J. Chem. Phys',
                    'year' => '2015',
                    'volume' => '142',
                    'pages' => '021101',
                    ]
            ],
			// remove dash at end of title
			[
                'source' => 'Boschetti, A. a. (2018). Python Data Science Essentials - Third Edition. BIRMINGHAM - MUMBAI: Packt Publishing. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Boschetti, A. a.',
                    'year' => '2018',
                    'edition' => 'Third',
                    'title' => 'Python Data Science Essentials',
                    'publisher' => 'Packt Publishing',
                    'address' => 'BIRMINGHAM - MUMBAI',
                    ]
            ],
			// date not detected
			[
                'source' => 'ECBTI. (2023, 08 28). Líneas de Investigación ECBTI Universidad Nacional Abierta y a Distancia UNAD. UNAD: https://academia.unad.edu.co/investigacion-ecbti/cadenas-de-formacion ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://academia.unad.edu.co/investigacion-ecbti/cadenas-de-formacion',
                    'author' => 'ECBTI',
                    'title' => 'Líneas de Investigación ECBTI Universidad Nacional Abierta y a Distancia UNAD',
					'year' => '2023',
					'month' => '08',
					'date' => '2023-08-28',
					'urldate' => '2023, 08 28',
                    'note' => 'UNAD',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// date not detected
			[
                'source' => 'fastText. (n.d.). Pretrained-vectors. Retrieved 08 28, 2020, from https://github.com/facebookresearch/fastText/blob/master/docs/pretrained-vectors.md ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://github.com/facebookresearch/fastText/blob/master/docs/pretrained-vectors.md',
                    'author' => 'fastText',
                    'year' => 'n.d.',
                    'title' => 'Pretrained-vectors',
					'urldate' => '08 28, 2020',
                    ]
            ],
			// date not detected
			 [
                'source' => 'Perez, J. (n.d.). Spanish Word Embeddings. Retrieved 08 01, 2020, from https://github.com/dccuchile/spanish-word-embeddings ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://github.com/dccuchile/spanish-word-embeddings',
                    'author' => 'Perez, J.',
                    'year' => 'n.d.',
                    'title' => 'Spanish Word Embeddings',
                    'urldate' => '08 01, 2020',
                    ]
            ],
   			// [J] at end of title and entry should be ignored
			[
                'source' => '[3]	Liu C L, Yin F, Wang D H, et al. Online and offline handwritten Chinese character recognition: benchmarking on new databases[J]. Pattern Recognition, 2013, 46(1): 155-162.[J]. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Liu, C. L. and Yin, F. and Wang, D. H. and others',
                    'title' => 'Online and offline handwritten Chinese character recognition: benchmarking on new databases',
                    'year' => '2013',
                    'journal' => 'Pattern Recognition',
                    'volume' => '46',
                    'number' => '1',
                    'pages' => '155-162',
                    ]
            ],
			// [J] at end of title and entry should be ignored
			 [
                'source' => '[18]	Yang W, Jin L, Tao D, et al. DropSample: A new training method to enhance deep convolutional neural networks for large-scale unconstrained handwritten Chinese character recognition[J]. Pattern Recognition, 2016, 58: 190-203.[J]. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yang, W. and Jin, L. and Tao, D. and others',
                    'title' => 'DropSample: A new training method to enhance deep convolutional neural networks for large-scale unconstrained handwritten Chinese character recognition',
                    'year' => '2016',
                    'journal' => 'Pattern Recognition',
                    'pages' => '190-203',
                    'volume' => '58',
                    ]
            ],
  			// Interpret string in brackets that is not entirely numeric at start of entry as label
			[
                'source' => '[Ale17] Alexiadis, D.S., Chatzitofis, A., Zioulis, N., Zoidi, O., Louizis, G., Zarpalas, D., & Daras, P. (2017). An Integrated Platform for Live 3D Human Reconstruction and Motion Capturing. IEEE Transactions on Circuits and Systems for Video Technology, 27, 798-813. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Alexiadis, D. S. and Chatzitofis, A. and Zioulis, N. and Zoidi, O. and Louizis, G. and Zarpalas, D. and Daras, P.',
                    'year' => '2017',
                    'title' => 'An Integrated Platform for Live 3D Human Reconstruction and Motion Capturing',
                    'journal' => 'IEEE Transactions on Circuits and Systems for Video Technology',
                    'volume' => '27',
                    'pages' => '798-813',
                    ]
            ],
   			// type detected as unpublished
			[
                'source' => '[2] Duc E., Lartigue C., Tournier C., Bourdet P., A new concept for the design and the manufacturing of free-form surfaces: the machining surface, Annals of the CIRP, vol 48/1, pp 103-106, 1999. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Duc, E. and Lartigue, C. and Tournier, C. and Bourdet, P.',
                    'title' => 'A new concept for the design and the manufacturing of free-form surfaces: the machining surface',
                    'journal' => 'Annals of the CIRP',
                    'year' => '1999',
                    'volume' => '48',
					'number' => '1',
                    'pages' => '103-106',
                    ]
            ],
			// detected as unpublished rather than article
			[
                'source' => 'Verhulst, Pierre-François. (1838). Notice sur la loi que la population suit dans son accroissement, Correspondance Mathematique et Physique 10: 113-121. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Verhulst, Pierre-François',
                    'year' => '1838',
                    'title' => 'Notice sur la loi que la population suit dans son accroissement',
					'journal' => 'Correspondance Mathematique et Physique',
					'volume' => '10',
					'pages' => '113-121',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// detected as unpublished rather than article
			[
                'source' => 'Arrow, Kenneth J. (1964). Optimal capital policy, the cost of capital, and myopic decision rules, Annals of the Institute of Statistical Mathematics, 16: 21-30. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Arrow, Kenneth J.',
                    'year' => '1964',
                    'title' => 'Optimal capital policy, the cost of capital, and myopic decision rules',
					'journal' => 'Annals of the Institute of Statistical Mathematics',
					'volume' => '16',
					'pages' => '21-30',
                    ]
            ],
   			// Title ended early
			[
                'source' => 'Abdelraheem, N., Li, F., Guo, P., Sun, Y., Liu, Y., Cheng, Y. ...& Hou, F. (2023). Nutrient utilization of native herbage and oat forage as feed for Tibetan sheep (Ovis Aries). Grassland Science, 69(1), 12-22. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abdelraheem, N. and Li, F. and Guo, P. and Sun, Y. and Liu, Y. and Cheng, Y. and others and Hou, F.',
                    'year' => '2023',
                    'title' => 'Nutrient utilization of native herbage and oat forage as feed for Tibetan sheep (Ovis Aries)',
                    'journal' => 'Grassland Science',
                    'volume' => '69',
                    'number' => '1',
                    'pages' => '12-22',
                    ]
            ],
            [
                'source' => '26. Akrouch, G.A. (2014). Doctoral thesis - Energy Piles in Cooling Dominated Climates. Texas A&M University. https://hdl.handle.net/1969.1/152552  ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'school' => 'Texas A&M University',
                    'url' => 'https://hdl.handle.net/1969.1/152552',
                    'author' => 'Akrouch, G. A.',
                    'year' => '2014',
                    'title' => 'Energy Piles in Cooling Dominated Climates',
                    ]
            ],
   			// underscores used as quotes
			[
                'source' => 'Gandt, R. (1995). _Skygods: The Fall of Pan Am_. New York: William Morrow and Company. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Gandt, R.',
                    'year' => '1995',
                    'title' => 'Skygods: The Fall of Pan Am',
                    'publisher' => 'William Morrow and Company',
                    'address' => 'New York',
                    ]
            ],
			// remove \\ at end of entry
			[
                'source' => '[5]	Kabir, M.M. Investigating the Effect of Connected Vehicle (CV) Route Guidance on Mobility. PhD thesis, Morgan State University, 2021.\\\\ ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Kabir, M. M.',
                    'title' => 'Investigating the Effect of Connected Vehicle (CV) Route Guidance on Mobility',
                    'year' => '2021',
                    'school' => 'Morgan State University',
                    ]
            ],
   			// authors ended early
			[
                'source' => 'Randy A Freeman and James A Primbs. Control lyapunov functions: New ideas from an old source. In Conference on Decision and Control (CDC), volume 4, pages 3926–3931. IEEE, 1996.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Randy A. Freeman and James A. Primbs',
                    'title' => 'Control lyapunov functions: New ideas from an old source',
                    'year' => '1996',
                    'pages' => '3926-3931',
                    'booktitle' => 'Conference on Decision and Control (CDC), volume 4, IEEE',
                    ]
            ],
			// Journal included in title
			[
                'source' => 'Azhar NNZBA, Ghazali PLB, Mamat MB, Abdullah YB, Mahmud SB, Lambak SB, et al. Acceptance of Integrated Modification Model of Auto Takaful Insurance in Malaysia. Far East Journal of Mathematical Sciences (FJMS). 2017 May 1;101(8):1771–84. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Azhar Nnzba and Ghazali Plb and Mamat, M. B. and Abdullah, Y. B. and Mahmud, S. B. and Lambak, S. B. and others',
                    'title' => 'Acceptance of Integrated Modification Model of Auto Takaful Insurance in Malaysia',
					'journal' => 'Far East Journal of Mathematical Sciences (FJMS).',
                    'year' => '2017',
                    'month' => 'May',
                    'date' => '2017-05-01',
                    'volume' => '101',
					'number' => '8',
					'pages' => '1771-84',
                    ]
            ],
   			// Didn't pick up month/day correctly
    		[
                'source' => 'Bouzakis KD, Bouzakis E, Kombogiannis S, Makrimallakis S, Skordaris G, Michailidis N, Charalampous P, Paraskevopoulou R, M\'Saoubi R, Aurich JC, Barthelmä F., 2014, Effect of cutting edge preparation of coated tools on their performance in milling various materials. CIRP Journal of Manufacturing Science and Technology. Jan 1;7(3):264-73. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bouzakis, K. D. and Bouzakis, E. and Kombogiannis, S. and Makrimallakis, S. and Skordaris, G. and Michailidis, N. and Charalampous, P. and Paraskevopoulou, R. and M\'Saoubi, R. and Aurich, J. C. and Barthelmä, F.',
                    'year' => '2014',
                    'title' => 'Effect of cutting edge preparation of coated tools on their performance in milling various materials',
                    'journal' => 'CIRP Journal of Manufacturing Science and Technology.',
                    'pages' => '264-73',
                    'volume' => '7',
                    'number' => '3',
					'month' => 'January',
					'date' => '2014-01-01',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// Didn't pick up month/day correctly
			[
                'source' => 'Tien DH, Duy TN, Thoa PTT. 2023. Applying GPR-FGRA hybrid algorithm for prediction and optimization of eco-friendly magnetorheological finishing Ti–6Al–4V alloy. International Journal on Interactive Design and Manufacturing. Apr 1;17(2):729–45. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Tien, D. H. and Duy, T. N. and Thoa, P. T. T.',
                    'year' => '2023',
                    'title' => 'Applying GPR-FGRA hybrid algorithm for prediction and optimization of eco-friendly magnetorheological finishing Ti--6Al--4V alloy',
                    'journal' => 'International Journal on Interactive Design and Manufacturing.',
					'date' => '2023-04-01',
					'month' => 'April',
                    'pages' => '729-45',
                    'volume' => '17',
                    'number' => '2',
                    ]
            ],
			// volume interpreted as day
			[
                'source' => 'Kalambouka A, Pampaka M, Omuvwie M, Wo L. Mathematics Dispositions of Secondary School Students with Special Educational Needs. Journal of Research in Special Educational Needs. 2016 Aug;16:701–7. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kalambouka, A. and Pampaka, M. and Omuvwie, M. and Wo, L.',
                    'title' => 'Mathematics Dispositions of Secondary School Students with Special Educational Needs',
                    'year' => '2016',
                    'month' => 'August',
					'volume' => '16',
                    'journal' => 'Journal of Research in Special Educational Needs.',
                    'pages' => '701-7',
                    ]
            ],
			// day interpreted as volume
			[
                'source' => 'Lim YR, Ariffin AS, Ali M, Chang KL. A Hybrid MCDM Model for Live-Streamer Selection via the Fuzzy Delphi Method, AHP, and TOPSIS. Applied Sciences. 2021 Oct 8;11(19):9322. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Lim, Y. R. and Ariffin, A. S. and Ali, M. and Chang, K. L.',
                    'title' => 'A Hybrid MCDM Model for Live-Streamer Selection via the Fuzzy Delphi Method, AHP, and TOPSIS',
                    'year' => '2021',
                    'month' => 'October',
					'date' => '2021-10-08',
                    'journal' => 'Applied Sciences.',
                    'pages' => '9322',
                    'volume' => '11',
					'number' => '19',
                    ]
            ],
			// date picked up correctly
			[
                'source' => 'Yao KC, Lai JY, Huang WT, Tu JC. Utilize Fuzzy Delphi and Analytic Network Process to Construct Consumer Product Design Evaluation Indicators. Mathematics. 2022 Jan 27;10(3):397. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yao, K. C. and Lai, J. Y. and Huang, W. T. and Tu, J. C.',
                    'title' => 'Utilize Fuzzy Delphi and Analytic Network Process to Construct Consumer Product Design Evaluation Indicators',
                    'year' => '2022',
                    'month' => 'January',
                    'date' => '2022-01-27',
                    'journal' => 'Mathematics.',
                    'volume' => '10',
                    'number' => '3',
                    'pages' => '397',
                    ]
            ],
			// date picked up correctly
			[
                'source' => 'Diaz‐Carrion R, López‐Fernández M, Romero‐Fernandez PM. Constructing an index for comparing human resources management sustainability in Europe. Human Resource Management Journal. 2020 Feb 25;31(1):120–42. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Diaz-Carrion, R. and López-Fernández, M. and Romero-Fernandez, P. M.',
                    'title' => 'Constructing an index for comparing human resources management sustainability in Europe',
                    'year' => '2020',
                    'month' => 'February',
                    'date' => '2020-02-25',
                    'journal' => 'Human Resource Management Journal.',
                    'volume' => '31',
                    'number' => '1',
                    'pages' => '120-42',
                ],
                'char_encoding' => 'utf8leave',
            ],
   			// No space after month => day interpreted incorrectly
			[
                'source' => '3: Abi Habib W, Brioude F, Edouard T, Bennett JT, Lienhardt-Roussie A, Tixier F, Salem J, Yuen T, Azzi S, Le Bouc Y, Harbison MD, Netchine I. Genetic disruption of the oncogenic HMGA2-PLAG1-IGF2 pathway causes fetal growth restriction. Genet Med. 2018 Feb;20(2):250-258. doi: 10.1038/gim.2017.105. PMID: 28796236; PMCID: PMC5846811. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'PMID: 28796236. PMCID: PMC5846811',
                    'doi' => '10.1038/gim.2017.105',
                    'author' => 'Abi Habib, W. and Brioude, F. and Edouard, T. and Bennett, J. T. and Lienhardt-Roussie, A. and Tixier, F. and Salem, J. and Yuen, T. and Azzi, S. and Le Bouc, Y. and Harbison, M. D. and Netchine, I.',
                    'title' => 'Genetic disruption of the oncogenic HMGA2-PLAG1-IGF2 pathway causes fetal growth restriction',
                    'year' => '2018',
                    'month' => 'February',
                    'journal' => 'Genet Med.',
                    'pages' => '250-258',
                    'volume' => '20',
                    'number' => '2',
                    ]
            ],
			// No space after month => day interpreted incorrectly
			[
                'source' => '1: Abdelhedi F, El Khattabi L, Cuisset L, Tsatsaris V, Viot G, Druart L, Lebbar A, Dupont JM. Neonatal Silver-Russell syndrome with maternal uniparental heterodisomy, trisomy 7 mosaicism, and dysplasia of the cerebellum. Am J Clin Pathol. 2014 Aug;142(2):248-53. doi: 10.1309/AJCPBLMPRXKU1JUE. PMID: 25015868. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'PMID: 25015868',
                    'doi' => '10.1309/AJCPBLMPRXKU1JUE',
                    'author' => 'Abdelhedi, F. and El Khattabi, L. and Cuisset, L. and Tsatsaris, V. and Viot, G. and Druart, L. and Lebbar, A. and Dupont, J. M.',
                    'title' => 'Neonatal Silver-Russell syndrome with maternal uniparental heterodisomy, trisomy 7 mosaicism, and dysplasia of the cerebellum',
                    'year' => '2014',
                    'month' => 'August',
                    'journal' => 'Am J Clin Pathol.',
                    'volume' => '142',
                    'number' => '2',
                    'pages' => '248-53',
                    ]
            ],
			// No space after month => day interpreted incorrectly
			[
                'source' => 'Hadi A, Puspa Liza Ghazali, Foziah M, Roslida Abdul Razak, Arifin J. The Role of Index For Assessment In Business. The Journal of Management Theory and Practice (JMTP). 2022 Sep 9;3(2):84–9. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Hadi, A. and Puspa Liza Ghazali and Foziah, M. and Roslida Abdul Razak and Arifin, J.',
                    'title' => 'The Role of Index For Assessment In Business',
                    'year' => '2022',
                    'month' => 'September',
                    'journal' => 'The Journal of Management Theory and Practice (JMTP).',
                    'pages' => '84-9',
                    'volume' => '3',
                    'number' => '2',
                    'date' => '2022-09-09',
                    ]
            ],
			// put pages in book in note
			[
                'source' => 'Ruozi, R., Ferrari, P., Ruozi, R., & Ferrari, P. (2013). Liquidity risk management in banks: economic and regulatory issues (pp. 1-54). Springer Berlin. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ruozi, R. and Ferrari, P. and Ruozi, R. and Ferrari, P.',
                    'year' => '2013',
                    'title' => 'Liquidity risk management in banks: economic and regulatory issues',
                    'note' => 'pp. 1-54',
                    'publisher' => 'Springer',
                    'address' => 'Berlin',
                    ]
            ],
			[
                'source' => '[5] M. Fornasier, Theoretical Foundations and Numerical Methods for Sparse Recovery vol. 9: Walter de Gruyter, 2010. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'M. Fornasier',
                    'title' => 'Theoretical Foundations and Numerical Methods for Sparse Recovery',
					'volume' => '9',
                    'year' => '2010',
                    'publisher' => 'Walter de Gruyter',
                    ]
            ],
			// string is too easily classified as "series"
			[
                'source' => 'Allen, M. J., & Yen, M. M. (1979). Introduction to measurement theory. Brooks/Cole Pub. Co.  ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Allen, M. J. and Yen, M. M.',
                    'year' => '1979',
                    'title' => 'Introduction to measurement theory',
                    'publisher' => 'Brooks/Cole Pub. Co',
                    ]
            ],
   			// title ended early
			[
                'source' => '[8] K. Choi and S. B. Thacker. An evaluation of influenza mortality surveillance, 1962–1979: I. Time series forecasts of expected pneumonia and influenza deaths. American journal of epidemiology, 113(3):215226, 1981.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'K. Choi and S. B. Thacker',
                    'title' => 'An evaluation of influenza mortality surveillance, 1962--1979: I. Time series forecasts of expected pneumonia and influenza deaths',
                    'year' => '1981',
                    'journal' => 'American journal of epidemiology',
                    'volume' => '113',
                    'number' => '3',
                    'pages' => '215226',
                    ]
            ],
			// problem with quotes embedded in title
			[
                'source' => '\bibitem{FU5rN} Bayamlıoğlu E (2022) The right to contest automated decisions under the General Data Protection Regulation : Beyond the so‐called ``right to explanation.\'\' Regul Gov 16:1058–1078 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bayamlıoğlu, E.',
                    'year' => '2022',
                    'title' => 'The right to contest automated decisions under the General Data Protection Regulation : Beyond the so-called ``right to explanation.\'\'',
					'journal' => 'Regul Gov',
					'volume' => '16',
					'pages' => '1058-1078',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// phrase at start of title ending in colon included in authors
			// Case of colon at end of authors is rare?
			[
                'source' => 'Thomas, J.E.; Ekanem, A.M.; George, N.J.; Akpan, A.E. Ionospheric perturbations: A case study of 2007 five major earthquakes using DEMETER data. Acta Geophys. 2023, 71, 1607–1618. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Thomas, J. E. and Ekanem, A. M. and George, N. J. and Akpan, A. E.',
                    'title' => 'Ionospheric perturbations: A case study of 2007 five major earthquakes using DEMETER data',
                    'year' => '2023',
                    'journal' => 'Acta Geophys',
                    'volume' => '71',
                    'pages' => '1607-1618',
                    ]
            ],
			// Authors ended early (erroneous period after Peter); part of title detected as series
			[
                'source' => 'Richerson, Peter. J., and Robert Boyd. 2005. Not By Genes Alone: How Culture Transformed Human Evolution. Chicago/London: University of Chicago Press ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Richerson, Peter J. and Robert Boyd',
                    'year' => '2005',
                    'title' => 'Not By Genes Alone: How Culture Transformed Human Evolution',
                    'address' => 'Chicago/London',
                    'publisher' => 'University of Chicago Press',
                    ]
            ],
   			// journal not detected
			[
                'source' => 'Zhang X., Bai X., Incentive policies from 2006 to 2016 and new energy vehicle adoption in 2010-2020 in China, Renewable Sustainable Energy Rev., 70, pp. 24-43, (2017) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Zhang, X. and Bai, X.',
                    'title' => 'Incentive policies from 2006 to 2016 and new energy vehicle adoption in 2010-2020 in {C}hina',
					'journal' => 'Renewable Sustainable Energy Rev.',
					'volume' => '70',
                    'year' => '2017',
                    'pages' => '24-43',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Yuan X., Liu X., Zuo J., The development of new energy vehicles for a sustainable future: a review, Renewable Sustainable Energy Rev., 42, pp. 298-305, (2015) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yuan, X. and Liu, X. and Zuo, J.',
                    'title' => 'The development of new energy vehicles for a sustainable future: a review',
					'journal' => 'Renewable Sustainable Energy Rev.',
					'volume' => '42',
                    'year' => '2015',
                    'pages' => '298-305',
                    ]
            ],
			// journal detected correctly
			 [
                'source' => 'Yang K., Hiteva R.P., Schot J., Expectation dynamics and niche acceleration in china\'s wind and solar power development, Environ. Innov. Soc. Transit., 36, pp. 177-196, (2020) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yang, K. and Hiteva, R. P. and Schot, J.',
                    'title' => 'Expectation dynamics and niche acceleration in china\'s wind and solar power development',
                    'year' => '2020',
                    'journal' => 'Environ. Innov. Soc. Transit.',
                    'volume' => '36',
                    'pages' => '177-196',
                    ]
            ],
			// journal detected correctly
			 [
                'source' => 'Sun Y., Cao C., The evolving relations between government agencies of innovation policymaking in emerging economies: a policy network approach and its application to the Chinese case, Res. Policy, 47, 3, pp. 592-605, (2018) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Sun, Y. and Cao, C.',
                    'title' => 'The evolving relations between government agencies of innovation policymaking in emerging economies: a policy network approach and its application to the Chinese case',
                    'year' => '2018',
                    'journal' => 'Res. Policy',
                    'volume' => '47',
                    'number' => '3',
                    'pages' => '592-605',
                    ]
            ],
			// journal detected correctly
			[
                'source' => 'Rogge K.S., Reichardt K., Policy mixes for sustainability transitions: an extended concept and framework for analysis, Res. Policy, 45, 8, pp. 1620-1635, (2016) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Rogge, K. S. and Reichardt, K.',
                    'title' => 'Policy mixes for sustainability transitions: an extended concept and framework for analysis',
                    'year' => '2016',
                    'journal' => 'Res. Policy',
                    'volume' => '45',
                    'number' => '8',
                    'pages' => '1620-1635',
                    ]
            ],
			// journal not detected correctly
			 [
                'source' => 'Schmidt T.S., Sewerin S., Measuring the temporal dynamics of policy mixes -- an empirical analysis of renewable energy policy mixes\' balance and design features in nine countries, Res. Policy, 48, 10, (2019) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Schmidt, T. S. and Sewerin, S.',
                    'title' => 'Measuring the temporal dynamics of policy mixes -- an empirical analysis of renewable energy policy mixes\' balance and design features in nine countries',
                    'year' => '2019',
                    'journal' => 'Res. Policy',
                    'volume' => '48',
                    'pages' => '10',
                    ]
            ],
			// journal not detected correctly
			[
                'source' => 'Trencher G., Truong N., Temocin P., Duygan M., Top-down sustainability transitions in action: how do incumbent actors drive electric mobility diffusion in China, Japan, and California?, Energy Res. Soc. Sci., 79, (2021) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Trencher, G. and Truong, N. and Temocin, P. and Duygan, M.',
                    'title' => 'Top-down sustainability transitions in action: how do incumbent actors drive electric mobility diffusion in China, Japan, and California?',
					'journal' => 'Energy Res. Soc. Sci.',
                    'year' => '2021',
                    'volume' => '79',
                    ]
            ],
			// journal not detected correctly
			[
                'source' => 'Svara J.H., The myth of the dichotomy: complementarity of politics and administration in the past and future of public administration, Public Adm. Rev., 61, 2, pp. 176-183, (2001) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Svara, J. H.',
                    'title' => 'The myth of the dichotomy: complementarity of politics and administration in the past and future of public administration',
                    'year' => '2001',
                    'journal' => 'Public Adm. Rev.',
                    'volume' => '61',
                    'number' => '2',
                    'pages' => '176-183',
                    ]
            ],
			// journal not detected correctly
			[
                'source' => 'Schmid N., Sewerin S., Schmidt T.S., Explaining advocacy coalition change with policy feedback, Policy Stud. J., 48, 4, pp. 1109-1134, (2020) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Schmid, N. and Sewerin, S. and Schmidt, T. S.',
                    'title' => 'Explaining advocacy coalition change with policy feedback',
                    'year' => '2020',
                    'journal' => 'Policy Stud. J.',
                    'volume' => '48',
                    'number' => '4',
                    'pages' => '1109-1134',
                    ]
            ],
			// journal not detected correctly
			[
                'source' => 'Pierson P., When effect becomes cause: policy feedback and political change, World Polit., 45, 4, pp. 595-628, (1993) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Pierson, P.',
                    'title' => 'When effect becomes cause: policy feedback and political change',
					'journal' => 'World Polit.',
					'volume' => '45',
					'number' => '4',
                    'year' => '1993',
                    'pages' => '595-628',
                    ]
            ],
			// journal detected correctly
			[
                'source' => 'Normann H.E., Policy networks in energy transitions: the cases of carbon capture and storage and offshore wind in Norway, Technol. Forecast. Soc. Change, 118, pp. 80-93, (2017) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Normann, H. E.',
                    'title' => 'Policy networks in energy transitions: the cases of carbon capture and storage and offshore wind in Norway',
                    'year' => '2017',
                    'journal' => 'Technol. Forecast. Soc. Change',
                    'volume' => '118',
                    'pages' => '80-93',
                    ]
            ],
			// journal detected correctly
			[
                'source' => 'Liu F.C., Simon D.F., Sun Y.T., Cao C., China\'s innovation policies: evolution, institutional structure, and trajectory, Res. Policy, 40, 7, pp. 917-931, (2011) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Liu, F. C. and Simon, D. F. and Sun, Y. T. and Cao, C.',
                    'title' => 'China\'s innovation policies: evolution, institutional structure, and trajectory',
                    'year' => '2011',
                    'journal' => 'Res. Policy',
                    'volume' => '40',
                    'number' => '7',
                    'pages' => '917-931',
                    ]
            ],
			// journal detected correctly
            [
                'source' => 'Kern F., Howlett M., Implementing transition management as policy reforms: a case study of the Dutch energy sector, Policy Sci., 42, 4, pp. 391-408, (2009) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kern, F. and Howlett, M.',
                    'title' => 'Implementing transition management as policy reforms: a case study of the Dutch energy sector',
                    'year' => '2009',
                    'journal' => 'Policy Sci.',
                    'volume' => '42',
                    'number' => '4',
                    'pages' => '391-408',
                    ]
            ],
			// title ended early
			[
                'source' => 'Kivimaa P., Kern F., Creative destruction or mere niche support? Innovation policy mixes for sustainability transitions, Res. Policy, 45, 1, pp. 205-217, (2016) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kivimaa, P. and Kern, F.',
                    'title' => 'Creative destruction or mere niche support? Innovation policy mixes for sustainability transitions',
                    'year' => '2016',
                    'journal' => 'Res. Policy',
                    'volume' => '45',
                    'number' => '1',
                    'pages' => '205-217',
                    ]
            ],
			// title ended early
			[
                'source' => 'Meadowcroft J., Who is in charge here? Governance for sustainable development in a complex world, J. Environ. Plann. Policy Manage., 9, 3-4, pp. 299-314, (2007) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Meadowcroft, J.',
                    'title' => 'Who is in charge here? Governance for sustainable development in a complex world',
                    'year' => '2007',
                    'journal' => 'J. Environ. Plann. Policy Manage.',
                    'volume' => '9',
                    'number' => '3-4',
                    'pages' => '299-314',
                    ]
            ],
            // Use of underscores to delimit journal name, "dan" = "and" in Indonesian
			[
                'source' => 'F. Hassan dan R. Gupta, "Technology\'s Role in Education: A Review of Serious Games," _Journal of Educational Technology_, vol. 17, no. 2, pp. 65-78, 2022. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'F. Hassan and R. Gupta',
                    'title' => 'Technology\'s Role in Education: A Review of Serious Games',
                    'year' => '2022',
                    'journal' => 'Journal of Educational Technology',
                    'volume' => '17',
                    'number' => '2',
                    'pages' => '65-78',
                    ]
            ],
			// allow 'in press' for year
			[
                'source' => 'Stephan, Y., Sutin, A. R., & Terracciano, A. (in press). Personality traits and polypharmacy: Meta-analysis of five samples. Psychology & Health. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Stephan, Y. and Sutin, A. R. and Terracciano, A.',
                    'title' => 'Personality traits and polypharmacy: Meta-analysis of five samples',
					'year' => 'in press',
                    'journal' => 'Psychology & Health',
                    ]
            ],
			// allow 'in press' for year
			[
                'source' => 'Sutin, A. R., Luchetti, M., Stephan, Y., & Terracciano, A. (in press). Purpose in life and cognitive health: A 28-year prospective study. International Psychogeriatrics. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Sutin, A. R. and Luchetti, M. and Stephan, Y. and Terracciano, A.',
                    'title' => 'Purpose in life and cognitive health: A 28-year prospective study',
					'year' => 'in press',
                    'journal' => 'International Psychogeriatrics',
                    ]
            ],
			// type detected?
			[
                'source' => 'W. Xiao, H. Zhao, H. Pan, Y. Song, V.W. Zheng, Q. Yang, Beyond personalization: Social content recommendation for creator equality and consumer satisfaction, in: Proceedings of the 25th ACM SIGKDD International Conference on Knowledge Discovery \& Data Mining, 2019, pp. 235–245. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'W. Xiao and H. Zhao and H. Pan and Y. Song and V. W. Zheng and Q. Yang',
                    'title' => 'Beyond personalization: Social content recommendation for creator equality and consumer satisfaction',
                    'booktitle' => 'Proceedings of the 25th ACM SIGKDD International Conference on Knowledge Discovery \& Data Mining, 2019',
                    'year' => '2019',
                    'pages' => '235-245',
                    ]
            ],
			// detected as article
			[
                'source' => 'H. Ma, H. Yang, M.R. Lyu, I. King, Sorec: social recommendation using probabilistic matrix factorization, in: Proceedings of the 17th ACM conference on Information and knowledge management, 2008, pp. 931–940. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'H. Ma and H. Yang and M. R. Lyu and I. King',
                    'title' => 'Sorec: social recommendation using probabilistic matrix factorization',
					'booktitle' => 'Proceedings of the 17th ACM conference on Information and knowledge management, 2008',
                    'year' => '2008',
                    'pages' => '931-940',
                    ]
            ],
			// detected as article
			 [
                'source' => 'X. He, L. Liao, H. Zhang, L. Nie, X. Hu, T.S. Chua, Neural collaborative filtering, in: Proceedings of the 26th international conference on world wide web, 2017, pp. 173–182. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'X. He and L. Liao and H. Zhang and L. Nie and X. Hu and T. S. Chua',
                    'title' => 'Neural collaborative filtering',
					'booktitle' => 'Proceedings of the 26th international conference on world wide web, 2017',
                    'year' => '2017',
                    'pages' => '173-182',
                    ]
            ],
			// title includes part of journal name
			[
                'source' => '	10	Dimitrov, George; Katzarkov, Ludmil, Some new categorical invariants, Selecta Math. (N.S.) 25 (2019), no. 3, Paper No. 45',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dimitrov, George and Katzarkov, Ludmil',
                    'title' => 'Some new categorical invariants',
					'journal' => 'Selecta Math. (N. S.)',
                    'year' => '2019',
                    'volume' => '25',
                    'number' => '3',
					'note' => 'Paper No. 45',
                    ]
            ],
			// title includes part of journal name
			[
                'source' => '	36	Favero, David; Iliev, Atanas; Katzarkov, Ludmil, On the Griffiths groups of Fano manifolds of Calabi-Yau Hodge type, Pure Appl. Math. Q. 10 (2014), no. 1, 1–55.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Favero, David and Iliev, Atanas and Katzarkov, Ludmil',
                    'title' => 'On the Griffiths groups of Fano manifolds of Calabi-Yau Hodge type',
                    'year' => '2014',
                    'journal' => 'Pure Appl. Math. Q.',
                    'volume' => '10',
                    'number' => '1',
                    'pages' => '1-55',
                    ]
            ],
			// title includes part of journal name
			[
                'source' => '	50	Alexeev, V.; Katzarkov, L., On K-stability of reductive varieties, Geom. Funct. Anal. 15 (2005), no. 2, 297–310.',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Alexeev, V. and Katzarkov, L.',
                    'title' => 'On K-stability of reductive varieties',
                    'year' => '2005',
                    'journal' => 'Geom. Funct. Anal.',
                    'volume' => '15',
                    'number' => '2',
                    'pages' => '297-310',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Han, M. Y., Özyilmaz, B., Zhang, Y., & Kim, P. (2007). Energy Band-gap engineering of Graphene Nanoribbons. Physical Review Letters, 98(20). https://doi.org/10.1103/physrevlett.98.206805 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Han, M. Y. and Özyilmaz, B. and Zhang, Y. and Kim, P.',
                    'title' => 'Energy Band-gap engineering of Graphene Nanoribbons',
                    'year' => '2007',
                    'volume' => '98',
					'journal' => 'Physical Review Letters',
					'number' => '20',
                    'doi' => '10.1103/physrevlett.98.206805',
                ],
				'char_encoding' => 'utf8leave',
            ],
			// pp included in journal name
			[
                'source' => '[144]	L. Hoelting et al., “Stem Cell-Derived Immature Human Dorsal Root Ganglia Neurons to Identify Peripheral Neurotoxicants,” Stem Cells Transl Med, pp. 505–509, 2016. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'L. Hoelting and others',
                    'title' => 'Stem Cell-Derived Immature Human Dorsal Root Ganglia Neurons to Identify Peripheral Neurotoxicants',
                    'year' => '2016',
                    'journal' => 'Stem Cells Transl Med',
                    'pages' => '505-509',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Lei Tian, Qiang Cao, Hong Jiang, Dan Feng, Changsheng Xie, and Qin Xin. Online Availability Upgrades for Parity-based RAIDs through Supplementary Parity Augmentations. ACM Transactions on Storage, Vol. 6, No. 4, Article 17, May 2011, Pages: 17:1-17:23 ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'Article 17',
                    'author' => 'Lei Tian and Qiang Cao and Hong Jiang and Dan Feng and Changsheng Xie and Qin Xin',
                    'title' => 'Online Availability Upgrades for Parity-based RAIDs through Supplementary Parity Augmentations',
					'journal' => 'ACM Transactions on Storage',
                    'year' => '2011',
                    'month' => 'May',
                    'pages' => '1-17',
                    'volume' => '6',
                    'number' => '4',
                    ]
            ],
   			// last author included in title
			[
                'source' => '\bibitem {Ganju et al.} Karan Ganju, Qi Wang, Wei Yang, Carl A. Gunter, Nikita Borisov: \emph{Property Inference Attacks on Fully Connected Neural Networks using Permutation Invariant Representations}, in CCS \'18: 2018 ACM SIGSAC Conference on Computer \& Communications Security Oct. 15-19, Toronto, ON, Canada, pp. 619-633. \url{https://doi.org/10.1145/3243734.3243834} ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Karan Ganju and Qi Wang and Wei Yang and Carl A. Gunter and Nikita Borisov',
                    'title' => 'Property Inference Attacks on Fully Connected Neural Networks using Permutation Invariant Representations',
                    'year' => '2018',
                    'pages' => '619-633',
                    'doi' => '10.1145/3243734.3243834',
                    'booktitle' => 'CCS \'18: 2018 ACM SIGSAC Conference on Computer \& Communications Security Oct. 15-19, Toronto, ON, Canada',
                    ]
            ],
   			// Names included in title
			[
                'source' => 'Ling Chen, Donghui Chen, Fan Yang, and Jianling Sun. A deep multi-task representation learning method for time series classification and retrieval. Inf. Sci., 555:17–32, 2021. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ling Chen and Donghui Chen and Fan Yang and Jianling Sun',
                    'title' => 'A deep multi-task representation learning method for time series classification and retrieval',
                    'year' => '2021',
                    'journal' => 'Inf. Sci.',
                    'volume' => '555',
                    'pages' => '17-32',
                    ]
            ],
			// "A" included at end of author list
			[
                'source' => 'George Zerveas, Srideepika Jayaraman, Dhaval Patel, Anuradha Bhamidipaty, and Carsten Eickhoff. A transformerbased framework for multivariate time series representation learning. In KDD, pages 2114–2124, 2021. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'George Zerveas and Srideepika Jayaraman and Dhaval Patel and Anuradha Bhamidipaty and Carsten Eickhoff',
                    'title' => 'A transformerbased framework for multivariate time series representation learning',
                    'year' => '2021',
                    'journal' => 'KDD',
                    'pages' => '2114-2124',
                    ]
            ],
			// second author included in title
			[
                'source' => 'Ling Yang and Shenda Hong. Unsupervised time-series representation learning with iterative bilinear temporalspectral fusion. In ICML, pages 25038–25054, 2022. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Ling Yang and Shenda Hong',
                    'title' => 'Unsupervised time-series representation learning with iterative bilinear temporalspectral fusion',
                    'year' => '2022',
                    'journal' => 'ICML',
                    'pages' => '25038-25054',
                    ]
            ],
			// title ended early
			[
                'source' => 'Song, C., Kenis, G., van Gastel,A., Bosmans, E., Lin, A., de Jong, R., et al. (1999). Influence of psychological stress on immune-inflammatory variables in normal humans. Part II: Altered serum concentrations of natural antiinflammatory agents and soluble membrane antigens of monocytes and T lymphocytes. Psychiatry Research, 85(3), 293–303 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Song, C. and Kenis, G. and van Gastel, A. and Bosmans, E. and Lin, A. and de Jong, R. and others',
                    'year' => '1999',
                    'title' => 'Influence of psychological stress on immune-inflammatory variables in normal humans. Part II: Altered serum concentrations of natural antiinflammatory agents and soluble membrane antigens of monocytes and T lymphocytes',
					'journal' => 'Psychiatry Research',
                    'volume' => '85',
                    'number' => '3',
                    'pages' => '293-303',
                    ]
            ],
			// authors includes first word of title (other examples in conversion 4729)
			 [
                'source' => '	3	Haiden, F.; Katzarkov, L.; Kontsevich, M.; Pandit, P., Semistability, modular lattices, and iterated logarithms, J. Differential Geom. 123 (2023), no. 1, 21–66. IF: 2.5 (Q1)   ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Haiden, F. and Katzarkov, L. and Kontsevich, M. and Pandit, P.',
                    'title' => 'Semistability, modular lattices, and iterated logarithms',
                    'year' => '2023',
                    'journal' => 'J. Differential Geom.',
                    'volume' => '123',
                    'number' => '1',
                    'pages' => '21-66',
                    ]
            ],
			// authors includes first word of title
			[
                'source' => '	6	Katzarkov, Ludmil; Pandit, Pranav; Spaide, Theodore, Calabi-Yau structures, spherical functors, and shifted symplectic structures, Adv. Math. 392 (2021), Paper No. 108037',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Katzarkov, Ludmil and Pandit, Pranav and Spaide, Theodore',
                    'title' => 'Calabi-Yau structures, spherical functors, and shifted symplectic structures',
                    'year' => '2021',
                    'journal' => 'Adv. Math.',
                    'volume' => '392',
					'note' => 'Paper No. 108037',
                    ]
            ],
			// authors includes first word of title
			[
                'source' => '	7	Katzarkov, Ludmil; Lupercio, Ernesto; Meersseman, Laurent; Verjovsky, Alberto, Quantum (non-commutative) toric geometry: foundations, Adv. Math. 391 (2021), Paper No. 107945, 110 pp. IF: 1.530 (Q1) Cited 4 times 	8	Fan, Yu-Wei; Filip, Simion; Haiden, Fabian; Katzarkov, Ludmil; Liu, Yijia, On pseudo-Anosov autoequivalences, Adv. Math. 384 (2021), Paper No. 107732, 37 pp. IF: 1.530 (Q1)  Cited 7 times  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Katzarkov, Ludmil and Lupercio, Ernesto and Meersseman, Laurent and Verjovsky, Alberto',
                    'title' => 'Quantum (non-commutative) toric geometry: foundations',
                    'year' => '2021',
                    'journal' => 'Adv. Math.',
                    'volume' => '391',
                    'number' => '2021',
                    ]
            ],
   			// problem with von name
			[
                'source' => 'Attard-Frost, B., De los Ríos, A., & Walters, D. R. (2023). The ethics of AI business practices: a review of 47 AI ethics guidelines. AI and Ethics, 3, 389–406. https://doi.org/10.1007/s43681-022-00156-6. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s43681-022-00156-6',
                    'author' => 'Attard-Frost, B. and De los Ríos, A. and Walters, D. R.',
                    'year' => '2023',
                    'title' => 'The ethics of AI business practices: a review of 47 AI ethics guidelines',
                    'pages' => '389-406',
                    'volume' => '3',
					'journal' => 'AI and Ethics',
                ],
                'char_encoding' => 'utf8leave',
            ],
            // year format
			[
                'source' => 'Althusser, L. (2008 (1971)). On Ideology. London: Verso. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Althusser, L.',
                    'year' => '2008 (1971)',
                    'title' => 'On Ideology',
                    'publisher' => 'Verso',
                    'address' => 'London',
                    ]
            ],
   			// \textsc used for authors
			[
                'source' => '\bibitem{A+M+P-2006}  \textsc{Aban IB, Meerschaert MM \& Panorska AK} (2006). Parameter estimation for the truncated Pareto distribution. {\it Journal of the American Statistical Association} {\bf 101}:473, 270--277. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Aban, I. B. and Meerschaert, M. M. and Panorska, A. K.',
					'year' => '2006',
					'title' => 'Parameter estimation for the truncated Pareto distribution',
                    'journal' => 'Journal of the American Statistical Association',
                    'pages' => '270-277',
                    'volume' => '101',
					'number' => '473',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Mengya Lei, Fan Li, Fang Wang, Dan Feng, Xiaomin Zou, Renzhi Xiao. SecNVM: An Eﬀicient and Write-Friendly Metadata Crash Consistency Scheme for Secure NVM. ACM Transactions on Architecture and Code Optimization, Vol.19 Issue 1, March 2022, Article No.8, Pages: 1–26 ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'Article',
                    'author' => 'Mengya Lei and Fan Li and Fang Wang and Dan Feng and Xiaomin Zou and Renzhi Xiao',
                    'title' => 'SecNVM: An Efficient and Write-Friendly Metadata Crash Consistency Scheme for Secure NVM',
                    'year' => '2022',
                    'month' => 'March',
                    'journal' => 'ACM Transactions on Architecture and Code Optimization',
                    'pages' => '1-26',
                    'volume' => '19',
                    'number' => '1',
					'note' => 'Article No. 8',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Dan Feng, Hai Jin , Jiangling Zhang, Performance Analysis of RAID for Different Communication Mechanism Between RAID Controller and String Controllers, IEEE Trans. on Magnetics, Vol. 32, No.5, September, 1996, Pages:3890-3892. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dan Feng and Hai Jin and Jiangling Zhang',
                    'title' => 'Performance Analysis of RAID for Different Communication Mechanism Between RAID Controller and String Controllers',
					'journal' => 'IEEE Trans. on Magnetics',
                    'year' => '1996',
                    'month' => 'September',
                    'pages' => '3890-3892',
                    'volume' => '32',
                    'number' => '5',
                    ]
            ],
			// journal not detected 
			[
                'source' => 'Yu Hua, Yifeng Zhu, Hong Jiang, Dan Feng and Lei Tian. Supporting Scalable and Adaptive Metadata Management in Ultra Large-scale File Systems. IEEE Transactions on Parallel and Distributed Systems (TPDS), Vol.22, No.4, April 2011, Pages: 580-593. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Yu Hua and Yifeng Zhu and Hong Jiang and Dan Feng and Lei Tian',
                    'title' => 'Supporting Scalable and Adaptive Metadata Management in Ultra Large-scale File Systems',
					'journal' => 'IEEE Transactions on Parallel and Distributed Systems (TPDS)',
                    'year' => '2011',
                    'month' => 'April',
                    'pages' => '580-593',
                    'volume' => '22',
                    'number' => '4',
                    ]
            ],
            // journal (newspaper) not detected
			[
                'source' => 'Belton, C., Mekhennet, S. and Harris, S. (2023, April 21). Kremlin tries to build antiwar coalition in Germany, documents show. The Washington Post. Retrieved April 24 from https://www.washingtonpost.com/world/2023/04/21/germany-russia-interference-afd-wagenknecht/. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Belton, C. and Mekhennet, S. and Harris, S.',
                    'title' => 'Kremlin tries to build antiwar coalition in Germany, documents show',
                    'journal' => 'The Washington Post',
                    'year' => '2023',
                    'month' => 'April',
					'date' => '2023-04-21',
					'urldate' => 'April 24',
                    'url' => 'https://www.washingtonpost.com/world/2023/04/21/germany-russia-interference-afd-wagenknecht/',
                    ]
            ],
			// journal (newspaper) not detected
			 [
                'source' => 'Gumenyuk, N. (2024, April 19). Brave new Ukraine. Foreign Affairs. Retrieved April 21 from https://www.foreignaffairs.com/ukraine/brave-new-ukraine. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Gumenyuk, N.',
                    'title' => 'Brave new Ukraine',
                    'journal' => 'Foreign Affairs',
                    'year' => '2024',
                    'month' => 'April',
					'date' => '2024-04-19',
					'urldate' => 'April 21',
                    'url' => 'https://www.foreignaffairs.com/ukraine/brave-new-ukraine',
                    ]
            ],
			// journal (newspaper) not detected
			[
                'source' => 'Kroet, C. (2016, February 1). German far-right slammed for ‘shoot refugees’ remark. Politico. Retrieved April 24 from https://www.politico.eu/article/afd-petry-german-far-right-slammed-for-shoot-refugees-remark/. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kroet, C.',
                    'title' => '{G}erman far-right slammed for `shoot refugees\' remark',
                    'journal' => 'Politico',
                    'year' => '2016',
                    'month' => 'February',
					'date' => '2016-02-01',
					'urldate' => 'April 24',
                    'url' => 'https://www.politico.eu/article/afd-petry-german-far-right-slammed-for-shoot-refugees-remark/',
                    ]
            ],
			// journal (newspaper) not detected
			[
                'source' => 'Motyl, A. J. (2023, May 27). Putin needs a geography lesson. The Hill. Retrieved April 24 from https://thehill.com/opinion/international/4022705-putin-needs-a-geography-lesson/. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Motyl, A. J.',
                    'title' => 'Putin needs a geography lesson',
                    'journal' => 'The Hill',
                    'year' => '2023',
                    'month' => 'May',
					'date' => '2023-05-27',
					'urldate' => 'April 24',
                    'url' => 'https://thehill.com/opinion/international/4022705-putin-needs-a-geography-lesson/',
                    ]
            ],
			// url access date not picked up
			[
                'source' => '\bibitem{Feedback GAN (FBGAN) for DNA}A. Gupta and J. Zou, "Feedback GAN (FBGAN) for DNA: a Novel Feedback-Loop Architecture for Optimizing Protein Functions." arXiv, Apr. 05, 2018. doi: 10.48550/arXiv.1804.01694. Available: \url{https://arxiv.org/abs/1804.01694}. [Accessed: May 21, 2024] ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'A. Gupta and J. Zou',
                    'title' => 'Feedback GAN (FBGAN) for DNA: a Novel Feedback-Loop Architecture for Optimizing Protein Functions',
                    'year' => '2018',
                    'doi' => '10.48550/arXiv.1804.01694',
                    'url' => 'https://arxiv.org/abs/1804.01694',
                    'archiveprefix' => 'arXiv',
                    'urldate' => 'May 21, 2024',
					'date' => '2018-04-05',
                    ]
            ],
			// url access date not picked up
			[
                'source' => '\bibitem{Gradient Descent}S. Ruder, "An overview of gradient descent optimization algorithms." arXiv, Jun. 15, 2017. Available: \url{http://arxiv.org/abs/1609.04747}. [Accessed: May 21, 2024] ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'S. Ruder',
                    'title' => 'An overview of gradient descent optimization algorithms',
                    'year' => '2017',
                    'url' => 'http://arxiv.org/abs/1609.04747',
                    'archiveprefix' => 'arXiv',
                    'urldate' => 'May 21, 2024',
					'date' => '2017-06-15',
                    ]
            ],
			// url access date not picked up
			[
                'source' => '\bibitem{deep learning in genomics}J. Liu, J. Li, H. Wang, and J. Yan, "Application of deep learning in genomics," Sci. China Life Sci., vol. 63, no. 12, pp. 1860-1878, Dec. 2020, doi: 10.1007/s11427-020-1804-5. Available: \url{https://doi.org/10.1007/s11427-020-1804-5}. [Accessed: May 21, 2024] ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Liu and J. Li and H. Wang and J. Yan',
                    'title' => 'Application of deep learning in genomics',
                    'journal' => 'Sci. China Life Sci.',
                    'year' => '2020',
                    'month' => 'December',
                    'volume' => '63',
                    'number' => '12',
                    'pages' => '1860-1878',
                    'doi' => '10.1007/s11427-020-1804-5',
                    'url' => 'https://doi.org/10.1007/s11427-020-1804-5',
					'urldate' => 'May 21, 2024',
                    ]
            ],
			// number and page numbers not picked up
			[
                'source' => 'Tham YC, Li X, Wong TY, Quigley HA, Aung T, Cheng CY. Global prevalence of glaucoma and projections of glaucoma burden through 2040: A systematic review and meta-analysis. Ophthalmology. 2014;121(11):2081- 2090. doi:10.1016/j.ophtha.2014.05.013 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Tham, Y. C. and Li, X. and Wong, T. Y. and Quigley, H. A. and Aung, T. and Cheng, C. Y.',
                    'title' => 'Global prevalence of glaucoma and projections of glaucoma burden through 2040: A systematic review and meta-analysis',
                    'journal' => 'Ophthalmology.',
                    'year' => '2014',
                    'volume' => '121',
                    'number' => '11',
                    'pages' => '2081-2090',
                    'doi' => '10.1016/j.ophtha.2014.05.013',
                    ]
            ],
            // number, pages, year (no spaces)
            [
                'source' => 'Jonas JB, Aung T, Bourne RR, Bron AM, Ritch R, Panda-Jonas S. Glaucoma. Lancet. 2017;390(10108):2183-2193. doi:10.1016/S0140-6736(17)31469-1 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jonas, J. B. and Aung, T. and Bourne, R. R. and Bron, A. M. and Ritch, R. and Panda-Jonas, S.',
                    'title' => 'Glaucoma',
                    'journal' => 'Lancet',
                    'year' => '2017',
                    'volume' => '390',
                    'number' => '10108',
                    'pages' => '2183-2193',
                    'doi' => '10.1016/S0140-6736(17)31469-1',
                    ]
            ],
			// translators not detected correctly
			[
                'source' => 'Barth, Karl. 2004. Church Dogmatics 2:1. Edited by G. W. Bromiley and T. F. Torrance. Translated by T. H. L. Parker, W. B. Johnston, Harold Knight, and J. L. M. Haire. London/New York: T&T Clark. First published 1957.  ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Barth, Karl',
                    'title' => 'Church Dogmatics 2:1',
                    'year' => '2004',
                    'note' => 'Translated by T. H. L. Parker, W. B. Johnston, Harold Knight, and J. L. M. Haire. First published 1957. Edited by G. W. Bromiley and T. F. Torrance.',
                    'address' => 'London/New York',
                    'publisher' => 'T&T Clark',
                    ]
            ],
			// "translated by" example
			[
                'source' => 'Leibniz, Gottfried Wilhelm. 1952. Theodicy. Edited by Austin Farrer and translated by E. M. Huggard. New Haven: Yale University Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Leibniz, Gottfried Wilhelm',
                    'title' => 'Theodicy',
                    'year' => '1952',
                    'note' => 'Translated by E. M. Huggard. Edited by Austin Farrer.',
                    'address' => 'New Haven',
                    'publisher' => 'Yale University Press',
                    ]
            ],
			// "translated by" example
			[
                'source' => 'Saadia Gaon. 1988. The Book of Theodicy: Translation and Commentary on the Book of Job. Translated by L. E. Goodman. New Haven: Yale University Press.  ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Saadia Gaon',
                    'title' => 'The Book of Theodicy: Translation and Commentary on the Book of Job',
                    'year' => '1988',
                    'note' => 'Translated by L. E. Goodman.',
                    'address' => 'New Haven',
                    'publisher' => 'Yale University Press',
                    ]
            ],
			// authors ended early
			[
                'source' => ' Jindong Xie, Xiyuan Luo, Xinpei Deng, Yuhui Tang, Wenwen Tian, Hui Cheng, Junsheng Zhang, Yutian Zou, Zhixing Guo and Xiaoming Xie \emph{Advances in artificial intelligence to predict cancer immunotherapy efficacy}, 2023, Frontiers in Immunology ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jindong Xie and Xiyuan Luo and Xinpei Deng and Yuhui Tang and Wenwen Tian and Hui Cheng and Junsheng Zhang and Yutian Zou and Zhixing Guo and Xiaoming Xie',
                    'title' => 'Advances in artificial intelligence to predict cancer immunotherapy efficacy',
					'year' => '2023',
					'journal' => 'Frontiers in Immunology',
                    ]
            ],
			// detect PMCID?
			[
                'source' => 'Laubenbacher R, Sluka JP, Glazier JA. \emph{Using digital twins in viral infection.} Science, 2021 Mar 12;371(6534):1105-1106. doi: 10.1126/science.abf3370. PMID: 33707255; PMCID: PMC8170388. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'PMID: 33707255. PMCID: PMC8170388',
                    'doi' => '10.1126/science.abf3370',
                    'author' => 'Laubenbacher, R. and Sluka, J. P. and Glazier, J. A.',
                    'title' => 'Using digital twins in viral infection',
                    'year' => '2021',
                    'month' => 'March',
                    'date' => '2021-03-12',
                    'journal' => 'Science',
					'pages' => '1105-1106',
					'number' => '6534',
                    'volume' => '371',
                    ]
            ],
            // All fields confused
			[
                'source' => 'Lange, N. A., Ed., 1961. Handbook of chemistry, 10th ed. McGraw-Hill Book Co., New York, 1961. ',
                'type' => 'book',
                'bibtex' => [
                    'editor' => 'Lange, N. A.',
                    'title' => 'Handbook of chemistry',
                    'year' => '1961',
                    'edition' => '10th',
                    'publisher' => 'McGraw-Hill Book Co',
                    'address' => 'New York',
                    ]
            ],
			// journal included in title
			[
                'source' => 'Narayan S, Ramamurthy A. Health and Safety Aspects of Beryllium Operations. Mineral Processing and Extractive Metallurgy Review. 1995 Jan;14(1):301–18.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Narayan, S. and Ramamurthy, A.',
                    'title' => 'Health and Safety Aspects of Beryllium Operations',
					'journal' => 'Mineral Processing and Extractive Metallurgy Review.',
                    'year' => '1995',
                    'month' => 'January',
                    'volume' => '14',
					'number' => '1',
                    'pages' => '301-18',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Saha S., The treatments of effluents in Beryllium production, Indian Chemical Engineer, XXXIII (2), 62-64, 1991. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Saha, S.',
                    'title' => 'The treatments of effluents in Beryllium production',
					'journal' => 'Indian Chemical Engineer',
                    'year' => '1991',
                    'volume' => 'XXXIII',
                    'pages' => '62-64',
                    'number' => '2',
                    ]
            ],
			// journal not detected
			[
                'source' => '\bibitem {Beltran2023} Beltr\\\'an, E. T. M., P\\\'erez, M. Q., S\\\'anchez, P. M. S., Bernal, S. L., Bovet, G., P\\\'erez, M. G., P\\\'erez, G. M., \& Celdr\\\'an, A. H. Decentralized federated learning: Fundamentals, state of the art, frameworks, trends, and challenges. IEEE Communications Surveys \& Tutorials. vol. 25, no. 2, pp. 2983-3013, September 2023 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Beltr\\\'an, E. T. M. and P\\\'erez, M. Q. and S\\\'anchez, P. M. S. and Bernal, S. L. and Bovet, G. and P\\\'erez, M. G. and P\\\'erez, G. M. and Celdr\\\'an, A. H.',
                    'title' => 'Decentralized federated learning: Fundamentals, state of the art, frameworks, trends, and challenges',
					'journal' => 'IEEE Communications Surveys \& Tutorials.',
                    'year' => '2023',
                    'month' => 'September',
                    'volume' => '25',
                    'number' => '2',
                    'pages' => '2983-3013',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Bhat P. N., Ghosh D. K., Desai M. V. M., Beryllium: an easily scavenged metal ion in the environment, Science of The Total Environment, 297(1–3):119-125, 2002. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bhat, P. N. and Ghosh, D. K. and Desai, M. V. M.',
                    'title' => 'Beryllium: an easily scavenged metal ion in the environment',
					'journal' => 'Science of The Total Environment',
                    'year' => '2002',
                    'volume' => '297',
					'number' => '1-3',
					'pages' => '119-125',
                    ]
            ],
			// date not picked up
			[
                'source' => '2.	Slimani M, Chamari K, Miarka B, Del Vecchio FB, Chéour F. Effects of Plyometric Training on Physical Fitness in Team Sport Athletes: A Systematic Review. J Hum Kinet. Dec 01 2016;53:231-247. doi:10.1515/hukin-2016-0026 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1515/hukin-2016-0026',
                    'author' => 'Slimani, M. and Chamari, K. and Miarka, B. and Del Vecchio, F. B. and Chéour, F.',
                    'title' => 'Effects of Plyometric Training on Physical Fitness in Team Sport Athletes: A Systematic Review',
                    'year' => '2016',
					'date' => '2016-12-01',
                    'month' => 'December',
                    'journal' => 'J Hum Kinet.',
                    'pages' => '231-247',
                    'volume' => '53',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// month and year picked up correctly
			 [
                'source' => '3.	Sáez-Sáez de Villarreal E, Requena B, Newton RU. Does plyometric training improve strength performance? A meta-analysis. J Sci Med Sport. Sep 2010;13(5):513-22. doi:10.1016/j.jsams.2009.08.005 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.jsams.2009.08.005',
                    'author' => 'Sáez-Sáez de Villarreal, E. and Requena, B. and Newton, R. U.',
                    'title' => 'Does plyometric training improve strength performance? A meta-analysis',
                    'year' => '2010',
                    'month' => 'September',
                    'journal' => 'J Sci Med Sport.',
                    'volume' => '13',
                    'number' => '5',
                    'pages' => '513-22',
                    ],
					'char_encoding' => 'utf8leave',
            ],
            // inproceedings not detected
            [
                'source' => '[111]Leonard Petnga, Huan Xu, Security of unmanned aerial vehicles: Dynamic state estimation under cyber-physical attacks, in: 2016 International Conference on Unmanned Aircraft Systems, ICUAS, 2016. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Leonard Petnga and Huan Xu',
                    'title' => 'Security of unmanned aerial vehicles: Dynamic state estimation under cyber-physical attacks',
                    'year' => '2016',
                    'booktitle' => '2016 International Conference on Unmanned Aircraft Systems, ICUAS',
                    ]
            ],
   			// should not lowercase first letter of name following -
			[
                'source' => 'YORK-BARR, J.; DUKE, K. What do we know about teacher leadership? Findings from two decades of scholarship. Review of Educational Research, 74, n. 3, 2004. 255-316. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'York-Barr, J. and Duke, K.',
                    'title' => 'What do we know about teacher leadership? Findings from two decades of scholarship',
                    'year' => '2004',
                    'journal' => 'Review of Educational Research',
                    'volume' => '74',
                    'number' => '3',
                    'pages' => '255-316',
                    ]
            ],
			// first word of title in author list
			[
                'source' => 'AMADO, J. Manual de investigação qualitativa em educação. 3ª. ed. Coimbra: Universidade de Coimbra, 2017. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Amado, J.',
                    'title' => 'Manual de investigação qualitativa em educação',
                    'year' => '2017',
                    'publisher' => 'Universidade de Coimbra',
                    'address' => 'Coimbra',
                    'edition' => '3ª',
                    ],
					'language' => 'pt',
					'char_encoding' => 'utf8leave',
            ],
			// part of title included in author list.  PhD thesis not detected
			[
                'source' => 'ARAÚJO, K. S. S. Dispositivos e diapositivos da educação científica: o TPACK e a constituição de práticas inovadoras na educação básica - Tese (Doutorado). Universidade do Estado da Bahia, Salvador, p. 202. 2019. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Araújo, K. S. S.',
                    'title' => 'Dispositivos e diapositivos da educação científica: o TPACK e a constituição de práticas inovadoras na educação básica',
					'school' => 'Universidade do Estado da Bahia, Salvador',
                    'year' => '2019',
                    'note' => 'p. 202',
                    ],
					'language' => 'pt',
					'char_encoding' => 'utf8leave',
            ],
			// part of title included in authors
			[
                'source' => 'GIMENO SACRISTÁN, J. Poderes instáveis em educação. Porto Alegre: ArtMed, 1999. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Gimeno Sacristán, J.',
					'title' => 'Poderes instáveis em educação',
                    'address' => 'Porto Alegre',
                    'year' => '1999',
                    'publisher' => 'ArtMed',
                    ],
					'language' => 'pt',
					'char_encoding' => 'utf8leave',
            ],
			// edition not identified
			[
                'source' => 'MANKIW, N. G. Principles of Microeconomics. 5ª Edição. Mason, Ohio: South-Western Cengage Learning, 2008. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Mankiw, N. G.',
                    'title' => 'Principles of Microeconomics',
                    'year' => '2008',
					'edition' => '5ª',
                    'publisher' => 'South-Western Cengage Learning',
                    'address' => 'Mason, Ohio',
                    ],
					'language' => 'pt',
					'char_encoding' => 'utf8leave',
            ],
			// journal not identified
			[
                'source' => 'Kister, M. J. 1980. ‘Labbayka, Allāhumma, Labbayka… On a Monotheistic Aspect of a Jāhiliyya Practice’, Jerusalem Studies in Arabic and Islam 2: 33–57. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kister, M. J.',
                    'title' => 'Labbayka, All{\=a}humma, Labbayka… On a Monotheistic Aspect of a J{\=a}hiliyya Practice',
                    'journal' => 'Jerusalem Studies in Arabic and Islam',
                    'year' => '1980',
                    'volume' => '2',
                    'pages' => '33-57',
                    ]
            ],
			// publisher address not identified
			[
                'source' => 'Åžeyhun, Ahmet. 2014. Islamist Thinkers in the Late Ottoman Empire and Early Turkish Republic. Leiden: Brill. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Åžeyhun, Ahmet',
                    'title' => 'Islamist Thinkers in the Late Ottoman Empire and Early Turkish Republic',
                    'year' => '2014',
                    'address' => 'Leiden',
                    'publisher' => 'Brill',
                    ],
					'char_encoding' => 'utf8leave',
            ],
            // first word of journal name is included in title
			[
                'source' => 'Berhe, H. H. (2022). Application of kaizen philosophy for enhancing manufacturing industries performance: Exploratory study of Ethiopian chemical industries. The International Journal of Quality & Reliability Management, 39(1), 204-235. https://doi.org/10.1108/IJQRM-09-2020-0328 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1108/IJQRM-09-2020-0328',
                    'author' => 'Berhe, H. H.',
                    'year' => '2022',
                    'title' => 'Application of kaizen philosophy for enhancing manufacturing industries performance: Exploratory study of Ethiopian chemical industries',
                    'journal' => 'The International Journal of Quality & Reliability Management',
                    'volume' => '39',
                    'number' => '1',
                    'pages' => '204-235',
                    ]
            ],
   			// Translator abbreviated "Trans"
			[
                'source' => 'Koselleck, Reinhart. (2002). The Practice of Conceptual History: Timing History, Spacing Concepts. Trans. Todd Samuel Presner et al. Stanford: Stanford University Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Koselleck, Reinhart',
                    'title' => 'The Practice of Conceptual History: Timing History, Spacing Concepts',
                    'year' => '2002',
                    'note' => 'Trans. Todd Samuel Presner et al.',
                    'address' => 'Stanford',
                    'publisher' => 'Stanford University Press',
                    ]
            ],
			// access date and pages confused.  Probably it is online rather than article, but there seems to be no way
            // for the script to determine that.
			[
                'source' => 'World Cocoa Foundation. (2024). Improving farmer income through sustainable practices. World Cocoa Foundation. Accessed 25-05-2024.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'World Cocoa Foundation',
                    'year' => '2024',
                    'title' => 'Improving farmer income through sustainable practices',
                    'journal' => 'World Cocoa Foundation',
					'urldate' => '25-05-2024',
                    ]
            ],
            // remove colon before doi
            [
                'source' => 'Garlock, T. et al. Aquaculture: The missing contributor in the food security agenda. Global Food Security 32, 100620 (2022). https://doi.org:10.1016/j.gfs.2022.100620 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Garlock, T. and others',
                    'title' => 'Aquaculture: The missing contributor in the food security agenda',
                    'journal' => 'Global Food Security',
                    'year' => '2022',
                    'volume' => '32',
                    'pages' => '100620',
                    'doi' => '10.1016/j.gfs.2022.100620',
                    ]
            ],
			//
			[
				'source' => '"Olson ME, Rosell JA, Martínez-Pérez C, León-Gómez C, Fajardo A, Isnard S, Cervantes-Alcayde M-A, Echeverría A, Figueroa-Abúndiz A, Segovia-Rivas A, et al. in press. Xylem vessel diameter-shoot length scaling: ecological significance of porosity types and other traits. Ecological Monographs"',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Olson, M. E. and Rosell, J. A. and Martínez-Pérez, C. and León-Gómez, C. and Fajardo, A. and Isnard, S. and Cervantes-Alcayde, M.-A. and Echeverría, A. and Figueroa-Abúndiz, A. and Segovia-Rivas, A. and others',
                    'title' => 'Xylem vessel diameter-shoot length scaling: ecological significance of porosity types and other traits',
                    'journal' => 'Ecological Monographs',
                    'year' => 'in press',
                    ],
				'char_encoding' => 'utf8leave',
			],
   			// remove comma before journal name
			[
                'source' => 'A. Wachter and L. T. Biegler, \textit{ On the implementation of a primal-dual interior point filter line search algorithm for large-scale nonlinear programming.}‚  Mathematical Programming, 106(1):25-57, 2006.   ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'A. Wachter and L. T. Biegler',
                    'title' => 'On the implementation of a primal-dual interior point filter line search algorithm for large-scale nonlinear programming',
                    'year' => '2006',
                    'journal' => 'Mathematical Programming',
                    'volume' => '106',
                    'number' => '1',
                    'pages' => '25-57',
                    ]
            ],
			// volume not detected
			[
                'source' => 'Boyraz, G., Legros, D. N., & Tigershtrom, A. (2020). COVID-19 and traumatic stress: The role of perceived vulnerability, COVID-19-related worries, and social isolation. Journal of Anxiety Disorders, 76(July), 102307. https://doi.org/10.1016/j.janxdis.2020.102307 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Boyraz, G. and Legros, D. N. and Tigershtrom, A.',
                    'title' => 'COVID-19 and traumatic stress: The role of perceived vulnerability, COVID-19-related worries, and social isolation',
                    'journal' => 'Journal of Anxiety Disorders',
                    'year' => '2020',
                    'month' => 'July',
                    'volume' => '76',
                    'pages' => '102307',
                    'doi' => '10.1016/j.janxdis.2020.102307',
                    ]
            ],
			// date not picked up
			 [
                'source' => 'Centers for Disease Control and Prevention. (2023, December 14). CDC respiratory virus updates. Centers for Disease Control and Prevention. https://www.cdc.gov/respiratory-viruses/whats-new/index.html ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Centers for Disease Control and Prevention',
                    'title' => 'CDC respiratory virus updates',
                    'note' => 'Centers for Disease Control and Prevention',
                    'year' => '2023',
					'month' => 'December',
					'date' => '2023-12-14',
                    'urldate' => '2023, December 14',
                    'url' => 'https://www.cdc.gov/respiratory-viruses/whats-new/index.html',
                    ]
            ],
			// spaces after \' in names cause problems; also arXiv for published article
			// (more in conversion 5245)
			[
                'source' => '\bibitem{PRDYM} M. Huerta-Leal, H. Novales-S\\\' anchez, J. J. Toscano, \textit{A gauge-invariant approach to beta function in Yang-Mills theories with universal extra dimensions}, Phys. Rev. D \textbf{101}, 9, 095038 (2020),  arXiv:1704.07339v3 [hep-ph]. ',
                'type' => 'article',
                'bibtex' => [
                    'title' => 'A gauge-invariant approach to beta function in Yang-Mills theories with universal extra dimensions',
					'journal' => 'Phys. Rev. D',
					'volume' => '101',
					'number' => '9',
					'pages' => '095038',
                    'archiveprefix' => 'arXiv',
                    'eprint' => '1704.07339v3',
                    'author' => 'M. Huerta-Leal and H. Novales-S\\\'anchez and J. J. Toscano',
                    'year' => '2020',
                    ]
            ],
			// Example with period within title
			[
                'source' => '  \bibitem{AbGal1} Abellanas L and Galindo A, Conserved densities for nonlinear evolution equations I. Even order Case, {\it J. Math. Phys.} 20(6), 1239--1243, (1979). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Abellanas, L. and Galindo, A.',
                    'title' => 'Conserved densities for nonlinear evolution equations I. Even order Case',
                    'year' => '1979',
                    'journal' => 'J. Math. Phys.',
                    'volume' => '20',
                    'number' => '6',
                    'pages' => '1239-1243',
                    ]
            ],
			// Part of journal included in title
			[
                'source' => ' Acharjee, S. A., Bharali, P., Gogoi, B., Sorhie, V., Walling, B., & Alemtoshi. (2023). PHA-Based Bioplastic: a Potential Alternative to Address Microplastic Pollution. Water, Air, & Soil Pollution, 234(1), 21. https://doi.org/10.1007/s11270-022-06029-2 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Acharjee, S. A. and Bharali, P. and Gogoi, B. and Sorhie, V. and Walling, B. and Alemtoshi',
                    'title' => 'PHA-Based Bioplastic: a Potential Alternative to Address Microplastic Pollution',
                    'journal' => 'Water, Air, & Soil Pollution',
                    'year' => '2023',
                    'volume' => '234',
                    'number' => '1',
                    'pages' => '21',
                    'doi' => '10.1007/s11270-022-06029-2',
                    ]
            ],
			// 'translation by' rather than 'translated by'
			[
                'source' => 'Abiy, A. (2018, April 3) Full English Transcript of Ethiopian Prime Minister Abiy Ahmed’s Inaugural Address. Translation by Hassen Hussein. Published at: OPride. Available at: https://www.opride.com/2018/04/03/english-partial-transcript-of-ethiopian-prime-minister-abiy-ahmeds-inaugural-address/ (Accessed 2 January 2024) ',
                'type' => 'online',
                'bibtex' => [
                    'note' => 'Translation by Hassen Hussein. Published at: OPride',
                    'url' => 'https://www.opride.com/2018/04/03/english-partial-transcript-of-ethiopian-prime-minister-abiy-ahmeds-inaugural-address/',
                    'urldate' => '2 January 2024',
                    'month' => 'April',
                    'date' => '2018-04-03',
                    'author' => 'Abiy, A.',
                    'year' => '2018',
                    'title' => 'Full English Transcript of Ethiopian Prime Minister Abiy Ahmed\'s Inaugural Address',
					'note' => 'Translation by Hassen Hussein. Published at: OPride',
                    ]
            ],
			// use of \textendash{} (also \textendash ) --- replace with --.
			[
                'source' => '\bibitem[Lentz(1995)]{Lentz95}Lentz, S.J. 1995. Sensitivity of the inner-shelf circulation to the eddy-viscosity profile. J. Phys. Oceanogr. 25:19\textendash{} 28. https://doi.org/10.1175/1520-0485(1995)025\%3C0019:SOTISC\%3E2.0.CO;2 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Lentz, S. J.',
                    'title' => 'Sensitivity of the inner-shelf circulation to the eddy-viscosity profile',
                    'journal' => 'J. Phys. Oceanogr.',
                    'year' => '1995',
                    'volume' => '25',
                    'pages' => '19-28',
                    'doi' => '10.1175/1520-0485(1995)025\%3C0019:SOTISC\%3E2.0.CO;2',
                    ]
            ],
			// use of \textquoteright --- replace with '
			[
                'source' => '\bibitem[Fewings and Lentz (2010)]{Fewings10}Fewings, M.R., Lentz, S.J. 2010. Momentum balances on the inner continental shelf at Martha\textquoteright s Vineyard Coastal Observatory. J. Geophys. Res. 115:C12023. https://doi.org/10.1029/2009JC005578 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Fewings, M. R. and Lentz, S. J.',
                    'title' => 'Momentum balances on the inner continental shelf at Martha\'s Vineyard Coastal Observatory',
                    'journal' => 'J. Geophys. Res.',
                    'year' => '2010',
                    'volume' => '115',
                    'pages' => 'C12023',
                    'doi' => '10.1029/2009JC005578',
                    ]
            ],
   			// n.d. not interpreted as "no date"
			[
                'source' => 'Valohai. n.d. What Is a Machine Learning Pipeline? https://valohai.com/machine-learning-pipeline/. ',
                'type' => 'online',
                'bibtex' => [
                    'title' => 'What Is a Machine Learning Pipeline?',
                    'url' => 'https://valohai.com/machine-learning-pipeline/',
                    'author' => 'Valohai',
                    'year' => 'n.d.',
                    ]
            ],
			// detected as article
			[
                'source' => 'Earl, L. M. (2013). Assessment as Learning: Using classroom assessment to maximize student learning (2nd ed.). Corwin. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Earl, L. M.',
                    'title' => 'Assessment as Learning: Using classroom assessment to maximize student learning',
                    'year' => '2013',
                    'edition' => '2nd',
                    'publisher' => 'Corwin',
                    ]
            ],
			// last word of title, a country, included in journal
			[
                'source' => '[27] Patel, N., Bhattacharjee, B., Mohammed, A., Tanupriya, B., Saha, S., 2006. Remote sensing of regional yield assessment of wheat in Haryana, India. International Journal of Remote Sensing, 27(19): 4071-4090.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Patel, N. and Bhattacharjee, B. and Mohammed, A. and Tanupriya, B. and Saha, S.',
                    'year' => '2006',
                    'title' => 'Remote sensing of regional yield assessment of wheat in Haryana, India',
                    'journal' => 'International Journal of Remote Sensing',
                    'volume' => '27',
                    'number' => '19',
                    'pages' => '4071-4090',
                    ]
            ],
			// journal not detected --- detected as online rather than article (Andrea Mattia Marcelli)
			[
                'source' => 'Andreatta, C., Rossi, L., & Cianfriglia, M. C. (2024). Principals and the fragile balance between organizational, educational, and vocational training: Insights for an impactful post-pandemic school. Formazione & Insegnamento, 22(1). https://ojs.pensamultimedia.it/index.php/siref/article/view/6979 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Andreatta, C. and Rossi, L. and Cianfriglia, M. C.',
                    'title' => 'Principals and the fragile balance between organizational, educational, and vocational training: Insights for an impactful post-pandemic school',
                    'journal' => 'Formazione & Insegnamento',
                    'year' => '2024',
                    'volume' => '22',
                    'number' => '1',
                    'url' => 'https://ojs.pensamultimedia.it/index.php/siref/article/view/6979',
                    ]
            ],
			// authors ended early
			[
                'source' => 'Singh, A. S., Saliasi, E., van den Berg, V., Uijtdewilligen, L., de Groot, R. H. M., Jolles, J., Andersen, L. B., Bailey, R., Chang, Y. K., Diamond, A., Ericsson, I., Etnier, J. L., Fedewa, A. L., Hillman, C. H., McMorris, T., Pesce, C., Pühse, U., Tomporowski, P. D., & Chinapaw, M. J. M. (2019). Effects of physical activity interventions on cognitive and academic performance in children and adolescents: a novel combination of a systematic review and recommendations from an expert panel. British journal of sports medicine, 53(10), 640–647. https://doi.org/10.1136/bjsports-2017-098136 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Singh, A. S. and Saliasi, E. and van den Berg, V. and Uijtdewilligen, L. and de Groot, R. H. M. and Jolles, J. and Andersen, L. B. and Bailey, R. and Chang, Y. K. and Diamond, A. and Ericsson, I. and Etnier, J. L. and Fedewa, A. L. and Hillman, C. H. and McMorris, T. and Pesce, C. and Pühse, U. and Tomporowski, P. D. and Chinapaw, M. J. M.',
                    'title' => 'Effects of physical activity interventions on cognitive and academic performance in children and adolescents: a novel combination of a systematic review and recommendations from an expert panel',
                    'journal' => 'British journal of sports medicine',
                    'year' => '2019',
                    'volume' => '53',
                    'number' => '10',
                    'pages' => '640-647',
                    'doi' => '10.1136/bjsports-2017-098136',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// authors ended early; detected as unpublished, not article
			[
                'source' => '\bibitem{ADD}N. Arkani-Hamed, S. Dimopoulos, and G. R. Dvali, \textit{The Hierarchy problem and new dimensions at a millimeter}, Phys. Lett. B \textbf{429}, 263 (1998), arXiv:hep-ph/9803315 ',
                'type' => 'article',
                'bibtex' => [
                    'journal' => 'Phys. Lett. B',
					'volume' => '429',
					'pages' => '263',
                    'archiveprefix' => 'arXiv',
                    'eprint' => 'hep-ph/9803315',
                    'author' => 'N. Arkani-Hamed and S. Dimopoulos and G. R. Dvali',
                    'title' => 'The Hierarchy problem and new dimensions at a millimeter',
                    'year' => '1998',
                    ]
            ],
            // use of Ð in page range
			[
                'source' => '7.	Freeman C, Todd C, Camilleri-Ferrant... C, Laxton C, Murrell P, Palmer C, et al. Quality improvement for patients with hip fracture: experience from a multi-site audit. Qual Saf Health Care. 2002 Sep;11(3):239Ð45.Ê ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Freeman, C. and Todd, C. andCamilleri-Ferrant and others C. Laxton, C. and Murrell, P. and Palmer, C. and others',
                    'title' => 'Quality improvement for patients with hip fracture: experience from a multi-site audit',
                    'year' => '2002',
                    'month' => 'September',
                    'journal' => 'Qual Saf Health Care.',
                    'volume' => '11',
                    'number' => '3',
                    'pages' => '239-45',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// detected as article
			[
                'source' => 'Santos, B. de S. (2015). O direito dos oprimidos. Cortez. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Santos, B. de S.',
                    'title' => 'O direito dos oprimidos',
                    'year' => '2015',
                    'publisher' => 'Cortez',
                    ]
            ],
			// author string truncated
			[
                'source' => '\bibitem{Hillen1981} W.~Hillen, T.C.~Goodman, and R.D.~Wells, {\em  Salt dependence and thermodynamic interpretation of the thermal-denaturation of small dna restriction fragments},  Nucleic Acids Res., 9 (1981), pp.~415--436.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'W. Hillen and T. C. Goodman and R. D. Wells',
                    'title' => 'Wells',
                    'year' => '1981',
                    'title' => 'Salt dependence and thermodynamic interpretation of the thermal-denaturation of small dna restriction fragments',
                    'pages' => '415-436',
                    'journal' => 'Nucleic Acids Res.',
					'volume' => '9',
                    ]
            ],
			// author string truncated
			 [
                'source' => '\bibitem{Poland1966} D.~Poland and H.A.~Scheraga {\em Occurrence of a Phase Transition in Nucleic Acid Models}, J. Chem. Phys. 45, 1464 (1966), pp.~1464-1469 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'D. Poland and H. A. Scheraga',
                    'title' => 'Occurrence of a Phase Transition in Nucleic Acid Models',
                    'year' => '1966',
                    'journal' => 'J. Chem. Phys.',
                    'volume' => '45',
                    'number' => '1464',
                    'pages' => '1464-1469',
                    ]
            ],
            // classified as incollection instead of article
			[
                'source' => '\bibitem{He2016} G.~He, J.~Li, H.~Ci, C.~Qi, and X.~Guo, {\em Direct measurement of single-molecule dna hybridization dynamics with single-base resolution}, Angew. Chem. Int. Ed. Engl., 55 (2016), pp.~9036--9040. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'G. He and J. Li and H. Ci and C. Qi and X. Guo',
                    'title' => 'Direct measurement of single-molecule dna hybridization dynamics with single-base resolution',
                    'year' => '2016',
                    'pages' => '9036-9040',
                    'journal' => 'Angew. Chem. Int. Ed. Engl.',
                    'volume' => '55',
                    ]
            ],
   			// ISBN-13
			[
                'source' => 'Escolano Benito, A. (2023). Etnografia della scuola: La cultura materiale dell’educazione. Bergamo: Junior. 160 pp. € 24.00. ISBN-13: 978 8884349538. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Escolano Benito, A.',
                    'title' => 'Etnografia della scuola: La cultura materiale dell\'educazione',
                    'year' => '2023',
                    'address' => 'Bergamo',
                    'publisher' => 'Junior',
                    'isbn' => '9788884349538',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// title ended early
			[
                'source' => 'Han, B.-C. (2015). Nello sciame. Visioni del digitale. Roma: Nottetempo. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Han, B.-C.',
                    'title' => 'Nello sciame. Visioni del digitale',
                    'year' => '2015',
                    'address' => 'Roma',
                    'publisher' => 'Nottetempo',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// volume, translator
			[
                'source' => 'Ricoeur, P. (1997). Tempo e narrativa (Tomo III, R. L. Ferreira trans.). Papirus. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ricoeur, P.',
                    'title' => 'Tempo e narrativa',
                    'year' => '1997',
                    'note' => 'Tomo III, R. L. Ferreira trans.',
                    'publisher' => 'Papirus',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// translator
			[
                'source' => 'Ricoeur, P. (2014). O si-mesmo como outro (I. C. Benedetti trans.). São Paulo: Martins Fontes. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ricoeur, P.',
                    'title' => 'O si-mesmo como outro',
                    'year' => '2014',
                    'note' => 'I. C. Benedetti trans.',
                    'address' => 'São Paulo',
                    'publisher' => 'Martins Fontes',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// translator not detected
			[
                'source' => 'Ricœur, P. (2005). Percorsi del riconoscimento: Tre Studi (F. Polidori, Trans.). Raffaello Cortina. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ricœur, P.',
                    'title' => 'Percorsi del riconoscimento: Tre Studi',
                    'year' => '2005',
                    'note' => 'F. Polidori, Trans.',
                    'publisher' => 'Raffaello Cortina',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// year range confused with pages
			[
                'source' => 'Foucault, M. (1984). L’etica della cura di sé come pratica della libertà. In A. Pandolfi (Ed.), Archivio Foucault. Interventi, colloqui, interviste. 3, 1878-1985. Estetica dell’esistenza, etica, politica (pp. 273‍–‍294). Milano: Feltrinelli.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Foucault, M.',
                    'title' => 'L\'etica della cura di sé come pratica della libertà',
                    'year' => '1984',
                    'pages' => '273-294',
                    'editor' => 'A. Pandolfi',
                    'address' => 'Milano',
                    'publisher' => 'Feltrinelli',
                    'booktitle' => 'Archivio Foucault. Interventi, colloqui, interviste. 3, 1878-1985. Estetica dell\'esistenza, etica, politica',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// year range confused with pages
			 [
                'source' => 'Foucault, M. (2001). Les techniques de soi. In M. Foucault, Dits et écrits II, 1976–1988 (pp. 1602–1632). Paris: Gallimard.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Foucault, M.',
                    'title' => 'Les techniques de soi',
                    'year' => '2001',
                    'pages' => '1602-1632',
                    'editor' => 'M. Foucault',
                    'address' => 'Paris',
                    'publisher' => 'Gallimard',
                    'booktitle' => 'Dits et écrits II, 1976--1988',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// ending page number not detected
			[
                'source' => 'Gilligan, C. (1982). New maps of development: New visions of maturity. American Journal of Orthopsychiatry, 52(2), 199‍–‍212. https://doi.org/10.1111/j.1939-0025.1982.tb02682.x  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Gilligan, C.',
                    'title' => 'New maps of development: New visions of maturity',
                    'journal' => 'American Journal of Orthopsychiatry',
                    'year' => '1982',
                    'volume' => '52',
                    'number' => '2',
                    'pages' => '199-212',
                    'doi' => '10.1111/j.1939-0025.1982.tb02682.x',
                    ]
            ],
			// school not detected
			[
                'source' => 'Walubwa, J. (2010). Kenya Slum Upgrading Programme: An Analysis of Kibera Integrated Water, Sanitation and Waste Management Project [Master’s Thesis, University of Nairobi]. http://erepository.uonbi.ac.ke/handle/11295/4615 ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Walubwa, J.',
                    'title' => 'Kenya Slum Upgrading Programme: An Analysis of Kibera Integrated Water, Sanitation and Waste Management Project',
                    'year' => '2010',
                    'url' => 'http://erepository.uonbi.ac.ke/handle/11295/4615',
                    'school' => 'University of Nairobi',
                    ]
            ],
			// Title was not detected, because Phytochemistry is a journal.  But now that journal names in db are not used, the conversion is correct
			[
                'source' => 'Goyal, A.K., 2014. Phytochemistry and in vitro studies on anti-fertility effect of Ficus religiosa fruits extract on uterine morphology of goat (Capra hircus). Int J Drug Dev Res, 6(2), pp.141-158.‎ ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Goyal, A. K.',
                    'year' => '2014',
                    'title' => 'Phytochemistry and in vitro studies on anti-fertility effect of Ficus religiosa fruits extract on uterine morphology of goat (Capra hircus)',
                    'journal' => 'Int J Drug Dev Res',
                    'pages' => '141-158',
                    'volume' => '6',
                    'number' => '2',
                    ]
            ],
            // page numbers not picked up; problem with journal name because The Lancet is in the db
            [
                'source' => 'Allegranzi, B., Tartari, E., Loftus, M. J., Pires, D., Pittet, D., & Stall, N. (2020). Global infection prevention and control priorities 2018–22: a call for action. The Lancet Global Health, 8(4), e536-e537. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Allegranzi, B. and Tartari, E. and Loftus, M. J. and Pires, D. and Pittet, D. and Stall, N.',
                    'year' => '2020',
                    'title' => 'Global infection prevention and control priorities 2018--22: a call for action',
                    'journal' => 'The Lancet Global Health',
                    'volume' => '8',
                    'number' => '4',
					'pages' => 'e536-e537',
                    ]
            ],
			// en-dash in year needs to be processed correctly
			[
                'source' => 'Elman Yaakov. 1990–1991. ‘Righteousness As Its Own Reward: An Inquiry Into the Theologies of the Stam’, Proceedings of the American Academy for Jewish Research 57: 35–67. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Elman Yaakov',
                    'title' => 'Righteousness As Its Own Reward: An Inquiry Into the Theologies of the Stam',
                    'journal' => 'Proceedings of the American Academy for Jewish Research',
                    'year' => '1990--1991',
                    'volume' => '57',
                    'pages' => '35-67',
                    ]
            ],
            // string for "edited by" (Italian)
            [
                'source' => 'Rossi, P.G. (2016). Progettazione didattica e professionalità docente. PROPIT: l’artefatto progettuale come mediatore didattico. In P.G. Rossi, C. Giaconi (a cura di). Micro-progettazione: pratiche didattiche a confronto PROPIT, EAS, Flipped Classroom. Milano: Franco Angeli.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Rossi, P. G.',
                    'title' => 'Progettazione didattica e professionalità docente. PROPIT: l\'artefatto progettuale come mediatore didattico',
                    'year' => '2016',
                    'editor' => 'P. G. Rossi and C. Giaconi',
                    'address' => 'Milano',
                    'publisher' => 'Franco Angeli',
                    'booktitle' => 'Micro-progettazione: pratiche didattiche a confronto PROPIT, EAS, Flipped Classroom',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
            // editors not detected
            [
                'source' => 'Conejo da Pena, A., Bridgewater Mateu, P. (eds.) (2023). The Medieval and  Early Modern Hospital: A Physical and Symbolic Space. Roma: Viella. ',
                'type' => 'book',
                'bibtex' => [
                    'title' => 'The Medieval and Early Modern Hospital: A Physical and Symbolic Space',
                    'year' => '2023',
                    'editor' => 'Conejo da Pena, A. and Bridgewater Mateu, P.',
                    'address' => 'Roma',
                    'publisher' => 'Viella',
                    ]
            ],
            // editors not detected
            [
                'source' => 'Conejo da Pena, A., Bridgewater Mateu, P. (eds.) (2023). The Medieval and  Early Modern Hospital: A Physical and Symbolic Space. Roma: Viella. ',
                'type' => 'book',
                'bibtex' => [
                    'title' => 'The Medieval and Early Modern Hospital: A Physical and Symbolic Space',
                    'year' => '2023',
                    'editor' => 'Conejo da Pena, A. and Bridgewater Mateu, P.',
                    'address' => 'Roma',
                    'publisher' => 'Viella',
                    ]
            ],
			// working paper not identified
			[
                'source' => 'Silva, S. P. (2017). A organização coletiva de catadores de material reciclável no brasil: Dilemas e potencialidades sob a ótica da economia solidária (Texto para discussão 2268; p. 56). IPEA - Instituto de Pesquisa Econômica Aplicada. https://repositorio.ipea.gov.br/bitstream/11058/7413/1/td_2268.PDF ',
                'type' => 'techreport',
                'bibtex' => [
                    'author' => 'Silva, S. P.',
                    'title' => 'A organização coletiva de catadores de material reciclável no brasil: Dilemas e potencialidades sob a ótica da economia solidária',
                    'year' => '2017',
                    'number' => '2268',
                    'note' => 'p. 56',
                    'url' => 'https://repositorio.ipea.gov.br/bitstream/11058/7413/1/td_2268.PDF',
                    'institution' => 'IPEA - Instituto de Pesquisa Econômica Aplicada',
                    'type' => 'Texto para discussão',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// journal not detected
			[
                'source' => 'Varela, D. G., & Fedynich, L. (2020). Leading Schools From a Social Distance: Surveying South Texas School District Leadership During the COVID-19 Pandemic. National Forum of Educational, Administration and Supervision Journal, 38(4), Article 3. https://www.nationalforum.com/Electronic',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Varela, D. G. and Fedynich, L.',
                    'title' => 'Leading Schools From a Social Distance: Surveying South Texas School District Leadership During the COVID-19 Pandemic',
                    'journal' => 'National Forum of Educational, Administration and Supervision Journal',
                    'year' => '2020',
                    'volume' => '38',
                    'number' => '4',
                    'note' => 'Article 3',
                    'url' => 'https://www.nationalforum.com/Electronic',
                    ]
            ],
			// abbreviation for PhD dissertation
			[
                'source' => 'Kolpacoff, D. J. (2000). Papal Schism, Archiepiscopal Politics, and Waldensian Persecution (1378–1396): The Ecclesiopolitical Landscape of Late Fourteenth-Century Mainz. Ph.D. diss. Northwestern University. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Kolpacoff, D. J.',
                    'title' => 'Papal Schism, Archiepiscopal Politics, and Waldensian Persecution (1378--1396): The Ecclesiopolitical Landscape of Late Fourteenth-Century Mainz',
                    'year' => '2000',
                    'school' => 'Northwestern University',
                    ]
            ],
            // journal included in title
			[
                'source' => 'D.J. France, R.G. Shiavi, S. Silverman, M. Silverman, M. Wilkes, Acoustical properties of speech as indicators of depression and suicidal risk, IEEE Transactions on Biomedical Engineering. 47 (2000) 829?837. https://doi.org/10.1109/10.846676. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1109/10.846676',
                    'author' => 'D. J. France and R. G. Shiavi and S. Silverman and M. Silverman and M. Wilkes',
                    'title' => 'Acoustical properties of speech as indicators of depression and suicidal risk',
					'journal' => 'IEEE Transactions on Biomedical Engineering.',
                    'year' => '2000',
                    'volume' => '47',
                    'pages' => '829-837',
                    ]
            ],
			// first word of journal in title, full page number not detected.
			[
                'source' => 'Trindade, S. P., Camargo, R. A., Torres, P. L., & Kowalski, R. P. G. (2022). Escolarização aberta e as práticas pedagógicas de aprendizagem articuladas com o projeto CONNECT na educação básica. Research, Society and Development, 11(12), e393111234449. https://doi.org/10.33448/rsd-v11i12.34449 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Trindade, S. P. and Camargo, R. A. and Torres, P. L. and Kowalski, R. P. G.',
                    'title' => 'Escolarização aberta e as práticas pedagógicas de aprendizagem articuladas com o projeto CONNECT na educação básica',
                    'journal' => 'Research, Society and Development',
                    'year' => '2022',
                    'volume' => '11',
                    'number' => '12',
                    'pages' => 'e393111234449',
                    'doi' => '10.33448/rsd-v11i12.34449',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// very partial conversion
			 [
                'source' => 'Elias, M. J., Leverett, L., Duffel, J. C., Humphrey, & C., Ferrito, J. (2015). Integrating SEL with Related Prevention and Youth Development Approaches. In J.A. Durlak, C.E. Domitrovich, R.P. Weissberg & T.P. Gullotta (Eds.), Handbook of Social and Emotional Learning, Research and Practice (pp. 39–45). New York-London: The Guilford Press. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Elias, M. J. and Leverett, L. and Duffel, J. C. Humphrey and C. Ferrito, J.',
                    'title' => 'Integrating SEL with Related Prevention and Youth Development Approaches',
                    'year' => '2015',
                    'pages' => '39-45',
                    'editor' => 'J. A. Durlak and C. E. Domitrovich and R. P. Weissberg and T. P. Gullotta',
                    'address' => 'New York-London',
                    'publisher' => 'The Guilford Press',
                    'booktitle' => 'Handbook of Social and Emotional Learning, Research and Practice',
                    ]
            ],
			// replace ? with - in page range
			[
                'source' => 'Franklin, M. C., Carey, K. D., Vajdos, F. F., Leahy, D. J., de Vos, A. M., & Sliwkowski, M. X. (2004). Insights into ErbB signaling from the structure of the ErbB2-pertuzumab complex. Cancer Cell, 5(4), 317?328.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Franklin, M. C. and Carey, K. D. and Vajdos, F. F. and Leahy, D. J. and de Vos, A. M. and Sliwkowski, M. X.',
                    'year' => '2004',
                    'title' => 'Insights into ErbB signaling from the structure of the ErbB2-pertuzumab complex',
                    'journal' => 'Cancer Cell',
                    'volume' => '5',
                    'number' => '4',
                    'pages' => '317-328',
                    ]
            ],
			// booktitle truncated
			[
                'source' => 'Dziób, A., Piasecki, M., & Rudnicka, E. (2019). plWordNet 4.1–a Linguistically Motivated, Corpus-based Bilingual Resource. In: Fellbaum, C., Vossen, P., Rudnicka, E., Maziarz, M., & Piasecki, M. (Eds.), Proceedings of the 10th Global WordNet Conference: July 23-27, 2019, Wrocław (Poland), 352-362. Wrocław: Oficyna Wydawnicza Politechniki Wrocławskiej.  ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Dziób, A. and Piasecki, M. and Rudnicka, E.',
                    'year' => '2019',
                    'title' => 'plWordNet 4.1--a Linguistically Motivated, Corpus-based Bilingual Resource',
                    'pages' => '352-362',
                    'editor' => 'Fellbaum, C. and Vossen, P. and Rudnicka, E. and Maziarz, M. and Piasecki, M.',
                    'booktitle' => 'Proceedings of the 10th Global WordNet Conference: July 23-27, 2019, Wrocław (Poland)',
					'publisher' => 'Oficyna Wydawnicza Politechniki Wrocławskiej',
					'address' => 'Wrocław',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// comma after year in date
			[
                'source' => 'AFP. (2021, September 20). US blasts \'dangerous\' rhetoric by ally of Ethiopia PM. AFP. Mailonline. Available at: https://www.dailymail.co.uk/wires/afp/article-10010089/US-blasts-dangerous-rhetoric-ally-Ethiopia-PM.html (Accessed 17 January 2021). ',
                'type' => 'online',
                'bibtex' => [
                    'note' => 'AFP. Mailonline',
                    'url' => 'https://www.dailymail.co.uk/wires/afp/article-10010089/US-blasts-dangerous-rhetoric-ally-Ethiopia-PM.html',
                    'urldate' => '17 January 2021',
                    'month' => 'September',
                    'date' => '2021-09-20',
                    'author' => 'AFP',
                    'year' => '2021',
                    'title' => 'US blasts \'dangerous\' rhetoric by ally of Ethiopia PM',
                    ]
            ],
			// remove 'retrived from' from note
			 [
                'source' => 'Addis Standard. (2020, September 11). Tigray election: beyond defying the central government. Addis Standard. Retrieved from: https://addisstandard.com/analysis-tigray-election-beyond-defying-the-central-government/ (Accessed 17 September 2023) ',
                'type' => 'online',
                'bibtex' => [
                    'note' => 'Addis Standard',
                    'url' => 'https://addisstandard.com/analysis-tigray-election-beyond-defying-the-central-government/',
                    'urldate' => '17 September 2023',
                    'month' => 'September',
                    'date' => '2020-09-11',
                    'author' => 'Addis Standard',
                    'year' => '2020',
                    'title' => 'Tigray election: beyond defying the central government',
                    ]
            ],
			// edition not detected
			 [
                'source' => '\bibitem[Kosiur(2001)]{Kosiur01}Kosiur, D., 2001. Understanding Policy-Based Networking. Wiley, New York, NY, 2nd edition. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Kosiur, D.',
                    'year' => '2001',
                    'title' => 'Understanding Policy-Based Networking',
                    'publisher' => 'Wiley',
					'address' => 'New York, NY',
                    'edition' => '2nd',
                    ]
            ],
			// name truncated, journal not detected 
			[
                'source' => 'Carmo, M. S. do. (2009). A Semântica ‘Negativa’ do Lixo como Aspecto ‘Positivo’—Um Estudo de Caso sobre uma Associação de Recicladores na Cidade do Rio de Janeiro, Brasil. Administração Pública e Gestão Social, 1(2), 121–150. https://periodicos.ufv.br/apgs/article/view/4001 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Carmo, M. S. do',
                    'title' => 'A Semântica `Negativa\' do Lixo como Aspecto `Positivo\'---Um Estudo de Caso sobre uma Associação de Recicladores na Cidade do Rio de Janeiro, Brasil',
                    'journal' => 'Administração Pública e Gestão Social',
                    'year' => '2009',
                    'volume' => '1',
                    'number' => '2',
                    'pages' => '121-150',
                    'url' => 'https://periodicos.ufv.br/apgs/article/view/4001',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// journal included in title; issue not parsed correctly
			[
                'source' => 'Chengning Wang, Dan Feng, Wei Tong, Yu Hua, Jingning Liu, Bing Wu, Wei Zhao, Linghao Song, Yang Zhang, Jie Xu, Xueliang Wei, Yiran Chen. Improving Multilevel Writes on Vertical 3-D Cross-Point Resistive Memory. IEEE Transactions on Computer-Aided Design of Integrated Circuits and Systems. Vol. 40, Issue: 4, April 2021, pages: 762-775 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Chengning Wang and Dan Feng and Wei Tong and Yu Hua and Jingning Liu and Bing Wu and Wei Zhao and Linghao Song and Yang Zhang and Jie Xu and Xueliang Wei and Yiran Chen',
                    'title' => 'Improving Multilevel Writes on Vertical 3-D Cross-Point Resistive Memory',
					'journal' => 'IEEE Transactions on Computer-Aided Design of Integrated Circuits and Systems.',
                    'year' => '2021',
                    'month' => 'April',
                    'pages' => '762-775',
                    'volume' => '40',
					'number' => '4',
                    ]
            ],
			// letter before issue and page numbers
			[
                'source' => 'McGee Z.A., Jones B.D., Reconceptualizing the policy subsystem: integration with complexity theory and social network analysis, Policy Studies Journal, 47, S1, pp. S138-S158, (2019) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'McGee, Z. A. and Jones, B. D.',
                    'title' => 'Reconceptualizing the policy subsystem: integration with complexity theory and social network analysis',
                    'year' => '2019',
					'volume' => '47',
					'number' => 'S1',
                    'journal' => 'Policy Studies Journal',
					'pages' => 'S138-S158',
                    ]
            ],
			// number range not detected
			[
                'source' => '\bibitem{So} V.\ Sordoni. \emph{Reduction scheme for semiclassical operator-valued Schr\"{o}dinger type equation and application to scattering}, Comm. Partial Differential Equations \textbf{28}, no. 7-8, 1221--1236 (2003).  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'V. Sordoni',
                    'title' => 'Reduction scheme for semiclassical operator-valued Schr\"{o}dinger type equation and application to scattering',
                    'journal' => 'Comm. Partial Differential Equations',
                    'year' => '2003',
                    'volume' => '28',
                    'number' => '7-8',
                    'pages' => '1221-1236',
                    ]
            ],
			// editor's name was repeated in result
			[
                'source' => 'Hotelling H. A generalized T test and measure of multivariate dispersion. In: Neyman J, ed. Proceedings of the Second Berkeley Symposium on Mathematical Statistics and Probability. Berkeley: University of California Press; 1951:23-41. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Hotelling, H.',
                    'title' => 'A generalized T test and measure of multivariate dispersion',
                    'year' => '1951',
                    'pages' => '23-41',
                    'editor' => 'Neyman, J.',
                    'publisher' => 'University of California Press',
                    'address' => 'Berkeley',
                    'booktitle' => 'Proceedings of the Second Berkeley Symposium on Mathematical Statistics and Probability',
                    ]
            ],
			// First word of title included in authors
			[
                'source' => 'Baker, M. P., Reynolds, H. M., Lumicisi, B. & Bryson, C. J. Immunogenicity of protein therapeutics: The key causes, consequences and challenges. Self Nonself 1, 314–322 (2010). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Baker, M. P. and Reynolds, H. M. and Lumicisi, B. and Bryson, C. J.',
                    'title' => 'Immunogenicity of protein therapeutics: The key causes, consequences and challenges',
                    'year' => '2010',
                    'journal' => 'Self Nonself',
                    'volume' => '1',
                    'pages' => '314-322',
                    ]
            ],
			// detected as article
			[
                'source' => 'Bouckaert, Luk; Hendrik Opdebeeck; and Laszlo Zsolnai. 2008. ‘Why frugality?’, in Frugality: Rebalancing Material and Spiritual Values in Economic Life. Bern: Peter Lang, 3–23. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Bouckaert, Luk and Hendrik Opdebeeck and Laszlo Zsolnai',
                    'title' => 'Why frugality?',
                    'year' => '2008',
                    'pages' => '3-23',
                    'address' => 'Bern',
                    'publisher' => 'Peter Lang',
                    'booktitle' => 'Frugality: Rebalancing Material and Spiritual Values in Economic Life',
                    ]
            ],
			// detected as incollection
			[
                'source' => 'Gutiérrez, Gustavo. 1988. A Theology of Liberation: History, Politics, and Salvation. Revised ed. Maryknoll, NY: Orbis Books. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Guti{\\\'e}rrez, Gustavo',
                    'title' => 'A Theology of Liberation: History, Politics, and Salvation',
                    'year' => '1988',
                    'address' => 'Maryknoll, NY',
                    'publisher' => 'Orbis Books',
                    'edition' => 'Revised',
                    ]
            ],
			// detected as incollection
			[
                'source' => '[261]	E. Imani and H.-R. Pourreza, "A novel method for retinal exudate segmentation using signal separation algorithm," Computer Methods and Programs in Biomedicine, vol. 133, pp. 195-205, 2016/09/01/ 2016, doi: https://doi.org/10.1016/j.cmpb.2016.05.016. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.cmpb.2016.05.016',
                    'author' => 'E. Imani and H.-R. Pourreza',
                    'title' => 'A novel method for retinal exudate segmentation using signal separation algorithm',
                    'year' => '2016',
                    'pages' => '195-205',
                    'journal' => 'Computer Methods and Programs in Biomedicine',
					'volume' => '133',
					'date' => '2016-09-01',
                    ]
            ],
			// remove parens around volume
			[
                'source' => 'Inaraja, M. (2003). Fuerzas Armadas y sociedad en México: hacia un proyecto integrado para el siglo XXI. Cuadernos de estrategia, (123), 59-83. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Inaraja, M.',
                    'year' => '2003',
                    'title' => 'Fuerzas Armadas y sociedad en México: hacia un proyecto integrado para el siglo XXI',
                    'journal' => 'Cuadernos de estrategia',
                    'pages' => '59-83',
                    'volume' => '123',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// use of [C] at end of title
			[
                'source' => 'Ahmed A., Shervashidze N., Narayanamurthy S., Josifovski V., Smola A.J. Distributed large-scale natural graph factorization[C]. Proceedings of the 22nd International Conference on World Wide Web, 2013: 37--48. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Ahmed, A. and Shervashidze, N. and Narayanamurthy, S. and Josifovski, V. and Smola, A. J.',
                    'title' => 'Distributed large-scale natural graph factorization',
                    'year' => '2013',
                    'pages' => '37-48',
                    'booktitle' => 'Proceedings of the 22nd International Conference on World Wide Web',
                    ]
            ],
			// use of [C] at end of title, and also //
			[
                'source' => '[34]Ashish Vaswani, Noam Shazeer, Niki Parmar, et al.  Attention is all you need[C]// Proceedings of the International Conference on Neural Information Processing Systems (NIPS). 2017: 6000-6010. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Ashish Vaswani and Noam Shazeer and Niki Parmar and others',
                    'title' => 'Attention is all you need',
					'booktitle' => 'Proceedings of the International Conference on Neural Information Processing Systems (NIPS)',
                    'year' => '2017',
                    'pages' => '6000-6010',
                    ]
            ],
			// detected as online rather than book
			[
                'source' => 'Barr, D. A. (2019). Health disparities in the United States: Social class, race, ethnicity, and health (3rd ed.). Johns Hopkins University Press. https://jhupbooks.press.jhu.edu/title/health-disparities-united-states ',
                'type' => 'book',
                'bibtex' => [
                    'url' => 'https://jhupbooks.press.jhu.edu/title/health-disparities-united-states',
                    'author' => 'Barr, D. A.',
                    'year' => '2019',
                    'edition' => '3rd',
                    'title' => 'Health disparities in the United States: Social class, race, ethnicity, and health',
					'publisher' => 'Johns Hopkins University Press',
                    ]
            ],
			// tesis de grado
			[
                'source' => 'Aguilar, E. (2019). La reformulación del sentido de vida en personas de la tercera edad a partir de la jubilación. [Tesis de grado, Instituto Tecnológico y de Estudios Superiores de Occidente] https://rei.iteso.mx/bitstream/handle/11117/7513/TOG',
                'type' => 'phdthesis',
                'bibtex' => [
                    'url' => 'https://rei.iteso.mx/bitstream/handle/11117/7513/TOG',
                    'author' => 'Aguilar, E.',
                    'year' => '2019',
                    'title' => 'La reformulación del sentido de vida en personas de la tercera edad a partir de la jubilación',
                    'school' => 'Instituto Tecnológico y de Estudios Superiores de Occidente',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// 'available' should not be included in Note
			[
                'source' => '\bibitem{RNN} M. I. Jordan, "Serial order: A parallel distributed processing approach", in \textit{Advances in Psychology}, Elsevier, 1997, pp. 471-495. Available: \url{http://faculty.otterbein.edu/dstucki/COMP4230/Jordan-TR-8604-OCRed.pdf} ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'M. I. Jordan',
                    'title' => 'Serial order: A parallel distributed processing approach',
                    'year' => '1997',
                    'pages' => '471-495',
                    'url' => 'http://faculty.otterbein.edu/dstucki/COMP4230/Jordan-TR-8604-OCRed.pdf',
                    'publisher' => 'Elsevier',
                    'booktitle' => 'Advances in Psychology',
                    ]
            ],
            // publisher and address interchanged
			[
                'source' => 'Ferraro, Vincent. 1996. Dependency Theory: An Introduction. Mount Holyoke College: South Hadley, MA. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ferraro, Vincent',
                    'title' => 'Dependency Theory: An Introduction',
                    'year' => '1996',
                    'address' => 'South Hadley, MA',
                    'publisher' => 'Mount Holyoke College',
                    ]
            ],
   			// remove [Google Scholar] etc. at end of reference (or put it in note)
			[
                'source' => '    Kim, S.H.; Lord, C. Restricted and repetitive behaviors in toddlers and preschoolers with autism spectrum disorders based on the Autism Diagnostic Observation Schedule (ADOS). Autism Res. 2010, 3, 162–173. [Google Scholar] [CrossRef] [Green Version] ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kim, S. H. and Lord, C.',
                    'title' => 'Restricted and repetitive behaviors in toddlers and preschoolers with autism spectrum disorders based on the Autism Diagnostic Observation Schedule (ADOS)',
                    'year' => '2010',
                    'journal' => 'Autism Res',
                    'pages' => '162-173',
                    'volume' => '3',
                    ]
            ],
			// remove [Google Scholar] etc. at end of reference (or put it in note)
			[
                'source' => '    Morgan, L.; Wetherby, A.M.; Barber, A. Repetitive and stereotyped movements in children with autism spectrum disorders late in the second year of life. J. Child Psychol. Psychiatry 2008, 49, 826–837. [Google Scholar] [CrossRef] [PubMed] [Green Version] ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Morgan, L. and Wetherby, A. M. and Barber, A.',
                    'title' => 'Repetitive and stereotyped movements in children with autism spectrum disorders late in the second year of life',
                    'year' => '2008',
                    'journal' => 'J. Child Psychol. Psychiatry',
                    'pages' => '826-837',
                    'volume' => '49',
                    ]
            ],
            // von name "i"?
            [
                'source' => '	K. Hawton, C. C. i Comabella, C. Haw, and K. Saunders, Risk factors for suicide in individuals with depression: a systematic review, Journal of affective disorders, vol. 147, no. 1-3, pp. 17-28, 2013. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'K. Hawton and C. C. i Comabella and C. Haw and K. Saunders',
                    'title' => 'Risk factors for suicide in individuals with depression: a systematic review',
                    'year' => '2013',
                    'journal' => 'Journal of affective disorders',
                    'pages' => '17',
                    'volume' => '147',
                    'number' => '1-3',
                    'pages' => '17-28',
                    ]
            ],
            //
            [
                'source' => '	A. T. Beck, R. A. Steer, and G. K. Brown, Beck depression inventory-II, San Antonio, vol. 78, no. 2, pp. 490-498, 1996. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'A. T. Beck and R. A. Steer and G. K. Brown',
                    'title' => 'Beck depression inventory-II',
                    'year' => '1996',
                    'journal' => 'San Antonio',
                    'volume' => '78',
                    'number' => '2',
                    'pages' => '490-498',
                    ]
            ],
            // master's degree thesis
            [
                'source' => 'Ashenati, C. (2015). Aging and retirement among Ethiopian Elderly, adjustment, challenges and policy, Master’s Degree Thesis, Addis Ababa University, Ethiopia. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Ashenati, C.',
                    'year' => '2015',
                    'title' => 'Aging and retirement among Ethiopian Elderly, adjustment, challenges and policy',
                    'school' => 'Addis Ababa University, Ethiopia',
                    ]
            ],
            // master's degree thesis
            [
                'source' => 'Mboga, S. M., (2014). Social and Cultural dynamics of the after retirement from public service in Kenya: A case of study of retirees from Kenyatta National Hospital, Nairobi Kenya, Master’s Degree Thesis, University of Nairobi. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Mboga, S. M.',
                    'year' => '2014',
                    'title' => 'Social and Cultural dynamics of the after retirement from public service in Kenya: A case of study of retirees from Kenyatta National Hospital, Nairobi Kenya',
                    'school' => 'University of Nairobi',
                    ]
            ],
            // masters thesis
            [
                'source' => 'Nyaboke, G. (2016). Challenges affecting the livelihood of teachers after retirement in Kenya: A case study of Kisii central sub- country in Kisii country, Kenya. Masters, Thesis, University of Nairobi Kenya. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Nyaboke, G.',
                    'year' => '2016',
                    'title' => 'Challenges affecting the livelihood of teachers after retirement in Kenya: A case study of Kisii central sub- country in Kisii country, Kenya',
                    'school' => 'University of Nairobi Kenya',
                    ]
            ],
			// two-part name not parsed correctly
			[
                'source' => 'Jiang Yong, Zhang Yugeng, Liang Wenju, et al. (2003). Spatial variability of exchangeable magnesium in arable land. Journal of Shenyang Agricultural University, 181-184. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Jiang Yong and Zhang Yugeng and Liang Wenju and others',
                    'year' => '2003',
                    'title' => 'Spatial variability of exchangeable magnesium in arable land',
                    'journal' => 'Journal of Shenyang Agricultural University',
                    'pages' => '181-184',
                    ]
            ],
			// organization name not detected correctly
			[
                'source' => '[7] CDC. CDC Fluview weekly report national, regional, and state/jurisdiction level outpatient illness and viral surveillance application quick reference guide, October 2017.  ',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'CDC',
                    'title' => 'CDC Fluview weekly report national, regional, and state/jurisdiction level outpatient illness and viral surveillance application quick reference guide',
                    'year' => '2017',
                    'month' => 'October',
                    ]
            ],
            // author should not be preceded by 'and'
            [
                'source' => 'Microsoft. "Dynamic Host Configuration Protocol (DHCP)." Microsoft, n.d. [Online]. Available: https://docs.microsoft.com/en-us/windows-server/networking/technologies/dhcp/dhcp-top ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://docs.microsoft.com/en-us/windows-server/networking/technologies/dhcp/dhcp-top',
                    'author' => 'Microsoft',
                    'title' => 'Dynamic Host Configuration Protocol (DHCP)',
                    'note' => 'Microsoft, n.d',
                    ]
            ],
            // Author CDC
            [
                'source' => 'CDC, “Heart Disease Facts,” Centers for Disease Control and Prevention, May 15, 2023. https://www.cdc.gov/heartdisease/facts.htm#:~:text=Heart',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.cdc.gov/heartdisease/facts.htm#:~:text=Heart',
                    'author' => 'CDC',
                    'title' => 'Heart Disease Facts',
                    'year' => '2023',
                    'note' => 'Centers for Disease Control and Prevention, May 15',
                    ]
            ],
			// booktitle truncated
			[
                'source' => '\bibitem[Andler(1979)]{Andler79}Andler, S., 1979. Predicate Path expressions. Proceedings of the 6th. ACM SIGACT-SIGPLAN symposium on Principles of Programming Languages, POPL \'79. ACM Press, New York, NY, pp. 226--236. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Andler, S.',
                    'year' => '1979',
                    'title' => 'Predicate Path expressions',
                    'pages' => '226-236',
                    'publisher' => 'ACM Press',
                    'address' => 'New York, NY',
                    'booktitle' => 'Proceedings of the 6th. ACM SIGACT-SIGPLAN symposium on Principles of Programming Languages, POPL \'79',
                    ]
            ],
			// try to deal with lack of spaces
			[
                'source' => '8. 	Brandon ML, Haynes PT, Bonamo JR, Flynn MII, Barrett GR, Sherman MF. The Association Between Posterior-Inferior Tibial Slope and Anterior Cruciate Ligament Insufficiency. Arthrosc - J Arthrosc Relat Surg. 2006;22(8):894-899. doi:10.1016/j.arthro.2006.04.098 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.arthro.2006.04.098',
                    'author' => 'Brandon, M. L. and Haynes, P. T. and Bonamo, J. R. and Flynn, M. I. I. and Barrett, G. R. and Sherman, M. F.',
                    'year' => '2006',
					'volume' => '22',
					'number' => '8',
					'pages' => '894-899',
                    'title' => 'The Association Between Posterior-Inferior Tibial Slope and Anterior Cruciate Ligament Insufficiency',
					'journal' => 'Arthrosc - J Arthrosc Relat Surg.',
                    ]
            ],
			// need to detect "Chapter N in ..."
			[
                'source' => 'Koopmans, T. and W. Hood (1953), “The Estimation of Simultaneous Linear Economic Relationships,” Chapter 6 in Hood, W. and T. Koopmans (editors) Studies in Econometric Method, Cowles Commission Monograph No. 14. New York: Wiley, 112-199. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Koopmans, T. and W. Hood',
                    'year' => '1953',
                    'title' => 'The Estimation of Simultaneous Linear Economic Relationships',
                    'pages' => '112-199',
                    'editor' => 'Hood, W. and T. Koopmans',
                    'publisher' => 'Wiley',
                    'booktitle' => 'Studies in Econometric Method, Cowles Commission Monograph No. 14.',
					'address' => 'New York',
                    'chapter' => '6',
                    ]
            ],
			// Chapter + other problems
			[
                'source' => 'Barrett, C.B. 2002. "Food Security and Food Assistance Programs." Chapter 40 in B. Gardner and G. Rausser, eds. Handbook of Agricultural Economics, Volume 2. Amsterdam: Elsevier. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Barrett, C. B.',
                    'year' => '2002',
                    'title' => 'Food Security and Food Assistance Programs',
                    'booktitle' => 'Handbook of Agricultural Economics, Volume 2',
                    'publisher' => 'Elsevier',
                    'address' => 'Amsterdam',
                    'editor' => 'B. Gardner and G. Rausser',
					'chapter' => '40',
                    ]
            ],
			// detect chapter?
			[
                'source' => 'Archetti, C., Speranza, M.G., and Vigo, D. 2014. Vehicle Routing Problems with Profits. In  Paolo Toth and Daniele Vigo (eds.), Vehicle Routing: Problems, Methods, and Applications, Second Edition. Society for Industrial and Applied Mathematics, chapter 10, 273–297. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Archetti, C. and Speranza, M. G. and Vigo, D.',
                    'title' => 'Vehicle Routing Problems with Profits',
					'booktitle' => 'Vehicle Routing: Problems, Methods, and Applications, Second Edition',
					'editor' => 'Paolo Toth and Daniele Vigo',
                    'publisher' => 'Society for Industrial and Applied Mathematics',
                    'year' => '2014',
                    'chapter' => '10',
                    'pages' => '273-297',
                    ]
            ],
   			// Use of 'Geopend' for 'opened'/'viewed'.
			[
                'source' => '[2] 	A. Levido, „NTC Thermistor Linearization,” circuit cellar, 4 Augustus 2021. [Online]. Available: https://circuitcellar.com/resources/quickbits/ntc-thermistor linearization-2/. [Geopend 1 11 2023]. ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://circuitcellar.com/resources/quickbits/ntc-thermistor',
                    'urldate' => '1 11 2023',
                    'author' => 'A. Levido',
                    'title' => 'NTC Thermistor Linearization',
                    'note' => 'circuit cellar',
                    'year' => '2021',
                    'month' => 'augustus',
                    'date' => '2021-08-04',
                ],
                'language' => 'nl',
            ],
			// pp not removed; detected as article not inproceedings
			[
                'source' => 'Banerjee, I., Nguyen, B. D., & Robbins, R. (2013). GUI testing of mobile applications: Challenges and future research directions. In Proceedings of the IEEE International Conference on Software Testing, Verification and Validation (pp. 119-128). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Banerjee, I. and Nguyen, B. D. and Robbins, R.',
                    'year' => '2013',
                    'title' => 'GUI testing of mobile applications: Challenges and future research directions',
                    'booktitle' => 'Proceedings of the IEEE International Conference on Software Testing, Verification and Validation',
                    'pages' => '119-128',
                    ]
            ],
			// space before closing parens for year
			[
                'source' => 'Amabile, T., & Kramer, S. (2011 ). The progress principle: Using small wins to ignite joy, engagement, and creativity at work. Harvard Business Press. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Amabile, T. and Kramer, S.',
					'year' => '2011',
                    'title' => 'The progress principle: Using small wins to ignite joy, engagement, and creativity at work',
                    'publisher' => 'Harvard Business Press',
                    ]
            ],
			// spaces around parens for year
			[
                'source' => 'Benedek, M., Karstendiek, M., Ceh, S. M., Grabner, R. H., Krammer, G., Lebuda, I., & Kaufman, J. C. ( 2021 ). Creativity myths: Prevalence and correlates of misconceptions on creativity. Personality and Individual Differences, 182, 111068. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Benedek, M. and Karstendiek, M. and Ceh, S. M. and Grabner, R. H. and Krammer, G. and Lebuda, I. and Kaufman, J. C.',
                    'year' => '2021',
                    'title' => 'Creativity myths: Prevalence and correlates of misconceptions on creativity',
                    'journal' => 'Personality and Individual Differences',
					'volume' => '182',
					'pages' => '111068',
                    ]
            ],
			// should report "journal missing"
			[
                'source' => '[4]	J. K. Bambara, V. Wadley, C. Owsley, R. C. Martin, C. Porter, and L. E. Dreer, "Family functioning and low vision: A systematic review," vol. 103, no. 3, pp. 137-149, 2009. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. K. Bambara and V. Wadley and C. Owsley and R. C. Martin and C. Porter and L. E. Dreer',
                    'title' => 'Family functioning and low vision: A systematic review',
                    'year' => '2009',
                    'volume' => '103',
                    'number' => '3',
                    'pages' => '137-149',
                    ]
            ],
            // Title includes journal; classified as online, not article
            [
                'source' => 'Pitts NB, Zero DT, Marsh PD, Ekstrand K, Weintraub JA, Ramos-Gomez F, et al. Dental caries. Nat Rev Dis Prim [Internet]. 2017;3(1):17030. Available from: https://doi.org/10.1038/nrdp.2017.30 ',
                'type' => 'article',
                'bibtex' => [
                    'volume' => '3',
                    'number' => '1',
                    'pages' => '17030',
                    'doi' => '10.1038/nrdp.2017.30',
                    'author' => 'Pitts, N. B. and Zero, D. T. and Marsh, P. D. and Ekstrand, K. and Weintraub, J. A. and Ramos-Gomez, F. and others',
                    'title' => 'Dental caries',
                    'journal' => 'Nat Rev Dis Prim',
                    'year' => '2017',
                    ]
            ],
			// phrases in Georgian
			[
                'source' => 'Armbruster, B. B. (1989). Metacognition in creativity. R. R. J. A. Glorver (რედ.), Handbook of creativity (გვ. 177–182). New York, NY: Springer',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Armbruster, B. B.',
                    'year' => '1989',
                    'title' => 'Metacognition in creativity',
                    'pages' => '177-182',
                    'editor' => 'R. R. J. A. Glorver',
                    'publisher' => 'Springer',
                    'address' => 'New York, NY',
                    'booktitle' => 'Handbook of creativity',
                    ]
            ],
			// pp. in Georgian
			[
                'source' => 'Zhou, J., & Shalley, C. E. (2003). Employee Creativity: A Critical Review and Directions for Future Research. Research in Personnel and Human Resources Management (გვ. 165–217). doi:10.1016/s0742-7301(03)22004-1  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/s0742-7301(03)22004-1',
                    'author' => 'Zhou, J. and Shalley, C. E.',
                    'year' => '2003',
                    'title' => 'Employee Creativity: A Critical Review and Directions for Future Research',
                    'journal' => 'Research in Personnel and Human Resources Management',
					'pages' => '165-217',
                    ]
            ],
            //
            [
                'source' => ' 	M.KABORI, «Problématique de la gestion des stocks dans les secteurs hotéliers. Cas de l\'hôtel Lac Kivu Lodge de 2009 à 2011,» Université Libre des Pays des Grands Lacs - Graduat 2012, [En ligne]. Available: https://www.memoireonline.com/07/12/6012/m_Problematique-de-la-gestion-des-stocks-dans-les-secteurs-hoteliers-Cas-de-lhtel-Lac-Kivu-Lod4.html. [Accès le 11 04 2024]. ',
                'type' => 'online',
                'bibtex' => [
                    'note' => 'Université Libre des Pays des Grands Lacs - Graduat',
                    'url' => 'https://www.memoireonline.com/07/12/6012/m_Problematique-de-la-gestion-des-stocks-dans-les-secteurs-hoteliers-Cas-de-lhtel-Lac-Kivu-Lod4.html',
                    'author' => 'M. Kabori',
                    'title' => 'Problématique de la gestion des stocks dans les secteurs hotéliers. Cas de l\'hôtel Lac Kivu Lodge de 2009 à 2011',
                    'year' => '2012',
					'urldate' => '11 04 2024',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// editors not identified
            [
                'source' => 'PASSOS, E.; BARROS, R. B. D. A cartografia como método de pesquisa-intervenção. In: PASSOS, E.; VIRGÍNIA, K.; ESCÓSSIA, L. D. Pistas do método da cartografia: pesquisa-intervenção e produção de subjetividade. Porto Alegre: Sulina, 2009. p. 207. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Passos, E. and Barros, R. B. D.',
                    'title' => 'A cartografia como método de pesquisa-intervenção',
                    'year' => '2009',
                    'editor' => 'Passos, E. and Virgínia, K. and Escóssia, L. D.',
					'booktitle' => 'Pistas do método da cartografia: pesquisa-intervenção e produção de subjetividade',
					'address' => 'Porto Alegre',
					'publisher' => 'Sulina',
					'pages' => '207',
                    ],
					'language' => 'pt',
					'char_encoding' => 'utf8leave',
            ],
			// When pages are removed, space should be added to separate items
			[
                'source' => 'Baron,M. et al. (2016) A Single-Cell Transcriptomic Map of the Human and Mouse Pancreas Reveals Inter- and Intra-cell Population Structure. Cell Syst, 3, 346–360 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Baron, M. and others',
                    'year' => '2016',
                    'title' => 'A Single-Cell Transcriptomic Map of the Human and Mouse Pancreas Reveals Inter- and Intra-cell Population Structure',
                    'journal' => 'Cell Syst',
                    'pages' => '346-360',
                    'volume' => '3',
                    ]
            ],
			// Spanish: "recuperado ..."
			[
                'source' => 'Caldeira, K., & Wickett, M. E. (25 de septiembre de 2003). Anthropogenic carbon and ocean pH. Nature. Recuperado el 20 de mayo de 2024, de https://doi.org/10.1038/425365a ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1038/425365a',
                    'month' => 'septiembre',
                    'date' => '2003-09-25',
                    'author' => 'Caldeira, K. and Wickett, M. E.',
                    'year' => '2003',
                    'title' => 'Anthropogenic carbon and ocean pH',
                    'journal' => 'Nature',
                    'url' => 'https://doi.org/10.1038/425365a',
					'urldate' => '20 de mayo de 2024',
                    ],
                    'language' => 'es',
            ],
			// Spanish: "recuperado ..."
			 [
                'source' => 'Kind Designs. (20 de febrero de 2024). Parametric Architecture. Recuperado el 14 de mayo de 2024, de https://parametric-architecture.com/kind-designs-installed-the-worlds-first-3d-printed-living-seawall/ ',
                'type' => 'online',
                'bibtex' => [
					'urldate' => '14 de mayo de 2024',
                    'url' => 'https://parametric-architecture.com/kind-designs-installed-the-worlds-first-3d-printed-living-seawall/',
                    'month' => 'febrero',
                    'date' => '2024-02-20',
                    'author' => 'Kind Designs',
                    'year' => '2024',
                    'title' => 'Parametric Architecture',
                    ],
                    'language' => 'es',
            ],
			// Correct dates
			[
                'source' => 'UNEP. (28 de febrero de 2020). Naciones Unidas Para el Medio Ambiente. Recuperado el 17 de mayo de 2024, de https://www.unep.org/es/noticias-y-reportajes/reportajes/siete-formas-en-las-que-estamos-conectados-con-los-arrecifes-de ',
                'type' => 'online',
                'bibtex' => [
                    'url' => 'https://www.unep.org/es/noticias-y-reportajes/reportajes/siete-formas-en-las-que-estamos-conectados-con-los-arrecifes-de',
                    'month' => 'febrero',
                    'date' => '2020-02-28',
                    'author' => 'UNEP',
                    'year' => '2020',
                    'title' => 'Naciones Unidas Para el Medio Ambiente',
                    'urldate' => '17 de mayo de 2024',
                ],
                'language' => 'es',
            ],
			// no space after p for pages
			[
                'source' => 'Sulochana, S., Gayathri, Siddartha JR., & Fathima, J. (2022). Clinical Significance Of Erythrocyte Sedimen Rate In Tuberculosis. Research Journal of Pharmacy and Technology, 15(1), p245-249. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Sulochana, S. and Gayathri, Siddartha J. R. and Fathima, J.',
                    'year' => '2022',
                    'title' => 'Clinical Significance Of Erythrocyte Sedimen Rate In Tuberculosis',
                    'journal' => 'Research Journal of Pharmacy and Technology',
                    'volume' => '15',
                    'number' => '1',
                    'pages' => '245-249',
                    ]
            ],
			// no space after p
			[
                'source' => 'Silvia, H., Mellysa, R., & Citra, T. (2018). Perbandingan Nilai Laju Endap Darah (LED) Antara Metode Westergren dengan Metode Mikro ESR Pada Penderita Tuberkulosis Paru. Jurnal Medikes, 5(2), p182. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Silvia, H. and Mellysa, R. and Citra, T.',
                    'year' => '2018',
                    'title' => 'Perbandingan Nilai Laju Endap Darah (LED) Antara Metode Westergren dengan Metode Mikro ESR Pada Penderita Tuberkulosis Paru',
                    'journal' => 'Jurnal Medikes',
                    'volume' => '5',
                    'number' => '2',
                    'pages' => '182',
                    ]
            ],
			// several problems
			 [
                'source' => 'Tannenbaum, A. J. (1997). “The meaning and making of giftedness”. En N. Colangelo y G. Davis (Eds.). Handbook of gifted education. Boston: Allyn and Bacon. (2ed.). 27-42. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Tannenbaum, A. J.',
                    'year' => '1997',
                    'title' => 'The meaning and making of giftedness',
                    'pages' => '27-42',
                    'publisher' => 'Allyn and Bacon',
					'address' => 'Boston',
                    'booktitle' => 'Handbook of gifted education',
                    'editor' => 'N. Colangelo and G. Davis',
                    ],
					'language' => 'es',
            ],
			// doi format
			[
                'source' => 'Sharma, M., Kanekar, A., & Lakhan, R. (2018). Developing a scale for measuring perfection quotient (pq) to predict readiness to health behavior change. Journal of Medical Research and Innovation, 2(2), e000130. [https://doi.org/10.15419/jmri.130](https://doi.org/10.15419/jmri.130) ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.15419/jmri.130',
                    'author' => 'Sharma, M. and Kanekar, A. and Lakhan, R.',
                    'year' => '2018',
                    'title' => 'Developing a scale for measuring perfection quotient (pq) to predict readiness to health behavior change',
                    'journal' => 'Journal of Medical Research and Innovation',
                    'volume' => '2',
                    'number' => '2',
                    'pages' => 'e000130',
                    ]
            ],
			// correct inproceedings
			[
                'source' => 'H. Cho, J. -W. Kim, S. Kim, J. Lee, J. -I. Oh and J. -W. Yu, "Grating Lobe Suppression in Wide-Angle Scanning Phased Array with Large Inter Element Spacing by Subarray Rotation," 2022 IEEE International Symposium on Radio-Frequency Integration Technology (RFIT), Busan, Korea, Republic of, 2022, pp. 90-92, doi: 10.1109/RFIT54256.2022.9882462. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/RFIT54256.2022.9882462',
                    'author' => 'H. Cho and J.-W. Kim and S. Kim and J. Lee and J.-I. Oh and J.-W. Yu',
                    'title' => 'Grating Lobe Suppression in Wide-Angle Scanning Phased Array with Large Inter Element Spacing by Subarray Rotation',
                    'year' => '2022',
                    'pages' => '90-92',
                    'booktitle' => '2022 IEEE International Symposium on Radio-Frequency Integration Technology (RFIT), Busan, Korea, Republic of',
                    ]
            ],
			// correct inproceedings
			[
                'source' => 'M. S. Althaf, K. P. Ray, A. Divantgi, S. R. D. Prasad, K. A. Nethravathi and S. Elayaperumal, "Grating Lobe Suppression in Aperiodic Antenna for Large Phased Array using Genetic Algorithm," 2023 IEEE Microwaves, Antennas, and Propagation Conference (MAPCON), Ahmedabad, India, 2023, pp. 1-6, doi: 10.1109/MAPCON58678.2023.10464016. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/MAPCON58678.2023.10464016',
                    'author' => 'M. S. Althaf and K. P. Ray and A. Divantgi and S. R. D. Prasad and K. A. Nethravathi and S. Elayaperumal',
                    'title' => 'Grating Lobe Suppression in Aperiodic Antenna for Large Phased Array using Genetic Algorithm',
                    'year' => '2023',
                    'pages' => '1-6',
                    'booktitle' => '2023 IEEE Microwaves, Antennas, and Propagation Conference (MAPCON), Ahmedabad, India',
                    ]
            ],
			// correct inproceedings
			[
                'source' => 'L. Piattella, R. M. Rossi and I. Russo, "The vertigo array for grating lobe reduction," 2017 IEEE International Symposium on Antennas and Propagation & USNC/URSI National Radio Science Meeting, San Diego, CA, USA, 2017, pp. 1583-1584, doi: 10.1109/APUSNCURSINRSM.2017.8072834. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'doi' => '10.1109/APUSNCURSINRSM.2017.8072834',
                    'author' => 'L. Piattella and R. M. Rossi and I. Russo',
                    'title' => 'The vertigo array for grating lobe reduction',
                    'year' => '2017',
                    'pages' => '1583-1584',
                    'booktitle' => '2017 IEEE International Symposium on Antennas and Propagation & USNC/URSI National Radio Science Meeting, San Diego, CA, USA',
                    ]
            ],
   			// Not inproceedings
			[
                'source' => 'Bernanke, B. (1986). Alternative explanations of the money-income correlation. Carnegie-Rochester conference series on public policy, 25, 49-99. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bernanke, B.',
                    'title' => 'Alternative explanations of the money-income correlation',
                    'journal' => 'Carnegie-Rochester conference series on public policy',
                    'year' => '1986',
                    'volume' => '25',
                    'pages' => '49-99',
                    ]
            ],
			// edition version not picked up correctly
			[
                'source' => 'Cooper, John. 2000. Body, Soul and Life Everlasting. 2nd Revised Edition. Grand Rapids: Eerdmans. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Cooper, John',
                    'title' => 'Body, Soul and Life Everlasting',
                    'year' => '2000',
                    'edition' => '2nd Revised',
                    'address' => 'Grand Rapids',
                    'publisher' => 'Eerdmans',
                    ]
            ],
			// volumes should be part of title
			[
                'source' => 'Aquinas, Thomas. 1948. Summa Theologica, Vols. I-III. Translated by Fathers of the English Dominican Province. Allen, TX: Christian Classics. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Aquinas, Thomas',
                    'title' => 'Summa Theologica',
                    'volume' => 'I-III',
                    'year' => '1948',
                    'note' => 'Translated by Fathers of the English Dominican Province.',
                    'address' => 'Allen, TX',
                    'publisher' => 'Christian Classics',
                    ]
            ],
            // label at start in braces
            [
                'source' => '{Wyth1907} W. Wythoff. A modification of the game of Nim. \textit{Nieuw Arch. Wisk.}, 7:199-202, 1907.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'W. Wythoff',
                    'title' => 'A modification of the game of Nim',
                    'journal' => 'Nieuw Arch. Wisk.',
                    'year' => '1907',
                    'volume' => '7',
                    'pages' => '199-202',
                    ]
            ],
			// put '2 vols' in note
			[
                'source' => 'Lane, E. W. 2003. Arabic–English Lexicon. 2 vols. Cambridge: Islamic Texts Society. First published 1836. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Lane, E. W.',
                    'title' => 'Arabic--English Lexicon',
                    'year' => '2003',
                    'note' => '2 vols. First published 1836',
                    'address' => 'Cambridge',
                    'publisher' => 'Islamic Texts Society',
                    ]
            ],
			// last name included in title --- because it is in dictionary? 
            [
                'source' => 'M. C. Vigano, G. Toso, G. Caille, C. Mangenot and I. E. Lager, "Spatial density tapered sunflower antenna array," 2009 3rd European Conference on Antennas and Propagation, Berlin, Germany, 2009, pp. 778-782. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'M. C. Vigano and G. Toso and G. Caille and C. Mangenot and I. E. Lager',
                    'title' => 'Spatial density tapered sunflower antenna array',
					'booktitle' => '2009 3rd European Conference on Antennas and Propagation, Berlin, Germany',
                    'year' => '2009',
                    'pages' => '778-782',
                    ]
            ],
			// misclassified as article
			[
                'source' => 'Nidorf, D.G.; Barone, L.; French, T. A comparative study of NEAT and XCS in Robocode. In Proceedings of the IEEE Congress on Evolutionary Computation, New Orleans, LA, USA, 5–8 June 2010; pp. 1–8. [CrossRef] ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Nidorf, D. G. and Barone, L. and French, T.',
                    'title' => 'A comparative study of NEAT and XCS in Robocode',
                    'year' => '2010',
                    'booktitle' => 'Proceedings of the IEEE Congress on Evolutionary Computation, New Orleans, LA, USA, 5--8 June 2010',
                    'pages' => '1-8',
                    ]
            ],
			// misclassified as incollection
			 [
                'source' => 'Harper, R. Spatial co-evolution in age layered planes (SCALP). In Proceedings of the IEEE Congress on Evolutionary Computation, Barcelona, Spain, 18–23 July 2010; pp. 1–8. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Harper, R.',
                    'title' => 'Spatial co-evolution in age layered planes (SCALP)',
                    'year' => '2010',
                    'pages' => '1-8',
                    'booktitle' => 'Proceedings of the IEEE Congress on Evolutionary Computation, Barcelona, Spain, 18--23 July 2010',
                    ]
            ],
            // Can refine address: publisher match to cover this case?
			[
                'source' => 'Abdel-Kader, Ali Hassan. 1962. The Life Personality and Writings of al-Junayd: A Study of a Third/Ninth Century Mystic with an Edition and Translation of his Writings. London: E. J. W. Gibb. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Abdel-Kader, Ali Hassan',
                    'title' => 'The Life Personality and Writings of al-Junayd: A Study of a Third/Ninth Century Mystic with an Edition and Translation of his Writings',
                    'year' => '1962',
                    'address' => 'London',
                    'publisher' => 'E. J. W. Gibb',
                    ]
            ],
			// last author's name included in title
			[
                'source' => '\bibitem{RGO1990} Romeiras F. J., Grebogi C., and Ott E.: Multifractal properties of snapshot attractors of random maps, Phys. Rev. A {\bf 41}, 784 (1990) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Romeiras, F. J. and Grebogi, C. and Ott, E.',
                    'title' => 'Multifractal properties of snapshot attractors of random maps',
                    'year' => '1990',
                    'journal' => 'Phys. Rev. A',
                    'volume' => '41',
                    'pages' => '784',
                    ]
            ],
			// use of {\AA} at start of name
			[
                'source' => '\bibitem{farisDIS1996} F. Gel\'mukhanov and H. {\AA}gren, X-ray resonant scattering involving dissociative states, {\it Phys. Rev. A},  {\bf 54}, 379  (1996). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'F. Gel\'mukhanov and H. {\AA}gren',
                    'title' => 'X-ray resonant scattering involving dissociative states',
                    'year' => '1996',
                    'journal' => 'Phys. Rev. A',
                    'volume' => '54',
					'pages' => '379',
                    ]
            ],
			// volume marked by * *
			[
                'source' => 'Byrge, L., & Kennedy, D. P. (2019). High-accuracy individual identification using a "thin slice" of the functional connectome. *Network Neuroscience*, *3*(2), 363--383. https://doi.org/10.1162/netn_a_00068 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1162/netn\_a\_00068',
                    'author' => 'Byrge, L. and Kennedy, D. P.',
                    'year' => '2019',
                    'title' => 'High-accuracy individual identification using a ``thin slice\'\' of the functional connectome',
                    'journal' => 'Network Neuroscience',
                    'pages' => '363-383',
                    'volume' => '3',
					'number' => '2',
                    ]
            ],
            // volume marked by * *
            [
                'source' => 'Zuo, X.-N., & Xing, X.-X. (2014). Test-retest reliabilities of resting-state FMRI measurements in human brain functional connectomics: A systems neuroscience perspective. *Neuroscience & Biobehavioral Reviews*, *45*, 100--118. https://doi.org/10.1016/j.neubiorev.2014.05.009  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.neubiorev.2014.05.009',
                    'author' => 'Zuo, X.-N. and Xing, X.-X.',
                    'year' => '2014',
                    'title' => 'Test-retest reliabilities of resting-state FMRI measurements in human brain functional connectomics: A systems neuroscience perspective',
                    'journal' => 'Neuroscience & Biobehavioral Reviews',
                    'pages' => '100-118',
                    'volume' => '45',
                    ]
            ],
   			// city in address not picked up; classified as incollection not book
			[
                'source' => 'Aquinas, Thomas. 2012. Commentary on the Letters of Saint Paul to the Corinthians. Translated by F.R. Larcher OP, B. Mortensen, and D. Keating. Edited by J. Mortensen and E. Alarcon. Lander, WY: The Aquinas Institute for the Study of Sacred Doctrine. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Aquinas, Thomas',
                    'title' => 'Commentary on the Letters of Saint Paul to the Corinthians',
                    'year' => '2012',
                    'note' => 'Translated by F.R. Larcher OP, B. Mortensen, and D. Keating. Edited by J. Mortensen and E. Alarcon.',
                    'address' => 'Lander, WY',
                    'publisher' => 'The Aquinas Institute for the Study of Sacred Doctrine',
                    ]
            ],
			// Note omitted
			[
                'source' => 'Macdonald, Paul. 2019. History of the Concept of Mind. London: Routledge. First published in 2003. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Macdonald, Paul',
                    'title' => 'History of the Concept of Mind',
                    'year' => '2019',
                    'note' => 'First published in 2003',
                    'address' => 'London',
                    'publisher' => 'Routledge',
                    ]
            ],
			// note omitted
			[
                'source' => 'Ryle, Gilbert. 2002. The Concept of Mind. Chicago: University of Chicago Press (originally published 1949). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Ryle, Gilbert',
                    'title' => 'The Concept of Mind',
                    'year' => '2002',
                    'note' => 'originally published 1949',
                    'address' => 'Chicago',
                    'publisher' => 'University of Chicago Press',
                    ]
            ],
			// month not picked up
			[
                'source' => 'Swinburne, Richard. 2023. ‘Design for Living’, in Times Literary Supplement, December, 24–25. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Swinburne, Richard',
                    'title' => 'Design for Living',
                    'journal' => 'Times Literary Supplement',
                    'year' => '2023',
                    'month' => 'December',
                    'pages' => '24-25',
                    ]
            ],
			// don't add "in" to note field; 'et al.:' misinterpreted because of trailing :?
			[
                'source' => '\bibitem{curran} Curran S. et al.: Hallucination is the last thing you need. In: arXiv, 2023. \url{http://arxiv.org/abs/2306.11520} ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'arXiv',
                    'year' => '2023',
                    'url' => 'http://arxiv.org/abs/2306.11520',
                    'author' => 'Curran, S. and others',
                    'title' => 'Hallucination is the last thing you need',
                    ]
            ],
            // title ended early
            [
                'source' => 'Cásula, C. (2006). Jardineros, princesas y puerco espines. Construyendo metáforas. México D.F.: Alom. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Cásula, C.',
                    'title' => 'Jardineros, princesas y puerco espines. Construyendo metáforas',
                    'year' => '2006',
                    'address' => 'México D. F.',
                    'publisher' => 'Alom',
                ],
                'language' => 'es',
                'char_encoding' => 'utf8leave',
            ],
			// address: publisher error
			[
                'source' => 'Eckhart, M. (2007). El hombre noble. Guanajuato, Mexico: Fundación de Estudios Tradicionales. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Eckhart, M.',
                    'title' => 'El hombre noble',
                    'year' => '2007',
                    'address' => 'Guanajuato, Mexico',
                    'publisher' => 'Fundación de Estudios Tradicionales',
                    ],
					'language' => 'es',
					'char_encoding' => 'utf8leave',
            ],
			// address: publisher error
			[
                'source' => 'Erickson, B. A., & Keeney, B. (2010). Milton H. Erickson un sanador americano. México D.F.: Alom. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Erickson, B. A. and Keeney, B.',
                    'title' => 'Milton H. Erickson un sanador americano',
                    'year' => '2010',
                    'address' => 'México D. F.',
                    'publisher' => 'Alom',
                    ],
					'language' => 'es',
					'char_encoding' => 'utf8leave',
            ],
			// title ended early
			[
                'source' => 'Hellinger, B. (2010). Plenitud. La mirada del Nahual. México: Grupo CUDEC. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Hellinger, B.',
                    'title' => 'Plenitud. La mirada del Nahual',
                    'year' => '2010',
                    'address' => 'México',
                    'publisher' => 'Grupo CUDEC',
                    ],
					'language' => 'es',
					'char_encoding' => 'utf8leave',
            ],
			// title ended early
			[
                'source' => 'Zeig, J. (1985). Un Seminario Didáctico con Milton H. Erickson. Buenos Aires: Amorrortu. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Zeig, J.',
                    'title' => 'Un Seminario Didáctico con Milton H. Erickson',
                    'year' => '1985',
                    'address' => 'Buenos Aires',
                    'publisher' => 'Amorrortu',
                    ],
					'language' => 'es',
					'char_encoding' => 'utf8leave',
            ],
			// braces around last name of first author mess things up
            // Rendering of {J}.-{P}. is not correct, but is seems such an edge case that it's not worth fixing
			[
                'source' => '\bibitem{alouani2004} F.~{Alouani-Bibi}, M.~M. Shoucri, and J.-P. Matte. \newblock Different {F}okker--{P}lanck approaches to simulate electron   transport in plasmas. \newblock {\em Comput. Phys. Commun.}, 164:60--66, 2004. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'F. {Alouani-Bibi} and M. M. Shoucri and J.-P. Matte', 
					'title' => 'Different {F}okker--{P}lanck approaches to simulate electron transport in plasmas',
                    'year' => '2004',
                    'journal' => 'Comput. Phys. Commun.',
                    'pages' => '60-66',
                    'volume' => '164',
                    ]
            ],
  			// doctoral thesis --- Turkish
			[
                'source' => 'Zeybek, G. (2011). Bilgisayar meslek dersi alan ortaöğretim öğrencilerinin bilişim teknolojilerini kullanımlarının etik açıdan değerlendirilmesi. Doktora Tezi, Selçuk Üniversitesi Eğitim Bilimleri Enstitüsü, Konya. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Zeybek, G.',
                    'year' => '2011',
                    'title' => 'Bilgisayar meslek dersi alan ortaöğretim öğrencilerinin bilişim teknolojilerini kullanımlarının etik açıdan değerlendirilmesi',
					'school' => 'Selçuk Üniversitesi Eğitim Bilimleri Enstitüsü, Konya',
                ],
                'char_encoding' => 'utf8leave'
            ],
			// 
			[
                'source' => 'Boring, S., M. Jurmu, and A. Butz. Scroll, tilt or move it: using mobile phones to continuously control pointers on large public displays. in Proceedings of the 21st Annual Conference of the Australian Computer-Human Interaction Special Interest Group: Design: Open 24/7. 2009. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Boring, S. and M. Jurmu and A. Butz',
                    'title' => 'Scroll, tilt or move it: using mobile phones to continuously control pointers on large public displays',
                    'year' => '2009',
                    'booktitle' => 'Proceedings of the 21st Annual Conference of the Australian Computer-Human Interaction Special Interest Group: Design: Open 24/7',
                ],
                'language' => 'es',
                'char_encoding' => 'utf8leave',
            ],
            // page range separator not translated to -
            [
                'source' => '(37)	Sweet, T. J.; Licatalosi, D. D. 3? END Formation and Regulation of Eukaryotic MRNAs. Methods in Molecular Biology 2014, 1125, 3Ð12. https://doi.org/10.1007/978-1-62703-971-0_1/COVER. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/978-1-62703-971-0\_1/COVER',
                    'author' => 'Sweet, T. J. and Licatalosi, D. D.',
                    'title' => '3? END Formation and Regulation of Eukaryotic MRNAs',
                    'year' => '2014',
                    'journal' => 'Methods in Molecular Biology',
                    'volume' => '1125',
                    'pages' => '3-12',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// label at start contains :
			[
                'source' => '[AnBr1:00] K.M. ANSTREICHER and N.W. BRIXIUS, A new bound for the quadratic assignment problem based on convex quadratic programming, Mathematical Programming 80:341-357, 2001. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'K. M. Anstreicher and N. W. Brixius',
                    'title' => 'A new bound for the quadratic assignment problem based on convex quadratic programming',
                    'journal' => 'Mathematical Programming',
                    'year' => '2001',
                    'volume' => '80',
                    'pages' => '341-357',
                    ]
            ],
			// translator
            [
                'source' => 'Urbach, Ephraim E. 1979. The Sages: Their Concepts and Beliefs, trans. Israel Abrahams. Jerusalem: Magnes. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Urbach, Ephraim E.',
                    'title' => 'The Sages: Their Concepts and Beliefs',
                    'year' => '1979',
                    'note' => 'Translated by Israel Abrahams.',
                    'address' => 'Jerusalem',
                    'publisher' => 'Magnes',
                    ]
            ],
			// masters thesis
			[
                'source' => 'Bıçakcı, G. (2022). Spor bilimleri fakülteleri öğrencileri ve akademik personelin uzaktan eğitim hakkındaki görüşlerinin incelenmesi. Yüksek Lisans Tezi. Yozgat: Yozgat Bozok Üniversitesi Lisansüstü Eğitim Enstitüsü. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Bıçakcı, G.',
                    'year' => '2022',
                    'title' => 'Spor bilimleri fakülteleri öğrencileri ve akademik personelin uzaktan eğitim hakkındaki görüşlerinin incelenmesi',
                    'school' => 'Yozgat Bozok Üniversitesi Lisansüstü Eğitim Enstitüsü',
                    'note' => 'Yozgat',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// phdthesis
			[
                'source' => 'Engin, M. (2013). Üniversitelerde Teknoloji Yoğun Uzaktan Eğitim Sistemlerinin Üretim, Uygulama ve Yönetim Süreçlerinin İncelenmesi. Doktora Tezi. Ankara: Ankara Üniversitesi Eğitim Bilimleri Enstitüsü. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Engin, M.',
                    'year' => '2013',
                    'title' => 'Üniversitelerde Teknoloji Yoğun Uzaktan Eğitim Sistemlerinin Üretim, Uygulama ve Yönetim Süreçlerinin İncelenmesi',
                    'school' => 'Ankara Üniversitesi Eğitim Bilimleri Enstitüsü',
                    'note' => 'Ankara',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// phdthesis correctly detected
			[
                'source' => '\bibitem{west} J. West, Permutations with forbidden subsequences and  stack-sortable permutations, PhD thesis, Massachusetts Institute of  Technology, 1990. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'J. West',
                    'title' => 'Permutations with forbidden subsequences and stack-sortable permutations',
                    'year' => '1990',
                    'school' => 'Massachusetts Institute of Technology',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// phdthesis (Turkish)
			[
                'source' => 'Çelik, K. (2018). Genişletilmiş Teknoloji Kabul Modeli: Uzaktan Eğitim Öğrencileri Üzerine Bir Araştırma. Yönetim Bilişim Sistemleri Anabilim Dalı, Gazi Üniversitesi Bilişim Enstitüsü,(Yayımlanmamış doktora tezi), Ankara. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Çelik, K.',
                    'year' => '2018',
                    'title' => 'Genişletilmiş Teknoloji Kabul Modeli: Uzaktan Eğitim Öğrencileri Üzerine Bir Araştırma',
                    'school' => 'Yönetim Bilişim Sistemleri Anabilim Dalı, Gazi Üniversitesi Bilişim Enstitüsü, Ankara',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// unpublished master's thesis
			[
                'source' => 'Ceylan, H. (2019). Fen bilgisi öğretmenlerinin eğitim-öğretimde, Eğitim Bilişim Ağından (EBA ) yararlanmaya ilişkin görüşleri. Yayınlanmamış Yüksek Lisans Tezi, Trakya Üniversitesi, Edirne. ',
                'type' => 'mastersthesis',
                'bibtex' => [
                    'author' => 'Ceylan, H.',
                    'year' => '2019',
                    'title' => 'Fen bilgisi öğretmenlerinin eğitim-öğretimde, Eğitim Bilişim Ağından (EBA) yararlanmaya ilişkin görüşleri',
                    'school' => 'Trakya Üniversitesi, Edirne',
                    ],
                    'char_encoding' => 'utf8leave',
            ],
			// urldate not detected
			[
                'source' => '[10] Solectroshop. (s.f.). Diferencias de los protocolos de comunicación UART vs I2C vs SPI. Solectroshop. Recuperado el 16 de junio de 2024, de https://solectroshop.com/es/blog/diferencias-de-los-protocolos-de-comunicacion-uart-vs-i2c-vs-spi-n107 ',
                'type' => 'online',
                'bibtex' => [
                    'urldate' => '16 de junio de 2024',
                    'url' => 'https://solectroshop.com/es/blog/diferencias-de-los-protocolos-de-comunicacion-uart-vs-i2c-vs-spi-n107',
                    'author' => 'Solectroshop',
                    'year' => 's.f.',
                    'title' => 'Diferencias de los protocolos de comunicación UART vs I2C vs SPI',
                    'note' => 'Solectroshop',
                    ],
					'language' => 'es',
                    'char_encoding' => 'utf8leave',
            ],
            //
            [
                'source' => 'Reuters. (2022). Mexico nationalizes lithium, plans review of contracts. Retrieved May 5, 2024,  from https://www.reuters.com/article/idUSKCN2MB12Y/. ',
                'type' => 'online',
                'bibtex' => [
                    'urldate' => 'May 5, 2024',
                    'url' => 'https://www.reuters.com/article/idUSKCN2MB12Y/',
                    'author' => 'Reuters',
                    'year' => '2022',
                    'title' => 'Mexico nationalizes lithium, plans review of contracts',
                    ]
            ],
            //
            [
                'source' => '---. (2023a). Albemarle agrees $1.5 billion plan to double Australian lithium hydroxide output. Retrieved May 4, 2024,  from https://www.reuters.com/markets/commodities/albermarle-agrees-1-bln-plan-double-australian-lithium-hydroxide-output-state-2023-05-03/. ',
                'type' => 'online',
                'bibtex' => [
                    'urldate' => 'May 4, 2024',
                    'url' => 'https://www.reuters.com/markets/commodities/albermarle-agrees-1-bln-plan-double-australian-lithium-hydroxide-output-state-2023-05-03/',
                    'author' => 'Reuters',
                    'year' => '2023',
                    'title' => 'Albemarle agrees $1.5 billion plan to double Australian lithium hydroxide output',
                    ]
            ],
			// remove right paren before publisher
			[
                'source' => 'Grawitz, Madeleine, Méthodes des sciences sociales. (Paris) Dalloz, 1964. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Grawitz, Madeleine',
                    'title' => 'Méthodes des sciences sociales',
                    'year' => '1964',
                    'publisher' => 'Dalloz',
                    'address' => 'Paris',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// not detected as book
			[
                'source' => 'Taft, Robert F. 1985. The Liturgy of the Hours in East and West (Collegeville, MN: The Liturgical Press). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Taft, Robert F.',
                    'year' => '1985',
                    'title' => 'The Liturgy of the Hours in East and West',
					'address' => 'Collegeville, MN',
					'publisher' => 'The Liturgical Press',
                    ]
            ],
			// not detected as book
			[
                'source' => 'Balla, Emil. 1912. Das ich der Psalmen untersucht (Göttingen: VandenHoeck & Ruprecht). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Balla, Emil',
                    'year' => '1912',
                    'title' => 'Das ich der Psalmen untersucht',
					'address' => 'Göttingen',
					'publisher' => 'VandenHoeck & Ruprecht',
                    ],
					'char_encoding' => 'utf8leave',
            ],
   			// volume as roman number
			[
                'source' => 'Shanley, Brian J. 1997a. ‘Eternal Knowledge of the Temporal in Aquinas’, American Catholic Philosophical Quarterly LXXI: 197–224. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Shanley, Brian J.',
                    'title' => 'Eternal Knowledge of the Temporal in Aquinas',
                    'journal' => 'American Catholic Philosophical Quarterly',
                    'year' => '1997',
                    'volume' => 'LXXI',
                    'pages' => '197-224',
                    ]
            ],
			// London is included in title and publisher and address reversed
			[
                'source' => 'T. W. Chaundy, P. R. Barrett and C. Batey, The Printing of Mathematics. London, U.K., Oxford Univ. Press, 1954. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'T. W. Chaundy and P. R. Barrett and C. Batey',
                    'title' => 'The Printing of Mathematics',
                    'year' => '1954',
                    'address' => 'London, U. K.',
                    'publisher' => 'Oxford Univ. Press',
                    ]
            ],
            // no space before & in author list
            [
                'source' => 'Călinoiu, L. F., &Vodnar, D. C. (2018). Whole Grains and Phenolic Acids: A Review on Bioactivity, Functionality, Health Benefits and Bioavailability. Nutrients, 10(11), 1615. https://doi.org/10.3390/nu10111615 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.3390/nu10111615',
                    'author' => 'Călinoiu, L. F. and Vodnar, D. C.',
                    'year' => '2018',
                    'title' => 'Whole Grains and Phenolic Acids: A Review on Bioactivity, Functionality, Health Benefits and Bioavailability',
                    'journal' => 'Nutrients',
                    'volume' => '10',
                    'number' => '11',
                    'pages' => '1615',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// don't stop editor's name at period
			[
                'source' => 'Bracken Long, Kimberly. 2014. ‘The Psalms in Christian Worship,’ in: The Oxford Handbook of the Psalms ed. by William P. Brown (Oxford: Oxford University Press), 545–556. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Bracken Long, Kimberly',
                    'year' => '2014',
                    'title' => 'The Psalms in Christian Worship',
                    'pages' => '545-556',
                    'booktitle' => 'The Oxford Handbook of the Psalms',
                    'editor' => 'William P. Brown',
                    'publisher' => 'Oxford University Press',
                    'address' => 'Oxford',
                    ]
            ],
			// don't stop at period after 'St'
			 [
                'source' => 'Anderson, James F. 1952. The Cause of Being: The Philosophy of Creation in St. Thomas. St. Louis: Herder.  ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Anderson, James F.',
                    'title' => 'The Cause of Being: The Philosophy of Creation in St. Thomas',
                    'year' => '1952',
                    'address' => 'St. Louis',
                    'publisher' => 'Herder',
                    ]
            ],
			// don't stop at period after 'St'
			[
                'source' => 'Weinandy, Thomas G. 1985. Does God Change?: The Word’s Becoming in the Incarnation. Still River, MA: St. Bede’s Publications. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Weinandy, Thomas G.',
                    'title' => 'Does God Change?: The Word\'s Becoming in the Incarnation',
                    'year' => '1985',
                    'address' => 'Still River, MA',
                    'publisher' => 'St. Bede\'s Publications',
                    ]
            ],
			// editor not detected
			[
                'source' => 'Melamed, Yitzhak Y. 2016a. ‘Introduction’, in Eternity: A History. Edited by Yitzhak J. Melamed. Oxford: Oxford University Press, 1–13. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Melamed, Yitzhak Y.',
                    'title' => 'Introduction',
                    'year' => '2016',
                    'pages' => '1-13',
                    'editor' => 'Yitzhak J. Melamed',
                    'address' => 'Oxford',
                    'publisher' => 'Oxford University Press',
                    'booktitle' => 'Eternity: A History',
                    ]
            ],
			// detected as article
			 [
                'source' => 'Thomas Aquinas. 1955. Summa contra Gentiles. Translated by Anton C. Pegis et al. 5 vols. Garden City: Doubleday & Company. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Thomas Aquinas',
                    'title' => 'Summa contra Gentiles',
                    'year' => '1955',
                    'note' => 'Translated by Anton C. Pegis et al. 5 vols.',
                    'address' => 'Garden City',
                    'publisher' => 'Doubleday & Company',
                    ]
            ],
			// 'edited by' repeated
			[
                'source' => 'Thomas Aquinas. 2012. Summa Theologiae. Translated by Lawrence Shapcote. Edited by John Mortensen and Enrique Alarcón. Lander, WY: The Aquinas Institute for the Study of Sacred Doctrine. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Thomas Aquinas',
                    'title' => 'Summa Theologiae',
                    'year' => '2012',
                    'note' => 'Translated by Lawrence Shapcote. Edited by John Mortensen and Enrique Alarc{\\\'o}n.',
                    'address' => 'Lander, WY',
                    'publisher' => 'The Aquinas Institute for the Study of Sacred Doctrine',
                    ]
            ],
			// phdthesis detected as article
			[
                'source' => 'Donald Atchison Spong, ?Equilibrium, confinement, and stability of runaway electrons in tokamaks" PhD Thesis, University of Michigan,  OAK RIDGE NATIONAL LABORATORY Oak Ridge, Tennessee (1976) ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Donald Atchison Spong',
                    'title' => 'Equilibrium, confinement, and stability of runaway electrons in tokamaks',
					'school' => 'University of Michigan, OAK RIDGE NATIONAL LABORATORY Oak Ridge, Tennessee',
                    'year' => '1976',
                    ]
            ],
			// phdthesis detected as article
			[
                'source' => 'Entrop I, ?Confinement of relativistic electrons in tokamak plasmas" Ph.D. Thesis, Eindhoven University of Technology, the Netherlands (ISBN 90-386-0474-2) 1999 ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Entrop, I.',
                    'title' => 'Confinement of relativistic electrons in tokamak plasmas',
                    'year' => '1999',
                    'school' => 'Eindhoven University of Technology, the Netherlands',
					'isbn' => '90-386-0474-2',
                    ]
            ],
			// phdthesis detected correctly
			[
                'source' => 'Alexander Nevil Tronchin-James, ?Investigations of runaway electron generation, transport, and stability in the DIII-D tokamak? PhD Thesis, University of California, San Diego (2011) ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Alexander Nevil Tronchin-James',
                    'title' => 'Investigations of runaway electron generation, transport, and stability in the DIII-D tokamak',
                    'year' => '2011',
                    'school' => 'University of California, San Diego',
                    ]
            ],
			// detected as book because of city?  PhD thesis should trump that.
			[
                'source' => 'Liu Chang, ?Runaway electrons in tokamak? PhD Thesis, Princeton, NJ. Princeton University (2017) ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Liu Chang',
                    'title' => 'Runaway electrons in tokamak',
					'school' => 'Princeton, NJ. Princeton University',
                    'year' => '2017',
                    ]
            ],
            // No space after &
            [
                'source' => 'Malinowska,E.,Jankowski,K.,Wisniewska-Kadzajan,B.,Sosnowski,J.,Kolczarek,R., Jankowska, J., &Ciepiela,G. (2015). Contentof Zincand Copper in Selected Plants Growing Along a Motorway. Bulletin of Environmental Contamination and Toxicology, 95, 638–643. https://doi.org/10.1007/s00128-015-1648-8. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1007/s00128-015-1648-8',
                    'author' => 'Malinowska, E. and Jankowski, K. and Wisniewska-Kadzajan, B. and Sosnowski, J. and Kolczarek, R. and Jankowska, J. and Ciepiela, G.',
                    'title' => 'Contentof Zincand Copper in Selected Plants Growing Along a Motorway',
                    'year' => '2015',
                    'journal' => 'Bulletin of Environmental Contamination and Toxicology',
                    'volume' => '95',
                    'pages' => '638-643',
                    ]
            ],
            // organization name truncated
            [
                'source' => 'American Psychiatric Association. Diagnostic and statistical manual of mental disorders. 5th ed. Arlington, VA: Am Psychiatric Publishing; 2013. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'American Psychiatric Association',
                    'edition' => '5th',
                    'title' => 'Diagnostic and statistical manual of mental disorders',
                    'year' => '2013',
                    'publisher' => 'Am Psychiatric Publishing',
                    'address' => 'Arlington, VA',
                    ]
            ],
			// "visited" before urldate
			[
                'source' => 'De Souza, Raymond J. 2007. ‘Benedict and Jesus: Teach Us How to Pray,’ National Catholic Register 4 September; https://www.ncregister.com/news/benedict-and-jesus, visited 1 March 2022. ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'De Souza, Raymond J.',
                    'title' => 'Benedict and Jesus: Teach Us How to Pray',
                    'year' => '2007',
                    'note' => 'National Catholic Register 4 September',
                    'url' => 'https://www.ncregister.com/news/benedict-and-jesus',
                    'urldate' => '1 March 2022',
                    ]
            ],
			// address-publisher not detected (because of dash?)
			[
                'source' => 'Helm, Paul. 1993. The Providence of God (Leicester: Inter-Varsity Press). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Helm, Paul',
                    'title' => 'The Providence of God',
                    'year' => '1993',
                    'address' => 'Leicester',
                    'publisher' => 'Inter-Varsity Press',
                    ]
            ],
			// 'translated by' not picked up
			[
                'source' => 'Augustine. 2002. Expositions of the Psalms translated by Maria Boulding (Hyde Park, NY: New City Press). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Augustine',
                    'title' => 'Expositions of the Psalms',
                    'year' => '2002',
                    'note' => 'Translated by Maria Boulding',
                    'address' => 'Hyde Park, NY',
                    'publisher' => 'New City Press',
                    ]
            ],
			// translated by not picked up
			[
                'source' => 'Calvin, John. 1536. Institutes of the Christian Religion: 1536 edition (translated by Ford Lewis Battles) (Grand Rapids: Eerdmans, 1986). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Calvin, John',
                    'title' => 'Institutes of the Christian Religion',
                    'year' => '1536',
                    'note' => 'Translated by Ford Lewis Battles',
                    'edition' => '1536',
                    'address' => 'Grand Rapids',
                    'publisher' => 'Eerdmans',
                    ]
            ],
			// year follows publisher, which means address: publisher not detected
			[
                'source' => 'Brümmer, Vincent. 2008. What Are We Doing When We Pray? On Prayer and the Nature of Faith (Aldershot: Ashgate, 2008). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Brümmer, Vincent',
                    'title' => 'What Are We Doing When We Pray? On Prayer and the Nature of Faith',
                    'year' => '2008',
                    'address' => 'Aldershot',
                    'publisher' => 'Ashgate',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// same
			[
                'source' => 'Coakley, Sarah. 2013b. ‘Beyond “Belief”: Liturgy and the Cognitive Apprehension of God,’ in: The Vocation of Theology Today ed. by Tom Greggs, Rachel Muers & Simeon Zahl (Eugene, OR: Wipf and Stock, 2013), 131–145. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Coakley, Sarah',
                    'title' => 'Beyond ``Belief\'\': Liturgy and the Cognitive Apprehension of God',
                    'year' => '2013',
                    'pages' => '131-145',
                    'editor' => 'Tom Greggs and Rachel Muers and Simeon Zahl',
                    'address' => 'Eugene, OR',
                    'publisher' => 'Wipf and Stock',
                    'booktitle' => 'The Vocation of Theology Today',
                    ]
            ],
			// same?
			[
                'source' => 'Desmond, William. 2016. ‘The Porosity of Being: Towards a Catholic Agapeics,’ in: Renewing the Church in a Secular Age: Holistic Dialogue and Kenotic Vision ed. by João J. Vila-Chã, George F. McLean, Charles Taylor, José Casanova (Washington, D.C.: Council for Research in Values and Philosophy), 283–305. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Desmond, William',
                    'title' => 'The Porosity of Being: Towards a Catholic Agapeics',
                    'year' => '2016',
                    'pages' => '283-305',
                    'editor' => 'João J. Vila-Chã and George F. McLean and Charles Taylor and José Casanova',
                    'address' => 'Washington, D. C.',
                    'publisher' => 'Council for Research in Values and Philosophy',
                    'booktitle' => 'Renewing the Church in a Secular Age: Holistic Dialogue and Kenotic Vision',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// problem with address
			[
                'source' => 'Dugan, Katherine. 2019. ‘iPrayer: Catholic Prayer Apps and Twenty-First-Century Catholic Subjectivities,’ in: Anthropological Perspectives on the Religious Uses of Mobile Apps ed. by Jacqueline H. Fewkes (Cham, Switzerland: Palgrave Macmillan), 131–151. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Dugan, Katherine',
                    'title' => 'iPrayer: Catholic Prayer Apps and Twenty-First-Century Catholic Subjectivities',
                    'year' => '2019',
                    'pages' => '131-151',
                    'editor' => 'Jacqueline H. Fewkes',
                    'address' => 'Cham, Switzerland',
                    'publisher' => 'Palgrave Macmillan',
                    'booktitle' => 'Anthropological Perspectives on the Religious Uses of Mobile Apps',
                    ]
            ],
			// similar?
			[
                'source' => 'Van den Brink, Gijsbert. 1992. ‘Natural Evil and Eschatology,’ in: Christian Faith and Philosophical Theology: Essays in Honour of Vincent Brümmer ed. by Gijsbert van den Brink, Luco van den Brom & Marcel Sarot (Kampen: Kok Pharos), 39–55. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Van den Brink, Gijsbert',
                    'title' => 'Natural Evil and Eschatology',
                    'year' => '1992',
                    'pages' => '39-55',
                    'editor' => 'Gijsbert van den Brink and Luco van den Brom and Marcel Sarot',
                    'address' => 'Kampen',
                    'publisher' => 'Kok Pharos',
                    'booktitle' => 'Christian Faith and Philosophical Theology: Essays in Honour of Vincent Brümmer',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// address, publisher messed up because 'Sheffield' is in db as a city?
			[
                'source' => 'Barker, Margaret. 2003. ‘The Temple Roots of the Christian Liturgy,’ in: Christian Origins: Worship, Belief and Society ed. by Kieran J. Mahoney (London: Sheffield Academic Press), 29–51. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Barker, Margaret',
                    'title' => 'The Temple Roots of the Christian Liturgy',
                    'year' => '2003',
                    'pages' => '29-51',
                    'editor' => 'Kieran J. Mahoney',
                    'address' => 'London',
                    'publisher' => 'Sheffield Academic Press',
                    'booktitle' => 'Christian Origins: Worship, Belief and Society',
                    ]
            ],
			// deal with month range
			[
                'source' => 'Maharjan SK. Propranolol is effective in decreasing stress response due to airway manipulation and CO2 pneumoperitoneum in patients undergoing laparoscopic cholecystectomy. Kathmandu University Medical Journal. 2005 Apr-Jun; 3(2): 102-6  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Maharjan, S. K.',
                    'title' => 'Propranolol is effective in decreasing stress response due to airway manipulation and CO2 pneumoperitoneum in patients undergoing laparoscopic cholecystectomy',
                    'year' => '2005',
                    'month' => 'April-June',
                    'journal' => 'Kathmandu University Medical Journal.',
                    'volume' => '3',
                    'number' => '2',
                    'pages' => '102-6',
                    ]
            ],
			//use of 'supp' in issue number
			 [
                'source' => 'Kamran Montazeri, Parviz Kashefi, Azim Honarmand, Mohammadreza Safavi, Anahita Hirmanpour. Attenuation of the pressor response to direct laryngoscopy and tracheal intubation: Oral Clonidine vs. Oral Gabapentin premedication. J Res Med Sci. Mar 2011;16(supp 11):S377-S386.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kamran Montazeri and Parviz Kashefi and Azim Honarmand and Mohammadreza Safavi and Anahita Hirmanpour',
                    'title' => 'Attenuation of the pressor response to direct laryngoscopy and tracheal intubation: Oral Clonidine vs. Oral Gabapentin premedication',
					'journal' => 'J Res Med Sci.',
                    'year' => '2011',
                    'month' => 'March',
					'number' => 'supp 11',
                    'pages' => 'S377-S386',
                    'volume' => '16',
                    ]
            ],
			// publisher misidentified because "Academic Press" is in db as publisher?
			[
                'source' => 'Day, John. 1990. Psalms (Sheffield: Sheffield Academic Press). ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Day, John',
                    'title' => 'Psalms',
                    'year' => '1990',
                    'address' => 'Sheffield',
                    'publisher' => 'Sheffield Academic Press',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Akima H.: A method of bivariate interpolation and smooth surface fitting for irregularly distributed data points. ACM Transactions on Mathematical Software (TOMS), 4(2), pp. 148-159.(1978). ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Akima, H.',
                    'title' => 'A method of bivariate interpolation and smooth surface fitting for irregularly distributed data points',
                    'journal' => 'ACM Transactions on Mathematical Software (TOMS)',
                    'year' => '1978',
                    'volume' => '4',
                    'number' => '2',
                    'pages' => '148-159',
                    ]
            ],
			// initial "N" included in title
			 [
                'source' => 'Mitchell D. P. and Netravali A. N.: Reconstruction filters in computer-graphics. ACM Siggraph Computer Graphics, 22(4), pp. 221-228. (1988).  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Mitchell, D. P. and Netravali, A. N.',
                    'title' => 'Reconstruction filters in computer-graphics',
                    'journal' => 'ACM Siggraph Computer Graphics',
                    'year' => '1988',
                    'volume' => '22',
                    'number' => '4',
                    'pages' => '221-228',
                    ]
            ],
			// last author's name repeated in title
			[
                'source' => 'AGGARWAL, Medhavi, KAUR, Ranjit, et KAUR, Beant. A review of denoising filters in image restoration. International Journal of Current Research And Academic Review, ISSN, 2014, p. 2347-3215. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Aggarwal, Medhavi and Kaur, Ranjit and Kaur, Beant',
                    'title' => 'A review of denoising filters in image restoration',
                    'journal' => 'International Journal of Current Research And Academic Review, ISSN',
                    'year' => '2014',
                    'pages' => '2347-3215',
                    ]
            ],
			// lowercase 'a' in 'aug' causes problems? (or presence of month?)
			[
                'source' => 'Memis D, Turan A, Kuramanliogu B ,Sekar S, Ture M. Gabapentin reduces cardiovascular responses to laryngoscopy and tracheal intubation. Eur J A anesthesiology, 2006 aug; 23(8):p686-90.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Memis, D. and Turan, A. and Kuramanliogu, B. and Sekar, S. and Ture, M.',
                    'title' => 'Gabapentin reduces cardiovascular responses to laryngoscopy and tracheal intubation',
					'journal' => 'Eur J A anesthesiology',
					'year' => '2006',
					'month' => 'August',
					'volume' => '23',
					'number' => '8',
					'pages' => '686-90',
                    ]
            ],
   			// nested quotes
			[
                'source' => 'Kaunfer, Alvan H. 1995. ‘Who Knows Four? The ‘Imahot’ in Rabbinic Judaism’, Judaism 44: 94–103. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Kaunfer, Alvan H.',
                    'title' => 'Who Knows Four? The `Imahot\' in Rabbinic Judaism',
                    'journal' => 'Judaism',
                    'year' => '1995',
                    'volume' => '44',
                    'pages' => '94-103',
                    ]
            ],
			// closing quotation mark trimmed
			[
                'source' => '[30] Stipanowich, Thomas J. 2010. “Arbitration: The ‘New Litigation.’” University of Illinois Law Review 2010 (1): 1–58. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Stipanowich, Thomas J.',
                    'year' => '2010',
                    'title' => 'Arbitration: The `New Litigation.\'',
                    'journal' => 'University of Illinois Law Review',
                    'volume' => '2010',
                    'number' => '1',
                    'pages' => '1-58',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Wang, Y., Sun, Y., Liu, Z., Sarma, S. E., Bronstein, M. M., & Solomon, J. M. (2019). Dynamic graph cnn for learning on point clouds. ACM Transactions on Graphics (tog), 38(5), 1-12. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Wang, Y. and Sun, Y. and Liu, Z. and Sarma, S. E. and Bronstein, M. M. and Solomon, J. M.',
                    'year' => '2019',
                    'title' => 'Dynamic graph cnn for learning on point clouds',
					'journal' => 'ACM Transactions on Graphics (tog)',
                    'volume' => '38',
					'number' => '5',
                    'pages' => '1-12',
                    ]
            ],
            // doi not detected
			[
                'source' => 'Reed DM, Toris CB, Gilbert J, et al. Eye Dynamics and Engineering Network Consortium: Baseline Characteristics of a Randomized Trial in Healthy Adults. Ophthalmol Glaucoma. Sep 10 2022;doi:10.1016/j.ogla.2022.09.001 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Reed, D. M. and Toris, C. B. and Gilbert, J. and others',
                    'title' => 'Eye Dynamics and Engineering Network Consortium: Baseline Characteristics of a Randomized Trial in Healthy Adults',
					'journal' => 'Ophthalmol Glaucoma',
                    'year' => '2022',
                    'month' => 'September',
                    'date' => '2022-09-10',
                    'doi' => '10.1016/j.ogla.2022.09.001',
                    ]
            ],
			// various issues
			[
                'source' => 'Badie, Bertrand et Richard Dubrell, Analyse systémique d’une crise: l’exemple du front populaire, Revue française de sciences politique, 1974, Vol 24, n°1, page : 80-119. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Badie, Bertrand and Richard Dubrell',
                    'volume' => '24',
                    'title' => 'Analyse systémique d\'une crise: l\'exemple du front populaire',
					'journal' => 'Revue française de sciences politique',
					'year' => '1974',
                    'number' => '1',
                    'pages' => '80-119',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// article detected as book
			[
                'source' => 'Zadeh, Taghi. Les Courants politiques dans la Turquie contemporaine, Revue du Monde Musulman, XXI, 1912. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Zadeh, Taghi',
                    'title' => 'Les Courants politiques dans la Turquie contemporaine',
                    'year' => '1912',
                    'journal' => 'Revue du Monde Musulman',
                    'volume' => 'XXI',
                    ],
					'char_encoding' => 'utf8leave',
            ],
			// detect and remove ISSN to note? (otherwise can get confused with pages)
			[
                'source' => 'G. Gualtieri, Analysing the uncertainties of reanalysis data used for wind resource assessment: A critical review, Renewable and Sustainable Energy Reviews, Volume 167, 2022, 112741, ISSN 1364-0321, https://doi.org/10.1016/j.rser.2022.112741.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'G. Gualtieri',
                    'title' => 'Analysing the uncertainties of reanalysis data used for wind resource assessment: A critical review',
                    'journal' => 'Renewable and Sustainable Energy Reviews',
                    'year' => '2022',
                    'volume' => '167',
                    'pages' => '112741',
                    'note' => 'ISSN 1364-0321',
                    'doi' => '10.1016/j.rser.2022.112741',
                    ]
            ],
            // ISSN
            [
                'source' => 'Saraim, U., Voruganti, S. (2022). Heart disease prediction using data mining. International Journal of Creative research Thoughts. 10(5) (pp. 886-895). ISSN 2320-2882. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Saraim, U. and Voruganti, S.',
                    'year' => '2022',
                    'title' => 'Heart disease prediction using data mining',
                    'journal' => 'International Journal of Creative research Thoughts.',
                    'pages' => '886-895',
                    'volume' => '10',
                    'number' => '5',
                    'note' => 'ISSN 2320-2882'
                    ]
            ],
            // ISSN
            [
                'source' => 'Miller, P. (2013) Justifying Fiduciary Duties. McGill Law Journal / Revue de droit de McGill ISSN 0024-9041 (print)1920-6356 (digital) ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Miller, P.',
                    'year' => '2013',
                    'title' => 'Justifying Fiduciary Duties',
                    'journal' => 'McGill Law Journal / Revue de droit de McGill',
                    'note' => 'ISSN 0024-9041 (print)1920-6356 (digital)',
                    ]
            ],
            // ISSN
            [
                'source' => 'Crawford, R. (2013) The effect of the financial crisis on the retirement plans of older workers in England, Economics Letters,Volume 121, Issue 2,Pages 156-159, ISSN 0165-1765,https://doi.org/10.1016/j.econlet.2013.07.024 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.econlet.2013.07.024',
                    'author' => 'Crawford, R.',
                    'year' => '2013',
                    'title' => 'The effect of the financial crisis on the retirement plans of older workers in England',
                    'journal' => 'Economics Letters',
                    'pages' => '156-159',
                    'volume' => '121',
                    'number' => '2',
                    'note' => 'ISSN 0165-1765',
                    ]
            ],
            // ISSN
            [
                'source' => '	\bibitem{DG.R. Dunlop} Dunlop D.R., Jones T.P. Position analysis of a two DOF parallel mechanism—the Canterbury tracker // Mechanism and Machine Theory, 1999, Vol 34, Issue 4, pp 599 – 614,ISSN 0094-114X. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Dunlop, D. R. and Jones, T. P.',
                    'title' => 'Position analysis of a two DOF parallel mechanism---the Canterbury tracker',
                    'journal' => 'Mechanism and Machine Theory',
                    'year' => '1999',
                    'pages' => '599-614',
                    'volume' => '34',
                    'number' => '4',
                    'note' => 'ISSN 0094-114X'
                    ]
            ],
            // ISSN
            [
                'source' => 'Degen, M. and Garcia, S. (2012) \'The Transformation of the ‘Barcelona Model’: An Analysis of Culture\'.International Journal of Urban and Regional Research, 36 (5). pp. 1022 - 1038. ISSN: 0309-1317',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Degen, M. and Garcia, S.',
                    'year' => '2012',
                    'title' => 'The Transformation of the `Barcelona Model\': An Analysis of Culture',
                    'journal' => 'International Journal of Urban and Regional Research',
                    'volume' => '36',
                    'number' => '5',
                    'pages' => '1022-1038',
                    'note' => 'ISSN: 0309-1317'
                    ]
            ],
			// need to replace nonstandard space with regular space
			[
                'source' => 'Przezdziecki, Marek A. 2005. Vowel harmony and coarticulation in three dialects of Yoruba: Phonetics determining phonology. Ph.D. dissertation. Cornell University. ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Przezdziecki, Marek A.',
                    'year' => '2005',
                    'title' => 'Vowel harmony and coarticulation in three dialects of Yoruba: Phonetics determining phonology',
                    'school' => 'Cornell University',
                    ]
            ],
			// year-volume-number not parsed
			[
                'source' => 'Ananthakrishnan AN, Beaulieu DB, Ulitsky A, et al. Does primary sclerosing cholangitis impact quality of life in patients with inflammatory bowel disease? 2010;16(3):494-500. doi:10.1002/IBD.21051 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1002/IBD.21051',
                    'author' => 'Ananthakrishnan, A. N. and Beaulieu, D. B. and Ulitsky, A. and others',
                    'year' => '2010',
					'volume' => '16',
					'number' => '3',
					'pages' => '494-500',
                    'title' => 'Does primary sclerosing cholangitis impact quality of life in patients with inflammatory bowel disease?',
                    ]
            ],
            //
            [
                'source' => ' Smith MP, Loe RH. Sclerosing cholangitis: Review of recent case reports and associated diseases and four new cases. The American Journal of Surgery. 1965;110(2):239-246. doi:10.1016/0002-9610(65)90018-8 ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/0002-9610(65)90018-8',
                    'author' => 'Smith, M. P. and Loe, R. H.',
                    'title' => 'Sclerosing cholangitis: Review of recent case reports and associated diseases and four new cases',
                    'year' => '1965',
                    'journal' => 'The American Journal of Surgery.',
                    'volume' => '110',
                    'number' => '2',
                    'pages' => '239-246',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Casali, Roderic F. 2016. Some inventory-related asymmetries in the patterning of tongue root harmony systems. Studies in African Linguistics 45(1/2): 95–140. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Casali, Roderic F.',
                    'year' => '2016',
                    'title' => 'Some inventory-related asymmetries in the patterning of tongue root harmony systems',
					'journal' => 'Studies in African Linguistics',
					'volume' => '45',
					'number' => '1/2',
					'pages' => '95-140',
                    ]
            ],
			// journal not detected
			[
                'source' => 'Purnomo, H. W & Bard, J. F., Cyclic Preference Scheduling for Nurses Using Branch and Price, Naval Research Logistics, 2005, Volume 54, Issue 2, Page 200-220 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Purnomo, H. W. and Bard, J. F.',
                    'title' => 'Cyclic Preference Scheduling for Nurses Using Branch and Price',
                    'journal' => 'Naval Research Logistics',
                    'year' => '2005',
                    'volume' => '54',
                    'number' => '2',
                    'pages' => '200-220',
                    ]
            ],
			// detected as article
			[
                'source' => 'Kelleher J, Namee B, D\'Arcy A. Fundamentals of Machine Learning for Predictive Data Analytics. The MIT Press. 2015. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Kelleher, J. and Namee, B. and D\'Arcy, A.',
                    'title' => 'Fundamentals of Machine Learning for Predictive Data Analytics',
                    'year' => '2015',
                    'publisher' => 'The MIT Press',
                    ]
            ],
            // editor/booktitle mixed up
			[
                'source' => 'Hyman, Larry M. 1999. The Historical Interpretation of Vowel Harmony in Bantu. In Jean-Marie Hombert and Larry M. Hyman (eds.), Bantu Historical Linguistics: Theoretical and Empirical Perspectives, 235–295. Stanford: CSLI Publications.  ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Hyman, Larry M.',
                    'year' => '1999',
                    'title' => 'The Historical Interpretation of Vowel Harmony in Bantu',
                    'pages' => '235-295',
                    'booktitle' => 'Bantu Historical Linguistics: Theoretical and Empirical Perspectives',
					'address' => 'Stanford',
					'publisher' => 'CSLI Publications',
                    'editor' => 'Jean-Marie Hombert and Larry M. Hyman',
                    ]
            ],
			// editor/booktitle mixed up
			 [
                'source' => 'Kutsch Lojenga, Constance. 2009. Two types of vowel harmony in Malila (M.24). In Margarida Petter & Ronald Beline Mendes (eds.), Proceedings of the special world congress of African linguistics: Exploring the African language connection in the Americas, São Paulo, 2008. 109–128. São Paulo: Humanitas. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Kutsch Lojenga, Constance',
                    'year' => '2009',
                    'title' => 'Two types of vowel harmony in Malila (M.24)',
                    'pages' => '109-128',
                    'booktitle' => 'Proceedings of the special world congress of African linguistics: Exploring the African language connection in the Americas, São Paulo, 2008',
					'address' => 'São Paulo',
					'publisher' => 'Humanitas',
                    'editor' => 'Margarida Petter and Ronald Beline Mendes',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// editor, booktitle mixed up
			[
                'source' => 'Morton, Deborah. 2012. Temporal and Aspectual Reference in Bassila Anii. In Elizabeth Bogal-Allbritton (ed.), Proceedings of SULA 6 and SULA-Bar: Semantics of Under-Represented Languages in the Americas (and Elsewhere), 349-363. Amherst: GLSA Publications. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Morton, Deborah',
                    'year' => '2012',
                    'title' => 'Temporal and Aspectual Reference in Bassila Anii',
                    'pages' => '349-363',
                    'booktitle' => 'Proceedings of SULA 6 and SULA-Bar: Semantics of Under-Represented Languages in the Americas (and Elsewhere)',
                    'publisher' => 'GLSA Publications',
                    'address' => 'Amherst',
                    'editor' => 'Elizabeth Bogal-Allbritton',
                    ]
            ],
            // ISSN
            [
                'source' => 'Bernacchia R, Preti R and Vinci G. Chemical Composition and Health Benefits of Flaxseed. Austin J Nutri Food Sci. 2014;2(8): 1045. ISSN: 2381-8980. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Bernacchia, R. and Preti, R. and Vinci, G.',
                    'title' => 'Chemical Composition and Health Benefits of Flaxseed',
                    'year' => '2014',
                    'journal' => 'Austin J Nutri Food Sci',
                    'pages' => '1045',
                    'volume' => '2',
                    'number' => '8',
                    'note' => 'ISSN: 2381-8980',
                    ]
            ],
			// {\em should not be put in note
			[
                'source' => '\bibitem{Heinzinger2023}Heinzinger, M., Weissenow, K., Sanchez, J., Henkel, A., Steinegger, M. \& Rost, B. ProstT5: Bilingual Language Model for Protein Sequence and Structure. {\em bioRxiv}. 2023.07.23.550085v1 (2023).  ',
                'type' => 'unpublished',
                'bibtex' => [
                    'archiveprefix' => 'bioRxiv',
                    'eprint' => '2023.07.23.550085v1',
                    'author' => 'Heinzinger, M. and Weissenow, K. and Sanchez, J. and Henkel, A. and Steinegger, M. and Rost, B.',
                    'title' => 'ProstT5: Bilingual Language Model for Protein Sequence and Structure',
                    'year' => '2023',
                    ]
            ],
			// publisher enclosed in {\em
			[
                'source' => '\bibitem{Alberts2002a} B.~Alberts, A.~Johnson, J.~Lewis, M.~Raff, K.~Roberts, and P.~Walter, ``DNA-Binding Motifs in Gene Regulatory Proteins,\'\' In: ``Molecular Biology of the Cell, 4th edition,\'\' {\em New York: Garland Science}, 2002. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'B. Alberts and A. Johnson and J. Lewis and M. Raff and K. Roberts and P. Walter',
                    'title' => 'DNA-Binding Motifs in Gene Regulatory Proteins',
                    'year' => '2002',
                    'booktitle' => 'Molecular Biology of the Cell, 4th edition',
                    'address' => 'New York',
                    'publisher' => 'Garland Science',
                    ]
            ],
			// publisher enclosed in {\em
			[
                'source' => '\bibitem{Cooper2000} G.~M. Cooper, ``The Central Role of Enzymes as Biological Catalysts,\'\' In: ``The Cell: A Molecular Approach, 2nd edition\'\' {\em Sunderland (MA): Sinauer Associates}, 2000. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'G. M. Cooper',
                    'title' => 'The Central Role of Enzymes as Biological Catalysts',
                    'year' => '2000',
                    'booktitle' => 'The Cell: A Molecular Approach, 2nd edition',
                    'publisher' => 'Sinauer Associates',
                    'address' => 'Sunderland (MA)',
                    ]
            ],
			// problem with {\em not preceded by punctuation
			[
                'source' => '\bibitem{backelin} J. Backelin, J. West, G. Xin,  Wilf-equivalence for singleton classes, {\em Adv. in Appl. Math.} {\bf 38} (2007), no. 2, 133--148. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. Backelin and J. West and G. Xin',
                    'title' => 'Wilf-equivalence for singleton classes',
                    'journal' => 'Adv. in Appl. Math.',
                    'year' => '2007',
                    'volume' => '38',
                    'number' => '2',
                    'pages' => '133-148',
                    ]
            ],
			// publisher enclosed in {\em
			[
                'source' => '\bibitem{Lehoucq1998} R.~B. Lehoucq, D.~C. Sorensen, and C.~Yang, ``ARPACK users guide: solution of large-scale eigenvalue problems with    implicitly restarted Arnoldi methods,\'\' {\em Philadelphia: SIAM}, 1998. \newblock ISBN 978-0-89871-407-4. ',
                'type' => 'book',
                'bibtex' => [
                    'isbn' => '978-0-89871-407-4',
                    'author' => 'R. B. Lehoucq and D. C. Sorensen and C. Yang',
                    'title' => 'ARPACK users guide: solution of large-scale eigenvalue problems with implicitly restarted Arnoldi methods',
                    'year' => '1998',
                    'publisher' => 'SIAM',
                    'address' => 'Philadelphia',
                    ]
            ],
			// publisher mixed up with editors
			[
                'source' => '\bibitem{Berger63} M.~J. Berger, ``Monte Carlo Calculations of the Penetration and Diffusion of Fast Charged Particles\'\', \emph{Methods in Computational Physics},Vol. 1, edited by B.Adler, S. Fernbach, and M. Rotenberg, Academic Press, New York, (1963). ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'M. J. Berger',
                    'title' => 'Monte Carlo Calculations of the Penetration and Diffusion of Fast Charged Particles',
                    'year' => '1963',
                    'booktitle' => 'Methods in Computational Physics, Vol. 1',
                    'publisher' => 'Academic Press',
					'editor' => 'B. Adler and S. Fernbach and M. Rotenberg',
                    'address' => 'New York',
                    ]
            ],
			// year not detected
			[
                'source' => 'Nerea Boada. Experiencia de usuario. https://www.cyberclick.es/numerical-blog/por-que-user-experience-o-experiencia-del-usuario. 2022 ',
                'type' => 'online',
                'bibtex' => [
                    'author' => 'Nerea Boada',
                    'title' => 'Experiencia de usuario',
                    'year' => '2022',
                    'url' => 'https://www.cyberclick.es/numerical-blog/por-que-user-experience-o-experiencia-del-usuario',
                    ]
            ],
			// 'available at' not removed
			[
                'source' => 'Bastian, B.T. et al. (2019) ‘Visual inspection and characterization of external corrosion in pipelines using deep neural network’, NDT & E International, 107, p. 102134. Available at: https://doi.org/10.1016/j.ndteint.2019.102134. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.ndteint.2019.102134',
                    'author' => 'Bastian, B. T. and others',
                    'year' => '2019',
                    'title' => 'Visual inspection and characterization of external corrosion in pipelines using deep neural network',
                    'journal' => 'NDT & E International',
                    'pages' => '102134',
                    'volume' => '107',
                    ]
            ],
			// edition not detected
			[
                'source' => 'Catto, Rebecca. 2016. ‘Reverse Mission: From the Global South to Mainline Churches’. In Church Growth in Britain: 1980 to the Present, edited by David Goodhew, 2nd edition, 91–103. London: Routledge. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Catto, Rebecca',
                    'title' => 'Reverse Mission: From the Global South to Mainline Churches',
                    'year' => '2016',
                    'pages' => '91-103',
                    'note' => '2nd edition',
                    'editor' => 'David Goodhew',
                    'address' => 'London',
                    'publisher' => 'Routledge',
                    'booktitle' => 'Church Growth in Britain: 1980 to the Present',
                    ]
            ],
			// authors ended early
			[
                'source' => 'Cullen G, Kroshinsky D, Cheifetz AS and Korzenik JR: Psoriasis associated with anti-tumour necrosis factor therapy in inflammatory bowel disease: A new series and a review of 120 cases from the literature. Aliment Pharmacol Ther. 34:1318–1327. 2011.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Cullen, G. and Kroshinsky, D. and Cheifetz, A. S. and Korzenik, J. R.',
                    'title' => 'Psoriasis associated with anti-tumour necrosis factor therapy in inflammatory bowel disease: A new series and a review of 120 cases from the literature',
                    'journal' => 'Aliment Pharmacol Ther.',
                    'year' => '2011',
                    'volume' => '34',
                    'pages' => '1318-1327',
                    ]
            ],
			// authors ended early
			[
                'source' => ' Tatu AL, Elisei AM, Chioncel V, Miulescu M and Nwabudike LC: Immunologic adverse reactions of β blockers and the skin (Review). Exp Ther Med 18: 955 959, 2019.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Tatu, A. L. and Elisei, A. M. and Chioncel, V. and Miulescu, M. and Nwabudike, L. C.',
                    'title' => 'Immunologic adverse reactions of β blockers and the skin (Review)',
                    'year' => '2019',
                    'journal' => 'Exp Ther Med',
                    'volume' => '18',
                    'number' => '955',
                    'pages' => '959',
                    ]
            ],
			// authors ended early
			[
                'source' => 'Adair JC, Gold M and Bond RE: Acyclovir neurotoxicity: Clinical experience and review of the literature. South Med J 87: 1227 1231, 1994.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Adair, J. C. and Gold, M. and Bond, R. E.',
                    'title' => 'Acyclovir neurotoxicity: Clinical experience and review of the literature',
                    'year' => '1994',
                    'journal' => 'South Med J',
                    'volume' => '87',
                    'number' => '1227',
                    'pages' => '1231',
                    ]
            ],
			// authors ended early
			[
                'source' => 'Haefeli WE, Schoenenberger RA, Weiss P and Ritz RF: Acyclovir induced neurotoxicity: Concentration side effect relationship in acyclovir overdose. Am J Med 94: 212 215, 1993.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Haefeli, W. E. and Schoenenberger, R. A. and Weiss, P. and Ritz, R. F.',
                    'title' => 'Acyclovir induced neurotoxicity: Concentration side effect relationship in acyclovir overdose',
                    'year' => '1993',
                    'journal' => 'Am J Med',
                    'volume' => '94',
                    'number' => '212',
                    'pages' => '215',
                    ]
            ],
			// authors ended early
			[
                'source' => 'George JN, Raskob GE, Shah SR, Rizvi MA, Hamilton SA, Osborne S and Vondracek T: Drug induced thrombocytopenia: A systematic review of published case reports. Ann Intern Med 129: 886 890, 1998. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'George, J. N. and Raskob, G. E. and Shah, S. R. and Rizvi, M. A. and Hamilton, S. A. and Osborne, S. and Vondracek, T.',
                    'title' => 'Drug induced thrombocytopenia: A systematic review of published case reports',
                    'year' => '1998',
                    'journal' => 'Ann Intern Med',
                    'volume' => '129',
                    'number' => '886',
                    'pages' => '890',
                    ]
            ],
            // authors ended early
			[
                'source' => 'Nguyen ATM and Holland AJA: Balanitis xerotica obliterans: An update for clinicians. Eur J Pediatr. 179:9?16. 2020. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Nguyen, A. T. M. and Holland, A. J. A.',
                    'title' => 'Balanitis xerotica obliterans: An update for clinicians',
                    'year' => '2020',
                    'journal' => 'Eur J Pediatr.',
                    'volume' => '179',
                    'pages' => '9-16',
                    ]
            ],
			// title included in author string.  Hard to see how to solve the problem. (Use fact that ; is being used as name separator??)
			[
                'source' => 'Webber, M. J.; Appel, E. A.; Meijer, E. W.; Langer, R., Supramolecular Biomaterials. Nat. Mater. 2016, 15, 13–26. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Webber, M. J. and Appel, E. A. and Meijer, E. W. and Langer, R.',
                    'title' => 'Supramolecular Biomaterials',
                    'journal' => 'Nat. Mater',
                    'year' => '2016',
                    'volume' => '15',
                    'pages' => '13-26',
                    ]
            ],
			// first word of title included in authors
            [
                'source' => '\bibitem{Kingma13} D. P. Kingma, M. Welling, Auto-encoding variational bayes, arXiv preprint, art no. 1312.6114, 2013.',
                'type' => 'unpublished',
                'bibtex' => [
                    'author' => 'D. P. Kingma and M. Welling',
                    'title' => 'Auto-encoding variational bayes',
                    'archiveprefixl' => 'arXiv',
					'eprint' => '1312.6114',
                    'year' => '2013',
                    ]
            ],
            // author format
            [
                'source' => 'Murshed, Muntasir & Ozturk, Ilhan, 2023. "Rethinking energy poverty reduction through improving electricity accessibility: A regional analysis on selected African nations," Energy, vol. 267. DOI: 10.1016/j.energy.2022.126547  ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/j.energy.2022.126547',
                    'author' => 'Murshed, Muntasir and Ozturk, Ilhan',
                    'year' => '2023',
                    'title' => 'Rethinking energy poverty reduction through improving electricity accessibility: A regional analysis on selected African nations',
                    'journal' => 'Energy',
                    'volume' => '267',
                    ]
            ],
            // author pattern
            [
                'source' => 'Wu, Jia, Junsen Zhang, and Chunchao Wang. 2023. "Student Performance, Peer Effects, and Friend Networks: Evidence from a Randomized Peer Intervention." American Economic Journal: Economic Policy, 15 (1): 510-42. https://hdl.handle.net/10419/289581  ',
                'type' => 'article',
                'bibtex' => [
                    'url' => 'https://hdl.handle.net/10419/289581',
                    'author' => 'Wu, Jia and Junsen Zhang and Chunchao Wang',
                    'year' => '2023',
                    'title' => 'Student Performance, Peer Effects, and Friend Networks: Evidence from a Randomized Peer Intervention',
                    'journal' => 'American Economic Journal: Economic Policy',
                    'volume' => '15',
                    'number' => '1',
                    'pages' => '510-42',
                    ]
            ],
            // author pattern
            [
                'source' => 'Alberto Alesina and David Dollar, (2000), Who Gives Foreign Aid to Whom and Why?, Journal of Economic Growth, 5, (1), 33-63 ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Alberto Alesina and David Dollar',
                    'year' => '2000',
                    'title' => 'Who Gives Foreign Aid to Whom and Why?',
                    'journal' => 'Journal of Economic Growth',
                    'volume' => '5',
                    'number' => '1',
                    'pages' => '33-63',
                    ]
            ],
            // author pattern
            [
                'source' => 'Huang, P., and Y. Liu. 2021. Toward just energy transitions in authoritarian regimes: Indirect participation and adaptive governance. Journal of Environmental Planning and Management 64 (1):1–21. doi:10.1080/09640568.2020.1743245. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1080/09640568.2020.1743245',
                    'author' => 'Huang, P. and Y. Liu',
                    'year' => '2021',
                    'title' => 'Toward just energy transitions in authoritarian regimes: Indirect participation and adaptive governance',
                    'journal' => 'Journal of Environmental Planning and Management',
                    'volume' => '64',
                    'number' => '1',
                    'pages' => '1-21',
                    ]
            ],
            // title delimited by asterisks
            [
                'source' => 'Benaroya, H. (2018). *Building habitats on the Moon: Engineering Approaches to Lunar Settlements*. Cham, Switzerland: Springer. ',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'Benaroya, H.',
                    'year' => '2018',
                    'title' => 'Building habitats on the Moon: Engineering Approaches to Lunar Settlements',
                    'publisher' => 'Springer',
                    'address' => 'Cham, Switzerland',
                    ]
            ],
            // booktitle delimited by asterisks
            [
                'source' => 'Filburn, T., et al. (2011). An investigation into life support systems for a lunar habitat. *41st International Conference on Environmental Systems*. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Filburn, T. and others',
                    'year' => '2011',
                    'title' => 'An investigation into life support systems for a lunar habitat',
                    'booktitle' => '41st International Conference on Environmental Systems',
                    ]
            ],
            // author pattern
            [
                'source' => '[3]	WANG Zihang, YIAN Pengwei, JIANG Zhuoren. Research on social media rumour identification based on interpretable graph neural network model[J]. Journal of Intelligence, 2023, 42(11): 1369-1381. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Wang, Zihang and Yian, Pengwei and Jiang, Zhuoren',
                    'title' => 'Research on social media rumour identification based on interpretable graph neural network model',
                    'year' => '2023',
                    'journal' => 'Journal of Intelligence',
                    'volume' => '42',
                    'number' => '11',
                    'pages' => '1369-1381',
                    ]
            ],
            // author pattern
            [
                'source' => '[6]	Wang J, Zheng V W, Liu Z, et al. Topological recurrent neural network for diffusion prediction[C]// Proceedings of the 2017 IEEE International Conference on Data Mining (ICDM), 2017: 475-484. ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Wang, J. and Zheng, V. W. and Liu, Z. and others',
                    'title' => 'Topological recurrent neural network for diffusion prediction',
                    'year' => '2017',
                    'pages' => '475-484',
                    'booktitle' => 'Proceedings of the 2017 IEEE International Conference on Data Mining (ICDM)',
                    ]
            ],
            // number designation
            [
                'source' => 'A. Brauer, «On a Problem of Partitions», American Journal of Mathematics, vol. 64, n.º 1/4. p. 299-312, 1942. doi: 10.2307/2371684. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'A. Brauer',
                    'title' => 'On a Problem of Partitions',
                    'journal' => 'American Journal of Mathematics',
                    'year' => '1942',
                    'volume' => '64',
                    'number' => '1',
                    'pages' => '299-312',
                    'doi' => '10.2307/2371684',
                    ]
            ],
            // proceedings exception
            [
                'source' => 'J. C. Rosales and P. A. Garcia-Sanchez, «On Cohen-Macaulay and Gorenstein simplicial affine semigroups», Proceedings of the Edinburgh Mathematical Society, vol. 41, no. 3, pp. 517–537, 1998. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'J. C. Rosales and P. A. Garcia-Sanchez',
                    'title' => 'On Cohen-Macaulay and Gorenstein simplicial affine semigroups',
                    'year' => '1998',
                    'pages' => '517-537',
                    'journal' => 'Proceedings of the Edinburgh Mathematical Society',
                    'volume' => '41',
                    'number' => '3',
                    ]
            ],
            // author pattern
            [
                'source' => '3. 	Frizzera, G.; Moran, E.M.; Rappaport, H. ANGIO-IMMUNOBLASTIC LYMPHADENOPATHY WITH DYSPROTEIN®MIA. The Lancet 1974, 303, 1070Ð1073, doi:10.1016/S0140-6736(74)90553-4. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1016/S0140-6736(74)90553-4',
                    'author' => 'Frizzera, G. and Moran, E. M. and Rappaport, H.',
                    'title' => 'ANGIO-IMMUNOBLASTIC LYMPHADENOPATHY WITH DYSPROTEIN®MIA',
                    'journal' => 'The Lancet',
                    'year' => '1974',
                    'volume' => '303',
                    'pages' => '1070-1073',
                    ]
            ],
            // author pattern
            [
                'source' => '5. 	Xu, B.; Liu, P. No Survival Improvement for Patients with Angioimmunoblastic T-Cell Lymphoma over the Past Two Decades: A Population-Based Study of 1207 Cases. PLoS One 2014, 9, doi:10.1371/JOURNAL.PONE.0092585. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1371/JOURNAL.PONE.0092585',
                    'author' => 'Xu, B. and Liu, P.',
                    'title' => 'No Survival Improvement for Patients with Angioimmunoblastic T-Cell Lymphoma over the Past Two Decades: A Population-Based Study of 1207 Cases',
                    'year' => '2014',
                    'journal' => 'PLoS One',
                    'volume' => '9',
                    ]
            ],
            // author pattern
            [
                'source' => 'Petkova, A.T.; Leapman, R.D.; Guo, Z.; Yau, W.M.; Mattson, M.P.; Tycko, R. Self-propagating, molecular-level polymorphism in Alzheimer\'s β-amyloid fibrils. \emph{Science} 2005, 307, 262--265, doi:10.1126/science.1105850. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1126/science.1105850',
                    'author' => 'Petkova, A. T. and Leapman, R. D. and Guo, Z. and Yau, W. M. and Mattson, M. P. and Tycko, R.',
                    'title' => 'Self-propagating, molecular-level polymorphism in Alzheimer\'s β-amyloid fibrils',
                    'journal' => 'Science',
                    'year' => '2005',
                    'volume' => '307',
                    'pages' => '262-265',
                    ]
            ],
            // author pattern
            [
                'source' => 'Yan, X.; Zhu, P.; Li, J. Self-assembly and application of diphenylalanine-based nanostructures. \emph{Chem. Soc. Rev.} 2010, 39, 1877--1890, doi:10.1039/b915765b. ',
                'type' => 'article',
                'bibtex' => [
                    'doi' => '10.1039/b915765b',
                    'author' => 'Yan, X. and Zhu, P. and Li, J.',
                    'title' => 'Self-assembly and application of diphenylalanine-based nanostructures',
                    'pages' => '1877-1890',
                    'journal' => 'Chem. Soc. Rev.',
                    'year' => '2010',
                    'volume' => '39',
                    ]
            ],
			// address and publisher repeated in editor field; detected as book
			[
                'source' => 'Tukey, J. W. 1960. A survey of sampling from contaminated distributions, in 	Contributions to Probability and Statistics, edited by I. Olkin, Stanford University Press, Stanford, CA. ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Tukey, J. W.',
                    'title' => 'A survey of sampling from contaminated distributions',
					'booktitle' => 'Contributions to Probability and Statistics',
                    'year' => '1960',
                    'editor' => 'I. Olkin',
                    'address' => 'Stanford, CA',
                    'publisher' => 'Stanford University Press',
                    ]
            ],
			// author pattern 8 fails because of errors in punctuation.  Need only at least one ;?
			[
                'source' => '\bibitem {Mitra2022} Mitra, G.; Low, J.Z.; Wei, S.; Francisco, K.R., Deffner, M.; Herrmann, C.; Campos, L.M.,; Scheer, E.; Interplay between Magnetoresistance and Kondo Resonance in Radical Single-Molecule Junctions. Nano Lett. 2022, 22, 5773–5779.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Mitra, G. and Low, J. Z. and Wei, S. and Francisco, K. R. and Deffner, M. and Herrmann, C. and Campos, L. M. and Scheer, E.',
                    'year' => '2022',
                    'title' => 'Interplay between Magnetoresistance and Kondo Resonance in Radical Single-Molecule Junctions',
					'journal' => 'Nano Lett',
                    'volume' => '22',
                    'pages' => '5773-5779',
                    ]
            ],
			// phdthesis institution not detected
			[
                'source' => 'Frøyshov, Stig Simeon. 2005. L’Horologe “Georgien” du Sinaiticus Ibericus 34, 2 vols (Ph.D diss., Paris IV/Institut Catholique/Institut de Théologie Orthodoxe Saint-Serge). ',
                'type' => 'phdthesis',
                'bibtex' => [
                    'author' => 'Frøyshov, Stig Simeon',
                    'title' => 'L\'Horologe ``Georgien\'\' du Sinaiticus Ibericus 34',
                    'year' => '2005',
                    'school' => 'Paris IV/Institut Catholique/Institut de Théologie Orthodoxe Saint-Serge',
                    'note' => '2 vols',
                ],
                'char_encoding' => 'utf8leave',
            ],
			// incollection not detected
			[
                'source' => ' Giannico GA, Hameed O (2018) Evaluation of prostate needle biopsies. In: Schatten H (ed) Clinical molecular and diagnostic imaging of prostate Cancer and treatment strategies. Springer Science Business Media ',
                'type' => 'incollection',
                'bibtex' => [
                    'author' => 'Giannico, G. A. and Hameed, O.',
                    'year' => '2018',
                    'title' => 'Evaluation of prostate needle biopsies',
					'editor' => 'Schatten, H.',
                    'publisher' => 'Springer Science Business Media',
                    'booktitle' => 'Clinical molecular and diagnostic imaging of prostate Cancer and treatment strategies',
                    ]
            ],
			// journal not detected
            [
                'source' => '199: Almirall D, DiStefano C, Chang YC, Shire S, Kaiser A, Lu X, Nahum-Shani I, Landa R, Mathy P, Kasari C. Longitudinal Effects of Adaptive Interventions With a Speech-Generating Device in Minimally Verbal Children With ASD. J Clin Child Adolesc Psychol. 2016 Jul-Aug;45(4):442-56. doi: 10.1080/15374416.2016.1138407. Epub 2016 Mar 8. PMID: 26954267; PMCID: PMC4930379. ',
                'type' => 'article',
                'bibtex' => [
                    'note' => 'PMID: 26954267. PMCID: PMC4930379. Epub 2016 Mar 8.',
                    'doi' => '10.1080/15374416.2016.1138407',
                    'author' => 'Almirall, D. and DiStefano, C. and Chang, Y. C. and Shire, S. and Kaiser, A. and Lu, X. and Nahum-Shani, I. and Landa, R. and Mathy, P. and Kasari, C.',
                    'title' => 'Longitudinal Effects of Adaptive Interventions With a Speech-Generating Device in Minimally Verbal Children With ASD',
					'journal' => 'J Clin Child Adolesc Psychol.',
                    'year' => '2016',
                    'month' => 'July-August',
                    'volume' => '45',
					'number' => '4',
					'pages' => '442-56',
                    ]
            ],
            // series not detected
            [
                'source' => '\bibitem[Sc5] {Sc5} M.-H. Schwartz, {\it Classes de Chern des ensembles analytiques.} Actualit\'es math\'ematiques, Hermann, Paris, 2000.',
                'type' => 'book',
                'bibtex' => [
                    'author' => 'M.-H. Schwartz',
                    'title' => 'Classes de Chern des ensembles analytiques',
                    'series' => 'Actualit\'es math\'ematiques',
                    'publisher' => 'Hermann',
                    'address' => 'Paris',
                    'year' => '2000',
                ]
            ],
            // author pattern
            [
                'source' => '\bibitem{Mechti2018} Mechti, S., Khoufi, N., Belguith, L. H.: Improving native language identification model with syntactic features: Case of arabic.. In : Proceedings of the 18th International Conference on Intelligent Systems Design and Applications. pp. 202--211 (2018). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Mechti, S. and Khoufi, N. and Belguith, L. H.',
                    'title' => 'Improving native language identification model with syntactic features: Case of arabic',
                    'year' => '2018',
                    'pages' => '202-211',
                    'booktitle' => 'Proceedings of the 18th International Conference on Intelligent Systems Design and Applications',
                    ]
            ],
            // author pattern
            [
                'source' => '\bibitem{Shervin2014} Shervin, M., DRAS, M.: Arabic native language identification. In : Proceedings of the EMNLP 2014 Workshop on Arabic Natural Language Processing (ANLP). pp. 180--186 (2014). ',
                'type' => 'inproceedings',
                'bibtex' => [
                    'author' => 'Shervin, M. and Dras, M.',
                    'title' => 'Arabic native language identification',
                    'year' => '2014',
                    'pages' => '180-186',
                    'booktitle' => 'Proceedings of the EMNLP 2014 Workshop on Arabic Natural Language Processing (ANLP)',
                    ]
            ],
            // author pattern
            [
                'source' => 'Chadegani AA, Salehi H, Yunus M, Farhadi H, Fooladi M, Farhadi, M, Ebrahim, NA (2013). A Comparison between Two Main Academic Literature Collections: Web of Science and Scopus Databases. Asian Social Science 9(5): 18–26. ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'Chadegani, A. A. and Salehi, H. and Yunus, M. and Farhadi, H. and Fooladi, M. and Farhadi, M. and Ebrahim, N. A.',
                    'year' => '2013',
                    'title' => 'A Comparison between Two Main Academic Literature Collections: Web of Science and Scopus Databases',
                    'journal' => 'Asian Social Science',
                    'volume' => '9',
                    'number' => '5',
                    'pages' => '18-26',
                    ]
            ],
            // remove braces from volume number
            [
                'source' => '\bibitem{C-E-I} R. C. Char{\~a}o, J. T. Espinoza  and R. Ikehata,  \emph{A second order fractional differential equation under effects of a super damping},  Comm. Pure Appl. Analysis, {19} (2020), 4433--4454.  ',
                'type' => 'article',
                'bibtex' => [
                    'author' => 'R. C. Char{\~a}o and J. T. Espinoza and R. Ikehata',
                    'title' => 'A second order fractional differential equation under effects of a super damping',
                    'journal' => 'Comm. Pure Appl. Analysis',
                    'year' => '2020',
                    'volume' => '19',
                    'pages' => '4433-4454',
                    ]
            ],
			// parens should be removed when pages are removed
			[
                'source' => 'Dev, S., Sheng, E., Zhao, J., Amstutz, A., Sun, J., Hou, Y., ... & Chang, K. W. (2022, November). On Measures of Biases and Harms in NLP. In Findings of the Association for Computational Linguistics: AACL-IJCNLP 2022 (pp. 246-267). ',
                'type' => 'article',
                'bibtex' => [
                    'month' => 'November',
                    'author' => 'Dev, S. and Sheng, E. and Zhao, J. and Amstutz, A. and Sun, J. and Hou, Y. and others and Chang, K. W.',
                    'year' => '2022',
                    'title' => 'On Measures of Biases and Harms in NLP',
                    'journal' => 'Findings of the Association for Computational Linguistics: AACL-IJCNLP',
                    'pages' => '246-267',
                    'volume' => '2022',
                    ]
            ],

            


            


            
            
            



        ];

        DB::statement('DELETE FROM examples');
        DB::statement('ALTER TABLE examples AUTO_INCREMENT 1');
        DB::statement('ALTER TABLE example_fields AUTO_INCREMENT 1');

        foreach ($examples as $example) {
            $ex = Example::create([
                'source' => $example['source'],
                'type' => $example['type'],
                'language' => $example['language'] ?? 'en',
                'char_encoding' => $example['char_encoding'] ?? 'utf8',
                'use' => $example['use'] ?? 'latex',
            ]);
            foreach ($example['bibtex'] as $key => $value) {
                ExampleField::create([
                    'example_id' => $ex->id,
                    'name' => $key,
                    'content' => $value
                ]);
            }
        }
    }
}

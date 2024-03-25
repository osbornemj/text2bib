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
                    'author' => 'Andr\\\'{e} B. Chadwick and P. Oblo{\\v z}insk{\\\' y} and M. Herman and N. M. Greene and R. D. McKnight and D. L. Smith and P. G. Young and R. E. MacFarlane and G. M. Hale and S. C. Frankle and A. C. Kahler and T. Kawano and R. C. Little and D. G. Madland and P. Moller and R. D. Mosteller and P. R. Page and P. Talou and H. Trellue and M. C. White and W. B. Wilson and R. Arcilla and C. L. Dunford and S. F. Mughabghab and B. Pritychenko and D. Rochman and A. A. Sonzogni and C. R. Lubitz and T. H. Trumbull and J. P. Weinman and D. A. Br and D. E. Cullen and D. P. Heinrichs and D. P. McNabb and H. Derrien and M. E. Dunn and N. M. Larson and L. C. Leal and A. D. Carlson and R. C. Block and J. B. Briggs and E. T. Cheng and H. C. Huria and M. L. Zerkle and K. S. Kozier and A. Courcelle and V. Pronyaev and S. C. van der Marck',
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
                    'title' => 'How to Make a Sound Map: Cartographic, Compositional, Performative. Acoustic Ecology @ The University of Hull, Scarborough Campus',
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
                    ]
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
                    ]
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
                'source' => '\bibitem{duff1} Darrell Duffie and Wayne Shafer, Equilibrium in Incomplete Markets: I {\em Journal of Mathematical Economics} 14(1985), 285-300. ',
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
                        ]
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
                        'title' => 'To each according to...? Markets, tournaments and the matching problem with borrowing constraints',
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
                    'type' => 'incollection',
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
                        'note' => 'http://www.princeton.edu/\symbol{126}smorris/pdfs/robustmechanism2001.pdf',
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
                        'booktitle' => 'Proc. Amer. Control Conf., San Diego, CA',
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
                        'booktitle' => 'Proc. AIAA Missile Sci. Conf., Monterey, CA',
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
                        'booktitle' => 'Proc. Amer. Cont. Conf., Pittsburgh, PA',
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
                            'author' => 'K. M. Sobel and W. Yu.',
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
                            'booktitle' => 'Proceedings of 7th Int. Symp. On Math Theory of Networks and Systems, Stockholm, Sweden',
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
                            'booktitle' => 'Proc. 12th World I. F. A. C. Congress, Sydney, Australia',
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
                            ]
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
                        'source' => '2007 * | Mooney, W. D., G. C. Beroza, and R. Kind, Fault Zones from Top to Bottom: A Geophysical Perspective, in Tectonic Faults: Agents of Change on a Dynamic Earth, Mark R. Handy, Greg Hirth, and Niels Hovius ed., Dahlem Foundation Conference, Berlin, Germany, ISBN-10:0-262-08362-0, 9-46. ',
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
                            'publisher' => 'Dahlem Foundation Conference',
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
                            'volume' => '114, B07202',
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
                            'title' => 'Depredaci{\\\'o}n de culebra de herradura, Hemorrhois hippocrepis , sobre sapillo pintojo ib{\\\'e}rico, Discoglossus galganoi y sapillo pintojo meridional Discoglossus jeanneae',
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
                            'note' => '',
                            'year' => '',
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
                        'source' => '\bibitem{K2-108} Bleiler, S. A. \& Scharlemann, M. G. (1986). Tangles, property $P$ and a problem of J. Martin. Math. Ann. Vol. 273, 215-225.  ',
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
                            ]
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
                            'title' => 'Brief history of GPS. El Segundo, Ca: Aerospace Corporation',
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
                            ]
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
                        'source' => 'Chen, S. J., L., R., Z. S. Xu, A. A. Khoreshok, H. B. Shao, and F. Feng. 2023. Surface Subsidence Laws of Footwall Coal Seam Mining of Normal Fault Under Different Overburden Strata. Journal of Shandong University of Science & Technology (Natural Science) 42, no. 01: 38–48. ',
                        'type' => 'article',
                        'bibtex' => [
                            'author' => 'Chen, S. J. L. R. and Z. S. Xu and A. A. Khoreshok and H. B. Shao and F. Feng',
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
                            'note' => 'https://arxiv.org/abs/math/0507473',
                            'author' => 'Arteaga, J. R. B. and Malakhaltsev, M. A.',
                            'title' => 'A remark on Ricci flow on left invariant metrics',
                            'year' => '',
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
                            'pages' => '051',
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
                            'title' => 'Saudi Arabia Says $50 Billion Investments Agreed With China',
                            'year' => '2022',
                            'urldate' => '2022, December 12',
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
                            'note' => 'https://web.eecs.umich.edu/~ackerm/pub/03e05/EC-privacy.ackerman.pdf',
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
                            'journal' => 'Wavenet: A generative model for raw audio',
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
                            'author' => 'Billot, B. and Greve, D. N. and Puonti, O. and Thielscher, A. and Van Leemput, K. and Fischl, B. and Iglesias, J. E.',
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
                            'year' => '2023',
                            'urldate' => '13 12 2023',
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

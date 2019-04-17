<?php

Config::set('search', array(
	'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'hooks' => [
		'admin'
	],
    //Cmfive stopwords that will get stripped from search object content before entering the database
    'stopwords' => "about above across after again against all almost alone along already also although always among and any anybody anyone anything anywhere are area areas around ask asked asking asks away back backed backing backs became because become becomes been before began behind being beings best better between big both but came can cannot case cases certain certainly clear clearly come could did differ different differently does done down downed downing downs during each early either end ended ending ends enough even evenly ever every everybody everyone everything everywhere face faces fact facts far felt few find finds first for four from full fully further furthered furthering furthers gave general generally get gets give given gives going good goods got great greater greatest group grouped grouping groups had has have having her here herself high higher highest him himself his how however important interest interested interesting interests into its itself just keep keeps kind knew know known knows large largely last later latest least less let lets like likely long longer longest made make making man many may member members men might more most mostly mrs much must myself necessary need needed needing needs never new newer newest next nobody non noone not nothing now nowhere number numbers off often old older oldest once one only open opened opening opens order ordered ordering orders other others our out over part parted parting parts per perhaps place places point pointed pointing points possible present presented presenting presents problem problems put puts quite rather really right room rooms said same saw say says second seconds see seem seemed seeming seems sees several shall she should show showed showing shows side sides since small smaller smallest some somebody someone something somewhere state states still such sure take taken than that the their them then there therefore these they thing things think thinks this those though thought thoughts three through thus today together too took toward turn turned turning turns two under until upon use used uses very want wanted wanting wants was way ways well wells went were what when where whether which while who whole whose why will with within without work worked working works would year years yet you young younger youngest your yours",
    'stopword_override' => false //Set to true to disable the default MySql stopwords
));

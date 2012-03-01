<?php
    require_once(dirname(__FILE__) . "/makenestedarray.php");
	// get query result
	$sqlresult = mysql_query("
		select	g.group,g.groupid,
				c.category,c.categoryid,
				n.name,n.nameid,n.alias
		from	groups g
		join 	categories c on g.groupid = c.categoryid
		join	names n on c.categoryid = n.categoryid
		order by g.group,c.category,n.name
	");
	// define model for nested data
	// "key" is required for each level to so that the array filter will work
	// "children" should be used for defining nested data	
	$model = array(
		'key'=>'group',
		'group'=>'group',
		'groupid'=>'groupid',
		'children'=>array(
			'categories'=>array(
				'key'=>'category',
				'category'=>'category',
				'children'=>array(
					'names'=>array(
						'key'=>'name',
						"nameid"=>"nameid",
						"alias"=>"alias"
					)
				)
			)
		)
	);
	// pass sql result and data model to makenestedarray() to retrieve nested result	
	$data = makenestedarray($sqlresult,$model);		
	$jsonresult = array("groups" => $data);
	// serialize this mug, and return the string
	return json_encode($jsonresult);
?>
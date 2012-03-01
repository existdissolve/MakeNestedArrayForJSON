<?php
    /*
	 * makenestedarray: this method recursively builds out arrays and arrays of arrays based on array of data
	 * 					and provided data model
	 * ARGUMENTS:	$array (required, array): the array of data to be used
	 * 				$model (required, array): an array representing the model which should be produced from the data
	 * RETURNS: 	array
	 */
    function makenestedarray($array,$model) {
		// check if array is a mysql result; if so, convert it to a flat array
		$array = gettype($array)=='resource' ? convertquerytoarray($array) : $array;
		// set blank index for tracking which rows we've already added	
		$idx = '';
		// blank array to store all the data
		$master = array();
		// retrieve value of "key" for the current iteratin of the data model
		$key = $model['key'];	
		// loop over each row in the passed data array	
		foreach($array as $row) {
			// if the value of the current key is not equal to the index value, evaluate row
			if($row[$key] != $idx) {
				// retrieve data for row based on model definition
				$item = makerowdata($row,$model);
				// if current level of model has "children" defined, evaluate each child
				if(isset($model['children'])) {
					// loop over array of children
					foreach($model['children'] as $child=>$val) {
						// take current array data, and whittle it down based on the child's data key	
						$childarr = filterarraybykey($array,$key,$row[$key]);
						// recursively call makenestedarray() with current filtered array in order to retrieve nested data
						$children = makenestedarray($childarr,$val);
						// add nested data to parent array, using the key of the current child array as the key in the parent
						$item[$child]=$children;
					}
				}
				// done with all the looping; add current data item to master array
				array_push($master,$item);
				// update the index so we can skip rows if they have the same index
				$idx = $row[$key];
			}	
		}
		// yay! all finished; return the completed data object
		return $master;
	}
	/*
	 * makerowdata: this method adds elements to an array based on the key:value definitions provided in the passed ata model
	 * ARGUMENTS:	$row (required, array): the array of data to be used
	 * 				$model (required, array): an array representing the model which should be produced from the data
	 * RETURNS: 	array
	 */
	function makerowdata($row,$model) {
		// empty array to hold our new "column" values	
		$columns = array();	
		// get all the keys of the passed row
		$keys = array_keys($model);
		// loop over all the keys, unless they are named "key" or "children"
		foreach($keys as $pos=>$key) {
			if($key != 'key' && $key != 'children') {
				// add key:value pair to "columns" array
				$columns[$smodel[$key]] = $row[$key];
			}
		}
		return $columns;
	}
	/*
	 * convertquerytoarray: this method turns a mysql result set into a straight array
	 * ARGUMENTS:	$query (required): the mysql result set to be transformed
	 * RETURNS:		array
	 */
	function convertquerytoarray($query) {
		while($row = mysql_fetch_assoc($query)) {
			$rows[] = $row;
		}
		return $rows;
	}
	/*
	 * filterarraybykey: this method filters an array based on the passed key:value pair
	 * ARGUMENTS:	$array (required, array): the array of data to be filtered
	 * 				$key (required, string): the name of the key in the array by which to filter
	 * 				$val (required, string): the value of the key in the array by which to filter
	 * RETURNS:		array
	 */
	function filterarraybykey($array,$key,$val) {
		return array_filter($array,function($row) use($key,$val) {
			// return value in array if the key:value pair matches the passed arguments	
			return $val==$row[$key];
		});
	}
?>
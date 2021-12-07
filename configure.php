<?php
ob_start();
session_start();

define('API_URL', "https://gist.githubusercontent.com/cristiberceanu/94c1539c9bd7cc0f2e3e6e12a26c1551/raw/771417ba472bf1e7c213b6684656be95898892d6/books-data-source.json");

class Connect {

    function dbconnect() {
        define('DBHOST', 'localhost');
        define('DBNAME', 'json-book-auth-db-api');
        define('DBUSER', 'root');
        define('DBPASS', '');
        $connect = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);        
        return $connect;        
    }

}

$conClass = new Connect;
$con = $conClass->dbconnect();	

if( isset($_SERVER['HTTPS']) ) :
    define('HTTP_PROTOCOL', 'https');
else :
    define('HTTP_PROTOCOL', 'http');
endif;

define('URL_BASE', HTTP_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].'/json-book-auth-db-api/');
define('DIR_BASE', $_SERVER['DOCUMENT_ROOT'].'/json-book-auth-db-api/');

define('TB_AUTHORS', 'authors');
define('TB_BOOKS', 'books');

function fetchqry($getitem, $tblnm, $arr="1", $orderby="", $extra="", $return="")
{
	global $con;
	$qry="select ".$getitem." from `".$tblnm."` where 1";
	if(is_array($arr))
	{
		foreach ($arr as $key => $val)
			$qry.=" and ".$key."'".$val."'";
	}
	else
	{
		$qry.=" and ".$arr; 
	}
	if($orderby!="")
		$qry.=" order by ".$orderby;
	
	$qry.=" ".$extra;
	
	if($return=='1')
		return $qry;
	else
		return mysqli_fetch_assoc(mysqli_query($con,$qry));
}
function selectqry($getitem, $tblnm, $arr="1", $orderby="", $extra="", $return="")
{
	global $con;
	$qry="select ".$getitem." from `".$tblnm."` where 1";
        
        if(is_array($arr))
	{
		foreach ($arr as $key => $val)
			$qry.=" and ".$key."'".$val."'";
	}
	else
	{
		$qry.=" and ".$arr; 
	}
	if($orderby!="")
		$qry.=" order by ".$orderby;
	$qry.=" ".$extra;
            
	if($return=='1')
		return $qry;
	else
		return mysqli_query($con,$qry);
}
function getrows($getitem, $tblnm, $arr=array(), $orderby="", $extra="")
{
	global $con;
	$qry="select ".$getitem." from `".$tblnm."` where 1";	
	if(is_array($arr))
	{
		foreach ($arr as $key => $val)
			$qry.=" and ".$key."'".$val."'";
	}
	else
	{
		$qry.=" and ".$arr; 
	}
	if($orderby!="")
		$qry.=" order by ".$orderby;
	$qry.=" ".$extra;	
	if(mysqli_num_rows(mysqli_query($con,$qry))>0):
		return mysqli_num_rows(mysqli_query($con,$qry));
	else:
		return mysqli_error($con);
        endif;
}

function getQuesitonName($questionID = 1){
    
    $res = fetchqry('*', TB_QUESTION_MASTER, array('ID='=>$questionID));
    
    if( count($res) > 0 && !empty($res['description']) ):
        return $res['description'];
    else:
        return "Qustion not available";
    endif;
    
    
}
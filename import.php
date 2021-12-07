<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
//error_reporting(0);

require_once __DIR__.'/configure.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, API_URL);
$res = curl_exec($ch);
curl_close($ch);
$result = json_decode($res);

if ( isset($result) && !empty($result) && count($result) > 0 ) :    
    
    $qry = "TRUNCATE `".TB_AUTHORS."`";
    mysqli_query($con,$qry);
    
    foreach( $result as $row ):
        if(!empty($row->authors) && count($row->authors)>0):
            foreach($row->authors as $author):                    
                if(!empty($author)):
                    $author = mysqli_real_escape_string($con, $author);    
                    $duplicate = fetchqry('`name`', TB_AUTHORS,array('`name`='=>$author));
                    if(empty($duplicate)):
                        $qry = "INSERT INTO `".TB_AUTHORS."` (`id`, `name`,`created_at`) VALUES (NULL, '".trim($author)."','".date('Y-m-d h:i:s')."');";
                        mysqli_query($con,$qry);                        
                    endif;
                endif;                
            endforeach;
        endif;              
    endforeach;
    
    /*Import Books*/
    $qry = "TRUNCATE `".TB_BOOKS."`";
    mysqli_query($con,$qry);
    
    foreach( $result as $rows ):     
        
        if(!empty($rows->title)):
            $title = (!empty($rows->title)) ? $rows->title : "NULL";
            $isbn = (!empty($rows->isbn)) ? $rows->isbn : "NULL";
            $pageCount = (!empty($rows->pageCount)) ? $rows->pageCount : "NULL";
            $publishedDate = (!empty($rows->publishedDate)) ? $rows->publishedDate->{'$date'} : "";
            $publishedDate = (!empty($publishedDate)) ? date('Y-m-d h:i:s', strtotime($publishedDate)) : 'NULL';
            if($publishedDate != 'NULL'){ 
                $publishedDate = "\"$publishedDate\"";
            }
            $thumbnailUrl = (!empty($rows->thumbnailUrl)) ? mysqli_real_escape_string($con, $rows->thumbnailUrl) : "NULL";
            $shortDescription = (!empty($rows->shortDescription)) ? mysqli_real_escape_string($con, $rows->shortDescription) : "NULL";
            $longDescription = (!empty($rows->longDescription)) ? mysqli_real_escape_string($con, $rows->longDescription) : "NULL";
            $status = (!empty($rows->status)) ? $rows->status : "NULL";
            $categories = (!empty($rows->categories)) ? implode(',', $rows->categories) : "NULL";
            
            if(!empty($rows->authors)):
                foreach($rows->authors as $author):                    
                    if(!empty($author)):
                        $author = mysqli_real_escape_string($con, $author);    
                        $duplicate = fetchqry('`id`', TB_AUTHORS,array('`name`='=>$author));
                        if(!empty($duplicate['id'])):
                            $qry = 'INSERT INTO `'.TB_BOOKS.'` ';
                            $qry .= '(`id`, `authorsId`,`title`,`isbn`,`pageCount`,`publishedDate`,`thumbnailUrl`,`shortDescription`,`longDescription`,`status`,`categories`,`created_at`) VALUES ';
                            $qry .= '(NULL,"'.trim($duplicate['id']).'","'.trim($title).'","'.trim($isbn).'","'.trim($pageCount).'",'.trim($publishedDate).',"'.trim($thumbnailUrl).'","'.trim($shortDescription).'","'.trim($longDescription).'","'.trim($status).'","'.trim($categories).'","'.date('Y-m-d h:i:s').'");';
                            $res = mysqli_query($con,$qry);                        
                        endif;
                    endif;                
                endforeach;
            endif;
        endif; 
        
    endforeach;
    
    $response = '{"status":"success","message":"Records imported successfully"}';

else:  
    $response = '{"status":"error","message":"No records found"}';
endif;

echo $response;
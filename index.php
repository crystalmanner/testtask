<?php
require 'configure.php';
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Import Data</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" >
    </head>
    <body>       
        <br><br>
            <center>
                <h2><u>Top 3 Authors</u></h2>
            </center>
        <br><br>
        <?php
        $qry = "SELECT b.authorsId, count(b.authorsId) as totalBookPublished, a.name ";
        $qry .= " from `".TB_BOOKS."` as b";
        $qry .= " LEFT JOIN `".TB_AUTHORS."` as a on (b.authorsId = a.id)";
        $qry .= " GROUP BY b.authorsId ";
        $qry .= " ORDER BY totalBookPublished DESC LIMIT 3";
        $result = mysqli_query($con,$qry);
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Author</th>
                    <th scope="col">Book Published</th>                
                </tr>
            </thead>
            <tbody>
                <?php
                $i=1;
                if(mysqli_num_rows($result) > 0):
                    $rows = array();
                    while($row = mysqli_fetch_assoc($result)):
                        array_push($rows, $row);
                    endwhile;
                    foreach($rows as $row): ?>
                        <tr>
                            <th scope="row"><?php echo $i;?></th>
                            <td><?php echo $row['name'];?></td>
                            <td><?php echo $row['totalBookPublished'];?></td>                
                        </tr>
                <?php    
                    $i++;
                    endforeach;
                else:
                    echo '<tr><td colspan="3">No records found</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
        <br><br>
        <hr>
        <br><br>
        <form method="post" action="javascript:void(0);" enctype="multipart/form-data">
            <center>
                <input id="importAuthors" type="button" value="Click Here to Import Data" />
            </center>
        </form>
        <br><br>
        <hr>
        <script src="js/jquery-2.2.4.min.js" ></script>
        <script>  
            //import authors
            $("#importAuthors").on('click', function(){ 
                $("body").append("<div id='ajax_loader_42160' style='background:black;opacity:0.7;position:fixed;top:0;bottom:0;width:100%;height:100%;z-index:99999999;display:none;'><div style='margin:200px auto; width:300px;'><img width='300' src='images/loading_home.gif' alt=''></div></div>");
                $("#ajax_loader_42160").show();
                setTimeout(function(){                      
                    $.ajax({
                        type: "POST",
                        url: "import.php",
                        async: false,
                        data: {"functionName": "import"},
                        success: function (data) {
                            $("#ajax_loader_42160").hide();
                            $("#ajax_loader_42160").remove();
                            let res = JSON.parse(data);                         
                            if (res.status == "success") {
                                alert(res.message);  
                                location.reload();
                            } else {
                                alert(res.message);
                            }
                        }
                    });
                },1000);
            });                             
        </script>
    </body>    
</html>
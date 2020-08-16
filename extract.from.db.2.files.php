<?php
//=================================================================================================

mysql_connect("db_host", "user_db_name", "user_db_pass") or
    die("Ошибка соединения: " . mysql_error());
mysql_select_db("db_name");
echo ("CONNECTED\n");

$res = mysql_query("SELECT p2.id as attach_id , p2.bug_id , p2.filename, p2.filesize, p2.folder
FROM vasilisa_bug_file_support p2");
//WHERE  p2.bug_id='0401570'");

while ($row = mysql_fetch_array($res))
{

        echo "Task Id is : ".$row['bug_id'];

        $res2 = mysql_query("SELECT content from vasilisa_bug_file_support WHERE id = ".$row['attach_id']);
        $file = mysql_fetch_array($res2);

        print "size: ".$row['filesize']." id: ".$row['attach_id'] ."\n";
        $sha1 = sha1($file['content']);
        $filename = "izabella-".$row['bug_id'].'-'.$row['attach_id'].'-'.$row['filename'];
        $folder = "/home/gorovoy/attachments.izabella/";
        $full_filename = $folder.$filename;

        printf("File full path: \n".$full_filename);

        if (file_exists($full_filename))
        {
                printf("file already exists\n");
                if($sha1 == sha1(file_get_contents($full_filename))) {
                        printf("file is identical!\n");
                } else {
                        die("file is not identical!\n");
                }
        }
        try {
            $size =  file_put_contents($full_filename, $file['content']);

        if ($row['filesize'] == $size) {
                $query="UPDATE vasilisa_bug_file_support SET diskfile ='" .$filename."', folder = '".$folder."'  WHERE id = ".$row['attach_id'];
//                echo $query."\n";
                mysql_query($query);
        } else {
//                die("filesize missmatch: ".$size." expected:".$row['filesize']."\n");
        }

        } catch (Exception $e) {
             echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }

//        ob_flush();
        flush();
}

?>

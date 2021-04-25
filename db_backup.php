<?php
function db_backup(){
    $connection = mysqli_connect('localhost','root','','t1');
    $tables = array();
    $result = mysqli_query($connection,"SHOW TABLES");
    if($result->num_rows > 0){
      while($row = mysqli_fetch_row($result)){
        $tables[] = $row[0];
      }
      $return = '';
      foreach($tables as $table){
        $result = mysqli_query($connection,"SELECT * FROM ".$table);
        $num_fields = mysqli_num_fields($result);
        
        $return .= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($connection,"SHOW CREATE TABLE ".$table));
        $return .= "\n".$row2[1].";\n\n";
        
        for($i=0;$i<$num_fields;$i++){
          while($row = mysqli_fetch_row($result)){
            $return .= "INSERT INTO ".$table." VALUES(";
            for($j=0;$j<$num_fields;$j++){
              $row[$j] = addslashes($row[$j]);
              if(isset($row[$j])){ $return .= '"'.$row[$j].'"';}
              else{ $return .= '""';}
              if($j<$num_fields-1){ $return .= ',';}
            }
            $return .= ");\n";
          }
        }
        $return .= "\n\n\n";
      }
      $connection = NULL;
  
      //--------------save file-----------------//
      //Create backup folder if not exists
      if(!file_exists("backup/")){
        mkdir("backup");
      }
      $fileName = date("Y-m-d__H-i-s") . ".sql";
      $path = "backup/" . $fileName;
      $handle = fopen($path,"w");
      if(fwrite($handle,$return)){
        fclose($handle);
        echo "Successfully backed up";
      }
      else{
        echo "Error Occured while writing the file!!!";
      }
    }
    else{
      echo "No Tables Found!!!";
    }
}
db_backup();
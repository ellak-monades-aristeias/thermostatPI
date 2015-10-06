<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Thresholds</title>
  </head>
  <body>

    <?php
      include_once 'config.php';

      $userU = NULL;
      $passU = NULL;
      //Check if the user name and password have been set.
      if (isset($_POST["userU"]) && isset($_POST["passU"]) ) {
        $userU  = $_POST["userU"];
        $passU  = $_POST["passU"];
      } else if (isset($_GET["userU"]) && isset($_GET["passU"]) ) {
        $userU  = $_GET["userU"];
        $passU  = $_GET["passU"];
      }
      if ($userU!=NULL && $passU!=NULL) {
        //Check if they are correct.
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $sql = "SELECT count(*) AS c FROM config AS c1, config AS c2 WHERE c1.`key`='username' AND c2.`key`='password' AND c1.`value`=:userU AND c2.`value`=:passU";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':userU', $userU);
          $stmt->bindParam(':passU', $passU);
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($row['c']!=1) {
            //Wrong username or password.
            $userU = NULL;
            $passU = NULL;
          }
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
      }
    ?>

    <?php
      //If the password or username is not set or are not correct, display the login form.
      if (is_null($userU) or is_null($passU)) {
    ?>
        <a href='thermostatConfiguration.php'>Please, Login.</a>
    <?php
        echo '</body></html>';
        return;
      }
    ?>

    <?php
      //Check if we must delete something. As the timestamps can not overlap, use only the start time.
      if (isset($_GET["delete"]) && $_GET["delete"]=="true" && isset($_GET["startDay"]) && isset($_GET["startHour"]) && isset($_GET["startMinute"]) ) {
        $startDay =    $_GET["startDay"];
        $startHour =   $_GET["startHour"];
        $startMinute = $_GET["startMinute"];
        
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $sql = "DELETE FROM `thresholds` WHERE `startDay`=:startDay AND `startHour`=:startHour AND `startMinute`=:startMinute";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':startDay',    $startDay);
          $stmt->bindParam(':startHour',   $startHour);
          $stmt->bindParam(':startMinute', $startMinute);
          $stmt->execute();
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
      }

      //Check if we must insert something.
      $startDay      = NULL;
      $startHour     = NULL;
      $startMinute   = NULL;
      $endDay      = NULL;
      $endHour     = NULL;
      $endMinute   = NULL;
      $lowerin  = NULL;
      $upperin  = NULL;
      $lowerout = NULL;
      $upperout = NULL;
      //Check if a new threshold was submited.
      if (isset($_POST["startDay"]) && isset($_POST["startHour"]) && isset($_POST["startMinute"]) && isset($_POST["endDay"]) && isset($_POST["endHour"]) && isset($_POST["endMinute"]) && isset($_POST["lowerin"]) && isset($_POST["upperin"]) && isset($_POST["lowerout"]) && isset($_POST["upperout"]) ) {
        $startDay      = intval($_POST["startDay"]);
        $startHour     = intval($_POST["startHour"]);
        $startMinute   = intval($_POST["startMinute"]);
        $endDay      = intval($_POST["endDay"]);
        $endHour     = intval($_POST["endHour"]);
        $endMinute   = intval($_POST["endMinute"]);
        $lowerin  = floatval($_POST["lowerin"]);
        $upperin  = floatval($_POST["upperin"]);
        $lowerout = floatval($_POST["lowerout"]);
        $upperout = floatval($_POST["upperout"]);
        
        if($_POST["startDay"]=="" || $_POST["startHour"]=="" || $_POST["startMinute"]=="" || $_POST["endDay"]=="" || $_POST["endHour"]=="" || $_POST["endMinute"]=="" || $_POST["lowerin"]=="" || $_POST["upperin"]=="" || $_POST["lowerout"]=="" || $_POST["upperout"]=="") {
          echo "<h2> Some values were not in the correct format, please check them.</h2>";
        } else if($startDay<0 || $startDay>6) {
          echo "<h2> Start day must be an integer between 0 and 6.</h2>";
        } else if ($startHour<0 || $startHour>23) {
          echo "<h2> Start hour must be an integer between 0 and 23.</h2>";
        } else if ($startMinute<0 || $startMinute>59) {
          echo "<h2> Start minute must be an integer between 0 and 59.</h2>";
        } else if($endDay<0 || $endDay>6) {
          echo "<h2> End day must be an integer between 0 and 6.</h2>";
        } else if ($endHour<0 || $endHour>23) {
          echo "<h2> End hour must be an integer between 0 and 23.</h2>";
        } else if ($endMinute<0 || $endMinute>59) {
          echo "<h2> End minute must be an integer between 0 and 59.</h2>";
        } else {
          //Values are ok.
        
          //Insert int database.
          try {
            $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "INSERT INTO `thresholds`(`startDay`, `startHour`, `startMinute`, `endDay`, `endHour`, `endMinute`, `lowerThresholdIn`, `upperThresholdIn`, `lowerThresholdOut`, `upperThresholdOut`) VALUES (:startDay, :startHour, :startMinute, :endDay, :endHour, :endMinute, :lowerThresholdIn, :upperThresholdIn, :lowerThresholdOut, :upperThresholdOut)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':startDay',    $startDay);
            $stmt->bindParam(':startHour',   $startHour);
            $stmt->bindParam(':startMinute', $startMinute);
            $stmt->bindParam(':endDay',    $endDay);
            $stmt->bindParam(':endHour',   $endHour);
            $stmt->bindParam(':endMinute', $endMinute);
            $stmt->bindParam(':lowerThresholdIn',  $lowerin);
            $stmt->bindParam(':upperThresholdIn',  $upperin);
            $stmt->bindParam(':lowerThresholdOut', $lowerout);
            $stmt->bindParam(':upperThresholdOut', $upperout);
            $stmt->execute();
            
            //If executed sucessfully, do not show them on the input fields.
            $startDay      = NULL;
            $startHour     = NULL;
            $startMinute   = NULL;
            $endDay      = NULL;
            $endHour     = NULL;
            $endMinute   = NULL;
            $lowerin  = NULL;
            $upperin  = NULL;
            $lowerout = NULL;
            $upperout = NULL;
          } catch (PDOException $e) {
            echo 'Error in sql: ' . $e->getMessage();
          }
          //Close connection.
          $dbh = null;
        }
      }
    ?>
    
    <form method="POST" action="thermostatConfiguration.php">
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
      <input type="submit" name="submit" value="Go to configuration page."/>
    </form>
    
    <form method="POST" action="">
      <table>
        <tr>
          <td>StartDay</td>
          <td>StartHour</td>
          <td>StartMinute</td>
          <td>EndDay</td>
          <td>EndHour</td>
          <td>EndMinute</td>
          <td>LowerIn</td>
          <td>UpperIn</td>
          <td>LowerOut</td>
          <td>UpperOut</td>
          <td> </td>
        </tr>
        <tr>
          <td> <input type="text" name="startDay"      value="<?php echo $startDay; ?>"> </td>
          <td> <input type="text" name="startHour"     value="<?php echo $startHour; ?>"> </td>
          <td> <input type="text" name="startMinute"   value="<?php echo $startMinute; ?>"> </td>
          <td> <input type="text" name="endDay"      value="<?php echo $endDay; ?>"> </td>
          <td> <input type="text" name="endHour"     value="<?php echo $endHour; ?>"> </td>
          <td> <input type="text" name="endMinute"   value="<?php echo $endMinute; ?>"> </td>
          <td> <input type="text" name="lowerin"  value="<?php echo $lowerin; ?>"> </td>
          <td> <input type="text" name="upperin"  value="<?php echo $upperin; ?>"> </td>
          <td> <input type="text" name="lowerout" value="<?php echo $lowerin; ?>"> </td>
          <td> <input type="text" name="upperout" value="<?php echo $upperout; ?>"> </td>
          <td> <input type="submit" name="submit" value="Add"/> </td>
        </tr>
        
    <?php
      //Display the hours that have been set until now.
      $drawJavascriptString = "";
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM `thresholds` ORDER BY `startDay`, `startHour`, `startMinute`;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $startDay    = $row['startDay'];
          $startHour   = $row['startHour'];
          $startMinute = $row['startMinute'];
          $endDay    = $row['endDay'];
          $endHour   = $row['endHour'];
          $endMinute = $row['endMinute'];
          $lowerThresholdIn  = $row['lowerThresholdIn'];
          $upperThresholdIn  = $row['upperThresholdIn'];
          $lowerThresholdOut = $row['lowerThresholdOut'];
          $upperThresholdOut = $row['upperThresholdOut'];
          echo "<tr>";
          echo "<td> $startDay </td>";
          echo "<td> $startHour </td>";
          echo "<td> $startMinute </td>";
          echo "<td> $endDay </td>";
          echo "<td> $endHour </td>";
          echo "<td> $endMinute </td>";
          echo "<td> $lowerThresholdIn </td>";
          echo "<td> $upperThresholdIn </td>";
          echo "<td> $lowerThresholdOut </td>";
          echo "<td> $upperThresholdOut </td>";
          echo "<td> <a href='viewThresholds.php?userU=$userU&passU=$passU&delete=true&startDay=$startDay&startHour=$startHour&startMinute=$startMinute'> Delete </a> </td>";
          echo "</tr>";
          while($startDay != $endDay) {
            $startTime = $startDay*24*60 + $startHour*60 + $startMinute;
            $drawJavascriptString .= "ctx.fillRect($startHour*60 + $startMinute + leftOffset, 10*$startDay + topOffset, (24*60) - $startHour*60 + $startMinute, 9);";
            $startDay++;
            $startHour   = 0;
            $startMinute = 0;
          }
          //The start and end days are the same.
          $startTime = $startHour*60 + $startMinute;
          $endTime   = $endHour*60   + $endMinute;
          $timeDiffInMinutes = $endTime - $startTime;
          $drawJavascriptString .= "ctx.fillRect($startTime + leftOffset, 10*$startDay + topOffset, $timeDiffInMinutes, 9);";
        }
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }
      //Close connection.
      $dbh = null;
    ?>

      </table>
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
    </form>
    
    <canvas id="myCanvas" width="1501" height="81" style="border:1px solid #000000;"> </canvas>
    <script>
      //Get the context of the canvas.
      var c = document.getElementById("myCanvas");
      var ctx = c.getContext("2d");

      var leftOffset = 60;
      var topOffset = 10;
     
      //Draw the hours.
      ctx.font = "10px Arial";
      for (var i=0; i<24; i++) {
        ctx.fillText(""+i, leftOffset+i*60, 9);
      }
      
      //Draw the days.
      ctx.fillText("Monday",    0, 20);
      ctx.fillText("Tuesday",   0, 30);
      ctx.fillText("Wednesday", 0, 40);
      ctx.fillText("Thursday",  0, 50);
      ctx.fillText("Friday",    0, 60);
      ctx.fillText("Saturday",  0, 70);
      ctx.fillText("Sunday",    0, 80);
      
      //Draw the rectangles gray.
      ctx.fillStyle="#CCCCCC";
      var graphWidth = 24 * 60 - 1;
      for (var i=0; i<7; i++) {
        ctx.fillRect(leftOffset, i*10 + topOffset, graphWidth, 9);
      }

      //Draw the times which have the thermostat open as gree.
      ctx.fillStyle="#00FF00";
      <?php echo $drawJavascriptString; ?>
     /* 
      //Draw green when on, gray when off.
      for(var i=0; i<7; i++) {
        for(var j=0; j<24; j++) {
          for(var k=0; k<60; k++) {
            if(k<30) {
              ctx.strokeStyle="#0000FF";
              ctx.beginPath();
              ctx.moveTo(j*60+k+leftOffset, i*10+topOffset);
              ctx.lineTo(j*60+k+leftOffset, (i+1)*10-1+topOffset);
              ctx.closePath();
              ctx.stroke();
            }
          }
        }
      }
     */
       
    </script>
  </body>
</html>


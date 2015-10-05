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
      //Check if we must delete something.
      if (isset($_GET["delete"]) && $_GET["delete"]=="true" && isset($_GET["day"]) && isset($_GET["hour"]) && isset($_GET["minute"]) ) {
        $day =    $_GET["day"];
        $hour =   $_GET["hour"];
        $minute = $_GET["minute"];
        
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $sql = "DELETE FROM `thresholds` WHERE `day`=:day AND `hour`=:hour AND `minute`=:minute";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':day',    $day);
          $stmt->bindParam(':hour',   $hour);
          $stmt->bindParam(':minute', $minute);
          $stmt->execute();
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
      }

      //Check if we must insert something.
      $day      = NULL;
      $hour     = NULL;
      $minute   = NULL;
      $lowerin  = NULL;
      $upperin  = NULL;
      $lowerout = NULL;
      $upperout = NULL;
      //Check if a new threshold was submited.
      if (isset($_POST["day"]) && isset($_POST["hour"]) && isset($_POST["minute"]) && isset($_POST["lowerin"]) && isset($_POST["upperin"]) && isset($_POST["lowerout"]) && isset($_POST["upperout"]) ) {
        $day      = intval($_POST["day"]);
        $hour     = intval($_POST["hour"]);
        $minute   = intval($_POST["minute"]);
        $lowerin  = floatval($_POST["lowerin"]);
        $upperin  = floatval($_POST["upperin"]);
        $lowerout = floatval($_POST["lowerout"]);
        $upperout = floatval($_POST["upperout"]);
        
        if($_POST["day"]=="" || $_POST["hour"]=="" || $_POST["minute"]=="" || $_POST["lowerin"]=="" || $_POST["upperin"]=="" || $_POST["lowerout"]=="" || $_POST["upperout"]=="") {
          echo "<h2> Some values were not in the correct format, please check them.</h2>";
        } else if($day<0 || $day>6) {
          echo "<h2> day must be an integer between 0 and 6.</h2>";
        } else if ($hour<0 || $hour>23) {
          echo "<h2> hour must be an integer between 0 and 23.</h2>";
        } else if ($minute<0 || $minute>23) {
          echo "<h2> minute must be an integer between 0 and 59.</h2>";
        } else {
          //Values are ok.
        
          //Insert int database.
          try {
            $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "INSERT INTO `thresholds`(`day`, `hour`, `minute`, `lowerThresholdIn`, `upperThresholdIn`, `lowerThresholdOut`, `upperThresholdOut`) VALUES (:day, :hour, :minute, :lowerThresholdIn, :upperThresholdIn, :lowerThresholdOut, :upperThresholdOut)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':day',    $day);
            $stmt->bindParam(':hour',   $hour);
            $stmt->bindParam(':minute', $minute);
            $stmt->bindParam(':lowerThresholdIn',  $lowerin);
            $stmt->bindParam(':upperThresholdIn',  $upperin);
            $stmt->bindParam(':lowerThresholdOut', $lowerout);
            $stmt->bindParam(':upperThresholdOut', $upperout);
            $stmt->execute();
            
            //If executed sucessfully, do not show them on the input fields.
            $day      = NULL;
            $hour     = NULL;
            $minute   = NULL;
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
          <td>Day</td>
          <td>Hour</td>
          <td>Minute</td>
          <td>LowerIn</td>
          <td>UpperIn</td>
          <td>LowerOut</td>
          <td>UpperOut</td>
          <td> </td>
        </tr>
        <tr>
          <td> <input type="text" name="day"      value="<?php echo $day; ?>"> </td>
          <td> <input type="text" name="hour"     value="<?php echo $hour; ?>"> </td>
          <td> <input type="text" name="minute"   value="<?php echo $minute; ?>"> </td>
          <td> <input type="text" name="lowerin"  value="<?php echo $lowerin; ?>"> </td>
          <td> <input type="text" name="upperin"  value="<?php echo $upperin; ?>"> </td>
          <td> <input type="text" name="lowerout" value="<?php echo $lowerin; ?>"> </td>
          <td> <input type="text" name="upperout" value="<?php echo $upperout; ?>"> </td>
          <td> <input type="submit" name="submit" value="Add"/> </td>
        </tr>
        
    <?php
      //Display the hours that have been set until now.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM `thresholds` ORDER BY `day`, `hour`, `minute`;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $day    = $row['day'];
          $hour   = $row['hour'];
          $minute = $row['minute'];
          $lowerThresholdIn  = $row['lowerThresholdIn'];
          $upperThresholdIn  = $row['upperThresholdIn'];
          $lowerThresholdOut = $row['lowerThresholdOut'];
          $upperThresholdOut = $row['upperThresholdOut'];
          echo "<tr>";
          echo "<td> $day </td>";
          echo "<td> $hour </td>";
          echo "<td> $minute </td>";
          echo "<td> $lowerThresholdIn </td>";
          echo "<td> $upperThresholdIn </td>";
          echo "<td> $lowerThresholdOut </td>";
          echo "<td> $upperThresholdOut </td>";
          echo "<td> <a href='viewThresholds.php?userU=$userU&passU=$passU&delete=true&day=$day&hour=$hour&minute=$minute'> Delete </a> </td>";
          echo "</tr>";
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
      
      //Draw green when on, gray when off.
      for(var i=0; i<7; i++) {
        for(var j=0; j<24; j++) {
          for(var k=0; k<60; k++) {
            if(k<30) {
              ctx.strokeStyle="#0000FF";
            } else {
              ctx.strokeStyle="#FF0000";
            }
            ctx.beginPath();
            ctx.moveTo(j*60+k+leftOffset, i*10+topOffset);
            ctx.lineTo(j*60+k+leftOffset, (i+1)*10-1+topOffset);
            ctx.closePath();
            ctx.stroke();
          }
        }
      }
      
    </script>
  </body>
</html>


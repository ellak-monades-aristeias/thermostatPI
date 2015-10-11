<?php ini_set('memory_limit', '256M'); ?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Consumption
    </title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>
  </head>
  <body>

    <?php
      include_once 'config.php';

      $userU = NULL;
      $passU = NULL;
      //Check if the user name and password have been set.
      if (isset($_POST["userU"]) && isset($_POST["passU"]) ) {
        $userU   = $_POST["userU"];
        $passU  = $_POST["passU"];
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

    <form method="POST" action="thermostatConfiguration.php">
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
      <input type="submit" name="submit" value="Go to configuration page."/>
    </form>

    <table>
    
    <?php
      //Get the measurments.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT count(*) as minutesOn  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND `status`=1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $minutesOn = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $minutesOn  = $row['minutesOn'];
        }
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }
      //Close connection.
      $dbh = null;
    ?>
    
      <tr>
        <?php $hoursOn   = floor($minutesOn / 60); ?>
        <?php $minutesOn = $minutesOn % 60; ?>
        <td>Last day "ON":</td>
        <td><?php echo "$hoursOn hours and $minutesOn minutes."; ?></td>
      </tr>
    
    <?php
      //Get the measurments.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT count(*) as minutesOn  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 WEEK) AND `status`=1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
                                                                                                                           $minutesOn = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $minutesOn  = $row['minutesOn'];
        }
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }
      //Close connection.
      $dbh = null;
    ?>
    
      <tr>
        <?php $hoursOn   = floor($minutesOn / 60); ?>
        <?php $minutesOn = $minutesOn % 60; ?>
        <td>Last week "ON":</td> 
        <td><?php echo "$hoursOn hours and $minutesOn minutes."; ?></td>
      </tr>

    <?php
      //Get the measurments.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT count(*) as minutesOn  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 MONTH) AND `status`=1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $minutesOn = 0;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $minutesOn  = $row['minutesOn'];
        }
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }  
      //Close connection.
      $dbh = null;
    ?>
      
      <tr>
        <?php $hoursOn   = floor($minutesOn / 60); ?>
        <?php $minutesOn = $minutesOn % 60; ?>
        <td>Last month "ON":</td> 
        <td><?php echo "$hoursOn hours and $minutesOn minutes."; ?></td>
      </tr>
    </table>
    
    <div id="chartContainer" style="height: 400px; width: 100%;"></div>
    
    <?php
      //Get the measurments.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT DATE_FORMAT(`time`, '%Y-%m-%dT%T') AS `time`, `temperatureIn`, `temperatureOut`, `status`  FROM `measurements` ORDER BY `time` ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        $timeArray    = array();
        $tempInArray  = array();
        $tempOutArray = array();
        $statusArray  = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $time    = $row['time'];
          $temperatureIn  = $row['temperatureIn'];
          $temperatureOut = $row['temperatureOut'];
          $status  = $row['status'];
          $timeArray[]    = $time;
          $tempInArray[]  = $temperatureIn;
          $tempOutArray[] = $temperatureOut;
          $statusArray[]  = $status;
        }
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }
      //Close connection.
      $dbh = null;
      
      $length = count($timeArray);
    ?>
    
    <script type="text/javascript">
    window.onload = function () {
      var chart = new CanvasJS.Chart("chartContainer",
      {
        zoomEnabled: true,
        toolTip:{   
          content: "Time: {x}, {legendText}: {y}"      
        },
        title:{
          text: "History"
        },
        axisX:{
          title: "timeline",
          gridThickness: 1
        },
        axisY: {
          title: "Temperature *C"
        },
        data: [
        {
          type: "stepArea",
          showInLegend: true,
          legendText: "Activated",
          toolTipContent: "Time {x}",
          dataPoints: [
            <?php
              for ($i = 0; $i < $length; $i++) {
                echo "{ x: new Date(\"" .  $timeArray[$i] . "\"), y: " . 10*$statusArray[$i] . " },";
              }
            ?>
          ]  
        },
        {
          type: "line",
          showInLegend: true,
          legendText: "InTemperature",
          dataPoints: [
            <?php
              for ($i = 0; $i < $length; $i++) {
                echo "{ x: new Date(\"" .  $timeArray[$i] . "\"), y: " . $tempInArray[$i] . " },";
              }
            ?>
          ]
        },
        {
          type: "line",
          showInLegend: true,
          legendText: "OutTemperature",
          dataPoints: [
            <?php 
              for ($i = 0; $i < $length; $i++) {
                echo "{ x: new Date(\"" .  $timeArray[$i] . "\"), y: " . $tempOutArray[$i] . " },";
              }
            ?>
          ]
        }
        ]
      });

      chart.render();
    }
    </script>
  </body>
</html>


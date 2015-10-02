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
    
    <?php
      //Display the hours that have been set until now.
      try {
        $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM `thresholds` ORDER BY `day`, `hour`, `minute`;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        echo "<table>";
        echo "<tr> <td>Day</td> <td>Hour</td> <td>Minute</td> <td>LowerIn</td> <td>UpperIn</td> <td>LowerOut</td> <td>UpperOut</td> </tr>"
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $day    = $fow['day'];
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
          echo "</tr>";
        }
        echo "</table>";
      } catch (PDOException $e) {
        echo 'Error in sql: ' . $e->getMessage();
      }
      //Close connection.
      $dbh = null;
    ?>
  </body>
</html>

            <tr>
              <td>UserId: </td>
              <td><input type="text" name="userU"></td>
            </tr>
            <tr>
              <td>Password: </td>
              <td><input type="text" name="passU"></td>
            </tr>
            <tr>
              <td> <input type="submit" name="submit1" value="Login"/> </td>
              <td></td>
            </tr>
          

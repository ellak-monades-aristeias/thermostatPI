<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Change Max Consumption</title>
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

    <?php
      //If here, the username and pass are correct.
      if (isset($_POST["period"]) && isset($_POST["maxConsumption"]) ) {
        $period         = $_POST["period"];
        $maxConsumption = $_POST["maxConsumption"];
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $sql = "UPDATE `config` SET `value`=:period WHERE `key`='period'";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':period', $period);
          $stmt->execute();

          $sql = "UPDATE `config` SET `value`=:maxConsumption WHERE `key`='maxConsumption'";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':maxConsumption', $maxConsumption);
          $stmt->execute();
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
    ?>
        <h3> Max consumption has changed. <h3>
        <form method="POST" action="thermostatConfiguration.php">
          <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
          <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
          <input type="submit" name="submit" value="Go to configuration page."/>
        </form>
    <?php
      } else {
        $period         = NULL;
        $maxConsumption = NULL;
        //Check if they are correct.
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $sql = "SELECT `c1`.`value` AS `period`, `c2`.`value` AS `maxConsumption` FROM `config` AS `c1`, `config` AS `c2` WHERE `c1`.`key`='period' AND `c2`.`key`='maxConsumption'";
          $stmt = $db->prepare($sql);
          $stmt->execute();
          if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $period         = $row['period'];
            $maxConsumption = $row['maxConsumption'];
          }
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
    ?>

        <form method="POST" action="thermostatConfiguration.php">
          <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
          <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
          <input type="submit" name="submit" value="Go to configuration page."/>
        </form>
        
        <form method="POST" action="changeMaxConsumption.php">
          <table>
            <tr>
              <td>Period: </td>
              <td>
                <select name="period">
                  <option value="day"   <?php if($period=="day")   echo "selected"; ?> >Per Day</option>
                  <option value="week"  <?php if($period=="week")  echo "selected"; ?> >Per Week</option>
                  <option value="month" <?php if($period=="month") echo "selected"; ?> >Per Month</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Max Consumption (in hours): </td>
              <td><input type="text" name="maxConsumption" value="<?php echo $maxConsumption; ?>"></td>
            </tr>
            <tr>
              <td> <input type="submit" name="submit" value="Save"/> </td>
              <td></td>
            </tr>
          </table>
          <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
          <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
        </form>
    <?php
      }
    ?>
  </body>
</html>


<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Thermostat configuration</title>
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
        <form method="POST" action="">
          <table>
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
          </table>
        </FORM>
    <?php
        echo '</body></html>';
        return;
      }
    ?>

    <?php
      //If here, the username and pass are correct.
    ?>
    <form method="POST" action="changePassword.php">
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
      <input type="submit" name="submit" value="Change Password"/>
    </form>

    <form method="POST" action="viewThresholds.php">
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
      <input type="submit" name="submit" value="View Thresholds"/>
    </form>

    <form method="POST" action="viewConsumption.php">
      <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
      <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
      <input type="submit" name="submit" value="View Consumption"/>
    </form>
  </body>
</html>


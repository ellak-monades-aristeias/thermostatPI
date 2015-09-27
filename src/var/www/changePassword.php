<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Change Password</title>
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
      if (isset($_POST["userNew"]) && isset($_POST["passNew"]) ) {
        $userNew  = $_POST["userNew"];
        $passNew  = $_POST["passNew"];
        try {
          $db = new PDO('mysql:dbname='.$dbname.';host='.$host.';port='.$port, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $sql = "UPDATE `config` SET `value`=:userNew WHERE `key`='username'";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':userNew', $userNew);
          $stmt->execute();

          $sql = "UPDATE `config` SET `value`=:passNew WHERE `key`='password'";
          $stmt = $db->prepare($sql);
          $stmt->bindParam(':passNew', $passNew);
          $stmt->execute();
        } catch (PDOException $e) {
          echo 'Error in sql: ' . $e->getMessage();
        }
        //Close connection.
        $dbh = null;
    ?>
        <h3> User name and password Changed. <h3>
        <form method="POST" action="thermostatConfiguration.php">
          <input type="hidden" name="userU" value="<?php echo $userNew; ?>" />
          <input type="hidden" name="passU" value="<?php echo $passNew; ?>" />
          <input type="submit" name="submit" value="Go to configuration page."/>
        </form>
    <?php
      } else {
    ?>
        <form method="POST" action="">
          <table>
            <tr> <td>New username: </td> <td><input type="text" name="userNew" value="<?php echo $userU; ?>"></td> </tr>
            <tr> <td>New Password: </td> <td><input type="text" name="passNew" value="<?php echo $passU; ?>"></td> </tr>
            <tr> <td> <input type="submit" name="submit" value="Save"/> </td> <td></td></tr>
          </table>
          <input type="hidden" name="userU" value="<?php echo $userU; ?>" />
          <input type="hidden" name="passU" value="<?php echo $passU; ?>" />
        </form>
    <?php
      }
    ?>
  </body>
</html>


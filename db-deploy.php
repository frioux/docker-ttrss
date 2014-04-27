<?php

  # why doesn't this work?
  # require "../config.php";
  $DB_TYPE = "mysql";
  $DB_HOST = getenv("MYSQL_PORT_3306_TCP_ADDR");
  $DB_USER = "docker";
  $DB_NAME = "docker";
  $DB_PASS = "docker";
  $DB_PORT =  getenv("MYSQL_PORT_3306_TCP_PORT");

  $link = db_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_TYPE, $DB_PORT);

  print "<h2>initializing database...</h2>";

  $lines = explode(";", preg_replace("/[\r\n]/", "", file_get_contents("../schema/ttrss_schema_$DB_TYPE.sql")));

  foreach ($lines as $line) {
     if (strpos($line, "--") !== 0 && $line) {
        db_query($link, $line, 'mysql');
     }
  }

  print "database initialization completed.";

  function db_query($link, $query, $type, $die_on_error = true) {
     if ($type == "pgsql") {
        $result = pg_query($link, $query);
        if (!$result) {
           $query = htmlspecialchars($query); // just in case
           if ($die_on_error) {
              die("Query <i>$query</i> failed [$result]: " . ($link ? pg_last_error($link) : "No connection"));
           }
        }
        return $result;
     } else if ($type == "mysql") {

        if (function_exists("mysqli_connect")) {
           $result = mysqli_query($link, $query);
        } else {
           $result = mysql_query($query, $link);
        }
        if (!$result) {
           $query = htmlspecialchars($query);
           if ($die_on_error) {
              die("Query <i>$query</i> failed: " . ($link ? function_exists("mysqli_connect") ? mysqli_error($link) : mysql_error($link) : "No connection"));
           }
        }
        return $result;
     }
  }

function db_connect($host, $user, $pass, $db, $type, $port = false) {
   if ($type == "pgsql") {

      $string = "dbname=$db user=$user";

      if ($pass) {
         $string .= " password=$pass";
      }

      if ($host) {
         $string .= " host=$host";
      }

      if ($port) {
         $string = "$string port=" . $port;
      }

      $link = pg_connect($string);

      return $link;

   } else if ($type == "mysql") {
      if (function_exists("mysqli_connect")) {
         if ($port)
            return mysqli_connect($host, $user, $pass, $db, $port);
         else
            return mysqli_connect($host, $user, $pass, $db);

      } else {
         $link = mysql_connect($host, $user, $pass);
         if ($link) {
            $result = mysql_select_db($db, $link);
            if ($result) return $link;
         }
      }
   }
}

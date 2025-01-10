<?php
  // pm-admin
  DEFINE("DB_ADMIN_USER", "pm-admin");
  DEFINE("DB_ADMIN_PASSWORD", "Jos28yBOsLtAJRD1");
  DEFINE("DB_ADMIN_HOST", "localhost");

  // pm-user
  DEFINE("DB_USER", "pm-user");
  DEFINE("DB_PASSWORD", "xF7BWP1qxRUUemaq");
  DEFINE("DB_HOST", "localhost");

  function dbcConnectAsAdmin()
  {
      return mysqli_connect(DB_ADMIN_HOST, DB_ADMIN_USER, DB_ADMIN_PASSWORD);
  }

  function dbcConnectAsViewer()
  {
      return mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
  }

// STRICT
"use strict";

var gulp = require("gulp");
var cssnano = require("gulp-cssnano");
var rename = require("gulp-rename");
// var sass = require("gulp-sass");
var util = require("gulp-util");

var rsync = require("rsyncwrapper").rsync;
var gulpsync = require("gulp-sync")(gulp);

/**
 * ============================================================================
 * watchtheavs.com
 * ============================================================================
 */

/**
 * Deploy code to watchtheavs.com
 */
gulp.task("deploy-for-html", () => {
  rsync(
    {
      ssh: true,
      src: "./html/",
      dest: "root@159.65.221.48:/var/www/watchtheavs.com",
      recursive: true,
      syncDest: true,
      args: ["--verbose"],
    },
    (error, stdout, stderr, cmd) => {
      util.log(stdout);
    }
  );
});

/**
 * Compile, compress, and deploy to watchtheavs.com
 */
gulp.task("deploy-html", gulpsync.sync([["deploy-for-html"]]));

/**
 * ============================================================================
 * All
 * ============================================================================
 */

/**
 * Deploy logocdn.com, i.logocdn.com, watchtheavs.com
 */
gulp.task("deploy", gulpsync.sync([["deploy-html"]]));

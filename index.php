<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<?php
require __DIR__ . '/vendor/autoload.php';

// read language json
$language = "zh";
$languages = json_decode(file_get_contents(__DIR__ . "/languages/{$language}.json"), true);

// hide depracted error
error_reporting(E_ALL ^ E_DEPRECATED);

use YouTube\Exception\YouTubeException;
use YouTube\YouTubeDownloader;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

$youtube = new YouTubeDownloader();
session_start();
$error = null;
if (isset($_POST["url"])) {
  try {
    $downloadOptions = $youtube->getDownloadLinks($_POST["url"]);
    $info = $downloadOptions->getInfo();


  } catch (YouTubeException $e) {
    $error = $e;
  }
}
?>
<!DOCTYPE html>
<head>
  <!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

  <!-- Compiled and minified JavaScript -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<body>
<nav class="light-red lighten-1" role="navigation">
  <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">ytdl-php</a>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
  </div>
</nav>
<div class="section no-pad-bot" id="index-banner">
  <div class="container">
    <br><br>
    <h1 class="header center red-text"><?php echo $languages["ytdl-php"] ?></h1>
    <form method="post" action="./index.php">

      <div class="row center">
        <h5 class="header col s12 light"><?php echo $languages["Download Youtube Video / Music"] ?></h5>


        <div class="input-field col s12">
          <input value="<?php
          if (isset($_POST["url"])) {
            echo $_POST["url"];
          }
          ?>" id="url" type="text" class="validate" name="url"
                 placeholder="https://www.youtube.com/watch?v=dDxYrwWzP2k">
          <label class="active" for="url">URL</label>
        </div>
      </div>
      <div class="row center">

        <?php
        if (isset($info)) {
          ?>
          <div class="card " style="text-align: left; ">

            <div class="card-content">
              <span class="card-title"><?php echo $info->getTitle() ?></span>
              <p><?php echo $info->getShortDescription() ?></p>
            </div>
            <div class="card-action">
              <?php
              $formats = $downloadOptions->getAllFormats();
              ?>
              <div class="row">
                <div class="col s12">
                  <ul class="tabs" id="tabs">
                    <li class="tab col s3 "><a href="#va">  <i class="material-icons">library_music</i>
                      </a></li>
                    <li class="tab col s3"><a href="#vOnly"><i class="material-icons">movie</i></a></li>
                    <li class="tab col s3"><a href="#aOnly"><i class="material-icons">album</i></a></li>
                  </ul>
                </div>
                <div id="va" class="col s12">
                  <table class="striped">
                    <thead>
                    <tr>
                      <th>Format</th>
                      <th>Size</th>
                      <th>Download</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $combined = $downloadOptions->getAllFormats();
                    foreach ($combined as $key => $format) {
                      if (empty($format->audioQuality) or empty($format->qualityLabel)) {
                        continue;
                      }
                      ?>
                      <tr>
                        <td><?php echo $format->qualityLabel ?></td>
                        <td><?php echo $format->contentLength / 1024 ?> KB</td>
                        <td><a href="./stream.php?url=<?php echo urlencode($_POST["url"]) ?>&index=<?php echo $key ?>"
                               target="_blank">Download</a></td>
                      </tr>
                      <?php
                    }
                    ?>
                    </tbody>
                  </table>
                </div>
                <div id="vOnly" class="col s12">
                  <table class="striped">
                    <thead>
                    <tr>
                      <th>Format</th>
                      <th>Type</th>
                      <th>Download</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($formats as $key => $format) {
                      if ($format->audioQuality) {
                        continue;
                      }
                      ?>
                      <tr>

                        <td><?php echo $format->qualityLabel ?></td>

                        <td><?php
                          if (strpos($format->mimeType, "mp4") !== false) {
                            echo "MP4";
                          } else {
                            echo "WebM";
                          }
                          ?></td>

                        <td><a href="./stream.php?url=<?php echo urlencode($_POST["url"]) ?>&index=<?php echo $key ?>">Download</a></td>
                      </tr>
                      <?php
                    }
                    ?>
                    </tbody>
                  </table>
                </div>
                <div id="aOnly" class="col s12">
                  <table class="striped">
                    <thead>
                    <tr>
                      <th>Quality</th>
                      <th>Type</th>
                      <th>Download</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($formats as $key => $format) {
                      if ($format->qualityLabel or empty($format->audioQuality)) {
                        continue;
                      }
                      ?>
                      <tr>
                        <td><?php echo $languages[$format->audioQuality]  ?> </td>
                        <td><?php
                          if (strpos($format->mimeType, "audio/mp4") !== false) {
                            echo "MP4";
                          } else {
                            echo "WebM";
                          }
                          ?></td>
                        <td><a href="./stream.php?url=<?php echo urlencode($_POST["url"]) ?>&index=<?php echo $key ?>">Download</a></td>
                      </tr>
                      <?php
                    }
                    ?>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
          <?php
        }
        ?>

        <button type="submit"
                class="btn-large waves-effect waves-light red"><?php
          if ($error) {
            echo $languages["Error"];
          } else {
            echo $languages["Download"];
          }?></button>
      </div>
    </form>
    <br><br>

  </div>
</div>


<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script>
  var instance = M.Tabs.init(document.getElementsByClassName("tabs"));

  // Or with jQuery

  $(document).ready(function () {
    $('.tabs').tabs();
  });
</script>
<style>
  .tab a[href] {
    color: #000 !important;
  }

</style>
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
</body>
</body>

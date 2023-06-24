<?php

require('./vendor/autoload.php');

$url = $_GET['url'] ?? null;

if (!isset($_GET['url'])) {
  die("No url provided");
}
// hide depracted error
error_reporting(E_ALL ^ E_DEPRECATED);




$youtube = new \YouTube\YouTubeStreamer();
$youtubeClient = new \YouTube\YouTubeDownloader();
$download = $youtubeClient->getDownloadLinks($url);
$videoFormats = $download->getAllFormats();
$youtube->stream($videoFormats[$_GET["index"] ?? 0]->url);

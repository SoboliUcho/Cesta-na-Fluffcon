<?php
function make_header($title, $language, $page)
{
    $locale = "cs_CZ";
    if ($language == "en") {
        $locale = "en_US";
    }

    $info = Db::queryOne("SELECT * FROM menu WHERE link like '$page'");
    // print_r($info);
    $description = !empty($info['Description_'.$language])? $info['Description_'.$language] : "" ;

    echo "<!DOCTYPE html>
    <html lang='$language'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>

        <meta name='author' content='Sobolí ucho'>
        <meta name='description' content='$description'>
        <meta name='keywords' content='Sobolí ucho, Sobolí, ucho, sobol, twitch, discord, instagram, stream, streamer, streamování, streamovani, streamovat, streamovani, streamování, streamerka, web, stránky, webstránky, webstranky, webstránka, webstranka, webdesign, webdesigner, design, designer, grafika, grafik, grafik, grafický, graficky, graficka, graficky, graficka'>
        <meta name='robots' content='index, follow'>
        
        <meta property='og:type' content='website'>
        <meta property='og:locale' content='$locale'>
        <meta property='og:title' content='$title'>
        <meta property='og:description' content='$description'>
        <meta property='og:url' content='https://soboliucho.cz/$page'>
        <meta property='og:image' content='https://soboliucho.cz/img/MLODY-SOBOL.png'>

        <link rel='stylesheet'
        href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css'>
        <link rel='icon' href='img/icon.ico'>
        <link rel='stylesheet' href='css/$page.css'>
        <script src='js/$page.js'></script>
        <title>Cesta na FluffCon</title>

    ";
    
}
function get_lang()
{
    $langer = "cs";
    
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
        return $langer;
    }

    $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

    foreach ($languages as $lang) {
        if (strpos($lang, 'cs') === 0) {
            $langer = "cs";
            break;
        }
        if (strpos($lang, 'en') === 0) {
            $langer = "en";
            break;
        }
    }

    if (isset($_GET['en'])) {
        $langer = "en";
    }
    if (isset($_GET['cs']) ) {
        $langer = "cs";
    }
    return $langer;
}
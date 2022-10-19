<?php
require 'vendor/autoload.php';

use \Symfony\Component\Panther\Client;

$client = Client::createChromeClient();

$client->get('https://quotes.toscrape.com/js/');


$file = fopen("quotes.csv", "a");

while (true) {
    $crawler = $client->waitFor('.quote');
    $crawler->filter('.quote')->each(function ($node) use ($file) {
        $author = $node->filter('.author')->text();
        $quote = $node->filter('.text')->text();
        fputcsv($file, [$author, $quote]);
    });
    try {
        $client->clickLink('Next');
    } catch (Exception) {
        // end of all pages
        break;
    }
}
fclose($file);

<!-- Prints all books using Goutte. -->
<?php
require 'vendor/autoload.php';

use Goutte\Client;


function scrapePage($url, $client, $file)
{
    $crawler = $client->request('GET', $url);
    $crawler->filter('.product_pod')->each(function ($node) use ($file) {
        $title = $node->filter('.image_container img')->attr('alt');
        $price = $node->filter('.price_color')->text();
        fputcsv($file, [$title, $price]);
    });
    try {
        $next_page = $crawler->filter('.next > a')->attr('href');
    } catch (InvalidArgumentException) { //Next page not found
        return null;
    }
    return "https://books.toscrape.com/catalogue/" . $next_page;
}

$client = new Client();
$file = fopen("books.csv", "a");
$nextUrl = "https://books.toscrape.com/catalogue/page-1.html";

while ($nextUrl) {
    echo "<h2>" . $nextUrl . "</h2>" . PHP_EOL;
    $nextUrl = scrapePage($nextUrl, $client, $file);
}
fclose($file);

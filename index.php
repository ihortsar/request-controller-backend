<?php
require 'includes/autoload.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $IATA = new IATAAnaliserBooking();
    $IATAHistory = new IATARequestHistory();
    $priceAnalyserController = new IATAAnaliserController($IATA, $IATAHistory);
    $priceAnalyserController->handlePriceRequest();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $IATA = new IATARequestHistory();
    $IATAHistoryController = new IATARequestHistoryController($IATA);
    $IATAHistoryController->fetchHistoryRequests();
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $IATA = new IATARequestHistory();
    $IATAHistoryController = new IATARequestHistoryController($IATA);
    $IATAHistoryController->clearHistoryRequests();
}

<?php

class IATAAnaliserController
{
    public $IATA;
    public $conn;
    public $IATARequestHistory;

    public function __construct(IATAAnaliserInterface $IATAAnaliserInterface, IATAHistoryInterface $historyInterface)
    {
        $this->conn = require './includes/db.php';
        $this->IATA = $IATAAnaliserInterface;
        $this->IATARequestHistory = $historyInterface;
    }


    /**
     * Handles the request for IATA price analysis.
     * Sorts the IATA codes using the IATAAnaliserInterface, sends an email report with the results,
     * and stores the request statistics in the IATA request history.
     * @return void
     */
    public function handlePriceRequest()
    {
        $IATARequests = $this->IATA->sortIATA();
        $mailer = new SendMail($IATARequests);
        $mailResult = $mailer->sendEmail();
        echo json_encode([
            'data' => $IATARequests,
            'message' => $mailResult['message']
        ]);
        $this->IATARequestHistory->addStatistics($IATARequests);
    }
}

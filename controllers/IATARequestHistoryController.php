<?php
class IATARequestHistoryController
{

    public $IATAHistory;

    public function __construct(IATAHistoryInterface $IATAHistoryInterface)
    {
        $this->IATAHistory = $IATAHistoryInterface;
    }


    /**
     * Handles the incoming request for IATA request history statistics,
     * reads the incoming JSON payload, validates the time period specified in the request, 
     * retrieves the corresponding statistics and returns the data as a JSON response.
     * @return void
     * @throws Exception If an invalid time period is provided or an error occurs while retrieving the statistics.
     */
    public function fetchHistoryRequests()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['time_period'])) {
            $validPeriods = ['DAY', 'WEEK', 'MONTH', 'YEAR'];

            if (in_array($data['time_period'], $validPeriods)) {
                $statistics = $this->IATAHistory->getStatistics($data['time_period']);
                echo json_encode($statistics);
            } else {
                throw new Exception('Invalid time period');
            }
        } else {
            echo json_encode(['error' => "'time_period' is missing"]);
            http_response_code(400);
        }
    }


    public function clearHistoryRequests()
    {
        $this->IATAHistory->clearHistory();
    }
}

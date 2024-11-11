<?php

class IATARequestHistory implements IATAHistoryInterface
{
    private $conn;
    public function __construct()
    {
        $this->conn = require './includes/db.php';
    }

    /**
     * Adds statistics to the IATA request history.
     * @param array $request_info Array of IATA request data where each element includes 'iata_code' and 'count'.
     * @throws Exception if a database error occurs during the insertion.
     * @return void
     */
    public function addStatistics($request_info)
    {

        $this->conn->beginTransaction();
        $stamp = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO iata_requests_history ( `time_of_stamp`, `IATA`, `request_count`) 
                VALUES (:time_of_stamp,:iata,:request_count)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':time_of_stamp', $stamp);
        try {
            foreach ($request_info as $request) {
                $stmt->bindValue(':iata', $request['iata_code']);
                $stmt->bindValue(':request_count', $request['count']);
                $stmt->execute();
            }
            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error Processing Request");
        }
    }


    /**
     * Retrieves IATA request statistics within a specified time period.
     * @param string $periodTime The period for which to retrieve statistics ('DAY', 'WEEK', 'MONTH', 'YEAR').
     * @throws Exception from validateTimePeriod($periodTime) if an invalid period is provided or a database error occurs during retrieval.
     * @return array An array of statistics records, where each record contains details of the IATA request history.
     */
    public function getStatistics($periodTime)
    {
        try {
            $this->validateTimePeriod($periodTime);
            $sql = "SELECT * FROM iata_requests_history WHERE time_of_stamp >= NOW() - INTERVAL 1 $periodTime";
            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching statistics.");
        }
    }


    private function validateTimePeriod($periodTime)
    {
        $validPeriods = ['DAY', 'WEEK', 'MONTH', 'YEAR'];
        if (!in_array($periodTime, $validPeriods)) {
            throw new Exception("Invalid period.");
        }
    }


    public function clearHistory()
    {
        $sql = "DELETE FROM iata_requests_history WHERE time_of_stamp <= NOW() - INTERVAL 1 YEAR";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute()) {
            $deletedRows = $stmt->rowCount();
            if ($deletedRows > 0) {
                echo json_encode(['message' => 'History cleared successfully.', 'deleted_rows' => $deletedRows]);
            } else {
                echo json_encode(['message' => 'No records older than 1 year were found.']);
            }
        } else {
            throw new Exception("Error Processing Request");
        }
    }
}

<?php

class IATAAnaliserBooking implements IATAAnaliserInterface
{
    private $conn;
    public static $IATARequests;

    public function __construct()
    {
        $this->conn = require './includes/db.php';
    }


    /**
     * Retrieves and sorts IATA codes based on bookings' requests' data.
     * aggregates IATA codes from booking requests made in the last 24 hours,
     * counting occurrences of each IATA code. It considers both departure ('von') and
     * destination ('nach') fields, extracts the IATA code from within parentheses, and
     * validates that the code is in the correct format (three uppercase letters).
     * 
     * @return array Returns an array of IATA codes with their request counts, sorted in descending order by count.
     */
    public function sortIATA()
    {
        $sql = "SELECT iata_code, COUNT(*) AS count
        FROM (
            SELECT 
                SUBSTRING_INDEX(SUBSTRING_INDEX(von, '(', -1), ')', 1) AS iata_code
            FROM 
                hp_bookingcom_pricerequests_pg_new
            WHERE 
                stamp >= NOW() - INTERVAL 1 DAY
            AND von LIKE '%(%)%'
        
            UNION ALL

            SELECT 
                SUBSTRING_INDEX(SUBSTRING_INDEX(nach, '(', -1), ')', 1) AS iata_code
            FROM 
                hp_bookingcom_pricerequests_pg_new
            WHERE 
                stamp >= NOW() - INTERVAL 1 DAY
            AND nach LIKE '%(%)%'
    ) AS combined_results
    WHERE 
        iata_code IS NOT NULL
        AND LENGTH(iata_code) = 3
        AND iata_code REGEXP '^[A-Z]{3}$'
    GROUP BY 
        iata_code
    ORDER BY 
        count DESC
";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute()) {
            return  self::$IATARequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

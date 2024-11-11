<?php
interface IATAHistoryInterface
{
    public function getStatistics(string $periodTime);
    public function addStatistics(array $IATARequests);
    public function clearHistory();
}

<?php

class Query_CorpStations {

    function __construct($corpID) {
        $this->corpID = $corpID;
    }
  
    /*
        Execute query to obtain all Stations for a corp
        
        @return Returns array of rows
    */
    function execute() {
        $result = Db::q("
                SELECT a.`stationName`, b.`security` 
                FROM `staStations` a 
                INNER JOIN `mapSolarSystems` b ON (b.`solarSystemID` = a.`solarSystemID`) 
                WHERE a.`corporationID` = :corpID
                ORDER BY a.`stationName` ASC", 
                array(':corpID'=>$this->corpID));
        
        return $result;
    }

}
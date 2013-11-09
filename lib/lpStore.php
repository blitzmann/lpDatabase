<?php

class lpStore {
    
    public $offers;
    public $corpID;
    
    function __construct($corpID, $emdr, $DB) {
        $this->DB     = $DB;
        $this->emdr   = $emdr;
        $this->corpID = $corpID;
        
        # set required item details for each offer
        # @todo: set to false if no items.
        $reqCont = array();
        foreach ($DB->qa($this->sql['reqItems'], array($corpID)) AS $item) {  
            $reqCont[$item['offerID']][] = $item; 
        }

        foreach ($DB->qa($this->sql['offers'], array($corpID)) AS $offer) {
            $this->offers[$offer['offerID']] = new lpOffer($offer['offerID'], $this->emdr, $this->DB);
            $this->offers[$offer['offerID']]->offerDetails = $offer;
            if (isset($reqCont[$offer['offerID']])) {
                $this->offers[$offer['offerID']]->reqDetails = $reqCont[$offer['offerID']]; }
            else {
                $this->offers[$offer['offerID']]->reqDetails = array(); }
            
            //$this->offers[$offer['offerID']]->calc('sell');
        }
    }
    
    private $sql = array(
        'reqItems'=>'
            SELECT      a.typeID, a.quantity, b.typeName, a.offerID
            FROM        lpOfferRequirements a
            INNER JOIN  invTypes b ON (b.typeID = a.typeID)
            INNER JOIN  lpStore c ON (a.offerID = c.offerID)
            WHERE       c.corporationID = ?',
        'offers'=>'
            SELECT `lpStore`.* , `invTypes`.`typeName`, `lpOffers`.*
            FROM lpStore
            NATURAL JOIN lpOffers
            NATURAL JOIN invTypes
            WHERE `lpStore`.`corporationID` = ?
            ORDER BY 
                `lpOffers`.`lpCost`, 
                `lpOffers`.iskCost, 
                `invTypes`.`typeName`'
    );
}

?>
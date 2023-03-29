<?php

class CRM_Agendabe_Generator {
  public static function print() {
    print "<events>";
    self::printAllEvents();
    print "</events>";
  }

  private static function printAllEvents() {
    $dao = self::getAgendaBeData();
    while ($dao->fetch()) {
      print "<event>";
      self::printEvent($dao);
      print "</event>";
    }
  }

  private static function printEvent($dao) {
    self::printEventId($dao);
    self::printEventCategory($dao);
    self::printEventDetail($dao);
    self::printEventDates($dao);
    self::printEventDateStartDateEnd($dao);
    self::printEventMedias($dao);
    self::printEventOrganizer($dao);
    self::printEventPlace($dao);
    self::printEventOnline($dao);
    self::printEventPrices($dao);
    self::printEventTargetAudience($dao);
    self::printEventTaalIcon($dao);
    self::printEventLanguages($dao);
  }

  private static function printEventId($dao) {
    print "<id>$dao->id</id>";
  }

  private static function printEventCategory($dao) {
    print "<category>$dao->label</category>";
  }

  private static function printEventDetail($dao) {
    print "<detail language='NL'>";
    print str_replace("&", "&amp;","<title>$dao->title</title>");

    if ($dao->is_online_registration == 1) {
      print "<url>https://icontact.muntpunt.be/civicrm/event/register?reset=1&amp;id=$dao->id</url>";
    }
    elseif ($dao->eventlink != "") {
      print str_replace("&", "&amp;","<url>$dao->eventlink</url>");
    }

    print str_replace("&", "&amp;","<shortdescription>$dao->summary</shortdescription>");
    print "<longdescription>";
    print str_replace("&", "&amp;",html_entity_decode(htmlspecialchars_decode(strip_tags($dao->description), ENT_QUOTES)));
    print "</longdescription>";
    print "</detail>";
  }

  private static function printEventDates($dao) {
    print "<dates>";

    print "<date>";
    print "<day>$dao->day</day>";
    print "<hourstart>$dao->hourstart</hourstart>";
    print "<hourend>$dao->hourend</hourend>";
    print "</date>";

    print "</dates>";
  }

  private static function printEventDateStartDateEnd($dao) {
    print "<datestart>$dao->day</datestart>";
    print "<dateend>$dao->dayend</dateend>";
  }

  private static function printEventMedias($dao) {
    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $dao->description, $img);
    if (!empty($img[1])) {
      print "<medias>";
      print "<media type='photo'>";
      print "<url>" . $img[1] . "</url>";
      print "</media>";
      print "</medias>";
    }
  }

  private static function printEventOrganizer($dao) {
    print "<organizer>";

    if (empty($dao->OrganizerID)) {
      print "<id>3</id>";
      print "<name>Muntpunt</name>";
      print "<street>Munt 6</street>";
      print "<zip>1000</zip>";
      print "<city>Brussel</city>";
    }
    else {
      print "<id>$dao->OrganizerID</id>";
      print "<name>$dao->OrganizerName</name>";
      print "<street>$dao->OrganizerStreet</street>";
      print "<zip>$dao->OrganizerZip</zip>";
      print "<city>$dao->OrganizerCity</city>";
    }

    print "</organizer>";
  }

  private static function printEventPlace($dao) {
 $zalen = substr(preg_replace("/\x01/",", ",$dao->muntpunt_zalen),1,-2);
    $plaatsnaam = $dao->PlaceName;
    print "<place>";
    print "<id>$dao->PlaceID</id>";
    if(!empty($zalen)){
        print "<name>$plaatsnaam, $zalen</name>";
    }else{
        print "<name>$plaatsnaam</name>";
    }
    print "<street>$dao->PlaceStreet</street>";
    print "<zip>$dao->PlaceZip</zip>";
    print "<city>$dao->PlaceCity</city>";
    print "</place>";

  }

  private static function printEventOnline($dao) {
    if (strpos($dao->PlaceName, 'online') !== false) {
      print "<online>1</online>";
    }
    else {
      print "<online>0</online>";
    }
  }

  private static function printEventPrices($dao) {
    if ($dao->pricelabel) {
      $priceAmounts = explode(',',$dao->amount);
      $index = 0;

      print "<prices>";
      foreach (explode(",", $dao->pricelabel) as $pricetype){
        print "<price type='$pricetype'>$priceAmounts[$index]</price>";
        $index++;
      }
      print "</prices>";
    }
    else {
      print "<prices><price>0</price></prices>";
    }
  }

  private static function printEventTargetAudience($dao) {
    $targetAudiences = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->doelgroep);
    $targetAges = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->leeftijd_41);

    print "<target_audience>";
    foreach ($targetAudiences as $targetAudience) {

	      if (!empty($targetAudience) && $targetAudience != 'Kinderen') {
        print "<type>$targetAudience</type>";
      }
      if($targetAudience == 'Kinderen' && empty($dao->leeftijd_41)){
	print "<type>Kinderen vanaf peuterleeftijd (0+)</type>";
	print "<type>Kinderen vanaf peuterleeftijd (3+)</type>";
	print "<type>Kinderen vanaf lagerschoolleeftijd (6+)</type>";
	print "<type>Kinderen vanaf 9 jaar (9+)</type>";
	print "<type>Jongeren vanaf 12 jaar (12+)</type>";
	print "<type>Jongeren vanaf 15 jaar (15+)</type>";
      }
    }

    foreach ($targetAges as $targetAge) {
      if (!empty($targetAge)) {
        if ($targetAge == 'Volwassen' && in_array('Volwassen', $targetAudiences)) {
          // avoid double entry
        }
        else {
          print "<type>$targetAge</type>";
        }
      }
    }

    print "</target_audience>";
  }

  private static function printEventTaalIcon($dao) {
    $targetLanguageLevels = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->taalniveau_42);

    foreach ($targetLanguageLevels as $targetLanguageLevel) {
      if (!empty($targetLanguageLevel)) {
        if ($targetLanguageLevel == "Taalniveau één") {
          print "<taalicon>1</taalicon>";
          print "<taalicondescription>Je begrijpt of spreekt nog niet veel Nederlands.</taalicondescription>";
        }
        elseif ($targetLanguageLevel == "Taalniveau twee") {
          print "<taalicon>2</taalicon>";
          print "<taalicondescription>Je begrijpt al een beetje Nederlands maar je spreekt het nog niet zo goed.</taalicondescription>";
        }
        elseif ($targetLanguageLevel == "Taalniveau drie") {
          print "<taalicon>3</taalicon>";
          print "<taalicondescription>Je begrijpt vrij veel Nederlands en kan ook iets vertellen.</taalicondescription>";
        }
        elseif ($targetLanguageLevel == "Taalniveau vier") {
          print "<taalicon>4</taalicon>";
          print "<taalicondescription>Je begrijpt veel Nederlands en spreekt het goed.</taalicondescription>";
        }
      }
    }
  }

  private static function printEventLanguages($dao) {
    $languages = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dao->taal);

    print "<languages>";
    foreach ($languages as $language) {
      if (!empty($language)) {
        print "<language>$language</language>";
      }
    }
    print "</languages>";
  }

  private static function getAgendaBeData() {
    $eventStatusCommunicatieOK = 5;
    $optionGroupEventType = 15;
    $participantRoleOrganizer = 5;

    $query = "
      SELECT
        a.id,
        counttable.numberofrecords as numberofrecords,
        a.title,
        DATE(a.start_date) AS 'day',
        DATE(a.end_date) AS 'dayend',
        TIME_FORMAT(a.start_date, '%H:%i') AS hourstart,
        TIME_FORMAT(a.end_date, '%H:%i') AS hourend,
        b.parent_id,
        c.label,
        d.muntpunt_zalen,
        d.evenement_link AS eventlink,
        d.doelgroep,
        d.leeftijd_41,
        d.taalniveau_42,
        organizer.id AS OrganizerID,
        organizer.organization_name AS OrganizerName,
        organizeraddress.street_address AS OrganizerStreet,
        organizeraddress.postal_code AS OrganizerZip,
        organizeraddress.city AS OrganizerCity,
        place.id AS PlaceID,
        place.name AS PlaceName,
        place.street_address AS PlaceStreet,
        place.postal_code AS PlaceZip,
        place.city AS PlaceCity,
        d.taal,
        e.id AS locblockid,
        e.address_id,
        a.summary,
        a.description,
        a.is_online_registration,
        pricetable.pricelabel,
        pricetable.amount
      FROM
        civicrm_event a
      LEFT JOIN civicrm_recurring_entity b ON
        a.id = b.entity_id
      LEFT JOIN civicrm_option_value c ON
        a.event_type_id = c.value
      LEFT JOIN civicrm_value_extra_evenement_info d ON
        a.id = d.entity_id
      LEFT JOIN civicrm_participant organizer_participant ON
        a.id = organizer_participant.event_id and organizer_participant.role_id like '%$participantRoleOrganizer%'
      LEFT JOIN civicrm_contact organizer ON
        organizer.id = organizer_participant.contact_id
      LEFT JOIN civicrm_address organizeraddress ON
        organizer.id = organizeraddress.contact_id and organizeraddress.is_primary = 1
      LEFT JOIN civicrm_loc_block e ON
        a.loc_block_id = e.id
      LEFT JOIN civicrm_address place ON
        e.address_id = place.id
      LEFT JOIN (
        select
          h.entity_id,
          i.id,
          GROUP_CONCAT(j.label SEPARATOR ',') AS pricelabel,
          GROUP_CONCAT(round(j.amount, 2) SEPARATOR ',') AS amount
        from
          civicrm_price_set_entity h
        LEFT JOIN civicrm_price_field i on
          h.price_set_id = i.price_set_id
        LEFT JOIN civicrm_price_field_value j on
          j.price_field_id = i.id
        group by
          h.entity_id) pricetable ON
        a.id = pricetable.entity_id
      JOIN (
        SELECT
          count(distinct f.id) AS numberofrecords
        FROM
          civicrm_event f
        LEFT JOIN civicrm_value_extra_evenement_info g ON
          f.id = g.entity_id
        WHERE
          f.start_date >= NOW()
          AND g.activiteit_status = $eventStatusCommunicatieOK) AS counttable
      WHERE
        a.start_date >= NOW()
        AND c.option_group_id = $optionGroupEventType
        AND (d.activiteit_status = $eventStatusCommunicatieOK)
      ORDER BY
        a.start_date;
    ";
    $dao = CRM_Core_DAO::executeQuery($query);
    return $dao;
  }
}

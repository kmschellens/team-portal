<?php
class StatistiekenGateway
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function GetGespeeldePunten(Team $team): array
    {
        $query = 'SELECT 
                    U.id, 
                    U.name AS naam, 
                    email, 
                    C.cb_rugnummer AS rugnummer,
                    P.aantalGespeeldePunten
                  FROM J3_users U
                  INNER JOIN J3_user_usergroup_map M ON U.id = M.user_id
                  INNER JOIN J3_usergroups G on M.group_id = G.id
                  INNER JOIN J3_comprofiler C ON U.id = C.user_id
                  LEFT JOIN (    
                    SELECT rugnummer, count(*) aantalGespeeldePunten FROM (
                      SELECT ra AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                      UNION ALL
                      SELECT rv AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                      UNION ALL
                      SELECT mv AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                      UNION ALL
                      SELECT lv AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                      UNION ALL
                      SELECT la AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                      UNION ALL
                      SELECT ma AS rugnummer FROM DWF_punten P inner join DWF_wedstrijden W on P.matchId = W.id where W.skcTeam = :nevobonaam || W.otherTeam = :nevobonaam
                    ) T1
                    GROUP BY rugnummer ORDER BY aantalGespeeldePunten DESC
                  ) P ON C.cb_rugnummer = P.rugnummer
                  where G.title = :skcnaam';
        $params = [
            "nevobonaam" => $team->naam,
            "skcnaam" => $team->GetSkcNaam()
        ];
        $rows = $this->database->Execute($query, $params);
        $result = [];
        foreach ($rows as $row) {
            $result[] = new DwfSpeler(
                new Persoon($row->id, $row->naam, $row->email),
                $row->aantalGespeeldePunten ?? 0
            );
        }
        return $result;
    }

    public function GetAllePuntenByTeam($team): array
    {
        $query = 'SELECT * FROM DWF_punten P
                  INNER JOIN DWF_wedstrijden W ON P.matchId = W.id
                  WHERE P.skcTeam = ?
                  ORDER BY P.id';
        $params = [$team];
        $rows = $this->database->Execute($query, $params);
        return $this->MapToDwfPunten($rows);
    }

    public function GetAllePuntenByMatchId($matchId, $team): array
    {
        $query = 'SELECT * FROM DWF_punten P
                  INNER JOIN DWF_wedstrijden W ON P.matchId = W.id
                  WHERE P.matchId = ? AND P.skcTeam = ?
                  ORDER BY P.id';
        $params = [$matchId, $team];
        $rows = $this->database->Execute($query, $params);
        return $this->MapToDwfPunten($rows);
    }

    public function GetAlleSkcPunten(): array
    {
        $query = 'SELECT * FROM DWF_punten P
                  INNER JOIN DWF_wedstrijden W ON P.matchId = W.id
                  ORDER BY P.id';
        $rows = $this->database->Execute($query);
        return $this->MapToDwfPunten($rows);
    }

    private function MapToDwfPunten(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = new Wedstrijdpunt(
                $this->id,
                $this->matchId,
                $this->skcTeam,
                $this->set,
                $this->isSkcService,
                $this->isSkcPunt,
                $this->puntenSkcTeam,
                $this->puntenOtherTeam,
                $this->rechtsAchter,
                $this->rechtsVoor,
                $this->midVoor,
                $this->linksVoor,
                $this->linksAchter,
                $this->midAchter,
            );
        }

        return $result;
    }
}

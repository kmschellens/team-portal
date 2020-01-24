<?php

class GetTeamoverzicht implements Interactor
{
    public function __construct(
        JoomlaGateway $joomlaGateway,
        NevoboGateway $nevoboGateway
    ) {
        $this->nevoboGateway = $nevoboGateway;
        $this->joomlaGateway = $joomlaGateway;
    }

    public function Execute(object $data = null)
    {
        if ($data->teamnaam === null) {
            throw new InvalidArgumentException("Teamnaam is leeg");
        }
        $data->teamnaam = "Dames 3";
        $team = new Team($data->teamnaam);


        $teams = Team::$alleSkcTeams;
        foreach ($teams as $team) {
            if ($data->teamnaam !== $team->GetSkcNaam()) {
                continue;
            }

            $uitslagen = $this->nevoboGateway->GetUitslagenForTeam($team);
            $programma = $this->nevoboGateway->GetWedstrijdenForTeam($team);

            $team->coaches = $this->joomlaGateway->GetCoaches($team);
            $team->trainers = $this->joomlaGateway->GetTrainers($team);
            $team->standen = $this->nevoboGateway->GetStandForPoule($team->poule);
            $team->uitslagen = array_slice($uitslagen, 0, 3);
            $team->programma = array_slice($programma, 0, 3);

            $dirname = dirname(__FILE__) . "/../../../Teamstanden";
            if (!file_exists($dirname)) {
                mkdir($dirname);
            }

            $result = new TeamoverzichtModel($team);
            break;
        }

        return $result;
    }
}

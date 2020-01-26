<?php

namespace TeamPortal\UseCases;

class TeamoverzichtModel
{
    public function __construct(Entities\Team $team)
    {
        $this->id = $team->id;
        $this->naam = $team->naam;
        $this->poule = $team->poule;
        foreach ($team->teamgenoten as $teamgenoot) {
            $this->teamgenoten[] = new PersoonModel($teamgenoot);
        }
        $this->niveau = Niveau::GetNiveauString($team->niveau);
        $this->facebook = $team->facebook;
        $this->trainingstijden = $team->trainingstijden;

        foreach ($team->uitslagen as $wedstrijd) {
            $this->uitslagen[] = new WedstrijdModel($wedstrijd);
        }

        $this->standen = $team->standen;

        $this->programma = [];
        foreach ($team->programma as $wedstrijd) {
            $this->programma[] = new WedstrijdModel($wedstrijd);
        }

        $trainers = array_map(function (Entities\Persoon $trainer) {
            return $trainer->naam;
        }, $team->trainers);
        $this->trainers = implode(", ", $trainers);

        $coaches = array_map(function (Entities\Persoon $coach) {
            return $coach->naam;
        }, $team->coaches);
        $this->coaches = implode(", ", $coaches);
    }
}

<?php

namespace TeamPortal\UseCases;

use TeamPortal\Common\DateFunctions;
use TeamPortal\Gateways;

class ToggleBhv implements Interactor
{

    public function __construct(
        Gateways\JoomlaGateway $joomlaGateway,
        Gateways\BarcieGateway $barcieGateway
    ) {
        $this->joomlaGateway = $joomlaGateway;
        $this->barcieGateway = $barcieGateway;
    }

    public function Execute(object $data = null)
    {
        if ($data->barlidId === null) {
            throw new InvalidArgumentException("barlidId is leeg");
        }
        if ($data->shift === null) {
            throw new InvalidArgumentException("Shift is leeg");
        }

        $date = DateFunctions::CreateDateTime($data->date);
        if ($date === null) {
            throw new InvalidArgumentException("Incorrecte datum: $data->date");
        }

        $barlid = $this->joomlaGateway->GetUser($data->barlidId);
        $bardag = $this->barcieGateway->GetBardag($date);
        if ($bardag->id === null) {
            return;
        }

        $dienst = $this->barcieGateway->Getbardienst($bardag, $barlid, $data->shift);
        if ($dienst !== null) {
            $this->barcieGateway->ToggleBhv($dienst);
        }
    }
}

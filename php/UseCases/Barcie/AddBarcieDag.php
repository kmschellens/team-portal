<?php

class AddBarcieDag implements IInteractorWithData
{

    public function __construct(JoomlaGateway $joomlaGateway, BarcieGateway $barcieGateway)
    {
        $this->joomlaGateway = $joomlaGateway;
        $this->barcieGateway = $barcieGateway;
    }

    public function Execute($data)
    {
        $userId = $this->joomlaGateway->GetUserId();
        if (!$this->joomlaGateway->IsTeamcoordinator($userId)) {
            throw new UnexpectedValueException("Je bent geen teamcoordinator");
        }

        $date = DateFunctions::CreateDateTime($data->date ?? null);

        if ($date === null) {
            throw new InvalidArgumentException("Date is leeg");
        }

        if (new DateTime() > $date) {
            throw new UnexpectedValueException("Dag ligt in het verleden");
        }

        $dayId = $this->barcieGateway->GetDateId($date);
        if ($dayId !== null) {
            throw new UnexpectedValueException("Dag bestaat al");
        } else {
            $this->barcieGateway->AddBarcieDag($date);
        }
    }
}

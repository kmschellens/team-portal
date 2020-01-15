<?php

class DeleteBarcieDag implements IInteractorWithData
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

        $dayId = $this->barcieGateway->GetDateId($date);
        if ($dayId === null) {
            return;
        }

        $barciediensten = $this->barcieGateway->GetBarciedienstenForDate($date);
        if (count($barciediensten) > 0) {
            throw new UnexpectedValueException("Datum heeft nog aanwezigheden");
        }

        $this->barcieGateway->DeleteBarcieDay($dayId);
    }
}

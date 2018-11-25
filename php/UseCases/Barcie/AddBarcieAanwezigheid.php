<?php
include_once 'IInteractorWithData.php';
include_once 'BarcieGateway.php';
include_once 'JoomlaGateway.php';

class AddBarcieAanwezigheid implements IInteractorWithData
{
    public function __construct($database)
    {
        $this->barcieGateway = new BarcieGateway($database);
        $this->joomlaGateway = new JoomlaGateway($database);
    }

    public function Execute($data)
    {
        $userId = $this->joomlaGateway->GetUserId();
        if (!$this->joomlaGateway->IsScheidsco($userId)) {
            InternalServerError("Je bent geen scheidsco");
        }

        $barcieLidId = $data->barcieLidId ?? null;
        $date = $data->date ?? null;
        $shift = $data->shift ?? null;

        if ($barcieLidId === null) {
            InternalServerError("Barcielid is leeg");
        }
        if ($date === null) {
            InternalServerError("Date is leeg");
        }
        if ($shift === null) {
            InternalServerError("Shift is leeg");
        }

        $dayId = $this->barcieGateway->GetDateId($date);
        if ($dayId === null) {
            InternalServerError("Er bestaat geen barciedag $date");
        }

        $aanwezigheid = $this->barcieGateway->GetAanwezigheid($dayId, $barcieLidId, $shift);
        if ($aanwezigheid === null) {
            $this->barcieGateway->InsertAanwezigheid($dayId, $barcieLidId, $shift);
        } else {
            InternalServerError("Aanwezigheid bestaat al");
        }
    }
}

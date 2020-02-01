<?php

namespace TeamPortal\UseCases;

use TeamPortal\Common\DateFunctions;
use TeamPortal\Gateways;

class DeleteBardag implements Interactor
{
    public function __construct(
        Gateways\JoomlaGateway $joomlaGateway,
        Gateways\BarcieGateway $barcieGateway
    ) {
        $this->joomlaGateway = $joomlaGateway;
        $this->barcieGateway = $barcieGateway;
    }

    public function Execute(object $data = null): void
    {
        $date = DateFunctions::CreateDateTime($data->date);
        if ($date === null) {
            throw new InvalidArgumentException("Date is leeg");
        }

        $bardag = $this->barcieGateway->GetBardag($date);
        if ($bardag->id === null) {
            return;
        }

        if (count($bardag->shifts) > 0) {
            throw new \UnexpectedValueException("Datum heeft nog diensten");
        }

        $this->barcieGateway->DeleteBardag($bardag);
    }
}

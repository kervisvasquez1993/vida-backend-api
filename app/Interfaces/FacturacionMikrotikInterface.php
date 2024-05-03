<?php

namespace App\Interfaces;

interface FacturacionMikrotikInterface
{
    public function getFacturasNoPagadas(): int;
    public function getTotalFacturas(): string;
}

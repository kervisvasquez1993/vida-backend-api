<?php

namespace App\Interfaces;

interface ServicioMikrotikInterface
{
    public function getId(): int;
    public function getIdperfil(): int;
    public function getNodo(): int;
    public function getCosto(): string;
    public function getIpap(): string;
    public function getMac(): string;
    public function getIp(): string;
    public function getInstalado(): string;
    public function getPppuser(): string;
    public function getPpppass(): string;
    public function getTiposervicio(): string;
    public function getStatusUser(): string;
    public function getCoordenadas(): string;
    public function getDireccion(): string;
    public function getSnmpComunidad(): string;
    public function getPerfil(): string;
}

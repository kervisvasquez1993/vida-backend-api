<?php

namespace App\Interfaces;

interface ClienteMikrotikInterface
{
    public function getId(): int;
    public function getNombre(): string;
    public function getEstado(): string;
    public function getCorreo(): string;
    public function getTelefono(): string;
    public function getMovil(): string;
    public function getCedula(): string;
    public function getPasarela(): string;
    public function getCodigo(): string;
    public function getDireccionPrincipal(): string;
    public function getServicios(): array;
    public function getFacturacion(): array;
}

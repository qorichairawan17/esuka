<?php

namespace App\Enum;

enum StatusPaniteraEnum: string
{
    case NonPlh = 'Non Plh/Plt';
    case Plh = 'Plh (Pelaksana Harian)';
    case Plt = 'Plt (Pelaksana Tugas)';
}

<?php

class Socio extends Model
{

    public function validate(): array
    {

        $errores = [];

        if (!preg_match('/^[0-9]{8,8}[A-Za-z]/', $this->dni))
            $errores[] = "Error en el formato del DNI.";
        if (strlen($this->nombre) < 1 || strlen($this->nombre) > 32)
            $errores[] = "Error en la longitud del Nombre.";
        if (strlen($this->apellidos) < 1 || strlen($this->apellidos) > 64)
            $errores[] = "Error en la longitud del Apellido.";
        if (!preg_match('/^(?:0?[1-9]|[1-4]\d|5[0-2])\d{3}$/', $this->cp))
            $errores[] = "Error en el formato del Código Postal.";
        if (strlen($this->poblacion) < 1 || strlen($this->poblacion) > 64)
            $errores[] = "Error en la longitud de la población.";
        if (strlen($this->provincia) < 1 || strlen($this->provincia) > 64)
            $errores[] = "Error en la longitud de la província.";
        if (strlen($this->telefono < 8 || strlen($this->telefono) > 13))
            $errores[] = "Error en el formato del Teléfono.";


        return $errores;
    }
}

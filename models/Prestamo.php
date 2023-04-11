<?php
class Prestamo extends Model
{
    public function returnPrestamo(int $id = 0)
    {

        $consulta = "UPDATE prestamos SET devolucion = CURRENT_TIMESTAMP 
                    WHERE id = $id";
        return (DB_CLASS)::update($consulta);
    }
}

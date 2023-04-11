<?php
class Tema extends Model
{
    public function getLibros(): array
    {
        $consulta = "SELECT l.*
                    FROM libros l
                        INNER JOIN temas_libros tl ON l.id=tl.idlibro
                    WHERE tl.idlibro=$this->id";

        return (DB_CLASS)::selectAll($consulta, 'Libro');
    }
}

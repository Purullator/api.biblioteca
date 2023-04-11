<?php

class Libro extends Model
{
    public function getTemas(): array
    {
        $consulta = "SELECT t.*
                    FROM temas t
                        INNER JOIN temas_libros tl ON t.id=tl.idtema
                    WHERE tl.idlibro=$this->id";

        return (DB_CLASS)::selectAll($consulta, 'Tema');
    }

    public function addTema(int $libro, int $tema): int
    {
        $consulta = "INSERT INTO temas_libros(idlibro, idtema)
                    VALUES($libro, $tema)";

        return (DB_CLASS)::insert($consulta);
    }

    public function removeTema(int $libro, int $tema): int
    {
        $consulta = "DELETE FROM temas_libros
                    WHERE idlibro = $libro AND idtema = $tema";
        return (DB_CLASS)::delete($consulta);
    }

    public function validate(): array
    {

        $errores = [];

        if (!preg_match("/^[\d\-]{13,17}$/", $this->isbn))
            $errores[] = "Error en el formato del ISBN";
        if (strlen($this->titulo) < 1 || strlen($this->titulo) > 64)
            $errores[] = "Error en la longitud del Título";
        if ($this->ediciones < 0)
            $errores[] = "Error en el número de ediciones";
        if ($this->edadrecomendada > 18 || $this->edadrecomendada < 0)
            $errores[] = "Error en el rango de Edad Recomendada,
                          debe estar entre 0 y 18.";
        if (strlen($this->autor) < 1 || strlen($this->autor) > 64)
            $errores[] = "Error en la longitud del Autor";
        if (strlen($this->editorial) < 1 || strlen($this->editorial) > 64)
            $errores[] = "Error en la longitud de la editorial";


        return $errores;
    }
}

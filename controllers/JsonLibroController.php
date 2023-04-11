<?php
class JsonLibroController extends Controller
{

    public function __construct()
    {
        header('Content-type:application/json; charset=utf-8');
    }

    public function get($param1 = NULL, $param2 = NULL)
    {

        switch (true) {

            case $param1 && $param2;
                $libros = Libro::getFiltered($param1, $param2);
                break;

            case $param1 && !$param2;
                if (!$libro = Libro::getById($param1)) {

                    http_response_code(404);
                    throw new ApiException("No se encontró el libro $param1");
                }
                $libros = [$libro];
                break;

            default:
                $libros = Libro::get();
        }

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "Se han recuperado " . sizeof($libros) . " resultados.";
        $response->results = sizeof($libros);
        $response->data = $libros;

        echo JSON::encode($response);
    }

    public function delete(int $id = 0)
    {

        if (empty($id))
            throw new ApiException('No se indicó el libro a borrar.');

        if (!$libro = Libro::getById($id))
            throw new ApiException('No existe el libro indicado.');

        if ($libro->hasMany('Ejemplar'))
            throw new ApiException('No se puede borrar un libro con ejemplares.');

        Libro::delete($id);

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "Borrado del $libro->titulo resultados.";
        $response->data = [$libro];

        echo JSON::encode($response);
    }

    public function post()
    {

        $json = $this->request->body();

        if (empty($json))
            throw new ApiException('No se indicaron libros a insertar');

        $libros = JSON::decode($json, 'Libro');

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "";
        $response->data = [];

        foreach ($libros as $libro) {

            $libro->saneate();
            $errores = $libro->validate();

            if (sizeof($errores)) {
                $response->status = "WARNING";
                $response->message .= "$libro->titulo tiene errores";
                $response->data[$libro->titulo] = $errores;
            } else {
                try {
                    $libro->save();
                    $response->message .= "$libro->titulo guardado correctamente. ";
                    http_response_code(201);
                } catch (Throwable $t) {
                    $response->status = "WARNING";
                    $response->message .= "$libro->titulo no se pudo guardar. ";
                    $response->data[$libro->titulo] = DEBUG ? $t->getMessage() : " Duplicado?";
                }
            }
        }
        echo JSON::encode($response);
    }

    public function put()
    {

        $json = $this->request->body();

        if (empty($json))
            throw new ApiException('No se recibió el JSON con los libros a actualizar.');

        $libros = JSON::decode($json, 'Libro');

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "";
        $response->data = [];

        foreach ($libros as $libro) {
            $libro->saneate();
            $errores = $libro->validate();

            if (sizeof($errores)) {

                $response->status = "WARNING";
                $response->message .= "$libro->titulo tiene errores. ";
                $response->data[$libro->titulo] = $errores;
            } else {
                if (empty($libro->id)) {

                    $response->status = "WARNING";
                    $response->message = "$libro->titulo no se puede actualizar. ";
                    $response->data[] = "No se indicó el ID a actualizar. ";
                } else {
                    try {
                        $libro->update();
                        $response->message .= "$libro->titulo actualizado correctamente. ";
                    } catch (Throwable $t) {
                        $response->status = "WARNING";
                        $response->message .= "$libro->titulo no se pudo actualizar. ";
                        $response->data[$libro->titulo] = DEBUG ? $t->getMessage() : " Duplicado? ";
                    }
                }
            }
        }
        echo JSON::encode($response);
    }
}

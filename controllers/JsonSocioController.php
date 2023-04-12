<?php
class JsonSocioController extends Controller
{

    public function __construct()
    {
        header('Content-type:application/json; charset=utf-8');
    }

    public function get($param1 = NULL, $param2 = NULL)
    {

        switch (true) {

            case $param1 && $param2;
                $socios = Socio::getFiltered($param1, $param2);
                break;

            case $param1 && !$param2;
                if (!$socio = Socio::getById($param1)) {

                    http_response_code(404);
                    throw new ApiException("No se encontró el socio $param1");
                }
                $socios = [$socio];
                break;

            default:
                $socios = Socio::get();
        }

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "Se han recuperado " . sizeof($socios) . " resultados.";
        $response->results = sizeof($socios);
        $response->data = $socios;

        echo JSON::encode($response);
    }

    public function delete(int $id = 0)
    {

        if (empty($id))
            throw new ApiException('No se indicó el socio a borrar.');

        if (!$socio = Socio::getById($id))
            throw new ApiException('No existe el socio indicado.');

        if ($socio->hasMany('Ejemplar'))
            throw new ApiException('No se puede borrar un socio con ejemplares.');

        Socio::delete($id);

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "Borrado del $socio->nombre resultados.";
        $response->data = [$socio];

        echo JSON::encode($response);
    }

    public function post()
    {

        $json = $this->request->body();

        if (empty($json))
            throw new ApiException('No se indicaron socios a insertar');

        $socios = JSON::decode($json, 'socio');

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "";
        $response->data = [];

        foreach ($socios as $socio) {

            $socio->saneate();
            $errores = $socio->validate();

            if (sizeof($errores)) {
                $response->status = "WARNING";
                $response->message .= "$socio->nombre tiene errores";
                $response->data[$socio->nombre] = $errores;
            } else {
                try {
                    $socio->save();
                    $response->message .= "$socio->nombre guardado correctamente. ";
                    http_response_code(201);
                } catch (Throwable $t) {
                    $response->status = "WARNING";
                    $response->message .= "$socio->nombre no se pudo guardar. ";
                    $response->data[$socio->nombre] = DEBUG ? $t->getMessage() : " Duplicado?";
                }
            }
        }
        echo JSON::encode($response);
    }

    public function put()
    {

        $json = $this->request->body();

        if (empty($json))
            throw new ApiException('No se recibió el JSON con los socios a actualizar.');

        $socios = JSON::decode($json, 'socio');

        $response = new stdClass();
        $response->status = "Ok";
        $response->message = "";
        $response->data = [];

        foreach ($socios as $socio) {
            $socio->saneate();
            $errores = $socio->validate();

            if (sizeof($errores)) {

                $response->status = "WARNING";
                $response->message .= "$socio->nombre tiene errores. ";
                $response->data[$socio->nombre] = $errores;
            } else {
                if (empty($socio->id)) {

                    $response->status = "WARNING";
                    $response->message = "$socio->nombre no se puede actualizar. ";
                    $response->data[] = "No se indicó el ID a actualizar. ";
                } else {

                    try {
                        $socio->update();
                        $response->message .= "$socio->nombre actualizado correctamente. ";
                    } catch (Throwable $t) {

                        $response->status = "WARNING";
                        $response->message .= "$socio->nombre no se pudo actualizar. ";
                        $response->data[$socio->nombre] = DEBUG ? $t->getMessage() : " Duplicado? ";
                    }
                }
            }
        }
        echo JSON::encode($response);
    }
}

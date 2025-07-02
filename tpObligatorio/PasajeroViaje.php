<?php

class PasajeroViaje
{
     //Atributos
    private Viaje $viaje;
    private Pasajero $pasajero;

    /**
     * @param $viaje
     * @param $pasajero
     */
    public function __construct($viaje, $pasajero)
    {
        $this->viaje = $viaje;
        $this->pasajero = $pasajero;
    }


    /**
     * @return mixed
     */
    public function getViaje()
    {
        return $this->viaje;
    }

    /**
     * @param mixed $viaje
     */
    public function setViaje($viaje): void
    {
        $this->viaje = $viaje;
    }

    /**
     * @return mixed
     */
    public function getPasajero()
    {
        return $this->pasajero;
    }

    /**
     * @param mixed $pasajero
     */
    public function setPasajero($pasajero): void
    {
        $this->pasajero = $pasajero;
    }

    //Metodos
    public function insertar()
    {
        $dataBase = new DataBase();
        $respuesta = false;
        $consulta="INSERT INTO pasajero_viaje(numeroDocumento, idViaje)
                VALUES ('".$this->getPasajero()->getNumeroDocumento()."','".$this->getViaje()->getIdViaje()."')";
        if($dataBase->iniciar()){
            if($dataBase->ejecutar($consulta)){
                $respuesta=true;
            }
            else{
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        }
        else{
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }
        return $respuesta;
    }

    public static function listarPorIdViaje($idViaje)
    {
        $arrayPasajero = [];
        $dataBase = new DataBase();
        $consulta = "SELECT * FROM pasajero_viaje WHERE idViaje = '".$idViaje."';";

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {

                while ($fila = $dataBase->registro()) {
                    $objPasajero = new Pasajero('','','','');
                    $objPasajero->buscar($fila['numeroDocumento']);
                    $arrayPasajero[] = $objPasajero;
                }
            } else {
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }

        return $arrayPasajero;
    }

    public function cantPasajerosEnViaje()
    {
        $dataBase = new DataBase();
        $consulta="SELECT count(idViaje) AS 'cantPasajeros' FROM pasajero_viaje WHERE idViaje = '".$this->getViaje()->getIdViaje()."';";
        $cantPasajeros = 0;


        if($dataBase->iniciar()){
            if($dataBase->ejecutar($consulta)){
                if ($fila = $dataBase->registro()) {
                    $cantPasajeros = $fila['cantPasajeros'];
                }
            }
            else{
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        }
        else{
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }
        return $cantPasajeros;
    }

    /**
     * Verifica si el pasajero ya esta cargado al viaje.
     * @return bool true si el pasajero se encuentra en el viaje.
     * @throws Exception
     */
    public function yaExiste()
    {
        $respuesta = false;
        $database = new DataBase();
        $consulta = "SELECT * FROM pasajero_viaje WHERE idViaje = '".$this->getViaje()->getIdViaje()."' AND numeroDocumento = '".$this->getPasajero()->getNumeroDocumento()."'";

        if ($database->iniciar()){
            if ($database->ejecutar($consulta)){
                if ($database->registro() != null){
                    $respuesta = true;
                }
            }else{
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        }else{
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }
        return $respuesta;
    }
}
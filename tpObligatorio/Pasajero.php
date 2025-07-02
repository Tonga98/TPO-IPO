<?php
require_once 'Persona.php';
class Pasajero extends Persona {
// Atributos
    private  $telefono;
    private $numeroDocumento;



    //Metodos
    public function __construct($numeroDocumento, $nombre, $apellido, $telefono){
        parent::__construct($nombre,$apellido);
        $this->numeroDocumento = $numeroDocumento;
        $this->telefono = $telefono;
    }
// Getters
    public function getTelefono(){
        return $this->telefono;
    }
// Setters
    public function setTelefono($newTelefono){
        $this->telefono = $newTelefono;
    }
    public function __toString(): string {
        $telefono = $this->getTelefono();
        $string = parent::__toString();
        return
            $string.
            "Numero Documento: ".$this->getNumeroDocumento()."\n".

        "Telefono: $telefono \n".
            "-----------------------------\n";
    }

    /**
     * @return mixed
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * @param mixed $numeroDocumento
     */
    public function setNumeroDocumento($numeroDocumento): void
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    public function buscar($numeroDoc)
    {
        $dataBase = new DataBase();
        $consulta = "SELECT pa.numeroDocumento, pa.telefono, p.nombre, p.apellido, p.id
                 FROM pasajero pa
                 INNER JOIN persona p ON pa.id = p.id
                 WHERE pa.numeroDocumento = '" . $numeroDoc . "'";
        $respuesta = false;

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                if ($fila = $dataBase->registro()) {
                    $this->setNumeroDocumento($fila['numeroDocumento']);
                    $this->setNombre($fila['nombre']);
                    $this->setApellido($fila['apellido']);
                    $this->setTelefono($fila['telefono']);
                    $this->setId($fila['id']);
                    $respuesta = true;
                }
            } else {
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }

        return $respuesta;
    }

    public static function listar(): array
    {
        $dataBase = new DataBase();
        $consulta = "SELECT p.id, p.nombre, p.apellido, pa.telefono, pa.numeroDocumento
                 FROM pasajero pa
                 INNER JOIN persona p ON pa.id = p.id";

        $arrayPasajero = [];

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                while ($fila = $dataBase->registro()) {
                    $objPasajero = new Pasajero(
                        $fila['numeroDocumento'],
                        $fila['nombre'],
                        $fila['apellido'],
                        $fila['telefono']
                    );
                    $objPasajero->setId($fila['id']);
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






    /**
     * @throws Exception
     */
    public function insertar()
    {
        $respuesta = false;

        try {
            //Insertar en persona
            $idPersona = parent::insertar();
            if ($idPersona !== -1) {
                $dataBase = new DataBase();
                $consultaPasajero = "INSERT INTO pasajero(id, numeroDocumento, telefono)
                                 VALUES ('{$this->getId()}', '{$this->getNumeroDocumento()}', '{$this->getTelefono()}')";

                if ($dataBase->iniciar()) {
                    if ($dataBase->ejecutar($consultaPasajero)) {
                        $respuesta = true;
                    } else {
                        throw new Exception("Error: la consulta INSERT en pasajero no se pudo ejecutar");
                    }
                } else {
                    throw new Exception("Error: la base de datos no se pudo iniciar");
                }
            }
        } catch (mysqli_sql_exception $e) {
            echo "Error de restricción: El numero de documento ya existe. \n";
        }

        return $respuesta;
    }

    public function modificar(): bool
    {
        $respuesta = false;
        $dataBase = new DataBase();

        try {
            //persona
            if (parent::modificar()) {

                $numeroDocumento = $this->getNumeroDocumento();
                $telefono = $this->getTelefono();

                $consultaPasajero = "UPDATE pasajero SET telefono = '$telefono' 
                                 WHERE numeroDocumento = '$numeroDocumento'";

                if ($dataBase->iniciar()) {
                    if ($dataBase->ejecutar($consultaPasajero)) {
                        $respuesta = true;
                    } else {
                        throw new Exception("Error al modificar pasajero.");
                    }
                } else {
                    throw new Exception("Error: no se pudo iniciar la base de datos.");
                }

            }
        } catch (Exception $e) {
            echo "Error al modificar: " . $e->getMessage() . "\n";
        }

        return $respuesta;
    }

    public function eliminar(): bool
    {
        $respuesta = false;
        $dataBase = new DataBase();

        $numeroDocumento = $this->getNumeroDocumento();

        try {
            if ($dataBase->iniciar()) {
                // 1. Eliminar de pasajero
                $consultaPasajero = "DELETE FROM pasajero WHERE numeroDocumento = '".$numeroDocumento."'";

                if (!$dataBase->ejecutar($consultaPasajero)) {
                    throw new Exception("Error al eliminar el pasajero.");
                }

                // 2. Eliminar de persona
                if (parent::eliminar()) {
                    $respuesta = true;
                } else {
                    throw new Exception("No se pudo eliminar la persona asociada.");
                }
            } else {
                throw new Exception("Error: no se pudo iniciar la base de datos.");
            }
        } catch (Exception $e) {
            echo "Error al eliminar: El pasajero pertence a un viaje. \n";
        }

        return $respuesta;
    }


}
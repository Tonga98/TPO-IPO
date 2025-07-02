<?php

class Viaje
{

    // Atributos
    private $idViaje;
    private $destino;
    private $cantMaxPasajeros;
    private Empresa $empresa;
    private ResponsableV $responsable;
    private $importe;
    private $colPasajeros;

    // Constructor
    public function __construct($destino, $cantMaxPasajeros, $empresa, $responsable, $importe)
    {
        $this->destino = $destino;
        $this->cantMaxPasajeros = $cantMaxPasajeros;
        $this->empresa = $empresa;
        $this->responsable = $responsable;
        $this->importe = $importe;
    }


    // Getters

    public function getColPasajeros() {
        return $this->colPasajeros;
    }

    public function getIdViaje()
    {
        return $this->idViaje;
    }

    public function getDestino()
    {
        return $this->destino;
    }

    public function getCantMaxPasajeros()
    {
        return $this->cantMaxPasajeros;
    }

    public function getEmpresa()
    {
        return $this->empresa;
    }

    public function getResponsable()
    {
        return $this->responsable;
    }

    public function getImporte()
    {
        return $this->importe;
    }

    // Setters
    public function setColPasajeros($coleccionPasajeros) {
        $this->colPasajeros = $coleccionPasajeros;
    }
    public function setIdViaje($idViaje)
    {
        $this->idViaje = $idViaje;
    }
    public function setDestino($destino)
    {
        $this->destino = $destino;
    }

    public function setCantMaxPasajeros($cantMaxPasajeros)
    {
        $this->cantMaxPasajeros = $cantMaxPasajeros;
    }

    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;
    }

    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    public function __toString()
    {
        $numViaje = $this->getIdViaje();
        $destino = $this->getDestino();
        $cantMaxPasajeros = $this->getCantMaxPasajeros();
        $empresa = $this->getEmpresa();
        $responsable = $this->getResponsable();
        $importe = $this->getImporte();

        return
            "Numero viaje: $numViaje\n" .
            "Destino: $destino\n" .
            "Cantidad máxima de pasajeros: $cantMaxPasajeros\n" .
            "Importe: $$importe\n".
            " - Empresa: \n$empresa\n" .
            " - Empleado:\n $responsable\n";
    }

    /**
     * @throws Exception
     */
    public function cargarPasajeros(): void
    {
        //instanciar un objeto pasajero viaje o llamar metodo estatico?
        $pasajeros = PasajeroViaje::listarPorIdViaje($this->getIdViaje());
        $this->setColPasajeros($pasajeros);
    }


    /**
     * Buscar un viaje por su ID en la base de datos
     * @param int $identificadorViaje identificadorViaje
     * @return boolean
     * @throws Exception
     */
    public function buscar($identificadorViaje): bool
    {
        $dataBase = new DataBase ();
        $consulta = "SELECT * FROM Viaje WHERE idViaje = '" . $identificadorViaje . "'";
        $respuesta = false;

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                if ($fila = $dataBase->registro()) {
                    $this->setDestino($fila["destino"]);
                    $this->setCantMaxPasajeros($fila["cantMaxPasajeros"]);
                    //Obtener objeto empresa
                    $empresa = new Empresa('','');
                    $empresa->buscar($fila['idEmpresa']);
                    $this->setEmpresa($empresa);
                    //Obtener objeto responsable
                    $responsable = new ResponsableV('','','');
                    $responsable->buscar($fila['numeroEmpleado']);
                    $this->setImporte($fila["importe"]);
                    $this->setResponsable($responsable);
                    $this->setIdViaje($identificadorViaje);
                    $respuesta = true;
                }
            } else {
                throw new Exception ("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception ("Error: la base de datos no se pudo iniciar");
        }
        return $respuesta;
    }

    /**
     * Listar toda la tabla Viaje
     * @return array|null
     * @throws Exception
     */
    public static function listar()
    {
        $dataBase = new DataBase ();
        $consulta = "SELECT * FROM Viaje";

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                $coleccionViajes = [];
                while ($fila = $dataBase->registro()) {
                    //Obtener objeto responsable
                    $responsable = new ResponsableV('','','');
                    $responsable->buscar($fila['numeroEmpleado']);

                    //Obtener objeto empresa
                    $empresa = new Empresa('','');
                    $empresa->buscar($fila['idEmpresa']);

                    $objViaje = new Viaje (
                        $fila["destino"],
                        $fila["cantMaxPasajeros"],
                        $empresa,
                        $responsable,
                        $fila["importe"]
                    );

                    $objViaje->setIdViaje($fila['idViaje']);
                    $coleccionViajes [] = $objViaje;
                }
            } else {
                throw new Exception ("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception ("Error: la base de datos no se pudo iniciar");
        }
        return $coleccionViajes;
    }

    /**
     * Permite insertar un nuevo viaje.
     * @return boolean
     * @throws Exception
     */
    public function insertar()
    {
        $dataBase = new DataBase ();
        $respuesta = false;
        $responsableId = $this->getResponsable()->getNumeroEmpleado();
        $empresa = $this->getEmpresa()->getIdEmpresa();

        $consulta = "INSERT INTO Viaje(destino, cantMaxPasajeros, idEmpresa, numeroEmpleado, importe)
                VALUES ('" . $this->getDestino() . "','" . $this->getCantMaxPasajeros() . "','" . $empresa . "','" . $responsableId . "','" . $this->getImporte() . "')";
        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                $respuesta = true;
            } else {
                throw new Exception ("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception ("Error: la base de datos no se pudo iniciar");
        }
        return $respuesta;
    }

    /**
     * Modificar los datos de un viaje
     * @return boolean
     * @throws Exception
     */
    public function modificar()
    {
        $dataBase = new DataBase();
        $consulta = "UPDATE Viaje SET ";
        $hayCampo = false;

        if ($this->getDestino() !== '') {
            $consulta .= "destino = '" . $this->getDestino() . "'";
            $hayCampo = true;
        }

        if ($this->getCantMaxPasajeros() !== '') {
            if ($hayCampo) $consulta .= ", ";
            $consulta .= "cantMaxPasajeros = '" . $this->getCantMaxPasajeros() . "'";
            $hayCampo = true;
        }

        if ($this->getEmpresa() != null) {
            if ($hayCampo) $consulta .= ", ";
            $consulta .= "idEmpresa = '" . $this->getEmpresa()->getIdEmpresa() . "'";
            $hayCampo = true;
        }

        if ($this->getResponsable() != null) {
            if ($hayCampo) $consulta .= ", ";
            $consulta .= "numeroEmpleado = '" . $this->getResponsable()->getNumeroEmpleado() . "'";
            $hayCampo = true;
        }

        if ($this->getImporte() !== '') {
            if ($hayCampo) $consulta .= ", ";
            $consulta .= "importe = '" . $this->getImporte() . "'";
            $hayCampo = true;
        }

        if (!$hayCampo) {
            throw new Exception("Error: no se proporcionaron campos para modificar.");
        }

        $consulta .= " WHERE idViaje = " . $this->getIdViaje() . ";";

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                return true;
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $dataBase->getError());
            }
        } else {
            throw new Exception("Error al iniciar la base de datos: " . $dataBase->getError());
        }
    }

    /**
     * Permite eliminar un viaje
     * @return boolean
     * @throws Exception
     */
    public function eliminar()
    {
        $respuesta = false;
        $dataBase = new DataBase ();

        if ($dataBase->iniciar()) {
            $consulta = "DELETE FROM Viaje WHERE idViaje = " . $this->getIdViaje();
            if ($dataBase->ejecutar($consulta)) {
                $respuesta = true;
            } else {
                throw new Exception ("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception ("Error: la base de datos no se pudo iniciar");
        }
        return $respuesta;
    }
}

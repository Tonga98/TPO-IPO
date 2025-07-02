<?php
require_once 'Persona.php';
class ResponsableV extends Persona
{
    // Atributos
    private $numeroEmpleado;
    private $numeroLicencia;

    /// Constructor
    public function __construct($numeroLicencia, $nombre, $apellido)
    {
        parent::__construct($nombre, $apellido);
        $this->numeroLicencia = $numeroLicencia;
    }

    // GETTERS
    public function getNumeroEmpleado()
    {
        return $this->numeroEmpleado;
    }

    public function getNumeroLicencia()
    {
        return $this->numeroLicencia;
    }


    // SETTERS
    public function setNumeroEmpleado($numeroEmpleado)
    {
        $this->numeroEmpleado = $numeroEmpleado;
    }
    public function setNumeroLicencia($numeroLicencia)
    {
        $this->numeroLicencia = $numeroLicencia;
    }

    public function __toString()
    {
        $string = parent::__toString();
        $numeroEmpleado = $this->getNumeroEmpleado();
        $numeroLicencia = $this->getNumeroLicencia();
        return
            $string.
            "Número empleado: $numeroEmpleado\n" .
            "Número licencia: $numeroLicencia\n" .
            "-----------------------------\n";
    }

    /**
     * Si encuentra el responsable con el id, le asigna los datos al objeto.
     * @param $numEmpleado
     * @return bool
     * @throws Exception
     */
    public function buscar($numEmpleado): bool
    {
        $dataBase = new DataBase();
        $consulta = "SELECT r.numeroEmpleado, r.numeroLicencia, p.nombre, p.apellido
                 FROM responsable r
                 JOIN persona p ON r.numeroEmpleado = p.id
                 WHERE r.numeroEmpleado = '" . $numEmpleado . "'";
        $respuesta = false;

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                if ($fila = $dataBase->registro()) {
                    $this->setNumeroEmpleado($fila['numeroEmpleado']);
                    $this->setNumeroLicencia($fila['numeroLicencia']);
                    $this->setNombre($fila['nombre']);
                    $this->setApellido($fila['apellido']);
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
        $consulta = "SELECT r.numeroEmpleado, r.numeroLicencia, p.nombre, p.apellido
                 FROM responsable r
                 INNER JOIN persona p ON r.numeroEmpleado = p.id";

        $arrayResponsable = [];

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {

                while ($fila = $dataBase->registro()) {
                    $objResponsable = new ResponsableV(
                        $fila['numeroLicencia'],
                        $fila['nombre'],
                        $fila['apellido']
                    );
                    $objResponsable->setNumeroEmpleado($fila['numeroEmpleado']);
                    $arrayResponsable[] = $objResponsable;
                }

            } else {
                throw new Exception("Error: la consulta no se pudo ejecutar");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }

        return $arrayResponsable;
    }


    /**
     * @return true
     * @throws Exception
     */
    public function insertar()
    {
        $dataBase = new DataBase();
        $respuesta = false;

        // 1. Insertar persona
        $consultaPersona = "INSERT INTO persona(nombre, apellido) VALUES ('".$this->getNombre()."','".$this->getApellido()."')";



        if ($dataBase->iniciar()) {
            if ($numEmpleado = $dataBase->devuelveIDInsercion($consultaPersona)) {
                $this->setNumeroEmpleado($numEmpleado);
                // 2. Insertar en responsable
                $consultaResponsable = "INSERT INTO responsable(numeroEmpleado, numeroLicencia) VALUES ('".$this->getNumeroEmpleado()."', '".$this->getNumeroLicencia()."')";
                if ($dataBase->ejecutar($consultaResponsable)){
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

    public function modificar(): bool
    {
        $dataBase = new DataBase();
        $respuesta = false;

        $idPersona = $this->getNumeroEmpleado();
        $numLicencia = $this->getNumeroLicencia();
        $nombre = $this->getNombre();
        $apellido = $this->getApellido();

        // Modificar persona
        $consultaPersona = "UPDATE persona SET nombre = '$nombre', apellido = '$apellido'
                        WHERE id = '$idPersona'";

        // Modificar responsable
        $consultaResponsable = "UPDATE responsable SET numeroLicencia = '$numLicencia' 
                            WHERE numeroEmpleado = '$idPersona'";

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consultaPersona) && $dataBase->ejecutar($consultaResponsable)) {
                $respuesta = true;
            } else {
                throw new Exception("Error: no se pudo ejecutar alguna de las consultas de actualización");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }

        return $respuesta;
    }

    /**
     * Elimina al responsable (de la tabla responsable y luego de persona)
     * @throws Exception
     */
    public function eliminar(): bool
    {
        $respuesta = false;
        $dataBase = new DataBase();
        $idPersona = $this->getNumeroEmpleado(); // Es el ID en persona

        try {
            if ($dataBase->iniciar()) {
                // 1. Eliminar de responsable
                $consultaResponsable = "DELETE FROM responsable WHERE numeroEmpleado = '$idPersona'";
                if (!$dataBase->ejecutar($consultaResponsable)) {
                    throw new Exception("Error: no se pudo eliminar de la tabla responsable");
                }

                // 2. Eliminar de persona
                $consultaPersona = "DELETE FROM persona WHERE id = '$idPersona'";
                if (!$dataBase->ejecutar($consultaPersona)) {
                    throw new Exception("Error: no se pudo eliminar la persona asociada");
                }

                $respuesta = true;

            } else {
                throw new Exception("Error: la base de datos no se pudo iniciar");
            }

        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error de restricción: no se puede eliminar porque el responsable está asociado a un viaje.");
        }

        return $respuesta;
    }

}

<?php

abstract class Persona
{
    private $id;
    private $nombre;
    private $apellido;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    public function __construct($nombre, $apellido)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
    }

    // Getters
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    // Setters
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function __toString()
    {
     return
     "Nombre: " .$this->getNombre()."\n".
     "Apellido: " .$this->getApellido()."\n";
    }

    public function insertar()
    {
        $dataBase = new DataBase();
        $idInsertado = -1;

        $consulta = "INSERT INTO persona(nombre, apellido) 
                 VALUES ('{$this->getNombre()}', '{$this->getApellido()}')";

        if ($dataBase->iniciar()) {
            $idInsertado = $dataBase->devuelveIDInsercion($consulta);
            if ($idInsertado !== null) {
                $this->setId($idInsertado);
            } else {
                throw new Exception("Error al insertar en persona.");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar");
        }

        return $idInsertado;
    }

    public function modificar()
    {
        $respuesta = false;
        $dataBase = new DataBase();

        $idPersona = $this->getId();
        $nombre = $this->getNombre();
        $apellido = $this->getApellido();

        $consulta = "UPDATE persona SET nombre = '$nombre', apellido = '$apellido' 
                 WHERE id = '$idPersona'";

        if ($dataBase->iniciar()) {
            if ($dataBase->ejecutar($consulta)) {
                $respuesta = true;
            } else {
                throw new Exception("Error al modificar persona.");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar.");
        }

        return $respuesta;
    }

    public function eliminar(): bool
    {
        $respuesta = false;
        $dataBase = new DataBase();

        $idPersona = $this->getId();

        if ($dataBase->iniciar()) {
            $consulta = "DELETE FROM persona WHERE id = $idPersona";

            if ($dataBase->ejecutar($consulta)) {
                $respuesta = true;
            } else {
                throw new Exception("Error al eliminar la persona.");
            }
        } else {
            throw new Exception("Error: la base de datos no se pudo iniciar.");
        }

        return $respuesta;
    }



}

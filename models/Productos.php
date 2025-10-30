<?php
namespace Model;

class Productos extends ActiveRecord {
    protected static $tabla = 'productos';
    protected static $columnasDB = [
        'id','nombre','colorway','descripcion','categoria_id',
        'marca','stock','imagenes','precio','costo',
        'destacado','deporte_id','terreno_id','silueta','tallas'
    ];

    public $id;
    public $nombre;
    public $colorway;
    public $descripcion;
    public $categoria_id;
    public $marca;
    public $stock;
    public $imagenes;
    public $precio;
    public $costo;
    public $destacado;
    public $deporte_id;
    public $terreno_id;
    public $silueta;
    public $tallas;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->colorway = $args['colorway'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->categoria_id = $args['categoria_id'] ?? null;
        $this->marca = $args['marca'] ?? null;
        $this->stock = $args['stock'] ?? 0;
        $this->precio = $args['precio'] ?? 0;
        $this->costo = $args['costo'] ?? 0;
        $this->destacado = $args['destacado'] ?? 0;
        $this->deporte_id = $args['deporte_id'] ?? null;
        $this->terreno_id = $args['terreno_id'] ?? null;
        $this->silueta = $args['silueta'] ?? '';
        $this->tallas = $args['tallas'] ?? '';

        // 🧩 Manejo robusto de imágenes (JSON, string o array)
        if (!empty($args['imagenes'])) {
            $imagenes = $args['imagenes'];

            if (is_string($imagenes)) {
                // Si es JSON válido → decodificar
                $decoded = json_decode($imagenes, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->imagenes = $decoded;
                } elseif ($decoded === true || $decoded === false || is_null($decoded) || !is_array($decoded)) {
                    $this->imagenes = [];
                } else {
                    // Si es una cadena separada por comas → convertir a array
                    $lista = array_filter(array_map('trim', explode(',', $imagenes)));
                    $this->imagenes = $lista ?: [];
                }
            } elseif (is_array($imagenes)) {
                $this->imagenes = array_values(array_filter($imagenes));
            } else {
                $this->imagenes = [];
            }
        } else {
            $this->imagenes = [];
        }

        // Ensure imagenes is always an array
        if (!is_array($this->imagenes)) {
            $this->imagenes = [];
        }

        // 🧩 Manejo robusto de tallas (JSON, string o array)
        if (!empty($args['tallas'])) {
            $tallas = $args['tallas'];

            if (is_string($tallas)) {
                // Si es JSON válido → decodificar
                $decoded = json_decode($tallas, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->tallas = $decoded;
                } else {
                    // Si es una cadena separada por comas → convertir a array
                    $lista = array_filter(array_map('trim', explode(',', $tallas)));
                    $this->tallas = $lista ?: [];
                }
            } elseif (is_array($tallas)) {
                $this->tallas = array_values(array_filter($tallas));
            } else {
                $this->tallas = [];
            }
        } else {
            $this->tallas = [];
        }
    }

    // ✅ Helper para verificar si un string es JSON válido
    private function isJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    protected static function crearObjeto($registro) {
                $objeto = parent::crearObjeto($registro);
                if (method_exists($objeto, 'procesarImagenes')) {
                    $objeto->procesarImagenes();
                }
                if (method_exists($objeto, 'procesarTallas')) {
                    $objeto->procesarTallas();
                }
                return $objeto;
            }

    // ✅ Procesar imágenes después de cargar desde DB
    public function procesarImagenes() {
        if (!empty($this->imagenes)) {
            $imagenes = $this->imagenes;

            if (is_string($imagenes)) {
                // Si es JSON válido → decodificar
                $decoded = json_decode($imagenes, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->imagenes = $decoded;
                } else {
                    // Si es una cadena separada por comas → convertir a array
                    $lista = array_filter(array_map('trim', explode(',', $imagenes)));
                    $this->imagenes = $lista ?: [];
                }
            } elseif (is_array($imagenes)) {
                $this->imagenes = array_values(array_filter($imagenes));
            } else {
                $this->imagenes = [];
            }
        } else {
            $this->imagenes = [];
        }
    }

    // ✅ Procesar tallas después de cargar desde DB
    public function procesarTallas() {
        if (!empty($this->tallas)) {
            $tallas = $this->tallas;

            if (is_string($tallas)) {
                // Si es JSON válido → decodificar
                $decoded = json_decode($tallas, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->tallas = $decoded;
                } else {
                    // Si es una cadena separada por comas → convertir a array
                    $lista = array_filter(array_map('trim', explode(',', $tallas)));
                    $this->tallas = $lista ?: [];
                }
            } elseif (is_array($tallas)) {
                $this->tallas = array_values(array_filter($tallas));
            } else {
                $this->tallas = [];
            }
        } else {
            $this->tallas = [];
        }
    }

    // ✅ Evita doble codificación del JSON
    public function atributos() {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;

            if ($columna === 'imagenes') {
                if (empty($this->imagenes)) {
                    $atributos[$columna] = null;
                } elseif (is_array($this->imagenes)) {
                    $atributos[$columna] = json_encode($this->imagenes, JSON_UNESCAPED_SLASHES);
                } elseif ($this->isJson($this->imagenes)) {
                    $atributos[$columna] = $this->imagenes; // ya es JSON
                } else {
                    $atributos[$columna] = json_encode(explode(',', $this->imagenes), JSON_UNESCAPED_SLASHES);
                }
            } elseif ($columna === 'tallas') {
                if (empty($this->tallas)) {
                    $atributos[$columna] = null;
                } elseif (is_array($this->tallas)) {
                    $atributos[$columna] = json_encode($this->tallas, JSON_UNESCAPED_SLASHES);
                } elseif ($this->isJson($this->tallas)) {
                    $atributos[$columna] = $this->tallas; // ya es JSON
                } else {
                    $atributos[$columna] = json_encode(explode(',', $this->tallas), JSON_UNESCAPED_SLASHES);
                }
            } else {
                $atributos[$columna] = $this->$columna;
            }
        }
        return $atributos;
    }

    // ✅ Validaciones básicas del formulario
    public function validarProducto() {
        self::$alertas = [];

        if (!$this->nombre) self::$alertas['error'][] = 'El nombre del producto es obligatorio';
        if (!$this->colorway) self::$alertas['error'][] = 'El colorway es obligatorio';
        if (!$this->descripcion) self::$alertas['error'][] = 'La descripción es obligatoria';
        if (!$this->categoria_id || !is_numeric($this->categoria_id))
            self::$alertas['error'][] = 'Debe seleccionar una categoría válida';
        if (!$this->marca || !is_numeric($this->marca))
            self::$alertas['error'][] = 'Debe seleccionar una marca válida';
        if ($this->stock === null || !is_numeric($this->stock) || $this->stock < 0)
            self::$alertas['error'][] = 'El stock debe ser un número positivo';
        if ($this->precio === null || !is_numeric($this->precio) || $this->precio <= 0)
            self::$alertas['error'][] = 'El precio debe ser un número positivo';
        if ($this->costo === null || !is_numeric($this->costo) || $this->costo <= 0)
            self::$alertas['error'][] = 'El costo debe ser un número positivo';
        if (!$this->deporte_id || !is_numeric($this->deporte_id))
            self::$alertas['error'][] = 'Debe seleccionar un deporte válido';
        if (!$this->terreno_id || !is_numeric($this->terreno_id))
            self::$alertas['error'][] = 'Debe seleccionar un terreno válido';
        if (!$this->silueta)
            self::$alertas['error'][] = 'La silueta es obligatoria';

        return self::$alertas;
    }
}
?>

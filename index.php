<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Notas</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Registro de Notas</h1>
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="matematicas">Nota de Matemáticas:</label>
                    <input type="number" id="matematicas" name="matematicas" min="0" max="20" required>
                </div>
                <div class="form-group">
                    <label for="fisica">Nota de Física:</label>
                    <input type="number" id="fisica" name="fisica" min="0" max="20" required>
                </div>
                <div class="form-group">
                    <label for="programacion">Nota de Programación:</label>
                    <input type="number" id="programacion" name="programacion" min="0" max="20" required>
                </div>
                <button type="submit" class="btn">Guardar</button>
            </form>
        </div>
        <div class="results-container">
            <h2>Resultados</h2>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nuevaNota = [
                    'cedula' => $_POST['cedula'],
                    'nombre' => $_POST['nombre'],
                    'matematicas' => $_POST['matematicas'],
                    'fisica' => $_POST['fisica'],
                    'programacion' => $_POST['programacion']
                ];

                $archivoNotas = 'notas.json';

                if (file_exists($archivoNotas)) {
                    $contenidoNotas = file_get_contents($archivoNotas);
                    $notas = json_decode($contenidoNotas, true);
                } else {
                    $notas = [];
                }

                $notas[] = $nuevaNota;
                $notasJson = json_encode($notas);

                if (file_put_contents($archivoNotas, $notasJson, LOCK_EX) !== false) {
                    echo "<p>Notas guardadas correctamente.</p>";
                } else {
                    echo "<p>Error al guardar las notas.</p>";
                }
            }

            $archivoNotas = 'notas.json';

            if (file_exists($archivoNotas)) {
                $contenidoNotas = file_get_contents($archivoNotas);
                $notas = json_decode($contenidoNotas, true);

                $numAlumnos = count($notas);

                if ($numAlumnos > 0) {
                    $promedioMatematicas = 0;
                    $promedioFisica = 0;
                    $promedioProgramacion = 0;

                    $aprobadosMatematicas = 0;
                    $aprobadosFisica = 0;
                    $aprobadosProgramacion = 0;

                    $aplazadosMatematicas = 0;
                    $aplazadosFisica = 0;
                    $aplazadosProgramacion = 0;

                    $aprobadosTodasMaterias = 0;
                    $aprobadosUnaMateria = 0;
                    $aprobadosDosMaterias = 0;

                    $notaMaximaMatematicas = 0;
                    $notaMaximaFisica = 0;
                    $notaMaximaProgramacion = 0;

                    foreach ($notas as $alumno) {
                        $promedioMatematicas += $alumno['matematicas'];
                        $promedioFisica += $alumno['fisica'];
                        $promedioProgramacion += $alumno['programacion'];

                        if ($alumno['matematicas'] >= 10) {
                            $aprobadosMatematicas++;
                        } else {
                            $aplazadosMatematicas++;
                        }

                        if ($alumno['fisica'] >= 10) {
                            $aprobadosFisica++;
                        } else {
                            $aplazadosFisica++;
                        }

                        if ($alumno['programacion'] >= 10) {
                            $aprobadosProgramacion++;
                        } else {
                            $aplazadosProgramacion++;
                        }

                        if ($alumno['matematicas'] >= 10 && $alumno['fisica'] >= 10 && $alumno['programacion'] >= 10) {
                            $aprobadosTodasMaterias++;
                        } elseif (($alumno['matematicas'] >= 10 && $alumno['fisica'] < 10 && $alumno['programacion'] < 10) ||
                            ($alumno['matematicas'] < 10 && $alumno['fisica'] >= 10 && $alumno['programacion'] < 10) ||
                            ($alumno['matematicas'] < 10 && $alumno['fisica'] < 10 && $alumno['programacion'] >= 10)) {
                            $aprobadosUnaMateria++;
                        } elseif (($alumno['matematicas'] >= 10 && $alumno['fisica'] >= 10 && $alumno['programacion'] < 10) ||
                            ($alumno['matematicas'] >= 10 && $alumno['fisica'] < 10 && $alumno['programacion'] >= 10) ||
                            ($alumno['matematicas'] < 10 && $alumno['fisica'] >= 10 && $alumno['programacion'] >= 10)) {
                            $aprobadosDosMaterias++;
                        }

                        $notaMaximaMatematicas = max($notaMaximaMatematicas, $alumno['matematicas']);
                        $notaMaximaFisica = max($notaMaximaFisica, $alumno['fisica']);
                        $notaMaximaProgramacion = max($notaMaximaProgramacion, $alumno['programacion']);
                    }

                    $promedioMatematicas /= $numAlumnos;
                    $promedioFisica /= $numAlumnos;
                    $promedioProgramacion /= $numAlumnos;

                    echo "<div class=\"results-container\">";
                    echo "<h2>Resultados:</h2>";

                    echo "<table>
                            <tr>
                                <th>Materia</th>
                                <th>Promedio</th>
                                <th>Aprobados</th>
                                <th>Aplazados</th>
                                <th>Máxima</th>
                            </tr>
                            <tr>
                                <td>Matemáticas</td>
                                <td>$promedioMatematicas</td>
                                <td>$aprobadosMatematicas</td>
                                <td>$aplazadosMatematicas</td>
                                <td>$notaMaximaMatematicas</td>
                            </tr>
                            <tr>
                                <td>Física</td>
                                <td>$promedioFisica</td>
                                <td>$aprobadosFisica</td>
                                <td>$aplazadosFisica</td>
                                <td>$notaMaximaFisica</td>
                            </tr>
                            <tr>
                                <td>Programación</td>
                                <td>$promedioProgramacion</td>
                                <td>$aprobadosProgramacion</td>
                                <td>$aplazadosProgramacion</td>
                                <td>$notaMaximaProgramacion</td>
                            </tr>
                        </table>";

                    echo "<p>Número de alumnos:</p>";
                    echo "<ul>
                                <li>Aprobaron todas las materias: $aprobadosTodasMaterias</li>
                                <li>Aprobaron una sola materia: $aprobadosUnaMateria</li>
                                <li>Aprobaron dos materias: $aprobadosDosMaterias</li>
                            </ul>";
                } else {
                    echo "<p>No hay alumnos registrados.</p>";
                }
            } else {
                echo "<p>No se encontraron registros de notas.</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>

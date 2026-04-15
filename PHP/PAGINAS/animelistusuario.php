<?php
// ¡Magia! Con esta línea traemos todos los datos procesados del otro archivo
require_once '../FUNCIONALIDADES/logica_animelist.php';

?>
    <link rel="stylesheet" href="../../CSS/medialistusuario.css">

<div class="list-container">
    <?php foreach ($listaAgrupada as $estado => $lista): ?>
        <?php if (count($lista) > 0): ?>
            <div class="status-section">
                <h2 class="status-title"><?php echo $nombresEstados[$estado]; ?></h2>
                
                <table class="anime-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th class="center-text" style="width: 100px;">Score</th>
                            <th class="center-text" style="width: 120px;">Progress</th>
                            <th class="center-text" style="width: 100px;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): ?>
                            <tr>
                                <td>
                                    <div class="anime-title-col">
                                        <img src="<?php echo htmlspecialchars($item['portada']); ?>" alt="Cover" class="anime-cover">
                                        <a href="#" class="anime-name"><?php echo htmlspecialchars($item['titulo']); ?></a>
                                    </div>
                                </td>
                                <td class="center-text">
                                    <?php echo ($item['puntuacion'] > 0) ? $item['puntuacion'] : '-'; ?>
                                </td>
                                <td class="center-text">
                                    <?php echo $item['episodios_vistos']; ?> / <?php echo ($item['episodios_totales'] > 0) ? $item['episodios_totales'] : '?'; ?>
                                </td>
                                <td class="center-text" style="text-transform: uppercase;">
                                    <?php echo htmlspecialchars($item['type']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
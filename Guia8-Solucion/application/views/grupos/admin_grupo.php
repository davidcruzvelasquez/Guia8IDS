<script src="<?php echo base_url(); ?>js/admin_grupo.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/bootstrap.min.css">
<script src="<?php echo base_url('js/bootstrap.bundle.min.js');?>"></script>
<script src="<?php echo base_url(); ?>js/jquery-3.4.1.min.js"></script>
<script src="<?php echo base_url(); ?>js/ajax.js"></script>
<div class="container">
    <h3 class="mt-4">Administrar grupo</h3>
    <div class="mt-4">
        <p><strong>Materia: </strong><?php echo $data_grupo->materia; ?></p>
        <p><strong>Profesor: </strong><?php echo $data_grupo->nombre . " " . $data_grupo->apellido; ?></p>
        <p><strong>Grupo: </strong><?php echo $data_grupo->num_grupo; ?></p>
        <p><strong>Año: </strong><?php echo $data_grupo->anio; ?></p>
        <p><strong>Ciclo: </strong><?php echo $data_grupo->ciclo; ?></p>
        <input type="hidden" id="idgrupo" value="<?= $data_grupo->idgrupo; ?>" />
    </div>
    <hr>
    <div class="mt-4">
        Estudiante:
        <select class="form-control" id="estudiante" style="width: auto !important">
            <option>Seleccione una opción</option>
            <?php foreach ($estudiantes as $item) : ?>
                <option value="<?= $item->idestudiante ?>">
                    <?= $item->nombre . " " . $item->apellido ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <button class="btn btn-success btnAgregar" onclick="agregarEstudiante()">Agregar</button>
        <a class="btn btn-danger" role="button" href="<?=site_url('GruposController/report_todos_los_estudiantes_grupo/' . $data_grupo->idgrupo)?>">Reporte en PDF</a>
    </div>
    <hr>
    <div class="mt-4">
        <form action="<?= site_url("GruposController"); ?>/postadministrar" method="POST" class="form-ajax">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Código</th>
                        <th>Estudiantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead> 
                <tbody align="center" id="contenido_tabla"></tbody>
            </table>
            <!-- Aqui se agrega toda la informacion que se enviará 
            Debe estar oculta porque solo interesa que se envie y que no se ve -->
            <div id="data" hidden></div>
            <button class="btn btn-success mt-2 btnAgregar">Guardar</button> <a class="btn btn-danger mt-2 btnBack" href="<?= site_url('GruposController') ?>">Volver</a>
        </form>
    </div>
</div>
<script>
    // Se cargar los estudiantes previamente agregados
    // Esto con el objetico de manipular la información
    // en formato de objetos JSON
    estudiantes = <?= json_encode($grupo_estudiantes) ?>;
    // Mostrando estudiantes
    mostrarEstudiantes();
</script>




<!-- Inicio de la Búsqueda -->
<div class="container-fluid bg-white pt-3 px-lg-5">
    <div class="row mx-n2">
        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <select class="custom-select px-4 mb-3" style="height: 50px;">
                <option selected>Lugar de Recogida</option>
                <option value="1">Ubicación 1</option>
                <option value="2">Ubicación 2</option>
                <option value="3">Ubicación 3</option>
            </select>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <select class="custom-select px-4 mb-3" style="height: 50px;">
                <option selected>Lugar de Devolución</option>
                <option value="1">Ubicación 1</option>
                <option value="2">Ubicación 2</option>
                <option value="3">Ubicación 3</option>
            </select>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <div class="date mb-3" id="date" data-target-input="nearest">
                <input type="text" class="form-control p-4 datetimepicker-input" placeholder="Fecha/Hora Renta"
                    data-target="#date" data-toggle="datetimepicker" />
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <div class="date mb-3" id="returnDate" data-target-input="nearest">
                <input type="text" class="form-control p-4 datetimepicker-input" placeholder="Fecha/Hora Devolución"
                    data-target="#returnDate" data-toggle="datetimepicker" />
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <select class="custom-select px-4 mb-3" style="height: 50px;">
                <option selected>Tipo de Vehículo</option>
                <option value="1">Sedán</option>
                <option value="2">SUV</option>
                <option value="3">Compacto</option>
                <option value="4">Camioneta / Pickup</option>
                <option value="5">Deportivo</option>
                <option value="6">Minivan</option>
                <option value="7">Convertible</option>
                <option value="8">Hatchback</option>
                <option value="9">Económico</option>
                <option value="10">Lujo</option>
            </select>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 px-2">
            <button class="btn btn-primary btn-block mb-3" type="submit" style="height: 50px;">Buscar</button>
        </div>
    </div>
</div>
<!-- Fin de la Búsqueda -->

@extends('layout')

@section('content')
    <h1>Editar Producto</h1>
    <form method="POST" action="{{ url('/products/edit', $model->id) }}">
        {{ csrf_field() }}
        {{ method_field('POST') }}

        <div class="row">
            <div class="col-md-4">
                <!-- Columna 1 -->
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $model->name }}" required="required">
                </div>

                <div class="form-group">
                    <label>Activo:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="active" id="activo_si" value="1" {{ $model->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo_si">Si</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="active" id="activo_no" value="0" {{ !$model->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo_no">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alias">Alias:</label>
                    <input type="text" class="form-control" id="alias" name="alias" value="{{ $model->alias }}" required="required">
                </div>

                <div class="form-group">
                    <label for="price">Precio:</label>
                    <input type="number" class="form-control" id="price" name="price" value="{{ $model->price }}">
                </div>

                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>

            <div class="col-md-4">
                <!-- Columna 2 -->
                <div class="form-group">
                    <label for="shipping">Flete:</label>
                    <input type="number" class="form-control" id="shipping" name="shipping" value="{{ $model->shipping }}">
                </div>

                <div class="form-group">
                    <label for="total">Precio total:</label>
                    <input type="number" class="form-control" id="total" name="total" value="{{ $model->total }}">
                </div>

                <div class="form-group">
                    <label for="coin">Moneda:</label>
                    <input type="text" class="form-control" id="coin" name="coin" value="{{ $model->coin }}">
                </div>

                <div class="form-group">
                    <label for="country">Pa��s:</label>
                    <input type="text" class="form-control" id="country" name="country" required="required" value="{{ $model->country }}">
                </div>
            </div>

            <div class="col-md-4">
                <!-- Columna 3 -->
                <div class="form-group">
                    <label for="description">Descripci��n:</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="description_to_print">Descripci��n para Imprimir:</label>
                    <textarea class="form-control" id="description_to_print" name="description_to_print"></textarea>
                </div>
            </div>
        </div>
    </form>
@endsection

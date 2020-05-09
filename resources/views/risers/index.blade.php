@extends('layouts.page')

@section('title', 'Risers')
@section('page-title')
    <i class="fal fa-fw fa-folders"></i> Riser Placements
@endsection
@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Riser Placement</h3>

        <div class="card-body">
            <riser-stack></riser-stack>


            <!--
            <form id="riser_settings" class="form-inline">
                <div class="form-group">
                    <label for="riser_rows">Rows</label>
                    <input id="riser_rows" name="rows" type="number" min="1" max="8" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <label for="riser_cols">Columns</label>
                    <input id="riser_cols" name="cols" type="number" min="1" max="8" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <label for="riser_singers">Singers</label>
                    <input id="riser_singers" name="singers" type="number" min="1" max="150" class="form-control mx-3">
                </div>

                <div class="form-group">
                    <a href="#" id="riser_settings_submit" class="btn btn-primary">Update</a>
                </div>
            </form>
            -->

            <svg id="risers" width="1000" height="500"></svg>

            <div class="row">

                <div class="col-md-3">
                    <div class="card bg-light">
                        <h5 class="card-header">Bass</h5>
                        <div class="card-body">
                            <riser-face email="abott@adorable.io" singer_name="abott"></riser-face>
                            <riser-face email="bob@adorable.io" singer_name="bob"></riser-face>
                            <riser-face email="carl@adorable.io" singer_name="carl"></riser-face>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-light">
                        <h5 class="card-header">Baritone</h5>

                        <div class="card-body">
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-light">
                        <h5 class="card-header">Lead</h5>

                        <div class="card-body">
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-light">
                        <h5 class="card-header">Tenor</h5>

                        <div class="card-body">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer">

        </div>

    </div>

@endsection